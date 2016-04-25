<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */


/**
 * Class ProductFilterBehavior
 * @property Filter $filter
 */
class ProductFilterBehavior extends SBehavior
{
  const FILTER_PRICE = 'price';

  const FILTER_COLOR = 'color';

  /**
   * @var Filter
   */
  private $filter;

  /**
   * @param bool $autoSetFilter
   *
   * @return Filter
   */
  public function getFilter($autoSetFilter = true)
  {
    if( is_null($this->filter) && $autoSetFilter )
      $this->setFilter();

    return $this->filter;
  }

  public function setFilter()
  {
    if( !isset($this->filter) )
    {
      $this->filter = new Filter(null, true);
    }

    $this->filter->addElement(array(
      'id'          => 'section_id',
      'label'       => 'Разделы',
      'htmlOptions' => ['class' => ''],
      'itemLabels'  => CHtml::listData(ProductSection::model()->findAll(), 'id', 'name'),
    ));

    $this->filter->addElement(array(
      'id'          => 'category_id',
      'label'       => 'Бренды',
      'type'        => 'checkbox',
      'htmlOptions' => ['class' => 'filter-block m30'],
      'itemLabels'  => CHtml::listData(ProductCategory::model()->findAll(), 'id', 'name'),
    ));

    $this->filter->addElement(array(
      'id'          => 'type_id',
      'label'       => 'Возраст',
      'type'        => 'checkbox',
      'htmlOptions' => ['class' => 'filter-block m30'],
      'itemLabels'  => CHtml::listData(ProductType::model()->findAll(), 'id', 'name'),
    ));

    $this->filter->addElement(array(
      'id'          => self::FILTER_PRICE,
      'label'       => 'Цена',
      'type'        => 'slider',
      'htmlOptions' => ['class' => ''],
      'borderRange' => 100,
    ));

    $criteria = new CDbCriteria();
    $criteria->compare('t.selection', 1);
    $parameters = ProductParameterName::model()->search($criteria);

    foreach($parameters as $parameter)
    {
      $this->filter->addElement(array(
        'id'          => $parameter->id,
        'key'         => $parameter->key,
        'label'       => $parameter->name,
        'notice' => $parameter->notice,
        'type'        => 'parameter',
        'htmlOptions' => ['class' => ''],
        'variants'    => $parameter->variants,
      ));
    }
  }

} 