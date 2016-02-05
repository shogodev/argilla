<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets
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
    $items = $this->model->findAll();

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
      $url = '#';

      if( is_array($action) )
      {
        $title = Arr::get($action, 'title', '');
        $class = Arr::get($action, 'class', $id);
        $url = Arr::get($action, 'url', '#');
        $submit = Arr::get($action, 'submit', false);
        $onClick = Arr::get($action, 'onClick');
      }

      foreach($items as $item)
      {
        $jdata[$item->id][$id] = array('disabled' => isset($item->$id) && !$item->$id ? true : false,
          'toggle' => $url === '#' ? true : false,
          'url' => $url === '#' ? '#' : $this->buildUrl($url, $item),
        );
      }

      echo CHtml::link('', $url, array('rel' => 'tooltip',
          'title' => $title,
          'data-action' => $id,
          'onClick' => $onClick,
          'class' => 'btn btn-small '.$class)).PHP_EOL;
    }

    echo CHtml::closeTag('div');

    $jdata = CJavaScript::encode($jdata);

    $this->registerScriptTreeViewActions($jdata);
    $this->registerScriptTreeViewActionsDragAndDrop();
  }

  private function buildUrl($url, $item)
  {
    $urlParams = Arr::reset($url);
    $urlAction = key($url);

    foreach($urlParams as $key => $param)
      $urlParams[$key] = Yii::app()->evaluateExpression($param, array('data' => $item));

    return Yii::app()->controller->createUrl($urlAction, $urlParams);
  }

  private function registerScriptTreeViewActions($jdata)
  {
    $modelId = $this->model->id;
    $modelClass = get_class($this->model);
    $indexUrl = Yii::app()->controller->createUrl('index');
    $ajaxUrl = Yii::app()->controller->createUrl('toggle', array('attribute' => '_attr_', 'id' => '_id_'));
    $deleteUrl = Yii::app()->controller->createUrl('delete', array('id' => '_id_'));

    Yii::app()->clientScript->registerScript(__CLASS__.'_InitPlugin', "
      var treeActions = {$jdata};
      var modelClass  = '{$modelClass}';
      var modelId     = '{$modelId}';
      var indexUrl    = '{$indexUrl}';
      var ajaxUrl     = '{$ajaxUrl}';
      var deleteUrl   = '{$deleteUrl}';

      function initTreeActions()
      {

        $('.filetree li:not(#node_1) a').unifloat({
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
      }
    ", CClientScript::POS_READY);

    Yii::app()->clientScript->registerScript(__CLASS__, "
      initTreeActions();

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

            $.post(deleteUrl.replace('_id_', id), {}, callback);
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

        $.post(ajaxUrl.replace('_attr_', action).replace('_id_', id), {}, callback);
      });

      $('#tree_'+modelClass+' a').each(function()
      {
        var id = $(this).parent().attr('id').match(/node_(\d+)/)[1];
        if( treeActions[id].visible && treeActions[id].visible.disabled === true )
          $(this).parent().addClass('disabled');
      });
    ", CClientScript::POS_READY);
  }

  private function registerScriptTreeViewActionsDragAndDrop()
  {
    $treeId = 'tree_'.get_class($this->model);
    $dragAndDropUrl = Yii::app()->controller->createUrl('info/dragAndDrop');

    Yii::app()->clientScript->registerScript(__CLASS__.'_dragAndDrop', "
      var treeId = '{$treeId}';

      var dragAndDropUrl = '{$dragAndDropUrl}';
      var parentSelector;
      var targetSelector;

      $('ul.filetree').on('click', 'li#node_1>a', function(e){
        e.preventDefault();
      });

      var dropCallback = function(target, draggableItem)
      {
        var callback = function callback(resp)
        {
          if( $(resp).attr('id') == treeId )
          {
            $('#sidebar').find('#'+treeId).html($(resp).html());
            $('#' + treeId).treeview({'persist':'cookie', 'collapsed':true, 'animated':'fast'});
            initTreeDragAndDrop($('#' + treeId));
            initTreeActions();
          }
        }

        var draggableText = draggableItem.children('a').text()
        var targetText = target.children('a').text()
        var current = $('#' + treeId + ' li.current').length > 0 ? $('#' + treeId + ' li.current').attr('id').match(/node_(\d+)/)[1] : 0

        var dragId = draggableItem.attr('id').match(/node_(\d+)/)[1];
        var dropId = target.attr('id').match(/node_(\d+)/)[1];
        var parentDragId = parentSelector.attr('id').match(/node_(\d+)/)[1];

        draggableItem.height('auto');

        if( parentDragId == dropId )
          return false;

        if( confirm('Вы действительно хотите перенести раздел \"' + draggableText + '\" в \"' + targetText + '\"' ) )
        {
          $.post(dragAndDropUrl, {
              'action' : 'move',
              'drag' : dragId,
              'drop' : dropId,
              'current' : current
            }
            , callback);

          return true;
        }
        else
         return false;
      };

      var initTreeDragAndDrop = function(tree)
      {
        var treeItems = tree.find('li');

        $(treeItems).droppable({
          tolerance : 'pointer',
          hoverClass: 'drop-hover',
          greedy: true,
          drop: function() {
            targetSelector = $(this);
          }
        });

        $(treeItems).draggable({
          connectToSortable: '#' + tree.attr('id'),
          revert: true,
          revertDuration: 0,
          draggableItem: null,
          start: function() {
            this.draggableItem = $(this);
            parentSelector = $(this).parent().parent();
            targetSelector = null;
          },
          stop: function()
          {
            if( targetSelector && !dropCallback(targetSelector, this.draggableItem) )
              return;

            if ( targetSelector !== null )
            {
              if ( targetSelector.hasClass('folder') )
              {
                $(this).appendTo(targetSelector.children('ul'));
              }
              else
              {
                var htmlContent = '<div class=\"hitarea folder-hitarea collapsable-hitarea\"></div>'+ targetSelector.html() +'<ul style=\"display: block;\"></ul>';
                targetSelector.removeClass('file').addClass('folder collapsable').html(htmlContent);
                $(this).appendTo(targetSelector.children('ul'));
              }
            }
            else
              $(this).appendTo(tree);

            if( !parentSelector.children('ul').has('li').length )
            {
              parentSelector.removeClass('folder')
                .addClass('file')
                .html('<a href=\"'+ parentSelector.children('a').attr('href') +'\">' + parentSelector.children('a').html() + '</a>');
            }
          }
        });
      };

      initTreeDragAndDrop($('#' + treeId));
    ", CClientScript::POS_READY);
  }
}