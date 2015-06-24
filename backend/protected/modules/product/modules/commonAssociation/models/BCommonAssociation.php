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
 * @property integer $pk
 * @property string $tag
 * @property string $association_group
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
      array('pk, tag', 'required'),
      array('pk', 'length', 'max' => 10),
      array('tag, association_group', 'length', 'max' => 255),
    );
  }

  /**
   * @param integer $parentPk - id инициатора привязки
   * @param integer $childPk - id с которым связывем
   * @param bool $associate - флаг действия (1 - привязать, 0 - отвязать)
   * @param string $associationGroup - группа связей
   *
   * @throws CHttpException
   */
  public function makeAssociation($parentPk, $childPk, $associate, $associationGroup)
  {
    $tag = $this->getTag($parentPk, $associationGroup);
    if( !$tag )
      $tag = $this->getTag($childPk, $associationGroup);
    if( !$tag )
      $tag = $this->createTag($parentPk, $associationGroup);

    if( $associate )
    {
      $this->createItem($childPk, $tag, $associationGroup);
    }
    else
    {
      $this->clearItem($childPk, $tag, $associationGroup);

      if( $this->countByAttributes(array('tag' => $tag, 'association_group' => $associationGroup)) == 1 )
      {
        $this->clearItem($parentPk, $tag, $associationGroup);
      }
    }
  }

  public function getCount($pk, $associationGroup)
  {
    if( $tag = $this->getTag($pk, $associationGroup) )
    {
      return $this->count('tag = :tag AND association_group = :associationGroup', array(':tag' => $tag, ':associationGroup' => $associationGroup));
    }

    return 0;
  }

  public function getChecked($parameters)
  {
    if( $parameters['src_id'] == $parameters['dst_id'] )
      return null;

    $srcTag = $this->getTag($parameters['src_id'], $parameters['dst']);
    $dstTag = $this->getTag($parameters['dst_id'], $parameters['dst']);

    if( $srcTag == $dstTag && !is_null($dstTag) )
      return true;
    else if( $srcTag != $dstTag && !is_null($srcTag) && !is_null($dstTag) )
      return null;
    else if( $srcTag != $dstTag && !is_null($dstTag) )
      return null;

    return false;
  }

  public function getAssociatedKeys($data = null)
  {
    if( !isset($data) )
    {
      $data = Arr::extract($_GET, array('src', 'dst', 'srcId'));
    }

    if( isset($data['srcId']) )
    {
      $data['src_id'] = Arr::cut($data, 'srcId');
    }

    $attributes = array('tag' => $this->getTag($data['src_id'], $data['dst']), 'association_group' => $data['dst']);

    return CHtml::listData(self::model()->findAllByAttributes($attributes), 'pk', 'pk');
  }

  private function createTag($primaryKey, $associationGroup)
  {
    while($tag = md5(microtime()))
    {
      if( !$this->findByAttributes(array('tag' => $tag, 'association_group' => $associationGroup)) )
        break;
    }

    $this->createItem($primaryKey, $tag, $associationGroup);

    return $tag;
  }

  private function getTag($pk, $associationGroup)
  {
    if( $model = $this->findByAttributes(array('pk' => $pk, 'association_group' => $associationGroup)))
      return $model->tag;

    return null;
  }

  private function createItem($pk, $tag, $associationGroup)
  {
    if( $this->getTag($pk, $associationGroup) == $tag )
     return;

    $model = new BCommonAssociation();
    $model->pk = $pk;
    $model->tag = $tag;
    $model->association_group = $associationGroup;

    if( !$model->save() )
      throw new CHttpException(500, 'Не удалось создать запись');
  }

  private function clearItem($pk, $tag, $associationGroup)
  {
    if( !self::model()->deleteAllByAttributes(array('pk' => $pk, 'tag' => $tag, 'association_group' => $associationGroup)) )
      throw new CHttpException(500, 'Не удалось удалить запись');
  }
}