<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 */
class FUrlManager extends CUrlManager
{
  public $urlRuleClass = 'FUrlRule';

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