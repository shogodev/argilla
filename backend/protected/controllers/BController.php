<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.controllers
 */
abstract class BController extends CController
{
  public $position = 0;

  public $name = '[Не задано]';

  public $layout = '//layouts/column1';

  /**
   * @var array
   */
  public $breadcrumbs = array();

  public $modelClass = 'BActiveRecord';

  public $popup = false;

  public function getViewPath()
  {
    if( ($module = $this->getModule()) === null )
      $module = Yii::app();

    $mappedId = array_search(get_class($this), $module->controllerMap);
    $id       = $mappedId ? $mappedId : $this->getId();

    return $module->getViewPath().DIRECTORY_SEPARATOR.$id;
  }

  public function filters()
  {
    return array(
      'postOnly + delete',
    );
  }

  public function beforeAction($action)
  {
    if( !AccessHelper::init()->checkAccess() )
    {
      if( !Yii::app()->user->isGuest )
        throw new CHttpException(403, 'Доступ запрещен.');
      else
      {
        Yii::app()->user->setReturnUrl(Yii::app()->request->requestUri);
        $this->redirect(Yii::app()->baseUrl . '/base');
      }
    }

    if( in_array($action->id, array('index')) )
    {
      $url = Yii::app()->request->url;
      Yii::app()->user->setState($this->uniqueId, $url);
    }

    $this->popup = Yii::app()->request->getQuery('popup', false);
    if( $this->popup )
      $this->layout = '//layouts/popup';
    return parent::beforeAction($action);
  }

  public function getBackUrl()
  {
    return Utils::cutQueryParams(Yii::app()->user->getState($this->uniqueId), array('ajax'));
  }

  public function actions()
  {
    return array('delete'        => 'BDefaultActionDelete',
                 'deleteRelated' => 'BRelatedActionDelete',
                 'association'   => 'BSaveAssociationAction',
                 'switch'        => 'ext.jtogglecolumn.SwitchAction',
                 'toggle'        => 'ext.jtogglecolumn.ToggleAction',
                 'onflyedit'     => 'ext.onflyedit.OnFlyEditAction',
                 'upload'        => 'upload.actions.UploadAction',
                 'directory'     => 'backend.modules.directory.actions.DirectoryAction',
                );
  }

  /**
   * Делаем редирект.
   * Если приложение запущено с тестовым конфигом, то записываем урл в сессию
   *
   * @param mixed $url
   * @param bool  $terminate
   * @param integer $statusCode
   *
   * @return string|void
   */
  public function redirect($url, $terminate = true, $statusCode = 302)
  {
    if( Yii::app()->params['mode'] === 'test' )
    {
      Yii::app()->user->setFlash('redirect', $url);
    }
    else
    {
      parent::redirect($url, $terminate, $statusCode);
    }
  }

  /**
   * @param string $view
   * @param null   $data
   * @param bool   $return
   *
   * @return string|void
   */
  public function render($view, $data = null, $return = false)
  {
    if( Yii::app()->params['mode'] === 'test' )
    {
      Yii::app()->user->setFlash('render', $data);
    }
    else
    {
      parent::render($view, $data, $return);
    }
  }

  /**
   * @param $id
   * @param string $modelClass
   *
   * @return mixed
   * @throws CHttpException
   */
  public function loadModel($id, $modelClass = null)
  {
    $class = $modelClass ? $modelClass : $this->modelClass;
    $model = $class::model()->findByPk($id);

    if( $model === null )
      throw new CHttpException(404, 'The requested page does not exist.');

    return $model;
  }

  public function actionIndex()
  {
    $dataProvider = new BActiveDataProvider($this->modelClass);

    $this->render('index', array(
      'model' => $this->createFilterModel(),
      'dataProvider' => $dataProvider,
    ));
  }

  public function actionCreate()
  {
    $this->actionSave(new $this->modelClass);
  }

  /**
   * @param $id
   */
  public function actionUpdate($id)
  {
    $this->actionSave($this->loadModel($id));
  }

  public function isUpdate()
  {
    return $this->action->id == 'update' ? true : false;
  }

  /**
   * Стандартный автокомплит для моделей
   *
   * $_GET['model'] - название модели для поиска
   * $_GET['field'] - поле для поиска
   * $_GET['q']     - значение
   *
   * @return void
   */
  public function actionAutocomplete()
  {
    if( !Yii::app()->request->isAjaxRequest )
      return;

    $modelClass = Yii::app()->request->getParam('model');
    $field      = Yii::app()->request->getParam('field');
    $value      = Yii::app()->request->getParam('q');

    if( $modelClass && $field && $value )
    {
      $criteria = new CDbCriteria();
      $criteria->addSearchCondition($field, $value);
      $criteria->limit = 10;

      $data   = $modelClass::model()->findAll($criteria);
      $answer = array();

      foreach( $data as $item )
        if( !in_array($item->$field, $answer) )
          $answer[] = $item->$field;

      echo implode("\n", $answer);
    }
  }

  /**
   * @return BActiveRecord
   */
  protected function createFilterModel()
  {
    $attributes = Yii::app()->request->getQuery($this->modelClass);
    $model = new $this->modelClass('search');
    $model->unsetAttributes();

    if( !empty($attributes) )
      $model->attributes = $attributes;

    return $model;
  }

  /**
   * @param BActiveRecord $model
   */
  protected function saveModel($model)
  {
    $this->performAjaxValidation($model);
    $attributes = Yii::app()->request->getPost(get_class($model));

    if( isset($attributes) )
    {
      $model->setAttributes($attributes);

      if( $model->save() )
      {
        $this->redirectAfterSave($model);
      }
    }
  }

  /**
   * Проводим валидацию и сохраняем несколько связанных моделей
   * Все модели должны быть связаны по первичному ключу
   *
   * @param $models
   * @param bool $extendedSave пытаемся сохранить все данные post, вызывая соответствующие методы контроллера
   *
   * @throws CHttpException
   */
  protected function saveModels($models, $extendedSave = true)
  {
    $this->performAjaxValidationForSeveralModels($models);

    if( Yii::app()->request->isPostRequest && $this->validateModels($models) )
    {
      Yii::app()->db->beginTransaction();

      $modelNames   = array_map(function($data){return get_class($data);}, $models);
      $unsavedKeys  = array_diff(array_keys($_POST), $modelNames);
      $primaryModel = $models[0];

      foreach($models as $key => $model)
      {
        if( $key !== 0 )
          $model->setPrimaryKey($primaryModel->getPrimaryKey());

        if( !$model->save(false) )
        {
          Yii::app()->db->currentTransaction->rollback();
          throw new CHttpException(500, 'Can`t save '.get_class($model).' model');
        }
      }

      if( $extendedSave )
        $this->saveMaximumPostData($unsavedKeys, $primaryModel);

      Yii::app()->db->currentTransaction->commit();
      $this->redirectAfterSave($primaryModel);
    }
  }

  /**
   * @param BActiveRecord[] $models
   *
   * @return bool
   */
  protected function validateModels($models)
  {
    $valid = !empty($models);

    foreach($models as $model)
    {
      $post = Yii::app()->request->getPost(get_class($model));
      $model->setAttributes($post);
      $valid = $model->validate() && $valid && !empty($post);
    }

    return $valid;
  }

  protected function redirectAfterSave($model)
  {
    Yii::app()->user->setFlash('success', 'Запись успешно '.($model->isNewRecord ? 'создана' : 'сохранена').'.');

    if( Yii::app()->request->getParam('action') )
      $this->redirect($this->getBackUrl());
    else
      $this->redirect(array('update', 'id' => $model->id));
  }

  /**
   * @param BActiveRecord $model
   *
   * @return mixed
   */
  protected function actionSave($model)
  {
    $this->saveModels(array($model));
    $this->render('_form', array('model' => $model));
  }

  /**
   * Performs the AJAX validation.
   *
   * @param BActiveRecord $model the model to be validated
   */
  protected function performAjaxValidation($model)
  {
    if( Yii::app()->request->getPost('ajax') === $model->getFormId() )
    {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

  /**
   * @param BActiveRecord[] $models
   */
  protected function performAjaxValidationForSeveralModels($models)
  {
    if( Yii::app()->request->getPost('ajax') === $models[0]->getFormId() )
    {
      $result = array();

      foreach($models as $model)
      {
        $errors = CJavaScript::jsonDecode(CActiveForm::validate($model));
        $result = CMap::mergeArray($result, $errors);
      }

      echo CJavaScript::jsonEncode($result);
      Yii::app()->end();
    }
  }

  /**
   * Проверям наличие в контроллере методов, чтобы сохранить данные из post
   *
   * @param array $unsavedKeys
   * @param $primaryModel
   */
  protected function saveMaximumPostData(array $unsavedKeys, $primaryModel)
  {
    foreach($unsavedKeys as $key)
    {
      $method = 'save'.$key;
      $data   = Yii::app()->request->getPost($key);

      if( is_array($data) && method_exists($this, $method) )
        call_user_func_array(array($this, $method), array($data, $primaryModel));
    }
  }
}