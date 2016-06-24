<?php
/**
 * @var $this BContactController
 * @var $model BContact
 * @var $form BActiveForm
 */

Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));

$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'name'); ?>
  <?php echo $form->textFieldRow($model, 'sysname'); ?>
  <?php echo $form->ckeditorRow($model, 'address'); ?>
  <?php echo $form->textFieldRow($model, 'url'); ?>
  <?php echo $form->ckeditorRow($model, 'notice'); ?>
  <?php echo $form->uploadRow($model, 'img', false); ?>
  <?php echo $form->uploadRow($model, 'img_big', false); ?>
  <?php echo $form->textareaRow($model, 'map'); ?>

  <!-- FIELDGROUPS -->
  <?php if( !empty($model->contactGroups) ):?>
    <?php foreach( $model->contactGroups as $group):?>
    <tr>
      <th>
        <?=$group->name?><br />
        <span style="color: #5b5b5b; font-weight: normal;">[<?php echo $group->sysname?>]</span>
        <!-- ADD FIELD -->
          <div style="margin-top:10px"><span class="btn btn-info action" data-group="<?=$group->id?>" data-action="field">Добавить поле</span></div>
          <div style="margin-top:10px"><span class="btn btn-danger delete-group" data-group="<?=$group->id?>" data-action="field">Удалить группу</span></div>
        <!-- END ADD FIELD -->
      </th>
      <td class="field-add-cell" data-group="<?=$group->id?>">

        <!-- GROUP FIELDS -->
        <ul style="list-style:none;margin:0;cursor:pointer" class="sortable">
        <?php if( !empty($group->contactFields) ):?>
          <?php foreach( $group->contactFields as $field ):?>
          <li style="margin-bottom:5px" data-fid="<?=$field->id?>" data-position="<?=$field->position?>">
            <i class="icon-move"></i>
            <?php echo CHtml::textField('BContactField[' . $field->id . '][value]', $field->value, array('style' => 'width: 250px;'))?>
            <?php echo CHtml::textField('BContactField[' . $field->id . '][description]', $field->description, array('style' => 'width: 500px;'))?>
            <a href="#" rel="tooltip" data-fid="<?=$field->id?>" title="Удалить поле" class="btn btn-alone delete"></a>
          </li>
          <?php endforeach;?>
        <?php endif;?>
        </ul>
        <!-- END GROUP FIELDS -->

      </td>
    </tr>
    <?php endforeach;?>
  <?php endif;?>
  <!-- END FIELDGROUPS -->

  <!-- TEXTBLOCKS -->
  <?php if( !empty($model->textblocks) ):?>
  <tr>
    <th>
      Текстовые блоки
    </th>
    <td>
      <ul class="textblock-container" style="list-style: none;padding: 0; margin: 0;">
        <?php foreach( $model->textblocks as $textblock ):?>
        <li style="cursor: pointer;padding-bottom: 15px;" data-textblock-id="<?php echo $textblock->id?>">
          <div style="display:table-cell;width:900px">
            <i class="icon-move" style="vertical-align: top;float: left;margin-top: 6px;"></i>
            <div style="vertical-align: top; float: left; padding-left: 4px;text-align: center;">
              <?php echo CHtml::textField(get_class($textblock) . '[' . $textblock->id . '][name]', $textblock->name, array('style' => 'width: 250px'))?><br />
              <strong>[<?php echo $textblock->sysname?>]</strong>
            </div>
            <?php echo CHtml::textArea(get_class($textblock) . '[' . $textblock->id . '][content]', $textblock->content, array('style' => 'max-width: 500px;vertival-align: top; margin-left: 4px;')); ?>
            <a href="#" rel="tooltip" data-textblock-id="<?=$textblock->id?>" title="Удалить поле" class="btn btn-alone delete" style="vertical-align: top;"></a>
          </div>
        </li>
        <?php endforeach;?>
      </ul>
    </td>
  </tr>
  <?php endif;?>
  <!-- END TEXTBLOCKS -->

  <!-- ADD FIELDGROUP -->
  <tr>
    <th>
      <span data-action="group" class="btn btn-info action">Добавить группу</span>
    </th>
    <td id="group-add-cell"></td>
  </tr>
  <!-- END ADD FIELDGROUP -->

  <!-- ADD TEXTBLOCK -->

  <tr>
    <th>
      <span data-action="add-textblock" class="btn btn-info action">Добавить текстовое поле</span>
    </th>
    <td id="textblock-add-cell"></td>
  </tr>

  <!-- END ADD TEXTBLOCK -->
</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>