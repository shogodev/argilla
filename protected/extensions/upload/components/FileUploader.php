<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload
 */
abstract class FileUploader
{
  /**
   * @var UploadBehavior
   */
  protected $behavior;

  protected $thumbs = array(
    'doc' => 'icn_doc.gif',
    'pdf' => 'icn_pdf.gif',
    'rar' => 'icn_rar.gif',
    'xls' => 'icn_xls.gif',
    'zip' => 'icn_zip.gif',
  );

  /**
   * @param UploadBehavior $behavior
   */
  public function __construct(UploadBehavior $behavior)
  {
    $this->behavior = $behavior;
  }

  public function getResizeableTypes()
  {
    return array('jpg', 'png', 'gif', 'jpeg');
  }

  /**
   * @param $fileName
   *
   * @return string
   */
  public function getThumbPath($fileName)
  {
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    if( !array_key_exists($extension, $this->thumbs) )
    {
      $settings  = Yii::app()->controller->module->getThumbsSettings();
      $thumbs    = isset($settings[Yii::app()->controller->id]) ? $settings[Yii::app()->controller->id] : array();
      $lastKey   = key(array_reverse($thumbs));
      $path      = Yii::app()->controller->module->getUploadUrl().($lastKey ? $lastKey.'_' : '').$fileName;
    }
    else
    {
      $path = Yii::app()->assetManager->getPublishedPath(Yii::getPathOfAlias('ext').'/upload/assets').'/img/'.$this->thumbs[$extension];
      $path = preg_replace('/^.*(backend.*)/', Yii::app()->getFrontendUrl()."$1", $path);
    }

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