<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class DummyLogger extends CLogger
{
  public function log($message,$level='info',$category='application')
  {
  }

  public function getLogs($levels='',$categories=array(), $except=array())
  {
    return array();
  }
}