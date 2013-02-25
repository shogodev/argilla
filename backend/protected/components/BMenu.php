<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
Yii::import('backend.modules.brac.components.AccessHelper');
Yii::import('backend.modules.brac.models');

/**
 * Класс для работы с модулями бэкенда.
 * Методы используются для построения меню групп, для построения меню отдельных групп
 */
class BMenu extends CComponent
{
  public $groupNames = array(
    'content'  => 'Контент',
    'seo'      => 'SEO',
    'settings' => 'Настройки',
    'help'     => 'Помощь',
  );

  private $groups = array();

  private $modules;

  private $submodules = array();

  public function init()
  {
    $this->initGroups();
  }

  public function getGroups()
  {
    return $this->groups;
  }

  public function getModules($hideOneModule = true)
  {
    $modules = Arr::get($this->modules, $this->getCurrentGroup(), array());
    return count($modules) < 2 && $hideOneModule ? array() : $modules;
  }

  /**
   * @param bool $hideOneController - не строим подменю, если контроллер единственный
   *
   * @return array
   */
  public function getSubmodules($hideOneController = true)
  {
    return count($this->submodules) < 2 && $hideOneController ? array() : $this->submodules;
  }

  /**
   * Получаем группу по текущему модулю
   *
   * @return mixed
   */
  public function getCurrentGroup()
  {
    $currentModule = $this->getCurrentModule();

    if( $currentModule && isset($currentModule->group) )
      return $currentModule->group;

    return '';
  }

  /**
   * Формируем список доступных групп и модулей в бэкенде
   *
   * @static
   */
  private function initGroups()
  {
    $modules       = Yii::app()->getModules();
    $currentModule = $this->getCurrentModule();

    if( isset($currentModule->id) )
      $this->submodules = $this->getControllers($currentModule->id);

    foreach($modules as $id => $module)
    {
      if( !AccessHelper::checkAssessToModule($id) )
        continue;

      if( !empty($module['autoloaded']) )
      {
        Yii::import($module['class']);

        $className = ucfirst($id).'Module';
        $class     = new $className($id, null);

        if( $class instanceof BModule && !empty($class->group) && $class->enabled )
        {
          if( !isset($this->groups[$class->group])  )
          {
            $this->groups[$class->group] = array('label'       => Arr::get($this->groupNames, $class->group, $class->group),
                                                 'url'         => $this->buildUrl($id),
                                                 'active'      => $this->getCurrentGroup() == $class->group,
                                                 'itemOptions' => array('class' => $class->group)
                                                );
          }

          $this->modules[$class->group][] = array('label'       => $class->name,
                                                  'url'         => $this->buildUrl($id),
                                                  'active'      => $this->isModuleActive($currentModule, $id),
                                                  'itemOptions' => array('class' => $id),
                                                  'position'    => $class->position
                                                 );

          // Добавляем в меню виртуальные контроллеры модуля
          if( method_exists($class, 'getMenuControllers') )
          {
            $fakeControllers = $class->getMenuControllers();
            if( !empty($fakeControllers) )
            {
              $this->modules[$class->group] = $fakeControllers;

              if( isset(Yii::app()->controller->moduleMenu) )
                $this->modules[$class->group][Yii::app()->controller->moduleMenu]['active'] = true;
            }
          }
        }
      }
    }

    $this->sort();
  }

  /**
   * @param $currentModule
   * @param $id
   *
   * @return bool
   */
  private function isModuleActive($currentModule, $id)
  {
    $controllerId = ucfirst(Yii::app()->getController()->id);
    $keys         = array_keys($this->submodules);

    return isset($currentModule->id) &&
           $currentModule->id == $id &&
           in_array(lcfirst(BApplication::cutClassPrefix($controllerId)), $keys);
  }

  /**
   * @return CWebModule
   */
  private function getCurrentModule()
  {
    $currentModule = Yii::app()->getController()->getModule();

    return $currentModule;
  }

  /**
   * Строим ссылку на необходимый модуль
   *
   * @param $module
   * @param null $controller
   *
   * @return string
   */
  private function buildUrl($module, $controller = null)
  {
    return Yii::app()->createUrl(implode("/", array($module, $controller)));
  }

  /**
   * Получаем все контроллеры модуля для формирования submenu
   *
   * @param $module
   *
   * @return array
   */
  private function getControllers($module)
  {
    $controllers = array();

    foreach(glob(dirname(__FILE__).'/../modules/'.$module.'/controllers/*') as $controllerClass)
    {
      $controller = Yii::app()->getController();
      /**
       * @var BModule $module
       */
      $module = $this->getCurrentModule();

      $class    = basename($controllerClass, '.php');
      $mappedId = array_search($class, $module->controllerMap);
      $id       = $mappedId ? : BApplication::cutClassPrefix($class);

      if( !AccessHelper::init($module->id, $id)->checkAccess() )
        continue;

      $class = new $class($id);

      // Убираем ненужные виртуальные контроллеры, которые уже отобразились в меню
      $fakeControllers = $module->getMenuControllers();
      if( !empty($fakeControllers) )
        if( !(isset($class->moduleMenu, $controller->moduleMenu) && in_array($class->id, $fakeControllers[$controller->moduleMenu]['menu'])) )
          continue;

      if( !isset($class->enabled) || $class->enabled === true )
      {
        $controllers[$id] = array('label'       => $class->name,
                                  'url'         => $this->buildUrl($module->id, ($id)),
                                  'active'      => $controller->id === $id || ucfirst($controller->id) === BApplication::CLASS_PREFIX.ucfirst($id),
                                  'position'    => $class->position,
                                  'itemOptions' => array('class' => $id),
        );
      }
    }

    return $controllers;
  }

  private function sort()
  {
    $this->sortGroups();
    $this->sortModules();
    $this->sortSubmodiles();
  }

  private function sortGroups()
  {
  }

  private function sortModules()
  {
  }

  private function sortSubmodiles()
  {
    uasort($this->submodules, function($a, $b){return $a['position'] > $b['position'];});
  }
}