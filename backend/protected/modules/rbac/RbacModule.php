<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 */
class RbacModule extends BModule
{
  public $defaultController = 'BUser';
  public $name = 'Доступ';
  public $group = 'settings';

  public $controllerMap = [
    'operation' => 'BRbacOperationController',
    'role'      => 'BRbacRoleController',
    'task'      => 'BRbacTaskController',
    'user'      => 'BUserController',
  ];
}
