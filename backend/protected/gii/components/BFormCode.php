<?php
Yii::import('system.gii.generators.form.FormCode');

class BFormCode extends FormCode
{
  protected $_modelClass;

  public function getModelAttributes()
  {
    $modelClass = $this->getModelClass();
    $model = new $modelClass();

    $attributes = $model->attributeNames();
    $safeAttributes = parent::getModelAttributes();

    foreach($attributes as $key => $attribute)
      if( array_search($attribute, $safeAttributes) === false )
        unset($attributes[$key]);

    return $attributes;
  }
}