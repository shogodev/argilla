<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FormModule extends BCodeModel
{
  public $suffix = 'Form';

  public function rules()
  {
    return array_merge(parent::rules(), array(
      array('className, label, tableName, author', 'required'),
      array('className', 'match', 'pattern'=>'/^\w+$/'),
      array('formTemplate, controllerPosition', 'safe')
    ));
  }

  public function attributeLabels()
  {
    return array_merge(parent::attributeLabels(), array(
      'className'=> 'Class Name',
      'label' => 'Название',
      'tableName' => 'Название таблицы',
      'controllerPosition' => 'Позиция backend контроллера'
    ));
  }

  public function prepare()
  {
    Yii::app()->params['author'] = AuthorList::getByIndex($this->author);

    $formName = $this->className.$this->suffix;
    $backendModelClass = BApplication::CLASS_PREFIX.$formName;
    $modulePath = 'application.modules.form';

    $this->createController(
      'application.gii.generators.controller.templates.backend',
      $modulePath.'.controllers',
      $backendModelClass,
      'BController',
      array('name' => $this->label)
    );

    $this->createModel(
      'application.gii.generators.model.templates.backend',
      $modulePath.'.models',
      $backendModelClass,
      'BActiveRecord'
    );

    $this->createBackendView(
      'application.gii.generators.form.templates.form',
      $modulePath,
      '_form',
      $backendModelClass
    );

    $this->createBackendView(
      'application.gii.generators.form.templates.index',
      $modulePath,
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
}