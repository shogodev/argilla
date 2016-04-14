<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения в frontend.php:
 * 'components' => array(
 *   ...
 *   'retailCrm' => array(
 *     'class' => 'ext.retailcrm.RetailCrm',
 *   ),
 *   ...
 * )
 */
Yii::import('frontend.components.cli.*');
Yii::import('ext.retailcrm.components.RetailCrmDataManager');

Yii::import('ext.retailcrm.components.lib.ApiClient', true);
Yii::import('ext.retailcrm.components.lib.Http.Client', true);
Yii::import('ext.retailcrm.components.lib.Exception.CurlException', true);
Yii::import('ext.retailcrm.components.lib.Exception.InvalidJsonException', true);
Yii::import('ext.retailcrm.components.lib.Response.ApiResponse', true);

/**
 * Class RetailCrm
 */
class RetailCrm extends CApplicationComponent
{
  public $debug = false;

  /**
   * @var string $url
   */
  protected $url;

  /**
   * @var string $apiKey
   */
  protected $apiKey;

  /**
   * @var boolean $enabled
   */
  protected $enabled;

  /**
   * @var boolean $log
   */
  protected $log;

  /**
   * @var ConsoleFileLogger $logger
   */
  public $logger;

  /**
   * @var RetailCrmDataManager $retailCrmDataManager
   */
  protected $retailCrmDataManager; 

  protected $exportProductCounter;

  /**
   * @var RetailCrm\ApiClient $apiClient
   */
  private $apiClient;

  public function init()
  {
    parent::init();

    $this->retailCrmDataManager = new RetailCrmDataManager();

    $this->configure();

    $this->logger = new ConsoleFileLogger('retail_crm.log');
    $this->logger->showLog = false;

    $this->apiClient = new RetailCrm\ApiClient(
      $this->url,
      $this->apiKey
    );
  }

  public function createCallback(Callback $model)
  {
    if( !$this->enabled )
      return;

    Utils::finishRequest();

    try
    {
      $data = $this->retailCrmDataManager->getCallbackData($model);
    }
    catch(CException $e)
    {
      $this->logger->error('Ошибка в формировании данных для RetailCrm. '.$e->getMessage());
      return;
    }
    $this->setCustomerData($data);

    if( $retailCrmOrderId = $this->sendOrder($data, $model) )
    {
      $this->retailCrmDataManager->setRetailCrmUrl($model, $data['number'], $this->url);
    }
  }

  public function createOrder(Order $model)
  {
    if( !$this->enabled )
      return;

    Utils::finishRequest();

    try
    {
      $data = $this->retailCrmDataManager->getOrderData($model);
    }
    catch(CException $e)
    {
      $this->logger->error('Ошибка в формировании данных для RetailCrm. '.$e->getMessage());
      return;
    }

    $this->setCustomerData($data);
    if( $retailCrmOrderId = $this->sendOrder($data, $model) )
    {
      $this->retailCrmDataManager->updateOrderStatus($model);
      $this->retailCrmDataManager->setRetailCrmUrl($model, $retailCrmOrderId, $this->url);
    }
  }

  public function registerEventEndExportIcml()
  {
    if( Yii::app()->request->getParam('force') != 'force' )
      return;

    $this->logger->startTimer(get_class($this));
    $this->logger->log('Начало экспорта icml', true);
    Yii::app()->attachEventHandler('onEndRequest', array($this, 'onEndExport'));
    Yii::app()->attachEventHandler('onException', array($this, 'onError'));
    Yii::app()->attachEventHandler('onError', array($this, 'onError'));
    register_shutdown_function(array($this, 'exceptionShutdown'));
  }

  public function onEndExport($event)
  {
    $logMessage = 'Экспорт icml завершен за '.$this->logger->finishTimer(get_class($this)).PHP_EOL;
    $logMessage .= 'Обработано '.$this->exportProductCounter.' продуктов.'.PHP_EOL;
    $this->logger->log($logMessage, true, true);
  }

  public function onError($event)
  {
    if( isset($event->exception) )
      $this->logger->error($event->exception->getMessage());
    else if( isset($event->message) )
      $this->logger->error($event->message);
    else
      $this->logger->error("Не известная ошибка");
  }

  public function exceptionShutdown()
  {
    $error = error_get_last();

    if (is_array($error) != FALSE)
    {
      if (isset($error['type']) != FALSE)
      {
        if ($error['type'] == 1)
        {
          $this->logger->error("Фатальная ошибка: ".$error['message']);
        }
      }
    }
  }

  public function increaseExportProductCounter()
  {
    $this->exportProductCounter++;
  }

  /**
   * @return ConsoleFileLogger
   */
  public function getLogger()
  {
    return $this->logger;
  }

  public function createDebugReport($attributes, $offerCounter)
  {
    if( $this->debug )
    {
      $log = 'Product id = '.$attributes['id'].' process'.PHP_EOL;
      $log .= 'Processed items '.$offerCounter.' Date '.date('d.m.Y H:i:s').PHP_EOL;
      $log .= 'Usage memory is '.round(memory_get_usage() / 1024).' KBt'.PHP_EOL;
      $log .= 'Peak usage memory is '.round(memory_get_peak_usage() / 1024).' KBt'.PHP_EOL;
      file_put_contents(Yii::getPathOfAlias('frontend.runtime').'/retail_crm_debug.log', array($log));
    }
  }

  /**
   * @param array $data
   *
   * @return null
   */
  private function setCustomerData(&$data = array())
  {
    $filter = array();

    if( !empty($data['phone']) )
      $filter['name'] = $data['phone'];
    else if( !empty($data['email']) )
      $filter['email'] = $data['email'];
    else
    {
      $nameArray = array();
      if( !empty($data['lastName']) )
        $nameArray[] = $data['lastName'];
      if( !empty($data['firstName']) )
        $nameArray[] = $data['firstName'];
      if( !empty($data['patronymic']) )
        $nameArray[] = $data['patronymic'];

      if( !empty($nameArray) )
        $filter['name'] = implode(' ', $nameArray);
    }

    if( empty($filter) )
      return;

    try
    {
      $response = $this->apiClient->customersList($filter);
    }
    catch(\RetailCrm\Exception\CurlException $e)
    {
      if( $this->log )
        $this->logger->error("Сетевые проблемы. Ошибка подключения к retailCRM: ".$e->getMessage());

      return;
    }

    if( $response->isSuccessful() )
    {
      if( !($user = Arr::reset($response->customers)) )
        return;

      if( isset($user['customerId']) )
      {
        $data['customerId'] = $user['customerId'];
      }
    }
    else
    {
      if( $this->log )
      {
        $errorMessage = "Ошибка при запросе пользователей: [Статус HTTP-ответа ".$response->getStatusCode()."] ".$response->getErrorMsg().'.';
        if( $response->offsetExists('errors') )
          $errorMessage .= ' '.$response->offsetGet('errors');

        $this->logger->error($errorMessage);
      }
    }
  }

  private function configure()
  {
    $configPath = Yii::getPathOfAlias('frontend.config.retail_crm').'.php';
    if( file_exists($configPath) )
    {
      $config = require($configPath);
      $this->url = $config['url'];
      $this->apiKey = $config['apiKey'];

      if( isset($config['idPrefix']) )
        $this->retailCrmDataManager->idPrefix = $config['idPrefix'];

      if( isset($config['log']) )
        $this->log = $config['log'];

      if( isset($config['debug']) )
        $this->debug = $config['debug'];

      $this->enabled = $config['enabled'];
    }
    else
    {
      throw new CHttpException('500', 'Не найден кофигурационный файл retail_crm.php в папке config');
    }
  }

  /**
   * @param array $data
   * @param CActiveRecord $model
   *
   * @return null|string $retailCrmId
   */
  private function sendOrder(array $data, CActiveRecord $model)
  {
    try
    {
      $response = $this->apiClient->ordersCreate($data);
    }
    catch(\RetailCrm\Exception\CurlException $e)
    {
      if( $this->log )
        $this->logger->error("Сетевые проблемы. Ошибка подключения к retailCRM: ".$e->getMessage());

      return null;
    }

    if( $response->isSuccessful() && 201 === $response->getStatusCode() )
    {
      if( $this->log )
        $this->logger->log('Заказ ('.get_class($model).') успешно создан id = '.$model->id.' retail_crm_id = '.$response->id);

      return $response->id;
    }
    else
    {
      if( $this->log )
      {
        $errorMessage = "Ошибка создания заказа(".get_class($model).") id = {$model->id}: [Статус HTTP-ответа ".$response->getStatusCode()."] ".$response->getErrorMsg().'.';
        if( $response->offsetExists('errors') )
          $errorMessage .= ' '.print_r($response->offsetGet('errors'), true);

        $this->logger->error($errorMessage);
      }
    }

    return null;
  }
}