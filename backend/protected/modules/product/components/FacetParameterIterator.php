<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.components.SqlIterator');

class FacetParameterIterator extends SqlIterator
{
  protected $parameterTable = '{{product_param}}';

  protected $parameterVariantTable = '{{product_param_variant}}';

  public function __construct(array $parameterList, $chunkSize, array $productIdList = null)
  {
    parent::__construct($this->getCriteria($parameterList, $productIdList), $this->parameterTable, $chunkSize);
  }

  public function current()
  {
    $row = parent::current();

    return $this->getAttributeList($row);
  }

  public function getAttributeList($row)
  {
    if( !($value = !empty($row['variant_id']) ? $row['variant_id'] : $row['value']) )
      return null;

    return array(array(
      'product_id' => $row['product_id'],
      'param_id' => $row['param_id'],
      'value' => $value
    ));
  }

  /**
   * @param $parameterList
   * @param $productIdList
   *
   * @return CDbCriteria
   */
  protected function getCriteria($parameterList, $productIdList)
  {
    $criteria = new CDbCriteria();
    $criteria->select = 't.id, t.param_id, t.product_id, t.variant_id, t.value, v.name';
    $criteria->join = 'LEFT OUTER JOIN '.Yii::app()->db->schema->quoteTableName($this->parameterVariantTable).' AS v ON v.id = t.variant_id';
    $criteria->addInCondition('t.param_id', $parameterList);

    if( !is_null($productIdList) )
      $criteria->addInCondition('t.product_id', $productIdList);

    return $criteria;
  }
}