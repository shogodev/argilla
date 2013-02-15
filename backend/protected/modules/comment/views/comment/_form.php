<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 1/6/13
 *
 * @var Comment $model
 * @var BCommentController $this
 * @var BActiveForm $form
 */

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

<table class="detail-view table table-striped table-bordered">
  <tbody>
  <?php echo $form->dropDownListRow($model, 'user_id', CHtml::listData(BFrontendUser::model()->findAll(), 'id', 'login'));?>
  <?php echo $form->textFieldRow($model, 'model');?>
  <?php echo $form->textFieldRow($model, 'item');?>
  <?php echo $form->textAreaRow($model, 'message');?>

  <?php if( !$model->isNewRecord ):?>
  <tr>
    <th>
      Раздел
    </th>
    <td>
      <?php echo $model->getModelSectionName();?>
    </td>
  </tr>
  <tr>
    <th>Название связанной страницы:</th>
    <td>
      <?php
        try
        {
          echo $model->getItemName();
        }
        catch( CException $e )
        {
          echo $e->getMessage();
        }
      ?>
    </td>
  </tr>
  <?php endif;?>

  <?php echo $form->checkBoxRow($model, 'visible');?>
  </tbody>

</table>

<?php
$this->renderPartial('//_form_buttons', array('model' => $model));
$this->endWidget();
?>