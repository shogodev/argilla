<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.models

 * @method static BUserProfile model(string $class = __CLASS__)

 * @property integer $user_id
 * @property string $name
 * @property string $last_name
 * @property string $patronymic
 * @property string $coordinates
 * @property string $address
 * @property string $phone
 * @property string $birthday
 * @property string $avatar
 */
class BUserProfile extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{user_profile}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('last_name, patronymic, address, phone, birthday, coordinates', 'safe')
    );
  }

  public function behaviors()
  {
    return array(
      'dateFilterBehavior' => array(
        'class' => 'DateFilterBehavior',
        'attribute' => 'birthday',
      )
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return array(
      'name'         => 'Имя',
      'last_name'    => 'Фамилия',
      'patronymic'   => 'Отчество',
      'phone'        => 'Контактный телефон',
      'address'      => 'Адрес',
      'birthday'     => 'Дата рождения',
      'coordinates'  => 'Координаты',
    );
  }

  /**
   * @return string
   */
  public function getFullAddress()
  {
    return $this->address;
  }
}