<?php

/**
 * @package RBAC
 * @date 04.09.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 */
class TUserIdentity extends CUserIdentity
{
  private $_id;

  public function authenticate()
  {
    $criteria = new CDbCriteria();
    $criteria->condition = 'username=:username';
    $criteria->params = array(':username' => $this->username);

    $user = BUser::model()->find($criteria);

    $this->_id = $user->id;
    return !$this->errorCode = self::ERROR_NONE;

  }

  public function getId()
  {
    return $this->_id;
  }
}