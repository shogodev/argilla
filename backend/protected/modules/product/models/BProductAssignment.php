<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductAssignment model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $visible
 */
class BProductAssignment extends BActiveRecord
{
  public function getFields()
  {
    $fields = $this->getMetaData()->columns;
    unset($fields['id'], $fields['product_id'], $fields['visible']);

    return $fields;
  }

  /**
   * Получаем данные зависимого атрибута
   *
   * @param $attribute
   * @param $dependedAttribute
   *
   * @return array
   */
  public function getDepends($attribute, $dependedAttribute)
  {
    $attribute = $this->toToAssignmentAttribute($attribute);
    $dependedAttribute = $this->toToAssignmentAttribute($dependedAttribute);

    $criteria = $this->getCriteria($attribute, $dependedAttribute);
    $data = $this->getDependedModels($dependedAttribute, $criteria);

    return $data;
  }

  /**
   * Формируем данные для DropDownList, чтобы перестроить список при ajax запросе
   *
   * @param $model
   * @param $attribute
   * @param $data
   *
   * @return string
   */
  public function getListOptions($model, $attribute, $data)
  {
    $data = CHtml::listData($data, 'id', 'name');
    $options = CHtml::tag('option', array('value' => ''), 'Не задано', true);

    foreach($data as $value => $name)
      $options .= CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);

    return $options;
  }

  public function getCheckBoxListOptions($model, $attribute, $data)
  {
    $data = CHtml::listData($data, 'id', 'name');

    $form = new BActiveForm();
    return $form->checkBoxList($model, $attribute, $data);
  }

  public function renderAjaxHtml($model, $attribute, $depended)
  {
    $data = $this->getDepends($attribute, $depended);

    $relationName = BProductStructure::getRelationName(BProductStructure::getModelName($depended));
    $relation = $model->getActiveRelation($relationName);
    $type = $relation instanceof CHasManyRelation ? 'checkboxlist' : 'dropdown';

    switch($type)
    {
      case 'dropdown':
        return $this->getListOptions($model, $depended, $data);
        break;
      case 'checkboxlist':
        return $this->getCheckBoxListOptions($model, $depended, $data);
        break;
    }
  }

  /**
   * @param $modelClass
   * @param null|CDbCriteria $criteria
   */
  public function getDependedModels($modelClass, $criteria = null)
  {
    $modelClass = $this->toToAssignmentAttribute($modelClass);
    $modelClass = 'BProduct'.ucfirst($modelClass);
    $model      = $modelClass::model();

    return $model->findAll($criteria);
  }

  public function saveAssignments($product, $assignments)
  {
    $saved = array();
    $attributes = $this->calculateAttributes($assignments);

    foreach($attributes as $assignment)
    {
      $model = $this->saveAssignment($product, $assignment);
      $saved[] = $model->id;
    }

    $this->deleteAssignments($product, $saved);
  }

  public function toProductAttribute($attribute)
  {
    return $attribute.'_id';
  }

  public function toToAssignmentAttribute($attribute)
  {
    return str_replace("_id", "", $attribute);
  }

  public function getSections($criteria = null)
  {
    return BProductSection::model()->findAll($criteria);
  }

  public function getTypes($criteria = null)
  {
    return BProductType::model()->findAll($criteria);
  }

  /**
   * @param $assignments
   *
   * @return array
   */
  protected function calculateAttributes($assignments)
  {
    $arrays = $digits = array();
    foreach($assignments as $key => $item)
      is_array($item) ? $arrays[$key] = $item : $digits[$key] = $item;

    if( !empty($arrays) )
    {
      $statistics = new Statistics($arrays);
      $combinations = $statistics->getCombinations();

      $attributes = array();
      foreach($combinations as $i => $combination)
      {
        foreach($this->getFields() as $field)
        {
          if( in_array($field->name, array_keys($digits)) )
            $attributes[$i][$field->name] = $digits[$field->name];
          else
            $attributes[$i][$field->name] = $combination[array_flip(array_keys($arrays))[$field->name]];
        }
      }
    }
    else
    {
      $attributes = array($digits);
    }

    return $attributes;
  }

  /**
   * @param BProduct $product
   * @param array $attributes
   * @return BProductAssignment|CActiveRecord
   * @throws CHttpException
   */
  protected function saveAssignment(BProduct $product, array $attributes)
  {
    $attributes['product_id'] = $product->id;

    if( !$model = $this->findByAttributes($attributes) )
      $model = new BProductAssignment();

    $model->setAttributes($attributes, false);
    if( !$model->save() )
      throw new CHttpException(500, 'Не удается сохранить модель привязок продукта');

    return $model;
  }

  protected function deleteAssignments($product, array $savedIds)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product_id', $product->id);
    $criteria->addNotInCondition('id', $savedIds);
    $this->deleteAll($criteria);
  }

  /**
   * Формируем критерий для выборки - массив id подходящих по зависимости атрибутов
   *
   * @param $attribute
   * @param $dependedAttribute
   *
   * @return CDbCriteria
   */
  protected function getCriteria($attribute, $dependedAttribute)
  {
    $dstAttribute = $this->toProductAttribute($attribute);

    $attributes = array(
      'src' => $dependedAttribute,
      'dst' => $attribute,
      'dst_id' => $this->{$dstAttribute}
    );

    $assignments = BProductTreeAssignment::model()->findAllByAttributes($attributes);
    $ids = array_map(function($model){return $model->src_id;}, $assignments);

    $criteria = new CDbCriteria();
    $criteria->addInCondition("id", $ids);

    return $criteria;
  }

  protected function beforeSave()
  {
    $this->visible = true;

    foreach(array_keys($this->getFields()) as $row)
    {
      $class = 'BProduct'.ucfirst($this->toToAssignmentAttribute($row));
      /**
       * @var BActiveRecord $model
       */
      $model = new $class;

      if( !($model instanceof BProductStructure) )
      {
        throw new CException('Model '.get_class($model).' must implement BProductStructure class');
      }

      if( $this->{$row} && $model = $model->findByPk($this->{$row}) )
      {
        $this->visible = $this->visible && $model->visible;
      }

      if( !$this->visible )
      {
        break;
      }
    }

    $this->visible = intval($this->visible);
    return parent::beforeSave();
  }
}