<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FCollection extends FAbstractCollection
{
  const COLLECTION_ITEMS_ROOT = 'root';

  /**
   * @var string
   */
  public $keyCollection;

  /**
   * @var bool
   */
  protected $useSession;

  /**
   * @var array
   */
  protected $compareByItemKeys;

  /**
   * @param string $keyCollection ключ колекции
   * @param array $compareByItemKeys объединять элементы с указаными одинаковыми параметрами
   * @param bool $useSession использовать сессию для хранения данных
   * @param FCollection|null $rootCollection родительская коллукция
   * пример:
   * $collection = new FCollection('basket', array('id', 'size'), true)
   */
  public function __construct($keyCollection, $compareByItemKeys = array(), $useSession = true, $rootCollection = null)
  {
    $this->keyCollection = $keyCollection;
    $this->compareByItemKeys = $compareByItemKeys;
    $this->useSession = $useSession;
    $this->rootCollection = $rootCollection ? $rootCollection : $this;

    if( $this->useSession )
      $this->load();

    $this->init();
  }

  /**
   * @param array|FCollectionElementBehavior $data
   * @param bool $restore
   *
   * @throws CHttpException
   * @return bool
   */
  public function add($data, $restore = false)
  {
    $addedIndex = $this->addInner($data);
    $this->merge($this->collectionTree[$addedIndex], false);

    if( !$restore )
      $this->update(true);
  }

  public function addInner($data)
  {
    if( empty($data) )
    {
      throw new CHttpException(500, 'Ошибка! Невозможно добавить пустой элемент в коллекцию.');
    }

    if( is_object($data) )
    {
      if( $data instanceof CModel && is_a($data->asa('collectionElement'), 'FCollectionElementBehavior') )
      {
        $data = $data->toArray();
      }
      else
      {
        throw new CHttpException(500, 'Ошибка! Невозможно добавить элемент '.get_class($data).' в коллекцию.');
      }
    }

    $object = new FCollectionElement($data, $this->compareByItemKeys, $this->rootCollection);

    if( !$object->validate() )
      throw new CHttpException(500, 'Ошибка валидации элемента type='.$object->type.' '.'id='.$object->primaryKey);

    return $this->attach($object);
  }

  public function clear()
  {
    $this->removeAll();
    $this->update();
  }

  public function remove($index)
  {
    unset($this[$index]);
    $this->update(true);
  }

  public function countAmount()
  {
    $amount = 0;

    foreach($this->collectionTree as $element)
    {
      $amount += $element->amount;
    }

    return $amount;
  }

  public function changeAmount($index, $amount = null)
  {
    if( $amount !== null )
    {
      $this->collectionList[$index]->amount = $amount;
      $this->update();
    }
  }

  public function changeItems($index, $newItems)
  {
    if( $element = $this->collectionList[$index] )
    {
      $element->items->collectionList = array();
      $element->items->collectionTree = array();
      $element->items->index = 0;
      $element->items = $element->buildItems($newItems, $this->root);
      $element->invalidate();

      $element->collectionParent->update(true);
      $this->update(true);
    }
  }

  public function isEmpty($collection = null)
  {
    if( $collection === null )
      $collection = $this;

    if( !isset($collection) || !($collection instanceof FCollection) )
      return true;

    return $collection->count() == 0 ? true : false;
  }

  /**
   * @param array|FCollectionElement|CActiveRecord $type
   * @param null $id
   *
   * @return bool
   */
  public function getIndex($type, $id = null)
  {
    if( isset($id) )
      $element = new FCollectionElement(array('id' => $id, 'type' => $type));
    else if( is_array($type) )
      $element = new FCollectionElement($type);
    else if( $type instanceof FCollectionElement )
      $element = $type;
    else if( $type instanceof CActiveRecord )
      $element = new FCollectionElement(array('id' => $type->primaryKey, 'type' => Utils::toSnakeCase(get_class($type))));

    foreach($this->collectionList as $collectionElement)
    {
      if( $collectionElement->compare($element) )
      {
        return $collectionElement->index;
      }
    }

    return null;
  }

  /**
   * @param array|FCollectionElement|CActiveRecord $type
   * @param null $id
   *
   * @return bool
   */
  public function isInCollection($type, $id = null)
  {
    return $this->getIndex($type, $id) !== null;
  }

  public function toArray()
  {
    return json_decode(json_encode($this->jsonSerialize()), true);
  }

  /**
   * @param string $listClass
   * @param CDbCriteria|null $criteria
   *
   * @return ProductList
   * @throws CException
   */
  public function getList($listClass = 'ProductList', CDbCriteria $criteria = null)
  {
    $allowedClass = preg_replace('/List$/', '', $listClass);
    $ids = array();

    foreach($this->toArray() as $item)
    {
      if( Utils::toCamelCase($item['type']) == $allowedClass )
        $ids[$item['id']] = $item['id'];
    }

    if( !$criteria )
      $criteria = new CDbCriteria();
    $criteria->addInCondition('t.id', $ids);

    if( $ids )
      $criteria->order = "FIND_IN_SET(t.id, '".implode(',', $ids)."')";

    return Yii::createComponent($listClass, $criteria, null, false);
  }

  protected function update($reindexing = false)
  {
    if( $reindexing )
    {
      $this->restore($this->toArray());
    }

    if( $this->useSession )
      $this->save();
  }

  protected function restore($data)
  {
    $this->removeAll();

    foreach($data as $itemData)
    {
      $this->add($itemData, true);
    }
  }

  protected function save()
  {
    Yii::app()->session[$this->keyCollection] = json_encode($this);
  }

  protected function load()
  {
    $data = isset(Yii::app()->session[$this->keyCollection]) ? json_decode(Yii::app()->session[$this->keyCollection], true) : array();

    $this->restore($data);
  }

  /**
   * @param FCollectionElement $element
   * @param bool $isNew
   *
   * @return int
   */
  protected function merge($element, $isNew = false)
  {
    foreach($this->collectionTree as $collectionElement)
    {
      if( $collectionElement->merge($element) )
      {
        $this->remove($element->index);

        return $collectionElement->index;
      }
    }

    if( $isNew )
    {
      return $this->attach($element);
    }
  }

  protected function init()
  {
  }
}