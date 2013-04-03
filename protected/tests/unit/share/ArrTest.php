<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ArrTest extends CTestCase
{
  public function testGet()
  {
    $array = [1 => 2, 3 => ''];

    $result = Arr::get($array, 1);
    $this->assertEquals(2, $result);

    $result = Arr::get($array, 3, 33, true);
    $this->assertEquals(33, $result);
  }

  public function testCut()
  {
    $array = [1 => 2, 3 => ''];

    $result = Arr::cut($array, 1);
    $this->assertEquals(2, $result);
    $this->assertNotContains(1, $array);

    $result = Arr::cut($array, 3, 33, true);
    $this->assertEquals(33, $result);
    $this->assertNotContains(33, $array);
  }

  public function testExtract()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::extract($array, 1);
    $this->assertEquals(2, $result);

    $result = Arr::extract($array, [1]);
    $this->assertEquals([1 => 2], $result);
  }

  public function testReset()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::reset($array);
    $this->assertEquals(2, $result);
  }

  public function testEnd()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::end($array);
    $this->assertEquals(6, $result);
  }

  public function testKeysExists()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::keysExists($array, 1);
    $this->assertTrue($result);

    $result = Arr::keysExists($array, [1]);
    $this->assertTrue($result);

    $result = Arr::keysExists($array, [7]);
    $this->assertFalse($result);
  }

  public function testFromObj()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $object = new stdClass();
    $object->a = 1;

    $result = Arr::fromObj($object);
    $this->assertEquals(['a' => 1], $result);

    $result = Arr::fromObj($array);
    $this->assertEquals($array, $result);
  }

  public function testIsIntersec()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::isIntersec($array, [1 => 2]);
    $this->assertTrue($result);

    $result = Arr::isIntersec($array, [1 => 'a']);
    $this->assertFalse($result);
  }

  public function testTrim()
  {
    $array = [' a', 'b| '];

    $result = Arr::trim($array);
    $this->assertEquals(['a', 'b|'], $result);

    $result = Arr::trim($array, ' |');
    $this->assertEquals(['a', 'b'], $result);
  }

  public function testImplode()
  {
    $array = ['a', ' b', '', ' c'];

    $result = Arr::implode($array, ", ");
    $this->assertEquals('a, b, c', $result);
  }

  public function testReflect()
  {
    $array = [1, 2];

    $result = Arr::reflect($array);
    $this->assertEquals([1 => 1, 2 => 2], $result);
  }

  public function testReduce()
  {
    $result = Arr::reduce([1, 2]);
    $this->assertEquals(1, $result);

    $result = Arr::reduce(3);
    $this->assertEquals(3, $result);
  }

  public function testPush()
  {
    $array = [1 => [1]];

    Arr::push($array, 1, 2);
    Arr::push($array, 2, 1);
    $this->assertEquals([1 => [1, 2], 2 => [1]], $array);
  }

  public function testDivide()
  {
    $array = [1, 2];

    $result = Arr::divide($array);
    $this->assertEquals([[1], [2]], $result);
  }

  public function testArrayMergeAssoc()
  {
    $array1 = [1 => 'a'];
    $array2 = [2 => 'b'];

    $result = Arr::mergeAssoc($array1, $array2);
    $this->assertEquals([1 => 'a', 2 => 'b'], $result);
  }

  public function testInsertAfter()
  {
    $array = ['a' => 'aa', 'b' => 'bb'];
    Arr::insertAfter($array, 'c', 'cc', 'b');
    $this->assertEquals(['a' => 'aa', 'b' => 'bb', 'c' => 'cc'], $array);

    $array = [1 => 'a', 3 => 'c'];
    Arr::insertAfter($array, 2, 'b', 1);
    $this->assertEquals([1 => 'a', 2 => 'b', 3 => 'c'], $array);
  }
}