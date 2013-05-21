<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class LinkSection Секция ссылок на ресурсы по теме.
 *
 * @method static LinkSection model(string $className = __CLASS__)
 *
 * Атрибуты:
 * @property integer $id
 * @property string  $name
 * @property string  $url
 * @property integer $position
 * @property boolean $visible
 *
 * Отношения:
 * @property Link[]  $links Возвращает все видимые ссылки в этой секции.
 * @property integer $linkCount Возвращает количество видимых ссылок в этой секции.
 *
 * @property integer $pageCount Возвращает количество страниц в этой секции.
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
   * Скоуп по умолчинию. Отыскивает видимые секции и сортирует их по позиции по возрастанию.
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
   * Именованный скоуп. Отыскивает секцию с указанным URL.
   *
   * @param string $url URL секции.
   *
   * @return LinkSection Владелец.
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
   * Возвращает количество страниц в этой секции.
   *
   * @return int Количество страниц.
   */
  public function getPageCount()
  {
    return intval($this->dbConnection->createCommand()
      ->select('MAX(page)')
      ->from(Link::model()->tableName())
      ->where('section_id = :sectionId', [':sectionId' => $this->id])
      ->andWhere('visible = :visible', [':visible' => '1'])
      ->queryScalar());
  }

  /**
   * Возвращает видимые ссылки из этой секции на указанной странице.
   *
   * @param int $page Страница для которой вернуть ссылки.
   *
   * @return Link[] Ссылки на странице.
   */
  public function getLinksOnPage($page)
  {
    return Link::model()->visible()->inSection($this->id)->onPage($page)->findAll();
  }
}