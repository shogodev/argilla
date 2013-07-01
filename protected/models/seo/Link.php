<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class Link Ссылка на ресурс по теме.
 *
 * @method static Link model(string $className = __CLASS__)
 * @method Link visible()
 *
 * @property integer $id
 * @property integer $section_id
 * @property string  $title
 * @property string  $content
 * @property string  $email
 * @property string  $region
 * @property string  $url
 * @property string  $date
 * @property integer $page
 * @property boolean $visible
 * @property integer $position
 *
 * @property LinkSection $section Секция, к которой принадлежит данная ссылка.
 */
class Link extends FActiveRecord
{
  const LINKS_PER_PAGE = 10;

  public function tableName()
  {
    return '{{seo_link}}';
  }

  public function rules()
  {
    return [
      ['title, url, section_id', 'required'],
      ['title, url, region', 'length', 'max' => 255, 'min' => 3],
      ['url', 'url'],
      ['email', 'email'],
      ['section_id', 'numerical', 'integerOnly' => true],
      ['content', 'safe'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'url' => 'URL сайта',
      'title' => 'Название сайта',
      'content' => 'Описание сайта',
      'section_id' => 'Категория',
      'email' => 'E-mail',
    ];
  }

  public function relations()
  {
    return [
      'section' => [self::BELONGS_TO, 'LinkSection', 'section_id'],
    ];
  }

  /**
   * Ссылки отсортированные по атрибуту 'position' по возрастанию.
   *
   * @return array
   */
  public function defaultScope()
  {
    return [
      'order' => 'IF(position, position, 999999999)',
    ];
  }

  public function scopes()
  {
    return [
      'visible' => [
        'condition' => 'visible = :visible',
        'params' => [
          ':visible' => '1',
        ],
      ],
    ];
  }

  /**
   * Ссылки на указанной странице.
   *
   * @param int $page Номер страницы, для которой нужно найти ссылки.
   *
   * @return Link
   */
  public function onPage($page)
  {
    $this->getDbCriteria()->mergeWith([
      'condition' => 'page = :page',
      'params' => [
        ':page' => strval($page),
      ],
    ]);

    return $this;
  }

  /**
   * Ссылки в указанной секции.
   *
   * @param int $sectionId ID секции, для которой искать ссылки.
   *
   * @return Link
   */
  public function inSection($sectionId)
  {
    $this->getDbCriteria()->mergeWith([
      'condition' => 'section_id = :sectionId',
      'params' => [
        ':sectionId' => strval($sectionId),
      ],
    ]);

    return $this;
  }

  /**
   * Ссылки на всех страницах перед указанной.
   *
   * @param int $page Страница до которой искать ссылки.
   *
   * @return Link
   */
  public function linksOnPagesBefore($page)
  {
    $this->getDbCriteria()->mergeWith([
      'condition' => 'page < :page',
      'params' => [
        ':page' => $page
      ]
    ]);

    return $this;
  }

  public function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( $this->isNewRecord )
      {
        $this->date = new CDbExpression('NOW()');
        $this->page = empty($this->page) || intval($this->page) < 1 ? 1 : $this->page;
        $this->page = $this->choosePage($this->page);
        $this->visible = 0;
      }

      return true;
    }

    return false;
  }

  /**
   * Выбирает страницу для новой ссылки.
   *
   * @param int $page Начальная страница.
   *
   * @return int Выбранная страница.
   */
  private function choosePage($page)
  {
    $linksOnPage = self::model()->inSection($this->section_id)->onPage($page)->count();

    if( $linksOnPage < self::LINKS_PER_PAGE )
    {
      return $page;
    }
    else
    {
      return $this->choosePage(++$page);
    }
  }
}