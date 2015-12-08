<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.form
 * Компонент для работы с формами
 * @property string returnUrl
 * @property string successMessage
 */
class FForm extends CForm
{
  public static $DEFAULT_FORMS_PATH = 'frontend.forms.';

  public $formName;

  public $layout = FormLayouts::FORM_LAYOUT;

  public $elementsLayout;

  public $inputElementClass = 'FFormInputElement';

  public $activeForm = array('class' => 'CActiveForm',
    'enableAjaxValidation' => true);

  public $ajaxSubmit = true;

  public $validateOnSubmit = true;

  public $validateOnChange = true;

  public $autocomplete = false;

  public $loadFromSession = false;

  /**
   * @var bool $setUserData флаг подстановки пользовательски данных в форму
   */
  public $setUserData = true;

  public $clearAfterSubmit = false;

  protected $layoutViewParams = array();

  /**
   * @var int
   */
  protected $redirectDelay;

  /**
   * @var string
   */
  protected $redirectUrl;

  /**
   * @var bool
   */
  protected $status = false;

  public function __construct($config, $model = null, $parent = null)
  {
    if( is_string($config) )
    {
      $config = strpos($config, '.') !== false ? $config : static::$DEFAULT_FORMS_PATH.$config;
      $this->formName = get_class($model);
    }
    else if( is_array($config) )
    {
      $this->formName = Arr::get($config, 'name');
    }

    parent::__construct($config, $model, $parent);
  }

  public function __toString()
  {
    return $this->render();
  }

  /**
   * @param bool $status
   *
   * @return void
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }

  /**
   * @return bool
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Добавление всплывающего окна после отправки формы
   * @HINT сообщение добавляется только при отправки формы НЕ через ajax
   *
   * @param string $message
   * @param string $type
   */
  public function setFlashMessage($message, $type = 'success')
  {
    if( $this->ajaxSubmit == false )
      Yii::app()->user->setFlash($type, $message);
  }

  /**
   * @param int $delay
   * @param string $url
   *
   * @return void
   */
  public function setRedirectDelay($delay, $url = null)
  {
    $this->redirectDelay = $delay;
    $this->redirectUrl = !empty($url) ? $url : Yii::app()->request->requestUri;
    $this->registerRedirectScript();
  }

  /**
   * @param null $defaultUrl
   *
   * @return mixed
   */
  public function getReturnUrl($defaultUrl = null)
  {
    return Yii::app()->user->getState('__'.$this->formName.'ReturnUrl', $defaultUrl === null ? Yii::app()->getRequest()->getScriptUrl() : CHtml::normalizeUrl($defaultUrl));
  }

  /**
   * @param string $value
   */
  public function setReturnUrl($value)
  {
    Yii::app()->user->setState('__'.$this->formName.'ReturnUrl', $value);
  }

  /**
   * @param $message
   */
  public function setSuccessMessage($message)
  {
    Yii::app()->user->setFlash('__'.$this->formName.'Success', $message);
  }

  /**
   * @return string
   */
  public function getSuccessMessage()
  {
    return Yii::app()->user->getFlash('__'.$this->formName.'Success');
  }

  /**
   * @param $message
   */
  public function setErrorMessage(array $message)
  {
    Yii::app()->user->setFlash('__'.$this->formName.'Error', $message);
  }

  /**
   * @return mixed
   */
  public function getErrorMessage()
  {
    return Yii::app()->user->getFlash('__'.$this->formName.'Error');
  }

  public function renderBegin()
  {
    $this->beforeRender();

    $options = array(
      'validateOnSubmit' => $this->validateOnSubmit,
      'validateOnChange' => $this->validateOnChange
    );

    if( $this->autocomplete === false )
      $this->activeForm['htmlOptions']['autocomplete'] = 'off';

    $this->activeForm['clientOptions'] = CMap::mergeArray($options, Arr::get($this->activeForm, 'clientOptions', array()));

    return parent::renderBegin();
  }

  public function renderBody()
  {
    $output = array('{title}' => $this->renderTitle(),
      '{elements}' => $this->renderElements(),
      '{errors}' => $this->getActiveFormWidget()->errorSummary($this->getModel()),
      '{description}' => $this->description,
      '{buttons}' => $this->renderButtons());

    // Рендерим представления динамически
    $output = CMap::mergeArray($output, $this->renderLayoutViews($this->layout));

    return strtr($this->layout, $output);
  }

  /**
   * @param string $name
   * @param CFormElement $element
   * @param bool $forButtons
   */
  public function addedElement($name, $element, $forButtons)
  {
    if( isset($element->type) && in_array($element->type, array('text', 'password', 'tel', 'textarea')) )
      if( empty($element->attributes['class']) )
        $element->attributes['class'] = 'inp';
  }

  public function renderTitle()
  {
    $output = '';

    if( $this->title !== null )
    {
      if( $this->getParent() instanceof self )
      {
        $attributes = $this->attributes;
        unset($attributes['name'], $attributes['type']);
        $output = $this->title;
      }
      else
        $output = $this->title;
    }

    return $output;
  }

  public function renderButtons()
  {
    $output = '';

    foreach($this->getButtons() as $button)
    {
      $output .= $this->renderElement($this->modifySubmitButton($button));
    }

    return $output !== '' ? $output : '';
  }

  /**
   * Renders a single element which could be an input element, a sub-form, a string, or a button.
   *
   * @param mixed $element the form element to be rendered. This can be either a {@link CFormElement} instance
   * or a string representing the name of the form element.
   *
   * @return string the rendering result
   */
  public function renderElement($element)
  {
    if( is_string($element) )
    {
      if( ($e = $this[$element]) === null && ($e = $this->getButtons()->itemAt($element)) === null )
        return $element;
      else
        $element = $e;
    }
    if( $element->getVisible() )
    {
      if( $element instanceof CFormInputElement )
      {
        if( $element->type === 'hidden' )
          return "<div style=\"visibility:hidden\">\n".$element->render()."</div>\n";
        else
        {
          return $element->render()."\n";
        }
      }
      else if( $element instanceof CFormButtonElement )
        return $element->render()."\n";
      else
        return $element->render();
    }

    return '';
  }

  /**
   * Обработка формы: получение данных и валидация
   * @return bool
   */
  public function process()
  {
    if( $this->loadFromSession )
      $this->loadFromSession();

    if( !Yii::app()->request->isPostRequest )
      return false;

    $this->loadData();
    $errors = json_decode(CActiveForm::validate($this->getModels(), null, false), true);

    if( !$errors )
      return true;

    if( Yii::app()->request->isAjaxRequest )
    {
      echo json_encode(array('status' => 'ok', 'validateErrors' => json_encode($errors)));
      Yii::app()->end();
    }
    else
      $this->setErrorMessage($errors);

    return false;
  }

  /**
   * Посылает сообщение о успешной обработке данных
   *
   * @param string $message - сообщение
   * @param bool $scrollOnMessage - скролить страницу на сообщение
   * @param array $responseData
   * @param bool $end - завершить работу скрипта
   */
  public function responseSuccess($message = '', $scrollOnMessage = false, $responseData = array(), $end = true)
  {
    echo json_encode(CMap::mergeArray($responseData, array(
      'status' => 'ok',
      'messageForm' => $message,
      'scrollOnMessage' => $scrollOnMessage
    )));

    if( $end )
      Yii::app()->end();
  }

  /**
   * Валидация и сохранение данных в модель
   * @return bool
   * @throws CHttpException
   */
  public function save()
  {
    if( $this->process() )
    {
      Yii::app()->db->beginTransaction();

      /**
       * @var CActiveRecord $model
       */
      foreach($this->getModels() as $model)
      {
        if( !($model instanceof CActiveRecord) )
          continue;

        if( get_class($model) !== get_class($this->model) )
        {
          $foreignKey = null;

          foreach($this->model->getMetaData()->relations as $relation)
            if( $relation->className == get_class($model) )
              $foreignKey = $relation->foreignKey;

          if( !$foreignKey )
            throw new CHttpException(500, 'Can`t get foreign key for '.get_class($model));

          $model->$foreignKey = $this->model->getPrimaryKey();
        }

        if( !$model->save(false) )
        {
          Yii::app()->db->currentTransaction->rollback();
          throw new CHttpException(500, 'Can`t save '.get_class($this->model).' model');
        }

        $this->saveFiles($model);
      }

      Yii::app()->db->currentTransaction->commit();

      if( $this->clearAfterSubmit )
        $this->clearSession();

      $this->setStatus(true);

      return true;
    }

    return false;
  }

  public function saveToSession()
  {
    // todo: сделать проверку на password и не сохранять его
    if( $this->getModel() !== null && Yii::app()->request->isPostRequest )
    {
      $sessionParams = Yii::app()->request->getPost(get_class($this->getModel()), array());
      $sessionKey = $this->getSessionKey($this->getModel());

      if( !isset(Yii::app()->session[$sessionKey]) )
        Yii::app()->session[$sessionKey] = array();

      $sessionParams = CMap::mergeArray(Yii::app()->session[$sessionKey], $sessionParams);
      Yii::app()->session[$sessionKey] = $sessionParams;
    }

    foreach($this->getElements() as $element)
      if( $element instanceof self )
        $element->saveToSession();
  }

  public function clearSession()
  {
    foreach($this->getModels() as $model)
      unset(Yii::app()->session[$this->getSessionKey($model)]);
  }

  public function loadFromSession()
  {
    if( $this->getModel() !== null )
    {
      $sessionKey = $this->getSessionKey($this->getModel());
      $sessionParams = Yii::app()->session[$sessionKey];

      if( $sessionParams )
        $this->getModel()->setAttributes($sessionParams);
    }

    foreach($this->getElements() as $element)
      if( $element instanceof self )
        $element->loadFromSession();
  }

  public function ajaxValidation()
  {
    if( Yii::app()->request->isAjaxRequest && isset($_POST['ajax']) )
    {
      if( $this->loadFromSession )
        $this->saveToSession();

      $this->loadData();

      $result = $this->performAjaxValidation();
      echo json_encode($result);
      Yii::app()->end();
    }
  }

  protected function performAjaxValidation()
  {
    $result = array();

    if( $this->getModel() !== null )
    {
      $this->getModel()->validate();

      foreach($this->getModel()->getErrors() as $attribute => $errors)
        $result[CHtml::activeId($this->getModel(), $attribute)] = $errors;

      foreach($this->getElements() as $element)
        if( $element instanceof self )
          $result = CMap::mergeArray($result, $element->performAjaxValidation());
    }

    return $result;
  }

  public function isTabular()
  {
    return false;
  }

  public function sendNotification($email, $vars = array())
  {
    Yii::app()->notification->send($this->model, $vars, $email);
  }

  public function sendNotificationBackend($vars = array())
  {
    $vars = CMap::mergeArray($vars, array('model' => $this->model));
    Yii::app()->notification->send(get_class($this->model).'Backend', $vars, null, 'backend');
  }

  public function addLayoutViewParams($data)
  {
    $this->layoutViewParams[] = $data;
  }

  public function insertAfter($after, $element, $key)
  {
    $elements = $this->getElements()->toArray();
    Arr::insertAfter($elements, $key, $element, $after);

    $this->getElements()->clear();
    $this->getElements()->copyFrom($elements);
  }

  /**
   * Подставляет пользовательские данных в форму
   */
  public function setUserData()
  {
    if( Yii::app()->user->isGuest )
      return;

    /**
     * @var FActiveRecord $model
     */
    foreach($this->getModels() as $model)
    {
      if( $this->loadFromSession && Yii::app()->session[$this->getSessionKey($model)] )
        continue;

      $attributes = array('email' => Yii::app()->user->getEmail());
      $attributes = CMap::mergeArray($attributes, Yii::app()->user->profile->getAttributes());

      foreach($model->getAttributes() as $attribute => $value)
      {
        if( empty($value) && !empty($attributes[$attribute]) )
          $model->setAttribute($attribute, $attributes[$attribute]);
      }
    }
  }

  /**
   * @param $model
   *
   * @throws CHttpException
   */
  protected function saveFiles($model)
  {
    if( Yii::app()->request->isPostRequest && $model instanceof FActiveFileRecord )
    {
      $files = CUploadedFile::getInstances($model, $model->formAttribute);

      foreach($files as $file)
      {
        $name = UploadHelper::prepareFileName($model->uploadPath, $file->name);
        $path = $model->uploadPath.$name;

        if( $file->saveAs($path) )
        {
          chmod($path, 0664);

          if( $model->fileModel === get_class($model) )
          {
            $fileModel = $model;
            $fileModel->{$model->formAttribute} = $name;
          }
          else
          {
            $fileModel = new $model->fileModel;
            $fileModel->name = $name;
            $fileModel->parent = $model->id;
            $fileModel->size = Yii::app()->format->formatSize($file->size);
          }

          if( !$fileModel->save() )
            throw new CHttpException(500, 'Can`t save uploaded file');
        }
        else
          throw new CHttpException(500, 'Can`t upload file');
      }
    }
  }

  /**
   * Создание скрипта для редиректа после сабмита формы
   * @return void
   */
  protected function registerRedirectScript()
  {
    $script = 'setTimeout(function(){
      location.href = "'.$this->redirectUrl.'";
    }, '.$this->redirectDelay.');';

    Yii::app()->getClientScript()->registerScript("redirectAfterSubmit", $script, CClientScript::POS_LOAD);
  }

  protected function renderLayoutViews($layout)
  {
    $replaceArray = array();

    if( preg_match_all('/{view:(.+)}/', $layout, $matches) )
    {
      foreach($matches[0] as $key => $value)
        $replaceArray[$value] = Yii::app()->controller->renderPartial($matches[1][$key], isset($this->layoutViewParams[$key]) ? $this->layoutViewParams[$key] : null, true);
    }

    return $replaceArray;
  }

  protected function modifySubmitButton($button)
  {
    if( !$this->ajaxSubmit || !isset($button->name) )
      return $button;

    if( !isset($button->attributes['ajax']) )
      $button->attributes['ajax'] = array();

    $button->attributes['ajax'] = CMap::mergeArray(array(
      'type' => 'POST',
      'dataType' => 'json',
      'beforeSend' => 'function(){
        //$("#'.$this->getActiveFormWidget()->id.'").data("settings").submitting = true;
        $.mouseLoader(true);
      }',
      'url' => $this->action,
      'success' => 'function(resp){checkResponse(resp, $("#'.$this->getActiveFormWidget()->id.'"))}',
      'error' => 'function(resp){alert(resp.responseText)}',
    ), $button->attributes['ajax']);

    $button->attributes['id'] = $this->getActiveFormWidget()->id.'_'.$button->name;

    return $button;
  }

  protected function beforeRender()
  {
    $this->returnUrl = Yii::app()->request->getUrl();
    $message = $this->getSuccessMessage();

    if( !empty($message) )
      return $message;
    else
    {
      $errors = $this->getErrorMessage();
      if( !empty($errors) )
      {
        $this->model->clearErrors();
        $this->model->addErrors($errors);
      }
    }

    if( $this->loadFromSession )
    {
      $this->loadFromSession();
    }

    if( $this->setUserData )
    {
      $this->setUserData();
    }
  }

  /**
   * @param FActiveRecord $model
   *
   * @return string
   */
  private function getSessionKey($model)
  {
    return 'form_'.$this->formName.get_class($model);
  }
}