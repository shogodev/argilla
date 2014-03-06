<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
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