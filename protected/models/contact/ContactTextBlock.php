<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date
 * @package
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