<?php
/**
 * Upload extension for Yii.
 * jQuery file upload extension for Yii, allows your users to easily upload files to your server using jquery
 * Its a wrapper of  http://blueimp.github.com/jQuery-File-Upload/
 * @author AsgarothBelem <asgaroth.belem@gmail.com>
 * @link http://blueimp.github.com/jQuery-File-Upload/
 * @link https://github.com/Asgaroth/xupload
 * @version 0.2
 */

Yii::import('zii.widgets.jui.CJuiInputWidget');
Yii::import('ext.upload.grid.*');

class UploadWidget extends CJuiInputWidget
{
  /**
   * @var CModel the data model associated with this widget.
   */
  public $model;

  /**
   * @var string the attribute associated with this widget.
   */
  public $attribute;

  /**
   * the url to the upload handler
   * @var string
   */
  public $url;

  /**
   * set to true to use multiple file upload
   * @var boolean
   */
  public $multiple = true;

  /**
   * The upload template id to display files available for upload
   * defaults to null, meaning using the built-in template
   */
  public $uploadTemplate;

  /**
   * The template id to display files available for download
   * defaults to null, meaning using the built-in template
   */
  public $downloadTemplate;

  /**
   * Wheter or not to preview image files before upload
   */
  public $previewImages = true;

  /**
   * Wheter or not to add the image processing plugin
   */
  public $imageProcessing = true;

  /**
   * Wheter or not to start uploading immediately
   */
  public $autoUpload = false;

  /**
   * @var string name of the form grid to be rendered
   */
  public $formGrid = 'grid';

  /**
   * @var array
   */
  public $gridOptions = array();

  /**
   * @var string name of the form view to be rendered
   */
  public $formView = 'form';

  /**
   * @var string name of the upload view to be rendered
   */
  public $uploadView = '_upload';

  /**
   * @var string name of the download view to be rendered
   */
  public $downloadView = '_download';

  public $previewMaxWidth = 20;

  public $previewMaxHeight = 20;

  /**
   * Publishes the required assets
   */
  public function init()
  {
    parent::init();
    $this->publishAssets();

    $this->attachBehaviorToModel();

    list($name, $id) = $this->resolveNameID();

    if( !isset($this->url) )
      $this->url = Yii::app()->controller->createUrl('upload', array('id' => $this->model->id, 'attr' => $this->attribute, 'model' => get_class($this->model)));

    if( !isset($this->uploadTemplate) )
      $this->uploadTemplate = "#template-upload";

    if( !isset($this->downloadTemplate) )
      $this->downloadTemplate = "#template-download";

    if( !isset($this->htmlOptions['id']) )
      $this->htmlOptions['id'] = $id.'-form';

    $this->htmlOptions['gridId'] = $id.'-files';

    if( !isset($this->htmlOptions['gridOptions']) )
      $this->htmlOptions['gridOptions'] = array();

    $this->options['url'] = $this->url;
    $this->options['autoUpload'] = $this->autoUpload;
    $this->options['previewMaxWidth'] = $this->previewMaxWidth;
    $this->options['previewMaxHeight'] = $this->previewMaxWidth;

    if( !isset($this->gridOptions['class']) )
      $this->gridOptions['class'] = $this->multiple ? 'MultiImageGrid' : 'SingleImageGrid';

    $classes = Arr::get($this->htmlOptions['gridOptions'], 'class', '');
    $this->htmlOptions['gridOptions']['class'] = $classes.(empty($classes) ? '' : ' ').'images-uploader';
  }

  public function attachBehaviorToModel()
  {
    if( $behavior = $this->model->asa('uploadBehavior') )
    {
      $behavior->attribute = $this->attribute;
    }
    else
    {
      $this->model->attachBehavior('uploadBehavior', array(
          'class' => 'UploadBehavior',
          'attribute' => $this->attribute)
      );
    }
  }

  public function run()
  {
    $this->publishInitScript(CJavaScript::encode($this->options));
    $this->registerDropZoneScript();

    $this->render($this->uploadView);
    $this->render($this->downloadView);

    if( !$this->multiple )
      $this->htmlOptions['gridOptions']['style'] = 'width: 15%';

    $this->renderGrid($this->gridOptions['class']);

    $htmlOptions = array();

    if( $this->multiple )
      $htmlOptions['multiple'] = true;

    if( !$this->model->isNewRecord )
      $this->render($this->formView, compact('htmlOptions'));

    $this->registerCropImageScript();
  }

  private function renderGrid($widgetGrid)
  {
    $this->widget($widgetGrid, array(
      'id' => $this->htmlOptions['gridId'],
      'model' => $this->model,
      'attribute' => $this->attribute,
      'htmlOptions' => $this->htmlOptions['gridOptions'],
      'buttonsTemplate' => false,
      'enableHistory' => false,
      'summaryTagName' => 'span'
    ));
  }

  private function publishInitScript($options)
  {
    Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->htmlOptions['id'], "
      jQuery(function($)
      {
       'use strict';

        var formId = '{$this->htmlOptions['id']}';
        var gridId = '{$this->htmlOptions['gridId']}'
        var multiply = '{$this->multiple}';
        var options = {$options};

        var td = $('#' + gridId).parents('td');
        var files = td.find('.fileupload-files');
        var buttons = td.find('.fileupload-buttonbar');

        if( !multiply && td.find('.items a').length )
          buttons.hide();

        var fileUploader = $('#' + formId).fileupload(options);
        fileUploader.bind('fileuploadstop', function(e, data)
        {
          $.fn.yiiGridView.update(gridId);
          if( !multiply )
            files.find('tbody').empty();
        });
        fileUploader.bind('fileuploaddestroy', function(e, data)
        {
          if( !multiply )
            buttons.show();
        });
        fileUploader.bind('fileuploadadded', function(e, data){
          if( !multiply ){
            buttons.hide();
            files.find('button.delete').click(function(){
              if( !files.find('.items a').length ) buttons.show();
            });
          }
        });
     });", CClientScript::POS_END);
  }

  private function publishAssets()
  {
    $assets = dirname(__FILE__).'/assets';
    $baseUrl = Yii::app()->assetManager->publish($assets);

    if( is_dir($assets) )
    {
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/vendor/jquery.ui.widget.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/vendor/tmpl.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.iframe-transport.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload.js', CClientScript::POS_END);

      if( $this->previewImages || $this->imageProcessing )
      {
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/vendor/load-image.all.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/vendor/canvas-to-blob.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-process.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-image.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-audio.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-video.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-validate.js', CClientScript::POS_END);
      }

      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-ui.js', CClientScript::POS_END);
      //Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/locale.js', CClientScript::POS_END);

      Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.fileupload.css');
      Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.fileupload-ui.css');
    }
    else
    {
      throw new CHttpException(500, __CLASS__.' - Error: Couldn\'t find assets to publish.');
    }
  }

  private function registerDropZoneScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.'DropZoneScript#'.$this->htmlOptions['gridId'], "
      $(document).bind('dragover', function (e) {
        var dropzoneContainer = $('#{$this->htmlOptions['gridId']}');
        var dropzone = $('<div id=\"dropzone\" />').html('<p>Перетащите файлы сюда</p>').appendTo(dropzoneContainer);

        setTimeout(function() {
          var dropZone = $('#dropzone'),
              timeout = window.dropZoneTimeout;
          if (!timeout) {
              dropZone.addClass('in');
          } else {
              clearTimeout(timeout);
          }
          var found = false,
              node = e.target;
          do {
              if (node === dropZone[0]) {
                  found = true;
                  break;
              }
              node = node.parentNode;
          } while (node != null);
          if (found) {
              dropZone.addClass('hover');
          } else {
              dropZone.removeClass('hover');
          }
          window.dropZoneTimeout = setTimeout(function () {
              window.dropZoneTimeout = null;
              dropZone.removeClass('in hover');
          }, 100);
        }, 0);
      });
      ", Yii::app()->clientScript->coreScriptPosition);
  }

  private function registerCropImageScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.'CropImageScript#'.$this->htmlOptions['gridId'], "
      $('.image-column > img').click(function(e) {
        e.preventDefault();

        // Блок с картинкой для ресайза
        var popupBlock = $('<div/>').addClass('img-resize-popup').html(
          '<span class=\"img-resize-inner\"><img src=\"' + $(this).attr('src') + '\" alt=\"\" id=\"raw-image\" /></span>'
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

      // Фиксирование соотношения сторон при выборе чекбокса Квадратные превью
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
      }", CClientScript::POS_LOAD);
  }
}