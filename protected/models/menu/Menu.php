<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @method static Menu model(string $class = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property string $sysname
 * @property string $url
 * @property int $visible
 *
 * @property IMenuItem[] $items
 */
class Menu extends FActiveRecord implements IMenuItem
{
  protected $depth = 1;

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu}}';
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'items' => array(self::HAS_MANY, 'MenuItem', 'menu_id'),
    );
  }

  /**
   * Устанавливает значение глубины построения меню
   *
   * @param int $d
   *
   * @return Menu|null
   */
  public function setDepth($d)
  {
    if( !is_int($d) )
      return null;

    $this->depth = $d;
    return $this;
  }

  /**
   * Построение меню
   *
   * @return array
   */
  public function build()
  {
    $data = array();

    if( $this->depth > 0 )
    {
      foreach( $this->items as $item )
      {
        /**
         * @var MenuItem $item
         */
        $item->setDepth(--$this->depth);
        $model = preg_replace("/([A-Z][a-z]+)\w*/", "$1", $item->frontend_model);

        $data[] = array(
          'label' => $item->getName(),
          'url'   => $item->getMenuLink(),
          'items' => $item->getChildren(),
          'itemOptions' => array(
            'class' => 'icn-top-'.strtolower($model).$item->item_id
          ),
        );
      }
    }

    return $data;
  }

  /**
   * Формирование ссылки на страницу, к которой привязано меню
   *
   * @return array
   */
  public function getMenuLink()
  {
    return array(
      $this->url,
    );
  }

  public function getChildren()
  {
    $this->build();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Получение меню по системному имени
   *
   * @param $sysname
   *
   * @return Menu
   */
  public static function getMenu($sysname)
  {
    return Menu::model()->find('sysname = :sysname', array(':sysname' => $sysname));
  }
}