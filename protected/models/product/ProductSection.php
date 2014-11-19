<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property string $url
 * @property string $img
 * @property string $notice
 * @property integer $visible
 *
 * @method static ProductSection model(string $class = __CLASS__)
 *
 * @property FActiveImage $image
 */
class ProductSection extends FActiveRecord
{
  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }

  public function getMenu($criteria = null)
  {
    /**
     * @var ProductSection[] $sections
     */
    $menu = array();
    $sections = ProductAssignment::model()->getModels('ProductSection', $criteria);

    foreach($sections as $section)
    {
      $menu[$section->id] = array(
        'id' => $section->id,
        'label' => $section->name,
        'url' => array('product/section', 'section' => $section->url)
      );
    }

    return $menu;
  }
}