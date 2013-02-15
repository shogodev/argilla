<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 30.08.12
 */
class TUploadedFile
{
  public static $testFile = array('name'     => 'img.jpg',
                                  'tmp_name' => '/var/tmp/tmpimg.jpg',
                                  'type'     => 'image/jpg',
                                  'size'     => 100,
                                  'error'    => 0
                                 );

  public static function init()
  {
    $mock = PHPUnit_Framework_MockObject_Generator::getMock('CUploadedFile',
                                                             array('saveAs'),
                                                             array(self::$testFile['name'],
                                                                   self::$testFile['tmp_name'],
                                                                   self::$testFile['type'],
                                                                   self::$testFile['size'],
                                                                   self::$testFile['error'])
                                                             );

    $callback = function()
    {
      return copy(self::$testFile['tmp_name'], func_get_args()[0]);
    };

    $mock->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
         ->method('saveAs')
         ->will(new PHPUnit_Framework_MockObject_Stub_ReturnCallback($callback));

    return $mock;
  }

  public static function setFile($name = null, $tmp_name = null, $type = null, $size = null, $error = null)
  {
    if( $name )
      self::$testFile['name'] = $name;

    if( $tmp_name )
      self::$testFile['tmp_name'] = $tmp_name;

    if( $type )
      self::$testFile['type'] = $type;

    if( $size )
      self::$testFile['size'] = $size;

    if( $error )
      self::$testFile['error'] = $error;
  }

  private function __construct()
  {
  }
}

?>