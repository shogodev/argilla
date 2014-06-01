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

  }
}