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
 * @property string  $url
 * @property string  $name
 * @property string  $notice
 * @property integer $visible
 *
 * @method static ProductSection model(string $class = __CLASS__)
 */
class ProductSection extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_section}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }

  /**
   * @return ProductSection[]
   */
  public function getSections()
  {
    $product    = Product::model()->tableName();
    $assignment = ProductAssignment::model()->tableName();

    $criteria            = new CDbCriteria();
    $criteria->select    = 't.id, t.name, t.notice, t.url';
    $criteria->join      = 'JOIN '.$assignment.' AS a ON t.id = a.section_id ';
    $criteria->join     .= 'JOIN '.$product.' AS p ON p.id = a.product_id';
    $criteria->condition = 'p.visible=1 AND a.visible=1';
    $criteria->distinct  = true;

    return $this->findAll($criteria);
  }

  public function getMenu($criteria = null)
  {
    $menu     = array();
    $sections = $this->getSections($criteria);

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