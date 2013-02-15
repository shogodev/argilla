<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
 */
class VacancyController extends FController
{
  public function filters()
  {
    return array(
      'postOnly + add',
    );
  }

  public function actionAdd()
  {
    $scenario = Yii::app()->request->isAjaxRequest ? '' : 'upload';
    $model = new Vacancy($scenario);
    $form  = new FForm('Vacancy', $model);

    if( $form->save() )
    {
      $form->sendNotification();
      $form->successMessage = CHtml::tag('div', array('class' => 'm20 bb center'), 'Ваше резюме успешно отправлено.');
    }

    $this->redirect($form->returnUrl);
  }
}