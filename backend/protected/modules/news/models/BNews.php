<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
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
  public function behaviors()
  {
    return array(
      'uploadBehavior' => array(
        'class' => 'UploadBehavior',
        'validAttributes' => "img"
      ),
      'dateFilterBehavior' => array(
        'class' => 'DateFilterBehavior',
        'attribute' => 'date',
        'defaultNow' => true
      )
    );
  }

  public function rules()
  {
    return array(
      array('url, section_id', 'required'),
      array('url', 'unique'),

      array('section_id, position, visible', 'numerical', 'integerOnly' => true),

      array('url', 'length', 'max' => 255),
      array('visible, main', 'length', 'max' => 1),

      array('position, section_id, date, name, url, notice, content, visible', 'safe'),
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

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('section_id', $this->section_id);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('main', $this->main);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}