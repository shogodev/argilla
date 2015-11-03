<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 *
 * Для использования необходимо в шаблоне вызвать метод show().
 * Хлебные крошки автоматически сформируются в зависимости от модуля, контроллера, действия и модели
 *
 * Пример:
 * <pre>
 *   Yii::app()->breadcrumbs->show();
 * </pre>
 */
class BBreadcrumbsManager
{
  /**
   * @var string
   */
  public static $indexName = 'index';

  /**
   * @var BActiveRecord
   */
  private $model;

  /**
   * @var BModule
   */
  private $module;

  /**
   * @var BController
   */
  private $controller;

  /**
   * @var BController
   */
  private $defaultModuleController;

  /**
   * @var string
   */
  private $defaultControllerLink;

  /**
   * @var array
   */
  private $breadcrumbs = array();

  /**
   * Есть ли возможность вырезать ссылку на стандартный контроллер,
   * ставится в true только, если появляются похожие части хлебных крошек
   * (одинаковое название DefaultController::Controller)
   *
   * @var boolean
   */
  private $canCutDefaultControllerLink = false;

  public function init()
  {
    try
    {
      $this->controller = Yii::app()->controller;
      $this->module     = Yii::app()->controller->module;

      if( !($this->module instanceof BModule) )
        return;

      $this->initModel();
      $this->initDefaultController();

      $this->initBreadcrumbs();
    }
    catch( Exception $e )
    {
      Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'Breadcrumbs');
    }
  }

  /**
   * Присваивание текущему контроллеру приложения сформированных хлебных крошек
   */
  public function show()
  {
    Yii::app()->controller->breadcrumbs = $this->breadcrumbs;
  }

  protected function initModel()
  {
    $params = $this->controller->getActionParams();

    if( $this->controller->action->id === self::$indexName )
      $this->model = null;
    elseif( !empty($params['id']) )
      $this->model = $this->controller->loadModel($params['id']);
    else
    {
      $modelName   = $this->controller->modelClass;
      $this->model = new $modelName;
    }
  }

  /**
   * @throws BBreadcrumbsManagerException
   */
  protected function initDefaultController()
  {
    $controllerName  = $this->module->defaultController;
    $controllerClass = ucfirst($controllerName).'Controller';

    if( class_exists($controllerClass, false) === false )
      throw new BBreadcrumbsManagerException('Невозможно найти класс '.$controllerClass);

    $this->defaultModuleController = new $controllerClass($controllerName);
  }

  protected function initBreadcrumbs()
  {
    $this->defaultControllerLink = Yii::app()->createUrl($this->module->id.'/'.$this->defaultModuleController->id);

    if( empty($this->model) )
      $this->initBreadcumbsWithotModel();
    elseif( empty($this->model->id) )
      $this->initBreadcrumbsWithNewModel();
    else
      $this->initBreadcrumbsWithExistingModel();

    if( !$this->checkControllersNameDiff() && $this->canCutDefaultControllerLink )
      $this->cutModulelink();
  }

  /**
   * Инициализация хлебных крошек без участия модели (actionIndex)
   */
  protected function initBreadcumbsWithotModel()
  {
    $this->canCutDefaultControllerLink = true;

    $this->breadcrumbs = array(
      $this->defaultModuleController->name => $this->defaultControllerLink,
      $this->controller->name,
    );
  }

  /**
   * Инициализация хлебных крошек с новой записью
   */
  protected function initBreadcrumbsWithNewModel()
  {
    $this->breadcrumbs = array(
      $this->defaultModuleController->name => $this->defaultControllerLink,
      $this->controller->name => array(self::$indexName),
      'Создание'
    );
  }

  /**
   * Инициализация хлебных крошек с существующей записью
   */
  protected function initBreadcrumbsWithExistingModel()
  {
    $this->breadcrumbs = array(
      $this->defaultModuleController->name => $this->defaultControllerLink,
      $this->controller->name => array(self::$indexName),

    );

    if( $this->model instanceof BProduct && !empty($this->model->parent) )
    {
      $this->breadcrumbs['Родительский продукт'] = Yii::app()->createUrl('/product/product/update', array('id' => $this->model->parent));
      $this->breadcrumbs[] = '[Модификация] '.$this->prepareItemName();
    }
    else
    {
      $this->breadcrumbs[] = $this->prepareItemName();
    }
  }

  /**
   * проверка на совпадение имени стандартного и текущего контроллера
   *
   * @return boolean
   */
  protected function checkControllersNameDiff()
  {
    return $this->controller->name !== $this->defaultModuleController->name;
  }

  /**
   * Удаление дублирующей записи контроллера. см. $this->canCutDefaultControllerLink
   */
  protected function cutModulelink()
  {
    unset($this->breadcrumbs[$this->controller->name]);
  }

  /**
   * Получение названия записи
   * Приоритет по полям:
   *  1) name
   *  2) title
   *  3) id
   *
   * @return string
   */
  protected function prepareItemName()
  {
    if( !empty($this->model->name) )
      return $this->model->name;
    elseif( !empty($this->model->title) )
      return $this->model->title;
    else
      return $this->model->id;
  }
}

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
class BBreadcrumbsManagerException extends CException
{

}