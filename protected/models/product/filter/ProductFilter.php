<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter

 * @property string $filterKey
 * @property array $state
 * @property ProductFilterElement[] elements
 */
class ProductFilter extends AbstractProductFilter
{
  public $emptyElementValue = array('' => 'Не задано');

  /**
  * тип элемнта по умолчанию.
  */
  public $defaultElementType = 'list';

  /**
   * @var ProductFilterElement[]
   */
  protected $elements = array();

  public function addElement(array $filterElement, $emptyValue = null)
  {
    $items = array();

    if( $emptyValue !== false )
    {
      $defaultItemOptions = array(
        'id' => key($this->emptyElementValue),
        'class' => 'ProductFilterElementItem',
        'label' => reset($this->emptyElementValue),
      );

      if( is_array($emptyValue) )
        $defaultItemOptions = CMap::mergeArray($defaultItemOptions, $emptyValue);

      $items[key($this->emptyElementValue)] = Yii::createComponent($defaultItemOptions);
    }

    if( !isset($filterElement['id']) && !empty($filterElement['key']) )
      $filterElement['id'] = ProductParamName::model()->findByAttributes(array('key' => $filterElement['key']))->id;
    else if( empty($filterElement['key']))
      $filterElement['key'] = $filterElement['id'];

    if( empty($filterElement['type']) )
      $filterElement['type'] = $this->defaultElementType;

    if( class_exists('ProductFilterElement'.ucfirst($filterElement['type'])) )
      $filterElement['class'] = 'ProductFilterElement'.$filterElement['type'];
    else
      throw new CHttpException(500, "Не удалось найти класс 'ProductFilterElement{$filterElement['type']}.") ;

    /**
     * @var $element ProductFilterElement
     */
    $element = Yii::createComponent(CMap::mergeArray(array('parent' => $this, 'items' => $items), $filterElement));

    $this->elements[$filterElement['id']] = $element;
  }

  public function removeElement($elementId)
  {
    if( isset($this->elements[$elementId]) )
      unset($this->elements[$elementId]);
  }

  public function getElements()
  {
    return $this->elements;
  }

  public function getElementByKey($key)
  {
    foreach($this->elements as $element)
    {
      if( $element->key == $key )
        return $element;
    }

    return null;
  }

  /**
   * @param CDbCriteria $actionCriteria
   *
   * @return CDbCriteria
   */
  public function apply(CDbCriteria $actionCriteria)
  {
    $availableValues = $this->getAvailableValues($actionCriteria);
    $this->buildItems($availableValues);
    $filteredCriteria = $this->createFilteredCriteria($actionCriteria, $availableValues);

    $this->disablingFilterElements($actionCriteria, $filteredCriteria, $availableValues);

    $this->countAmount($filteredCriteria);

    $this->removeEmptyItems();

    return $filteredCriteria;
  }

  protected function createFilteredCriteria(CDbCriteria $actionCriteria, array $availableValues)
  {
    $builder = new CriteriaBuilder($actionCriteria);

    foreach($this->elements as $element)
    {
      if( !$element->inAvailableValues($availableValues)  )
        continue;

      $builder->addCondition($element);
    }

    $filteredCriteria = $builder->getFilteredCriteria();

    return $filteredCriteria;
  }

  protected function getAvailableValues(CDbCriteria $criteria)
  {
    $builder                 = new CriteriaBuilder($criteria);
    $availableValuesCriteria = $builder->getAvailableValuesCriteria($this->elements);

    $commandBuilder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $commandBuilder->createFindCommand(Product::model()->tableName(), $availableValuesCriteria);
    $data    = $command->queryAll();

    $availableValues = array();

    foreach($this->elements as $element)
    {
      foreach($data as $row)
      {
        $itemId = $element->isProperty() ? $element->id : 'variant_id';

        if( $element->isParameter() && $element->id != $row['param_id'] )
          continue;

        $value = $element->isParameter() && $row['variant_id'] == null ? $row['value'] : $row[$itemId];

        $availableValue = $element->prepareAvailableValues($value);
        $availableValueArray = is_array($availableValue) ? $availableValue : array($availableValue);

        foreach($availableValueArray as $availableValue)
          $availableValues[$element->id][$availableValue] = $availableValue;
      }
    }

    return $availableValues;
  }

  protected function countAmount($criteria, $onlySelected = false)
  {
    $cb = new CriteriaBuilder($criteria);

    foreach($this->elements as $element)
      if( $element->isProperty() && (!$onlySelected || $onlySelected && $element->isSelected()) )
        $this->countAmountProperties($element, $cb);

    $this->countAmountParameters($cb, $onlySelected);
  }

  /**
   * @param ProductFilterElement $element
   * @param CriteriaBuilder $builder
   */
  protected function countAmountProperties($element, $builder)
  {
    $criteria = $builder->getPropertyAmountCriteria($element);

    $commandBuilder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $commandBuilder->createFindCommand(Product::model()->tableName(), $criteria);
    $data    = $command->queryAll();

    foreach($data as $row)
    {
      if( isset($row[$element->id]) && isset($element->items[$row[$element->id]]) )
        $element->items[$row[$element->id]]->amount = $row['count'];
    }
  }

  /**
   * @param CriteriaBuilder $builder
   * @param $onlySelected
   */
  protected function countAmountParameters($builder, $onlySelected)
  {
    $criteria = $builder->getParameterAmountCriteria();

    $commandBuilder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $commandBuilder->createFindCommand(Product::model()->tableName(), $criteria);
    $data    = $command->queryAll();

    foreach($this->elements as $element)
    {
      if( $element->isProperty() )
        continue;

      if( $onlySelected && !$element->isSelected() )
        continue;

      foreach($data as $row)
      {
        $value = $row['variant_id'] == null ? $row['value'] : $row['variant_id'];

        $itemId = $element->prepareAvailableValues($value);
        $itemIdsArray = is_array($itemId) ? $itemId : array($itemId);

        foreach($itemIdsArray as $itemId)
        {
          if( $element->id != $row['param_id'] || !isset($element->items[$itemId]) )
            continue;

          if( $onlySelected && $element->items[$itemId]->isSelected() )
            continue;

          if( $row['variant_id'] == null  ) // полея у которых нет вариантов, а есть value
            $element->items[$itemId]->amount += $row['count'];
          else
            $element->items[$itemId]->amount = $row['count'];
        }
      }
    }
  }

  protected function disablingFilterElements($actionCriteria, &$filteredCriteria, $availableValues)
  {
    $selectedStates   = array();
    $unselectedStates = array();

    foreach($this->elements as $element)
    {
      $id    = $element->id;
      $value = isset($this->state[$id]) ? $this->state[$id] : '';

      if( !isset($value) || $value === '' )
        $unselectedStates[$id] = $value;
      else
      {
        // Пропускаем отмеченные параметры (присутствуют в сессии),
        // но отсутствуют в наличии (были проставлены на других страницах каталога)
        if( !$element->inAvailableValues($availableValues)  )
          continue;

        $selectedStates[$id] = $value;
      }
    }

    // возможно потребуется рекурсия
    $this->checkOldState($actionCriteria, $selectedStates, $availableValues);
    $filteredCriteria = $this->createFilteredCriteria($actionCriteria, $availableValues);

    // Чтобы правильно считалось количество важно, чтобы выделенные элементы обрабатывались раньше невыделенных
    $this->processSelectedStates($actionCriteria, $selectedStates, $availableValues);
    $this->processUnselectedStates($filteredCriteria, $unselectedStates, $availableValues);
  }

  protected function checkOldState($actionCriteria, &$selectedStates, &$availableValues)
  {
    if( $this->issetValues($actionCriteria, $selectedStates) )
      return;

    foreach(array_reverse($selectedStates, true) as $id => $selectedValue)
    {
      $otherSelectedStates = $selectedStates;
      unset($otherSelectedStates[$id]);

      if( $this->issetValues($actionCriteria, $otherSelectedStates) )
      {
        unset($this->state[$id]);
        unset($selectedStates[$id]);
        unset($availableValues[$id][$selectedValue]);
        unset($this->elements[$id]->items[$selectedValue]);
        return;
      }
    }
  }

  protected function issetValues($actionCriteria, $selectedStates)
  {
    $oldStateFilteredCriteria      = $this->createFilteredCriteria($actionCriteria, $selectedStates);
    $oldStateAvailableValuesForOld = $this->getAvailableValues($oldStateFilteredCriteria);

    return !empty($oldStateAvailableValuesForOld);
  }

  /**
   * Отключаем значения для незаполненных элементов фильтра после изменения критерии с учетом выбранных фильтров
   *
   * @param $filteredCriteria
   * @param $unselectedStates
   * @param $availableValues
   */
  protected function processUnselectedStates($filteredCriteria, $unselectedStates, $availableValues)
  {
    $filteredValues = $this->getAvailableValues($filteredCriteria);
    $this->disablingUnavailableValues($unselectedStates, $availableValues, $filteredValues);
  }

  /**
   * Обрабатываем заполненные селекторы. Пускаем цикл, каждый параметр по очереди делаем незаполненным
   * (удаляем значения из выборки), получаем его значения при условии, что оставшиеся остались заполненными.
   * Кладем данные в общий массив и ищем пересечение для всех параметров.
   *
   * @param $actionCriteria
   * @param $selectedStates
   * @param $availableValues
   */
  protected function processSelectedStates($actionCriteria, $selectedStates, $availableValues)
  {
    $otherSelectedValues = array();
    foreach($selectedStates as $id => $selectedValue)
    {
      $otherSelectedStates = $selectedStates;
      unset($otherSelectedStates[$id]);

      $selectedCriteria = $this->createFilteredCriteria($actionCriteria, $otherSelectedStates);

      // только для выбранных элементов
      $this->countAmount($selectedCriteria, true);

      $otherAvailableValues = $this->getAvailableValues($selectedCriteria);

      if( isset($otherAvailableValues[$id]) )
        $otherSelectedValues[$id][$id] = $otherAvailableValues[$id];
    }

    $intersects = array();
    foreach($otherSelectedValues as $elements)
      foreach($elements as $id => $item)
        $intersects[$id] = empty($intersects[$id]) ? $item : array_intersect($intersects[$id], $item);

    // Отключаем значения для заполненных элементов фильтра
    $this->disablingUnavailableValues($selectedStates, $availableValues, $intersects);
  }

  protected function disablingUnavailableValues($states, $allValues, $disablingValues)
  {
    foreach($states as $id => $value)
    {
      $this->elements[$id]->disabled = !empty($disablingValues[$id]) ? array_diff($allValues[$id], $disablingValues[$id]) : (isset($allValues[$id]) ? $allValues[$id] : array());
    }
  }

  protected function buildItems($availableValues)
  {
    foreach($this->elements as $element)
    {
      $elementAvailableValues = isset($availableValues[$element->id]) ? $availableValues[$element->id] : array();
      $element->buildItems($elementAvailableValues);
      $element->setSelected($this->state);
    }
  }

  /**
   * Удаление пустых или полностью отключенных элементов из фильтра
   */
  protected function removeEmptyItems()
  {
    foreach($this->elements as $id => $element)
    {
      if( empty($element->items) )
      {
        unset($this->elements[$id]);
        continue;
      }

      if( !array_diff(array_keys($element->items), array_keys($element->disabled)) )
        unset($this->elements[$id]);
    }
  }
}