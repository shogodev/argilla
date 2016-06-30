<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractPaymentSystem extends CComponent implements IPaymentSystem
{
  public $payableOrderClass = 'PayableOrder';

  /**
   * @var IPayableOrder
   */
  protected $order;

  /**
   * @param integer $orderId Уникальный id заказа
   * @param array $config
   */
  public function __construct($orderId = null, $config = array())
  {
    $this->setConfig($config);

    if( isset($orderId) )
    {
      $this->setOrder($orderId);
    }
  }

  /**
   * @return integer $orderId
   */
  public function getOrderId()
  {
    return $this->order->getId();
  }

  /**
   * @param array $config
   * @throws CException
   */
  protected function setConfig($config = array())
  {
    if( empty($config) )
    {
      $id = $this->getId();
      $path = GlobalConfig::instance()->frontendConfigPath.'/'.$id.'.php';

      if( !file_exists($path) )
      {
        throw new NoConfigException('Не удается найти конфигурацию платежной системы');
      }

      $config = require $path;
    }

    foreach($config as $parameter => $value)
    {
      if( property_exists($this, $parameter) )
      {
        $this->{$parameter} = $value;
      }
    }
  }

  /**
   * @param $orderId
   */
  protected function setOrder($orderId)
  {
    if( !isset($this->order) )
    {
      $this->order = new $this->payableOrderClass($orderId);
    }
  }

  abstract public function getId();
}