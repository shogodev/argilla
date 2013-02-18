<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @property int $id
 * @property int $section_id
 * @property string $title
 * @property string $content
 * @property string $email
 * @property string $region
 * @property string $url
 * @property string $date
 * @property int $page
 * @property int $visible
 * @property int $position
 * @property BLinkSection $section
 *
 * @method static BLink model(string $class = __CLASS__)
 */
class BLink extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{seo_link}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('url, content, title', 'required'),
      array('url', 'unique'),
      array('section_id, position, visible', 'numerical', 'integerOnly' => true),
      array('url', 'length', 'max' => 255),
      array('visible', 'length', 'max' => 1),
      array('date', 'date', 'format' => 'mm.dd.yyyy'),
      array('page, title, notice, content', 'safe'),
      array('section_id', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.`date` DESC',
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'title' => 'Текст ссылки',
      'page' => 'Страница',
      'region' => 'Регион',
    ));
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'BLinkSection', 'section_id'),
    );
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('url', $this->url, true);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('section_id', $this->section_id);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  /**
   * @return bool
   */
  protected function beforeSave()
  {
    if( parent::beforeSave() )
    {
      $this->date = date('Y-m-d', strtotime(!empty($this->date) ? $this->date : 'now'));
      return true;
    }

    return false;
  }

  protected function afterFind()
  {
    $this->date = !empty($this->date) ? date('d.m.Y', strtotime($this->date)) : '';
    parent::afterFind();
  }
}