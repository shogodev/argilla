<?php
/**
 * @var CActiveRecord    $model
 * @var BController $this
 * @var integer $current
 */
?>

<aside id="sidebar" class="span4">
  <table class="table table-striped table-bordered">
    <thead><tr><th>Структура</th></tr></thead>
    <tbody>
    <tr>
      <td>
        <?php
          $this->widget('CTreeView', array('options'     => array('persist'   => 'cookie',
                                                                  'collapsed' => true,
                                                                  'animated'  => 'fast'),
                                           'htmlOptions' => array('id'    => 'tree_'.get_class($model),
                                                                  'class' => 'filetree'),
                                           'data'        => $model->getTreeView(null, false, $this->createUrl($this->id.'/update/'), $current),
                                          ));
        ?>
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
    var drugCallback = function(target)
    {
      console.log(target);
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
        start: function()
        {
          parentSelector = $(this).parent().parent();
          targetSelector = null;
        },
        stop: function()
        {
          if( targetSelector )
            drugCallback(targetSelector);

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
    var parentSelector;
    var targetSelector;

    //initTreeDrugAndDrop($('#' + treeId));
  });
</script>