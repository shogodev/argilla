<?php
/* @var $this BInfoController */
/* @var $model BInfo */
/* @var $path string */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/**
 * @var BActiveForm $form
 */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire();?>

<table id="yw0" class="detail-view table table-striped table-bordered">
<thead>
  <th colspan="2">Информационная страница</th>
</thead>
<tbody>

  <tr>
    <td colspan="2"><?php echo $path?> &nbsp;</td>
  </tr>

  <?php echo $form->datePickerRow($model, 'date'); ?>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'template', array('maxlength' => 255)); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'url', array('rel' => 'extender', 'data-extender' => 'translit', 'data-source' => 'input[name*="name"]')); ?>
  <tr>
    <td colspan="2">
      <div style="padding-left: 50px; font-size: 10px">
        <p>Допустимые значения переменных:</p>
        <p><b>{HTTP_HOST}</b> - используется при построении ссылки на текущий доменный адрес.<br>
          Пример: <b>{HTTP_HOST}/faq/</b> . Получаем: <b><?php echo Yii::app()->getRequest()->getHostInfo()?>/faq/ </b><br>
          <b>{EXTERNAL_URL:<i>some_url</i></b> - используется для построения ссылок на внешние ресурсы<br>
          Пример: <b>{EXTERNAL_URL:http://www.shogo.ru</b>. Получаем открытие внешнего ресурса <b>http://www.shogo.ru</b>
        </p>
      </div>
    </td>
  </tr>

  <?php echo $form->ckeditorRow($model, 'notice'); ?>

  <?php echo $form->uploadRow($model, 'info_files'); ?>

  <?php echo $form->ckeditorRow($model, 'content'); ?>

  <?php echo $form->textFieldRow($model, 'reference', array('rel' => 'extender', 'data-extender' => 'link')); ?>

  <?php echo $form->checkBoxRow($model, 'visible'); ?>

  <?php echo $form->checkBoxRow($model, 'siblings'); ?>

  <?php echo $form->checkBoxRow($model, 'children'); ?>

  <?php echo $form->checkBoxRow($model, 'menu'); ?>

  <?php echo $form->checkBoxRow($model, 'sitemap'); ?>

</tbody>
</table>

<?php $this->renderPartial('_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>