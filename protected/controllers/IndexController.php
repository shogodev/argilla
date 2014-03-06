<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class IndexController extends FController
{
  public function actionIndex()
  {
    $news = News::model()->findAll();
    $newsDataProvider = !empty($news) ? new FArrayDataProvider($news, array('pagination' => false)) : null;

    $banners  = Banner::model()->getByLocation('index_banner');

    $this->render('index', array(
      'banners' => $banners,
      'newsDataProvider' => $newsDataProvider
    ));
  }
}