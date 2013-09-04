<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FCollection extends SplObjectStorage
{
  public $keyCollection;

  public $externalIndexStorage = array();

  protected $mergeByKey;

  protected $index = 0;

  protected $autoSave;

  protected $arrayParentElements = array();
  /**
   * @param $keyCollection ключ колекции
   * @param array $mergeByKey объединять элементы с указаными одинаковыми параметрами
   * @param array $allowedModels сприсок разрешенных моделей для которых нужно создать объект
   * @param bool $autoSave автоматисеская запись и чтение из сессии
   *
   * пример:
   * $collection = new FCollection('basket', array('id', 'size'), array('Product', 'Option'), true)
   */
  public function __construct($keyCollection, $mergeByKey = array(), $allowedModels = array(), $autoSave = true)
  {
    $this->keyCollection = $keyCollection;
    $this->allowedModels = $allowedModels;

    $this->mergeByKey = $mergeByKey;
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

  public function change($index, $amount = null, $items = null)
  {
    $element = $this->getElementByIndex($index);

    if( $amount !== null )
      $element->collectionAmount = $amount;

    if( $items !== null )
      $element->collectionItems = $this->createObjectsItems($items);

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
    return in_array($this->pathToString(array($type, $id)), $this->arrayParentElements);
  }

  public function isInCollectionClass($class)
  {
    return $this->isInCollectionData(get_class($class), $class->primaryKey);
  }

  //todo: если при создании колекции $autoSave всегда true createPathsRecursive можно сделать приватным
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
        $data[$item->collectionIndex] = $item->toArray();
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
   * @return mixed
   */
  public function findElement($element)
  {
    if( !is_object($element) )
      $element = $this->createObject($element);

    foreach($this as $item)
    {
      if( $item->collectionIndex === $element->collectionIndex )
        continue;

      //if( $item->parentCollection == $element->parentCollection  && $item->primaryKey == $element->primaryKey && $this->compareItems($item, $element) )
      if( get_class($item) == get_class($element) && $item->primaryKey == $element->primaryKey && $this->compareItems($item, $element) )
        return $item;
    }

    return null;
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
       * @var FActiveRecord $model
       */
      $model = $className::model()->findByPk($data['id']);

      if( !$model || !$model->asa('collectionElement') )
        throw new CHttpException('500', 'Ошибка!');

      $model->collectionAmount = Arr::cut($data, 'amount', 1);
      $model->collectionIndex = Arr::cut($data, 'index', null);
      $model->parentCollection = $this;

      if( isset($data['items']) && is_array($data['items']) )
        $model->collectionItems = $this->createObjectsItems($data['items']);

      return $model;
    }
    else if( is_array($data) && $this->isObject(reset($data)) )
    {
      $collection = new FCollection($innerCollectionKey, $this->mergeByKey, $this->allowedModels, false);

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
    $collectionElement = $this->findElement($newElement);

    if( $collectionElement )
    {
      $collectionElement->collectionAmount += $newElement->collectionAmount;

      if( !$addNewElement )
         $this->detach($newElement);
    }
    else if( $addNewElement )
      $this->attachElement($newElement);
  }

  /**
   * Сравнивает содержимое collectionItems 2-х элементов колеуции на совпадение mergeByKey.
   * Не будет работать если в collectionItems по ключу из mergeByKey окажется массив.
   * @param $element1
   * @param $element2
   * @return bool
   */
  protected function compareItems($element1, $element2)
  {
    if( empty($this->mergeByKey) )
      return true;

    if( empty($element1->collectionItems) && empty($element2->collectionItems) )
      return true;

    //todo: Сделать проверку вложенных массивов, тогда можно будет сравнивать элемнты содержащие в collectionItems объекты
    $items1 = $this->toArray($element1->collectionItems);
    $items2 = $this->toArray($element2->collectionItems);

    $mergeItems1 = array();
    $mergeItems2 = array();

    foreach($this->mergeByKey as $key)
    {
      if( isset($items1[$key]) )
        $mergeItems1[$key] = $items1[$key];

      if( isset($items2[$key]) )
        $mergeItems2[$key] = $items2[$key];
    }

    $diff = array_diff_assoc($mergeItems1, $mergeItems2);

    if( empty($diff) )
      return true;

    return false;
  }

  protected function attachElement($element)
  {
    if( $element->collectionIndex === null )
      $element->collectionIndex = $this->incrementIndex();

    $this->attach($element);
  }
}