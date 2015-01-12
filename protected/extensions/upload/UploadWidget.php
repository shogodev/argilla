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

  public $gridClass;

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

    $this->options['url']              = $this->url;
    $this->options['autoUpload']       = $this->autoUpload;
    $this->options['previewMaxWidth']  = $this->previewMaxWidth;
    $this->options['previewMaxHeight'] = $this->previewMaxWidth;

    $this->publishInitScript(CJavaScript::encode($this->options));

    $htmlOptions = array();

    if( $this->multiple )
      $htmlOptions['multiple'] = true;

    $this->render($this->uploadView);
    $this->render($this->downloadView);

    if( !isset($this->gridOptions['class']) )
      $this->gridOptions['class'] = $this->multiple ? 'ImageGrid' : 'SingleImageGrid';

    $this->gridClass = Yii::createComponent($this->gridOptions['class'], $this);

    $this->render($this->formGrid, array(
      'model'       => $this->model,
      'gridId'      => $this->htmlOptions['gridId'],
      'htmlOptions' => $this->htmlOptions['gridOptions']
    ));

    if( !$this->model->isNewRecord )
      $this->render($this->formView, compact('htmlOptions'));
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
            files.hide().find('tbody').empty();
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
    $assets  = dirname(__FILE__).'/assets';
    $baseUrl = Yii::app()->assetManager->publish($assets);

    if( is_dir($assets) )
    {
      Yii::app()->clientScript->registerScriptFile($baseUrl.'js/vendor/jquery.ui.widget.js', CClientScript::POS_END);
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
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/locale.js', CClientScript::POS_END);

      Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.fileupload.css');
      Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.fileupload-ui.css');
    }
    else
    {
      throw new CHttpException(500, __CLASS__.' - Error: Couldn\'t find assets to publish.');
    }
  }
}