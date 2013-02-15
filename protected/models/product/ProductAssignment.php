<?php

/**
 * @property int $product_id
 * @property int $section_id
 * @property int $type_id
 *
 */
class ProductAssignment extends FActiveRecord
{
  protected $menu = array();

  protected $navigationPath = array('section', 'type', 'configuration', 'series', 'subseries', 'product');

  public function tableName()
  {
    return '{{product_assignment}}';
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'ProductSection', 'section_id'),
      'type' => array(self::BELONGS_TO, 'ProductType', 'type_id'),
      'configuration' => array(self::BELONGS_TO, 'ProductConfiguration', 'configuration_id'),
      'series' => array(self::BELONGS_TO, 'ProductSeries', 'series_id'),
      'subseries' => array(self::BELONGS_TO, 'ProductSubseries', 'subseries_id'),
    );
  }

  /**
   * @return array
   */
  public function getAssignments()
  {
    $product       = Product::model()->tableName();
    $section       = ProductSection::model()->tableName();
    $type          = ProductType::model()->tableName();
    $configuration = ProductConfiguration::model()->tableName();
    $series        = ProductSeries::model()->tableName();
    $subseries     = ProductSubseries::model()->tableName();

    $criteria            = new CDbCriteria();
    $criteria->select    = 't.section_id, t.type_id, t.configuration_id, t.series_id, t.subseries_id, p.id AS product_id,';
    $criteria->select   .= 's.name AS section_name, s.url AS section_url, s.menu_name AS section_menu_name, ';
    $criteria->select   .= 'type.name AS type_name, type.url AS type_url, ';
    $criteria->select   .= 'configuration.name AS configuration_name, configuration.url AS configuration_url, ';
    $criteria->select   .= 'series.name AS series_name, series.url AS series_url, ';
    $criteria->select   .= 'subseries.name AS subseries_name, subseries.url AS subseries_url, ';

    $criteria->select   .= 'p.name AS product_name, p.url AS product_url';

    $criteria->join      = 'LEFT JOIN '.$product.' AS p ON p.id = t.product_id ';
    $criteria->join     .= 'LEFT JOIN '.$section.' AS s ON s.id = t.section_id ';
    $criteria->join     .= 'LEFT JOIN '.$type.' AS type ON type.id = t.type_id ';
    $criteria->join     .= 'LEFT JOIN '.$configuration.' AS configuration ON configuration.id = t.configuration_id ';
    $criteria->join     .= 'LEFT JOIN '.$series.' AS series ON series.id = t.series_id ';
    $criteria->join     .= 'LEFT JOIN '.$subseries.' AS subseries ON subseries.id = t.subseries_id';

    $criteria->condition .= 'p.visible=1 AND s.visible=1 ';
    $criteria->condition .= 'AND (type.visible=1 OR ISNULL(type.visible))';
    $criteria->condition .= 'AND (configuration.visible=1 OR ISNULL(configuration.visible))';
    $criteria->condition .= 'AND (series.visible=1 OR ISNULL(series.visible))';
    $criteria->condition .= 'AND (subseries.visible=1 OR ISNULL(subseries.visible))';

    $criteria->distinct = true;
    $criteria->order    = 's.position, p.position';

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(self::tableName(), $criteria);
    $data    = $command->queryAll();

    return $data;
  }

  /**
   * @param array $selected
   *
   * @return array
   */
  public function getMenu($selected = array())
  {
    if( empty($this->menu) )
      $this->menu = $this->createMenu($selected);

    return $this->menu;
  }

  private function createMenu($selected)
  {
    $menu = array();
    $assignments  = $this->getAssignments();
    $selectedPath = $this->findPath($assignments, $selected);
    $filteredMenu = $this->filterMenu($assignments, $selectedPath['selected']);

    foreach($filteredMenu as $element => $items)
    {
      if( isset($selectedPath['selected'][$element]) && isset($items[$selectedPath['selected'][$element]]) )
        unset($items[$selectedPath['selected'][$element]]);

      $menu[$element] = array(
        'id'    => isset($selectedPath['data'][$element]) ? $selectedPath['data'][$element]['id'] : 0,
        'name'  => isset($selectedPath['data'][$element]) ? $selectedPath['data'][$element]['name'] : 'Все',
        'url'   => isset($selectedPath['data'][$element]) ? Yii::app()->controller->createUrl('product/'.$element, array($element => $selectedPath['data'][$element]['url'])) : Yii::app()->controller->getCurrentUrl(),
        'items' => $items
      );
    }

    return $menu;
  }

  private function filterMenu($assignments, $selectedPath)
  {
    $menu = array();

    foreach($this->navigationPath as $index => $element)
    {
      if( count($menu) > count($selectedPath) )
        break;

      foreach($assignments as $assignment)
      {
        if( empty($assignment[$element.'_id']) )
          continue;

        if( !$this->isSelected($selectedPath, $assignment, $element) )
          continue;

        $menu[$element][$assignment[$element.'_id']] = array(
          'id'    => $assignment[$element.'_id'],
          'label' => $assignment[$element.'_name'],
          'url'   => array('product/'.$element, $element => $assignment[$element.'_url'])
        );
      }
    }

    return $menu;
  }

  private function findPath($assignments, $selected)
  {
    $selectedPath = array();
    $selectedElementsData = array();
    $selectedElement = key($selected);
    $selectedValue   = reset($selected);
    $path            = array_reverse($this->navigationPath);

    foreach($path as $index => $element)
      if($element == $selectedElement)
        $selectedWithIndex = $index;

    foreach($assignments as $item)
    {
      if( $item[$selectedElement.'_id'] == $selectedValue )
      {
        foreach($path as $index => $element)
        {
          if( $index >= $selectedWithIndex && !empty($item[$element.'_id']) )
          {
            $selectedPath[$element]         = $item[$element.'_id'];
            $selectedElementsData[$element] = array(
              'id' => $item[$element.'_id'],
              'name' => $item[$element.'_name'],
              'url' => $item[$element.'_url'],
            );
          }
        }
      }
    }

    return array(
      'selected' => array_reverse($selectedPath),
      'data' => $selectedElementsData
    );
  }

  private function isSelected($selectedPath, $assignment, $checkedElement)
  {
    $skipMask = array();

    $checkedElementIndex = array_search($checkedElement, $this->navigationPath);
    foreach($this->navigationPath as $index => $element)
    {
      if( $index >= $checkedElementIndex )
        $skipMask[$element] = $element;
    }

    foreach($selectedPath as $element => $value)
    {
      if( isset($skipMask[$element]) )
        continue;

      if( $assignment[$element.'_id'] != $value )
        return false;
    }

    return true;
  }
}