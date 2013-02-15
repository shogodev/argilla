<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class NewsController extends FController
{
  public function actionOne($url)
  {
    $model = News::model()->findByAttributes(array('url' => $url));

    if( $model === null )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->section->name => array('news/section', 'url' => $model->section->url),
      $model->name
    );

    $this->render('news', array(
      'model' => $model,
    ));
  }

  public function actionSection($url)
  {
    $model = NewsSection::model()->findByAttributes(array('url' => $url));

    if( $model === null )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array($model->name);

    $criteria = new CDbCriteria();
    $criteria->compare('section_id', '='.$model->id);

    $dataProvider = new FActiveDataProvider('News', array(
      'criteria' => $criteria,
    ));

    $this->render('section', array(
      'model' => $model,
      'dataProvider' => $dataProvider,
    ));
  }
}