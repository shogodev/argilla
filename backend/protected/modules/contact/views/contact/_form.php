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

<script>
  $(function(){
    //-----------------------------------------------------------------------------
    // Добавить тестовое поле
    var textblockCounter = 0;
    $('.action.btn[data-action="add-textblock"]').on('click', function(){
      var name    = $('<input type="text" style="width:250px" name="ContactTextBlock[new][' + textblockCounter + '][name]"    placeholder="Заголовок" />');
      var sysname = $('<input type="text" style="width:250px" name="ContactTextBlock[new][' + textblockCounter + '][sysname]" placeholder="Системное имя" />');

      // создание кнопки "Удалить"
      var button_delete = $('<a href="#" rel="tooltip" title="Удалить тестовый блок" class="btn btn-alone delete" />');
      button_delete.on('click', function(e) {
        e.preventDefault();
        $(this).parent('div').remove();
      });

      var content = $('<div style="margin-bottom:5px" />');
      content.append(name);
      $(content).append(' ');
      content.append(sysname);
      $(content).append(' ');
      $(content).append(button_delete);
      $('#textblock-add-cell').append(content);

      textblockCounter++;
    });

    //-----------------------------------------------------------------------------
    // Добавление группы полей
    var group_counter = 1;
    $('.action.btn[data-action="group"]').on('click', function(){
      // создание поля с названием поля
      var name    = $('<input type="text" style="width:250px" name="BContactGroup[' + group_counter + '][name]" placeholder="Название" />');

      // создание поля с системным именем поля
      var sysname = $('<input type="text" style="width:250px" name="BContactGroup[' + group_counter + '][sysname]" placeholder="Системное имя" />');

      // создание кнопки "Удалить"
      var button_delete = $('<a href="#" rel="tooltip" title="Удалить поле" class="btn btn-alone delete" />');
      button_delete.on('click', function(e) {
        e.preventDefault();
        $(this).parent('div').remove();
      });

      var content = $('<div style="margin-bottom:5px" />');
      content.append(name);
      $(content).append(' ');
      content.append(sysname);
      $(content).append(' ');
      $(content).append(button_delete);

      $('#group-add-cell').append(content);
      group_counter++;
    });

    //-----------------------------------------------------------------------------
    // Добавление поля к группе
    $('.action.btn[data-action="field"]').on('click', function(){
      var group = $(this).attr('data-group'),
          list = $('.field-add-cell[data-group=' + group + '] ul');
      var last_id = list.find('li').size();

      // создание иконки
      var icon = $('<i class="icon-move" style="background:none;"/>');

      // создание поля со значением поля
      var name = $('<input type="text" style="width:250px" name="BContactField[new][' + last_id + '][' + group + '][value]" />');

      // создание поля с описанием поля
      var description = $('<input type="text" style="width:500px" name="BContactField[new][' + last_id + '][' + group + '][description]" />');

      // создание кнопки "Удалить"
      var button_delete = $('<a href="#" rel="tooltip" title="Удалить поле" style="background:none;content:none;" class="btn btn-alone delete" />');
      button_delete.on('click', function(e) {
        e.preventDefault();
        $(this).parent('li').remove();
      });

      // добавляем все созданные элементы в список
      var content = $('<li class="not-sortable" style="cursor:pointer;margin-bottom:5px;cursor:default;" />');

      $(content).append(icon);
      $(content).append(' ');
      $(content).append(name);
      $(content).append(' ');
      $(content).append(description);
      $(content).append(' ');
      $(content).append(button_delete);

      list.append(content);
    });

    // -----------------------------------------------------------------------------------------
    // Сортировка полей в группе
    $('.sortable').sortable({
      items: "li:not(.not-sortable)",
      update: function(event, ui) {
        sortFields(ui.item);
      }
    });

    // -----------------------------------------------------------------------------------------
    // Сортировка группы полей и сохранение позиции
    function sortFields(item) {
      var url      = "<?php echo Yii::app()->getController()->createUrl('sort', array())?>";
      var list     = new Array();
      var position = 1;
      var hasError = false;

      // создание массива полей
      $(item).parents('ul').children('li:not(.not-sortable)').each(function(){
        var listItem         = {};
        listItem['id']       = $(this).attr('data-fid');
        listItem['position'] = position;

        list.push(listItem);
        position++;
      });

      if( !hasError )
        $.post(url, {'sort' : list, 'type' : 'field'});
    }

    // -----------------------------------------------------------------------------------------
    // Сортировка текстовых блоков
    $('.textblock-container').sortable({
      update: function(event, ui) {
        handle: "tr",
        sortTextBlocks(ui.item);
      }
    });

    function sortTextBlocks(item)
    {
      var url      = "<?php echo Yii::app()->getController()->createUrl('sort', array())?>";
      var list     = new Array();
      var position = 1;
      var hasError = false;

      console.log(item);

      // создание массива полей
      $(item).parents('ul').children('li').each(function(){
        var listItem         = {};
        listItem['id']       = $(this).attr('data-textblock-id');
        listItem['position'] = position;

        list.push(listItem);
        position++;
      });

      if( !hasError )
        $.post(url, {'sort' : list, 'type' : 'textblock'});
    }

    // -------------------------------------------------------------------------------------------
    // Удаление полей
    $('.btn.delete').on('click', function(e){
      e.preventDefault();

      var id = $(this).attr('data-fid');
      var type = 'field';

      if( id === undefined )
      {
        id = $(this).attr('data-textblock-id');
        type = 'textblock';
      }

      if( id === undefined )
        return false;

      var url      = "<?php echo Yii::app()->getController()->createUrl('delete', array())?>";
      var self     = $(this);

      var callback = function(resp){
        $(self).parents('li').fadeOut();
      }

      $.post(url, {'delete' : {'id' : id, 'type' : type}}, callback);
    });

    // -------------------------------------------------------------------------------------------
    // Удаление группы полей
    $('span.delete-group').on('click', function(){
      if( !confirm("Вы уверены, что хотите удалить группу полей?") )
        return false;

      var url  = "<?php echo Yii::app()->getController()->createUrl('delete', array())?>";
      var id   = $(this).attr('data-group');

      var callback = function(resp){
        $('td.field-add-cell[data-group=' + id + ']').parents('tr').fadeOut();
      }

      $.post(url, {'delete' : {'id' : id, 'type' : 'group'}}, callback);
    });

  });
</script>