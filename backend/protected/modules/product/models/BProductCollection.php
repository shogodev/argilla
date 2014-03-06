<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductCollection model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property string $url
 * @property string $img
 * @property string $notice
 * @property integer $visible
 */
class BProductCollection extends BProductStructure
{
  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'));
  }

  public function rules()
  {
    return array(
      array('url, name', 'required'),
      array('url', 'unique'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('url', 'length', 'max' => 255),
      array('name, notice', 'safe'),
    );
  }

  public function afterDelete()
  {
    BProductTreeAssignment::assignToModel($this, 'category')->delete();
    parent::afterDelete();
  }
}