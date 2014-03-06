<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 */
class FUrlManager extends CUrlManager
{
  public $urlRuleClass = 'FUrlRule';

  public $isDefaultParamsUsed = false;

  protected function createUrlRule($route, $pattern)
  {
    if(is_array($route) && isset($route['class']))
      return new $route['class']($route, $pattern);
    else
      return new $this->urlRuleClass($route, $pattern);
  }
}

class FUrlRule extends CUrlRule
{
  /**
   * @param FUrlManager $manager
   * @param CHttpRequest $request
   * @param string $pathInfo
   * @param string $rawPathInfo
   *
   * @return mixed
   */
  public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
  {
    $result = parent::parseUrl($manager, $request, $pathInfo, $rawPathInfo);

    if( $result === false && !empty($this->defaultParams) )
    {
      // добавляем из маршрута параметры по-умолчанию и снова проверяем правило с ними
      foreach($this->defaultParams as $param)
        $pathInfo .= '/'.$param;

      if( $result = parent::parseUrl($manager, $request, $pathInfo, $rawPathInfo) )
      {
        $manager->isDefaultParamsUsed = true;
      }
    }

    return $result;
  }

  public function createUrl($manager, $route, $params, $ampersand)
  {
    // если в параметрах построения ссылки не заданы какие-то параметры по-умолчанию, то добавляем их
    foreach($this->defaultParams as $key => $value)
      if( !isset($params[$key]) )
        $params[$key] = '';

    // временно скидываем параметры по-умолчанию, так как все недостающие уже перенесены в параметры построения
    // и в родительском методе проверка на них не нужна
    $defaultParams       = $this->defaultParams;
    $this->defaultParams = array();

    $url                 = parent::createUrl($manager, $route, $params, $ampersand);
    $this->defaultParams = $defaultParams;

    return $url;
  }
}