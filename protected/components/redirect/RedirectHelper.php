<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 *
 * @property bool $isRedirect
 * @property string $targetUrl
 */
class RedirectHelper extends CApplicationComponent
{
  const REGEXP_START_CHAR = '#';

  public $cacheUrls = false;

  /**
   * @var Redirect[]
   */
  protected $redirects;

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
  private $parsedUrl;

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
    if( !$url && isset($_SERVER['REQUEST_URI']) )
    {
      $url = $_SERVER['REQUEST_URI'];
    }

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

    $this->parsedUrl = parse_url($url);

    if( !isset($this->parsedUrl['path']) )
    {
      $this->parsedUrl['path'] = '/';
    }
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
    if( $this->urlNeedTrailingSlash($this->parsedUrl['path']) )
    {
      $this->isRedirect = true;

      $this->targetUrl = $this->parsedUrl['path'].'/';
      $this->restoreUrl();

      $this->redirect = new Redirect();
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
    $this->clearUrl();

    foreach($this->getRedirects() as $redirect)
    {
      if( $this->isReplaceMode() && $redirect->type_id != RedirectType::TYPE_REPLACE )
        continue;

      $this->checkRedirect($redirect);

      if( $this->isRedirect )
        break;
    }

    if( !$this->isRedirect && $this->originExists() && !$this->isReplaceMode() )
    {
      $this->redirect = new Redirect();
      $this->redirect->type_id = RedirectType::TYPE_404;
      $this->isRedirect = true;
    }

    $this->restoreUrl();

    return $this;
  }

  /**
   * @throws CHttpException
   */
  public function move()
  {
    if( $this->isRedirect )
    {
      if( $this->redirect->type_id == RedirectType::TYPE_404 )
      {
        throw new CHttpException(404, RedirectType::getList()[404]);
      }
      elseif( $this->redirect->type_id == RedirectType::TYPE_REPLACE )
      {
        $_SERVER['REQUEST_URI'] = $this->targetUrl;
      }
      else
      {
        Yii::app()->request->redirect($this->targetUrl, true, $this->redirect->type_id);
      }
    }
  }

  protected function urlNeedTrailingSlash($url)
  {
    return !preg_match("/.+\.\w+$/", $url) && substr($url, -1, 1) !== '/';
  }

  /**
   * Проверяем, что текущий url не переопределен через редиректы
   *
   * @return boolean
   */
  protected function originExists()
  {
    foreach($this->getRedirects() as $redirect)
    {
      if( $redirect->base === $this->parsedUrl['path'] )
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Проверка на возможность редиректа по текущему url и Redirect $r
   *
   * @param Redirect $redirect
   */
  protected function checkRedirect(Redirect $redirect)
  {
    $this->redirect = $this->checkRedirectType($redirect);
    $this->isRedirect = true;

    if( $this->redirect->hasRegExpCoincidence($this->parsedUrl['path']) )
    {
      $this->targetUrl = preg_replace($this->redirect->base, $this->prepareReplacement($this->redirect->target), $this->parsedUrl['path']);
    }
    elseif( $this->redirect->hasStringCoincidence($this->parsedUrl['path']) )
    {
      $this->targetUrl = $this->redirect->target;
    }
    else
    {
      $this->redirect = null;
      $this->isRedirect = false;
    }
  }

  /**
   * Проверка типа редиректа,
   * если тип "подмена", то меняются местами начальный и конечный URL
   *
   * @param Redirect $redirect
   *
   * @return Redirect
   */
  protected function checkRedirectType($redirect)
  {
    if( $redirect->type_id == RedirectType::TYPE_REPLACE && !$this->isReplaceMode() )
    {
      $redirect = clone $redirect;
      $tmp = $redirect->base;
      $redirect->base = $redirect->target;
      $redirect->target = $tmp;
    }

    return $redirect;
  }

  protected function clearUrl()
  {
    $this->parsedUrl['path'] = '/'.trim($this->parsedUrl['path'], '/');
  }

  /**
   * @return string
   */
  protected function restoreUrl()
  {
    if( $this->targetUrl !== null )
    {
      $url = $this->parsedUrl;
      $url['path'] = $this->targetUrl;

      $this->targetUrl = Utils::buildUrl($url);
    }
  }

  /**
   * @return array
   */
  protected function getRedirects()
  {
    if( $this->redirects === null )
      $this->redirects = Redirect::model()->findAll();

    return $this->redirects;
  }

  /**
   * Подготовка выражения для подстановки
   *
   * @param string $template
   *
   * @return string
   */
  protected function prepareReplacement($template)
  {
    $template = preg_replace_callback("/\([^\)]+\)/", function(){
      static $position = 1;
      return '$'.$position++;
    }, trim($template, '#'));

    return Yii::app()->createUrl($template);
  }
}