<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 *  $this->widget('BCustomColumnsGridView', array(
 *    ...
 *    'columns' => array(
 *      ...
 *      array(
 *        'name' => 'BFeature',
 *        'header' => 'Особенности',
 *        'class' => 'BPopupColumn',
 *        'iframeAction' => 'product/feature/feature',
 *      ),
 *      ...
 *    ),
 *    ...
 *  ));
 */

 /**
 * @method static BFeature model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $img
 * @property string $name
 * @property string $notice
 */
class BFeature extends BActiveRecord implements IHasFrontendModel
{
  public function tableName()
  {
    return '{{product_feature}}';
  }

  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => "img"));
  }

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('position', 'numerical', 'integerOnly' => true),
      array('notice', 'safe'),
    );
  }

  public function defaultScope()
  {
    return array(
      'order' => "IF(position=0, 999999999, position), id",
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'notice' => 'Значение',
    ));
  }
}