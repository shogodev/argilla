<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */

Yii::import('application.models.gallery.*');

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