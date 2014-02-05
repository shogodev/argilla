<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.compontnts
 */
Yii::import('frontend.tests.components.collection.*');

class FCollectionElementTest extends CTestCase
{
  public function testGetSum()
  {
    $element = new TestCollectionElement();
    $element->attachBehaviors($element->behaviors());
    $element->price = 3020.40;
    $element->collectionAmount = 3;

    $this->assertEquals($element->sum, 9061.2);
  }

  public function testToArray()
  {
    $element = new TestCollectionElement();
    $element->attachBehaviors($element->behaviors());

    $element->collectionIndex = 2;
    $element->collectionAmount = 5;
    $element->collectionItems = array('size' => '10');

    $this->assertEquals($element->toArray(), array(
      'id' => 1,
      'type' => 'test_collection_element',
      'amount' => 5,
      'index' => 2,
      'items' => array('size' => '10')
    ));


    $collection = new FCollection('test', array('size'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product',
      'amount' => 3,
      'items' => array(
        'size' => '15',
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

    $this->assertEquals($collection->getElementByIndex(0)->toArray(), array(
      'id' => '1',
      'type' => 'product',
      'amount' => 3,
      'index' => 0,
      'items' => array(
        'size' => '15',
        'options' => array(
          0 => array(
            'id' => '3',
            'type' => 'product',
            'amount' => 2,
            'index' => 0,
            'items' => array()
          ),
          1 => array(
            'id' => '1',
            'type' => 'product',
            'amount' => 1,
            'index' => 1,
            'items' => array()
          )
        )
      )
    ));
  }

  public function testItemsToArray()
  {
    $element = new TestCollectionElement();
    $element->attachBehaviors($element->behaviors());

    $element->collectionIndex = 2;
    $element->collectionAmount = 5;
    $element->collectionItems = array(
      'size' => '40',
      'height' => 15
    );

    $this->assertEquals($element->collectionItemsToArray(), array(
      'size' => '40',
      'height' => '15'
    ));

    $collection = new FCollection('test', array('size'), array('Product'), true);
    $collection->add(array(
      'id' => 1,
      'type' => 'product',
      'amount' => 3,
      'items' => array(
        'size' => '15',
        'options' => array(
          array(
            'id' => 3,
            'type' => 'product'
          ),
          'sub_options' => array(
            'id' => 1,
            'type' => 'product',
            'items' => array(
              'data' => array(
                'test1' => 1,
                'test2' => 2,
              )
            )
          ),
          array(
            'id' => 3,
            'type' => 'product'
          ),
        )
      )
    ));

    $this->assertEquals($collection->getElementByIndex(0)->collectionItemsToArray(), array(
        'size' => '15',
        'options' => array(
          0 => array(
            'id' => '3',
            'type' => 'product',
            'amount' => 2,
            'index' => 0,
            'items' => array()
          ),
          1 => array(
            'id' => '1',
            'type' => 'product',
            'amount' => 1,
            'index' => 1,
            'items' => array(
              'data' => array(
                'test1' => 1,
                'test2' => 2,
              )
            )
          )
        )
      )
    );
  }

  public function testCollectionItemsListData()
  {
    $collection = new FCollection('test', array('size'), array('Product'), false);
    $collection->add(array(
      'id' => 1,
      'type' => 'product',
      'amount' => 3,
      'items' => array(
        'size' => '15',
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

    $this->assertEquals($collection->getElementByIndex(0)->collectionItemsListData('options', 'collectionIndex', 'id'), array(
      0 => 3,
      1 => 1
    ));
  }
}