<?php
/**
 * Поведение для добавления файлов в ActiveRecord модель
 *
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 23.08.12
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

  protected function init()
  {
    $prefix = Yii::app()->db->tablePrefix;
    $tables = Yii::app()->db->schema->tables;

    if( array_key_exists($prefix.$this->attribute, $tables) )
    {
      $this->table    = $prefix.$this->attribute;
      $this->uploader = $this->UploaderFactory(false);
    }
    else
    {
      $this->table    = $this->owner->tableName();
      $this->uploader = $this->UploaderFactory(true);
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
    else return new TableUploader($this);

  }
}

?>