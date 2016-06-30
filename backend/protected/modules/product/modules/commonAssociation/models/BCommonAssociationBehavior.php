<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * public function behaviors()
 * {
 *   return array(
 *     'commonAssociationBehavior' => array(
 *       'class' => 'backend.modules.commonAssociation.models.BCommonAssociationBehavior',
 *       'associationGroup' => 'color'
 *     ),
 *   );
 * }
 */
Yii::import('backend.modules.commonAssociation.models.BCommonAssociation');
/**
 * Class BCommonAssociationBehavior
 *
 * @property BActiveRecord $owner
 */
class BCommonAssociationBehavior extends SActiveRecordBehavior
{
  public $associationGroup;

  public function init()
  {
    if( empty($this->associationGroup) )
      throw new RequiredPropertiesException(__CLASS__, 'associationGroup');
  }

  public function afterDelete($event)
  {
    parent::afterDelete($event);

    BCommonAssociation::model()->clear($this->associationGroup);
  }
}