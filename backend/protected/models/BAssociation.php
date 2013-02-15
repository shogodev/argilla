<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.models.Association
 *
 * @method static BAssociation model(string $class = __CLASS__)
 *
 * @property string $src
 * @property integer    $src_id
 * @property string $dst
 * @property integer    $dst_id
 */
class BAssociation extends BActiveRecord
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{association}}';
  }

  /**
   * @param BActiveRecord $model
   * @param string        $dst
   * @param array         $ids
   * @param bool          $deleteAssociations
   */
  public function updateAssociations(BActiveRecord $model, $dst, array $ids, $deleteAssociations = true)
  {
    if( $deleteAssociations )
      BAssociation::model()->deleteAssociations($model, $dst);

    foreach($ids as $id)
    {
      $association         = new BAssociation();
      $association->src    = $this->getSrc($model);
      $association->src_id = $model->getPrimaryKey();
      $association->dst    = $dst;
      $association->dst_id = $id;

      $association->save();
    }
  }

  /**
   * @param BActiveRecord $model
   * @param null|string   $dst
   * @param null|string   $dstId
   */
  public function deleteAssociations(BActiveRecord $model, $dst = null, $dstId = null)
  {
    $src   = $this->getSrc($model);
    $srcId = $model->getPrimaryKey();

    $criteria = new CDbCriteria();
    $criteria->compare('src', '='.$src);
    $criteria->compare('src_id', '='.$srcId);

    if( $dst )
      $criteria->compare('dst', '='.$dst);

    if( $dstId )
      $criteria->compare('dst_id', '='.$dstId);

    $this->deleteAll($criteria);
  }

  /**
   * @param BActiveRecord $model
   *
   * @return string
   */
  public function getSrc(BActiveRecord $model)
  {
    return strtolower(get_class($model));
  }
}