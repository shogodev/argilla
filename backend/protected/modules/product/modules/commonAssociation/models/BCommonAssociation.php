<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения в views:
 * 'columns' => array(
 *   array(
 *    'name' => 'color',
 *     'widget' => 'commonAssociation.components.BCommonAssociationButton',
 *     'header' => 'Цвета',
 *     'class' => 'BPopupColumn',
 *   ),
 * ),
 *
 */

/**
 * Class BCommonAssociation
 *
 * @method static BCommonAssociation model(string $class = __CLASS__)
 *
 * @property integer $product_id
 * @property string $tag
 */
class BCommonAssociation extends BActiveRecord
{
  public function tableName()
  {
    return '{{product_common_association}}';
  }

  public function rules()
  {
    return array(
      array('tag', 'unique', 'on' => 'new'),
      array('product_id, tag', 'required'),
      array('product_id', 'length', 'max' => 10),
      array('tag', 'length', 'max' => 255),
    );
  }

  public function makeAssociation($parentPk, $productId, $value)
  {
    $tag = $this->getTag($parentPk);
    if( !$tag )
      $tag = $this->getTag($productId);
    if( !$tag )
      $tag = $this->createTag($parentPk);

    if( $value )
    {
      $this->createItem($productId, $tag);
    }
    else
    {
      $this->clearItem($productId, $tag);

      if( $this->countByAttributes(array('product_id' => $parentPk, 'tag' => $tag)) == 1 )
        $this->clearItem($parentPk, $tag);
    }
  }

  public function getCount($pk)
  {
    if( $tag = $this->getTag($pk) )
    {
      return $this->count('tag = :tag', array(':tag' => $tag));
    }

    return 0;
  }

  public function getChecked($parameters)
  {
    if( $parameters['src_id'] == $parameters['dst_id'] )
      return null;

    $srcTag = $this->getTag($parameters['src_id']);
    $dstTag = $this->getTag($parameters['dst_id']);

    if( $srcTag == $dstTag && !is_null($dstTag) )
      return true;
    else if( $srcTag != $dstTag && !is_null($srcTag) && !is_null($dstTag) )
      return null;
    else if( $srcTag != $dstTag && !is_null($dstTag) )
      return null;

    return false;
  }

  private function createTag($primaryKey)
  {
    while($tag = md5(microtime()))
    {
      if( !$this->findByAttributes(array('tag' => $tag)) )
        break;
    }

    $this->createItem($primaryKey, $tag);

    return $tag;
  }

  private function getTag($primaryKey)
  {
    if( $model = $this->findByAttributes(array('product_id' => $primaryKey)))
      return $model->tag;

    return null;
  }

  private function createItem($pk, $tag)
  {
    if( $this->getTag($pk) == $tag )
     return;

    $model = new BCommonAssociation();
    $model->product_id = $pk;
    $model->tag = $tag;

    if( !$model->save() )
      throw new CHttpException(500, 'Не удалось создать запись');
  }

  private function clearItem($pk, $tag)
  {
    if( !self::model()->deleteAllByAttributes(array('product_id' => $pk, 'tag' => $tag)) )
      throw new CHttpException(500, 'Не удалось удалить запись');
  }
}