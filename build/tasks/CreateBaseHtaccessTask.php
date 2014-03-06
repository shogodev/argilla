<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.build.tasks
 */
require_once "phing/Task.php";

class CreateBaseHtaccessTask extends Task
{
  protected $directory;

  public function setDir($directory)
  {
    $this->directory = $directory;
  }

  public function main()
  {
    $content = "RemoveType application/x-httpd-php".PHP_EOL;
    $content .= "AddType application/x-httpd-php-".phpversion()." .php".PHP_EOL;

    if( !file_put_contents($this->directory.'.htaccess', $content) )
      throw new BuildException('Cannot create file .htaccess');
  }
}