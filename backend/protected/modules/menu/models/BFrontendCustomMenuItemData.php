<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 *
 * @property integer $id
 * @property integer $parent
 * @property string $name
 * @property string $value
 * @property integer $visible
 *
 * @method static BFrontendCustomMenuItemData model(string $class = __CLASS__)
 */
class BFrontendCustomMenuItemData extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu_custom_item_data}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('parent, name, value', 'safe'),
    );
  }
}