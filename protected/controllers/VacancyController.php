<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
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