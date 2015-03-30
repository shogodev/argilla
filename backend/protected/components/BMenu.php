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

  /**
   * @var BController
   */
  private $controller;

  /**
   * @var BModule
   */
  private $currentModule;

  /**
   * @var string
   */
  private $currentGroup;


  public function init()
  {
    $this->controller = Yii::app()->controller;
    $this->currentModule = $this->controller->module;
    if( $this->currentModule && !empty($this->currentModule->group) )
      $this->currentGroup = $this->currentModule->group;

    $this->buildStructure(Yii::app()->getModules());

    $this->sort();
  }

  public function getGroups()
  {
    return $this->groups;
  }

  public function getModules($hideOneModule = true)
  {
    $modules = Arr::get($this->modules, $this->currentGroup, array());
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

  public function getDefaultRout($default = '')
  {
    if( isset($this->modules) && $group = Arr::reset($this->modules) )
    {
      if( $module = Arr::reset($group) )
      {
        return $module['module']->id.'/'.key($module['module']->controllerMap);
      }
    }

    return $default;
  }

  /**
   * @param array $modules
   * @param BModule $parent|null
   * @throws CException
   */
  private function buildStructure(array $modules, $parent = null)
  {
    $filteredModules = AccessHelper::filterModulesByAccess($modules);

    foreach($filteredModules as $moduleId => $moduleConfig)
    {
      if( empty($moduleConfig['autoloaded']) || $moduleConfig['autoloaded'] == false )
        continue;

      Yii::import($moduleConfig['class']);
      $moduleClassName = ucfirst($moduleId).'Module';
      /**
       * @var BModule $module
       */
      $module = new $moduleClassName($moduleId, $parent);
      if( !$this->allowedModule($module) )
        continue;

      if( $this->isModuleActive($module) )
      {
        $this->createSubmodulesMenu($module);
      }

      if( $this->needCreateModulesMenu($module) )
      {
        $this->createGroupsMenu($module);
        $this->createModulesMenu($module);
      }

      if( !empty($moduleConfig['modules']) )
      {
        $module->setModules($moduleConfig['modules']);
        $this->buildStructure($module->getModules(), $module);
      }
    }
  }

  /**
   * @param BModule $module
   *
   * @return bool
   */
  private function isModuleActive(BModule $module)
  {
    if( !$this->currentModule )
      return false;

    if( $this->currentModule->getName() == $module->getName() )
      return true;

    if( $currentModuleParents = $this->currentModule->getParents() )
    {
      if( isset($currentModuleParents[$module->getName()]) )
        return true;
    }

    if( $parents = $module->getParents() )
    {
      if( isset($parents[$this->currentModule->getName()]) )
        return true;
    }

    if( array_intersect(array_keys($currentModuleParents), array_keys($parents)))
      return true;

    return false;
  }

  /**
   * @param BModule $module
   *
   * @return bool
   */
  private function needCreateModulesMenu(BModule $module)
  {
    return !$module->getParentModule();
  }

  /**
   * @param BModule $module
   */
  private function createGroupsMenu($module)
  {
    if( !isset($this->groups[$module->group]) )
    {
      $this->groups[$module->group] = array(
        'label' => Arr::get($this->groupNames, $module->group, $module->group),
        'url' => $module->createUrl('/'),
        'active' => $this->currentGroup == $module->group,
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
      'url' => $module->createUrl('/'),
      'active' => $this->isModuleActive($module),
      'itemOptions' => array('class' => $module->id),
      'position' => $module->position,
      'module' => $module
    );

    // Добавляем в меню виртуальные контроллеры модуля
    if( method_exists($module, 'getMenuControllers') )
    {
      $fakeControllers = $module->getMenuControllers();
      if( !empty($fakeControllers) )
      {
        $this->modules[$module->group] = $fakeControllers;

        if( isset($this->controller->moduleMenu) )
          $this->modules[$module->group][$this->controller->moduleMenu]['active'] = true;
      }
    }
  }

  /**
   * @param BModule $module
   *
   * @return array
   */
  private function createSubmodulesMenu($module)
  {
    foreach($module->controllerMap as $mappedId => $controllerClass)
    {
      $id = $mappedId ? : BApplication::cutClassPrefix($controllerClass);

      if( !AccessHelper::init($module->id, $id)->checkAccess() )
        continue;

      // todo: Использовать рефлекшен если он быстей
      /**
       * @var BController $controller
       */
      $controller = new $controllerClass($id, null);

      // Убираем ненужные виртуальные контроллеры, которые уже отобразились в меню
      $fakeControllers = $module->getMenuControllers();
      if( !empty($fakeControllers) )
        if( !(isset($controller->moduleMenu, $this->controller->moduleMenu) && in_array(BApplication::CLASS_PREFIX.ucfirst($controller->id), $fakeControllers[$this->controller->moduleMenu]['menu'])) )
          continue;

      if( !isset($controller->enabled) || $controller->enabled === true )
      {
        $this->submodules[$id] = array(
          'label' => $controller->name,
          'url' => $module->createUrl($controller->id),
          'active' => $this->controller->id === $id || ucfirst($this->controller->id) === BApplication::CLASS_PREFIX.ucfirst($id),
          'position' => $controller->position,
          'itemOptions' => array('class' => $id),
        );
      }
    }
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
    if( empty($this->modules) )
      return;
    foreach($this->modules as $key => $data)
    {
      uasort($this->modules[$key], function($a, $b) {
        return $a['label'] > $b['label'];
      });
    }
  }

  private function sortSubmodiles()
  {
    uasort($this->submodules, function($a, $b) {
      return $a['position'] > $b['position'];
    });
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