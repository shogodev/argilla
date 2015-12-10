<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 */
class ParametersWidget extends CWidget
{
  /**
   * @var BProduct
   */
  public $model;

  public $form;

  /**
   * @var array $hideParameters - масив id параметров которые нужно скрыть
   */
  public $hideParameters = array();

  public function init()
  {
    if( is_null($this->model) )
      throw new CHttpException(500, 'Укажите свойство model для виджета '.__CLASS__);

    if( is_null($this->form) )
      throw new CHttpException(500, 'Укажите свойство form для виджета '.__CLASS__);

    if( $this->model->asa('modificationBehavior') && $this->model->isModification())
    {
      $parent = $this->model->getParentModel();
      $this->model->section_id = $parent->section_id;
    }
  }

  public function run()
  {
    if( !$this->isAvailable() )
      return;

    echo CHtml::openTag('div', array('class' => 'group-view', 'id' => 'product-parameters'));
    $this->renderTable();
    echo CHtml::closeTag('div');
  }

  private function renderTable()
  {
    echo '<table class="items table table-striped table-bordered param-table">';
    echo '<thead><tr><th>Параметры</th><th>Значения</th></tr></thead>';
    echo '<tbody>';
    $this->renderBody();
    echo '</tbody>';
    echo '</table>';
  }

  private function renderBody()
  {
    foreach(BProductParam::model()->getParameters($this->model) as $parameter)
    {
      if( $parameter->isGroup() )
      {
        echo '<tr class="group"><td colspan="2">'.$parameter->name.'</td></tr>';
      }
      else
      {
        if( in_array($parameter->id, $this->hideParameters) )
          echo '<tr style="display: none">';
        else
          echo '<tr>';
        echo '<th>';
        echo CHtml::label($parameter->name, null);
        echo '</th>';
        echo '<td>';
        $this->renderParameter($parameter);
        echo '</td></tr>';
      }
    }
  }

  /**
   * @param BProductParam $param
   */
  private function renderParameter($param)
  {
    if( $this->model->asa('productColorBehavior') && $this->model->getColorParameterId() == $param->id )
    {
      $this->renderColorParameter($param, true);
      return;
    }

    if( $this->model->asa('updateParameterColorGroupBehavior') && $this->model->colorGroupParameterId == $param->id )
    {
      $this->renderColorParameter($param, false);
      return;
    }

    switch($param->type)
    {
      case 'text':
      case 'slider':
        echo CHtml::activeTextField($param, "[$param->id]value");
      break;

      case 'checkbox':
        echo CHtml::tag('div', array('style' => 'float: left'), false, false);
        echo $this->form->checkBoxList($param, "[$param->id]value", CHtml::listData($param->variants, 'id', 'name'), array('template' => '<span class="{labelCssClass}">{input}{label}</span>'));
        echo CHtml::closeTag('div');
      break;

      case 'select':
        echo $this->form->dropDownList($param, "[$param->id]value", array('' => 'Не задано') + CHtml::listData($param->variants, 'id', 'name'));
      break;

      case 'radio':
        echo CHtml::tag('div', array('style' => 'float: left'), false, false);
        echo $this->form->radioButtonList($param, "[$param->id]value", CHtml::listData($param->variants, 'id', 'name'));
        echo CHtml::closeTag('div');
      break;
    }
  }

  /**
   * @param BProductParam $param
   * @param boolean $image
   */
  private function renderColorParameter($param, $image)
  {
    $variants = array();

    foreach($param->variants as $i => $variant)
    {
      if($image )
        $variants[$variant->id] = CHtml::image($variant->getImage(), $variant->alt, array('style' => 'height: 25px; background: '.$variant->notice , 'title' => $variant->name, ));
      else
        $variants[$variant->id] = CHtml::tag('span', array('style' => 'display: block; height: 25px; width: 25px; background-color: '.$variant->notice , 'title' => $variant->name, ));

      if( is_array($param->value) && in_array($variant->id, $param->value) )
      {
        echo CHtml::tag('span', array('class' => 'checkbox'),
          CHtml::label($variants[$variant->id], null).
          CHtml::hiddenField("BProductParamName[$param->id][value][]", $variant->id)
        );
      }
    }
  }

  private function isAvailable()
  {
    return !$this->controller->popup && $this->controller->isUpdate();
  }
}
