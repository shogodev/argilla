<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 *
 * @property BMenu $menu
 * @property BBreadcrumbsManager $breadcrumbs
 */
class BApplication extends CWebApplication
{
  const CLASS_PREFIX = 'B';

  /**
   * Удаление префикса класса
   *
   * @param string $className
   *
   * @return string mixed
   */
  public static function cutClassPrefix($className)
  {
    return preg_replace('/^'.self::CLASS_PREFIX.'([A-Z])(.*)/', '$1$2', $className);
  }

  /**
   * Получение id контроллера из BModule::$controllerMap
   * если он не находится, то возвращается обычный ID контроллера
   * @return string
   */
  public static function getMappedControllerId()
  {
    $controllerMap = Yii::app()->controller->module->controllerMap;
    $controllerClass = get_class(Yii::app()->controller);
    $mappedId = array_search($controllerClass, $controllerMap);

    return $mappedId ? : Yii::app()->controller->id;
  }

  public function getFrontendRoot()
  {
    return realpath(Yii::getPathOfAlias('frontend').'/..').'/';
  }

  public function getFrontendUrl()
  {
    return $this->request->getHostInfo().'/';
  }

  public function registerAjaxUpdateError()
  {
    Yii::app()->clientScript->registerScript('ajaxUpdateError', '
      function ajaxUpdateError(xhr, err)
      {
        if( xhr.status === 0 && xhr.readyState === 0 )
        {
          console.log("Ajax request aborted");
          return;
        }

        if( xhr.status === 401 )
          assigner.open(
            "'.Yii::app()->controller->createUrl("/base/index", array('popup' => true)).'",
            {width : "800", height : "400", left : "50%", top : "50%", marginTop : "-250px", marginLeft: "-400px"}
          );
        else
        {
          if( !err )
          {
            if( xhr.status && !/^\s*$/.test(xhr.status) )
              err = "Error " + xhr.status;
            else
            {
              err = "Error";
            }

            if( xhr.responseText && !/^\s*$/.test(xhr.responseText) )
              err = err + ": " + xhr.responseText;
          }

          alert(err);
        }
      }', CClientScript::POS_HEAD);
  }

  protected function init()
  {
    $this->setModules($this->findModules(Yii::getPathOfAlias('backend')));

    $this->params->project = preg_replace("/^www./", '', Yii::app()->request->serverName);
    $this->setMbEncoding();

    parent::init();
  }

  protected function setMbEncoding()
  {
    mb_internal_encoding("UTF-8");
    mb_http_output("UTF-8");
  }

  /**
   * @param string $basePath
   *
   * @return array
   */
  protected function findModules($basePath)
  {
    $modulesPath = $basePath.DIRECTORY_SEPARATOR.'modules';
    $modules = array();

    foreach(glob($modulesPath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $moduleDirectory)
    {
      if( preg_match("/\w+/", basename($moduleDirectory)) )
      {
        $moduleName = basename($moduleDirectory);
        $modules[$moduleName] = array('autoloaded' => true);

        if( $submodules = $this->findModules($moduleDirectory) )
          $modules[$moduleName]['modules'] = $submodules;
      }
    }

    return $modules;
  }
}