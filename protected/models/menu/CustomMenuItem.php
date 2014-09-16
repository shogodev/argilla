<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $visible
 *
 * @property CustomMenuItemData[] $data
 */
class CustomMenuItem extends FActiveRecord implements IMenuItem
{
  public function tableName()
  {
    return '{{menu_custom_item}}';
  }

  public function defaultScope()
  {
    return array(
      'with' => 'data'
    );
  }

  public function relations()
  {
    return array(
      'data' => array(self::HAS_MANY, 'CustomMenuItemData', 'parent'),
    );
  }

  /**
   * @return array
   */
  public function getMenuUrl()
  {
    if( substr($this->url, 0, 1) === '/' && substr($this->url, -1, 1) === '/' )
      return $this->url;

    $url = array($this->url);

    foreach($this->data as $item)
    {
      $url[$item->name] = $item->value;
    }

    return $url;
  }

  /**
   * @return array
   */
  public function getChildren()
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
   * @param int $depth
   */
  public function setDepth($depth = null)
  {

  }
}