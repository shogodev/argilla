<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductParam model(string $class = __CLASS__)
 *
 * @property integer $param_id
 * @property integer $product_id
 * @property integer $variant_id
 * @property string $value
 *
 * @property BProductParamName $param
 * @property BProduct $product
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

  public function getParameters(BProduct $product)
  {
    // устанавливаем привязку параметров
    $paramNames = new BProductParamName();
    $paramNames->section_id = $product->section_id;

    $parameters = array();
    $names      = $paramNames->search();

    foreach($names->data as $name)
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
            $name->value = $param->variant_id;
            break;

          default:
            $name->value[] = $param->variant_id;
        }
      }

      $parameters[] = $name;
    }

    return $parameters;
  }

  public function setParameters(BProduct $product, array $parameters)
  {
    self::model()->deleteAllByAttributes(array('product_id' => $product->id));
    $types = BProductParamName::model()->getParameterTypes(array_keys($parameters));

    foreach($parameters as $id => $param)
    {
      if( !is_array($param['value']) )
        $param['value'] = array($param['value']);

      foreach($param['value'] as $value)
      {
        if( $value === '' )
          continue;

        $variant_id = null;

        // из-за ограничения по внешним ключам записываем id варианта и текстовое значение
        // в разные столбцы таблицы параметров
        if( !in_array($types[$id], array('text', 'slider')) )
        {
          $variant_id = $value;
          $value      = null;
        }

        $model = new BProductParam();
        $model->setAttributes(array('param_id'   => $id,
                                    'product_id' => $product->id,
                                    'variant_id' => $variant_id,
                                    'value'      => $value));
        if( !$model->save() )
        {
          throw new CHttpException(500, 'Не удается сохранить параметры продукта');
        }
      }
    }
  }


}