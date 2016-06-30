<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.behaviors.BaseDaoBehavior');

class DaoProductBehavior extends BaseDaoBehavior
{
  public function getProductsWithoutModifications(CDbCriteria $criteria)
  {
    $criteria->addCondition('t.parent IS NULL');

    $command = $this->builder->createFindCommand(self::PRODUCT_TABLE, $criteria);
    return $command->queryAll();
  }

  public function getProductModifications(CDbCriteria $criteria)
  {
    $criteria->addCondition('NOT t.parent IS NULL');

    $command = $this->builder->createFindCommand(self::PRODUCT_TABLE, $criteria);
    return $command->queryAll();
  }

  public function getProductsWithModification(CDbCriteria $criteria)
  {
    $command = $this->builder->createFindCommand(self::PRODUCT_TABLE, $criteria);
    return $command->queryAll();
  }

  public function updateProduct($data, $id)
  {
    $criteria = new CDbCriteria ();
    $criteria->compare('id', $id);

    $command = $this->builder->createUpdateCommand(self::PRODUCT_TABLE, $data, $criteria);
    return $command->execute();
  }
}