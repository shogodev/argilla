<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.models
 *
 * @method static BUserDataExtended model(string $class = __CLASS__)
 *
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
class BUserDataExtended extends BActiveRecord implements IHasCoordinates
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{user_data_extended}}';
  }

  /**
   * @return array
   */
  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'avatar'));
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('last_name, patronymic, address, birthday, coordinates, birth_day, birth_mount, birth_year', 'safe')
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
      'avatar'       => 'Изображение',
      'coordinates'  => 'Координаты',
    );
  }

  /**
   * @return void
   */
  public function afterFind()
  {
    if( !empty($this->birthday) )
      $this->birthday = str_replace('-', '.', $this->birthday);

    return parent::afterFind();
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->user_id;
  }

  /**
   * @return string
   */
  public function getFullAddress()
  {
    return $this->address;
  }
}