<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @method static ProductAssignment model(string $class = __CLASS__) *
 *
 * @property int $product_id
 * @property int $section_id
 * @property int $type_id
 */
class ProductAssignment extends FActiveRecord
{
  protected $assignments;

  public function relations()
  {
    return array(
      'product' => array(self::BELONGS_TO, 'ProductProduct', 'product_id'),
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
    if( $this->assignments === null )
    {
      $criteria = $this->buildCriteria();

      if( $defaultCriteria !== null )
        $criteria->mergeWith($defaultCriteria);

      $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
      $command = $builder->createFindCommand(self::tableName(), $criteria);
      $this->assignments = $command->queryAll();
    }

    return $this->assignments;
  }

  public function setAssignments($assignments)
  {
    $this->assignments = $assignments;
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
          'url' => array('product/type',
            'type' => $item['type_url']
          )
        );
      }
    }

    return $menu;
  }

  private function buildCriteria()
  {
    $criteria = new CDbCriteria(array('distinct' => true));

    $criteria->select = array(
      't.section_id, t.type_id, t.category_id',
      'section.name AS section_name, section.url AS section_url',
      'type.name AS type_name, type.url AS type_url',
    );

    $condition = implode(" AND ", array(
      'product.visible = 1',
      'section.visible = 1',
      'type.visible = 1',
    ));

    $join = array(
      'product' => Product::model()->tableName(),
      'section' => ProductSection::model()->tableName(),
      'type'    => ProductType::model()->tableName(),
    );

    array_walk($join, function(&$value, $key){
      $value = PHP_EOL.'LEFT OUTER JOIN '.$value.' AS '.$key.' ON '.$key.'.id = t.'.$key.'_id';
    });

    $criteria->join = implode(" ", $join);
    $criteria->condition = $condition;

    return $criteria;
  }
}