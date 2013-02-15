<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
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