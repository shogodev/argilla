<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FCollection extends SplObjectStorage
{
  public $keyCollection;

  public $externalIndexStorage = array();

  protected $compareByItemKeys;

  protected $index = 0;

  protected $autoSave;

  protected $arrayParentElements = array();

  /**
   * @param string $keyCollection ключ колекции
   * @param array $compareByItemKeys объединять элементы с указаными одинаковыми параметрами
   * @param array $allowedModels сприсок разрешенных моделей для которых нужно создать объект
   * @param bool $autoSave автоматисеская запись и чтение из сессии
   *
   * пример:
   * $collection = new FCollection('basket', array('id', 'size'), array('Product', 'Option'), true)
   */
  public function __construct($keyCollection, $compareByItemKeys = array(), $allowedModels = array(), $autoSave = true)
  {
    $this->keyCollection = $keyCollection;
    $this->allowedModels = $allowedModels;

    $this->compareByItemKeys = $compareByItemKeys;
    $this->autoSave = $autoSave;

    if( $this->autoSave )
      $this->load();

    $this->init();
  }

  public function init()
  {

  }

  public function add($collectionData)
  {
    if( empty($collectionData) )
      throw new CHttpException(500, 'Ошибка! Невозможно добавить пустой элемент в коллекцию.');

    $object = $this->createObject($collectionData);

    $this->merge($object, true);

    if( $this->autoSave )
       $this->save();

    return true;
  }

  public function save()
  {
    Yii::app()->session[$this->keyCollection] = $this->toArray();

    $this->createPathsRecursive();
  }

  public function load()
  {
    $collectionData = isset(Yii::app()->session[$this->keyCollection]) ? Yii::app()->session[$this->keyCollection] : array();

    foreach($collectionData as $data)
    {
      $this->restoreIndex($data['index']);
      $this->add($data);
    }

    $this->createPathsRecursive();
  }

  public function remove($index)
  {
    $element = $this->getElementByIndex($index);
    $element->parentCollection->detach($element);

    unset($this->externalIndexStorage[$element->collectionExternalIndex]);

    if( $this->autoSave )
    {
      $this->save();
      $this->update();
    }
  }

  public function clear()
  {
    $this->removeAll($this);

    if( $this->autoSave )
      $this->save();
  }

  public function update()
  {
    $this->removeAll($this);
    $this->load();
  }

  public function countAmount()
  {
    $amount = 0;

    foreach($this as $element)
      $amount += $element->collectionAmount;

    return $amount;
  }

  public function changeAmount($index, $amount = null)
  {
    $element = $this->getElementByIndex($index);

    if( $amount !== null )
      $element->collectionAmount = $amount;

    $this->merge($element);

    if( $this->autoSave )
      $this->save();
  }

  public function changeItems($index, $items)
  {
    $element = $this->getElementByIndex($index);

    if( $items !== null )
      $element->collectionItems = $this->createObjectsItems($items);

    $this->merge($element);

    if( $this->autoSave )
      $this->save();
  }

  public function changeItemsPartial($index, $items)
  {
    $element = $this->getElementByIndex($index);

    if( $items === null )
      return;

    $itemsArray = $element->collectionItemsToArray();

    $element->collectionItems = $this->createObjectsItems(Arr::mergeAssoc($itemsArray, $items));

    $this->merge($element);

    if( $this->autoSave )
      $this->save();
  }

  public function isEmpty($collection = null)
  {
    if( $collection === null )
      $collection = $this;

    if( !isset($collection) || !($collection instanceof FCollection) )
      return true;

    return $collection->count() == 0 ? true : false;
  }

  public function isInCollectionData($type, $id)
  {
    return in_array($this->pathToString(array(Utils::toSnakeCase($type), $id)), $this->arrayParentElements);
  }

  public function isInCollectionClass($class)
  {
    $data = $this->convertClassToArray($class);

    return $this->isInCollectionData(Arr::get($data, 'type'), Arr::get($data, 'id'));
  }

  public function convertClassToArray($class)
  {
    return array('type' => Utils::toSnakeCase(get_class($class)), 'id' => $class->primaryKey);
  }

  public function convertCollectionItemsToArray($items)
  {
    $arrayItems = array();
    foreach($items as $key => $item)
    {
      if( empty($item) )
        continue;

      if( is_object($item) )
        $arrayItems[$key] = $this->convertClassToArray($item);
      else
        $arrayItems[$key] = $item;
    }

    return $arrayItems;
  }

  public function createPathsRecursive($collection = null, $path = array(), $rootCollection = null)
  {
    if( empty($path) )
      $this->externalIndexStorage = array();

    if( $rootCollection === null )
      $rootCollection = $this;

    if( $collection === null )
      $collection = $this;

    foreach($collection as $element)
    {
      if( $rootCollection == $collection )
        $this->arrayParentElements[$element->collectionIndex] = $this->pathToString(array(Utils::toSnakeCase(get_class($element)), $element->id));

      $currentPath = CMap::mergeArray($path, array($collection->keyCollection, $element->collectionIndex));
      $element->indexationElement($currentPath, $rootCollection);

      if( empty($element->collectionItems) )
        continue;

      foreach($element->collectionItems as $key => $item)
      {
        if( is_object($item) )
        {
          if( $item instanceof FCollection )
            $this->createPathsRecursive($item, $currentPath, $rootCollection);
          else
            $item->indexationElement(CMap::mergeArray($currentPath, array($key)), $rootCollection);
        }
      }
    }
  }

  public function pathToString(array $path)
  {
    $pathString = Arr::cut($path, 0);
    $pathString .= '['.implode('][', $path).']';

    return $pathString;
  }

  public function getElementByExternalIndex($index)
  {
    return isset($this->externalIndexStorage[$index]) ? $this->externalIndexStorage[$index] : null;
  }

  public function toArray($items = null)
  {
    if( $items === null )
      $items = $this;

    if( empty($items) )
      return array();

    $data = array();

    foreach($items as $key => $item)
    {
      if( is_object($item) )
      {
        if( $item instanceof FCollection )
          $data[$key] = $this->toArray($item);
        else
          $data[$item->collectionIndex !== null ? $item->collectionIndex : $key] = $item->toArray();
      }
      else
        $data[$key] = $item;
    }

    return $data;
  }

  /**
   * Ищет элементы не принадлежащие текущей коллекции в коллекции.
   * Если входной параметр элемент текущей коллекции, то вернет null
   * @param $element объект или массив
   * @param array $compareByItemKeys масив ключей items которые нужно сравнить
   * @return FCollectionElement
   */
  public function findElement($element, $compareByItemKeys = array())
  {
    if( !is_object($element) )
      $element = $this->createObject($element);

    foreach($this as $item)
    {
      if( $item->collectionIndex === $element->collectionIndex )
        continue;

      if( get_class($item) == get_class($element) && $item->primaryKey == $element->primaryKey && $this->compareItems($item, $element, $compareByItemKeys) )
        return $item;
    }

    return null;
  }

  /**
   * @return FCollectionElement
   */
  public function firstElement()
  {
    $this->rewind();
    return $this->current();
  }

  protected function incrementIndex()
  {
    return $this->index++;
  }

  protected function restoreIndex($index)
  {
    $this->index = $index > $this->index ? $index : $this->index;
    $this->incrementIndex();
  }

  /**
   * @param $index
   * @return FCollectionElement|null
   */
  public function getElementByIndex($index)
  {
    if( is_numeric($index) )
    {
      foreach($this as $element)
        if($element->collectionIndex == $index)
          return $element;
    }
    else
      return $this->getElementByExternalIndex($index);

    return null;
  }

  protected function createObject($data, $innerCollectionKey = null)
  {
    if( $this->isObject($data) )
    {
      $className = Utils::toCamelCase($data['type']);
      /**
       * @var FActiveRecord|FCollectionElement $model
       */
      $model = $className::model()->findByPk($data['id']);

      if( !$model )
        throw new CHttpException('500', 'Ошибка! Не удалось создать модель.');

      if( !$model->asa('collectionElement') )
        throw new CHttpException('500', 'Ошибка! Не найдено поведение.');

      $model->collectionAmount = Arr::cut($data, 'amount', 1);
      $model->collectionIndex = Arr::cut($data, 'index', null);
      $model->parentCollection = $this;

      if( isset($data['items']) && is_array($data['items']) )
        $model->collectionItems = $this->createObjectsItems($data['items']);

      $model->afterCreateCollection();

      return $model;
    }
    else if( is_array($data) && $this->isObject(reset($data)) )
    {
      $collection = new FCollection($innerCollectionKey, $this->compareByItemKeys, $this->allowedModels, false);

      foreach($data as $value)
        $collection->add($value);

      return $collection;
    }
    else
      return $data;
  }

  protected function createObjectsItems($items)
  {
    $objects = array();

    foreach($items as $key => $item)
      $objects[$key] = $this->createObject($item, $key);

    return $objects;
  }

  protected function isObject($data)
  {
    if( !is_array($data) )
      return false;

    if( !isset($data['id']) || !isset($data['type']) )
      return false;

    if( !in_array(Utils::toCamelCase($data['type']), $this->allowedModels) )
      return false;

    return true;
  }

  protected function merge($newElement, $addNewElement = false)
  {
    $collectionElement = $this->findElement($newElement, $this->compareByItemKeys);

    if( $collectionElement )
    {
      $collectionElement->collectionAmount += $newElement->collectionAmount;

      if( !$addNewElement )
         $this->detach($newElement);
    }
    else if( $addNewElement )
      $this->attachElement($newElement);
  }

  protected function attachElement($element)
  {
    if( $element->collectionIndex === null )
      $element->collectionIndex = $this->incrementIndex();

    $this->attach($element);
  }

  /**
   * @param FCollectionElement $element1
   * @param FCollectionElement $element2
   * @param array $compareByItemKeys
   * @return bool
   */
  protected function compareItems($element1, $element2, $compareByItemKeys)
  {
    if( empty($compareByItemKeys) )
      return true;

    if( empty($element1->collectionItems) && empty($element2->collectionItems) )
      return true;

    $arrayItems1 = $element1->collectionItemsToArray();
    $arrayItems2 = $element2->collectionItemsToArray();

    foreach($compareByItemKeys as $key)
    {
      if( !$this->compareItemsArray($arrayItems1, $arrayItems2, $key) )
        return false;
    }

    return true;
  }

  protected function compareItemsArray($items1, $items2, $itemName)
  {
    if( empty($items1[$itemName]) && empty($items2[$itemName]) )
      return true;

    if( !isset($items1[$itemName]) && !isset($items2[$itemName]) )
      return true;

    if( isset($items1[$itemName]) && !isset($items2[$itemName]) )
      return false;

    if( !isset($items1[$itemName]) && isset($items2[$itemName]) )
      return false;

    $dataForCompare1 = $this->preparationDataForCompare($items1[$itemName]);
    $dataForCompare2 = $this->preparationDataForCompare($items2[$itemName]);

    return $dataForCompare1 == $dataForCompare2;
  }

  protected function preparationDataForCompare($data)
  {
    if( !is_array($data) )
      return $data;

    $ids = array();
    foreach($data as $key => $item)
    {
      if( $this->isObject($item) )
      {
        $uniqueKey = $item['id'].'_'.$item['amount'];
        $ids[$uniqueKey] = $uniqueKey;
      }
      else
      {
        if( is_array($item) )
          asort($item);

        $ids[$key] = $item;
      }
    }

    ksort($ids);
    return $ids;
  }
}