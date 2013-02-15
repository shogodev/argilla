<?php

Yii::import('backend.modules.tag.models.*');
Yii::import('backend.modules.tag.behaviors.*');
Yii::import('backend.modules.product.models.*');

/**
 * @author Nikita Melnikov <melnikov@shogocms.ru>
 * @date 24.09.2012
 * @package Tag
 */
class TagBehaviorTest extends CDbTestCase
{
  private $tables = array(
    'tag'                  => 'shogocms_tag',
    'product_assigned_tag' => 'shogocms_product_tag',
  );

  public $fixtures = array(
    'tag'     => 'Tag',
    'product' => 'BProduct'
  );

  protected function setUp()
  {
    parent::setUp();
  }

  public function tearDown()
  {
    $this->getFixtureManager()->truncateTable($this->tables['tag']);
  }

  public function testCreation()
  {
    $tag = new Tag();
    $tag->name = 'newTag';
    $this->assertTrue($tag->save());
  }

  public function testDelimiterChange()
  {
    $strings = array('first' => 'tag1, tag2, tag3', 'second' => 'tag1:tag2:tag3');

    $behavior = new TagBehavior();

    $pointArr = $behavior->parseString($strings['first']);

    $behavior->delimiter = ':';

    $colonArr = $behavior->parseString($strings['second']);

    $this->assertEquals($pointArr, $colonArr);
  }

  public function testUpdate()
  {
    $tags    = 'tag1, tag2, tag3, tag4';
    $tagsNew = ',';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->set($tags);

    $this->assertNotEmpty($product->tags->toArray());
    $this->assertNotEquals($tags, $tagsNew);

    $product->tags->update($tagsNew);

    $this->assertEquals($product->tags->parseString($tagsNew), $product->tags->toArray());
  }

  public function testAddTagsString()
  {
    $tags = 'tag1, tag2, tag3, tag4';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->set($tags);

    $this->assertEquals($product->tags->parseString($tags), $product->tags->toArray());
  }

  public function testToString()
  {
    $tags = '123:345:456';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->delimiter = ':';
    $product->tags->set($tags);

    $tagsArray = $product->tags->toArray();

    $this->assertEquals($product->tags->toString(), $product->tags->implode($tagsArray));
    $this->assertEquals($product->tags->toString(), $product->tags->__toString());
  }

  public function testToObject()
  {
    $tags = '123:345:456';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->delimiter = ':';
    $product->tags->set($tags);

    $this->assertEquals($product->tags->get(), $product->tags->toObject());
    $this->assertEquals($product->tags->tags, $product->tags->toObject());
  }

  public function testSetExisting()
  {
    $tags0 = '123:345:456';
    $tags1 = '123:457';
    $tags3 = '123:345:456:457';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->delimiter = ':';
    $product->tags->set($tags0);
    $product->tags->set($tags1);

    $this->assertEquals($product->tags->parseString($tags3), $product->tags->toarray());
  }

  public function testSetOneTag()
  {
    $tags    = 'tag1';

    $product = BProduct::model()->findByPk(1);
    $product->attachBehavior('tags', new TagBehavior());
    $product->tags->table = $this->tables['product_assigned_tag'];
    $product->tags->set($tags);

    $this->assertEquals($product->tags->toString(), $tags);
  }
}
