<?php
class CallbackController extends FController
{
  public function actionIndex()
  {
    $this->callbackForm->ajaxValidation();

    if( $this->callbackForm->save() )
    {
      $this->callbackForm->sendNotification();
      $this->callbackForm->responseSuccess(
        $this->textBlockRegister('Обратный звонок', '<div class="m7">Ваша заявка принята.</div><div>Наш менеджер свяжется с вами в ближайшее время.</div>', null)
      );
    }
  }

  public function filters()
  {
    return array('ajaxOnly + index');
  }
}