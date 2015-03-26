<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 *
 * @property string $commonSalt - соль общая для проектов на поддоменах
 * @property string $commonConnectionID - идентификатор подключения к БД
 *
 * Класс общей авторизации пользователей на поддоменах
 * Пример подключения в frontend.php
 * <pre>
 *  'user' => array(
 *    'class' => 'CommonSubdomainUser',
 *     'commonSalt' => '23452345239054289374592033582345'
 *     'commonConnectionID' => 'commonDB',
 *     'allowAutoLogin' => true,
 *     'loginUrl' => '/',
 *   ),
 */
class CommonSubdomainUser extends FWebUser
{
  public $commonSalt;

  public $commonConnectionID;

  /**
   * @var SubdomainAuth
   */
  private $subdomainAuth;

  public function init()
  {
    if( empty($this->commonSalt) )
      throw new CHttpException(500, 'Не указано свойство commonSalt.');

    if( empty($this->commonConnectionID) )
      throw new CHttpException(500, 'Не указано свойство commonConnectionID.');

    $this->subdomainAuth = new SubdomainAuth($this->commonSalt, $this->commonConnectionID);
    $this->subdomainAuth->autoLogin($this);

    parent::init();

    if( !$this->isGuest )
      $this->subdomainAuth->autoLogout($this);
  }

  protected function beforeLogin($id, $states, $fromCookie)
  {
    $this->subdomainAuth->login($id);

    return parent::beforeLogin($id, $states, $fromCookie);
  }

  protected function beforeLogout()
  {
    $this->subdomainAuth->logout($this->id);

    return parent::beforeLogout();
  }
}