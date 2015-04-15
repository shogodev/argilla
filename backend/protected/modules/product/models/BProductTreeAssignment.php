<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductTreeAssignment model(string $class = __CLASS__)
 *
 * @property string $src
 * @property integer $src_id
 * @property string $dst
 * @property integer $dst_id
 */
class BProductTreeAssignment extends BActiveRecord
{
  private $foundDstId;
  private $foundDst;

  public function rules()
  {
    return array(
      array('dst, dst_id, src, src_id', 'required'),
      array('dst_id, src_id', 'length', 'max' => 10),
    );
  }

  public function afterFind()
  {
    $this->foundDst = $this->dst;
    $this->foundDstId = $this->dst_id;

    parent::afterFind();
  }

  public function afterSave()
  {
    if( !$this->isNewRecord )
    {
      if( $this->foundDst == $this->dst && $this->foundDstId != $this->dst_id )
      {
        $field = BProductStructure::getRowName($this->foundDst);

        $criteria = new CDbCriteria();
        $criteria->compare(BProductStructure::getRowName($this->src), $this->src_id);
        $criteria->compare($field, $this->foundDstId);
        $command = $this->dbConnection->schema->commandBuilder->createUpdateCommand(BProductAssignment::model()->tableName(), array($field => $this->dst_id), $criteria);
        $command->execute();

        $modelName = BProductStructure::getModelName($field);
        /**
         * @var BProductStructure $model
         */
        $model = $modelName::model()->findByPk($this->dst_id);
        $model->updateVisibility();
      }
    }

    parent::afterSave();
  }
}