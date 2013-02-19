<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class InfoController extends FController
{
  public function actionIndex($url)
  {
    $model = Info::model()->findByAttributes(array('url' => $url));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = $model->getBreadCrumbs();

    $this->render($model->getTemplate(), array(
      'model' => $model,
    ));
  }
}