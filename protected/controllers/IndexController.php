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
    $news = News::model()->main()->findAll();
    $rotator = Banner::model()->getByLocationAll('rotator');

    $this->render('index', array(
      'rotator' => $rotator,
      'news' => $news,
    ));
  }

  public function getShowCase()
  {
    $criteria = new CDbCriteria(array('limit' => 12));
    $criteria->compare('t.main', 1);

    $showcase = new Showcase($criteria);

    $showcase->createTabByCondition('Новинки', 't.novelty', 1);
    $showcase->createTabByCondition('Лидеры продаж', 't.spec', 1);
    $showcase->createTabByCondition('Скидки', 't.discount', 1);

    return $showcase;
  }
}