<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.componetns.ar
 */
class FActiveRecord extends CActiveRecord
{
  /**
   * @param string $className
   *
   * @return SActiveRecord
   */
  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  /**
   * @return string
   */
  public function tableName()
  {
    return Utils::getTableNameFromClass(get_class($this));
  }

  public function attributeLabels()
  {
    return array(
      'name' => 'Имя',
      'content' => 'Сообщение',
    );
  }

  /**
   * @param CActiveRecord $object
   *
   * @return CActiveRecord
   */
  public function findThroughAssociation(CActiveRecord $object)
  {
    $association = Association::model()->source($this)->destination($object)->find();

    return !empty($association) ? $object->findByPk($association->dst_id) : null;
  }

  /**
   * @param CActiveRecord $object
   * @param $reverseMode
   *
   * @return array
   */
  public function findAllThroughAssociation(CActiveRecord $object, $reverseMode = false)
  {
    $ids = array();

    $source      = $reverseMode ? $object : $this;
    $destination = $reverseMode ? $this : $object;
    $key         = $reverseMode ? 'src_id' : 'dst_id';
    $id          = $reverseMode ? $this->id : null;

    foreach( Association::model()->source($source)->destination($destination, $id)->findAll() as $association )
      $ids[] = $association->{$key};

    return $object->findAllByPk($ids);
  }
}