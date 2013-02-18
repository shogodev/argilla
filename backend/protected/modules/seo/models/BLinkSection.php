<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $position
 * @property int $visible
 * @property BLink[] $links
 *
 * @method static BLinkSection model(string $class = __CLASS__)
 */
class BLinkSection extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{seo_link_section}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('url, name', 'required'),
      array('url', 'unique'),
      array('url', 'length', 'max' => 255),
      array('visible', 'length', 'max' => 1),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('notice', 'safe'),
    );
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'links' => array(self::HAS_MANY, 'BLink', 'section_id'),
    );
  }
}