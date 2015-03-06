<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>, Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
 *
 * Секция ссылок на ресурсы по теме.
 *
 * @method static LinkSection model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property string  $name
 * @property string  $url
 * @property integer $position
 * @property boolean $visible
 *
 * @property Link[]  $links Все видимые ссылки в текущей секции.
 * @property integer $linkCount Количество видимых ссылок в текущей секции.
 *
 * @property integer $pageCount Количество страниц в текущей секции.
 */
class LinkSection extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_link_section}}';
  }

  public function relations()
  {
    return [
      'links' => [self::HAS_MANY, 'Link', 'section_id', 'scopes' => 'visible'],
      'linkCount' => [self::STAT, 'Link', 'section_id',
        'condition' => 't.visible = :visible',
        'params' => [
          ':visible' => '1'
        ]
      ],
    ];
  }

  /**
   * Видимые секции отсортированные по позиции по возрастанию.
   *
   * @return array
   */
  public function defaultScope()
  {
    return [
      'condition' => 'visible = :visible',
      'order' => 'IF(position, position, 999999999)',
      'params' => [
        ':visible' => '1'
      ]
    ];
  }

  /**
   * Секция с указанным URL.
   *
   * @param string $url URL секции.
   *
   * @return LinkSection
   */
  public function whereUrl($url)
  {
    $this->getDbCriteria()->mergeWith([
      'condition' => 'url = :url',
      'params' => [
        ':url' => $url
      ]
    ]);

    return $this;
  }

  /**
   * Возвращает количество страниц в текущей секции.
   *
   * @return int Количество страниц.
   */
  public function getPageCount()
  {
    return intval(
      $this->dbConnection->createCommand()
        ->select('MAX(page)')
        ->from(Link::model()->tableName())
        ->where('section_id = :sectionId', [':sectionId' => $this->id])
        ->andWhere('visible = :visible', [':visible' => '1'])
        ->queryScalar()
    );
  }

  /**
   * Возвращает видимые ссылки из текущей секции на указанной странице.
   *
   * @param int $page Страница, для которой вернуть ссылки.
   *
   * @return Link[] Ссылки на странице.
   */
  public function getLinksOnPage($page)
  {
    return Link::model()->visible()->inSection($this->id)->onPage($page)->findAll();
  }

  /**
   * Номер записи с какой начинается страница
   *
   * @param int $page Страница, для которой вернуть ссылки.
   *
   * @return Link[] Ссылки на странице.
   */
  public function getNumLink($page)
  {
    return Link::model()->visible()->inSection($this->id)->linksOnPagesBefore($page)->count();
  }

  protected function afterFind()
  {
    $this->url = Yii::app()->controller->createUrl('link/section', array('section' => $this->url));
    parent::afterFind();
  }
}