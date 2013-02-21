<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.news.models
 *
 * @method static BNews model(string $class = __CLASS__)
 *
 * @property string  $id
 * @property integer $section_id
 * @property integer $position
 * @property string  $url
 * @property integer $visible
 * @property integer $main
 * @property string  $date
 * @property string  $notice
 * @property string  $name
 * @property string  $content
 * @property string  $img
 */
class BNews extends BActiveRecord
{
  public $date_from;

  public $date_to;

  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => "img"));
  }

  public function rules()
  {
    return array(
      array('url', 'required'),
      array('url', 'unique'),

      array('section_id, position, visible', 'numerical', 'integerOnly' => true),

      array('url', 'length', 'max' => 255),
      array('visible, main', 'length', 'max' => 1),

      array('date', 'date', 'format' => 'mm.dd.yyyy'),

      array('position, section_id, date, name, url, notice, content, visible', 'safe'),
      array('date_from, date_to', 'safe', 'on' => 'search'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.date DESC',
    );
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'BNewsSection', 'section_id'),
    );
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

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('section_id', '='.$this->section_id);
    $criteria->compare('visible', '='.$this->visible);
    $criteria->compare('main', '='.$this->main);
    $criteria->compare('name', $this->name, true);

    if( !empty($this->date_from) || !empty($this->date_to) )
      $criteria->addBetweenCondition('date', Utils::dayBegin($this->date_from), Utils::dayEnd($this->date_to));

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}