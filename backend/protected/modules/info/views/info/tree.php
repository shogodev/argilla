<?php
/**
 * @var BInfo $model
 * @var BInfoController $this
 * @var integer $current
 */
?>

<aside id="sidebar" class="span4">
  <table class="table table-striped table-bordered">
    <thead><tr><th>Структура</th></tr></thead>
    <tbody>
    <tr>
      <td>
        <?php $this->renderPartial('_tree', $_data_)?>
      </td>
    </tr>
    </tbody>
  </table>

  <?php $this->widget('TreeviewActionsWidget', array('model' => $model));?>

</aside>

<section id="content" class="offset4">
  <?php echo $this->renderPartial('_form', array('model' => $model, 'path' => $path)); ?>
</section>

<script>
  $(function()
  {
    $('ul.filetree').on('click', 'li#node_1>a', function(e){
      e.preventDefault();
    });

    var drugCallback = function(target, draggableItem)
    {
      var callback = function callback(resp)
      {
        if( $(resp).attr('id') == treeId )
        {
          $('#sidebar').find('#'+treeId).html($(resp).html());
          $('#' + treeId).treeview({'persist':'cookie', 'collapsed':true, 'animated':'fast'});
          initTreeDrugAndDrop($('#' + treeId));
        }
      }

      $.post(drugAndDropUrl, { 'drug' : draggableItem.attr('id').match(/node_(\d+)/)[1],
                               'drop' : target.attr('id').match(/node_(\d+)/)[1],
                               'current' : $('#' + treeId + ' li.current').length > 0 ? $('#' + treeId + ' li.current').attr('id').match(/node_(\d+)/)[1] : 0
      } , callback);
    };

    var initTreeDrugAndDrop = function(tree)
    {
      var treeItems = tree.find('li');

      $(treeItems).droppable({
        tolerance : 'pointer',
        hoverClass: 'drop-hover',
        greedy: true,
        drop: function()
        {
          targetSelector = $(this);
        }
      });

      $(treeItems).draggable({
        connectToSortable: '#' + tree.attr('id'),
        revert: true,
        revertDuration: 0,
        draggableItem: null,
        start: function()
        {
          this.draggableItem = $(this);
          parentSelector = $(this).parent().parent();
          targetSelector = null;
        },
        stop: function()
        {
          if( targetSelector )
            drugCallback(targetSelector, this.draggableItem);

          if ( targetSelector !== null )
          {
            if ( targetSelector.hasClass('folder') )
            {
              $(this).appendTo(targetSelector.children('ul'));
            }
            else
            {
              var htmlContent = '<div class="hitarea folder-hitarea collapsable-hitarea"></div>'+ targetSelector.html() +'<ul style="display: block;"></ul>';
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
                    .html('<a href="'+ parentSelector.children('a').attr('href') +'">' + parentSelector.children('a').html() + '</a>');
          }
        }
      });
    };

    var treeId = '<?php echo 'tree_'.get_class($model)?>';
    var drugAndDropUrl = '<?php echo $this->createUrl('info/drugAndDrop')?>';
    var parentSelector;
    var targetSelector;

    initTreeDrugAndDrop($('#' + treeId));
  });
</script>