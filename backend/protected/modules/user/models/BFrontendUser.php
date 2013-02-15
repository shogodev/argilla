<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.models
 *
 * @method static BFrontendUser model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $date_create
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string $service
 * @property string $service_id
 * @property string $discount
 * @property string $restore_code
 * @property string $type
 * @property integer $visible
 */
class BFrontendUser extends BActiveRecord
{
  const TYPE_USER = 'user';

  public $password_confirm;

  public $fullName;

  private $_password;

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
      array('password_confirm', 'compare', 'compareAttribute' => 'password'),
      array('password, discount, visible', 'safe'),
      array('fullName', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array('fullName' => 'Имя'));
  }

  public function defaultScope()
  {
    return array(
      'order' => 'date_create DESC',
      'condition' => "type='".BFrontendUser::TYPE_USER."'"
    );
  }

  public function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( empty($this->password) )
        $this->password = $this->_password;
      else
      {
        Yii::import('frontend.components.FUserIdentity');
        $this->password = FUserIdentity::createPassword($this->login, $this->password);
      }
      return true;
    }
    return false;
  }

  public function afterFind()
  {
    $this->_password = $this->password;
    $this->password  = '';
    return parent::afterFind();
  }

  public function relations()
  {
    return array(
      'user' => array(self::HAS_ONE, 'BUserDataExtended', 'user_id'),
    );
  }

  public function getFullName()
  {
    $fullName = $this->user ? (implode(" ", array($this->user->last_name, $this->user->name, $this->user->patronymic))) : '';

    return strtr($fullName, array(
      '   ' => ' ',
      '  '  => ' '
    ));
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->together = true;
    $criteria->with     = array('user');

    $criteria->compare('visible', '='.$this->visible);
    $criteria->compare('login', $this->login, true);
    $criteria->compare('email', $this->email, true);

    if( !empty($this->fullName) )
    {
      $criteria->addSearchCondition('CONCAT(user.last_name, " ", user.name, " ", user.patronymic)', $this->fullName, true);
    }

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}