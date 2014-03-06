<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
 *
 * @property integer $id
 * @property string $route
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $models
 * @property string $clips
 * @property integer $visible
 */
class MetaRoute extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_meta_route}}';
  }

  public function getData($route)
  {
    return $this->find('route=:route AND visible=:visible', array(':route' => $route, ':visible' => '1'));
  }
}