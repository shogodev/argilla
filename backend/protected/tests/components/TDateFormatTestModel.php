<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backond.tests.components
 *
 * @method static DateFormatBehavior model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $date
 */
class TDateFormatTestModel extends BActiveRecord
{
  public function tableName()
  {
    return '{{date_format_test_table}}';
  }

  public function behaviors()
  {
    return array(
      'dateFormatBehavior' => array(
        'class' => 'DateFormatBehavior',
        'attribute' => 'date'
      )
    );
  }
}