<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 *
 * @property bool $defaultParamsUsed
 */
class FUrlManager extends CUrlManager
{
  public $urlRuleClass = 'FUrlRule';

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
   * @var bool Использовались ли при построении ссылки параметры по-умолчанию
   */
  private $defaultParamsUsed = false;

  public function init()
  {
    parent::init();

    $this->urlCreator = Yii::createComponent($this->urlCreatorClass);
    $this->urlCreator->init();
  }

  public function getDefaultParamsUsed()
  {
    return $this->defaultParamsUsed;
  }

  /**
   * @param $value
   */
  public function setDefaultParamsUsed($value)
  {
    $this->defaultParamsUsed = $value;
  }

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
}