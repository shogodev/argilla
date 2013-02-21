<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductSection model(string $class = __CLASS__)
 *
 * @property string $id
 * @property integer $position
 * @property string $url
 * @property string $name
 * @property string $menu_name
 * @property string $notice
 * @property string $img
 * @property integer $visible
 *
 * @property BProductAssignment[] $productAssignments
 */
class BProductSection extends BActiveRecord
{
  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => "img"));
  }

  public function rules()
  {
    return array(
      array('url, name', 'required'),
      array('url', 'unique'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('url', 'length', 'max' => 255),
      array('name, menu_name, notice, content_top, content_bottom', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'content_top'    => 'Текст вверху',
      'content_bottom' => 'Текст внизу',
      'menu_name' => 'Название для меню',
    ));
  }
}