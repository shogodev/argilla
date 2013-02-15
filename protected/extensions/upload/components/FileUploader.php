<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 27.08.12
 */
abstract class FileUploader
{
  /**
   * @var UploadBehavior
   */
  protected $behavior;

  /**
   * @param UploadBehavior $behavior
   */
  public function __construct(UploadBehavior $behavior)
  {
    $this->behavior = $behavior;
  }

  /**
   * @param $fileName
   *
   * @return string
   */
  public function getThumbPath($fileName)
  {
    $settings = Yii::app()->controller->module->getThumbsSettings();
    $thumbs   = isset($settings[Yii::app()->controller->id]) ? $settings[Yii::app()->controller->id] : array();
    $lastKey  = key(array_reverse($thumbs));
    $path     = Yii::app()->controller->module->getUploadUrl().($lastKey ? $lastKey.'_' : '').$fileName;

    return $path;
  }

  /**
   * @param array $file
   *
   * @return bool
   */
  abstract public function saveFile(array $file);

  /**
   * @return CArrayDataProvider $files
   */
  abstract public function getFiles();

  /**
   * @param $id
   *
   * @return string
   */
  abstract public function getFileName($id);

  /**
   * @param $id
   *
   * @return int
   */
  abstract public function deleteFile($id);
}

?>