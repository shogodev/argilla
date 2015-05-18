<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('application.gii.generators.model.*');
Yii::import('system.gii.generators.form.*');
Yii::import('system.gii.generators.controller.*');

class BCodeModel extends CCodeModel
{
  public $className;

  public $label;

  public $connectionId = 'db';

  public $tableName;

  public $author;

  public $suffix = '';

  public $formTemplate;

  public $controllerPosition = 10;

  public function prepare()
  {

  }

  protected  function createModel($templatePath, $modelPath, $modelName, $baseClass)
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
    if( isset($this->module) && preg_match('/.([^\.]+)$/', $this->module, $matches) )
      $codeModel->module = $matches[1];

    $codeModel->prepare();

    $this->files = CMap::mergeArray($this->files, $codeModel->files);
  }

  protected function createBackendView($templatePath, $modulePath, $formName, $backendModelClass)
  {
    Yii::app()->controller->templates['form'] = Yii::getPathOfAlias($templatePath);

    $codeForm = new BFormCode();
    $codeForm->attachBehavior('giiProperty', 'GiiPropertyBehavior');
    $codeForm->controller = BApplication::CLASS_PREFIX.$this->className.$this->suffix.'Controller';
    $codeForm->modelName = $backendModelClass;
    $codeForm->template = 'form';

    Yii::import($modulePath.'.models.*');

    $codeForm->model = $modulePath.'.models.'.$backendModelClass;
    $codeForm->viewPath = $modulePath.'.views.'.$this->getViewDirName();
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

  protected function createController($templatePath, $controllerPath, $controllerName, $baseClass, $params = array())
  {
    $controllerCode = new ControllerCode();
    $controllerCode->controller = $controllerName;
    $controllerCode->baseClass = $baseClass;
    $controllerCode->actions = '';

    $controllerCode->attachBehavior('giiProperty', 'GiiPropertyBehavior');
    $controllerCode->controllerPosition = $this->controllerPosition;

    $this->files[] = new CCodeFile(
      Yii::getPathOfAlias($controllerPath.'.'.$controllerName).'Controller.php',
      $controllerCode->render( Yii::getPathOfAlias($templatePath).'/controller.php', $params)
    );
  }

  protected function createBehavior($templatePath, $behaviorPath, $behaviorName, $baseClass, $params = array())
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

  protected function createFrontendForm($formName)
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

  protected function parseFormTemplate()
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

  protected function getViewDirName()
  {
    $name = $this->className.$this->suffix;

    return strtolower(substr($name, 0, 1)).substr($name, 1);
  }
} 