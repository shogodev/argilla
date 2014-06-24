<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static TestClass model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string  $date
 * @property integer $position
 * @property string  $template
 * @property string  $name
 *
 * behaviors
 * @property string $rootAttribute
 */
class TestClass extends FActiveRecord
{
  public function tableName()
  {
    return '{{test_class}}';
  }

  public function behaviors()
  {
    return array('nestedSetBehavior' => array('class' => 'nestedset.NestedSetBehavior'));
  }

/*  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductAssignment', 'product_id'),
    );
  }

  public function scopes()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductAssignment', 'product_id'),
    );
  }

  public function getData()
  {
  }  */
}