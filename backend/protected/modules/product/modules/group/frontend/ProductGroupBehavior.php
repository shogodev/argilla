<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * return array(
 *  'productGroupBehavior' => array('class' => 'backend.modules.product.modules.group.frontend.ProductGroupBehavior'),
 * );
 */
Yii::import('backend.modules.product.modules.group.frontend.*');

/**
 * Class ProductGroupBehavior
 *
 *  @property FActiveRecord $owner
 */
class ProductGroupBehavior extends AssociationBehavior
{
  /**
   * @param integer $id
   *
   * @return Association
   */
  public function getProductGroupAssociation($id)
  {
    return ProductGroup::model()->findByPk($id)->getAssociationForMe('Product');
  }
} 