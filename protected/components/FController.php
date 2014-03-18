<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * @mixin CommonBehavior
 * @mixin SeoBehavior
 * @mixin FControllerBehavior
 *
 * @property Counter[] $counters
 * @property array $copyrights
 * @property array $contacts
 * @property array|string $settings
 *
 * @property FBasket|FCollectionElement[] $basket
 * @property FFavorite|FCollectionElement[] $favorite
 * @property FForm $fastOrderForm
 * @property FForm $callbackForm
 * @property FForm $loginPopupForm
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

  public function behaviors()
  {
    return array(
      'seo' => array('class' => 'SeoBehavior'),
      'controller' => array('class' => 'FControllerBehavior'),
      'common' => array('class' => 'CommonBehavior'),
    );
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

  /**
   * http://help.yandex.ru/webmaster/?id=1111858#canonical
   *
   * @return string
   */
  public function getCanonicalUrl()
  {
    $path = CHtml::encode(Yii::app()->request->getPathInfo());
    $url  = Yii::app()->request->getHostInfo().($path ? '/'.Utils::normalizeUrl($path) : '/');

    return $url;
  }

  /**
   * @return string
   */
  public function getCurrentUrl()
  {
    return $this->createUrl($this->id.'/'.$this->action->id, $this->getActionParams(true));
  }

  /**
   * @return string
   */
  public function getCurrentAbsoluteUrl()
  {
    return $this->createAbsoluteUrl($this->id.'/'.$this->action->id, $this->getActionParams(true));
  }

  /**
   * @param bool $cutDefaultParams
   *
   * @return array
   */
  public function getActionParams($cutDefaultParams = false)
  {
    $params = $_GET;

    if( $cutDefaultParams )
    {
      $rule = Arr::get(Yii::app()->urlManager->rules, Yii::app()->urlManager->ruleIndex);
      foreach(Arr::get($rule, 'defaultParams', array()) as $key => $value)
        unset($params[$key]);
    }

    return $params;
  }

  /**
   * @param CAction $action
   *
   * @return void
   */
  protected function afterAction($action)
  {
    if( $this->shouldRememberReturnUrl() )
    {
      Yii::app()->user->setReturnUrl($this->getCurrentUrl());
    }

    parent::afterAction($action);
  }

  /**
   * Запоминаем или нет адрес текущей страницы в сессию пользователя
   *
   * @return bool
   */
  protected function shouldRememberReturnUrl()
  {
    $excludedPages = array(
      'user/login',
      'user/logout',
      'user/registration',
      'user/restore',
      'user/profile',
      'user/data',
    );

    $remember = !in_array(Yii::app()->request->pathInfo, $excludedPages) &&
                !isset(Yii::app()->errorHandler->error) &&
                !Yii::app()->request->isAjaxRequest;

    return $remember;
  }
}