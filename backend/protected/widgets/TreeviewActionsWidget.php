<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.TreeviewActionsWidget
 */
class TreeviewActionsWidget extends CWidget
{
  /**
   * @var CActiveRecord
   */
  public $model;

  public function run()
  {
    if( !isset($this->model) )
    {
      throw new CHttpException(500, '"model" have to be set!');
    }

    $actions = array();
    $items   = $this->model->findAll();

    if( method_exists($this->model, 'getTreeActions') )
      $actions = $this->model->getTreeActions();

    if( !empty($actions) )
    {
      $this->renderActions($actions, $items);
    }
  }

  private function renderActions(array $actions, array $items)
  {
    $jdata = array();

    echo CHtml::openTag('div', array('id' => 'treeview-actions'));

    foreach($actions as $id => $action)
    {
      $class = $id;
      $title = $action;
      $url   = '#';

      if( is_array($action) )
      {
        $title   = Arr::get($action, 'title', '');
        $class   = Arr::get($action, 'class', $id);
        $url     = Arr::get($action, 'url', '#');
        $submit  = Arr::get($action, 'submit', false);
        $onClick = Arr::get($action, 'onClick');
      }

      foreach($items as $item)
      {
        $jdata[$item->id][$id] = array('disabled' => isset($item->$id) && !$item->$id ? true : false,
                                       'toggle'   => $url === '#' ? true : false,
                                       'url'      => $url === '#' ? '#' : $this->buildUrl($url, $item),
        );
      }

      echo CHtml::link('', $url, array('rel' => 'tooltip',
                                       'title'       => $title,
                                       'data-action' => $id,
                                       'onClick'     => $onClick,
                                       'class'       => 'btn btn-small '.$class)).PHP_EOL;
    }

    echo CHtml::closeTag('div');

    $modelId    = $this->model->id;
    $modelClass = get_class($this->model);
    $jdata      = CJavaScript::encode($jdata);
    $indexUrl   = Yii::app()->controller->createUrl('index');
    $ajaxUrl    = Yii::app()->controller->createUrl('toggle', array('attribute' => '_attr_', 'id' => '_id_'));
    $deleteUrl  = Yii::app()->controller->createUrl('delete', array('id' => '_id_'));

    Yii::app()->clientScript->registerScript(__CLASS__, <<<EOD

var treeActions = {$jdata};
var modelClass  = '{$modelClass}';
var modelId     = '{$modelId}';
var indexUrl    = '{$indexUrl}';
var ajaxUrl     = '{$ajaxUrl}';
var deleteUrl   = '{$deleteUrl}';

$('.filetree li a').unifloat({
  rel: '#treeview-actions',
  posTop: { value: 'top - 2', auto: false },
  posLeft: { value: 'after', auto: false },

  onShow: function(source, target)
  {
    if( $(source).parent().attr('id') === undefined )
      return false;
    var id = $(source).parent().attr('id').match(/node_(\d+)/)[1];

    for(var i in treeActions[id])
    {
      var button = $(target).find('[data-action='+i+']');
      button.toggleClass('disabled', treeActions[id][i].disabled);
      button.attr('href', treeActions[id][i].url);
      button.data('toggle', treeActions[id][i].toggle);
      button.data('id', id);
    }
  }
});

$('#treeview-actions a').on('click', function(e)
{
  var self   = this;
  var action = $(this).data('action');
  var id     = $(this).data('id');

  if( action === 'delete' )
  {
    e.preventDefault();

    if( !confirm('Вы действительно хотите удалить данный элемент?') )
      return;

    return function(id)
    {
      var callback = function(resp)
      {
        document.location.href = indexUrl;
      };

      $.post(deleteUrl.replace('_id_', id), {}, callback, 'json');
    }(id);
  }

  if( !$(this).data('toggle') )
    return;
  else
    e.preventDefault();

  var callback = function(resp)
  {
    $(self).toggleClass('disabled');
    treeActions[id][action].disabled = !treeActions[id][action].disabled;

    if( treeActions[id].visible )
      $('#tree_'+modelClass+' li#node_'+id).toggleClass('disabled', treeActions[id].visible.disabled);

    if( $('#'+modelClass+'_'+action).length && modelId == id  )
      $('#'+modelClass+'_'+action).attr('checked', treeActions[id][action].disabled ? false : true);
  };

  $.post(ajaxUrl.replace('_attr_', action).replace('_id_', id), {}, callback, 'json');
});

$('#tree_'+modelClass+' a').each(function()
{
  var id = $(this).parent().attr('id').match(/node_(\d+)/)[1];
  if( treeActions[id].visible && treeActions[id].visible.disabled === true )
    $(this).parent().addClass('disabled');
});

EOD
    ,CClientScript::POS_READY);
  }

  private function buildUrl($url, $item)
  {
    $urlParams = Arr::reset($url);
    $urlAction = key($url);

    foreach($urlParams as $key => $param)
      $urlParams[$key] = Yii::app()->evaluateExpression($param, array('data' => $item));

    return Yii::app()->controller->createUrl($urlAction, $urlParams);
  }

}