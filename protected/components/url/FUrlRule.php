<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 *
 * <pre>
 * 'productType' => array('product/type', 'pattern' => 'type-<type:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1), 'canonicalParams' => array('page'), 'shouldRemember' => false),
 * </pre>
 */
class FUrlRule extends CUrlRule
{
  /**
   * @var array Параметры, которые будут оставлены при построении канонической ссылки
   */
  public $canonicalParams = array();

  /**
   * @var bool Запоминаем или нет маршрут в сессию, чтобы вернуться на страницу после авторизации пользователя
   */
  public $shouldRemember = true;

  /**
   * @var bool Строим ли ссылку с параметрами по-умолчанию или без них
   */
  public $createWithDefault = false;

  public function __construct($route, $pattern)
  {
    if( is_array($route) )
      foreach(array('canonicalParams', 'shouldRemember', 'createWithDefault') as $name)
        if( isset($route[$name]) )
          $this->$name = $route[$name];

    parent::__construct($route, $pattern);
  }

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
    $manager->defaultParams = array();

    if( ($pathInfo = $this->preparePathInfo($manager, $request, $pathInfo, $rawPathInfo)) === false )
    {
      return false;
    }

    if( !empty($this->defaultParams) && !preg_match($this->pattern, $pathInfo, $matches)  )
    {
      $pathInfo .= implode('/', $this->defaultParams).'/';
      $manager->defaultParams = $this->defaultParams;
    }

    if( preg_match($this->pattern, $pathInfo, $matches) )
    {
      $manager->rule = $this;
      return $this->getRoute($manager, $pathInfo, $matches);
    }
    else
      return false;
  }

  public function createUrl($manager, $route, $params, $ampersand)
  {
    if( $this->parsingOnly )
      return false;

    if( $manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive )
      $case = '';
    else
      $case = 'i';

    $tr = array();
    if( $route !== $this->route )
    {
      if( $this->routePattern !== null && preg_match($this->routePattern.$case, $route, $matches) )
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
        $params[$key] = $this->createWithDefault ? $value : '';

    foreach($this->params as $key => $value)
      if( !isset($params[$key]) )
        return false;

    if( $manager->matchValue && $this->matchValue === null || $this->matchValue )
    {
      foreach($this->params as $key => $value)
      {
        if( !preg_match('/\A'.$value.'\z/u'.$case, $params[$key]) )
          return false;
      }
    }

    foreach($this->params as $key => $value)
    {
      $tr["<$key>"] = preg_match('/[a-z\/-]/', $params[$key]) ? $params[$key] : urlencode($params[$key]);
      unset($params[$key]);
    }

    $suffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;
    $url = strtr($this->template, $tr);

    if( empty($suffix) && !empty($url) )
      $url = trim($url, '/').'/';

    if( $this->hasHostInfo )
    {
      $hostInfo = Yii::app()->getRequest()->getHostInfo();
      if( stripos($url, $hostInfo) === 0 )
        $url = substr($url, strlen($hostInfo));
    }

    if( empty($params) )
      return $url !== '' ? $url.$suffix : $url;

    if( $this->append )
      $url .= '/'.$manager->createPathInfo($params, '/', '/').$suffix;
    else
    {
      if( $url !== '' )
        $url .= $suffix;
      $url .= '?'.$manager->createPathInfo($params, '=', $ampersand);
    }

    return $url;
  }

  /**
   * @param FUrlManager $manager
   * @param CHttpRequest $request
   * @param string $pathInfo
   * @param string $rawPathInfo
   *
   * @return mixed
   */
  protected function preparePathInfo($manager, $request, $pathInfo, $rawPathInfo)
  {
    if( $this->verb !== null && !in_array($request->getRequestType(), $this->verb, true) )
      return false;

    if( !($manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive) )
      $this->pattern .= 'i';

    if( $this->urlSuffix !== null )
      $pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);

    // URL suffix required, but not found in the requested URL
    if( $manager->useStrictParsing && $pathInfo === $rawPathInfo )
    {
      $urlSuffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;
      if( $urlSuffix != '' && $urlSuffix !== '/' )
        return false;
    }

    if( $this->hasHostInfo )
      $pathInfo = strtolower($request->getHostInfo()).rtrim('/'.$pathInfo, '/');

    $pathInfo .= '/';

    return $pathInfo;
  }

  /**
   * @param FUrlManager $manager
   * @param string $pathInfo
   * @param array $matches
   *
   * @return string
   */
  protected function getRoute($manager, $pathInfo, $matches)
  {
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

    if( $pathInfo !== $matches[0] ) // there're additional GET params
      $manager->parsePathInfo(ltrim(substr($pathInfo, strlen($matches[0])), '/'));
    if( $this->routePattern !== null )
      return strtr($this->route, $tr);
    else
      return $this->route;
  }
}