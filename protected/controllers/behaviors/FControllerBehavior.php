<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 *
 * @var Contact $headerContacts
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

  /**
   * @var MenuBuilder
   */
  private $menuBuilder;

  /**
   * @return FBasket|null
   */
  public function getBasket()
  {
    if( !isset($this->basket) )
    {
      $this->basket = new FBasket('basket', array('options', 'ingredients'));
      $this->basket->ajaxUrl = Yii::app()->createUrl('basket/ajax');
      $this->basket->addButtonAjaxUrl = Yii::app()->createUrl('basket/ajax');
      $this->basket->collectionItemsForSum = array('ingredients');
    }

    return $this->basket;
  }

  public function getFavorite()
  {
    if( !isset($this->favorite) )
    {
      $this->favorite = new FFavorite('favorite');
      $this->favorite->ajaxUrl = Yii::app()->controller->createUrl('favorite/index');
    }

    return $this->favorite;
  }

  public function getVisits()
  {
    if( !isset($this->visits) )
    {
      $this->visits = new FFavorite('visits');
      $this->visits->ajaxUrl = Yii::app()->createUrl('visits/index');
    }

    return $this->visits;
  }

  /**
   * @return FCompare
   */
  public function getCompare()
  {
    if( !isset($this->compare) )
    {
      $this->compare = new FCompare('compare');
      $this->compare->ajaxUrl = Yii::app()->createUrl('compare/index');
      $this->compare->addButtonAjaxUrl = Yii::app()->createUrl('compare/add');
    }

    return $this->compare;
  }

  /**
   * @return MenuBuilder
   */
  public function getMenuBuilder()
  {
    if( !isset($this->menuBuilder) )
    {
      $this->menuBuilder = new MenuBuilder();
    }

    return $this->menuBuilder;
  }

  /**
   * @return FForm
   */
  public function getLoginPopupForm()
  {
    if( !isset($this->loginPopupForm) )
    {
      $this->loginPopupForm = new FForm('LoginPopupForm', new Login());
      $this->loginPopupForm->action = Yii::app()->createUrl('user/login');
      $this->loginPopupForm->ajaxSubmit = false;
      $this->loginPopupForm->validateOnChange = false;
      $this->loginPopupForm->validateOnSubmit = false;
      $this->loginPopupForm->autocomplete = true;
    }

    return $this->loginPopupForm;
  }

  /**
   * @return FForm
   */
  public function getCallbackForm()
  {
    if( !isset($this->callbackForm) )
    {
      $this->callbackForm = new FForm('CallbackForm', new Callback());
      $this->callbackForm->action = Yii::app()->createUrl('callback/index');
    }

    return $this->callbackForm;
  }

  /**
   * @return FForm
   */
  public function getFastOrderForm()
  {
    if( !isset($this->fastOrderForm) )
    {
      $this->fastOrderForm = new FForm('frontend.forms.order.FastOrder', new Order('fastOrder'));
      $this->fastOrderForm->action = Yii::app()->createUrl('basket/fastOrder');
    }

    return $this->fastOrderForm;
  }
}