<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * Пример подключения
 * ...
 * array(
 *  'name' => 'BProductGroup',
 *  'header' => 'Группы товаров',
 *  'class' => 'BPopupColumn',
 *  'iframeAction' => '/productGroup/productGroup/index',
 * ),
 * ...
 *
 * @method static BProductGroup model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 */
class BProductGroup extends BActiveRecord implements IHasFrontendModel
{
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('position', 'numerical', 'integerOnly' => true)
    );
  }

  public function getFrontendModelName()
  {
    return 'ProductGroup';
  }
}