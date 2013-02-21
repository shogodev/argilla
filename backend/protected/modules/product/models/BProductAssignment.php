<?php

/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductAssignment model(string $class = __CLASS__)
 *
 * @property integer $product_id
 */
class BProductAssignment extends BActiveRecord
{
  public function getFields()
  {
    $fields = $this->getMetaData()->columns;
    unset($fields['id']);
    unset($fields['product_id']);

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
    $attribute         = $this->toToAssignmentAttribute($attribute);
    $dependedAttribute = $this->toToAssignmentAttribute($dependedAttribute);

    $criteria          = $this->getCriteria($attribute, $dependedAttribute);
    $data              = $this->getDependedModels($dependedAttribute, $criteria);

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
    $data    = CHtml::listData($data, 'id', 'name');
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

  public function renderAjaxHtml($model, $type, $attribute, $depended)
  {
    $data = $this->getDepends($attribute, $depended);

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
    $this->deleteAssignments($product);
    $arrays = array();

    foreach($assignments as $key => $item)
      if( is_array($item) )
        $arrays[$key] = count($item);

    if( !empty($arrays) )
    {
      foreach($arrays as $paramKey => $count)
      {
        reset($assignments[$paramKey]);

        for($i = 0; $i < $count; $i++)
        {
          $model = new BProductAssignment();
          $model->product_id = $product->id;

          foreach($assignments as $param => $value)
          {
            $value = is_array($value) ? current($assignments[$paramKey]) : $value;
            $model->setAttribute($param, $value);
          }
          $model->save();
          next($assignments[$paramKey]);
        }
      }
    }
    else
    {
      $model = new BProductAssignment();
      $model->setAttributes($assignments, false);
      $model->product_id = $product->id;
      if( !$model->save() )
      {
        throw new CHttpException(500, 'Не могу сохранить модель привязок продукта');
      }
    }
  }

  public function deleteAssignments($product)
  {
    BProductAssignment::model()->deleteAllByAttributes(array('product_id' => $product->id));
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
    $ids         = array_map(function($model){return $model->src_id;}, $assignments);

    $criteria = new CDbCriteria();
    $criteria->addInCondition("id", $ids);

    return $criteria;
  }
}