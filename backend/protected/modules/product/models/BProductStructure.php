<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
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

  protected function updateVisibility()
  {
    $pk = $this->getPrimaryKey();
    !empty($this->visible) ? $this->setVisible($pk) : $this->unsetVisible($pk);
  }

  /**
   * @param $pk
   */
  protected function setVisible($pk)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 't.id';
    $criteria->distinct = true;
    $criteria->compare($this->getRowName(get_called_class()), $pk);

    foreach(array_keys($this->assignmentModel->getFields()) as $row)
    {
      $class = $this->getModelName($row);
      /**
       * @var BActiveRecord $model
       */
      $model = new $class;
      $criteria->join .= 'JOIN '.$model->tableName().' AS '.$class.' ON t.'.$row.' = '.$class.'.id AND '.$class.'.visible=1 ';
    }

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $ids = $builder->createFindCommand($this->assignmentTable, $criteria)->queryColumn();

    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $ids);
    $command = $builder->createUpdateCommand($this->assignmentTable, array('visible' => 1), $criteria);
    $command->query();
  }

  /**
   * @param $pk
   */
  protected function unsetVisible($pk)
  {
    $criteria = new CDbCriteria();
    $criteria->compare($this->getRowName(get_called_class()), $pk);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createUpdateCommand($this->assignmentTable, array('visible' => 0), $criteria);
    $command->query();
  }

  /**
   * @param $class
   *
   * @return string
   */
  protected function getRowName($class)
  {
    return lcfirst(str_replace($this->classPrefix, '', $class)).'_id';
  }

  /**
   * @param $row
   *
   * @return string
   */
  protected function getModelName($row)
  {
    return $this->classPrefix.ucfirst(str_replace('_id', '', $row));
  }
}