<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.ar
 */

/**
 * Class FActiveFileRecord
 *
 * @var string $uploadPath - путь для загрузки файлов, по умолчанию /f/названте_модели
 * @var string $fileModel - модель хранения файлов, по умолчанию унаследованая от FActiveFileRecord
 * @var string $formAttribute - поле модели храняшие имя файла
 */
class FActiveFileRecord extends FActiveRecord
{
  public $maxFiles = 5;

  public $fileTypes = array('zip', 'rar', 'doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif');

  public $uploadPath;

  public $formAttribute = 'file';

  /**
   * @var $fileModel - модель хранения файлов
   */
  public $fileModel;

  public function init()
  {
    if( empty($this->fileModel) )
      $this->fileModel = get_class($this);

    $scenario = Yii::app()->request->isPostRequest ? 'upload' : $this->getScenario();
    $this->setScenario($scenario);
    $this->initPath();
  }

  public function rules()
  {
    return array(
      array($this->formAttribute, 'file',
        'allowEmpty' => true,
        'types'      => $this->fileTypes,
        'maxFiles'   => $this->maxFiles,
        'maxSize'    => 10 * (1024 * 1024), //10MB
      ),
    );
  }

  public function getFile()
  {

  }

  public function setFile()
  {

  }

  protected function initPath()
  {
    $this->uploadPath = Yii::getPathOfAlias('webroot').'/f/'.($this->uploadPath ? $this->uploadPath : strtolower(get_called_class()));
    $this->uploadPath = preg_replace('/\/$/', '', $this->uploadPath).'/';

    if( !file_exists($this->uploadPath) )
    {
      mkdir($this->uploadPath);
      chmod($this->uploadPath, 0777);
    }
  }
}