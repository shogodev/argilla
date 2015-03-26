<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Использование:
 * ...
 *  'optionsBehavior' => array('class' => 'option.models.behaviors.BOptionBehavior')
 * ...
 */
Yii::import('option.models.*');
/**
 * Class BOptionBehavior
 * @property BOption[] $options
 */
class BOptionBehavior extends SActiveRecordBehavior
{
  public function init()
  {
    $this->owner->getMetaData()->addRelation('options', array(
        BActiveRecord::HAS_MANY, 'BOption', 'product_id')
    );
  }
}