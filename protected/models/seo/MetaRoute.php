<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
 *
 * @method static MetaRoute model(string $class = __CLASS__)
 *
 * @property string $route
 * @property string $header
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $models
 * @property string $clips
 * @property integer $noindex
 * @property integer $visible
 */
class MetaRoute extends FActiveRecord
{
  const DEFAULT_ROUTE = 'default';

  public function tableName()
  {
    return '{{seo_meta_route}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  public function findByRoute($route, $findDefault = true)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('route', $route);

    if( $findDefault )
    {
      $criteria->addCondition('route = :default', 'OR');
      $criteria->order = 'IF(route = :default, 1, 0)';
      $criteria->params[':default'] = self::DEFAULT_ROUTE;
    }

    return $this->find($criteria);
  }
}