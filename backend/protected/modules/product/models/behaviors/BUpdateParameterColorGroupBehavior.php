<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 *  Подключение:
 *  ...
 *    'updateParameterColorGroupBehavior' => array(
 *      'class' => 'BUpdateParameterColorGroupBehavior',
 *      'colorParameterId' => BProductParamName::COLOR_ID,
 *      'colorGroupParameterId' => BProductParamName::COLOR_GROUP_ID
 *    )
 *  ...
 */

/**
 * Class BUpdateParameterColorGroupBehavior - Поведение для автоматического заполнения параметра "группа цвета" по параметру "цвет"
 * @property BProduct $owner
 */
class BUpdateParameterColorGroupBehavior extends SActiveRecordBehavior
{
  public $colorParameterId;

  public $colorGroupParameterId;

  /**
   * @var string $bindingThroughAttribute id группы цвета в таблице ProductParameterVariant
   */
  public $bindingThroughAttribute = 'notice';

  public function init()
  {
    if( empty($this->colorParameterId))
      throw new RequiredPropertiesException(__CLASS__, 'colorParameterId');

    if( empty($this->colorGroupParameterId))
      throw new RequiredPropertiesException(__CLASS__, 'colorGroupParameterId');
  }

  public function afterSave($event)
  {
    $this->clearOldColorGroupParameters();
    $this->createNewColorGroupParameters();
  }

  private function clearOldColorGroupParameters()
  {
    BProductParam::model()->deleteAllByAttributes(array('param_id' => $this->colorGroupParameterId, 'product_id' => $this->owner->id));
  }

  private function createNewColorGroupParameters()
  {
    $colorGroupVariants = $this->getColorGroupVariants();
    $this->saveColorGroupParameters($colorGroupVariants);
  }

  private function getColorGroupVariants()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.product_id', $this->owner->id);
    $criteria->compare('t.param_id', $this->colorParameterId);
    $criteria->join = 'JOIN '.BProductParamVariant::model()->tableName().' AS v ON '.' t.variant_id = v.id';
    $criteria->select = 'v.'.$this->bindingThroughAttribute;
    $criteria->distinct = true;

    $command = Yii::app()->db->commandBuilder->createFindCommand(BProductParam::model()->tableName(), $criteria);

    return $command->queryColumn();
  }

  private function saveColorGroupParameters(array $colorGroupVariants)
  {
    foreach($colorGroupVariants as $colorGroupVariantId)
    {
      if( empty($colorGroupVariantId) )
        continue;

      $productParameter = new BProductParam();
      $productParameter->product_id = $this->owner->id;
      $productParameter->param_id = $this->colorGroupParameterId;
      $productParameter->variant_id = $colorGroupVariantId;
      $productParameter->save();
    }
  }
}