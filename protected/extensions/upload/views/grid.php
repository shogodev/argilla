<?php
/* @var $this UploadWidget */
/* @var $model CActiveRecord */
/* @var $gridId string */
/* @var $htmlOptions array */
?>

<?php
$columns = array(
  array(
    'header'              => 'Изоб.',
    'class'               => 'EImageColumn',
    'imagePathExpression' => '!empty($data["thmb"]) ? $data["thmb"] : $data["path"]',
    'width'               => 20/*135*/,
    'htmlOptions'         => array('class' => 'center', 'style' => 'width:6.5%'),
    //'belowText'           => '<a href="#" class="resize-pic">Вырезать превью</a>'
  ),
);

if( $this->multiple )
{
  $columns[] = array('name' => 'position', 'header' => 'Позиция', 'class' => 'OnFlyEditField', 'gridId' => $gridId, 'htmlOptions' => array('class' => 'span2'));
  $columns[] = array('name' => 'type', 'header' => 'Тип', 'class' => 'OnFlyEditField', 'dropDown' => $this->model->imageTypes, 'gridId' => $gridId, 'htmlOptions' => array('class' => 'span2'));
  $columns[] = array('name' => 'size', 'header' => 'Размер', 'htmlOptions' => array('class' => 'span2'));
  $columns[] = array('name' => 'notice', 'class' => 'OnFlyEditField', 'gridId' => $gridId, 'header' => 'Описание');
}

$columns[] = array(
  'class'           => 'bootstrap.widgets.TbButtonColumn',
  'template'        => '{delete}',
  'deleteButtonUrl' => function ($data) use ($model)
  {
    return Yii::app()->controller->createUrl('upload', array(
      'id'     => $model->id,
      'model'  => get_class($model),
      'fileId' => $data['id'],
      'attr'   => $this->attribute,
      'method' => 'delete'));
  },
);

if( $this->multiple )
{
  $this->widget('BGridView', array(
    'id'              => $gridId,
    'htmlOptions'     => $htmlOptions,
    'dataProvider'    => $this->model->getUploadedFiles(),
    'buttonsTemplate' => false,
    'columns'         => $columns,
  ));
}
else
{
  $this->widget('BGridView', array(
    'id'              => $gridId,
    'htmlOptions'     => array('style' => 'width: 15%'),
    'dataProvider'    => $this->model->getUploadedFiles(),
    'buttonsTemplate' => false,
    'hideHeader'      => true,
    'template'        => "{items}\n{pager}",
    'ajaxUpdate'      => null,
    'afterAjaxUpdate' => "js:function(){
                            var td = $('#' + this.ajaxUpdate[0]).parents('td');
                            if( td.find('.items a').length == 0 ){
                              td.find('.fileupload-files').show();
                              td.find('.fileupload-buttonbar').show();}}",
    'columns'         => $columns,
  ));
}
?>

<script type="text/javascript">
//<![CDATA[
  $('.resize-pic').click(function(e){
    e.preventDefault();

    // Блок с картинкой для ресайза
    var popupBlock = $('<div/>').addClass('img-resize-popup').html(
                        '<span class="img-resize-inner">'
                      +   '<img src="' + $(this).parent().find('img').attr('src') + '" alt="" id="raw-image" /><br />'
                      +   '<div style="float: left">'
                      +     '<div><label style="float: left;">W <input id="preview-width" type="text" name="preview-width" size="4" style="width: 32px; margin-bottom: 0;"></label>'
                      +     '<label>H <input id="preview-height" type="text" name="preview-height" size="4" style="width: 32px; margin-bottom: 0;"></label></div>'
                      +     '<input type="checkbox" id="img-resize-squared-lock" style="float: left;" />'
                      +     '<label for="img-resize-squared-lock" style="float: left; margin: 2px 0 0 5px;">Квадратные превью</label>'
                      +   '</div>'
                      +   '<div style="float: right; margin-top: 24px;">'
                      +     '<button class="btn btn-primary" name="preview-submit" id="preview-submit" type="submit">Применить</button> '
                      +     '<a class="btn close-popup btn-danger" href="">Закрыть</a>'
                      +   '</div>'
                      + '</span>'
                      );

    // Вызов оверлея и попапа для ресайза картинки
    $('body').append( $('<div/>').addClass('overlay-white') )
    .append( popupBlock );

    $('#raw-image').Jcrop({
      onChange: showSize,
      onSelect: showSize,
      onRelease: clearSize
    }, function(){
      jcrop_api = this;
    });
  });

  // Фиксирование соотношения сторон при выборе чекбокса "Квадратные превью"
  $('#img-resize-squared-lock').live('change', function(){
    jcrop_api.setOptions(
      this.checked ? { aspectRatio: 1/1 } : { aspectRatio: 0 }
    );
    jcrop_api.focus();
  });

  // Изменение выбранной области при изменении значения в поле ширина
  $('#preview-width').live('input', function(){
    var pos_x = jcrop_api.tellSelect().x,
        pos_y = jcrop_api.tellSelect().y,
        pos_x2 = jcrop_api.tellSelect().x2,
        pos_y2 = jcrop_api.tellSelect().y2;
    if ( $.isNumeric( $(this).val() )) {
      jcrop_api.setSelect([ pos_x, pos_y, parseInt(pos_x) + parseInt($(this).val()), pos_y2 ]);
    }
  });

  // Изменение выбранной области при изменении значения в поле высота
  $('#preview-height').live('input', function(){
    var pos_x = jcrop_api.tellSelect().x,
        pos_y = jcrop_api.tellSelect().y,
        pos_x2 = jcrop_api.tellSelect().x2,
        pos_y2 = jcrop_api.tellSelect().y2;
    if ( $.isNumeric( $(this).val() )) {
      jcrop_api.setSelect([ pos_x, pos_y, pos_x2, parseInt(pos_y) + parseInt($(this).val()) ]);
    }
  });

  // Обновление полей с размерами превью
  function showSize(c) {
    $('#preview-width').val(c.w);
    $('#preview-height').val(c.h);
  }

  // Очистка полей с размерами превью
  function clearSize(c) {
    $('#preview-width').val('');
    $('#preview-height').val('');
  }

  // Собственно кроп картинки
  $('#preview-submit').live('click', function(){
    var pos_x = jcrop_api.tellSelect().x,
        pos_y = jcrop_api.tellSelect().y,
        pos_x2 = jcrop_api.tellSelect().x2,
        pos_y2 = jcrop_api.tellSelect().y2;
    console.log( pos_x, pos_y, pos_x2, pos_y2 );
    closeResizePopup();
  });

  // Клик по кнопке закрыть
  $('.img-resize-popup .close-popup').live('click', function(e){
    e.preventDefault();
    closeResizePopup();
  });

  // Закрытие попапа для ресайза картинки
  function closeResizePopup() {
    jcrop_api.disable();
    $('.img-resize-popup, .overlay-white').hide().remove();
  }
//]]>
</script>