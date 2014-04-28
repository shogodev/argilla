<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order
 */
class OrderModule extends BModule
{
  public $defaultController = 'BOrder';

  public $name = 'Заказы';

  public function beforeControllerAction($controller, $action)
  {
    Yii::import('frontend.components.ar.FActiveRecord');
    Yii::import('frontend.models.order.*');
    Yii::import('frontend.models.order.payment.*');
    Yii::import('frontend.models.order.paymentSystem.*');
    Yii::import('frontend.models.order.paymentSystem.platron.*');

    return parent::beforeControllerAction($controller, $action);
  }

  protected function getExtraDirectoriesToImport()
  {
    return array(
      'backend.modules.user.models.*',
    );
  }
}