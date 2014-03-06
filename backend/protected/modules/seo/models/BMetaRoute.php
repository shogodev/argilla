<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo.models
 *
 * @method static BMetaRoute model(string $class = __CLASS__)
 */
class BMetaRoute extends BActiveRecord
{
  public $globalVars = '{project}';

  public function tableName()
  {
    return '{{seo_meta_route}}';
  }

  public function rules()
  {
    return array(
      array('route', 'unique'),
      array('route, title, description, keywords', 'length', 'max' => 255),
      array('route, title, description, keywords, visible' , 'safe'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.route ASC',
    );
  }

  public function afterFind()
  {
    if( !empty($this->clips) )
    {
      $clips = explode(',', $this->clips);
      $this->clips = '{'.implode('}, {', $clips).'}';
    }
  }

  public function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( !empty($this->clips) )
      {
        $clips = explode('}{', trim($this->clips, '}{'));
        $this->clips = implode(',', $clips);
      }

      return true;
    }

    return false;
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'clips' => 'Переменные шаблона',
      'globalVars' => 'Общие переменные',
      'route' => 'Маршрут',
      'title' => 'Title страницы',
    ));
  }

  /**
   * Получает список шаблонов маршрутов
   * @return array
   */
  public function getRoutesList()
  {
    $data = array(
      'default' => array(
        'id'   => 'default',
        'name' => 'default'
      ),
      'error' => array(
        'id'   => 'error/error',
        'name' => 'error/error'
      )
    );

    $routes = require_once Yii::getPathOfAlias('frontend.config')."/routes.php";

    foreach($routes as $value)
    {
      $key = reset($value);
      $data[$key]['id']= $key;
      $data[$key]['name'] = $key;
    }

    return $data;
  }

  public function getRoutesListOptions()
  {
    $existRoutes = array();
    $data = $this->findAll();

    if( !empty($data) )
      foreach($data as $value)
        $existRoutes[$value->route] = array('class' => 'bb');

    return array('options' => $existRoutes);
  }

  /**
   * Получает список переменных моделей
   * @return array
   */
  public function getModelVariables()
  {
    if( empty($this->models) )
      return array();

    $this->importFrontendModels();

    $variables = array();

    $models = explode(',', $this->models);
    foreach($models as $model)
    {
      $reflection = new ReflectionClass($model);

      if( $this->isModel($reflection) )
      {
        $findVariables = $this->parsePhpDocs($reflection->getDocComment());

        foreach($findVariables as $var)
        {
          $variableName = substr($var, 1);
          $variables[$model][] = count($models) > 1 ? '{'.$model.':'.$variableName.'}' : '{'.$variableName.'}';
        }
      }
    }

    return $variables;
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('route', $this->route, true);
    $criteria->compare('title', $this->title, true);
    $criteria->compare('visible', $this->visible);

    return $criteria;
  }

  /**
   * Парсит phpDoc возвращает список свойств
   * @param $php_doc
   * @return array
   */
  private function parsePhpDocs($php_doc)
  {
    $data = array();

    if( preg_match_all('/\@property\ +string\ +(\$\w+)/', $php_doc, $matches) )
      $data = $matches[1];

    return $data;
  }

  private function importFrontendModels()
  {
    $config = require Yii::getPathOfAlias('frontend.config.frontend').'.php';

    foreach($config['import'] as $import)
      if( preg_match('/^frontend.models.(.*)/', $import) )
        Yii::import($import);

    Yii::import('frontend.components.ar.*');
  }

  private function isModel($reflection)
  {
    while( $parent = $reflection->getParentClass() )
    {
      if( $parent->name == 'CModel' )
        return true;
      $reflection = $parent;
    }

    return false;
  }
}