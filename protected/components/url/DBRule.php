<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 */
Yii::import('CUrlManager', true);

class DBRule extends CUrlRule
{
  public $models = array();

  function __construct($route, $pattern)
  {
    if( is_array($route) )
    {
      foreach(array('models') as $name)
      {
        if( isset($route[$name]) )
          $this->$name = $route[$name];
      }
    }

    parent::__construct($route, $pattern);
  }

  public function createUrl($manager, $route, $params, $ampersand)
  {
    if($this->parsingOnly)
      return false;

    $tr = array();
    if( $route !== $this->route )
    {
      if( $this->routePattern !== null && preg_match($this->routePattern, $route, $matches) )
      {
        foreach($this->references as $key => $name)
          $tr[$name] = $matches[$key];
      }
      else
        return false;
    }

    // Если в параметрах построения ссылки не заданы какие-то параметры по-умолчанию,
    // то добавляем пустое значение в массив параметров. Это позволяет пройти проверку,
    // но в ссылку они добавлены не будут
    foreach($this->defaultParams as $key => $value)
      if( !isset($params[$key]) )
        $params[$key] = '';

    foreach($this->params as $key => $value)
      if( !isset($params[$key]) )
        return false;

    if( $manager->matchValue && $this->matchValue === null || $this->matchValue )
      foreach($this->params as $key => $value)
        if( !preg_match('/\A'.$value.'\z/u', $params[$key]) )
          return false;

    foreach($this->params as $key => $value)
    {
      $tr["<$key>"] = urlencode($params[$key]);
      unset($params[$key]);
    }

    $url = strtr($this->template, $tr);

    if( !empty($params) )
      $url .= '?'.$manager->createPathInfo($params, '=', $ampersand);

    return $url;
  }

  /**
   * @param FUrlManager $manager
   * @param CHttpRequest $request
   * @param string $pathInfo
   * @param string $rawPathInfo
   *
   * @return bool|string
   */
  public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
  {
    if( $this->verb !== null && !in_array($request->getRequestType(), $this->verb, true) )
      return false;

    if( $manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive )
      $case = '';
    else
      $case = 'i';

    if( $this->urlSuffix !== null )
      $pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);

    if( $manager->useStrictParsing && $pathInfo === $rawPathInfo )
    {
      $urlSuffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;
      if( $urlSuffix != '' && $urlSuffix !== '/' )
        return false;
    }

    if( $this->hasHostInfo )
      $pathInfo = strtolower($request->getHostInfo()).rtrim('/'.$pathInfo, '/');

    if( !empty($this->defaultParams) && !preg_match($this->pattern.$case, $pathInfo.'/', $matches)  )
    {
      $pathInfo .= rtrim('/' . implode("/", $this->defaultParams), '/') . '/';
      $isDefaultParamsUsed = true;
    }
    else
    {
      $pathInfo .= '/';
      $isDefaultParamsUsed = false;
    }

    if( preg_match($this->pattern.$case, $pathInfo, $matches) )
    {
      $result  = true;
      $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());

      foreach($this->models as $key => $class)
      {
        /**
         * @var FActiveRecord $class
         */
        $criteria = new CDbCriteria();
        $criteria->limit = 1;
        $criteria->compare('url', $matches[$key]);
        $command = $builder->createFindCommand($class::model()->tableName(), $criteria);

        if( !$command->queryAll() )
          $result = false;
      }

      if( !$result )
        return false;

      foreach($this->defaultParams as $name => $value)
      {
        if( !isset($_GET[$name]) )
          $_REQUEST[$name] = $_GET[$name] = $value;
      }

      $tr = array();
      foreach($matches as $key => $value)
      {
        if( isset($this->references[$key]) )
          $tr[$this->references[$key]] = $value;
        elseif( isset($this->params[$key]) )
          $_REQUEST[$key] = $_GET[$key] = $value;
      }

      $manager->isDefaultParamsUsed = $isDefaultParamsUsed;

      if( $pathInfo !== $matches[0] )
        $manager->parsePathInfo(ltrim(substr($pathInfo, strlen($matches[0])), '/'));
      if( $this->routePattern !== null )
        return strtr($this->route, $tr);
      else
        return $this->route;
    }

    return false;
  }
}