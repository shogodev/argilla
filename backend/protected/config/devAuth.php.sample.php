<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Файл для свободной авторизации в backend-приложении.
 * Использоваться может 2 способами:
 * 1) Возвращает ассоциативный массив с клачами username, password
 * 2) Указывает на доступный ini-файл в системе, который также должен содержать ключи username и password
 *
 * Для подключения возможности свободной аунтификации необходимо скопировать этот файл в
 * webroot/backend/protected/config/devAuth.php
 */

return array(
  'username' => null,
  'password' => null,
);