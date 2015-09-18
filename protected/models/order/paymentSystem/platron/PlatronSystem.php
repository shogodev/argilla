<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class PlatronSystem extends AbstractPaymentSystem
{
  const SYSTEM_URL = 'https://www.platron.ru/';

  const PAYMENT_USER_URL = 'payment.php';

  const PAYMENT_URL = 'init_payment.php';

  const STATUS_URL = 'get_status.php';

  const CAPTURE_URL = 'do_capture.php';

  /**
   * Результат успешной оплаты заказа
   */
  const PAYMENT_SUCCESS = 1;

  /**
   * @var array Статусы платежа
   */
  public static $statuses = array(
    'partial' => 'Создание платежа',
    'pending' => 'Ожидание оплаты',
    'failed' => 'Платеж не прошел',
    'revoked' => 'Платеж отозван',
    'ok' => 'Платеж завершен успешно'
  );

  /**
   * @var bool Тестовый режим
   */
  protected $testMode;

  /**
   * @var string Хост, на который будут приходить ответы платрона в тестовом режиме
   */
  protected $testModeHost;

  /**
   * @var Идентификатор продавца в системе
   */
  protected $merchantId;

  /**
   * @var Секретный ключ магазина
   */
  protected $secretKey;

  /**
   * @var string Метод отправки и получения данных от платежной системы
   */
  protected $requestMethod = 'POST';

  /**
   * @var string Метод передачи данных на страницу завершения платежа
   */
  protected $successUrlMethod = 'POST';

  /**
   * @var string Метод передачи данных на страницу завершения платежа
   */
  protected $failureUrlMethod = 'POST';

  /**
   * @var int Время жизни счета (3600 * 24)
   */
  protected $lifeTime = 86400;

  /**
   * @var Случайные данные для формирования сигнатуры
   */
  protected $salt;

  /**
   * Возвращаем текстовый статус платежа
   *
   * @param $statusKey
   *
   * @return string
   */
  public static function getStatus($statusKey)
  {
    return isset(self::$statuses[$statusKey]) ? self::$statuses[$statusKey] : null;
  }

  /**
   * @return string
   */
  public function getId()
  {
    return 'platron';
  }

  /**
   * @param bool $captureOutput
   *
   * @throws CException
   * @return string|null
   */
  public function renderWidget($captureOutput = false)
  {
    if( !$this->order )
      throw new CException('Невозможно инициировать платеж. Не задан заказ для оплаты');

    $url = self::SYSTEM_URL.self::PAYMENT_USER_URL;
    $request = $this->initPayment($url);
    $data = $this->buildRequestUrl($url, $request);

    if( $captureOutput )
    {
      return $data;
    }
    else
    {
      echo $data;

      return null;
    }
  }

  /**
   * Инициализация платежа для оправки его в платежную систему
   * @throws CException
   * @return array $response
   */
  public function getPayment()
  {
    if( !$this->order )
      throw new CException('Невозможно инициировать платеж. Не задан заказ для оплаты');

    $url = self::SYSTEM_URL.self::PAYMENT_URL;
    $request = $this->initPayment($url);

    $data = $this->doRequest($url, $request);
    $this->checkSignature($data, PG_Signature::getScriptNameFromUrl($url));

    $this->order->setPaymentId(Arr::get($data, 'pg_payment_id'));

    return $data;
  }

  /**
   * Запрос на получение статуса платежа у платежной системы
   * @return array $response
   * @throws CException
   */
  public function getPaymentStatus()
  {
    if( !$this->order )
      throw new CException('Не задан заказ для получения статуса платежа');

    $request = array();
    $url = self::SYSTEM_URL.self::STATUS_URL;

    $request['pg_merchant_id'] = $this->merchantId;

    if( $paymentId = $this->order->getPaymentId() )
      $request['pg_payment_id'] = $paymentId;
    else
      $request['pg_order_id'] = $this->order->getId();

    $request['pg_salt'] = $this->getSalt();
    $request['pg_sig'] = $this->getSignature($request, PG_Signature::getScriptNameFromUrl($url));

    $data = $this->doRequest($url, $request);
    $this->checkSignature($data, PG_Signature::getScriptNameFromUrl($url));

    if( $data['pg_status'] === 'error' )
      throw new CException($data['pg_error_description']);

    $this->order->setStatus(Arr::get($data, 'pg_transaction_status'));

    return $data;
  }

  /**
   * Запрос на проведение операции клиринга
   * @return array $response
   * @throws CException
   */
  public function getCapturePayment()
  {
    if( !$this->order )
      throw new CException('Не задан заказ для проведения клиринга платежа');

    $request = array();
    $url = self::SYSTEM_URL.self::CAPTURE_URL;

    $request['pg_merchant_id'] = $this->merchantId;
    $request['pg_payment_id'] = $this->order->getPaymentId();
    $request['pg_salt'] = $this->getSalt();
    $request['pg_sig'] = $this->getSignature($request, PG_Signature::getScriptNameFromUrl($url));

    $data = $this->doRequest($url, $request);
    $this->checkSignature($data, PG_Signature::getScriptNameFromUrl($url));

    return $data;
  }

  /**
   * Проверка платежной системы возможность совершить платеж
   */
  public function processCheckPayment()
  {
    $data = $this->getRequestData($this->requestMethod);
    $this->checkSignature($data);
    $this->setOrder($data['pg_order_id']);
    $this->order->setPaymentId(Arr::get($data, 'pg_payment_id'));

    $response = array();
    $error = '';

    $response['pg_salt'] = $this->getSalt();
    $response['pg_status'] = $this->order->isOrderAvailable($data, $error) ? 'ok' : 'error';
    $response['pg_error_description'] = $error;
    $response['pg_sig'] = $this->getSignature($response);

    $this->sendXmlResponse($response);
  }

  /**
   * Уведомление магазина платежной системой о результате платежа
   */
  public function processResultPayment()
  {
    $data = $this->getRequestData($this->requestMethod);
    $this->checkSignature($data);
    $this->setOrder($data['pg_order_id']);
    $this->order->setPaymentId(Arr::get($data, 'pg_payment_id'));

    $response = array();
    $description = '';
    $payed = $data['pg_result'] == self::PAYMENT_SUCCESS;

    $response['pg_salt'] = $this->getSalt();
    $response['pg_status'] = $this->order->setPaymentResult($payed, $data, $description) ? 'ok' : 'error';
    $response['pg_description'] = $description;
    $response['pg_sig'] = $this->getSignature($response);

    $this->sendXmlResponse($response);
  }

  /**
   * Уведомление магазина платежной системой о завершении операции клиринга
   */
  public function processCapturePayment()
  {
    $data = $this->getRequestData($this->requestMethod);
    $this->checkSignature($data);
    $this->setOrder($data['pg_order_id']);
    $this->order->setPaymentId(Arr::get($data, 'pg_payment_id'));

    $response = array();
    $description = '';
    $clearing = $data['pg_result'] == self::PAYMENT_SUCCESS;

    $response['pg_salt'] = $this->getSalt();
    $response['pg_status'] = $this->order->setCaptureResult($clearing, $data, $description) ? 'ok' : 'error';
    $response['pg_description'] = $description;
    $response['pg_sig'] = $this->getSignature($response);

    $this->sendXmlResponse($response);
  }

  /**
   * Переход на страницу удачной оплаты
   */
  public function successPaymentResult()
  {
    $data = $this->getRequestData($this->successUrlMethod);
    $this->checkSignature($data);
    $this->setOrder($data['pg_order_id']);
  }

  /**
   * Переход на страницу неудачной оплаты
   */
  public function failurePaymentResult()
  {
    $data = $this->getRequestData($this->failureUrlMethod);
    $this->checkSignature($data);
    $this->setOrder($data['pg_order_id']);

    return $data['pg_failure_description'];
  }

  protected function initPayment($url)
  {
    $request = array();

    // Выставляем ссылки на страницы статусов
    $request['pg_check_url'] = $this->getCheckUrl();
    $request['pg_result_url'] = $this->getResultUrl();
    $request['pg_success_url'] = $this->getSuccessUrl();
    $request['pg_failure_url'] = $this->getFailureUrl();
    $request['pg_capture_url'] = $this->getCaptureUrl();

    // Методы передачи данных
    $request['pg_request_method'] = $this->requestMethod;
    $request['pg_success_url_method'] = $this->successUrlMethod;
    $request['pg_failure_url_method'] = $this->failureUrlMethod;

    // Данные магазина
    $request['pg_merchant_id'] = $this->merchantId;
    $request['pg_lifetime'] = $this->lifeTime;
    $request['pg_description'] = $this->getPaymentDescription();

    // Данные заказа
    $request['pg_order_id'] = $this->order->getId();
    $request['pg_amount'] = $this->order->getAmount();
    $request['pg_currency'] = $this->order->getCurrency();
    $request['pg_payment_system'] = $this->getPaymentSystem();

    // Данные пользователя
    $request['pg_user_phone'] = $this->order->getUserPhone();
    $request['pg_user_contact_email'] = $this->order->getUserEmail();

    //$request['pg_recurring_start'] = 1;

    if( !$this->getTestMode() )
      $request['pg_user_ip'] = Yii::app()->request->userHostAddress;

    // Режим работы
    $request['pg_testing_mode'] = $this->getTestMode();

    // Подписываем заказ
    $request['pg_salt'] = $this->getSalt();
    $request['pg_sig'] = $this->getSignature($request, PG_Signature::getScriptNameFromUrl($url));

    return $request;
  }

  /**
   * @return string Ссылка на страницу проверки актуальности заказа
   */
  protected function getCheckUrl()
  {
    return $this->createUrl('payment/check');
  }

  /**
   * @return string Ссылка на страницу установки результата платежа
   */
  protected function getResultUrl()
  {
    return $this->createUrl('payment/result');
  }

  /**
   * @return string Ссылка на страницу успешного оформления платежа
   */
  protected function getSuccessUrl()
  {
    return $this->createUrl('payment/success');
  }

  /**
   * @return string Ссылка на страницу неудачной оплаты
   */
  protected function getFailureUrl()
  {
    return $this->createUrl('payment/failure');
  }

  /**
   * @return string Ссылка на страницу окончания проведения клиринга
   */
  protected function getCaptureUrl()
  {
    return $this->createUrl('payment/capture');
  }

  protected function createUrl($route)
  {
    return isset($this->testModeHost) ? $this->testModeHost.$route.'/' : Yii::app()->controller->createAbsoluteUrl($route);
  }

  /**
   * @return int
   */
  protected function getTestMode()
  {
    return $this->testMode ? 1 : 0;
  }

  /**
   * Получаем данные запроса в зависимости от его типа
   *
   * @param $method
   *
   * @return array
   */
  protected function getRequestData($method)
  {
    return $method === 'POST' ? $_POST : $_GET;
  }

  /**
   * Формируем описание платежа
   * @return string
   */
  protected function getPaymentDescription()
  {
    return 'Оплата заказа №'.sprintf("%08d", $this->order->getId());
  }

  /**
   * Идентификатор выбранной платежной системы
   * @return null
   */
  protected function getPaymentSystem()
  {
    return $this->testMode ? 'TEST' : $this->order->getPaymentSystem();
  }

  /**
   * Формируем случайную соль
   * @return string
   */
  protected function getSalt()
  {
    if( !$this->salt )
      $this->salt = md5(rand(21, 43433).strtotime('now').$this->order->getId());

    return $this->salt;
  }

  /**
   * @param $salt
   */
  protected function setSalt($salt)
  {
    $this->salt = $salt;
  }

  /**
   * формируем подпись для переданных данных и скрипта их обработки
   *
   * @param array $request
   * @param null $script
   *
   * @return string
   */
  protected function getSignature(array $request, $script = null)
  {
    if( !$script )
    {
      $script = PG_Signature::getOurScriptName();
    }

    $request = array_filter($request, function ($item)
    {
      return isset($item);
    });

    return PG_Signature::make($script, $request, $this->secretKey);
  }

  /**
   * Проверка подписи данных, полученных от платежной системы
   *
   * @param array $request
   * @param null $scriptName
   *
   * @throws CException
   */
  protected function checkSignature(array $request, $scriptName = null)
  {
    if( $scriptName === null )
      $scriptName = PG_Signature::getOurScriptName();

    $signature = isset($request['pg_sig']) ? $request['pg_sig'] : null;

    if( !PG_Signature::check($signature, $scriptName, $request, $this->secretKey) )
    {
      throw new CException('Невозможно выполнить операцию. Получены неверные данные');
    }
    else
    {
      $this->setSalt($request['pg_salt']);
    }
  }

  /**
   * Отправляем ответ в платежную систему на полученный запрос
   *
   * @param $data
   * @param bool $exit
   */
  protected function sendXmlResponse($data, $exit = true)
  {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
    if( !headers_sent() )
      header('Content-type: text/xml');

    foreach($data as $key => $value)
      $xml->addChild($key, $value);

    echo $xml->asXML();

    if( $exit )
    {
      Yii::app()->end();
    }
  }

  /**
   * Делаем запрос в платежную систему
   *
   * @param string $url
   * @param array $request
   *
   * @throws CException
   * @return array|string
   */
  protected function doRequest($url, array $request)
  {
    $url = $this->buildRequestUrl($url, $request);

    $curl = new Curl();
    $curl->setTimeOut();

    if( $result = $curl->get($url) )
    {
      $xml = new SimpleXMLElement($result);
    }
    else
    {
      throw new CException($curl->getLastError());
    }

    return $this->xmlToArray($xml);
  }

  /**
   * @param $xml
   *
   * @return array
   */
  protected function xmlToArray($xml)
  {
    $array = json_decode(json_encode($xml), true);

    foreach(array_slice($array, 0) as $key => $value)
    {
      if( empty($value) )
      {
        $array[$key] = null;
      }
      elseif( is_array($value) )
      {
        $array[$key] = $this->xmlToArray($value);
      }
    }

    return $array;
  }

  /**
   * @param string $url
   * @param array $data
   *
   * @return string
   */
  protected function buildRequestUrl($url, array $data)
  {
    return Utils::buildUrl(array('path' => $url, 'query' => $data));
  }
}