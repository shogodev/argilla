<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class RowGridWidget
 */
abstract class RowGridWidget extends CWidget
{
  public $modelName;

  public $modelId;

  public $modelAttribute;

  public $urlRoute;

  public $header = 'Заголовок';

  public $templateButtonColumn = '{update} {delete}';

  public $urlCreate;

  public $urlEdit;

  public $urlDelete;

  /**
   * @return array
   */
  abstract protected function getColumns();

  public function init()
  {
    if( is_null($this->modelName) )
      throw new CHttpException(500, 'Укажите свойство modelName для виджета '.__CLASS__);

    if( is_null($this->modelAttribute) )
      throw new CHttpException(500, 'Укажите свойство modelAttribute для виджета '.__CLASS__);

    if( is_null($this->urlRoute) && (is_null($this->urlCreate) || is_null($this->urlEdit) || is_null($this->urlDelete)) )
      throw new CHttpException(500, 'Укажите свойство urlRoute для виджета '.__CLASS__);
    else
    {
      if( is_null($this->urlCreate) )
        $this->urlCreate = $this->controller->createUrl($this->urlRoute.'/create', array('model_id' => $this->modelId, 'popup' => true));

      if( is_null($this->urlEdit) )
        $this->urlEdit = 'Yii::app()->controller->createUrl("'.$this->urlRoute.'/update", array("id" => $data->primaryKey, "popup" => true))';

      if( is_null($this->urlDelete) )
        $this->urlDelete = 'Yii::app()->controller->createUrl("'.$this->urlRoute.'/delete", array("id" => $data->primaryKey))';
    }
  }

  public function run()
  {
    if( !$this->isAvailable() )
      return;

    echo '<tr><th><label>'.$this->header.'</label></th><td>';
    $this->renderButton();
    $this->renderGrid();
    echo '</td></tr>';
  }

  protected function getUpdateButtonClass()
  {
    return $this->getId().'_update_button';
  }

  /**
   * @return BActiveRecord $model
   */
  protected function getModel()
  {
    /**
     * @var BActiveRecord $model
     */
    $modelName = $this->modelName;
    $model = $modelName::model();
    $model->setAttribute($this->modelAttribute, $this->modelId);

    return $model;
  }

  private function renderButton()
  {
    $buttonId = $this->getId().'_add_button';

    echo CHtml::openTag('div', array('class' => 's-buttons s-buttons-top'));
    $this->widget('BButton', array(
      'htmlOptions' => array('id' => $buttonId),
      'label' => 'Добавить',
      'url' => $this->urlCreate,
      'type' => 'info',
      'popupDepended' => true,
    ));
    echo CHtml::closeTag('div');

    Yii::app()->clientScript->registerScript($buttonId.'_script', "
      jQuery(document).on('click', '#{$buttonId}', function(e){
        e.preventDefault();
        assigner.open(this.href, {'closeOperation' : function(){ $.fn.yiiGridView.update('{$this->getId()}')}});
      });
    ");
  }

  private function renderGrid()
  {
    $this->controller->widget('BGridView', array(
      'id' => $this->getId(),
      'dataProvider' => $this->getModel()->search(),
      'template' => "{items}\n{pager}\n{scripts}",
      'filter' => $this->getModel(),
      'columns' => CMap::mergeArray($this->getColumns(), $this->getColumnButtons())
    ));

    $this->registerUpdateButtonScript();
  }

  /**
   * @return array
   */
  protected function getColumnButtons()
  {
    return array(
      array(
        'class' => 'BButtonColumn',
        'template' => $this->templateButtonColumn,
        'updateButtonUrl' => $this->urlEdit,
        'updateButtonOptions' => array('class' => 'update '.$this->getUpdateButtonClass()),
        'deleteButtonUrl' => $this->urlDelete
      )
    );
  }

  private function registerUpdateButtonScript()
  {
    Yii::app()->clientScript->registerScript($this->getUpdateButtonClass().'_script', "
      jQuery(document).on('click', '.{$this->getUpdateButtonClass()}', function(e){
        e.preventDefault();
        assigner.open(this.href, {'closeOperation' : function(){ $.fn.yiiGridView.update('{$this->getId()}')}});
      });
    ");
  }

  private function isAvailable()
  {
    return !$this->controller->popup && $this->controller->isUpdate();
  }
}