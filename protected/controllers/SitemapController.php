<?php
/**
 * Class SitemapController
 *
 */
class SitemapController extends FController
{
  public function actionIndex()
  {
    $this->breadcrumbs = array('Карта сайта');

    $this->render('sitemap');
  }

  /**
   * @return array
   */
  public function getSiteMapMenu()
  {
    $customMenu = Menu::model()->getMenu('site_map');

    $criteria = new CDbCriteria();
    $criteria->compare('sitemap', 1);
    $infoMenu = Info::model()->resetScope()->findByPk(Info::ROOT_ID)->getMenu($criteria);

    return CMap::mergeArray($customMenu, $infoMenu);
  }
}
