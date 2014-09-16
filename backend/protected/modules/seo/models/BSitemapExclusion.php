<?php
/**
 * @author Nikita Pimoshenko <pimoshenko@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.models
 *
 * @method static BSitemapRoute model(string $className = __CLASS__)
 *
 * @property int    $id
 * @property string $route
 * @property bool   $lastmod
 * @property string $changefreq
 * @property float  $priority
 * @property bool   $visible
 *
 * @property array  $changeFreqs
 */
class BSitemapExclusion extends BSitemap
{
  public function tableName()
  {
    return '{{seo_sitemap_exclusion}}';
  }

  /**
   * Validation rules
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('route', 'filter', 'filter' => array('Utils', 'getRelativeUrl')),
      array('route', 'required'),
      array('lastmod, visible', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1),
      array('route, changefreq', 'length', 'max' => 255),
      array('route', 'unique'),
      array('priority', 'length', 'max' => 5),
      array('id, route, lastmod, changefreq, priority, visible', 'safe', 'on' => 'search'),
    );
  }
}