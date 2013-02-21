<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 *
 * @method static BMetaRoute model(string $class = __CLASS__)
 */
class BMetaRoute extends BActiveRecord
{
  public $globalVars = '{project}';

  public function rules()
  {
    return array(
      array('route', 'required'),
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

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('route', $this->route, true);
    $criteria->compare('title', $this->title, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
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
      $data[$key]['id']   = $key;
      $data[$key]['name'] = $key;
    }

    return $data;
  }

  public function getRoutesListOptions()
  {
    $existRoutes = array();
    $data        = $this->findAll();

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
    $variables = array();

    if( empty($this->models) )
      return array();

    $this->importFrontendModels();
    $models = explode(',', $this->models);

    foreach($models as $value)
    {
      $model = $value::model();
      if( is_a($model, 'CModel') )
      {
        $reflection = new ReflectionClass($model);
        $parse_vars = $this->parsePhpDocs($reflection->getDocComment());

        foreach($parse_vars as $var)
        {
          $var_name = substr($var, 1);
          $variables[$value][] = count($models) > 1 ? '{'.$value.':'.$var_name.'}' : '{'.$var_name.'}';
        }
      }
    }

    return $variables;
  }

  /**
   * Парсит phpDoc возвращает список свойств
   * @param $php_doc
   * @return array
   */
  private function parsePhpDocs($php_doc)
  {
    $data = array();

    if( preg_match_all('/\@property.*(\$\w+)/', $php_doc, $matches) )
      $data = $matches[1];

    return $data;
  }

  private function importFrontendModels()
  {
    $config = require Yii::getPathOfAlias('frontend.config.frontend').'.php';

    foreach($config['import'] as $import)
      if( preg_match('/^frontend.models.(.*)/', $import) )
        Yii::import($import);

    Yii::import('frontend.components.interfaces.*');
  }
}