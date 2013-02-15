<?php
/**
 * @property string $title
 * @property string $description
 * @property string $keywords
 */
class Meta extends CComponent
{
  public static $VARS_PATTERN = '/{(\w+)}|{(\w+):(\w+)}/';
  private $route;
  private $usedModels = array();
  private $usedClips = array();

  private $title;
  private $description;
  private $keywords;

  public function __construct($route, $default_title = '')
  {
    $this->route = $route;

    $model     = MetaMask::model();
    $meta_data = $model->getData(Yii::app()->request->requestUri);

    if( !empty($meta_data) )
      $this->initMetaData($meta_data, $default_title);
    else
    {
      $model     = MetaRoute::model();
      $meta_data = $model->getData($route);

      if( empty($meta_data) )
        $meta_data = $model->getData('default');

      $this->initMetaData($meta_data, $default_title);
    }
  }

  /**
   * Ищет модель в $data
   * @param $data
   */
  public function findModel($data)
  {
    if( !isset($data) )
      return;

    foreach($data as $model)
    {
      if( is_a($model, 'CModel') || is_a($model, 'CForm') )
      {
        if( is_a($model, 'CModel') )
          $modelName = get_class($model);
        else if( is_a($model, 'CForm') )
        {
          $model = $model->model;
          $modelName = get_class($model);
        }

        $this->usedModels[$modelName] = $model;

        $this->title       = $this->replaceVars($this->title, $model);
        $this->description = $this->replaceVars($this->description, $model);
        $this->keywords    = $this->replaceVars($this->keywords, $model);
      }
    }
  }

  /**
   * Пишем в базу найденные модели
   */
  public function saveUsedModels()
  {
    $model = MetaRoute::model()->find('route=:route', array(':route' => $this->route));

    if( empty($model) )
      $model = new MetaRoute();

    $model->route  = $this->route;
    $model->models = !empty($this->usedModels) ? implode(',', array_keys($this->usedModels)) : '';
    $model->clips  = !empty($this->usedClips) ? implode(',', array_keys($this->usedClips)) : '';
    $model->save();
  }

  public function getTitle()
  {
    return $this->clear($this->title);
  }

  public function getDescription()
  {
    return $this->clear($this->description);
  }

  public function getKeywords()
  {
    return $this->clear($this->keywords);
  }

  public function registerClip($id,  $value)
  {
    if( empty($id) )
      return;

    $this->usedClips[$id] = $id;

    $this->title       = strtr($this->title, array("{{$id}}" => $value));
    $this->description = strtr($this->description, array("{{$id}}" => $value));
    $this->keywords    = strtr($this->keywords, array("{{$id}}" => $value));
  }

  private function initMetaData($meta_data, $default_title)
  {
    $this->title       = isset($meta_data) ? $meta_data->title       : $default_title;
    $this->description = isset($meta_data) ? $meta_data->description : '';
    $this->keywords    = isset($meta_data) ? $meta_data->keywords    : '';
  }

  /**
   * Заменяет встречающиеся в $var переменные на значения свойств $model
   * @param $var
   * @param $model
   * @return string
   */
  private function replaceVars($var, $model)
  {
    //todo: мозно сделать оптимальней передава сразу все модели известные в afterRender и обатывать все здесь
    if( preg_match_all(self::$VARS_PATTERN, $var, $matches) )
    {
      $replace_data = array();

      // переменные без указания моделей
      if( !empty($matches[1]) )
      {
        foreach($matches[1] as $key => $property)
        {
          if( ($property_value=$this->getPropertyValue($model, $property)) !== false)
            $replace_data[$matches[0][$key]] = $property_value;
        }
      }

      // переменные с указанием модели
      if( !empty($matches[2]) && !empty($matches[3]) )
      {
        foreach($matches[3] as $key => $property)
        {
          if( strtolower($matches[2][$key]) != strtolower(get_class($model)) )
            continue;

          if( ($property_value=$this->getPropertyValue($model, $property)) !== false)
            $replace_data[$matches[0][$key]] = $property_value;
        }
      }

      if( !empty($replace_data) )
        $var = strtr($var, $replace_data);

      $var = strtr($var, array('{project}' => Yii::app()->params->project));
    }

    return $var;
  }

  /**
   * Вырезает незамененные переменные в $string
   *
   * @param string $string
   *
   * @return string
   */
  private function clear($string)
  {
    $string = preg_replace(self::$VARS_PATTERN, '', $string);

    return $string;
  }

  private function getPropertyValue($model, $property)
  {
    if( !isset($model->{$property}) )
      return false;

    $property_value = $model->{$property};

    if( is_object($property_value) || is_array($property_value) )
      return false;

    return $property_value;
  }
}
?>