<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.user
 *
 * @method static UserProfile model(string $class = __CLASS__)
 *
 * @property integer $user_id
 * @property string $name
 * @property string $last_name
 * @property string $patronymic
 * @property string $address
 * @property string $phone
 * @property string $birthday
 * @property string $discount
 */
class UserProfile extends FActiveRecord
{
  public function rules()
  {
    return array(
      array('name', 'required', 'except' => User::SCENARIO_REGISTRATION),
      array('name, last_name, patronymic, address, birthday, phone', 'safe'),
    );
  }

  public function behaviors()
  {
    return array(
      'dateFormatBehavior' => array(
        'class' => 'DateFormatBehavior',
        'attribute' => 'birthday',
      )
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя',
      'last_name' => 'Фамилия',
      'patronymic' => 'Отчество',
      'phone' => 'Контактный телефон',
      'address' => 'Адрес',
      'bicycle' => 'Модель велосипеда',
      'birthday' => 'Дата рождения',
      'discount' => 'Персональная скидка'
    ));
  }
}