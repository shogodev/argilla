<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 *
 * Класс для работы с модулями бэкенда.
 * Методы используются для построения меню групп, для построения меню отдельных групп
 */
Yii::import('backend.modules.brac.components.AccessHelper');
Yii::import('backend.modules.brac.models');

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
    if( $currentModule = $this->getCurrentModule() )
      $this->submodules = $this->getSubmodulesMenu($currentModule);

    foreach(Yii::app()->getModules() as $moduleId => $moduleConfig)
    {
      if( !AccessHelper::checkAssessToModule($moduleId) )
        continue;

      if( !empty($moduleConfig['autoloaded']) )
      {
        Yii::import($moduleConfig['class']);
        $moduleClassName = ucfirst($moduleId).'Module';
        /**
         * @var BModule $module
         */
        $module = new $moduleClassName($moduleId, null);

        if( !$this->allowedModule($module) )
          continue;

        if( $currentModule = $this->getCurrentModule() )
        {
          if( $currentModule->parentModule == $moduleId || $module->parentModule == $currentModule->id )
          {
            $this->submodules = CMap::mergeArray($this->submodules, $this->getSubmodulesMenu($module));
          }
        }

        if( !empty($module->parentModule) )
          continue;

        $this->createGroupsMenu($module);
        $this->createModulesMenu($module);
      }
    }

    $this->sort();
  }

  /**
   * @param BModule $module
   */
  private function createGroupsMenu($module)
  {
    if( !isset($this->groups[$module->group])  )
    {
      $this->groups[$module->group] = array(
        'label' => Arr::get($this->groupNames, $module->group, $module->group),
        'url' => $this->buildUrl($module->id),
        'active' => $this->getCurrentGroup() == $module->group,
        'itemOptions' => array('class' => $module->group)
      );
    }
  }

  /**
   * @param BModule $module
   */
  private function createModulesMenu($module)
  {
    $this->modules[$module->group][] = array(
      'label' => $module->name,
      'url' => $this->buildUrl($module->id),
      'active' => $this->isModuleActive($module->id),
      'itemOptions' => array('class' => $module->id),
      'position' => $module->position
    );

    // Добавляем в меню виртуальные контроллеры модуля
    if( method_exists($module, 'getMenuControllers') )
    {
      $fakeControllers = $module->getMenuControllers();
      if( !empty($fakeControllers) )
      {
        $this->modules[$module->group] = $fakeControllers;

        if( isset(Yii::app()->controller->moduleMenu) )
          $this->modules[$module->group][Yii::app()->controller->moduleMenu]['active'] = true;
      }
    }
  }

  /**
   * @param $id
   *
   * @return bool
   */
  private function isModuleActive($id)
  {
    if( !$currentModule = $this->getCurrentModule())
      return false;

    $controllerId = ucfirst(Yii::app()->getController()->id);
    $keys = array_keys($this->submodules);

    return in_array($id, array($currentModule->id, $currentModule->parentModule)) && in_array(lcfirst(BApplication::cutClassPrefix($controllerId)), $keys);
  }

  /**
   * @return BModule
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
   * @param BModule $module
   *
   * @return array
   */
  private function getSubmodulesMenu($module)
  {
    $controllers = array();

    foreach($this->getModuleControllers($module->id) as $controllerClass)
    {
      $controller = Yii::app()->getController();

      $class    = basename($controllerClass, '.php');
      $mappedId = array_search($class, $module->controllerMap);
      $id       = $mappedId ? : BApplication::cutClassPrefix($class);
      if( !AccessHelper::init($module->id, $id)->checkAccess() )
        continue;

      $class = new $class($id);

      // Убираем ненужные виртуальные контроллеры, которые уже отобразились в меню
      $fakeControllers = $module->getMenuControllers();
      if( !empty($fakeControllers) )
        if( !(isset($class->moduleMenu, $controller->moduleMenu) && in_array(BApplication::CLASS_PREFIX.ucfirst($class->id), $fakeControllers[$controller->moduleMenu]['menu'])) )
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

  /**
   * Получаем все контроллеры модуля для формирования submenu
   * @param $moduleName
   *
   * @return array
   */
  private function getModuleControllers($moduleName)
  {
    return glob(dirname(__FILE__).'/../modules/'.$moduleName.'/controllers/*');
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

  /**
   * @param BModule $module
   *
   * @return bool
   */
  private function allowedModule($module)
  {
    return $module instanceof BModule && !empty($module->group) && $module->enabled;
  }
}