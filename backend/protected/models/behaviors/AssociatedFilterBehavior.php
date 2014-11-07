<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.models.behaviors
 *
 * Examples:
 * <pre>
 * public function behaviors()
 * {
 *   return array(
 *     'associatedFilter' => array(
 *       'class' => 'AssociatedFilterBehavior',
 *     ),
 *   );
 * }
 * </pre>
 */
class AssociatedFilterBehavior extends SActiveRecordBehavior
{
  /**
   * @var integer
   */
  public $associated;

  public function init()
  {
    $this->attachValidators();
    $this->attachEvents();
  }

  public function beforeSearch(CEvent $event)
  {
    /**
     * @var CDbCriteria $criteria
     */
    $criteria = $event->params['criteria'];

    if( isset($this->associated) )
    {
      $field = $this->owner->tableAlias.'.id';
      $keys = BAssociation::model()->getAssociatedKeys();

      if( !empty($this->associated) )
        $criteria->addInCondition($field, $keys);
      elseif( $this->associated === '0' )
        $criteria->addNotInCondition($field, $keys);
    }

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