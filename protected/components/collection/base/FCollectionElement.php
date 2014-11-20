<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 * @property string $key
 * @property string $type
 * @property string $primaryKey
 */
class FCollectionElement extends CComponent implements JsonSerializable
{
  const CUSTOM = 'customElement';

  const COLLECTION = 'innerCollection';

  public $amount;

  public $items;

  public $index;

  /**
   * @var FCollection
   */
  public $collectionParent;

  private $key;

  private $primaryKey;

  private $type;

  private $object;

  private $compareByItemKeys;

  public function __construct($data, $compareByItemKeys = array(), $rootCollection = null)
  {
    $this->type = $data['type'];
    $this->primaryKey = $data['id'];

    if( isset($data['key']) )
    {
      $this->key = $data['key'];
    }

    $this->amount = isset($data['amount']) ? intval($data['amount']) : 1;

    if( isset($data['items']) )
      $this->items = $this->buildItems($data['items'], $rootCollection);

    $this->compareByItemKeys = $compareByItemKeys;
  }

  public function validate()
  {
    if( in_array($this->type, array(self::COLLECTION, self::CUSTOM)) )
      return true;

    if( $this->createObject() )
      return true;

    return false;
  }

  /**
   * @param FCollectionElement $element
   *
   * @return bool
   */
  public function merge($element)
  {
    if( $element->index === $this->index )
      return false;

    if( $this->compare($element) && $this->compareItems($this->items, $element->items, $this->compareByItemKeys) )
    {
      $this->amount += $element->amount;
      $this->invalidate();

      return true;
    }

    return false;
  }

  /**
   * @param FCollectionElement $element
   *
   * @return bool
   */
  public function compare($element)
  {
    if( $this->primaryKey == $element->primaryKey && $this->type == $element->type )
    {
      return true;
    }

    return false;
  }

  public function invalidate()
  {
    $this->object = null;
  }

  public function getObject()
  {
    if( is_null($this->object) )
    {
      if( $this->type == self::CUSTOM )
      {
        $this->object = $this->primaryKey;
      }
      else if( $this->type == self::COLLECTION )
      {
        $this->object = $this->items;
      }
      else
      {
        $this->object = $this->createObject();
      }
    }

    return $this->object;
  }

  public function jsonSerialize()
  {
    if( in_array($this->type, array(self::CUSTOM, self::COLLECTION)) )
    {
      $data = $this->getObject();
    }
    else
    {
      $data = array(
        'id' => $this->primaryKey,
        'type' => $this->type,
        'amount' => $this->amount,
      );

      if( !is_null($this->items) )
        $data['items'] = $this->items;
    }

    return $data;
  }

  public function buildItems($items, $rootCollection)
  {
    $preparedItems = $this->getPreparedItems($items, $rootCollection);

    $collectionItems = new FCollection('items', $this->compareByItemKeys, false, $rootCollection);

    foreach($preparedItems as $item)
    {
      $collectionItems->addInner($item);
    }

    return $collectionItems;
  }

  public function toArray()
  {
    return json_decode(json_encode($this->jsonSerialize()), true);
  }

  protected function getType()
  {
    return $this->type;
  }

  protected function getKey()
  {
    return $this->key;
  }

  protected function getPrimaryKey()
  {
    return $this->primaryKey;
  }

  private function getPreparedItems($items, $rootCollection)
  {
    $preparedItems = array();

    foreach($items as $key => $item)
    {
      if( $this->isObject($item) )
      {
        $preparedItems[$key] = $item;

        if( !is_numeric($key) )
          $preparedItems[$key]['key'] = $key;

        if( isset($item['items']) )
          $item['items'] = $this->buildItems($item['items'], $rootCollection);
      }
      else if( is_array($item) && $this->isObject(reset($item)) )
      {
        $preparedItems[$key] = array(
          'id' => $key,
          'key' => $key,
          'type' => self::COLLECTION,
          'items' => $item
        );
      }
      else
      {
        $preparedItems[$key] = array(
          'id' => $item,
          'key' => $key,
          'type' => self::CUSTOM
        );
      }
    }

    return $preparedItems;
  }

  private function createObject()
  {
    $className = Utils::toCamelCase($this->type);

    /**
     * @var FActiveRecord|FCollectionElementBehavior $model
     */
    $modelClass = $className::model();

    if( !($modelClass instanceof CModel) )
      throw new CHttpException('500', 'Ошибка! Не удалось создать модель.');

    if( !$modelClass->asa('collectionElement') )
      throw new CHttpException('500', 'Ошибка! Не найдено поведение.');

    $model = $modelClass->findByPk($this->primaryKey);

    if( !$model )
      return null;

    $model->setCollectionElement($this);
    $model->afterCreateCollection();

    return $model;
  }

  private function isObject($data)
  {
    if( !is_array($data) )
      return false;

    if( !isset($data['id']) || !isset($data['type']) )
      return false;

    return true;
  }

  /**
   * @param $collectionItems1
   * @param $collectionItems2
   * @param array $compareByItemKeys
   *
   * @return bool
   */
  private function compareItems($collectionItems1, $collectionItems2, $compareByItemKeys)
  {
    if( empty($compareByItemKeys) )
      return true;

    if( empty($collectionItems1) && empty($collectionItems2) )
      return true;

    foreach($compareByItemKeys as $key)
    {
      if( !$this->compareItemsArray($collectionItems1, $collectionItems2, $key) )
        return false;
    }

    return true;
  }

  private function compareItemsArray($items1, $items2, $itemName)
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

  private function preparationDataForCompare($data)
  {
    if( $data instanceof FCollection  )
      $data = $data;

    if( is_object($data)  )
      $data = $data->toArray();

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