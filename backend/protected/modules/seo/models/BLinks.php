<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BLinks model(string $class = __CLASS__)
 *
 * @property string date
 */
class BLinks extends BActiveRecord
{

  public function tableName()
  {
    return '{{links}}';
  }

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

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.date DESC',
    );
  }

  public function attributeLabels()
  {
    static $label_array = array('title' => 'Текст ссылки',
      'page' => 'Страница',
      'region' => 'Регион',
    );
    return CMap::mergeArray(parent::attributeLabels(), $label_array);
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'BLinksSection', 'section_id'),
    );
  }

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

  protected function beforeSave()
  {
    return parent::beforeSave() ? $this->date = date('Y-m-d', strtotime(!empty($this->date) ? $this->date : 'now')) : false;
  }

  protected function afterFind()
  {
    $this->date = !empty($this->date) ? date('d.m.Y', strtotime($this->date)) : '';
    parent::afterFind();
  }
}