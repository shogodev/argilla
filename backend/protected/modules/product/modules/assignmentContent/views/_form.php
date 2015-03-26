<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var BAssignmentContentController $this
 * @var BAssignmentContent $model
 * @var BActiveForm $form
 */

Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

  <table class="detail-view table table-striped table-bordered">
    <tbody>
    <?php echo $form->dropDownListDefaultRow($model, 'section_id', BProductSection::model()->listData())?>

    <?php echo $form->dropDownListDefaultRow($model, 'type_id', BProductType::model()->listData())?>

    <?php echo $form->dropDownListDefaultRow($model, 'category_id', BProductCategory::model()->listData())?>

    <?php echo $form->dropDownListDefaultRow($model, 'collection_id', BProductCollection::model()->listData())?>

    <?php echo $form->dropDownListRow($model, 'location', AssignmentContentModule::$locations)?>

    <?php echo $form->ckeditorRow($model, 'content', AssignmentContentModule::$locations)?>

    <?php echo $form->checkBoxRow($model, 'visible');?>
    </tbody>
  </table>

<?php
$this->renderPartial('//_form_buttons', array('model' => $model));
$this->endWidget();