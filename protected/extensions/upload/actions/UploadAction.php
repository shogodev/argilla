<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload
 */
class UploadAction extends CAction
{
  /**
   * The query string variable name where the subfolder name will be taken from.
   * If false, no subfolder will be used.
   * Defaults to null meaning the subfolder to be used will be the result of date("mdY").
   * @see UploadAction::init().
   * @var string
   * @since 0.2
   */
  public $subfolderVar = false;

  /**
   * Path of the main uploading folder.
   * @see UploadAction::init()
   * @var string
   * @since 0.1
   */
  public $path;

  /**
   * Public path of the main uploading folder.
   * @see UploadAction::init()
   * @var string
   * @since 0.1
   */
  public $publicPath;

  /**
   * Model there added photos has been saved
   * @var SActiveRecord
   */
  public $model;

  public $previewWidth = 20;

  public $previewHeight = 20;

  public $dirMode = 0777;

  public $fileMode = 0775;

  /**
   * The resolved subfolder to upload the file to
   * @var string
   * @since 0.2
   */
  private $_subfolder = "";

  /**
   * The main action that handles the file upload request
   *
   * @param string $method
   * @param int $fileId
   * @param string $attr
   */
  public function run($method = 'upload', $fileId = null, $attr = null)
  {
    $this->init($attr);

    switch($method)
    {
      case 'delete':
        $this->deleteFile($fileId);
      break;

      case 'crop':
        $this->cropFile($fileId);
      break;

      default:
        $this->uploadFile();
    }
  }

  /**
   * Initialize the properties of action
   *
   * @param null $attr
   *
   * @throws CHttpException
   */
  private function init($attr = null)
  {
    $this->model = Yii::app()->controller->loadModel(Yii::app()->request->getQuery('id'), Yii::app()->request->getQuery('model'));

    if( !($this->model instanceof CActiveRecord) || !in_array('uploadBehavior', array_keys($this->model->behaviors())) )
      throw new CHttpException(500, 'Model should be an instance of AR class with UploadBehavior behavior');

    $this->model->asa('uploadBehavior')->attribute = $attr ? $attr : Yii::app()->request->getQuery('attr');

    if( !isset($this->path) )
    {
      $this->path = Yii::app()->controller->module->getUploadPath();
    }

    if( !isset($this->publicPath) )
    {
      $this->publicPath = Yii::app()->controller->module->getUploadUrl();
    }

    if( $this->subfolderVar )
    {
      $this->_subfolder = Yii::app()->request->getQuery($this->subfolderVar, date("mdY"));
    }
    else if( $this->subfolderVar !== false )
    {
      $this->_subfolder = date("mdY");
    }

    $this->path       = ($this->_subfolder != "") ? "{$this->path}/{$this->_subfolder}" : "{$this->path}";
    $this->publicPath = ($this->_subfolder != "") ? "{$this->publicPath}/{$this->_subfolder}" : "{$this->publicPath}";

    if( !is_dir($this->path) )
    {
      mkdir($this->path, $this->dirMode);
      chmod($this->path, $this->dirMode);
    }
    else if( !is_writable($this->path) )
    {
      chmod($this->path, $this->dirMode);
    }
  }

  private function setHeader()
  {
    if( !headers_sent() )
    {
      header('Vary: Accept');

      if( isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) )
        header('Content-type: application/json');
      else
        header('Content-type: text/plain');
    }
  }

  private function deleteFile($id)
  {
    if( !$id )
      throw new CHttpException(500, 'File id can`t be null.');

    $name = $this->model->getFileName($id);

    if( $this->model->deleteUploadedFile($id) )
    {
      if( $name && file_exists($this->path.$name) )
      {
        unlink($this->path.$name);
      }
      $this->deleteThumbs($name);
      echo json_encode(array('files' => array($name => true)));
    }
  }

  /**
   * @param CUploadedFile $uploadedFile
   *
   * @throws CHttpException
   */
  private function uploadFile(CUploadedFile $uploadedFile = null)
  {
    $model = new UploadForm('create');
    $model->file = $uploadedFile? $uploadedFile : CUploadedFile::getInstance($this->model, $this->model->asa('uploadBehavior')->attribute);

    if( $model->file !== null )
    {
      $model->mime_type = $model->file->getType();
      $model->size = $model->file->getSize();
      $model->name = $model->file->getName();

      $file = array(
        'name' => $model->name,
        'size' => $model->size,
      );

      if( $model->validate() )
      {
        $this->prepareName($model);
        $this->saveFile($model);

        $file = CMap::mergeArray($file, array(
          'name' => $model->name,
          'url' => $this->publicPath.$model->name,
          'thumbnailUrl' => $this->createThumbs($model),
          'deleteType' => "DELETE"
        ));

        $fileId = $this->addFileToModel($file);
        $file['deleteUrl'] = Yii::app()->controller->createUrl('upload', array(
          'id' => $this->model->id,
          'model' => get_class($this->model),
          'fileId' => $fileId,
          'attr' => $this->model->asa('uploadBehavior')->attribute,
          'method' => 'delete'
          )
        );

        $this->setHeader();
      }
      else
      {
        $file = CMap::mergeArray($file, array('error' => $model->getErrors('file')));
      }
      echo json_encode(array('files' => array($file)));
    }
    else
    {
      throw new CHttpException(500, "Could not upload file");
    }
  }

  /**
   * @param $id
   *
   * @throws CHttpException
   */
  private function cropFile($id)
  {
    if( !$id )
      throw new CHttpException(500, 'File id can`t be null.');

    if( !($cropSettings = Arr::get(Yii::app()->controller->module->getCropSettings(), Yii::app()->getMappedControllerId())) )
      throw new CHttpException(500, 'Укажите настройки модуля в методе getCropSettings');

    $originalName = $this->model->getFileName($id);
    $x = Yii::app()->request->getParam('x');
    $y = Yii::app()->request->getParam('y');

    foreach($cropSettings as $prefix => $settings)
    {
      Yii::app()->phpThumb->options['jpegQuality'] = 100;
      $cropImage = Yii::app()->phpThumb->create($this->path.$originalName);

      $thumbsSettings = $this->getThumbsSettings();
      $thumbSizes = end($thumbsSettings);
      $cropImage->resize($thumbSizes[0], $thumbSizes[1]);

      list($width, $height) = $settings;
      $cropImage->crop($x, $y, $width, $height);
      $cropImagePath = $this->path.$prefix.'_'.$originalName;
      if( file_exists($cropImagePath) )
        unlink($cropImagePath);
      $cropImage->save($cropImagePath);
      chmod($cropImagePath, $this->fileMode);
    }
  }

  /**
   * Если файл существует с таким названием уже существует, то создаёт новое имя файла.
   *
   * @param $model
   */
  private function prepareName($model)
  {
    $model->name = Utils::translite($model->name, false);

    while( file_exists($this->path . $model->name) )
      $model->name = Utils::doCustomFilename($model->name);
  }

  private function saveFile($model)
  {
    if( file_exists($this->path.$model->name) )
      throw new CHttpException(500, "Could not save Image. File exists.");

    $model->file->saveAs($this->path.$model->name);
    chmod($this->path.$model->name, $this->fileMode);
  }

  /**
   * @param array $file
   *
   * @return int
   * @throws CHttpException
   */
  private function addFileToModel(array &$file)
  {
    $result = $this->model->saveUploadedFile($file);

    if( !$result )
    {
      throw new CHttpException(500, "Could not save Image.\n".CHtml::errorSummary($this->model));
    }
    else
    {
      return $result;
    }
  }

  private function getThumbsSettings()
  {
    $settings = Yii::app()->controller->module->getThumbsSettings();

    if( method_exists($this->model, 'getThumbsSettings') )
      $settings = array_merge($settings, $this->model->getThumbsSettings());

    $settings = Arr::get($settings, BApplication::getMappedControllerId(), array());

    return $settings;
  }

  private function createThumbs(UploadForm $model)
  {
    if( $this->model->isResizeable($model->file->getExtensionName()) )
    {
      foreach($this->getThumbsSettings() as $pref => $options)
      {
        if( $jpegQuality = Arr::get($options, 'jpegQuality') )
        {
          Yii::app()->phpThumb->options['jpegQuality'] = $jpegQuality;
          Yii::app()->phpThumb->init();
        }
        $thumb = Yii::app()->phpThumb->create($this->path.$model->name);

        if( Arr::get($options, 'crop') == true )
          $thumb->cropFromCenter(min($thumb->getDimensions()));

        $width = Arr::cut($options, 0);
        $height = Arr::cut($options, 1);
        if( isset($width, $height) )
          $thumb->resize($width, $height);
        else
          throw new CHttpException(500, 'Параметры ресайза заданы не верно');

        if( $pref === 'origin' )
          $newPath = $this->path.$model->name;
        else
          $newPath = $this->path.$pref.'_'.$model->name;

        $this->addWatermark($thumb, $pref);
        $thumb->save($newPath);
        chmod($newPath, $this->fileMode);
      }

      $previewPath = $this->path.$model->name;
    }
    else
    {
      $previewPath = dirname(__FILE__).'/../assets/img/'.$this->model->getThumb($model->name);
    }

    $thumb = Yii::app()->phpThumb->create($previewPath);
    $thumb->resize($this->previewWidth, $this->previewHeight);
    $preview = 'data:image/png;base64,'.base64_encode($thumb->getImageAsString());

    return $preview;
  }

  private function addWatermark($thumb, $preffix = null)
  {
    $settings = Yii::app()->controller->module->getWatermarkSettings();
    $settings = Arr::get($settings, BApplication::getMappedControllerId(), array());

    if( $preffix && isset($settings[$preffix]) )
    {
      $settings = $settings[$preffix];
      $position = Arr::get($settings, 'position', 'center');
      $opacity = Arr::get($settings, 'opacity', 100);
      $offsetX = Arr::get($settings, 'offsetX', 0);
      $offsetY = Arr::get($settings, 'offsetY', 0);

      if( !file_exists($this->path.$settings['image']) )
        return;

      $watermark = Yii::app()->phpThumb->create($this->path.$settings['image']);
      $thumb->addWatermark($watermark, $position, $opacity, $offsetX, $offsetY);
    }
  }

  private function deleteThumbs($name)
  {
    $settings = $this->getThumbsSettings();

    foreach($settings as $pref => $sizes)
    {
      $newPath = $this->path.$pref.'_'.$name;

      if( $newPath && file_exists($newPath) )
        unlink($newPath);
    }
  }
}
