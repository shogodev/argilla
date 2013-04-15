<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BGridSettingsTest extends CTestCase
{
  public function testGetClasses()
  {
    $model   = new BGridSettings();
    $classes = $model->getClasses();

    foreach($classes as $class => $header)
      $this->assertTrue(class_exists($class));
  }
}