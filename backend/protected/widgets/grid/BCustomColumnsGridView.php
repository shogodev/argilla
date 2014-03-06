<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
 *
 * Example:
 * <pre>
 * $this->widget('BCustomColumnsGridView', array(
 *   'dataProvider' => $model->search(),
 *   'filter' => $model,
 *   'columns' => array(
 *     array(
 *       'name' => 'BProduct',
 *       'header' => 'Продукты',
 *       'class' => 'BPopupColumn',
 *       'iframeAction' => 'index',
 *     ),
 *   ),
 * ));
 * </pre>
 */

Yii::import('backend.modules.settings.models.BGridSettings');

class BCustomColumnsGridView extends BGridView
{
  public $templateColumns = array();

  public function init()
  {
    $this->templateColumns = $this->columns;
    $this->columns = $this->getGridColumns();

    array_push($this->columns, array('class' => 'BButtonColumn'));
    array_unshift($this->columns, array('name' => 'id', 'class' => 'BPkColumn'));

    $this->mergeTemplateColumns($this->templateColumns);
    parent::init();
  }

  /**
   * @return array
   */
  protected function getGridColumns()
  {
    $columns = array();

    foreach($this->getSettings() as $i => $settings)
    {
      if( !$this->isColumnValid($settings['name']) )
        continue;

      $columns[$i]['name'] = $settings['name'];

      if( !empty($settings['header']) )
        $columns[$i]['header'] = $settings['header'];

      if( !empty($settings['class']) )
        $columns[$i]['class'] = $settings['class'];

      if( !empty($settings['type']) )
        $columns[$i]['type'] = $settings['type'];

      $columns[$i]['filter'] = $settings->getFilter();
    }

    return $columns;
  }

  /**
   * @return BGridSettings[]
   */
  protected function getSettings()
  {
    $criteria = new CDbCriteria;
    $criteria->order = 'IF(position, position, 999999)';

    return BGridSettings::model()->visible()->findAll($criteria);
  }

  /**
   * Добавляем к столбцам те, которые описаны в шаблоне
   * и о которых существует запись в таблице с настройками
   *
   * @param array $templateColumns
   */
  protected function mergeTemplateColumns($templateColumns)
  {
    foreach($templateColumns as $templateColumn)
    {
      foreach($this->columns as $i => $column)
      {
        if( Arr::get($column, 'name') !== $templateColumn['name'] )
          continue;

        if( isset($column['header']) )
          $templateColumn['header'] = $column['header'];

        $this->columns[$i] = $templateColumn;
      }
    }
  }

  /**
   * @param $name
   *
   * @return bool
   */
  protected function isColumnValid($name)
  {
    try
    {
      foreach($this->templateColumns as $column)
        if( $column['name'] == $name )
          return true;

      $value = $this->filter->{$name};
      return true;
    }
    catch(CException $e)
    {
      return false;
    }
  }
}