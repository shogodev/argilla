<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ManyRecordInserter extends CComponent
{
  /**
   * @var CDbCommandBuilder
   */
  private $builder;

  private $data = array();

  private $table;

  private $insertChunkSize;

  public function __construct($table, $insertChunkSize = 25000)
  {
    $this->table = $table;
    $this->insertChunkSize = $insertChunkSize;
    $this->builder = Yii::app()->db->commandBuilder;
  }

  public function addAttributes($attributes)
  {
    $this->data[] = $attributes;
  }

  public function setAttributesList($attributesList)
  {
    $this->data = $attributesList;
  }

  /**
   * @param bool $writeNow = false
   *
   * @return int $count
   */
  public function save($writeNow = false)
  {
    if( empty($this->data) )
    {
      $this->onSave(new CEvent($this, array('count' => 0)));
      return 0;

    }

    if( count($this->data) >= $this->insertChunkSize || $writeNow )
    {
      $command = $this->builder->createMultipleInsertCommand($this->table, $this->data);
      $count = $command->query()->count();

      $this->data = array();
      $this->onSave(new CEvent($this, array('count' => $count)));

      return $count;
    }
  }

  public function onSave(CEvent $event)
  {
     $this->raiseEvent('onSave', $event);
  }
}