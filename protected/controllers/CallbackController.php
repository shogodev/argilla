<?php
class CallbackController extends FController
{
  public function actionIndex()
  {
    $this->callbackForm->ajaxValidation();

    if( $this->callbackForm->save() )
    {
      $this->callbackForm->sendNotification();
      $this->callbackForm->responseSuccess('<p class="bb">Спасибо! Ваша заявка принята, ожидайте звонок в ближайшее время.</p>');
    }
  }

  public function filters()
  {
    return array('ajaxOnly + index');
  }
}