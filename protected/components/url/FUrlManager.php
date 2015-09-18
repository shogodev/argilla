<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 * @property bool $defaultParams
 */
class FUrlManager extends CUrlManager
{
  public $urlRuleClass = 'FUrlRule';

  /**
   * @var FUrlRule
   */
  public $rule;

  /**
   * @var mixed Индекс совпавшего правила из массива rules
   */
  public $ruleIndex;

  public $urlCreatorClass = 'ReplaceRedirectComponent';

  /**
   * @var ReplaceRedirectComponent
   */
  protected $urlCreator;

  /**
   * @var array
   */
  protected $defaultParams;

  public function __construct()
  {
    $this->rules = require(Yii::getPathOfAlias('frontend.config.').DIRECTORY_SEPARATOR.'routes.php');
    $this->rules = CMap::mergeArray($this->rules, $this->getRouteModules());
  }

  public function init()
  {
    parent::init();

    $this->urlCreator = Yii::createComponent($this->urlCreatorClass);
    $this->urlCreator->init();
  }

  /**
   * @return array
   */
  public function getDefaultParams()
  {
    return $this->defaultParams;
  }

  /**
   * @param $value
   */
  public function setDefaultParams(array $value)
  {
    $this->defaultParams = $value;
  }

  /**
   * @param string $route
   * @param array $params
   * @param string $ampersand
   *
   * @return string
   */
  public function createUrl($route, $params = array(), $ampersand = '&')
  {
    if( $this->hasStaticPatterns($params) )
    {
      return $this->urlCreator->getStaticUrl($params['url']);
    }
    else
    {
      $url = parent::createUrl($route, $params, $ampersand);

      return $this->urlCreator->getUrl($url);
    }
  }

  /**
   * @param $path
   *
   * @return string
   */
  public function createPath($path)
  {
    return $this->urlCreator->getUrl($path);
  }

  /**
   * Запоминаем или нет адрес текущей страницы в сессию пользователя
   * @return bool
   */
  public function shouldRememberReturnUrl()
  {
    return $this->rule && $this->rule->shouldRemember && !Yii::app()->errorHandler->error && !Yii::app()->request->isAjaxRequest;
  }

  /**
   * @param CHttpRequest $request
   *
   * @return string route (controllerID/actionID)
   */
  public function parseUrl($request)
  {
    $route = parent::parseUrl($request);

    foreach($this->rules as $index => $rule)
      if( Arr::get($rule, 0) === $route )
        $this->ruleIndex = $index;

    return $route;
  }

  /**
   * @param mixed $route
   * @param string $pattern
   *
   * @return FUrlRule
   */
  protected function createUrlRule($route, $pattern)
  {
    if( is_array($route) && isset($route['class']) )
      return new $route['class']($route, $pattern);
    else
      return new $this->urlRuleClass($route, $pattern);
  }

  private function hasStaticPatterns(array $params)
  {
    return isset($params['url']) && $this->urlCreator->hasStaticPatterns($params['url']);
  }

  private function getRouteModules()
  {
    $routeModules = array();

    $basePath = Yii::getPathOfAlias('frontend.modules');
    foreach(glob($basePath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $moduleDirectory)
    {
      $routesPath = $moduleDirectory.DIRECTORY_SEPARATOR.'routes.php';
      if( file_exists($routesPath) )
        $routeModules = array_merge($routeModules, require($routesPath));
    }

    return $routeModules;
  }
}