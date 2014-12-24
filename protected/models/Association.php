<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @property string $src
 * @property string $src_frontend
 * @property int $src_id
 * @property string $dst
 * @property string $dst_frontend
 * @property int $dst_id
 *
 * @method static Association model(string $class = __CLASS__)
 */
class Association extends FActiveRecord
{
  private $modelName;

  public function getKeys()
  {
    return CHtml::listData($this->getData(), 'pk', 'pk');
  }

  /**
   * @param CDbCriteria|null $criteria
   * @param string|null $sorting
   * @param bool $pagination
   * @param string|null $filters
   *
   * @return BaseList
   * @throws CHttpException
   */
  public function getList(CDbCriteria $criteria = null, $sorting = null, $pagination = false, $filters = null)
  {
    $className = $this->modelName.'List';
    if( !class_exists($className) )
      throw new CHttpException(500, 'Не удалось найти класс '.$className);

    $criteria = $this->getAssociationCriteria($criteria);

    return Yii::createComponent($className, $criteria, $sorting, $pagination, $filters);
  }

  /**
   * @param CDbCriteria|null $criteria
   * @return array|FActiveRecord[]
   */
  public function getModels(CDbCriteria $criteria = null)
  {
    $criteria = $this->getAssociationCriteria($criteria);

    /**
     * @var FActiveRecord $model
     */
    $model = new $this->modelName;
    return $model->findAll($criteria);
  }

  /**
   * @param FActiveRecord $model
   * @param string $targetModelName
   *
   * @return $this
   */
  public function setSource(FActiveRecord $model, $targetModelName)
  {
    $this->modelName = $targetModelName;

    $criteria = new CDbCriteria();
    $criteria->select = 'dst_id AS pk';

    $criteria->compare('src_frontend', get_class($model));
    $criteria->compare('src_id', $model->primaryKey);
    $criteria->compare('dst_frontend', $this->modelName);
    $this->setDbCriteria($criteria);

    return $this;
  }

  /**
   * @param FActiveRecord $model
   * @param string $targetModelName
   *
   * @return $this
   */
  public function setDestination(FActiveRecord $model, $targetModelName)
  {
    $this->modelName = $targetModelName;

    $criteria = new CDbCriteria();
    $criteria->select = 'src_id AS pk';

    $criteria->compare('dst_frontend', get_class($model));
    $criteria->compare('dst_id', $model->primaryKey);
    $criteria->compare('src_frontend', $this->modelName);
    $this->setDbCriteria($criteria);

    return $this;
  }

  private function getData()
  {
    $command = $this->dbConnection->schema->commandBuilder->createFindCommand($this->tableName(), $this->getDbCriteria());
    return $command->queryAll();
  }

  /**
   * @param CDbCriteria|null $criteria
   *
   * @return CDbCriteria
   */
  private function getAssociationCriteria(CDbCriteria $criteria = null)
  {
    if( !$criteria )
      $associationCriteria = new CDbCriteria();
    else
      $associationCriteria = clone $criteria;

    $associationCriteria->addInCondition('t.id', $this->getKeys());

    return $associationCriteria;
  }
}