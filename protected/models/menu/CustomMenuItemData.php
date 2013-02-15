<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @property int $id
 * @property int $parent
 * @property string $name
 * @property string $value
 */
class CustomMenuItemData extends FActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu_custom_item_data}}';
  }
}