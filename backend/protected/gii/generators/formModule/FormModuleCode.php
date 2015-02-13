<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('application.gii.generators.model.*');
Yii::import('system.gii.generators.form.*');
Yii::import('system.gii.generators.controller.*');

class FormModuleCode extends CCodeModel
{
  public $className;

  public $label;

  public $connectionId = 'db';

  public $tableName;

  public $author;

  public $suffix = 'Form';

  public $formTemplate;

  public function rules()
  {
    return array_merge(parent::rules(), array(
      array('className, label, tableName, author', 'required'),
      array('className', 'match', 'pattern'=>'/^\w+$/'),
      array('formTemplate', 'safe')
    ));
  }

  public function attributeLabels()
  {
    return array_merge(parent::attributeLabels(), array(
      'className'=> 'Class Name',
      'label' => 'Название',
      'tableName' => 'Названте таблицы'
    ));
  }

  public function prepare()
  {
    Yii::app()->params['author'] = AuthorList::getByIndex($this->author);

    $formName = $this->className.$this->suffix;
    $backendModelClass = BApplication::CLASS_PREFIX.$formName;

    $this->createController(
      'application.gii.generators.controller.templates.backend',
      'application.modules.form.controllers',
      $backendModelClass,
      'BController',
      array('name' => $this->label)
    );

    $this->createModel(
      'application.gii.generators.model.templates.backend',
      'application.modules.form.models',
      $backendModelClass,
      'BActiveRecord'
    );

    $this->createBackendView(
      'application.gii.generators.form.templates.form',
      '_form',
      $backendModelClass
    );

    $this->createBackendView(
      'application.gii.generators.form.templates.index',
      'index',
      $backendModelClass
    );

    $frontendModelClass = $this->className;
    $this->createModel(
      'application.gii.generators.model.templates.frontend',
      'frontend.models',
      $frontendModelClass,
      'FActiveRecord'
    );

    $this->createFrontendForm($formName);

    $this->createBehavior('application.gii.generators.behavior.templates.frontend',
      'frontend.controllers.behaviors',
      $formName,
      'SBehavior',
      array(
        'name' => $this->label,
        'model' => $frontendModelClass,
        'form' => $formName
      )
    );

    $this->createController(
      'application.gii.generators.controller.templates.frontend',
      'frontend.controllers',
      $formName,
      'FController',
      array('behaviors' => array($formName.'Behavior'))
    );
  }

  private function createController($templatePath, $controllerPath, $controllerName, $baseClass, $params = array())
  {
    $controllerCode = new ControllerCode();
    $controllerCode->controller = $controllerName;
    $controllerCode->baseClass = $baseClass;
    $controllerCode->actions = '';

    $this->files[] = new CCodeFile(
      Yii::getPathOfAlias($controllerPath.'.'.$controllerName).'Controller.php',
      $controllerCode->render( Yii::getPathOfAlias($templatePath).'/controller.php', $params)
    );
  }

  private function createModel($templatePath, $modelPath, $modelName, $baseClass)
  {
    Yii::app()->controller->templates['model'] = Yii::getPathOfAlias($templatePath);

    $codeModel = new BModelCode();
    $codeModel->tableName = $this->tableName;
    $codeModel->modelClass = $modelName;
    $codeModel->template = 'model';
    $codeModel->modelPath = $modelPath;
    $codeModel->baseClass = $baseClass;
    $codeModel->buildRelations = true;
    $codeModel->commentsAsLabels = true;

    $codeModel->prepare();

    $this->files = CMap::mergeArray($this->files, $codeModel->files);
  }

  private function createBackendView($templatePath, $formName, $backendModelClass)
  {
    Yii::app()->controller->templates['form'] = Yii::getPathOfAlias($templatePath);

    $codeForm = new FormCode();
    $codeForm->attachBehavior('giiProperty', 'GiiPropertyBehavior');
    $codeForm->controller = BApplication::CLASS_PREFIX.$this->className.$this->suffix.'Controller';
    $codeForm->modelName = $backendModelClass;
    $codeForm->template = 'form';

    $codeForm->model = 'application.modules.form.models.'.$backendModelClass;
    $codeForm->viewPath = 'application.modules.form.views.'.$this->getViewDirName();
    $codeForm->viewName = $formName;

    try
    {
      $codeForm->validate();
    }
    catch(CException $e)
    {

    }
    $codeForm->prepare();
    $this->files = CMap::mergeArray($this->files, $codeForm->files);
  }

  private function getViewDirName()
  {
    $name = $this->className.$this->suffix;

    return strtolower(substr($name, 0, 1)).substr($name, 1);
  }

  private function createBehavior($templatePath, $behaviorPath, $behaviorName, $baseClass, $params = array())
  {
    $params = CMap::mergeArray($params, array(
      'baseClass' => $baseClass,
      'class' => $behaviorName.'Behavior',
    ));

    $this->files[]= new CCodeFile(
      Yii::getPathOfAlias($behaviorPath.'.'.$behaviorName).'Behavior.php',
      $this->render(Yii::getPathOfAlias($templatePath).'/behavior.php', $params)
    );
  }

  private function createFrontendForm($formName)
  {
    $table = Yii::app()->db->getSchema()->getTable($this->tableName);

    $templateData = $this->parseFormTemplate();

    $params = array(
      'cssClass' => Arr::get($templateData, 'formClass', ''),
      'columns' => $table->columns,
      'buttonClass' => Arr::get($templateData, 'buttonClass', ''),
      'buttonName' => Arr::get($templateData, 'buttonName', 'Отправить')
    );

    $this->files[]= new CCodeFile(
      Yii::getPathOfAlias('frontend.forms').'/'.$formName.'.php',
      $this->render(Yii::getPathOfAlias('application.gii.generators.form.templates.frontendForm').'/form.php', $params)
    );
  }

  private function parseFormTemplate()
  {
    $data = array();
    $this->formTemplate = substr($this->formTemplate, strpos($this->formTemplate, '<form'));
    $this->formTemplate = substr($this->formTemplate, 0, strpos($this->formTemplate, '</form')-6);

    $template = strtr($this->formTemplate, array("\n" => '', "\r" => '', '  ' => ' '));

    if( preg_match('/<form[^>]*class="([\w\-\s\_]+)"/i', $template, $matches) )
      $data['formClass'] = $matches[1];

    if( preg_match('/<(input|button)[^>]type="submit"[^>]value="([^"]+)"[^>]class="([\w\-\s\_]+)"/i', $template, $matches) )
    {
      $data['buttonName'] = $matches[2];
      $data['buttonClass'] = $matches[3];
    }

    return $data;
  }
} 