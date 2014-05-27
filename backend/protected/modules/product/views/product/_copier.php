<?php
/**
 * @var BProductController $this
 * @var BProduct $model
 */
?>
<?php if( !$model->isNewRecord ){?>
  <tr>
    <th>Действия</th>
    <td>
      <?php echo CHtml::tag('button', array('class' => 'btn btn-info copy-product-btn', 'data-id' => $model->id), 'Копировать');?>
      <?php echo CHtml::tag('button', array('class' => 'btn btn-info copy-product-btn with-images', 'data-id' => $model->id), 'Копировать c изображениями');?>
    </td>
  </tr>

  <?php
  Yii::app()->clientScript->registerScript('productCopier', "$('.copy-product-btn').on('click', function(e)
  {
    e.preventDefault();
    var url = '".$this->createUrl('copyProduct')."';
    if( !confirm('Вы действительно хотите скопировать данный продукт?') )
      return;

    var finish = function(resp)
    {
      if( resp.url )
        document.location.href = resp.url
    };

    $.post(url, {id : $(this).data('id'), withImages : $(this).hasClass('with-images') ? 1 : 0 }, finish, 'json');
  });
  ", CClientScript::POS_READY)?>
<?php } ?>