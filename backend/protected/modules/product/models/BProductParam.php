<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductParam model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $param_id
 * @property integer $product_id
 * @property integer $variant_id
 * @property string $value
 *
 * @property BProductParamName $param
 * @property BProductParamVariant $variant
 */
class BProductParam extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('param_id, product_id', 'required'),
      array('param_id, product_id', 'length', 'max' => 10),
      array('variant_id, value', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'param' => array(self::BELONGS_TO, 'BProductParamName', 'param_id'),
      'variant' => array(self::BELONGS_TO, 'BProductParamVariant', 'variant_id'),
    );
  }

  public function getParameters(BProduct $product)
  {
    $paramName = new BProductParamName();
    $criteria = new CDbCriteria();
    $criteria->order = 't.position';
    $criteria->with = array('assignment', 'variants');
    $criteria->addInCondition('assignment.section_id', is_array($product->section_id) ? $product->section_id : array($product->section_id));
    $criteria->addCondition('(assignment.section_id IS NULL OR assignment.section_id = 0)', 'OR');
    $criteria->compare('t.id', '<>'.BProductParamName::ROOT_ID);
    $criteria->compare('parent', BProductParamName::ROOT_ID);

    $parameters = array();
    foreach($paramName->buildParams($criteria)->getData() as $name)
    {
      // todo: переписать на dao и вынести из цикла
      $params = self::model()->findAllByAttributes(array('param_id' => $name->id, 'product_id' => $product->id));

      foreach($params as $param)
      {
        switch($name->type)
        {
          case 'text':
          case 'slider':
            $name->value = $param->value;
            break;

          case 'radio':
            $name->value[$param->id] = $param->variant_id;
            break;

          default:
            $name->value[$param->id] = $param->variant_id;
        }
      }

      $parameters[] = $name;
    }

    return $parameters;
  }

  public function saveParameters(BProduct $product, array $parameters)
  {
    $types    = BProductParamName::model()->getParameterTypes(array_keys($parameters));
    $savedIds = array();

    foreach($parameters as $id => $param)
    {
      $param['value'] = !is_array($param['value']) ? array($param['value']) : $param['value'];

      foreach($param['value'] as $value)
      {
        if( $value === '' )
          continue;

        $attributes = array(
          'param_id' => $id,
          'product_id' => $product->id,
          'variant_id' => $this->getVariantId($id, $value, $types),
          'value' => $value,
        );

        $model = $this->saveParameter($attributes);
        $savedIds[] = $model->id;
      }
    }

    $this->deleteProductParameters($product, $savedIds);
  }

  /**
   * @param $id
   * @param $value
   * @param array $types
   *
   * @return null
   */
  protected function getVariantId($id, $value, array $types)
  {
    // из-за ограничения по внешним ключам записываем id варианта и текстовое значение
    // в разные столбцы таблицы параметров
    return !in_array($types[$id], array('text', 'slider')) ? $value : null;
  }

  /**
   * @param array $attributes
   *
   * @return BProductParam
   * @throws CHttpException
   */
  protected function saveParameter(array $attributes)
  {
    if( !$model = $this->findByAttributes(Arr::extract($attributes, array('param_id', 'product_id', 'variant_id'))) )
    {
      $model = new BProductParam();
    }

    $model->setAttributes($attributes);
    $model->value = !isset($attributes['variant_id']) ? $attributes['value'] : null;

    if( !$model->save() )
    {
      throw new CHttpException(500, 'Не удается сохранить параметры продукта');
    }

    return $model;
  }

  /**
   * @param BProduct $product
   * @param array $savedIds
   */
  protected function deleteProductParameters(BProduct $product, array $savedIds)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product_id', $product->id);
    $criteria->addNotInCondition('id', $savedIds);
    $this->deleteAll($criteria);
  }
}