<?php
/**
 * @var Menu $model
 */
?>

<div class="overlay-white" style="display: none; position: fixed; z-index: 2000; left: 0; right: 0; top: 0; bottom: 0; background: #fff; background: rgba(255,255,255,0.7);"></div>
<div id="edit-custom-item" class="popup" style="display: none; position: absolute; z-index: 2001; top: 50%; left: 50%; margin-left: -300px; margin-top: -100px; width: 600px;">
  <form method="post" id="menu-custom-item-form">
    <table class="detail-view table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="2">Создание / редактирование элемента меню</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <th>Название: </th>
          <td>
            <?php echo CHtml::hiddenField('BFrontendCustomMenuItem[id]', '', array('class' => 'custom-item id'));?>
            <?php echo CHtml::hiddenField('BFrontendCustomMenuItem[menu_id]', $model->id, array('class' => 'custom-item id'));?>
            <?php echo CHtml::textField('BFrontendCustomMenuItem[name]', '', array('class' => 'custom-item name', 'style' => 'width: 495px'));?>
          </td>
        </tr>
        <tr>
          <th>Url: </th>
          <td><?php echo CHtml::textField('BFrontendCustomMenuItem[url]', '', array('class' => 'custom-item url', 'style' => 'width: 495px'));?></td>
        </tr>
        <tr>
          <th>
            Параметры
          </th>
          <td>
            <ul class="custom-item data" style="margin: 0; list-style: none;"></ul>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center;">
            <?php echo CHtml::tag('button', array('class' => 'btn custom-item add-data'), 'Добавить параметр');?>
            <?php echo
              CHtml::ajaxSubmitButton(
                'Сохранить',
                Yii::app()->controller->createUrl('menuCustomItem/save'),
                array(
                  'type'    => 'POST',
                  'cache'   => false,
                  'success' => 'function(data) {afterSubmitBFrontendCustomMenuItemForm(data)}'
                ),
                array(
                  'class' => 'btn'
                )
              );
            ?>
            <?php echo CHtml::tag('button', array('class' => 'btn custom-item close-popup'), 'Закрыть');?>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</div>

<script>
  $(function(){
    //--------------------------------------------------------------------
    // Добавление нового параметра
    $('.btn.custom-item.add-data').on('click', function(e){
      e.preventDefault();

      var count  = $('.custom-item.data li').size() + 1;
      addCustomItemParam(count, '', '');
    });

    //-------------------------------------------------------------------
    // Редактирование BFrontendCustomMenuItem
    $('.edit-custom-item').live('click', function(e){
      e.preventDefault();

      var url = "<?php echo Yii::app()->controller->createUrl('menuCustomItem/getData')?>";
      var id  = $(this).attr('data-model-id');

      var callback = function( resp ) {
        if( resp.error === undefined )
          appendDataToForm(resp);
        else
          alert(resp.error);
      };

      $.post(url, {"id" : id}, callback, "json");
    });

    //------------------------------------------------------------------
    // Добавление нового BFrontendCustomMenuItem
    $('#create-menu-entry').on('click', function(){
      clearDataRows();
      $('.custom-item.id').val('');
      $('.custom-item.url').val('');
      $('.custom-item.name').val('');
    });
  });

  //---------------------------------------------------------------------
  // Добавление данных к форме
  function appendDataToForm(data)
  {
    clearDataRows();

    $('.custom-item.id').val(data.model.id);
    $('.custom-item.url').val(data.model.url);
    $('.custom-item.name').val(data.model.name);

    for( var i = 0; i < data.data.length; i++ ) {
      addCustomItemParam(i, data.data[i].name, data.data[i].value);
    }
  }

  //---------------------------------------------------------------------
  // Событие после сохранение формы
  function afterSubmitBFrontendCustomMenuItemForm(data)
  {
    location.reload();
  }

  //---------------------------------------------------------------------
  // Удаление строк для данных
  function clearDataRows()
  {
    $('.custom-item.data li').each(function(){
      $(this).remove();
    });
  }

  //----------------------------------------------------------------------
  // Добавление параметра
  function addCustomItemParam(id, name, value)
  {
    var inputStyle = 'style="width: 495px;"';

    var li          = $('<li>');
    var nameField   = $('<input type="text" placeholder="Название" value="'+name+'" name="BFrontendCustomMenuItem[data]['+id+'][name]" '+inputStyle+' />');
    var valueField  = $('<input type="text" placeholder="Значение" value="'+value+'" name="BFrontendCustomMenuItem[data]['+id+'][value]" '+inputStyle+' />');

    var parent = $('.custom-item.data');

    li.append(nameField);
    li.append(' ');
    li.append(valueField);

    parent.append(li);
  }
</script>