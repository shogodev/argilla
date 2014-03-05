<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.ar
 */
class FActiveRecord extends CActiveRecord
{
  /**
   * @param string $className
   *
   * @return FActiveRecord
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
    return '{{'.Utils::toSnakeCase(get_class($this)).'}}';
  }

  public function attributeLabels()
  {
    return array(
      'name' => 'Имя',
      'content' => 'Сообщение',
    );
  }

  /**
   * @param FActiveRecord $object
   *
   * @return FActiveRecord
   */
  public function findThroughAssociation(FActiveRecord $object)
  {
    /**@var Association $association*/
    $association = Association::model()->setSource($this)->setDestination($object)->find();

    return !empty($association) ? $object->findByPk($association->dst_id) : null;
  }

  /**
   * @param FActiveRecord $object
   * @param $reverseMode
   *
   * @return FActiveRecord[]
   */
  public function findAllThroughAssociation(FActiveRecord $object, $reverseMode = false)
  {
    $ids = array();

    $source      = $reverseMode ? $object : $this;
    $destination = $reverseMode ? $this : $object;
    $key         = $reverseMode ? 'src_id' : 'dst_id';
    $id          = $reverseMode ? $this->id : null;

    foreach(Association::model()->setSource($source)->setDestination($destination)->findAll() as $association)
      $ids[] = $association->{$key};

    return $object->findAllByPk($ids);
  }
}