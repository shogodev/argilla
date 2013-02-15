<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
 */
class ResponseController extends FController
{
  public function filters()
  {
    return array(
      'ajaxOnly + add',
    );
  }

  public function actionAdd()
  {
    $form = new FForm('Response', new Response());

    $attributes = Yii::app()->request->getPost('Response');
    $model      = Product::model()->visible()->findByPk(Arr::get($attributes, 'product_id'));

    if( !$model )
      throw new CHttpException(404, 'Товар не существует');

    if( $form->save() )
    {
      $form->sendNotification();
      $form->responseSuccess(CHtml::tag('b', array('class' => 'm20 bb red'), 'Ваш отзыв успешно отправлен.'));
    }
  }
}