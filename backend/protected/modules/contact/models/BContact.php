<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.models
 *
 * @method static BContact model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $address
 * @property string $notice
 * @property string $img
 * @property string $img_big
 * @property string $map
 * @property integer $visible
 *
 * @property ContactFgroup[] $contactFgroups
 * @property ContactField[] $contactFields
 */
class BContact extends BActiveRecord
{
  /**
   * @return array
   */
  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img, img_big'));
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('visible', 'numerical', 'integerOnly' => true),
      array('url, name', 'length', 'max' => 255),
      array('address', 'length', 'max' => 255),
      array('notice, map', 'length', 'max' => 1024),
      array('name, sysname, url, address, notice, map', 'safe'),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'contactGroups' => array(self::HAS_MANY, 'BContactGroup', 'contact_id'),
      'textblocks'    => array(self::HAS_MANY, 'BContactTextBlock', 'contact_id', 'order'=>'j.position ASC', 'alias'=>'j'),
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    $labels = array(
      'address' => 'Адрес',
      'notice'  => 'Примечание',
      'img'     => 'Карта проезда (уменьшенная)',
      'img_big' => 'Карта проезда (увеличенная)',
      'map'     => 'Карта проезда (код)',
    );

    return CMap::mergeArray(parent::attributeLabels(), $labels);
  }
}