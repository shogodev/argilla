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
    var content = $('<li class="not-sortable" style="margin-bottom:5px;cursor:default;" />');

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
    var list     = [];
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
    var list     = [];
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
    };

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
    };

    $.post(url, {'delete' : {'id' : id, 'type' : 'group'}}, callback);
  });

});