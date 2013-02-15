<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.BAutoBuildingActiveForm
 *
 * @example
 * <code>
 *   // In view
 *   $form = $this->beginWidget('BAutoBuildingActiveForm', array('id' => $model->getFormId()));
 *   $form->show($model);
 *   $this->endWidget();
 * </code>
 */
class BAutoBuildingActiveForm extends BActiveForm
{
  /**
   * @var CActiveRecordMetaData
   */
  private $metaData;

  /**
   * @var CActiveRecord
   */
  private $model;

  /**
   * Построение всех доступных полей для заданой формы
   *
   * @param CActiveRecord $model
   *
   * @return string
   */
  public function show($model)
  {
    $this->model = $model;
    $this->initMetaData();

    return $this->buildFields();
  }

  /**
   * @throws CException
   */
  private function initMetaData()
  {
    if( $this->model instanceof CActiveRecord )
      $this->metaData = new CActiveRecordMetaData($this->model);
    else
      throw new CException('Модель должна реализовывать CActiveRecord');
  }

  /**
   * @return string
   */
  private function buildFields()
  {
    ob_start();

    foreach( $this->metaData->columns as $column )
    {
      if( $column->isPrimaryKey )
        continue;

      $this->renderColumn($column);
    }

    return ob_get_clean();
  }

  /**
   * @param CMysqlColumnSchema $column
   */
  private function renderColumn(CMysqlColumnSchema $column)
  {
    switch( $column->type )
    {
      case 'string':
        $this->renderStringTypeColumn($column);
        break;
      case 'integer':
        $this->renderIntegerTypeColumn($column);
        break;
    }
  }

  /**
   * @param CMysqlColumnSchema $column
   */
  private function renderStringTypeColumn(CMysqlColumnSchema $column)
  {
    if( $column->name === 'img' || $column->name === 'image' )
      echo $this->uploadRow($this->model, $column->name, false);
    elseif( preg_match('/varchar\(\w+\)/', $column->dbType) )
      echo $this->textFieldRow($this->model, $column->name);
    elseif( $column->dbType === 'text' )
      echo $this->ckeditorRow($this->model, $column->name);
    else
      $this->renderIntegerTypeColumn($column);
  }

  /**
   * @param CMysqlColumnSchema $column
   */
  private function renderIntegerTypeColumn(CMysqlColumnSchema $column)
  {
    echo $this->textFieldRow($this->model, $column->name, array('class' => 'span2'));
  }
}