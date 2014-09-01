<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.models
 */

Yii::import('frontend.components.auth.FUserIdentity');

/**
 * Class BFrontendUser
 *
 * @method static BFrontendUser model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $date_create
 * @property string $login
 * @property string $email
 * @property string $password_hash
 * @property string $service
 * @property string $service_id
 * @property string $restore_code
 * @property string $type
 * @property integer $visible
 * @property BUserProfile $profile
 */
class BFrontendUser extends BActiveRecord
{
  const TYPE_USER = 'user';

  /**
   * @var string
   */
  public $password;

  /**
   * @var string
   */
  public $confirmPassword;

  public $fullName;

  public $userPhone;

  public function tableName()
  {
    return '{{user}}';
  }

  public function rules()
  {
    return array(
      array('login, email', 'required'),
      array('email', 'email'),
      array('email', 'unique'),
      array('confirmPassword', 'compare', 'compareAttribute' => 'password'),
      array('password', 'doHashPassword'),
      array('visible', 'safe'),
      array('fullName, userPhone', 'safe', 'on' => 'search'),
    );
  }

  public function defaultScope()
  {
    return array(
      'order' => 'date_create DESC',
      'condition' => 'type = :type_user',
      'params' => array(':type_user' => BFrontendUser::TYPE_USER)
    );
  }

  public function relations()
  {
    return array(
      'profile' => array(self::HAS_ONE, 'BUserProfile', 'user_id'),
    );
  }

  public function afterValidate()
  {
    parent::afterValidate();

    if( $this->isNewRecord )
      $this->type = self::TYPE_USER;
  }

  public function doHashPassword($attribute, $params)
  {
    if( !empty($this->password) )
    {
      $this->restore_code = '';
      $this->password_hash = FUserIdentity::createPassword($this->login, $this->password);
    }
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'fullName' => 'Имя',
      'userPhone' => 'Телефон',
      'confirmPassword' => 'Подтверждение пароля',
    ));
  }

  public function getFullName()
  {
    $fullName = $this->profile ? (implode(" ", array($this->profile->last_name, $this->profile->name, $this->profile->patronymic))) : '';
    return preg_replace("/\s+/", " ", trim($fullName));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  public function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->together = true;
    $criteria->with = array('profile');

    $criteria->compare('visible', $this->visible);
    $criteria->compare('login', $this->login, true);
    $criteria->compare('email', $this->email, true);

    $criteria->compare('profile.phone', $this->userPhone, true);

    if( !empty($this->fullName) )
      $criteria->addSearchCondition('CONCAT(profile.last_name, " ", profile.name, " ", profile.patronymic)', $this->fullName, true);

    return $criteria;
  }
}