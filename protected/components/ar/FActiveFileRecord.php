<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.ar
 */
class FActiveFileRecord extends FActiveRecord
{
  public $maxFiles = 5;

  public $fileTypes = array('zip', 'rar', 'doc', 'docx', 'pdf', 'jpg');

  public $uploadPath;

  public $formAttribute = 'file';

  public function init()
  {
    if( empty($this->fileModel) )
      throw new CHttpException(500, 'Set fileModel property');

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

  public function getFile(){}

  public function setFile(){}

  protected function initPath()
  {
    if( !$this->uploadPath )
      $this->uploadPath = Yii::getPathOfAlias('webroot').'/f/'.strtolower(get_called_class()).'/';

    if( !file_exists($this->uploadPath) )
    {
      mkdir($this->uploadPath);
      chmod($this->uploadPath, 0777);
    }
  }
}