<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
abstract class CackleAbstractManager
{
  /**
   * @var CackleApi
   */
  protected $cackleApi;

  public function __construct()
  {
    $this->cackleApi = new CackleApi();
  }

  public function insert($item)
  {
    $this->save($this->getNewModel(), $item);
  }

  public function update($item)
  {
    $this->save($this->getModel()->findByPk($item->id), $item);
  }

  public function clearAll()
  {
    $this->getModel()->deleteAll();
  }

  public function getLastModified()
  {
    $criteria = new CDbCriteria();
    $criteria->order = 'modified DESC';

    $model = $this->getModel()->find($criteria);

    return $model ? $model->modified : 0;
  }

  public function getIdsForUpdate()
  {
    $criteria = new CDbCriteria();
    $criteria->select = 't.id';

    $builder = Yii::app()->getDb()->getCommandBuilder();
    $command = $builder->createFindCommand($this->getModel()->tableName(), $criteria);

    return CHtml::listData($command->queryAll(), 'id', 'id');
  }

  /**
   * @return FActiveRecord
   */
  abstract protected function getModel();

  /**
   * @return FActiveRecord
   */
  abstract protected function getNewModel();

  abstract protected  function save(FActiveRecord $comment, $item);
} 