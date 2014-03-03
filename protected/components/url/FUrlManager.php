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

  /**
   * @var bool Использовались ли при построении ссылки параметры по-умолчанию
   */
  private $defaultParamsUsed = false;

  protected function createUrlRule($route, $pattern)
  {
    if( is_array($route) && isset($route['class']) )
      return new $route['class']($route, $pattern);
    else
      return new $this->urlRuleClass($route, $pattern);
  }

  public function getDefaultParamsUsed()
  {
    return $this->defaultParamsUsed;
  }

  public function setDefaultParamsUsed($value)
  {
    $this->defaultParamsUsed = $value;
  }

  public function createUrl($route, $params = array(), $ampersand = '&')
  {
    $pattern = '{HTTP_HOST}';

    if( isset($params['url']) && strpos($params['url'], $pattern) !== false )
    {
      $params['url'] = trim(str_replace($pattern, '', $params['url']), '/');
      $url = Yii::app()->getRequest()->getHostInfo().parent::createUrl($route, $params, $ampersand);
    }
    else
    {
      $url = RedirectedUrlCreator::init(parent::createUrl($route, $params, $ampersand))->create();
    }

    return $url;
  }
}