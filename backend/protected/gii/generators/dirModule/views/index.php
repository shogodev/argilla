<?php
/**
 * @var CCodeForm $form
 * @var DirModule $model
 */
?>
<h1>Генератор модулей справочников</h1>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

<div class="row">
  <?php echo $form->labelEx($model, 'author'); ?>

  <?php echo $form->dropDownList($model, 'author', AuthorList::getList()); ?>

  <div class="tooltip">
   Автор
  </div>

  <?php echo $form->error($model, 'author'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'module'); ?>

  <?php echo $form->dropDownList($model, 'module', $model->getModules()); ?>

  <div class="tooltip">
    Добавить в
  </div>

  <?php echo $form->error($model, 'module'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model,'className'); ?>

  <?php echo $form->textField($model, 'className', array('size'=>65)); ?>

  <div class="tooltip">
    Класс формы в единственном числе, должен содержать только буквы. (Пример Service)
  </div>

  <?php echo $form->error($model,'className'); ?>
</div>


<div class="row">
  <?php echo $form->labelEx($model,'label'); ?>

  <?php echo $form->textField($model, 'label', array('size'=>65)); ?>

  <div class="tooltip">
    Название формы
  </div>

  <?php echo $form->error($model,'label'); ?>
</div>


<div class="row">
  <?php echo $form->labelEx($model,'tableName'); ?>
  <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
    'model'=>$model,
    'attribute'=>'tableName',
    'name'=>'tableName',
    'source'=>Yii::app()->hasComponent($model->connectionId) ? array_keys(Yii::app()->{$model->connectionId}->schema->getTables()) : array(),
    'options'=>array(
      'minLength'=>'0',
      'focus'=>new CJavaScriptExpression('function(event,ui) {
					$("#'.CHtml::activeId($model,'tableName').'").val(ui.item.label).change();
					return false;
				}')
    ),
    'htmlOptions'=>array(
      'id'=>CHtml::activeId($model,'tableName'),
      'size'=>'65',
      'data-tooltip'=>'#tableName-tooltip'
    ),
  )); ?>
  <div class="tooltip" id="tableName-tooltip">
    This refers to the table name that a new model class should be generated for
    (e.g. <code>tbl_user</code>). It can contain schema name, if needed (e.g. <code>public.tbl_post</code>).
    You may also enter <code>*</code> (or <code>schemaName.*</code> for a particular DB schema)
    to generate a model class for EVERY table.
  </div>
  <?php echo $form->error($model,'tableName'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model,'controllerPosition'); ?>

  <?php echo $form->textField($model, 'controllerPosition', array('size' => 2)); ?>

  <div class="tooltip">
    Позиция backend контроллера
  </div>

  <?php echo $form->error($model,'controllerPosition'); ?>
</div>

<?php $this->endWidget(); ?>