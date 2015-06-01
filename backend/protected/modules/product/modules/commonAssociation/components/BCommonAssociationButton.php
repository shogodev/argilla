<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.modules.commonAssociation.models.BCommonAssociation');

class BCommonAssociationButton extends BAssociationButton
{
  public $ajaxAction = '/product/commonAssociation/commonAssociation/association';

  public $parameters = array(
    'BPkColumn[associationClass]' => 'commonAssociation.models.BCommonAssociation',
  );

  protected function getAssociationsCount($parameters)
  {
    return BCommonAssociation::model()->getCount($parameters['srcId']);
  }
}