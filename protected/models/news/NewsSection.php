<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.news
 *
 * @method static NewsSection model(string $class = __CLASS__)
 *
 * @property string  $id
 * @property integer $position
 * @property string  $url
 * @property string  $name
 * @property string  $notice
 * @property string  $img
 * @property integer $visible
 *
 * @property News[]  $news
 */
class NewsSection extends FActiveRecord
{
  public function tableName()
  {
    return '{{news_section}}';
  }

  public function relations()
  {
    return array(
      'news' => array(self::HAS_MANY, 'News', 'section_id',
        'condition' => 'news.visible = 1',
        'order' => 'news.date DESC',
      ),
    );
  }
}