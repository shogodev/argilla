<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backond.tests.components
 *
 * @method static DateFormatBehavior model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $date
 * @property string $date_time
 * @property string $timestamp
 */
class TDateFormatTestModel extends BActiveRecord
{
  const STATIC_TABLE_NAME = '{{date_format_test_table}}';

  public function tableName()
  {
    return self::STATIC_TABLE_NAME;
  }

  public function behaviors()
  {
    return array(
      'dateFormatBehavior' => array(
        'class' => 'DateFormatBehavior',
        'attribute' => 'date'
      ),
      'dateFormatBehavior2' => array(
        'class' => 'DateFormatBehavior',
        'attribute' => 'date_time'
      ),
      'dateFormatBehavior3' => array(
        'class' => 'DateFormatBehavior',
        'attribute' => 'timestamp'
      )
    );
  }
}