<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class RetailCrmDataManager
{
  public $orderPrefix = 'order-';

  public $callbackPrefix = 'call-';

  /**
   * @var string $idPrefix
   */
  public $idPrefix;

  public function getCallbackData(Callback $model)
  {
    $data = array();
    $data['call'] = true;
    $data['contragentType'] = 'individual';
    $data['orderMethod'] = 'callback';
    $data['status'] = 'new';
    $data['number'] = $this->idPrefix.$this->callbackPrefix.$model->id;
    $data['firstName'] = (!empty($model->name) ? $model->name : 'Имя не указанно');
    $data['phone'] = (!empty($model->phone)) ? ViewHelper::getClearPhone($model->phone) : '';
    $data['email'] = (!empty($model->email) ? $model->email : '');
    $data['customerComment'] = (!empty($model->time) ? 'Просьба перезвонить в '.$model->time.'.' : '').' '.$model->content;
    $data['items'] = array();

    return $data;
  }

  /**
   * @link http://www.retailcrm.ru/docs/%D0%A0%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%87%D0%B8%D0%BA%D0%B8/%D0%A1%D0%BF%D1%80%D0%B0%D0%B2%D0%BE%D1%87%D0%BD%D0%B8%D0%BA%D0%9C%D0%B5%D1%82%D0%BE%D0%B4%D0%BE%D0%B2APIV3
   * @param Order $model
   *
   * @return array
   */
  public function getOrderData(Order $model)
  {
    $model->refresh();

    $data = array();
    $data['call'] = false;
    $data['contragentType'] = 'individual';
    $data['orderMethod'] = $model->type == Order::TYPE_FAST ? 'one-click' : 'shopping-cart';
    $data['createdAt'] = $model->date_create;
    $data['status'] = 'new';
    $data['number'] = $this->idPrefix.$this->orderPrefix.$model->id;
    $data['firstName'] = !empty($model->name) ? $model->name : 'Имя не указанно';
    $data['phone'] = !empty($model->phone) ? ViewHelper::getClearPhone($model->phone) : '';
    $data['email'] = !empty($model->email) ? $model->email : '';
    $data['customerComment'] = !empty($model->comment) ? $model->comment : '';

    if( $model->type !== Order::TYPE_FAST  )
    {
      $data['delivery']['code'] = $this->getDeliveryCode($model);
      $data['delivery']['address']['text'] = !empty($model->address) ? $model->address : '';
      $data['paymentType'] = $this->getPaymentCode($model);
    }
    $data['items'] = $this->getProductsData($model->products);

    return $data;
  }

  /**
   * @param Order $model
   *
   * @throws ModelValidateException
   */
  public function updateOrderStatus($model)
  {
    try
    {
      $model->updateByPk($model->id, array('status_id' => OrderStatus::STATUS_CONFIRMED));
    }
    catch(Exception $e)
    {
      throw new ModelValidateException($model);
    }
  }

  /**
   * @param FActiveRecord $model
   * @param $id
   * @param $url
   *
   * @throws ModelValidateException
   */
  public function setRetailCrmUrl($model, $id, $url)
  {
    $retailCrmUrl = $url.'/orders/s2-'.mb_strtolower($id).'/edit';

    try
    {
      if( isset($model->getTableSchema()->columns['retail_crm_url']) )
        $model->updateByPk($model->primaryKey, array('retail_crm_url' => $retailCrmUrl));
    }
    catch(Exception $e)
    {
      throw new ModelValidateException($model);
    }
  }

  /**
   * @param OrderProduct[] $products
   *
   * @return array
   */
  private function getProductsData(array $products)
  {
    $data = array();

    $index = 0;
    foreach($products as $product)
    {
      $data[$index]['productId'] = $product->history->product_id;
      $data[$index]['initialPrice'] = round($product->price);
      $data[$index]['quantity'] = $product->count;
      $data[$index]['productName'] = $product->name;

      $data[$index]['properties'] = array();

      if( !empty($product->history->articul) )
      {
        $data[$index]['properties'][] = array(
          'code' => 'article',
          'name' => 'Артикул',
          'value' => $product->history->articul
        );
      }

      $this->setParameters($data[$index]['properties'], $product);
      $this->setOptions($data, $index, $product);

      $index++;
    }

    return $data;
  }

  private function setParameters(&$data, OrderProduct $product)
  {
    foreach($product->items as $option)
    {
      if( $option->type != 'ProductParameter' )
        continue;

      $parameter['name'] = $option->name;
      $parameter['value'] = $option->value;
      $parameter['code'] = $option->pk;

      $data[] = $parameter;
    }
  }

  private function setOptions(&$data, &$index, OrderProduct $product)
  {
    foreach($product->items as $option)
    {
      if( $option->type != 'ProductOption' )
        continue;

      $index++;

      $data[$index]['initialPrice'] = round($option->price);
      $data[$index]['quantity'] = $option->amount;
      $data[$index]['productName'] = $option->value.' (Опция)';
    }
  }

  private function getDeliveryCode(Order $model)
  {
    if( !($model->delivery instanceof OrderDelivery) )
      return '';

    $deliverCodeList = array(
      OrderDeliveryType::SELF_DELIVERY  => 'self-delivery',
      OrderDeliveryType::DELIVERY_MOSCOW => 'courier',
      OrderDeliveryType::DELIVERY_MOSCOW_REGION => 'courier',
      OrderDeliveryType::DELIVERY_REGION => 'russian-post'
    );

    return $deliverCodeList[$model->delivery->delivery_type_id];
  }

  private function getPaymentCode(Order $model)
  {
    if( !($model->payment instanceof OrderPayment) )
      return '';

    $paymentCodeList = array(
      OrderPaymentType::CASH => 'cash',
      OrderPaymentType::NON_CASH => 'bank-card',
      OrderPaymentType::E_PAY => 'e-money',
    );

    return $paymentCodeList[$model->payment->payment_type_id];
  }
}