<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 */
class SubdomainAuth extends CComponent
{
  const ACTION_LOGIN = 'login';
  
  const ACTION_LOGOUT = 'logout';
  
  /**
   * @var CDbHttpSession
   */
  private $storage;

  /**
   * @var string
   */
  private $salt;

  public function __construct($salt, $connectionID)
  {
    $this->salt = $salt;
    $this->storage = $this->getDBAuthStorage($connectionID);
  }

  public function autoLogin(CommonSubdomainUser $user)
  {
    $data = $this->load();

    if( Arr::get($data, 'action') != self::ACTION_LOGIN  )
      return;

    /**
     * @var Login $loginModel
     */
    if( $loginModel = Login::model()->findByPk(Arr::get($data, 'userId')) )
    {
      $userIdentity = new FUserIdentity($loginModel->login, $loginModel->password);
      $userIdentity->computePasswordHash = false;

      if( $userIdentity->authenticate() )
        $user->login($userIdentity);
    }
  }

  public function autoLogout(CommonSubdomainUser $user)
  {
    $data = $this->load();

    if( Arr::get($data, 'action') != self::ACTION_LOGOUT  )
      return;

    $user->logout(false);
  }

  /**
   * @param integer $id
   */
  public function login($id)
  {
    $this->save($id, self::ACTION_LOGIN);
  }

  /**
   * @param integer $id
   */
  public function logout($id)
  {
    $this->save($id, self::ACTION_LOGOUT);
  }

  /**
   * @param $connectionID
   *
   * @return CDbHttpSession
   */
  private function getDBAuthStorage($connectionID)
  {
    $storage = new CDbHttpSession();
    $storage->connectionID = $connectionID;
    $storage->sessionTableName = '{{subdomain_auth_session}}';
    $storage->openSession(null, null);

    return $storage;
  }

  private function save($id, $action, $onlyUpdate = false)
  {
    $userUniqueHash = $this->getUserUniqueHash($id);
    $this->saveUserHash($userUniqueHash);

    if( $onlyUpdate && !$this->storage->readSession($userUniqueHash) )
      return;

    $data = array(
      'userId' => $id,
      'action' => $action
    );
    
    $this->storage->writeSession($userUniqueHash, serialize($data));
  }

  /**
   * @return array|null
   */
  private function load()
  {
    $userHash = $this->loadUserHash();
    if( !$userHash  )
      return null;

    $serializedData = $this->storage->readSession($userHash->value);

    if( empty($serializedData) )
      return null;

    return unserialize($serializedData);
  }

  private function saveUserHash($userUniqueHash)
  {
    $cookie = new CHttpCookie('subdomainAuth', $userUniqueHash, array('domain' => Utils::getDomain(2)));

    Yii::app()->request->cookies->add('subdomainAuth', $cookie);
  }

  /**
   * @return CHttpCookie|null
   */
  private function loadUserHash()
  {
    return Yii::app()->request->cookies['subdomainAuth'];
  }

  /**
   * @param $id
   *
   * @return string
   */
  private function getUserUniqueHash($id)
  {
    return md5($id.$this->salt.'340295712349721304');
  }
} 