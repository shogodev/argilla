<?php
class SFormatterTest extends CTestCase
{
  public function testAgo()
  {
    $format = new SFormatter;
    $ago = $format->ago('-1 day');
    $this->assertEquals('1 день назад', $ago);

    $ago = $format->ago('-5 year');
    $this->assertEquals('5 лет назад', $ago);

    $ago = $format->ago('-2 month');
    $this->assertEquals('2 месяца назад', $ago);

    $ago = $format->ago('-2 day');
    $this->assertEquals('2 дня назад', $ago);

    $ago = $format->ago('-7 hour');
    $this->assertEquals('7 часов назад', $ago);

    $ago = $format->ago('-3 minute');
    $this->assertEquals('3 минуты назад', $ago);

    $ago = $format->ago('-5 second');
    $this->assertEquals('только что', $ago);
  }

  public function testTrim()
  {
    $format = new SFormatter;
    $s = $format->trim(" test \n \t");
    $this->assertEquals('test', $s);
  }

  public function testToLower()
  {
    $format = new SFormatter;
    $s = $format->toLower("TesT");
    $this->assertEquals('test', $s);
  }
}