<?php
/**
 * @author    Nikita Melnikov <melnikov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
class SitemapController extends FController
{
  public function actionIndex()
  {
    throw new HttpException(404, 'Страница не найдена');
  }

  public function actionXml()
  {
    $generatorFactory = new GeneratorFactory(Yii::getPathOfAlias('frontend.components.sitemapXml.locationGenerators'),
                                             Yii::app()->controller);
    $siteMap          = new SitemapXml(SitemapRoute::model()->findAll(), $generatorFactory->getGenerators());
    $xml              = $siteMap->build(new SitemapUrlBuilder(), new DateTime());

    header('Content-Type: text/xml; charset=utf-8');
    echo $xml;
  }
}