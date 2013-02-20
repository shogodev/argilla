<?php
/**
 * Base class for all backend`s modules
 *
 * @author Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
class BModule extends CWebModule
{
  public $group = 'content';

  public $name = '[Не задано]';

  public $position = 0;

  public $enabled = true;

  public $autoloaded;

  public $defaultUploadDir  = 'f/';

  public $thumbsSettings = array();

  /**
   * Поддериктории подуля для загрузки
   *
   * @var array
   */
  public $defaultDirectoriesToImport = array(
    'controllers',
    'models',
    'components',
    'exceptions',
  );

  /**
   * @param string  $id
   * @param CModule $parent
   * @param null    $config
   */
  public function __construct($id, $parent, $config = null)
  {
    parent::__construct($id, $parent, $config);
    $this->loadControllerMap();
  }

  public function init()
  {
    $import = CMap::mergeArray($this->getAutomaticImport(), $this->loadExtraDirectoriesToImport());
    $this->setImport($import);
  }

  public function beforeControllerAction($controller, $action)
  {
    if( !$this->enabled )
      throw new CHttpException(404, 'The requested page does not exist.');

    return parent::beforeControllerAction($controller, $action);
  }

  public function getUploadPath()
  {
    return Yii::app()->getFrontendPath().$this->defaultUploadDir.$this->id.'/';
  }

  public function getUploadUrl()
  {
    return Yii::app()->getFrontendUrl().$this->defaultUploadDir.$this->id.'/';
  }

  /**
   * Возвращаем значения миниатюр для изображений
   * Массив сортируем по убыванию, последним элементом должна быть самая маленькая миниатюра.
   * Если в массиве присутствует ключ origin, то оригинальное изображение пережимается до указанных
   * по этому ключу размеров.
   *
   * @return array
   */
  public function getThumbsSettings()
  {
    return $this->thumbsSettings;
  }

  /**
   * Возвращаем массив контроллеров, которые нужно отображать в меню
   *
   * @return array
   */
  public function getMenuControllers()
  {
    return array();
  }

  /**
   * Загрузка всех контроллеров из модуля в controllerMap
   */
  protected function loadControllerMap()
  {
    if( $this->controllerMap !== [] )
      return;

    $directory = Yii::getPathOfAlias('backend.modules.'.$this->getId().'/controllers');

    if( file_exists($directory) )
    {
      foreach( CFileHelper::findFiles($directory, array('fileTypes' => array('php'))) as $controllerFilePath )
      {
        $controllerFilePathParts = explode(DIRECTORY_SEPARATOR, $controllerFilePath);
        $controllerName = str_replace('.php', '', end($controllerFilePathParts));
        $controllerAlias = $this->getControllerAlias($controllerName);

        if( empty($this->controllerMap[$controllerAlias]) )
          $this->controllerMap[$controllerAlias] = $controllerName;
      }
    }
    else
      Yii::log('Невозможно загрузить директорию контроллеров '.get_class($this).' по пути '.$directory);
  }

  /**
   * Получение синонима контроллера для controllerMap
   *
   * @param string controller
   *
   * @return string
   */
  protected function getControllerAlias($controller)
  {
    return lcfirst(str_replace('Controller', '', BApplication::cutClassPrefix($controller)));
  }

  /**
   * Получение синонимов для автоматического импорта модуля
   *
   * @return array
   */
  protected function getAutomaticImport()
  {
    $import = array();

    foreach( $this->defaultDirectoriesToImport as $directory )
    {
      $import[] = 'backend.modules.'.$this->getId().'.'.$directory.'.*';
    }

    return $import;
  }

  /**
   * Загрузка дополнительный директорий для импорта
   * Синоним пути должен быть полным (backend.modules.somemodule.models.*)
   *
   * @return array
   * @throws CException
   */
  final protected function loadExtraDirectoriesToImport()
  {
    $import = array();

    foreach( $this->getExtraDirectoriesToImport() as $directory )
    {
      if( !preg_match('/backend/', $directory) )
        throw new CException('Неверный формат импорта для '.$directory.' в '.get_class($this));

      $import[] = $directory;
    }

    return $import;
  }

  /**
   * Получение дополнительных поддиректорий для импорта в модуле
   *
   * @return array
   */
  protected function getExtraDirectoriesToImport()
  {
    return array();
  }
}