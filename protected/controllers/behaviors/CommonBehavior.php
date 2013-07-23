<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
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

  /**
   * @var FForm
   */
  private $fastOrderForm;

  private $basket;

  private $favorite;

  private $fastOrderBasket;

  private $topCatalogMenu;

  /**
   * @return FBasket|null
   */
  public function getBasket()
  {
    if ( $this->basket == null )
      $this->basket = new FBasket('basket', array('service'), array('Product', 'ServiceBasket'));

    return $this->basket;
  }

  public function getFavorite()
  {
    if ( $this->favorite == null )
      $this->favorite = new FFavorite('favorite', array(), array('Product'));

    return $this->favorite;
  }

  /**
   * @return FBasket|null
   */
  public function getFastOrderBasket()
  {
    if ( $this->fastOrderBasket == null )
      $this->fastOrderBasket = new FBasket('fastOrderBasket', array('service'), array('Product', 'Service'), false);

    return $this->fastOrderBasket;
  }

  public function getTopMenu()
  {
    $menu = Menu::getMenu('top');
    return  $menu ? $menu->build() : array();
  }

  public function getBottomMenu()
  {
    $menu = Menu::getMenu('bottom');
    return  $menu ? $menu->build() : array();
  }

  public function getTopCatalogMenu()
  {
    if( $this->topCatalogMenu === null )
      $this->topCatalogMenu = ProductSection::model()->getMenu();

    return $this->topCatalogMenu;
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
      $this->loginForm->autocomplete = true;
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

  /**
   * @return FForm
   */
  public function getFastOrderForm()
  {
    if( !$this->fastOrderForm )
    {
      $this->fastOrderForm = new FForm('FastOrder', new Order('fastOrder'));
      $this->fastOrderForm->action = Yii::app()->controller->createUrl('basket/fastOrder');
    }

    return $this->fastOrderForm;
  }
}