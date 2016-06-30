<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class BFileUploaderController extends BController
{
  const ALLOWED_FILE_NAME = '/^[^\.].*$/';

  const UPLOAD_PATH = '/f/upload/';

  public $name = 'Загрузка файлов';

  public function beforeAction($action)
  {
    Yii::app()->log->getRoutes()[1]->enabled = false;

    return parent::beforeAction($action);
  }

  public function actions()
  {
    return array(
      'connector' => array(
        'class' => 'ext.elFinder.ElFinderConnectorAction',
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        'settings' => array(
          'roots' => array(
            array(
              'driver' => 'LocalFileSystem',
              'path' => GlobalConfig::instance()->rootPath.self::UPLOAD_PATH.'/',
              'URL' => self::UPLOAD_PATH,
              'alias' => self::UPLOAD_PATH,
              'acceptedName' => self::ALLOWED_FILE_NAME,
              'attributes' => array(
                array(
                  'pattern' => '/\/[.].*$/',
                  'read' => false,
                  'write' => false,
                  'hidden' => true,
                ),
              ),
            )
          ),
        )
      ),

      'elfinderCKEditor' => array(
        'class' => 'ext.elFinder.ElFinderPopupAction',
        'connectorRoute' => 'connector',
      ),
    );
  }

  public function actionQuickUpload()
  {
    $uploadPath = GlobalConfig::instance()->rootPath.self::UPLOAD_PATH.'/';
    $file = CUploadedFile::getInstanceByName('upload');

    $fileName = UploadHelper::prepareFileName($uploadPath, $file->getName());
    $uploadNum = intval(Yii::app()->request->getParam('CKEditorFuncNum'));

    $uploadFile = '';
    $error = '';

    if( !preg_match(self::ALLOWED_FILE_NAME, $fileName, $matches) )
      $error = 'Ошибка, не верный формат файла!';

    if( empty($error) && !$file->saveAs($uploadPath.$fileName) )
      $error = 'Ошибка, не удалось загрузить файл!';

    if( empty($error) )
    {
      if( $file->getName() != $fileName )
        $error = 'Файл был переименован в '.CHtml::encode($fileName);

      $uploadFile = self::UPLOAD_PATH.$fileName;
    }

    @chmod($uploadPath.$fileName, 0664);

    echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$uploadNum.", '".$uploadFile."', '".$error."');</script>";
  }
} 