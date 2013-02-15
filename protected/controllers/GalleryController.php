<?php

Yii::import('application.models.gallery.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Gallery
 */
class GalleryController extends FController
{
  public function actionIndex()
  {
    $this->breadcrumbs = array('О компании' => array('info/index', 'url' => 'about'));

    $this->render('index', array(
      'dataProvider' => new FActiveDataProvider('Gallery', array('pagination' => false)),
    ));
  }

  public function actionView($url)
  {
    $model = Gallery::model()->findByAttributes(array('url' => $url));

    if( $model === null )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      'О компании' => array('index', 'about'),
      'Фотогалерея' => array('gallery', 'index'),
      $model->name
    );

    $this->render('gallery', array(
      'model' => $model,
    ));
  }
}