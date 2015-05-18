<?php
Yii::import('system.gii.generators.form.FormCode');

class BFormCode extends FormCode
{
  protected $_modelClass;

  public $skipCheckSafe = array('img');

  public function getModelAttributes($noFilterAttributes = array())
  {
    $modelClass = $this->getModelClass();
    $model = new $modelClass();

    $attributes = $model->attributeNames();
    $safeAttributes = parent::getModelAttributes();

    $noFilterAttributes = CMap::mergeArray($noFilterAttributes, $this->skipCheckSafe);

    foreach($attributes as $key => $attribute)
    {
      if( array_search($attribute, $safeAttributes) === false && !in_array($attribute, $noFilterAttributes) )
        unset($attributes[$key]);
    }

    return $attributes;
  }

  public function findRelatedModel(CActiveRecord $model, $foreignKey)
  {
    foreach($model->relations() as $data)
    {
      if( Arr::get($data, 2) == $foreignKey )
        return Arr::get($data, 1);
    }

    return null;
  }

  public function findRelation(CActiveRecord $model, $foreignKey)
  {
    foreach($model->relations() as $name => $data)
    {
      if( Arr::get($data, 2) == $foreignKey )
        return $name;
    }

    return null;
  }
}