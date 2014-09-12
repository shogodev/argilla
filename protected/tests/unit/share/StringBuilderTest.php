<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class StringBuilderTest extends CTestCase
{
  public function testBuilder()
  {
    $string  = 'some data';
    $builder = new StringBuilder($string);

    for($i = 0; $i < 10; $i++)
    {
      $string .= $i;
      $builder->append($i);
    }

    $this->assertEquals($string, $builder->get());

    $string = str_replace('some', '', $string);
    $this->assertEquals($string, $builder->remove('some'));

    $string = preg_replace('/(\d+)/', '2', $string);
    $this->assertEquals($string, $builder->replace('/(\d+)/', '2', true));

    $string = preg_replace_callback('(2)', function ()
    {
      return null;
    }, $string);

    $this->assertEquals($string, $builder->replace('(2)', function ()
    {
      return null;
    }, true));

    $string = str_replace('data', 'atad', $string);
    $this->assertEquals($string, $builder->replace('data', 'atad'));
  }

  public function testChain()
  {
    $string = null;

    for($i = 0; $i < 50; $i++)
    {
      $string .= $i;
    }

    $builder = new StringBuilder($string);

    $string = str_replace('2', null, $string);
    $string = str_replace('5', '10', $string);
    $string = preg_replace('/[0-5]/', '0', $string);

    $builder->remove('2')->replace('5', '10')->replace('/[0-5]/', '0', true);

    $this->assertEquals($string, $builder->get());
  }
}