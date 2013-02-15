<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FCaptchaAction extends CCaptchaAction
{
  protected function getSessionKey()
  {
    return self::SESSION_VAR_PREFIX . Yii::app()->getId() . '.index.' . $this->getId();
  }
}