<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
class RedirectHelper extends CApplicationComponent
{
  const REGEXP_START_CHAR = '#';

  /**
   * @var Redirect[]
   */
  protected $redirects = array();

  /**
   * Текущая страница для сравнения
   *
   * @var string
   */
  protected $currentUrl;

  /**
   * Конечный url
   *
   * @var string
   */
  protected $targetUrl;

  /**
   * Redirect, установленный в качестве исходного для обработки
   *
   * @var Redirect
   */
  protected $redirect;

  /**
   * Используется ли редирект
   *
   * @var bool
   */
  protected $isRedirect = false;

  /**
   * Части исходого url
   *
   * @var array
   */
  private $currentUrlParts;

  /**
   * Флаг, указывающий на то, что мы работаем в режиме подмены ссылок
   *
   * @var bool
   */
  private $replaceMode = false;

  public function init()
  {
    $this->makeSlashRedirect();
    Yii::app()->attachEventHandler('onBeginRequest', array($this, 'onBeginRequest'));
  }

  /**
   * @param string $url
   */
  public function __construct($url = null)
  {
    if( !$url )
      $url = $_SERVER['REQUEST_URI'];

    $this->setCurrentUrl($url);
  }

  /**
   * @param $event
   */
  public function onBeginRequest($event)
  {
    $this->find()->move();
  }

  /**
   * @return string
   */
  public function getCurrentUrl()
  {
    return $this->currentUrl;
  }

  /**
   * @param $url
   */
  public function setCurrentUrl($url)
  {
    $this->redirect   = null;
    $this->targetUrl  = null;
    $this->isRedirect = false;

    $this->currentUrl = $url;
    $this->clearUrl();
  }

  /**
   * @return string
   */
  public function getTargetUrl()
  {
    return $this->targetUrl;
  }

  /**
   * @return bool
   */
  public function getIsRedirect()
  {
    return $this->isRedirect;
  }

  /**
   * @return Redirect
   */
  public function getRedirect()
  {
    return $this->redirect;
  }

  /**
   * @param bool $r
   *
   * @return void
   */
  public function setReplaceMode($r = true)
  {
    $this->replaceMode = $r ? true : false;
  }

  /**
   * @return bool
   */
  public function isReplaceMode()
  {
    return $this->replaceMode;
  }

  /**
   * Делаем редирект на ссылку со слэшем, если в запросе его не было
   */
  public function makeSlashRedirect()
  {
    $url = parse_url($_SERVER['REQUEST_URI']);

    if( !preg_match("/.+\.\w+$/", $url['path']) && substr($url['path'], -1, 1) !== '/' )
    {
      $this->isRedirect = true;
      $this->targetUrl  = $this->currentUrl.'/';

      $this->redirect          = new Redirect();
      $this->redirect->type_id = RedirectType::TYPE_301;
      $this->move();
    }
  }

  /**
   * Нахождение возможного редиректа для текущего url
   *
   * @return RedirectHelper
   */
  public function find()
  {
    foreach( $this->getRedirects() as $redirect )
    {
      if( $this->isReplaceMode() && $redirect->type_id != RedirectType::TYPE_REPLACE )
        continue;

      if( $this->isRedirect )
        break;

      $this->checkRedirect($redirect);
    }

    if( !$this->isRedirect && $this->originExists() && !$this->isReplaceMode() )
    {
      $redirect          = new Redirect();
      $redirect->type_id = RedirectType::TYPE_404;
      $this->isRedirect  = true;
    }

    $this->restoreUrl();

    return $this;
  }

  /**
   * Выполнение редиректа, если он существует
   *
   * @throws CHttpException
   */
  public function move()
  {
    if( $this->isRedirect )
    {
      if( empty($this->redirect) )
        throw new CHttpException(404, RedirectType::getList()[404]);
      elseif( $this->redirect->type_id == RedirectType::TYPE_REPLACE )
        $_SERVER['REQUEST_URI'] = $this->targetUrl;
      else
      {
        $redirectMessage = RedirectType::getList()[$this->redirect->type_id];

        header("HTTP/1.0 {$this->redirect->type_id} {$redirectMessage}");
        header("Location: http://{$_SERVER['HTTP_HOST']}{$this->targetUrl}");
        Yii::app()->end();
      }
    }
  }

  /**
   * Проверка на существование текущего url, как оригинального
   *
   * @return boolean
   */
  public function originExists()
  {
    $exists = false;

    foreach( $this->getRedirects() as $redirect )
    {
      if( $redirect->base == $this->currentUrl )
      {
        $exists = true;
        break;
      }
    }

    return $exists;
  }

  /**
   * Проверка на возможность редиректа по текущему url и Redirect $r
   *
   * @param Redirect $r
   */
  protected function checkRedirect(Redirect $r)
  {
    $currentRedirect = $this->checkRedirectType($r);

    if( stripos($currentRedirect->target, self::REGEXP_START_CHAR) === 0 && @preg_match($currentRedirect->target, $this->currentUrl) )
    {
      $this->targetUrl  = preg_replace($currentRedirect->target, $this->prepareReplacement($currentRedirect->base), $this->currentUrl);
      $this->redirect   = $currentRedirect;
      $this->isRedirect = true;
    }
    elseif( $this->currentUrl == $currentRedirect->base )
    {
      $this->targetUrl  = $currentRedirect->target;
      $this->redirect   = $currentRedirect;
      $this->isRedirect = true;
    }
  }

  /**
   * Проверка типа редиректа,
   * если тип "подмена", то меняются местами начальный и конечный URL
   *
   * @param Redirect $r
   *
   * @return Redirect
   */
  protected function checkRedirectType($r)
  {
    $checkedRedirect = clone $r;

    if( $r->type_id == RedirectType::TYPE_REPLACE && !$this->replaceMode )
    {
      $tmp                     = $checkedRedirect->base;
      $checkedRedirect->base   = $checkedRedirect->target;
      $checkedRedirect->target = $tmp;
    }

    return $checkedRedirect;
  }

  /**
   * Удаление query и fragment
   */
  protected function clearUrl()
  {
    $this->currentUrlParts = parse_url($this->currentUrl);
    $this->currentUrl      = $this->currentUrlParts['path'];
    $this->currentUrl      = '/'.trim($this->currentUrl, '/');
  }

  /**
   * Добавление к конечному url исходных параметров
   */
  protected function restoreUrl()
  {
    if( !empty($this->currentUrlParts['query']) )
      $this->targetUrl .= '?' . $this->currentUrlParts['query'];

    if( !empty($this->currentUrlParts['fragment']) )
      $this->targetUrl .= '#' . $this->currentUrlParts['fragment'];
  }

  /**
   * @return array
   */
  protected function getRedirects()
  {
    if( empty($this->redirects) )
      $this->redirects = Redirect::model()->findAll();

    return $this->redirects;
  }

  /**
   * Подготовка выражения для подстановки
   *
   * @param $string
   *
   * @return string
   */
  private function prepareReplacement($string)
  {
    $string = preg_replace_callback("/\([^\)]+\)/", function(){
      static $position = 0;

      return '$'.++$position;
    }, trim($string, '#'));

    return Yii::app()->createUrl($string);
  }
}