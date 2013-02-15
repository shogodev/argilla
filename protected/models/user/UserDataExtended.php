<?php
/**
 * User: tatarinov
 * Date: 24.12.12
 *
 * @property int    $user_id
 * @property string $name
 * @property string $last_name
 * @property string $patronymic
 * @property string $coordinates
 * @property string $address
 * @property string $phone
 * @property string $birthday
 * @property string $avatar
 *
 * @property User $user
 */
class UserDataExtended extends FActiveFileRecord implements JsonSerializable
{
  const PATH = 'f/user/';

  const COORDINATED_CACHE_ID = 'coordinated_udata';

  const DEFAULT_AVATAR_NAME = 'default_avatar.jpg';

  /**
   * @var string
   */
  public $fileModel = 'UserDataExtended';

  /**
   * @var int
   */
  public $maxFiles = 1;

  /**
   * @var string
   */
  public $formAttribute = 'avatar';

  /**
   * @var array
   */
  public $fileTypes = array('jpg', 'jpeg', 'png', 'gif');

  public $maxFileWidth = 100;

  public $maxFileHeight = 100;

  /**
   * @static
   *
   * @return array
   */
  public static function getCoordinatedData()
  {
    $query = 'SELECT d.name, d.last_name, c.name as city, d.coordinates, d.user_id, d.avatar, d.address
              FROM '.self::model()->tableName().' as d
              LEFT JOIN '.City::model()->tableName().' as c ON c.id = d.city_id
              WHERE d.coordinates != ""';

    $connection = Yii::app()->db;
    $command    = $connection->createCommand($query);
    $result = $command->queryAll();

    $data = array();

    foreach( $result as $item )
    {
      $url = '/user/profile/'.$item['user_id'].'/';

      $data[] = array(
        'dealer'      => array(
          'name' => $item['name'].' '.$item['last_name'],
          'img'  => !empty($item['avatar']) ? '/f/user/'.$item['avatar'] : '/f/user/'.self::DEFAULT_AVATAR_NAME,
        ),
        'position'    => 0,
        'city'        => !empty($item['city']) ? $item['city'] : '',
        'address'     => !empty($item['address']) ? $item['address'] : '',
        'url'         => $url,
        'href'        => $url,
        'coordinates' => explode(',', $item['coordinates']),
      );
    }

    return $data;
  }

  /**
   * @static
   *
   * @return CDbCacheDependency
   */
  public static function getCacheDependency()
  {
    return new CDbCacheDependency("SELECT `value` FROM `{{settings}}` WHERE `param` = 'community_map_cache'");
  }

  /**
   * @OVERRIDE
   *
   * @return string
   */
  public function tableName()
  {
    return '{{user_data_extended}}';
  }

  /**
   * @OVERRIDE
   *
   * @return void
   */
  public function init()
  {
    $this->uploadPath = Yii::getPathOfAlias('webroot').'/'.self::PATH;
    parent::init();
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('last_name, patronymic, address, coordinates, birthday, birth_day, birth_mount, birth_year, avatar', 'safe')
    );
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'User', 'user_id'),
    );
  }

  /**
   * @return $this
   */
  public function coordinated()
  {
    $this->getDbCriteria()->condition = 'coordinates != ""';
    return $this;
  }

  /**
   * @OVERRIDE
   *
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
      'city'         => 'Город',
      'bicycle'      => 'Модель велосипеда',
      'frame_number' => 'Номер рамы',
      'equipment'    => 'Комплектация',
      'interests'    => 'Интересы',
      'ride_places'  => 'Перечислите Ваши любимые места катания на велосипеде',
      'birthday'     => 'Дата рождения',
      'avatar'       => 'Аватара 100x100',
      'city_id'      => 'Город',
    );
  }

  /**
   * @OVERRIDE
   *
   * @return void
   */
  public function afterFind()
  {
    if( !empty($this->birthday) )
      $this->birthday = str_replace('-', '.', $this->birthday);

    return parent::afterFind();
  }

  public function afterSave()
  {
    if( !empty($this->avatar) && file_exists($this->getPathOfAvatar().$this->avatar) )
    {
      list($width, $height) = getimagesize($this->getPathOfAvatar().$this->avatar);

      if( $this->maxFileWidth < $width || $this->maxFileHeight < $height)
        $this->resize($this->getPathOfAvatar().$this->avatar, $this->maxFileWidth, $this->maxFileHeight);
    }

    return parent::afterSave();
  }

  /**
   * @return bool
   */
  public function deleteFile()
  {
    if( !empty($this->{$this->formAttribute}) )
    {
      $img = $this->{$this->formAttribute};
      $this->{$this->formAttribute} = '';

      if( unlink($this->uploadPath.$img) && $this->save(false) );
        return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public function getDeleteFileUrl()
  {
    return Yii::app()->controller->createUrl('user/deleteAvatar');
  }

  /**
   * @return string
   */
  public function getCity()
  {
    if( !empty($this->city_id) )
      return City::model()->findByPk($this->city_id)->name;

    return '';
  }

  /**
   * @OVERRIDE
   *
   * @return array|mixed
   */
  public function jsonSerialize()
  {
    $attributes = $this->attributes;

    $attributes['dealer']         = array();
    $attributes['dealer']['name'] = $this->name . ' ' . $this->last_name;
    $attributes['dealer']['img']  = $this->user->getAvatar();

    $attributes['position']       = 0;
    $attributes['city']           = $this->getCity();
    $attributes['url']            = Yii::app()->controller->createUrl('user/profile', array('id' => $this->user_id));
    $attributes['href']           = Yii::app()->controller->createUrl('user/profile', array('id' => $this->user_id));
    //$attributes['link']           = str_replace("http://", "", $attributes['href']);
    $attributes['coordinates']    = explode(',', $this->coordinates);

    return $attributes;
  }

  /**
   * @return string
   */
  public function getPathOfAvatar()
  {
    return 'f/user/';
  }

  /**
   * @OVERRIDE
   *
   * @return void
   */
  protected function initPath()
  {
    if( !$this->uploadPath )
      $this->uploadPath = Yii::getPathOfAlias('webroot').'/f/user/';

    if( !file_exists($this->uploadPath) )
    {
      mkdir($this->uploadPath);
      chmod($this->uploadPath, 0777);
    }
  }

  /**
   * @param $file
   * @param $width
   * @param $height
   *
   * @return void
   */
  private function resize($file, $width, $height)
  {
    $thumb = Yii::app()->phpThumb->create($file);
    $thumb->resize($width, $height);
    $thumb->save($file);
  }

}