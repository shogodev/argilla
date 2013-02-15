<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BLinksSection model(string $class = __CLASS__)
 */
class BLinksSection extends BActiveRecord
{
  public function tableName()
  {
    return '{{links_section}}';
  }

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

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  public function relations()
  {
    return array(
      'links' => array(self::HAS_MANY, 'BLinks', 'section'),
    );
  }
}