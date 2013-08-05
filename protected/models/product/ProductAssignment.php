<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static ProductAssignment model(string $class = __CLASS__) *
 *
 * @property int $product_id
 * @property int $section_id
 * @property int $type_id
 *
 */
class ProductAssignment extends FActiveRecord
{
  protected $menu = array();

  public function tableName()
  {
    return '{{product_assignment}}';
  }

  public function relations()
  {
    return array(
      'section' => array(self::BELONGS_TO, 'ProductSection', 'section_id'),
      'type' => array(self::BELONGS_TO, 'ProductType', 'type_id'),
    );
  }

  /**
   * @param CDbCriteria $defaultCriteria
   *
   * @return array
   */
  public function getAssignments(CDbCriteria $defaultCriteria = null)
  {
    $product = Product::model()->tableName();
    $section = ProductSection::model()->tableName();
    $type    = ProductType::model()->tableName();

    $criteria = new CDbCriteria();

    $criteria->select    = 't.section_id, t.type_id, t.category_id, t.collection_id, ';
    $criteria->select   .= 's.name AS section_name, s.url AS section_url, s.img AS section_img, ';
    $criteria->select   .= 'type.name AS type_name, type.url AS type_url';

    $criteria->join      = 'JOIN '.$product.' AS p ON p.id = t.product_id ';
    $criteria->join     .= 'JOIN '.$section.' AS s ON s.id = t.section_id ';
    $criteria->join     .= 'JOIN '.$type.' AS type ON type.id = t.type_id';

    $criteria->condition = 'p.visible=1 AND s.visible=1 AND type.visible=1';
    $criteria->distinct  = true;

    if( $defaultCriteria !== null )
      $criteria->mergeWith($defaultCriteria);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(self::tableName(), $criteria);
    $data    = $command->queryAll();

    return $data;
  }

  /**
   * @return array
   */
  public function getSectionMenu()
  {
    $menu        = array();
    $criteria    = new CDbCriteria();
    $assignments = $this->getAssignments($criteria);

    foreach($assignments as $item)
    {
      if( !isset($menu[$item['section_id']]) )
      {
        $menu[$item['section_id']] = array(
          'label' => $item['section_name'],
          'url' => array('product/section', 'section' => $item['section_url']),
          'items' => array(),
        );
      }

      if( !isset($menu[$item['section_id']]['items'][$item['type_id']]) )
      {
        $menu[$item['section_id']]['items'][$item['type_id']] = array(
          'label' => $item['type_name'],
          'url' => array('product/section',
            'section' => $item['section_url'],
            'type' => $item['type_url']
          )
        );
      }
    }

    return $menu;
  }
}