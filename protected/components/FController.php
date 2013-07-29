<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * @method textBlock($key)
 * @method textBlocks($key)
 * @method textBlockRegister($name = null, $content = null, $htmlOptions = array('class' => 'm20 bb red center'))
 * @method Counter[] getCounters($key = 'copyright')
 * @method array getCopyrights()
 * @method array getContacts($groupName = null)
 * @method array getSettings($key = null, $defaultValue = null)
 *
 * @property Counter[] $counters
 * @property array $copyrights
 * @property array $contacts
 * @property array|string $settings
 *
 * @property FBasket|FCollectionElement[] $basket
 * @property FBasket|FCollectionElement[] $fastOrderBasket
 * @property FFavorite|FCollectionElement[] $favorite
 * @property FForm $fastOrderForm
 * @property FForm $callbackForm
 * @property FForm $loginForm
 */
class FController extends CController
{
  public $breadcrumbs = array();

  /**
   * @var Meta
   */
  public $meta = null;

  /**
   * @var array $activeUrl
   */
  public $activeUrl = array();

  /**
   * @var bool
   */
  protected $rememberThisPage;

  /**
   * @param CAction $action
   *
   * @return void
   */
  protected function afterAction($action)
  {
    $excludedPages = array(
      'user/login',
      'user/logout',
      'user/registration',
      'user/restore',
      'user/profile',
      'user/data',
    );

    if( !Yii::app()->request->isAjaxRequest && !in_array(Yii::app()->request->pathInfo, $excludedPages) && $this->shouldRememberReturnUrl() )
      Yii::app()->user->setReturnUrl($this->getCurrentUrl());

    parent::afterAction($action);
  }

  public function behaviors()
  {
    return array('common' => array('class' => 'CommonBehavior'));
  }

  public function actions()
  {
    return array(
      'captcha' => array(
        'class' => 'FCaptchaAction',
      ),
    );
  }

  public function init()
  {
    Yii::app()->setHomeUrl($this->createAbsoluteUrl('index/index'));
  }

  public function renderOverride($view, $data = null, $return = false, $processOutput = false)
  {
    if( !empty($this->action) && file_exists($this->viewPath.'/'.$this->action->id.'/'.$view.'.php') )
    {
      $this->renderPartial($this->action->id.'/'.$view, $data, $return, $processOutput);
    }
    else if( file_exists($this->viewPath.'/'.$view.'.php') )
    {
      $this->renderPartial($view, $data, $return, $processOutput);
    }
    else
    {
      $this->renderPartial('//'.$view, $data, $return, $processOutput);
    }
  }

  public function createUrl($route, $params = array(), $ampersand = '&')
  {
    $absPattern = '{HTTP_HOST}';

    if( isset($params['url']) && strpos($params['url'], $absPattern) !== false )
    {
      $params['url'] = str_replace($absPattern, '', $params['url']);
      $url           = Yii::app()->getRequest()->getHostInfo().$params['url'];
    }
    else
    {
      $url = RedirectedUrlCreator::init(parent::createUrl($route, $params, $ampersand))->create();
      $url = parent::createUrl($url);
    }

    return $this->normalizeUrl($url);
  }

  /**
   * @param string $modelClass
   * @param int $id
   *
   * @return CActiveRecord
   * @throws CHttpException
   */
  public function loadModel($modelClass, $id)
  {
    $model = $modelClass::model()->findByPk($id);

    if( $model === null )
      throw new CHttpException(404, "The requested page of {$modelClass} does not exist.");

    return $model;
  }

  /**
   * @param string $view
   * @param null $data
   * @param bool $return
   * @return mixed|string
   */
  public function render($view, $data = null, $return = false)
  {
    if( !is_object($this->meta) )
    {
      $this->meta = new Meta($this->route, $this->getPageTitle());
      $this->meta->findModel($data);
      $this->meta->registerMetaTags();
    }

    return parent::render($view, $data, $return);
  }

  public function afterRender($view, &$output)
  {
    $this->meta->saveUsedModels();

    parent::afterRender($view, $output);
  }

  public function clip($id, $value)
  {
    $this->beginClip($id);
    echo $value;
    $this->endClip();

    if( $this->meta )
      $this->meta->registerClip($id, $value);

    return $this->clips[$id];
  }

  public function getCanonicalUrl()
  {
    // http://help.yandex.ru/webmaster/?id=1111858#canonical
    $path = Yii::app()->request->getPathInfo();
    $url  = Yii::app()->request->getHostInfo().($path ? '/'.$path : '');

    return $url;
  }

  public function getCurrentUrl()
  {
    return $this->createUrl($this->id."/".$this->action->id, $this->getActionParams(true));
  }

  public function getActionParams($cutDefaultParams = false)
  {
    $params = $_GET;

    if( $cutDefaultParams )
    {
      $defaultParams = array();

      $rules = Yii::app()->urlManager->rules;

      foreach($rules as $rule)
      {
        if( Yii::app()->controller->route == Arr::get($rule, 0) )
        {
          if( isset($rule['defaultParams']) )
            $defaultParams = $rule['defaultParams'];

          break;
        }
      }

      if( !empty($defaultParams) )
        foreach($defaultParams as $key => $value)
          unset($params[$key]);
    }

    return $params;
  }

  /**
   * Надо ли запоминать текущую страницу
   *
   * @return bool
   */
  protected function shouldRememberReturnUrl()
  {
    if( $this->rememberThisPage === null )
      $this->checkShouldRememberReturnUrl();

    return $this->rememberThisPage;
  }

  /**
   * Проверка на необходимость запоминать текущую страницу
   *
   * @return void
   */
  protected function checkShouldRememberReturnUrl()
  {
    $this->rememberThisPage = Yii::app()->errorHandler->error === null;
  }

  /**
   * Преобразуем все ссылки к единому формату.
   *
   * @param $url
   *
   * @return string
   */
  protected function normalizeUrl($url)
  {
    $url = rtrim($url, '/');
    $url = str_replace('/?', '?', $url);

    $components          = parse_url(rtrim($url, '/'));
    $components['path'] .= preg_match("/.+\.\w+$/", $components['path']) ? "" : '/';
    $components['path']  = preg_replace("/\/+/", "/", $components['path']);


    return Utils::buildUrl($components);
  }
}