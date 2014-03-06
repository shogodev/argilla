<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 */
class FControllerBehavior extends CBehavior
{
  /**
   * @var FForm
   */
  private $loginPopupForm;

  /**
   * @var FForm
   */
  private $callbackForm;

  /**
   * @var FForm
   */
  private $fastOrderForm;

  /**
   * @var FBasket
   */
  private $basket;

  /**
   * @var FFavorite
   */
  private $favorite;

  /**
   * @var FVisits
   */
  private $visits;

  /**
   * @var FCompare
   */
  private $compare;

  private $sectionMenu;

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

  public function getVisits()
  {
    if ( $this->visits == null )
    {
      $this->visits = new FFavorite('visits', array(), array('Product'));
      $this->visits->ajaxUrl = Yii::app()->controller->createUrl('visits/index');
    }
    return $this->visits;
  }

  /**
   * @return FCompare
   */
  public function getCompare()
  {
    if ( $this->compare == null )
    {
      $this->compare = new FCompare('compare', array(), array('Product', 'ProductSection'));
      $this->compare->ajaxUrl = Yii::app()->controller->createUrl('compare/index');
      $this->compare->addButtonAjaxUrl = Yii::app()->controller->createUrl('compare/add');
    }

    return $this->compare;
  }

  public function getTopMenu()
  {
    return Menu::model()->getMenu('top');
  }

  public function getBottomMenu()
  {
    return Menu::model()->getMenu('bottom');
  }

  public function getSectionMenu()
  {
    if( $this->sectionMenu === null )
      $this->sectionMenu = ProductAssignment::model()->getSectionMenu();

    return $this->sectionMenu;
  }

  /**
   * @return FForm
   */
  public function getLoginPopupForm()
  {
    if( !$this->loginPopupForm )
    {
      $this->loginPopupForm = new FForm('LoginPopupForm', new Login());
      $this->loginPopupForm->action = Yii::app()->controller->createUrl('user/login');
      $this->loginPopupForm->ajaxSubmit = false;
      $this->loginPopupForm->autocomplete = true;
    }

    return $this->loginPopupForm;
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