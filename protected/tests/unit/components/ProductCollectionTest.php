<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class ProductCollectionTest extends CTestCase
{
  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Product', 'one', array('url' => 'new_product1'));

    parent::setUp();
  }

  public function testCreate()
  {
    $collection = new FCollection('basket');
    $this->assertInstanceOf('FCollection', $collection);
  }

  public function testAdd()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));
    $this->assertEquals($collection->count(), 1);

    $collection->add(array(
      'id' => 1,
      'amount' => 3,
      'type' => 'product'
    ));
    $this->assertEquals($collection->count(), 1);

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'red')
    ));
    $this->assertEquals($collection->count(), 2);

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'red')
    ));
    $this->assertEquals($collection->count(), 2);

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'yellow')
    ));
    $this->assertEquals($collection->count(), 3);

    $collection->add(array(
      'id' => 4,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          'id' => 3,
          'type' => 'product'
        )
      )
    ));
    $this->assertEquals($collection->count(), 4);

    $collection = new FCollection('basket', array(), array('Product'), false);

    $collection->add(array(
      'id' => 1,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $this->assertEquals($collection->count(), 1);

    $collection = new FCollection('basket', array('options'), array('Product'), false);

    $collection->add(array(
      'id' => 1,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        )
      )
    );

    $collection->add(array(
        'id' => 1,
        'amount' => 1,
        'type' => 'product',
        'items' => array(
          'size' => '20',
        )
      )
    );

    $this->assertEquals($collection->count(), 1);
  }

  public function testGetElementByIndex()
  {
    $collection = new FCollection('basket', array(), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 3,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 3,
      'amount' => 2,
      'type' => 'product'
    ));

    $element = $collection->getElementByIndex(1);


    $this->assertEquals($element->collectionIndex, 1);
    $this->assertEquals($element->primaryKey, 2);
    $this->assertEquals($element->collectionAmount, 3);
  }

  public function testCountAmount()
  {
    $collection = new FCollection('basket', array(), array('Product'), false);

    $collection->add(array(
      'id' => 1,
      'type' => 'product',
      'amount' => 2
    ));

    $collection->add(array(
      'id' => 3,
      'type' => 'product',
    ));

    $this->assertEquals($collection->countAmount(), 3);

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $this->assertEquals($collection->countAmount(), 5);

    $element = $collection->getElementByIndex(2);

    $this->assertEquals($element->collectionItems['options']->countAmount(), 3);

    /*    $collection->add(3);
        $this->assertEquals($collection->countAmount(), 4);


        $collection = new FCollection('basket', array('size'));
        $collection->add(1, 2, array('size' => 3));
        $collection->add(1, 1, array('size' => 3));
        $this->assertEquals($collection->countAmount(), 3);

        $collection->add(1, 1, array('size' => 4));
        $this->assertEquals($collection->countAmount(), 4);


        $collection = new FCollection('basket', array('size'));

        $collection->add(1, 2, array('size' => 3));
        $collection->add(1, 1, array('color' => 'red', 'size' => 3));
        $this->assertEquals($collection->countAmount(), 3);*/
  }

  public function testRemove()
  {
    $collection = new FCollection('basket', null, array('Product'));
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 3,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 4,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'red')
    ));

    $collection->add(array(
      'id' => 3,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $collection->createPathsRecursive();

    $this->assertEquals($collection->count(), 4);

    $collection->remove(1);
    $this->assertEquals($collection->count(), 3);

    $this->assertEquals($collection->getElementByIndex('basket[3]')->collectionItems['options']->count(), 2);
    $collection->remove('basket[3][options][0]');
    $this->assertEquals($collection->getElementByIndex('basket[3]')->collectionItems['options']->count(), 1);
    $this->assertEquals($collection->count(), 3);
  }

  public function testSave()
  {
    $collection = new FCollection('basket', array(), array('Product'));

    $this->assertTrue(!isset($_SESSION['basket']));

    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $data = array(
      0 => array(
      'id' => 1,
        'type' => 'product',
        'amount' => 1,
        'index' => 0,
        'items' => array()
      )
    );

    $this->assertEquals($_SESSION['basket'], $data);

    $collection->add(array(
      'id' => 2,
      'type' => 'product',
      'amount' => 2,
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $data = array(
      'id' => 2,
      'type' => 'product',
      'amount' => 2,
      'index' => 1,
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product',
            'amount' => 2,
            'index' => 0,
            'items' => array()
          ),
          array(
            'id' => 1,
            'type' => 'product',
            'amount' => 1,
            'index' => 1,
            'items' => array()
          )
        )
      )
    );

    $this->assertEquals($_SESSION['basket'][1], $data);

    $collection = new FCollection('basket', array(), array('Product'));

    $collection->add(array(
      'id' => 3,
      'type' => 'product'
    ));

    $collection = new FCollection('basket', array(), array('Product'));

    $this->assertEquals($collection->count(), 3);

  }

  public function testLoad()
  {
    $_SESSION['basket'] = array(
      0 => array(
        'id' => 1,
        'type' => 'product',
        'amount' => 2,
        'index' => 0,
        'params' => array()
      ),
      2 => array(
        'id' => 3,
        'type' => 'product',
        'amount' => 2,
        'index' => 2,
        'items' => array(
          'size' => '10',
          'color' => array(
            'id' => 4,
            'type' => 'product'
          ),
          'options' => array(
            array(
              'id' => 3,
              'type' => 'product'
            ),
            array(
              'id' => 2,
              'type' => 'product'
            ),
            array(
              'id' => 3,
              'type' => 'product'
            ),
          )
        )
      )
    );

    $productCollection = new FCollection('basket', array(), array('Product'), true);

    $this->assertEquals($productCollection->count(), 2);

    $element = $productCollection->getElementByIndex(2);

    $this->assertEquals($element->collectionItems['size'], '10');
    $this->assertEquals($element->collectionItems['color']->primaryKey, 4);
  }

  public function testChange()
  {
    $collection = new FCollection('basket', array('size'), array('Product'));

    $collection->add(array(
      'id' => 1,
      'amount' => 2,
      'type' => 'product',
      'items' => array('size' => 3)
    ));

    $collection->add(array(
      'id' => 1,
      'amount' => 1,
      'type' => 'product',
      'items' => array('color' => 'red', 'size' => 3)
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 1,
      'type' => 'product',
      'items' => array('color' => 'red', 'size' => 3)
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 1,
      'type' => 'product',
      'items' => array('size' => 10)
    ));

    $collection->add(array(
      'id' => 3,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    //$collection->createPathsRecursive();

    $this->assertEquals($collection->countAmount(), 7);

    $collection->change(0, 1, null);

    $this->assertEquals($collection->countAmount(), 5);
    $this->assertEquals($collection->count(), 4);

    $collection->change(2, null, array('size' => 3));
    $this->assertEquals($collection->countAmount(), 5);
    $this->assertEquals($collection->count(), 3);

    $this->assertEquals($collection->getElementByIndex(3)->collectionItems['options']->count(), 2);
    $this->assertEquals($collection->getElementByIndex(3)->collectionItems['options']->countAmount(), 3);

    $collection->change('basket[3][options][1]', 3);
    $this->assertEquals($collection->getElementByIndex(3)->collectionItems['options']->countAmount(), 5);
  }

  public function testCreatePathsRecursive()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $collection->add(array(
      'id' => 4,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'options' => array(
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 2,
            'type' => 'product'
          ),
        ),
        'sizes' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 4,
            'type' => 'product'
          )
        ),
        'size' => array(
          'id' => 1,
          'type' => 'product'
        )
      )
    ));

    $collection->createPathsRecursive();

    $this->assertEquals($collection->getElementByIndex(0)->collectionPath, array('basket', 0));
    $this->assertEquals($collection->getElementByIndex(1)->collectionItems['options']->getElementByIndex(0)->collectionPath, array('basket', 1, 'options', 0));
    $this->assertEquals($collection->getElementByIndex(2)->collectionItems['sizes']->getElementByIndex(1)->collectionPath, array('basket', 2, 'sizes', 1));
    $this->assertEquals($collection->getElementByIndex(2)->collectionItems['size']->collectionPath, array('basket', 2, 'size'));
  }

  public function testGetElementByExternalIndex()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $collection->add(array(
      'id' => 4,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'options' => array(
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 2,
            'type' => 'product'
          ),
        ),
        'sizes' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 4,
            'type' => 'product'
          )
        ),
        'size' => array(
          'id' => 1,
          'type' => 'product'
        )
      )
    ));

    $collection->createPathsRecursive();

    $element = $collection->getElementByExternalIndex('basket[1][options][0]');
    $this->assertEquals($element->primaryKey, 3);

    $element = $collection->getElementByExternalIndex('basket[2][size]');
    $this->assertEquals($element->primaryKey, 1);

    $element = $collection->getElementByExternalIndex('basket[2][sizes][1]');
    $this->assertEquals($element->primaryKey, 4);
  }

  public function testPathToString()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array(
        'size' => '10',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          array(
            'id' => 1,
            'type' => 'product'
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $collection->createPathsRecursive($collection);

    $this->assertEquals($collection->getElementByIndex(0)->collectionPath, array('basket', 0));
    $this->assertEquals($collection->getElementByIndex(1)->collectionItems['options']->getElementByIndex(0)->collectionPath, array('basket', 1, 'options', 0));
  }

  public function testClear()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 3,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 3,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'red')
    ));
    $this->assertEquals($collection->count(), 3);

    $collection->clear();

    $this->assertEquals($collection->count(), 0);
  }

  public function testFindElement()
  {
    $collection = new FCollection('basket', array('color'), array('Product'), false);

    $collection->add(array(
      'id' => 1,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 3,
      'amount' => 3,
      'type' => 'product'
    ));

    $collection->add(array(
      'id' => 2,
      'amount' => 2,
      'type' => 'product',
      'items' => array('color' => 'red')
    ));

    $element = $collection->findElement(array(
      'id' => 3,
      'type' => 'product'
    ));

    $this->assertEquals($element->id, 3);
  }
}