<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ViewHelperTest extends CTestCase
{
  public function testGetClearPhone()
  {
    $this->assertEquals('+74954567890', ViewHelper::getClearPhone('8(495)456-78-90'));
    $this->assertEquals('+78124567890', ViewHelper::getClearPhone('8(812)456-78-90'));
    $this->assertEquals('+302106742949', ViewHelper::getClearPhone('+30 (210) 6742949'));
    $this->assertEquals('+74996491734', ViewHelper::getClearPhone('+7 (499) 649-17-34'));
    $this->assertEquals('6491635', ViewHelper::getClearPhone('649-16-35'));
  }
}