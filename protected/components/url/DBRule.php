<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.url
 *
 * <pre>
 * 'productCategory' => array('product/category', 'class' => 'DBRule', 'pattern' => '<category:\w+>', 'models' => array('category' => 'ProductCategory')),
 * </pre>
 */
class DBRule extends FUrlRule
{
  public $models = array();

  public function __construct($route, $pattern)
  {
    if( is_array($route) )
    {
      foreach(array('models') as $name)
      {
        if( isset($route[$name]) )
          $this->$name = $route[$name];
      }
    }

    parent::__construct($route, $pattern);
  }

  /**
   * @param FUrlManager $manager
   * @param CHttpRequest $request
   * @param string $pathInfo
   * @param string $rawPathInfo
   *
   * @return bool|string
   */
  public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
  {
    $manager->defaultParams = array();

    if( ($pathInfo = $this->preparePathInfo($manager, $request, $pathInfo, $rawPathInfo)) === false )
    {
      return false;
    }

    if( !empty($this->defaultParams) && !preg_match($this->pattern, $pathInfo, $matches)  )
    {
      $pathInfo .= implode('/', $this->defaultParams).'/';
      $manager->defaultParams = $this->defaultParams;
    }

    if( preg_match($this->pattern, $pathInfo, $matches) )
    {
      $result  = true;
      $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());

      foreach($this->models as $key => $class)
      {
        /**
         * @var FActiveRecord $class
         */
        $criteria = new CDbCriteria(array('limit' => 1));
        $criteria->compare('url', $matches[$key]);
        $command = $builder->createFindCommand($class::model()->tableName(), $criteria);

        if( !$command->queryAll() )
        {
          $result = false;
          break;
        }
      }

      return $result ? $this->getRoute($manager, $pathInfo, $matches) : false;
    }

    return false;
  }
}