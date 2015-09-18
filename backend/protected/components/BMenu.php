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
    if( $groups = $this->getGroups() )
    {
      return Arr::reset($groups)['route'];
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

      if( !class_exists($moduleClassName) )
      {
        throw new ClassNotFoundException($moduleClassName, $moduleConfig['class']);
      }

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
        if( $this->createGroupsMenu($module) )
        {
          if( !$this->createFakeModulesMenu($module) )
            $this->createModulesMenu($module);
        }
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
   *
   * @return bool
   */
  private function createGroupsMenu($module)
  {
    if( !isset($this->groups[$module->group]) )
    {
      if( !$controllerClass = $this->getAllowedControllerClass($module) )
        return false;

      $controllerId = $module->getControllerId($controllerClass);

      $this->groups[$module->group] = array(
        'label' => Arr::get($this->groupNames, $module->group, $module->group),
        'url' => $module->createUrl($controllerId),
        'route' => implode('/', array($module->getName(),  $controllerId)),
        'active' => $this->currentGroup == $module->group,
        'itemOptions' => array('class' => $module->group)
      );
    }

    return true;
  }

  /**
   * @param BModule $module
   */
  private function createModulesMenu($module)
  {
    if( !$controllerClass = $this->getAllowedControllerClass($module) )
      return false;

    $controllerId = $module->getControllerId($controllerClass);

    $this->modules[$module->group][$module->getName()] = array(
      'label' => $module->name,
      'url' => $module->createUrl($controllerId),
      'active' => $this->isModuleActive($module),
      'itemOptions' => array('class' => $module->id),
      'position' => $module->position,
      'module' => $module
    );
  }

  /**
   * Добавляет в меню виртуальные контроллеры модуля
   *
   * @param BModule $module
   *
   * @return bool
   * @throws CHttpException
   */
  private function createFakeModulesMenu($module)
  {
    if( $fakeMenu = $module->getMenuControllers() )
    {
      foreach($fakeMenu as $key => $menuItem)
      {
        $subMenu = Arr::cut($menuItem, 'menu', array());

        foreach($subMenu as $controllerName)
        {
          $controllerClass = $controllerName.'Controller';

          if( AccessHelper::checkAccessByNameClasses(get_class($module), $controllerClass) )
          {
            $controllerId = $module->getControllerId($controllerClass);

            if( !isset($this->modules[$module->group][$key]) )
            {
              $this->modules[$module->group][$key] = $menuItem;
              $this->modules[$module->group][$key]['url'] = $module->createUrl($controllerId);
            }

            $this->modules[$module->group][$key]['menu'][$controllerId] = $controllerName;
          }
        }
      }

      if( isset($this->controller->moduleMenu) )
        $this->modules[$module->group][$this->controller->moduleMenu]['active'] = true;

      return true;
    }
  }

  /**
   * @param BModule $module
   *
   * @return array
   */
  private function createSubmodulesMenu($module)
  {
    foreach($module->controllerMap as $id => $controllerClass)
    {
      if( !AccessHelper::checkAccessByNameClasses(get_class($module), $controllerClass) )
        continue;

      /**
       * @var BController $controller
       */
      $controller = new $controllerClass($id, null);

      if( !$controller->showInMenu )
        continue;

      // Убираем ненужные виртуальные контроллеры, которые уже отобразились в меню
      if( $fakeControllers = $module->getMenuControllers() )
      {
        if( !(isset($controller->moduleMenu, $this->controller->moduleMenu) && in_array(BApplication::CLASS_PREFIX.ucfirst($controller->id), $fakeControllers[$this->controller->moduleMenu]['menu'])) )
          continue;
      }

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

  private function getAllowedControllerClass(BModule $module)
  {
    $defaultController = $module->defaultController.'Controller';

    if( !class_exists($defaultController) )
      throw new CHttpException(500, 'Не удалось найти defaultController '.$defaultController);

    if( AccessHelper::checkAccessByNameClasses(get_class($module), $defaultController) )
      return $defaultController;

    foreach($module->controllerMap as $controller)
    {
      if( AccessHelper::checkAccessByNameClasses(get_class($module), $controller) )
        return $controller;
    }

    return null;
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