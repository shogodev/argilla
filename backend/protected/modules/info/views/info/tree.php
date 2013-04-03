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