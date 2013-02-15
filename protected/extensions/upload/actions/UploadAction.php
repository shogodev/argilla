<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
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

      echo json_encode(true);
    }
  }

  /**
   * @param CUploadedFile $uploadedFile
   *
   * @throws CHttpException
   */
  private function uploadFile(CUploadedFile $uploadedFile = null)
  {
    $model       = new UploadForm();
    $model->file = $uploadedFile? $uploadedFile : CUploadedFile::getInstance($this->model, $this->model->asa('uploadBehavior')->attribute);

    if( $model->file !== null )
    {
      $model->mime_type = $model->file->getType();
      $model->size      = $model->file->getSize();
      $model->name      = $model->file->getName();

      if( $model->validate() )
      {
        $this->prepareName($model);
        $this->saveFile($model);

        $file = array("name"          => $model->name,
                      "mime_type"     => $model->mime_type,
                      "size"          => $model->size,
                      "url"           => $this->publicPath.$model->name,
                      "thumbnail_url" => $this->createThumbs($model->name),
                      );

        $fileId             = $this->addFileToModel($file);
        $file['delete_url'] = Yii::app()->controller->createUrl('upload', array('id'     => $this->model->id,
                                                                                'model'  => get_class($this->model),
                                                                                'fileId' => $fileId,
                                                                                'attr'   => $this->model->asa('uploadBehavior')->attribute,
                                                                                'method' => 'delete'));

        $this->setHeader();
        echo json_encode(array($file));
      }
      else
      {
        echo json_encode(array(array("error" => $model->getErrors('file'))));
      }
    }
    else
    {
      throw new CHttpException(500, "Could not upload file");
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

    $settings = Arr::get($settings, Yii::app()->controller->id, array());

    return $settings;
  }

  private function createThumbs($name)
  {
    $settings = $this->getThumbsSettings();

    foreach($settings as $pref => $sizes)
    {
      $thumb = Yii::app()->phpThumb->create($this->path.$name);
      $thumb->resize($sizes[0], $sizes[1]);

      if( $pref === 'origin' )
        $newPath = $this->path.$name;
      else
        $newPath = $this->path.$pref.'_'.$name;

      $thumb->save($newPath);
      chmod($newPath, $this->fileMode);
    }

    $thumb = Yii::app()->phpThumb->create($this->path.$name);
    $thumb->resize($this->previewWidth, $this->previewHeight);
    $preview = 'data:image/png;base64,'.base64_encode($thumb->getImageAsString());

    return $preview;
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
?>