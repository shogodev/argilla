<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $visible
 */
class CustomMenuItem extends FActiveRecord implements IMenuItem
{

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu_custom_item}}';
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'data' => array(self::HAS_MANY, 'CustomMenuItemData', 'parent'),
    );
  }

  /**
   * Получение ссылки на элемент
   *
   * @return array
   */
  public function getMenuLink()
  {
    $url   = array();
    $url[] = $this->url;

    foreach( $this->data as $entry )
    {
      $url[$entry->name] = $entry->value;
    }

    return $url;
  }

  /**
   * У кастомных элементов не может быть дочерних элементов
   *
   * @param int $depth
   *
   * @return array
   */
  public function getChildren($depth = 0)
  {
    return array();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param int $d
   *
   * @return CustomMenuItem
   */
  public function setDepth($d)
  {
    return $this;
  }
}