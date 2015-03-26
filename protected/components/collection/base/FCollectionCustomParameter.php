<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FCollectionCustomParameter extends CComponent
{
  protected $primaryKey;

  protected $itemType;

  protected $itemValue;

  protected $itemName;

  public function __construct($name, $value, $type = 'customParameter', $pk = null)
  {
    $this->itemName = $name;
    $this->itemValue = $value;
    $this->itemType = $type;
    $this->primaryKey = $pk;

    $this->attachBehavior('collectionElement', array('class' => 'FCollectionElementBehavior'));
  }

  public function getOrderItemType()
  {
    return $this->itemType;
  }

  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }

  public function getOrderItemName()
  {
    return $this->itemName;
  }

  public function getOrderItemValue()
  {
    return $this->itemValue;
  }
} 