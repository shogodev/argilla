<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 *
 * @property array counters
 * @property array copyrights
 * @property FForm loginForm
 * @property FForm callbackForm
 */
class CommonBehavior extends CommonDataBehavior
{
  /**
   * @var FForm
   */
  private $loginForm;

  /**
   * @var FForm
   */
  private $callbackForm;

  private $basket;

  /**
   * @return FBasket|null
   */
  public function getBasket()
  {
    if ( empty ($this->basket) )
      $this->basket = new FBasket();

    return $this->basket;
  }

  public function getTopMenu()
  {
    $menu = Menu::getMenu('top');
    return  $menu ? $menu->build() : array();
  }

  public function getBottomMenu()
  {
    return Menu::getMenu('bottom')->build();
  }


  public function getCatalogMenu()
  {
    $menu = ProductSection::model()->getMenu();

    $menu[] = array(
      'label' => 'Аксессуары',
      'url' => array('accessory/index'),
    );

    return $menu;
  }

  /**
   * @return FForm
   */
  public function getLoginForm()
  {
    if( !$this->loginForm )
    {
      $this->loginForm = new FForm('LoginForm', new Login());
      $this->loginForm->action = Yii::app()->controller->createUrl('user/login');
      $this->loginForm->ajaxSubmit = false;
    }

    return $this->loginForm;
  }

  /**
   * @return FForm
   */
  public function getCallbackForm()
  {
    if( !$this->callbackForm )
    {
      $this->callbackForm = new FForm('CallbackForm', new Callback());
      $this->callbackForm->action = Yii::app()->controller->createUrl('callback/index');
    }

    return $this->callbackForm;
  }
}