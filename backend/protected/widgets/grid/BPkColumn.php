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

    $class = new $this->associationClass;

    if( $class->getChecked($parameters) )
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