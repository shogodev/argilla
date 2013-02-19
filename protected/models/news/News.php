<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
 */
class News extends FActiveRecord
{
  public $image;

  public function tableName()
  {
    return '{{news}}';
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

  protected function afterFind()
  {
    $this->date  = !empty($this->date) ? date('d.m.Y', strtotime($this->date)) : '';
    $this->url   = Yii::app()->controller->createUrl('news/one', array('url' => $this->url));
    $this->image = $this->img ? new FSingleImage($this->img, 'news') : null;

    parent::afterFind();
  }
}