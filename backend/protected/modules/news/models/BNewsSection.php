<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.news.models
 *
 * @method static BNewsSection model(string $class = __CLASS__)
 *
 * @property string  $id
 * @property integer $position
 * @property string  $url
 * @property string  $name
 * @property string  $notice
 * @property string  $img
 * @property integer $visible

 * @property BNews[]  $news
 */
class BNewsSection extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('url, name', 'required'),
      array('url', 'unique'),
      array('url', 'length', 'max' => 255),

      array('visible', 'length', 'max' => 1),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('position, url, name, notice, img', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'news' => array(self::HAS_MANY, 'BNews', 'section'),
    );
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('visible', '='.$this->visible);
    $criteria->compare('name', $this->name, true);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}