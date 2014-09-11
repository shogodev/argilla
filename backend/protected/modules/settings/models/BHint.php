<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.settings.models
 *
 * @method static BHint model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $model
 * @property string $attribute
 * @property string $content
 */
class BHint extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('model, attribute, content', 'required'),
      array('model, attribute', 'length', 'max' => 255),
      array('popup', 'numerical', 'integerOnly' => true),

      array('id, model, attribute, content', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
        'popup' => 'Вывод в popup'
      )
    );
  }
}