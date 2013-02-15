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
   * @var string name of the form view to be rendered
   */
  public $formView = 'form';

  /**
   * @var string name of the upload view to be rendered
   */
  public $uploadView = 'upload';

  /**
   * @var string name of the download view to be rendered
   */
  public $downloadView = 'download';

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
      $this->model->attachBehavior('uploadBehavior', array('class'     => 'UploadBehavior',
                                                           'attribute' => $this->attribute));
    }
  }

  /**
   * Generates the required HTML and Javascript
   */
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

    $this->render($this->formGrid, array(
      'model'       => $this->model,
      'gridId'      => $this->htmlOptions['gridId'],
      'htmlOptions' => $this->htmlOptions['gridOptions']
    ));

    if( !$this->model->isNewRecord )
      $this->render($this->formView, compact('htmlOptions'));
  }

  public function publishInitScript($options)
  {
    Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->htmlOptions['id'], <<<EOD

jQuery(function($)
{
  var formId   = '{$this->htmlOptions['id']}';
  var gridId   = '{$this->htmlOptions['gridId']}'
  var multiply = '{$this->multiple}';
  var options  = {$options};

  var td       = $('#' + gridId).parents('td');
  var files    = td.find('.fileupload-files');
  var buttons  = td.find('.fileupload-buttonbar');

  if( !multiply && td.find('.items a').length )
    buttons.hide();

  $('#' + formId).fileupload(options)
    .bind('fileuploadprogress', function(e, data)
    {
      $.fn.yiiGridView.update(gridId);
      if( !multiply )
        files.hide().find('tbody').empty();
    })
    .bind('fileuploaddestroy', function(e, data)
    {
      $.fn.yiiGridView.update(gridId);
      if( !multiply )
        buttons.show();
    })
    .bind('fileuploadadded', function(e, data){
      if( !multiply ){
        buttons.hide();
        files.find('button.delete').click(function(){
          if( !files.find('.items a').length ) buttons.show();
        });
      }
    });
});
EOD
    ,CClientScript::POS_END);
  }

  /**
   * Publises and registers the required CSS and Javascript
   * @throws CHttpException if the assets folder was not found
   */
  public function publishAssets()
  {
    $assets  = dirname(__FILE__).'/assets';
    $baseUrl = Yii::app()->assetManager->publish($assets);

    if( is_dir($assets) )
    {
      Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.fileupload-ui.css');

      // The Templates plugin is included to render the upload/download listings
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/tmpl.min.js', CClientScript::POS_END);

      // The basic File Upload plugin
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload.js', CClientScript::POS_END);

      if( $this->previewImages || $this->imageProcessing )
      {
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/load-image.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/canvas-to-blob.min.js', CClientScript::POS_END);
      }

      // The Iframe Transport is required for browsers without support for XHR file uploads
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.iframe-transport.js', CClientScript::POS_END);

      // The File Upload image processing plugin
      if( $this->imageProcessing )
      {
        Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-ip.js', CClientScript::POS_END);
      }
      // The File Upload user interface plugin
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.fileupload-ui.js', CClientScript::POS_END);

      // The localization script
      Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/locale.js', CClientScript::POS_END);

      /**
      <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
      <!--[if gte IE 8]><script src="<?php echo Yii::app()->baseUrl; ?>/js/cors/jquery.xdr-transport.js"></script><![endif]-->
      */
    }
    else
    {
      throw new CHttpException(500, __CLASS__.' - Error: Couldn\'t find assets to publish.');
    }
  }
}