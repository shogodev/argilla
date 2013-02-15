<?php
class CallbackController extends FController
{
  public function actionIndex()
  {
    $callback_form = $this->getCallbackForm();

    if( $callback_form->save() )
    {
      $callback_form->sendNotification();
      $callback_form->responseSuccess('<p class="bb">Спасибо! Ваша заявка принята, ожидайте звонок в ближайшее время.</p>');
    }
  }

  public function filters()
  {
    return array('ajaxOnly + index');
  }
}