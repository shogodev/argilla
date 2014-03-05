<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
abstract class BAbstractModelCopier
{
  /**
   * @param BActiveRecord $model
   * @param mixed $unsetAttributes
   * @param mixed $setAttributes
   *
   * @return BActiveRecord
   */
  protected function copyModel(BActiveRecord $model, $unsetAttributes = null, $setAttributes = null)
  {
    if( $unsetAttributes === null )
    {
      $unsetAttributes = array('visible');

      if( ($pk = $this->getPrimaryColumn($model)) && is_string($pk) )
        $unsetAttributes[] = $pk;
    }

    if( $setAttributes === null )
      $setAttributes = array();

    $copy = clone $model;
    $copy->isNewRecord = true;
    $this->unsetAttributes($copy, $unsetAttributes);
    $this->setAttributes($copy, $setAttributes);
    $copy->setScenario('copy');
    $copy->save(false);

    return $model->findByPk($copy->getPrimaryKey());
  }

  /**
   * @param BActiveRecord $model
   * @param array $attributes
   */
  protected function unsetAttributes(BActiveRecord $model, array $attributes = array())
  {
    foreach($model->getValidators() as $validator)
      if( $validator instanceof CUniqueValidator )
        foreach($validator->attributes as $attribute)
          $model->{$attribute} = null;

    foreach($attributes as $attribute)
      if( isset($model->{$attribute}) )
        $model->{$attribute} = null;
  }

  /**
   * @param BActiveRecord $model
   * @param array $attributes
   */
  protected function setAttributes(BActiveRecord $model, $attributes = array())
  {
    foreach($attributes as $attribute => $value)
      if( $model->hasAttribute($attribute) )
        $model->setAttribute($attribute, $value);
  }

  /**
   * @param BActiveRecord $copyModel
   * @param BActiveRecord $originModel
   * @param $relationName
   * @return bool
   */
  protected function copyRelations(BActiveRecord $copyModel, BActiveRecord $originModel, $relationName)
  {
    $relation      = $originModel->getActiveRelation($relationName);
    $relatedModels = $originModel->getRelated($relationName);

    if( !is_array($relatedModels) )
      $relatedModels = array($relatedModels);

    //todo: сделать обработку для составных внешних ключей
    $fk = $relation->foreignKey;
    if( is_array($fk) )
      return false;

    $result = true;

    foreach($relatedModels as $relatedModel)
    {
      $pk = $this->getPrimaryColumn($relatedModel);

      $copy = $this->copyModel(
        $relatedModel,
        CMap::mergeArray(array($fk), is_array($pk) ? array() : array($pk)),
        array($fk => $copyModel->getPrimaryKey())
      );

      $result = $copy && $result;
    }

    return $result;
  }

  /**
   * @param BActiveRecord $model
   *
   * @return array|string
   */
  protected function getPrimaryColumn(BActiveRecord $model)
  {
    return $model->getMetaData()->tableSchema->primaryKey;
  }

  abstract public function copy();
}