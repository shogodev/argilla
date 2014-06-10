<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.sitemap
 *
 * @method static SitemapRoute model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string  $route
 * @property bool    $lastmod
 * @property string  $changefreq
 * @property float   $priority
 */
class SitemapRoute extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_sitemap_route}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(true, false);

    return array(
      'condition' => "{$alias}.visible = 1",
    );
  }
}