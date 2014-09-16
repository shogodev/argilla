<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.models
 *
 * @property string $base
 * @property string $target
 * @property integer $type_id
 * @property integer $visible
 */

Yii::import('frontend.components.redirect.RedirectHelper');

/**
 * Class BRedirect
 *
 * @method static BRedirect model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $base
 * @property string $target
 * @property integer $type_id
 * @property integer $visible
 *
 * @property BRedirectType $type
 */
class BRedirect extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{seo_redirect}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('base, target, type_id', 'required'),
      array('type_id, visible', 'numerical', 'integerOnly' => true),
      array('base, target', 'length', 'max' => 255),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'type' => array(self::HAS_ONE, 'BRedirectType', 'type_id'),
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'base'    => 'Начальный URL',
      'target'  => 'Конечный URL',
      'type_id' => 'Тип',
    ));
  }

  /**
   * @return bool
   */
  protected function beforeSave()
  {
    if( !RedirectHelper::isRegExp($this->base) )
      $this->base = Utils::getRelativeUrl($this->base);

    if( !RedirectHelper::isRegExp($this->target) )
      $this->target = Utils::getRelativeUrl($this->target);

    return parent::beforeSave();
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('base', $this->base, true);
    $criteria->compare('target', $this->target, true);
    $criteria->compare('type_id', $this->type_id);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }
}