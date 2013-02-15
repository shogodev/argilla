<?php
/**
 * @property string filterKey
 * @property array state
 * @property ProductFilterElement[] elements
 */
class ProductFilter extends CComponent implements JsonSerializable
{
  public $emptyElementValue = array('' => 'Не задано');

  /**
  * тип элемнта по умолчанию.
  */
  public $defaultElementType = 'list';

  protected $filterKey;

  /**
   * @var ProductFilterElement[]
   */
  protected $elements = array();

  /**
   * @var array Текущее состояние фильтра
   */
  protected $state = array();

  public function __construct($filterKey = 'productFilter')
  {
    $this->filterKey = $filterKey;
    $this->setState();
  }

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
    else
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

  public function getElements()
  {
    return $this->elements;
  }

  public function getFilterKey()
  {
    return $this->filterKey;
  }

  public function getState()
  {
    return $this->state;
  }

  public function setState($state = null)
  {
    if( !$state )
      $state = Arr::get(Yii::app()->session, $this->filterKey, array());

    $this->state = $state;
  }

  public function saveState($state = null, $replaceOldState = false)
  {
    if( !$state )
      $state = Yii::app()->request->getPost($this->filterKey, array());

    $session = isset(Yii::app()->session[$this->filterKey]) ? Yii::app()->session[$this->filterKey] : array();

    if( $replaceOldState )
      $session = $state;
    else
      $session = Arr::array_merge_assoc($session, $state);

    Yii::app()->session[$this->filterKey] = $session;
  }

  /**
   * @param CDbCriteria $actionCriteria
   *
   * @return CDbCriteria
   */
  public function apply(CDbCriteria $actionCriteria)
  {
    $availableValues = $this->getAvailableValues($actionCriteria, false);
    $this->buildItems($availableValues);
    $filteredCriteria = $this->createFilteredCriteria($actionCriteria, $availableValues);

    $this->disablingFilterElements($actionCriteria, $filteredCriteria, $availableValues);
    $this->removeEmptyItems();

    return $filteredCriteria;
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

  public function jsonSerialize()
  {
    $data = array();

    foreach($this->elements as $element)
      $data[$element->id] = $element->jsonSerialize();

    return $data;
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

  protected function getAvailableValues(CDbCriteria $criteria, $filtered = true)
  {
    $builder                 = new CriteriaBuilder($criteria);
    $availableValuesCriteria = $builder->getAvailableValuesCriteria($this->elements);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(Product::model()->tableName(), $availableValuesCriteria);
    $data    = $command->query();

    $propertyValues = array();

    foreach($data as $row)
    {
      foreach($this->elements as $element)
      {
        $itemId = $element->isProperty() ? $element->id : 'variant_id';

        if( $element->isProperty() || $element->id == $row['param_id'] )
        {
          $availableValue = $element->prepareAvailableValues($row[$itemId], $filtered);
          $propertyValues[$element->id][$availableValue] = $availableValue;

          if( isset($element->items[$availableValue]) )
            $element->items[$availableValue]->amount = $row['count'];
        }
      }
    }

    return $propertyValues;
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
        // но отсутствуют в ниличии (были проставлены на других страницах каталога)
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

      $selectedCriteria     = $this->createFilteredCriteria($actionCriteria, $otherSelectedStates);
      $otherAvailableValues = $this->getAvailableValues($selectedCriteria);

      if( isset($otherAvailableValues[$id]) )
        $otherSelectedValues[$id][$id] = $otherAvailableValues[$id];
    }

    $intersects = array();
    foreach($otherSelectedValues as $elements)
      foreach($elements as $id => $item)
        $intersects[$id] = empty($intersects[$id]) ? $item : array_intersect($intersects[$id], $item);

    // Отключаем значения для заполненных элеметров фильтра
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
      $element->setSelected($this->state, $elementAvailableValues);
    }
  }

  /**
   * Удаляем пыстые или полностью отключенные элементы из фильра
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