<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class UtilsTest extends CTestCase
{
  public function testBuildUrl()
  {
    $urls = array(
      'http://www.argilla.ru/',
      'http://www.argilla.ru#foo',
      'http://www.argilla.ru/index.php',
      'http://www.argilla.ru/index.php?param1=foo',
      'http://www.argilla.ru/index.php?param1=foo&param2=bar#foo',
      '/',
      '/index.php',
      '/index.php?param1=foo',
      '/index.php?param1=foo&param2=bar#foo',
      '',
      'index.php',
      'index.php?param1=foo',
      'index.php?param1=foo&param2=bar#foo',
      '/?' => '/',
      '/index.php?' => '/index.php',
      '/index.php#' => '/index.php',
    );

    foreach($urls as $key => $url)
    {
      $processedUrl = is_int($key) ? $url : $key;
      $processedUrl = Utils::buildUrl(parse_url($processedUrl));
      $this->assertEquals($url, $processedUrl);
    }
  }
}