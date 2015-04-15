<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.grid
 */
class BPkColumn extends BDataColumn
{
  public $name = false;

  public $htmlOptions = array('class' => 'center span1');

  public $filter = false;

  public $header = '#';

  public $ajaxAction = 'association';

  public $ajaxUrl;

  public $associationClass = 'BAssociation';

  protected $popup = false;

  protected $assocSrcId;

  protected $assocSrc;

  protected $assocDst;

  public function init()
  {
    $this->popup      = Yii::app()->controller->popup;
    $this->assocSrcId = Yii::app()->request->getQuery('srcId');
    $this->assocSrc   = Yii::app()->request->getQuery('src');
    $this->assocDst   = Yii::app()->request->getQuery('dst');

    if( $this->ajaxUrl === null )
    {
      $this->ajaxUrl = Yii::app()->controller->createUrl(
        $this->ajaxAction, array('srcId' => $this->assocSrcId, 'src' => $this->assocSrc, 'dst' => $this->assocDst)
      );

      $this->registerScript();
    }

    parent::init();
  }

  public function renderFilterCell()
  {
    if( $this->popup && $this->filter !== false )
    {
      echo CHtml::activeLabel($this->grid->filter, 'Привязанные', array('id' => false));
      echo CHtml::activeDropDownList($this->grid->filter, 'associated', CHtml::listData($this->grid->filter->yesNoList(), 'id', 'name'), array('id' => false, 'prompt' => ''));
    }
  }

  protected function renderDataCellContent($row, $data)
  {
    if( !$this->popup )
      echo $data->getPrimaryKey();
    else
      $this->renderPkCheckboxContent($data);
  }

  protected function renderPkCheckboxContent(CActiveRecord $data)
  {
    $parameters['src'] = $this->assocSrc;
    $parameters['src_id'] = $this->assocSrcId;
    $parameters['dst'] = $this->assocDst;
    $parameters['dst_id'] = $data->getPrimaryKey();

    $options = array(
      'type' => 'checkbox',
      'class' => 'select',
      'id' => 'pk_'.$data->getPrimaryKey(),
    );

    $association = Yii::createComponent($this->associationClass);

    if( !method_exists($association, 'getChecked') )
      throw new CHttpException(500, 'Класс заданный свойством associationClass должен реализовывать метод getChecked().');

    $status = $association->getChecked($parameters);
    if( $status )
      $options['checked'] = 'checked';
    else if( $status === null )
      $options['disabled'] = 'disabled';

    echo CHtml::tag('input', $options);
  }

  protected function registerScript()
  {
    Yii::app()->clientScript->registerScript('pkColumnChange', "
      $('body').on('change', '.grid-view input.select', function() {
        var value = $(this).prop('checked') ? 1 : 0;
        var id    = $(this).attr('id').match(/pk_(\d+)/)[1];

        $.post('{$this->ajaxUrl}', {'value' : value, 'ids' : id});
      });
    ", CClientScript::POS_READY);
  }
}