<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class TemplateController extends FController
{
  protected function beforeAction($action)
  {
    if( !YII_DEBUG )
    {
      throw new CHttpException(404, 'Страница не найдена');
    }

    return parent::beforeAction($action);
  }

  public function actionIndex($url)
  {
    if( !file_exists(Yii::getPathOfAlias('frontend.views.template').DIRECTORY_SEPARATOR.$url.'.php') )
      throw new CHttpException(500, 'Файл '.$url.' не найден');

    $this->breadcrumbs = array('Шаблон '.$url);

    if( file_exists(Yii::getPathOfAlias('frontend.views.layouts').DIRECTORY_SEPARATOR.$url.'.php') )
      $this->layout = $url;

    $this->render($url);
  }

  public function actionBasketThirdStep()
  {
    //$this->basket->clear();
    Yii::app()->session['orderSuccess'] = true;
    Yii::app()->session['orderId'] = 1234;
    $this->forward('order/thirdStep', false);
  }

  public function actionProfileData($url)
  {
    $login = new Login();
    $login->login = Yii::app()->request->getParam('empty', false) ? 'test2' : 'test';
    $login->password = '123';
    $login->authenticate(null, null);

    switch( $url )
    {
      case 'personal':
        $this->forward('userProfile/data', false);
      break;

      case 'personal_password':
        $this->forward('userProfile/changePassword', false);
        break;

      case 'personal_history':
        $this->forward('userProfile/historyOrders', false);
      break;
    }

    Yii::app()->user->logout(false);
  }

}
