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
 *     'commonAssociationBehavior' => array('class' => 'backend.modules.product.modules.commonAssociation.frontend.CommonAssociationBehavior'),
 *   );
 * }
 */
Yii::import('backend.modules.product.modules.commonAssociation.frontend.CommonAssociation');
/**
 * Class AssociationBehavior
 *
 * @property FActiveRecord $owner
 */
class CommonAssociationBehavior extends SActiveRecordBehavior
{
  /**
   * Возвращает привязки к текушей модели
   *
   * @param string $associationGroup - группа привязок
   * @param bool $withMe - с родиткльской моделью
   *
   * @return CommonAssociation
   */
  public function getCommonAssociation($associationGroup, $withMe = false)
  {
    $model = new CommonAssociation();
    $model->association_group = $associationGroup;
    return $model->setTagByModel($this->owner, $withMe);
  }
}