<?php

/**
 * @property string $id
 * @property string $dealer_id
 * @property integer $position
 * @property string $name
 * @property string $city_id
 * @property string $address
 * @property string $worktime
 * @property string $phone
 * @property string $phone_additional
 * @property string $fax
 * @property string $email
 * @property string $skype
 * @property string $notice
 * @property string $site_url
 * @property string $coordinates
 * @property integer $visible
 *
 * @property BDealerCity $city
 */
class BDealerFilial extends BActiveRecord
{
  public function tableName()
  {
    return '{{dealer_filial}}';
  }

  public function rules()
  {
    return array(
      array('dealer_id', 'required'),
      array('city_id', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('dealer_id, city_id', 'length', 'max' => 10),
      array('name, address, worktime, phone, phone_additional, email, skype, site_url, fax', 'length', 'max' => 255),
      array('coordinates', 'filter', 'filter' => function($value){return preg_replace("/(\d+\.\d{6})\d+,(\d+.\d{6})\d+/", "$1,$2", $value);}),
      array('coordinates', 'length', 'max' => 50),
      array('notice', 'safe')
    );
  }

  public function relations()
  {
    return array('city' => array(self::BELONGS_TO, 'BDealerCity', 'city_id'));
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'dealer_id' => 'Дилер',
      'city_id' => 'Город',
      'worktime' => 'Часы работы',
      'coordinates' => 'Координаты',
      'phone_additional' => 'Дополнительные телефоны',
      'site_url' => 'Ссылка на сайт'
    ));
  }

  public function getFullAddress()
  {
    $address = array();

    if( $this->city && !empty($this->city->name) )
      $address[] = $this->city->name;

    if( !empty($this->address) )
      $address[] = $this->address;

    return implode(', ', $address);
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('id', $this->id);
    $criteria->compare('dealer_id', $this->dealer_id);
    $criteria->compare('city_id', $this->city_id);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}