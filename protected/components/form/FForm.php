<?php
/**
 * @property string returnUrl
 * @property string successMessage
 */
class FForm extends CForm
{
  public static $DEFAULT_FORMS_PATH = 'frontend.forms.';

  public $formName;

  public $layout = "{title}\n{elements}\n<div class=\"text-container form-hint\">{description}</div>\n<div class=\"form-submit\">{buttons}</div>\n";

  public $inputElementClass = 'FFormInputElement';

  public $activeForm = array('class' => 'CActiveForm',
                             'enableAjaxValidation' => true);

  public $ajaxSubmit = true;

  public $validateOnSubmit = true;

  public $validateOnChange = true;

  public $autocomplete     = false;

  public $loadFromSession  = false;

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
    if( is_string($config) /*&& strpos($config, '.') === false*/ )
    {
      $pathToForm     = self::$DEFAULT_FORMS_PATH.$config;
      $this->formName = get_class($model);
    }
    else
    {
      //$pathToForm     = get_class($model);
      //$this->formName = substr($config, strrpos($config, '.') + 1);
      $pathToForm     = $config;
      $this->formName = $config['name'];
    }

    parent::__construct($pathToForm, $model, $parent);
  }

  public function __toString()
  {
    return $this->render();
  }

  public function render()
  {
    $this->returnUrl = Yii::app()->request->getUrl();
    $message         = $this->getSuccessMessage();

    if( !empty($message) )
      return $message;
    else
    {
      $errors = $this->getErrorMessage();
      if( !empty($errors) )
        $this->model->addErrors($errors);
    }

    if( $this->loadFromSession )
    {
      $this->loadFromSession();
    }

    return parent::render();
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
   *
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
   * @param int    $delay
   * @param string $url
   *
   * @return void
   */
  public function setRedirectDelay($delay, $url = null)
  {
    $this->redirectDelay = $delay;
    $this->redirectUrl   = !empty($url) ? $url : Yii::app()->request->requestUri;
    $this->registerRedirectScript();
  }

  /**
   * @param null $defaultUrl
   *
   * @return mixed
   */
  public function getReturnUrl($defaultUrl = null)
  {
    return Yii::app()->user->getState('__'.$this->formName.'ReturnUrl', $defaultUrl===null ? Yii::app()->getRequest()->getScriptUrl() : CHtml::normalizeUrl($defaultUrl));
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
   * @return
   */
  public function getErrorMessage()
  {
    return Yii::app()->user->getFlash('__'.$this->formName.'Error');
  }

  public function renderBegin()
  {
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
    $output = array('{title}'       => $this->renderTitle(),
                    '{elements}'    => $this->renderElements(),
                    '{errors}'      => $this->getActiveFormWidget()->errorSummary($this->getModel()),
                    '{description}' => $this->description,
                    '{buttons}'     => $this->renderButtons());

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
    if( isset($element->type) && in_array($element->type, array('text', 'password')) )
      if( empty($element->attributes['class']) )
        $element->attributes['class'] = 'inp';
  }

  public function renderTitle()
  {
    $output = '';

    if($this->title !== null)
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
      if( $this->ajaxSubmit )
      {
        if( !isset($button->attributes['ajax']) )
          $button->attributes['ajax'] = array();

        $button->attributes['ajax'] = CMap::mergeArray(array(
                                                         'type'       => 'POST',
                                                         'dataType'   => 'json',
                                                         'beforeSend' => '$.mouseLoader(true)',
                                                         'url'        => $this->action,
                                                         'success'    => 'function(resp){checkResponse(resp, $("#'.$this->getActiveFormWidget()->id.'"))}',
                                                         'error'      => 'function(resp){alert(resp.responseText)}',
                                                        ), $button->attributes['ajax']);

        $button->attributes['id'] = $this->getActiveFormWidget()->id.'_'.$button->name;
      }
      $output .= $this->renderElement($button);
    }

    return $output !== '' ? $output : '';
  }

  /**
   * Renders a single element which could be an input element, a sub-form, a string, or a button.
   * @param mixed $element the form element to be rendered. This can be either a {@link CFormElement} instance
   * or a string representing the name of the form element.
   * @return string the rendering result
   */
  public function renderElement($element)
  {
    if(is_string($element))
    {
      if(($e=$this[$element])===null && ($e=$this->getButtons()->itemAt($element))===null)
        return $element;
      else
        $element=$e;
    }
    if($element->getVisible())
    {
      if($element instanceof CFormInputElement)
      {
        if($element->type==='hidden')
          return "<div style=\"visibility:hidden\">\n".$element->render()."</div>\n";
        else
        {
          return $element->render()."\n";
        }
      }
      else if($element instanceof CFormButtonElement)
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
   * @param string $message
   */
  public function responseSuccess($message = '')
  {
    echo json_encode(array('status' => 'ok', 'messageForm' => $message));
    Yii::app()->end();
  }

  /**
   * Валидация и сохранение данных в модель
   *
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
      $class         = get_class($this->getModel());
      $sessionParams = Yii::app()->request->getPost($class, array());

      if( !isset(Yii::app()->session['form_'.$class]) )
        Yii::app()->session['form_'.$class] = array();

      $sessionParams = CMap::mergeArray(Yii::app()->session['form_'.$class], $sessionParams);
      Yii::app()->session['form_'.$class] = $sessionParams;
    }

    foreach($this->getElements() as $element)
      if($element instanceof self)
        $element->saveToSession();
  }

  public function clearSession()
  {
    foreach($this->getModels() as $model)
      unset(Yii::app()->session['form_'.get_class($model)]);
  }

  public function loadFromSession()
  {
    if( $this->getModel() !== null )
    {
      $sessionParams = Yii::app()->session['form_'.get_class($this->getModel())];

      if( $sessionParams )
        $this->getModel()->setAttributes($sessionParams);
    }

    foreach($this->getElements() as $element)
      if($element instanceof self)
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
        $result[CHtml::activeId($this->getModel(),$attribute)] = $errors;

      foreach($this->getElements() as $element)
        if($element instanceof self)
          $result = CMap::mergeArray($result, $element->performAjaxValidation());
    }

    return $result;
  }

  public function isTabular()
  {
    return false;
  }

  public function sendNotification($data = array(), $email = '')
  {
    Yii::app()->notification->send($this->getModel(), $data, $email);
  }

  public function addLayoutViewParams($data)
  {
    $this->layoutViewParams[] = $data;
  }

  public function insertAfter($after, $element, $key)
  {
    $elements = $this->getElements()->toArray();
    Arr::insertAfter($elements, $after, $element, $key);

    $this->getElements()->clear();
    $this->getElements()->copyFrom($elements);
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
        $name = Utils::translite($file->name, false);

        while( file_exists($model->uploadPath.$name) )
          $name = Utils::doCustomFilename($name);

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
            $fileModel         = new $model->fileModel;
            $fileModel->name   = $name;
            $fileModel->parent = $model->id;
            $fileModel->size   = Yii::app()->format->formatSize($file->size);
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
   *
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
}