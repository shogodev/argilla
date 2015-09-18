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
class BSitemapRoute extends BSitemap
{
  /**
   * Validation rules
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('route', 'required'),
      array('lastmod, visible', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1),
      array('route, changefreq', 'length', 'max' => 255),
      array('route', 'unique'),
      array('priority', 'length', 'max' => 5),
      array('id, route, lastmod, changefreq, priority, visible', 'safe', 'on' => 'search'),
    );
  }

  /**
   * Returns defaultScope when using database lazy loading
   *
   * @return array
   */
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.route ASC',
    );
  }

  /**
   * Function constructs array of site routes and returns it
   *
   * @return array
   */
  public function getRoutes()
  {
    Yii::import('frontend.components.url.FUrlManager');
    $frontendUrlManager = new FUrlManager();

    $routes = array();
    array_walk($frontendUrlManager->rules, function (array $routeConfig) use (&$routes)
    {
      $route = reset($routeConfig);
      $routes[$route] = $route;
    });

    return $routes;
  }
}