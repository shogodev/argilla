<?php
/* @var $this UploadWidget */
/* @var $model CActiveRecord */
/* @var $gridId string */
/* @var $htmlOptions array */
?>

<?php
if( $this->multiple )
{
  $this->widget('BGridView', array(
    'id'              => $gridId,
    'htmlOptions'     => $htmlOptions,
    'dataProvider'    => $this->model->getUploadedFiles(),
    'buttonsTemplate' => false,
    'columns'         => $this->gridClass->getColumns(),
    'enableHistory'   => false,
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
    'columns'         => $this->gridClass->getColumns(),
    'enableHistory'   => false,
  ));
}
?>

<script type="text/javascript">
//<![CDATA[
  $('.image-column > img').click(function(e) {
    e.preventDefault();

    // Блок с картинкой для ресайза
    var popupBlock = $('<div/>').addClass('img-resize-popup').html(
      '<span class="img-resize-inner"><img src="' + $(this).attr('src') + '" alt="" id="raw-image" /></span>'
    );

    // Вызов оверлея и попапа для ресайза картинки
    $('body').append( $('<div/>').addClass('overlay-white') ).append( popupBlock );

    $('#raw-image').Jcrop({
      onChange: showSize,
      onSelect: showSize,
      onRelease: clearSize
    }, function(){
      jcrop_api = this;
    });
  });

  // Фиксирование соотношения сторон при выборе чекбокса "Квадратные превью"
  $('body').on('change', '#img-resize-squared-lock', function(){
    jcrop_api.setOptions(
      this.checked ? { aspectRatio: 1/1 } : { aspectRatio: 0 }
    );
    jcrop_api.focus();
  });

  // Изменение выбранной области при изменении значения в поле ширина
  $('body').on('input', '#preview-width', function(){
    var pos_x = jcrop_api.tellSelect().x,
        pos_y = jcrop_api.tellSelect().y,
        pos_x2 = jcrop_api.tellSelect().x2,
        pos_y2 = jcrop_api.tellSelect().y2;
    if ( $.isNumeric( $(this).val() )) {
      jcrop_api.setSelect([ pos_x, pos_y, parseInt(pos_x) + parseInt($(this).val()), pos_y2 ]);
    }
  });

  // Изменение выбранной области при изменении значения в поле высота
  $('body').on('input', '#preview-height', function(){
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
  $('body').on('click', '#preview-submit', function(){
    var pos_x = jcrop_api.tellSelect().x,
        pos_y = jcrop_api.tellSelect().y,
        pos_x2 = jcrop_api.tellSelect().x2,
        pos_y2 = jcrop_api.tellSelect().y2;
    console.log( pos_x, pos_y, pos_x2, pos_y2 );
    closeResizePopup();
  });

  // Клик по кнопке закрыть
  $('body').on('click','.img-resize-popup', function(e){
    e.preventDefault();
    closeResizePopup();
  });

  $('body').on('click', '.img-resize-popup .img-resize-inner', function(e){
    e.stopPropagation();
    e.preventDefault();
  });

  // Закрытие попапа для ресайза картинки
  function closeResizePopup() {
    jcrop_api.disable();
    $('.img-resize-popup, .overlay-white').hide().remove();
  }
//]]>
</script>