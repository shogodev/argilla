<?php
/**
 * Base class for all backend`s modules
 *
 * @author Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
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

  public $defaultUploadDir = 'f/';

  public $thumbsSettings = [];

  /**
   * Список других модулей, от которых зависит текущий модуль
   *
   * @var array
   */
  public $moduleDependencies = [];

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
    'modules',
    'behaviors',
    '*'
  );

  protected function preinit()
  {
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
    return Yii::app()->getFrontendRoot().$this->defaultUploadDir.$this->id.'/';
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

  public function getWatermarkSettings()
  {
    return array();
  }

  public function getHeaderCssClass()
  {
    return preg_replace('/(\/.*)/', '', $this->getId());
  }

  /**
   * @return array
   */
  public function getParents()
  {
    $parents = array();
    if( $parent = $this->getParentModule() )
    {
      $parents = CMap::mergeArray(array($parent->getName() => $parent), $parent->getParents($parent));
    }

    return array_reverse($parents);
  }

  public function createUrl($route, $params=array(), $ampersand='&')
  {
    $parents = CMap::mergeArray(array_keys($this->getParents()), array($this->id));
    $route = implode('/', CMap::mergeArray($parents, array($route)));

    return Yii::app()->createUrl($route, $params, $ampersand);
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

    if( file_exists($this->getControllerPath()) )
    {
      foreach( CFileHelper::findFiles($this->getControllerPath(), array('fileTypes' => array('php'))) as $controllerFilePath )
      {
        $controllerFilePathParts = explode(DIRECTORY_SEPARATOR, $controllerFilePath);
        $controllerName = str_replace('.php', '', end($controllerFilePathParts));
        $controllerAlias = $this->getControllerAlias($controllerName);

        if( empty($this->controllerMap[$controllerAlias]) )
          $this->controllerMap[$controllerAlias] = $controllerName;
      }
    }
    else
      Yii::log('Невозможно загрузить директорию контроллеров '.get_class($this).' по пути '.$this->getControllerPath());
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

    foreach($this->defaultDirectoriesToImport as $directory)
    {
      $import[] = "{$this->id}.{$directory}.*";
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