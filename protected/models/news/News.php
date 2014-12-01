<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.news
 *
 * @method static News model(string $class = __CLASS__)
 * @method News main()
 *
 * @property string  $id
 * @property integer $section_id
 * @property integer $position
 * @property string  $url
 * @property string  $visible
 * @property string  $date
 * @property string  $notice
 * @property string  $name
 * @property string  $content
 * @property string  $img
 *
 * @property NewsSection $section
 * @property FActiveImage $image
 */
class News extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.date DESC, '.$alias.'.position ASC',
    );
  }

  public function scopes()
  {
    $alias = $this->getTableAlias();

    return array(
      'main' => array(
        'condition' => $alias.'.main=1'
      )
    );
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'NewsSection', 'section_id'),
    );
  }

  /**
   * @param string $format
   *
   * @return string
   */
  public function getFormatDate($format = 'd.m.Y')
  {
    return DateTime::createFromFormat('Y-m-d H:i:s', $this->date)->format($format);
  }

  protected function afterFind()
  {
    $this->url = Yii::app()->controller->createUrl('news/one', array('section' => $this->section->url, 'url' => $this->url));

    parent::afterFind();
  }
}