<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.BPkColumn
 */
class BPkColumn extends BDataColumn
{
  public $htmlOptions = array('class' => 'center span1');

  public $filter = false;

  protected $popup = false;

  protected $assocSrcId;

  protected $assocSrc;

  protected $assocDst;

  protected $ajaxUrl;

  public function init()
  {
    $this->popup      = Yii::app()->controller->popup;
    $this->assocSrcId = Yii::app()->request->getQuery('srcId');
    $this->assocSrc   = Yii::app()->request->getQuery('src');
    $this->assocDst   = Yii::app()->request->getQuery('dst');

    $this->ajaxUrl = Yii::app()->controller->createUrl(
      'association', array('srcId' => $this->assocSrcId, 'src' => $this->assocSrc, 'dst' => $this->assocDst)
    );

    $this->registerScript();
    parent::init();
  }

  protected function renderDataCellContent($row, $data)
  {
    if( !$this->popup )
    {
      parent::renderDataCellContent($row, $data);
    }
    else
    {
      $this->renderPkCheckboxContent($data);
    }
  }

  protected function renderPkCheckboxContent(CActiveRecord $data)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('src', '='.$this->assocSrc);
    $criteria->compare('src_id', '='.$this->assocSrcId);
    $criteria->compare('dst', '='.strtolower(get_class($data)));
    $criteria->compare('dst_id', '='.$data->getPrimaryKey());

    $options = array(
      'type' => 'checkbox',
      'class' => 'select',
      'id' => 'pk_'.$data->getPrimaryKey(),
    );

    if( BAssociation::model()->find($criteria) )
      $options['checked'] = 'checked';

    echo CHtml::tag('input', $options);
  }

  protected function registerScript()
  {
    Yii::app()->clientScript->registerScript('pkColumnChange', <<<EOD
  $('.grid-view input.select').live('change', function(){
    var value = $(this).prop('checked') ? 1 : 0;
    var id    = $(this).attr('id').match(/pk_(\d+)/)[1];

    $.post('{$this->ajaxUrl}', {'value' : value, 'ids' : id}, null, 'json');
  });
EOD
, CClientScript::POS_READY);
  }
}