<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class CommonAssociation
 *
 * @method static CommonAssociation model(string $class = __CLASS__)
 *
 * @property integer $product_id
 * @property string $tag
 */
class CommonAssociation extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_common_association}}';
  }

  private $modelName = 'Product';

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
   * @param bool $withMe
   *
   * @return $this
   */
  public function setTagByModel(FActiveRecord $model, $withMe = false)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 'product_id AS pk';

    if( $commonAssociation = $this->findByAttributes(array('product_id' => $model->primaryKey)) )
    {
      $criteria->compare('tag', $commonAssociation->tag);
      if( !$withMe )
        $criteria->compare('product_id', '<>'.$model->primaryKey);
      else
        $criteria->compare('product_id', '='.$model->primaryKey, false, 'OR');
    }
    else
      $criteria->compare('tag', 0);

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