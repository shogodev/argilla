<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.commands.api
 */
Yii::import('frontend.commands.api.*');
Yii::import('frontend.commands.CheckPhpDocCommand');

class CheckPhpDocTest extends CTestCase
{
  public function setUp()
  {
    Yii::app()->db->createCommand()->createTable("{{test_class}}", array(
      'id' => 'INT(11) NOT NULL',
      'name' => 'VARCHAR(255)',
      'email' => 'VARCHAR(255)'
    ));
  }

  public function testCheckProperties()
  {
    $path = dirname(__FILE__).DIRECTORY_SEPARATOR.'testedClasses';

    $files = CFileHelper::findFiles($path, array('fileTypes' => array('php')));

    $model = new checkPhpDoc();

    ob_start();
    $model->checkProperties($files);
    $errors = $this->errors();
    ob_clean();

    $this->assertTrue(array('email') == $errors['notEnough']['TestClass']);
//    $this->assertTrue(array('date', 'position', 'template') == $errors['excess']['TestClass']);
  }

  public function tearDown()
  {
    Yii::app()->db->createCommand()->dropTable("{{test_class}}");
  }


  protected function errors()
  {
    $errors = array();

    preg_match_all('/ERROR.............:\s(.*)!\nSource file.......:\s(.*)\nClass.............:\s(.*)\nProperties:.......:\s(.*)\n/', ob_get_contents(), $matches);

    foreach($matches[0] as $key => $value)
    {
      $error = strpos($matches[1][$key], 'Excess') !== false ? 'excess' : 'notEnough';

      if( !isset($errors[$error]) )
        $errors[$error] = array();

      $class = $matches[3][$key];


      $errors[$error][$class] = explode(', ', $matches[4][$key]);
    }

    return $errors;
  }
}