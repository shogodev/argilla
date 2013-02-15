<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * @property string $name
 */
class FBasket extends FProductsCollection
{
  protected $collectionKey = 'basket';

  protected $options = array();

  public $name = 'Корзина';

  public function getButtonAdd($productModel, $htmlOptions=array(), $text = '', $basketParameters = array())
  {
    return CHtml::ajaxLink(
      $text,
      $this->getBasketAddUrl(),
      array(
        'type'       => 'POST',
        'data'       => $this->dataForSend($productModel->id, $basketParameters),
        'dataType'   => 'json',
        'beforeSend' => '$.mouseLoader(true)',
        'success'    => "function(resp){checkResponse(resp);}",
        'error'      => 'function(resp){alert("Ошибка!")}',
        ),
      $htmlOptions);
  }

  public function getButtonDelete($productModel, $htmlOptions=array(), $text = '')
  {
    return CHtml::ajaxLink(
      $text,
      $this->getBasketDeleteUrl(),
      array(
        'type'       => 'POST',
        'data'       => array($this->collectionKey => array('collectionIndex' => $productModel->collectionIndex)),
        'dataType'   => 'json',
        'beforeSend' => 'function(){if(confirm("Действительно хотите удалить \"'.CHtml::encode($productModel->name).'\"")){$.mouseLoader(true)} else return false;}',
        'success'    => 'function(resp){checkResponse(resp)}',
        'error'      => 'function(resp){alert("Ошибка!")}',
        ),
      $htmlOptions);
  }

  public function getButtonFastOrder($productModel, $htmlOptions=array(), $text = '')
  {
    $htmlOptions['id']    = 'fast_order_'.$productModel->id;
    $htmlOptions['class'] = !empty($htmlOptions['class']) ? $htmlOptions['class'].' fast_order'  : 'fast_order';

    return CHtml::link($text, '#', $htmlOptions);
  }

  public function getElementId($name)
  {
    $separator = '_';

    $data = array();
    $data[] = $this->collectionKey;

    if( is_array($name) )
      $data = array_merge($data, $name);
    else
      $data[] = $name;

    return implode($separator, $data);
  }

  public function createElementText($name, $value, $htmlOptions = array())
  {
    $htmlOptions['class'] = isset($htmlOptions['class']) ? $this->getElementId($name).' '.$htmlOptions['class'] : $this->getElementId($name);

    return CHtml::tag('span', $htmlOptions, $value);
  }

  public function createElementInput($name, $value, $htmlOptions = array())
  {
    $id   = $this->getElementId($name);
    $ajax = array('type'       => 'POST',
                  'url'        => $this->getBasketChangeCountUrl(),
                  'data'       => new CJavaScriptExpression('jQuery(this).serialize()'),
                  'dataType'   => 'json',
                  'beforeSend' => '$.mouseLoader(true)',
                  'success'    => 'function(resp){checkResponse(resp)}',
                  'error'      => 'function(resp){alert("Ошибка!")}',
                 );

    $handler = CHtml::ajax($ajax);

    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');
    $cs->registerScript('Yii.CHtml.#'.$id, "$('body').on('change','#$id',function(){{$handler}});");

    return CHtml::textField($this->getElementId($name), $value, $htmlOptions);
  }

  public function getBasketUrl()
  {
    return Yii::app()->controller->createUrl('basket/index');
  }

  public function getBasketAddUrl()
  {
    return Yii::app()->controller->createUrl('basket/add');
  }

  public function getBasketDeleteUrl()
  {
    return Yii::app()->controller->createUrl('basket/delete');
  }

  public function getBasketChangeCountUrl()
  {
    return Yii::app()->controller->createUrl('basket/changeCount');
  }

  public function getSum()
  {
    $sum = 0;

    foreach($this->getProducts() as $product)
      $sum += $product->sum;

    if( !empty($sum) )
      $sum += $this->getDeliverySum();

    return $sum;
  }

  public function getCount()
  {
    $count = 0;
    $data  = $this->getSessionData();

    foreach($data as $value)
      $count += isset($value['count']) ? $value['count'] : 1;

    return $count;
  }

  public function responseSuccess($data = array())
  {
    $response = array('status' => 'ok');

    $response['updateElements'] = array(
      $this->getElementId('count')    => $this->getCount(),
      $this->getElementId('sum')      => Yii::app()->format->formatNumber($this->getSum()),
    );

    if( count($this->getSessionData()) == 0 )
    {
      $response['hideElements'][] = $this->getElementId('checkout_url');
      $response['hideElements'][] = $this->getElementId('parent_block');
      $response['showElements'][] = $this->getElementId('empty_widget');
      $response['showElements'][] = $this->getElementId('empty');
    }
    else
    {
      $response['showElements'][] = $this->getElementId('checkout_url');
      $response['hideElements'][] = $this->getElementId('empty_widget');
    }

    if( isset($data['updateElements']) )
      $response['updateElements'] = array_merge($response['updateElements'], Arr::cut($data, 'updateElements'));

    if( isset($data['showElements']) )
      $response['showElements'] = array_merge($response['showElements'], Arr::cut($data, 'showElements'));

    if( isset($data['hideElements']) )
      $response['hideElements'] = array_merge($response['hideElements'], Arr::cut($data, 'hideElements'));

    echo json_encode(array_merge($response, $data));
    Yii::app()->end();
  }

  public function getDeliverySum()
  {
    return 0;
  }
}