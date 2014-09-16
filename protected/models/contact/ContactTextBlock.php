<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.contact
 */
class ContactTextBlock extends FActiveRecord
{
  protected static $textblocks = array();

  public function tableName()
  {
    return '{{contact_textblock}}';
  }

  public static function getByKey($key)
  {
    if( empty(self::$textblocks[$key]) )
      self::$textblocks[$key] = self::model()->findByAttributes(array('sysname' => $key));

    return self::$textblocks[$key];
  }
}