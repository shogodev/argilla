<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Examples:
 *
 * public function behaviors()
 * {
 *   return array(
 *     'associatedFilter' => array(
 *       'class' => 'AssociatedFilterBehavior',
 *     ),
 *   );
 * }
 */
class AssociatedFilterBehavior extends CActiveRecordBehavior
{
  /**
   * @var integer
   */
  public $associated;

  public function attach($owner)
  {
    parent::attach($owner);
    $this->attachValidators();
    $this->attachEvents();
  }

  public function beforeSearch(CEvent $event)
  {
    /**
     * @var CDbCriteria $criteria
     */
    $criteria = $event->params['criteria'];

    if( !empty($this->associated) )
      $criteria->addInCondition('id', BAssociation::model()->getAssociatedKeys());
    elseif( $this->associated === '0' )
      $criteria->addNotInCondition('id', BAssociation::model()->getAssociatedKeys());

    return $criteria;
  }

  private function attachValidators()
  {
    $this->owner->getValidatorList()->add(
      CValidator::createValidator('CSafeValidator', $this->owner, 'associated', array('on' => 'search'))
    );
  }

  private function attachEvents()
  {
    $this->owner->attachEventHandler('onBeforeSearch', array($this, 'beforeSearch'));
  }
}