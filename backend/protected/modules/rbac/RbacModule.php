<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 */
class RbacModule extends BModule
{
  public $defaultController = 'BUser';

  public $name = 'Доступ';

  public $group = 'settings';

  public $controllerMap = [
    'rbacOperation' => 'BRbacOperationController',
    'rbacRole' => 'BRbacRoleController',
    'rbacTask' => 'BRbacTaskController',
    'user' => 'BUserController',
  ];
}
