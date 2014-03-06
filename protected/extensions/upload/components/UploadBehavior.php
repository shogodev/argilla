<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload
 *
 * Поведение для добавления файлов в ActiveRecord модель
 */
class UploadBehavior extends CActiveRecordBehavior
{
  /**
   * @var атрибут модели, с которым мы производим действия по загрузке файлов
   *
   * Может принимать значение поля модели или таблицы, в которой будет храниться
   * запись о загруженном файле. Таблица указывается без префикса бд.
   */
  public $attribute;

  /**
   * @var таблица, в которой содержится артибут, (таблица модели или внешняя таблица)
   */
  public $table;

  /**
   * @var string строка всех артибутов, в которых будет осуществляться загрузка файлов
   * Несколько значений разделяются запятыми
   */
  public $validAttributes;

  /**
   * @var FileUploader
   */
  protected $uploader;

  /**
   * Сохраняем загруженный файл
   *
   * @param array $file
   *
   * @return bool
   * @throws CHttpException
   */
  public function saveUploadedFile(array $file)
  {
    $this->init();

    if( isset($file['size']) && preg_match("/\d+/", $file['size']) )
    {
      $formatter = new CFormatter();
      $file['size'] = $formatter->formatSize($file['size']);
    }

    return $this->uploader->saveFile($file);
  }

  public function getUploadedFiles()
  {
    $this->init();
    return $this->uploader->getFiles();
  }

  /**
   * Получаем имя файла
   *
   * @param $id
   *
   * @return string
   */
  public function getFileName($id)
  {
    $this->init();
    return $this->uploader->getFileName($id);
  }

  /**
   * Удаляем загруженный файл из модели
   *
   * @param $id
   *
   * @return int
   */
  public function deleteUploadedFile($id)
  {
    $this->init();
    return $this->uploader->deleteFile($id);
  }

  public function beforeDelete($event)
  {
    $attributes = explode(",", $this->validAttributes);
    $action     = Yii::app()->controller->createAction('upload');

    foreach($attributes as $attr)
    {
      $this->attribute = trim($attr);

      $files = $this->getUploadedFiles();
      foreach($files->rawData as $file)
      {
        $action->runWithParams(array('method' => 'delete', 'fileId' => $file['id'], 'attr' => trim($attr)));
      }
    }

    parent::beforeDelete($event);
  }

  public function isResizeable($extension)
  {
    $this->init();
    return in_array($extension, $this->uploader->getResizeableTypes());
  }

  public function getThumb($fileName)
  {
    $this->init();
    return pathinfo($this->uploader->getThumbPath($fileName), PATHINFO_BASENAME);
  }

  protected function init()
  {
    if( array_key_exists($this->attribute, $this->owner->attributes) )
    {
      $this->table    = $this->owner->tableName();
      $this->uploader = $this->UploaderFactory(true);
    }
    else
    {
      $this->table    = Yii::app()->db->tablePrefix.$this->attribute;
      $this->uploader = $this->UploaderFactory(false);
    }
  }

  /**
   * Создаем объект для загрузки файлов в модель
   *
   * @param bool $useSelfModel
   *
   * @return FileUploader
   */
  protected function UploaderFactory($useSelfModel = true)
  {
    if( $useSelfModel )
    {
      if( in_array('nestedSetBehavior', array_keys($this->owner->behaviors())) )
        return new TreeModelUploader($this);
      else
        return new ModelUploader($this);
    }
    else
    {
      return new TableUploader($this);
    }
  }
}