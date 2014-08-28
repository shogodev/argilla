<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class PlatronSystemTest extends CDbTestCase
{
  protected $fixtures = array(
    'order' => 'Order',
  );

  /**
   * @var PHPUnit_Framework_MockObject_MockObject|PlatronSystem $paymentSystem
   */
  protected $paymentSystem;

  protected $config = array(
    'merchantId' => '123',
    'secretKey' => 'quwawekaninusaba',
    'testMode' => true,
    'testModeHost' => 'http://devpass.shogo-test.ru/'
  );

  protected function setUp()
  {
    parent::setUp();

    $this->paymentSystem = $this->getMock('PlatronSystem', array('getTestMode', 'getPaymentSystem', 'doRequest'), array(999, $this->config));
    $this->paymentSystem->expects($this->any())->method('getTestMode')->will($this->returnValue(1));
    $this->paymentSystem->expects($this->any())->method('getPaymentSystem')->will($this->returnValue('TESTCARD'));
    $this->paymentSystem->expects($this->any())->method('doRequest')->will($this->returnCallback(array($this, 'doRequestDataProvider')));
  }

  public function testRenderWidget()
  {
    $result = $this->paymentSystem->renderWidget(true);
    $this->assertStringStartsWith('https://www.platron.ru/', $result);
  }

  public function testGetPayment()
  {
    $result = $this->paymentSystem->getPayment();
    $this->assertEquals('ok', $result['pg_status']);

    $result = $this->paymentSystem->getPaymentStatus();
    $this->assertEquals('ok', $result['pg_transaction_status']);

    $result = $this->paymentSystem->getCapturePayment();
    $this->assertEquals('error', $result['pg_status']);
  }

  /**
   * @param $data
   *
   * @dataProvider resultDataProvider
   */
/*  public function testProcessCheckPayment($data)
  {
    $this->paymentSystem = $this->getMock('PlatronSystem', array('getRequestData'), array(89, $this->config));
    $this->paymentSystem->expects($this->any())->method('getRequestData')->will($this->returnValue($data));

    try
    {
      ob_start();
      $this->paymentSystem->processCheckPayment();
    }
    catch(TEndException $exception)
    {
      $xml = new SimpleXMLElement(ob_get_clean());
      $this->assertEquals('ok', strval($xml->pg_status));
      return;
    }

    $this->fail('Отсутствует исключение TEndException');
  }*/

  /**
   * @param $data
   *
   * @dataProvider resultDataProvider
   */
/*  public function testProcessResultPayment($data)
  {
    $this->paymentSystem = $this->getMock('PlatronSystem', array('getRequestData'), array(89, $this->config));
    $this->paymentSystem->expects($this->any())->method('getRequestData')->will($this->returnValue($data));

    try
    {
      ob_start();
      $this->paymentSystem->processResultPayment();
    }
    catch(TEndException $exception)
    {
      $xml = new SimpleXMLElement(ob_get_clean());
      $this->assertEquals('ok', strval($xml->pg_status));
      return;
    }

    $this->fail('Отсутствует исключение TEndException');
  }*/

  public function checkDataProvider()
  {
    return array(array(
      array(
        'pg_salt' => 'b4eaeb038',
        'pg_order_id' => 89,
        'pg_payment_id' => 8789192,
        'pg_amount' => '11500.0000',
        'pg_currency' => 'RUR',
        'pg_ps_amount' => 11500,
        'pg_ps_full_amount' => '11500.00',
        'pg_ps_currency' => 'RUR',
        'pg_payment_system' => 'TEST',
        'pg_sig' => 'c4a10dbeeccf6111856a7872cd7344b6',
      )
    ));
  }

  public function resultDataProvider()
  {
    return array(array(
      array(
        'pg_salt' => 'eb5393',
        'pg_order_id' => 89,
        'pg_payment_id' => 8789192,
        'pg_amount' => '11500.0000',
        'pg_currency' => 'RUR',
        'pg_net_amount' => 11500,
        'pg_ps_amount' => 11500,
        'pg_ps_full_amount' => '11500.00',
        'pg_ps_currency' => 'RUR',
        'pg_payment_system' => 'TEST',
        'pg_description' => '',
        'pg_result' => '1',
        'pg_payment_date' => '2013-12-19 16:20:55',
        'pg_can_reject' => 0,
        'pg_user_phone' => '923423423423',
        'pg_sig' => 'aad51c42b85b6e2173b028132664be8f',
      )
    ));
  }

  public function doRequestDataProvider($url)
  {
    $data = array(
      'https://www.platron.ru/init_payment.php' => array(
        'pg_salt' => '6060e9c4bdb85acbfc0a800092260827',
        'pg_status' => 'ok',
        'pg_payment_id' => '10871876',
        'pg_redirect_url' => 'https://www.platron.ru/ps/test/start_payment.php?payment_id=10871876',
        'pg_redirect_url_type' => 'payment system',
        'pg_accepted_payment_systems' => 'TESTCARD',
        'pg_sig' => 'ebcc70ab1650bc1a516985e3ce126447',
      ),
      'https://www.platron.ru/get_status.php' => array(
        'pg_salt' => '6060e9c4bdb85acbfc0a800092260827',
        'pg_status' => 'ok',
        'pg_transaction_status' => 'ok',
        'pg_can_reject' => null,
        'pg_create_date' => '2014-04-28 14:18:49',
        'pg_result_date' => null,
        'pg_revoke_date' => null,
        'pg_payment_system' => 'TESTCARD',
        'pg_sig' => '6a7eaf3590834f4332842e34b70246fb',
      ),
      'https://www.platron.ru/do_capture.php' => array(
        'pg_salt' => 'c58685faf9549ceb234b42e040d46966',
        'pg_status' => 'error',
        'pg_error_code' => '373',
        'pg_error_description' => 'incorrect transaction status',
        'pg_sig' => '4ad5f63c534cd2659e66d134da64fb12',
      ),
    );

    return $data[$url];
  }
}