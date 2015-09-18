<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.order
 * @method static OrderDeliveryExtended model(string $class = __CLASS__)
 */
class OrderDeliveryExtended extends CFormModel
{
  const NEW_ADDRESS = 'new';

  public $delivery_id;

  public $rememberAddress;

  public $metro_id;

  public $street;

  public $house;

  public $case;

  public $building;

  public $porch;

  public $room;

  public $doorphone;

  public function rules()
  {
    return array(
      array('delivery_id, metro_id, street, house', 'validateNewAddress'),
      array('delivery_id, rememberAddress, region, metro_id, street, house, case, building, porch, room, doorphone', 'safe')
    );
  }

  public function getAddress()
  {
    $userAddress = null;

    if( !$this->isNewAddress() )
    {
      $userAddress = UserAddress::model()->findByPk($this->rememberAddress);
    }

    if( is_null($userAddress) )
    {
      $userAddress = new UserAddress();
      $userAddress->setAttributes($this->attributes);
    }

    return $userAddress->getAddress();
  }

  public function saveAddress()
  {
    if( Yii::app()->user->isGuest || !$this->isNewAddress() )
      return;

    /**
     * @var UserAddress $userAddress
     */
    $userAddress = new UserAddress();
    $userAddress->setAttributes($this->attributes);
    $userAddress->user_id = Yii::app()->user->id;
    $userAddress->save();
  }

  public function attributeLabels()
  {
    return array(
      'delivery_id' => Yii::t('template', 'Способ доставки'),
      'metro_id' => Yii::t('template', 'Метро'),
      'street' => Yii::t('template', 'Улица'),
      'house' => Yii::t('template', 'Дом'),
      'case' => Yii::t('template', 'Корпус'),
      'building' => Yii::t('template', 'Строение'),
      'porch' => Yii::t('template', 'Подъезд'),
      'room' => Yii::t('template', 'Квартира/офис'),
      'doorphone' => Yii::t('template', 'Домофон'),
      'city' => Yii::t('template', 'Населенный пункт'),
    );
  }

  public function validateNewAddress($attribute)
  {
    if( $this->isNewAddress() && empty($this->{$attribute}) )
    {
      $params['{attribute}'] = $this->getAttributeLabel($attribute);
      $this->addError($attribute, strtr(Yii::t('yii', '{attribute} cannot be blank.'), $params));
    }
  }

  public function getDeliveryPrice($orderSum)
  {
    $metro = Metro::model()->findByPk($this->metro_id);
    if( $orderSum < $metro->metroZone->price )
    {
      return $metro->metroZone->price_delivery;
    }

    return null;
  }

  protected function isNewAddress()
  {
    return empty($this->rememberAddress) || $this->rememberAddress == self::NEW_ADDRESS;
  }
}