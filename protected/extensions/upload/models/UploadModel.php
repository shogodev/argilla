<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 21.09.12
 */
class UploadModel extends CModel
{
  public $size;

  public $type;

  public $notice;

  public $position;

  protected $id;

  protected $parent;

  protected $name;

  protected $tableName;

  private $db;

  public function __construct($table = null)
  {
    $this->db        = Yii::app()->db;
    $this->tableName = $table;
  }

  public function attributeNames()
  {
    return array('size', 'type', 'notice', 'position');
  }

  public function findByPk($id)
  {
    $result = $this->db->createCommand()->select()->from("{{{$this->tableName}}}")
      ->where('id=:id', array(':id' => $id))
      ->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array($this->tableName))
      ->queryAll();

    return Arr::reset($result);
  }

  public function save()
  {
    $params = array();
    foreach($this->attributeNames() as $param)
      if( $this->{$param} !== null )
        $params[$param] = $this->{$param};

    return $this->db->createCommand()->update("{{{$this->tableName}}}", $params, 'id=:id', array(':id' => $this->id));
  }
}