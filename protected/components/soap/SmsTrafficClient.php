<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.soap
 */
class SmsTrafficClient extends AbstractSoapClient
{
  /**
   * @var int
   */
  protected static $rusOption = '1';

  /**
   * Название отправителя
   *
   * @var string
   */
  protected static $originator = 'Cuberussia';

  /**
   * @param string $phone
   * @param string $message
   */
  public function sendMessage($phone, $message)
  {
    $this->send(SmsSoapConfig::getInstance()->getLogin(),
                SmsSoapConfig::getInstance()->getPassword(),
                $phone,
                $message,
                self::$originator,
                self::$rusOption);
  }

  /**
   * @return string
   */
  public function getWsdl()
  {
    return SmsSoapConfig::getInstance()->getWsdl();
  }

  /**
   * @param $data
   *
   * @return string
   */
  protected function encode($data)
  {
    return iconv('UTF-8', 'CP1251', $data);
  }
}