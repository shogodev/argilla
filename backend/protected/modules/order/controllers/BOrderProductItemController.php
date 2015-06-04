<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BOrderProductItemController extends BController
{
  public $name = 'Параметры заказа';

  public $modelClass = 'BOrderProductItem';

  public $showInMenu = false;

  public function actionParameters($id)
  {
    if( $model = BOrderProduct::model()->findByPk($id))
    {
      if( $elements = Yii::app()->request->getPost('elements') )
      {
        $this->addParameter($elements, $model);
      }
      else
      {
        $this->render('index', array(
          'model' => new BProductParam(),
          'dataProvider' => $model->getOrderParametersDataProvider(),
        ));
      }
    }
  }

  /**
   * @param array $elements
   * @param BOrderProduct $orderProduct
   *
   * @throws CDbException
   * @throws CHttpException
   */
  public function addParameter($elements, $orderProduct)
  {
    if( $orderProduct && $elements )
    {
      Yii::app()->db->beginTransaction();

      foreach($elements as $parameterId => $element)
      {
        if( $element === "true" && $parameter = BProductParam::model()->findByPk($parameterId))
        {
          $orderProductItem = new BOrderProductItem();
          $orderProductItem->setAttributes(array(
            'order_product_id' => $orderProduct->id,
            'type' => 'ProductParameter',
            'pk' => $parameter->id,
            'name' => $parameter->param->name,
            'value' => $parameter->variant->name
          ), false);

          if( !$orderProductItem->save() )
            throw new CHttpException(500, 'Ошибка добасления праметра заказа');
        }
      }

      Yii::app()->db->getCurrentTransaction()->commit();
    }
  }
} 