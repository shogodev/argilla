<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductStructure model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $visible
 */
class BProductStructure extends BActiveRecord
{
  protected $assignmentClass = 'BProductAssignment';

  /**
   * @var BProductAssignment
   */
  protected $assignmentModel;

  protected $assignmentTable;

  protected $previousState;

  protected $classPrefix = 'BProduct';

  public function init()
  {
    if( !array_key_exists('visible', $this->attributes) )
    {
      throw new CException('Model does not contain `visible` attribute');
    }

    $this->assignmentModel = new $this->assignmentClass;
    $this->assignmentTable = $this->assignmentModel->tableName();

    parent::init();
  }

  protected function beforeSave()
  {
    $model = $this->findByPk($this->getPrimaryKey());
    $this->previousState = $model ? $model->visible : 0;
    return parent::beforeSave();
  }

  protected function afterSave()
  {
    if( $this->previousState != $this->visible )
    {
      $this->updateVisibility();
    }

    parent::afterSave();
  }

  private function updateVisibility()
  {
    $pk = $this->getPrimaryKey();
    !empty($this->visible) ? $this->setVisible($pk) : $this->unsetVisible($pk);
  }

  /**
   * @param integer $pk
   */
  private function setVisible($pk)
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand($this->assignmentTable, $this->buildVisibleCriteria($pk));
    $values = array();

    foreach($command->queryAll() as $row)
    {
      $id = Arr::cut($row, 'id');

      if( !in_array('0', $row, true) )
      {
        $values[] = $id;
      }
    }

    if( !empty($values) )
    {
      $criteria = new CDbCriteria();
      $criteria->addInCondition('id', $values);
      $command = $builder->createUpdateCommand($this->assignmentTable, array('visible' => 1), $criteria);
      $command->query();
    }
  }

  /**
   * @param integer $pk
   */
  private function unsetVisible($pk)
  {
    $criteria = new CDbCriteria();
    $criteria->compare($this->getRowName(get_called_class()), $pk);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createUpdateCommand($this->assignmentTable, array('visible' => 0), $criteria);
    $command->query();
  }

  /**
   * @param $pk
   *
   * @return CDbCriteria
   */
  private function buildVisibleCriteria($pk)
  {
    $criteria = new CDbCriteria();
    $criteria->select = array('t.id');
    $criteria->distinct = true;
    $criteria->compare($this->getRowName(get_called_class()), $pk);

    foreach(array_keys($this->assignmentModel->getFields()) as $row)
    {
      $class = $this->getModelName($row);
      /**
       * @var BActiveRecord $model
       */
      $model = new $class;
      $criteria->select[] = $class.'.visible AS '.$row.'_visible';
      $criteria->join[] = 'LEFT OUTER JOIN '.$model->tableName().' AS '.$class.' ON t.'.$row.' = '.$class.'.id';
    }

    $criteria->join = implode(' ', $criteria->join);

    return $criteria;
  }

  /**
   * @param $class
   *
   * @return string
   */
  private function getRowName($class)
  {
    return lcfirst(str_replace($this->classPrefix, '', $class)).'_id';
  }

  /**
   * @param $row
   *
   * @return string
   */
  private function getModelName($row)
  {
    return $this->classPrefix.ucfirst(str_replace('_id', '', $row));
  }
}