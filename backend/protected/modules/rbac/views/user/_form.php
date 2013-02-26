<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @var BUserController $this
 * @var BActiveForm $form
 * @var BUser $model
 */
Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));

$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'username'); ?>
  <?php echo $form->passwordFieldRow($model, 'passwordNew'); ?>

  <?php if( !$model->isNewRecord ):?>
  <?php echo $form->checkBoxListRow($model, 'roles', $roles)?>
  <?php endif;?>

</tbody>
</table>

<?php
$this->renderPartial('//_form_buttons', array('model' => $model));
$this->endWidget();