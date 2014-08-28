<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * @property string $header
 * @property string $title
 * @property string $description
 * @property string $keywords
 */
class Meta extends CApplicationComponent
{
  const VARIABLE_PATTERN = "/{([\w:]+)}/";

  const COMMAND_PATTERN = "/([a-z]+)\(([^()]+)\)/";

  /**
   * @var array
   */
  public $exceptedModels = array('ProductAssignment', 'ProductParameterAssignment', 'ProductTreeAssignment');

  /**
   * @var array
   */
  public $replaces = array();

  /**
   * @var int
   */
  public $maxSearchDepth = 1;

  /**
   * @var FController
   */
  private $controller;

  /**
   * @var string
   */
  private $route;

  /**
   * @var string
   */
  private $requestUri;

  /**
   * @var FActiveRecord[]
   */
  private $renderedModels = array();

  /**
   * @var array
   */
  private $renderedClips = array();

  private $header;

  private $title;

  private $description;

  private $keywords;

  private $noindex;

  public function init()
  {
    parent::init();

    if( ($controller = Yii::app()->controller) )
    {
      $this->setController($controller);
      $this->controller->attachEventHandler('onBeforeRender', array($this, 'setRenderedModels'));
      $this->controller->attachEventHandler('onBeforeRenderLayout', array($this, 'registerMeta'));
    }

    Yii::app()->attachEventHandler('onEndRequest', array($this, 'updateRenderedModels'));

    if( isset(Yii::app()->request) )
      $this->setRequestUri(Yii::app()->request->requestUri);

    $this->replaces = array(
      '{project}' => Arr::get(Yii::app()->params, 'project'),
    );
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->clear($this->title);
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->clear($this->description);
  }

  /**
   * @return string
   */
  public function getKeywords()
  {
    return $this->clear($this->keywords);
  }

  /**
   * @param $header
   *
   * @return string
   */
  public function setHeader($header)
  {
    if( !empty($this->header) )
    {
      $header = $this->clear($this->header);
    }

    $this->registerClip('h1', $header);
    return $header;
  }

  /**
   * @param FController $controller
   */
  public function setController(FController $controller)
  {
    if( !isset($this->controller) )
    {
      $this->controller = $controller;
      $this->setRoute();
    }
  }

  /**
   * @param string $uri
   */
  public function setRequestUri($uri)
  {
    $this->requestUri = preg_replace('/\?.*/', '', $uri);
  }

  public function setMeta()
  {
    /**
     * @var MetaMask $metaMask
     * @var MetaRoute $metaRoute
     */
    $metaMask = MetaMask::model()->findByUri($this->requestUri);
    $metaRoute = MetaRoute::model()->findByRoute($this->route);

    foreach(array('header', 'title', 'keywords', 'description', 'noindex') as $property)
    {
      if( $metaRoute )
        $this->$property = Arr::get($metaRoute, $property);

      if( $metaMask && trim($metaMask->$property) !== '' )
        $this->$property = $metaMask->$property;
    }

    if( empty($this->title) )
    {
      $this->title = $this->controller->getPageTitle();
    }
  }

  /**
   * Собираем все модели, переданные в render()
   *
   * @param CEvent $event
   */
  public function setRenderedModels(CEvent $event)
  {
    $this->setRoute();
    $this->setMeta();

    $this->processModels(Arr::get($event->params, 'data', array()));
  }

  /**
   * @param FActiveRecord[] $models
   */
  public function addModels(array $models)
  {
    $this->processModels($models);
  }

  public function registerClip($id, $value)
  {
    $this->replaces['{'.$id.'}'] = $value;
    $this->renderedClips[$id] = $id;

    return $value;
  }

  /**
   * Запись в базу моделей, найденных во время рендеринга
   */
  public function updateRenderedModels()
  {
    if( strpos($this->route, '/') !== false )
    {
      if( !$model = MetaRoute::model()->resetScope()->findByRoute($this->route, false) )
        $model = new MetaRoute();

      $model->route  = $this->route;
      $model->models = implode(',', array_keys($this->renderedModels));
      $model->clips  = implode(',', array_keys($this->renderedClips));
      $model->save();
    }
  }

  public function registerMeta()
  {
    if( $clientScript = Yii::app()->clientScript )
    {
      $clientScript->registerMetaTag($this->getDescription(), 'description', null, array(), 'description');
      $clientScript->registerMetaTag($this->getKeywords(), 'keywords', null, array(), 'keywords');

      if( $this->noindex )
      {
        $clientScript->registerMetaTag('noindex, nofollow', 'robots', null, array(), 'robots');
      }
    }
  }

  private function setRoute()
  {
    if( isset($this->controller) )
      $this->route = $this->controller->route;
  }

  /**
   * Удаление переменных моделей в строке метатегов
   *
   * @param string $string
   *
   * @return string
   */
  private function clear($string)
  {
    $string = $this->replaceVariables($string);
    $string = $this->replaceCommands($string);
    $string = preg_replace(self::VARIABLE_PATTERN, '', $string);
    $string = preg_replace('/\s+/', ' ', $string);

    return CHtml::encode(trim($string));
  }

  /**
   * @param FActiveRecord[] $data
   */
  private function processModels(array $data)
  {
    static $depth;

    foreach($data as $model)
    {
      if( $model instanceof FActiveRecord )
        $modelName = get_class($model);
      else if( $model instanceof FForm )
      {
        $model = $model->model;
        $modelName = get_class($model);
      }
      else
        continue;

      if( in_array($modelName, $this->exceptedModels) )
        continue;

      $this->renderedModels[$modelName] = $model;

      if( $model instanceof FActiveRecord && $depth <= $this->maxSearchDepth )
      {
        $depth++;
        $this->processRelations($model);
      }
    }
  }

  /**
   * @param FActiveRecord $model
   */
  private function processRelations(FActiveRecord $model)
  {
    foreach($model->relations() as $name => $relation)
    {
      if( in_array($relation[0], array(FActiveRecord::HAS_ONE, FActiveRecord::BELONGS_TO)) )
      {
        if( !isset($this->renderedModels[$relation[1]]) )
        {
          $this->processModels(array($model->{$name}));
        }
      }
    }
  }

  /**
   * Замена встречающиеся переменных на значения свойств $model
   *
   * @param $string
   *
   * @return string
   */
  private function replaceVariables($string)
  {
    if( preg_match_all(self::VARIABLE_PATTERN, $string, $matches) )
    {
      if( !empty($matches[0]) )
      {
        foreach($matches[0] as $key => $value)
        {
          $replace = $matches[0][$key];
          $value = $matches[1][$key];
          $this->processValue($value, $replace);
        }
      }

      $string = strtr($string, $this->replaces);
    }

    return $string;
  }

  private function replaceCommands($string)
  {
    if( preg_match(self::COMMAND_PATTERN, $string, $matches) )
    {
      if( !empty($matches[0]) )
      {
        $command = $matches[1];
        $args = $matches[2];
        $value = $this->processCommand($command, $args);

        $string = strtr($string, array($matches[0] => $value));
        $string = $this->replaceCommands($string);
      }
    }

    return $string;
  }

  /**
   * @param $value
   * @param $replace
   */
  private function processValue($value, $replace)
  {
    if( strpos($value, ':') !== false )
    {
      list($modelName, $attribute) = explode(':', $value);
      $model = Arr::get($this->renderedModels, $modelName);

      if( $model && ($property = $this->getPropertyValue($model, $attribute)) )
      {
        $this->replaces[$replace] = $property;
      }
    }
  }

  /**
   * @param string $command
   * @param string $args
   *
   * @return string
   */
  private function processCommand($command, $args)
  {
    $args = Arr::trim(explode(',', $args));

    switch($command)
    {
      case 'ucfirst':
        return Utils::ucfirst($args[0]);
        break;

      case 'upper':
        return mb_strtoupper($args[0]);
        break;

      case 'lower':
        return mb_strtolower($args[0]);
        break;

      case 'wrap':
        $left = Arr::get($args, 1, '(');
        $right = Arr::get($args, 2, ')');
        return $left.$args[0].$right;
        break;

      case 'implode':
        return implode(trim(Arr::cut($args, 0), '\'"'), $args);
        break;

      default:
        return $args[0];
    }
  }

  /**
   * @param FActiveRecord $model
   * @param string $property
   *
   * @return string
   */
  private function getPropertyValue($model, $property)
  {
    return isset($model->$property) && !is_object($model->$property) && !is_array($model->$property) ? $model->$property : null;
  }
}