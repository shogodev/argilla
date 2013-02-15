<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 02.10.12
 * @package Compare
 *
 * Для подключения сравнения необходимо добавить в конфигурационный файл инициализациюю класса
 * в массив components
 * @example
 * <code>
 *  'compare' => array(
 *    'class' => 'application.models.ProductCompare',
 *  ),
 * </code>
 *
 * @HINT За свойство, по которому будет производиться сортировка отвечает константа ORDER_BY.
 *
 * Чтобы назначить ID записей свойтсва для сортировки используется $order
 * @example
 * <code>
 *  public $order = array(
 *    array(ID, ID, ID, ID),
 *    array(ID, ID, ID),
 *    ...
 *  );
 * </code>
 *
 * Для вывода сортированного сравнения используется $sortedProducts
 * @example
 * <code>
 *  Yii::app()->compare->sortedProducts
 * </code>
 *
 * Общее количество продуктов храниться в $count
 * @example
 * <code>
 *  Yii::app()->compare->count
 * </code>
 *
 * Несортированный массив продуктов храниться в переменной $products
 * @example
 * <code>
 *  Yii::app()->compare->products
 * </code>
 */
class ProductCompare
{
  const ORDER_BY = 'type';

  public $limit = 3;

  /**
   * Сортировка продуктов по типу для отображения в сравнении
   * Во вложенных массивах используются ID записей параметра продукта
   *
   * @var array
   */
  public $order = array(
    array(),
  );

  /**
   * Название переменной сравнения продуктов в сессии
   *
   * @var string
   */
  public $sessionVar = 'compare';

  /**
   * Общее количество продуктов
   *
   * @var string
   */
  public $count    = 0;

  /**
   * ID продуктов
   *
   * @var array of int
   */
  public $ids      = array();

  /**
   * Массив объектов продуктов
   *
   * @var array of Product
   */
  public $products = array();

  /**
   * Массив с сортированными по $order продуктами
   *
   * @var array of array of Product
   */
  public $sortedProducts = array(array());

  /**
   * Инициализация класса
   *
   * @return void
   */
  public function init()
  {
    $this->initOrder();
    $this->ids = Yii::app()->session[$this->sessionVar] ? : array();

    $this->getProducts();
    $this->getCount();
    $this->getOrder();
  }

  /**
   * Получение всех продуктов по их ID в сессии
   *
   * @return array of Product
   */
  public function getProducts()
  {
    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.id', $this->ids);

    $productList = new ProductList($criteria, null, false);
    $productList->fetchProductParameters = true;

    return $this->products = $productList->getProducts()->getData();
  }

  /**
   * Сортировка продуктов по выбранному параметру для сравнения
   *
   * @return array
   */
  public function getOrder()
  {
    for($i = 0; $i < $this->limit; $i++)
    {
      $id = Arr::get($this->ids, $i);
      $this->sortedProducts[$i] = array();

      foreach($this->products as $product)
      {
        if( $product->id == $id )
          $this->sortedProducts[$i] = $product;
      }
    }

    return $this->sortedProducts;
  }

  public function getParameters()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product', '=1');
    $parameters = ProductParam::model()->getParameters($criteria);

    foreach($parameters as $i => $parameter)
    {
      $notEmpty = false;
      $diff     = array();

      foreach($this->products as $product)
      {
        if( !empty($product->parameters[$parameter->id]->value) )
        {
          $notEmpty = true;
          $diff[] = $product->parameters[$parameter->id]->value;
        }
      }

      if( !$notEmpty )
        unset($parameters[$i]);

      $parameter->different = $this->getDifferent($diff);
    }

    return $parameters;
  }

  /**
   * Получение общей количества продуктов
   *
   * @return int
   */
  public function getCount()
  {
    return $this->count = count($this->products);
  }

  /**
   * Добавление в сравнение продукта с ID $id
   *
   * @param $id
   * @param null $place
   */
  public function add($id, $place = null)
  {
    if( $place !== null )
      $this->ids[$place] = $id;
    else
      $this->ids[count($this->ids)] = $id;

    $this->ids = count($this->ids) > $this->limit ? array_slice($this->ids, 1, $this->limit) : $this->ids;
    $this->editSession();
  }

  /**
   * Удаление из сравнения продукта
   *
   * @param int $id
   *
   * @return void
   */
  public function remove($id)
  {
    foreach($this->ids as $key => $value)
      if( $id == $value )
        unset($this->ids[$key]);

    $this->editSession();
  }

  /**
   * Удаление всех продуктов из сравнения
   *
   * @return void
   */
  public function clear()
  {
    $this->ids = array();
    $this->editSession();
  }

  /**
   * Удаление товаров из сгруппированного массива ID продуктов
   *
   * @param int $id
   *
   * @return void
   */
  public function clearGroup($id)
  {
    if( isset($this->sortedProducts[$id]) )
    {
      $products = $this->sortedProducts[$id];

      foreach( $products as $product )
      {
        unset($this->ids[$product->id]);
      }

      $this->editSession();
    }
  }

  public function isInCompare($id)
  {
    foreach($this->ids as $key => $value)
      if( $id == $value )
        return true;

    return false;
  }

  public function responseSuccess($data = array())
  {
    $response = array('status' => 'ok');

    $response['updateElements'] = array(
      'compare_count'     => $this->getCount(),
      'compare_count_top' => $this->getCount(),
    );

    echo json_encode(array_merge($response, $data));
    Yii::app()->end();
  }

  protected function getDifferent($diff)
  {
    return false;
    /*if( count($diff) < 2 )
      return false;
    else if( count($diff) != count($this->ids) )
      return count($diff) === count(array_unique($diff));*/
  }

  protected function initOrder()
  {
    $this->order = array();
  }

  /**
   * Обновление сессии
   *
   * @return void
   */
  protected function editSession()
  {
    Yii::app()->session[$this->sessionVar] = $this->ids;
    $this->init();
  }
}