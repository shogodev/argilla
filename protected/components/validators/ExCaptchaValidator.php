<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.validators
 */
class ExCaptchaValidator extends CCaptchaValidator
{
  protected function validateAttribute($object, $attribute)
  {
   if( !Yii::app()->request->isAjaxRequest )
   {
     parent::validateAttribute($object, $attribute);
   }
  }
}