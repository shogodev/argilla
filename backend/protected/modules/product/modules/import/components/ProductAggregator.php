<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.modules.product.modules.import.components.*');
Yii::import('backend.modules.product.modules.import.components.exceptions.*');
Yii::import('backend.modules.product.modules.import.components.abstracts.AbstractAggregator');

class ProductAggregator extends AbstractAggregator
{
  public $groupByColumn;

  public $useModification = true;

  public $product = array(
    'name' => '',
    'articul' => '',
    'price' => '',
    'url' => '',
    'notice' => '',
    'content' => '',
    'visible' => '',
    'dump' => '',
  );

  public $assignment = array(
    'section_id' => '',
    'type_id' => '',
    'category_id' => '',
    'collection_id' => ''
  );

  /**
   * @var array
   */
  public $parameter = array();

  public function init()
  {
    if( !is_numeric($this->groupByColumn) )
      $this->groupByColumn = ImportHelper::lettersToNumber($this->groupByColumn);

    $this->convertColumnIndexes($this->product);
    $this->convertColumnIndexes($this->assignment);
    $this->convertColumnIndexes($this->parameter);
  }

  public function process($data, $rowIndex, $file)
  {
    $groupIndex = $data[$this->groupByColumn];

    if( !isset($this->data[$groupIndex]) )
    {
      $this->data[$groupIndex] = array(
        'uniqueIndex' => $data[$this->product['articul']],
        'uniqueAttribute' => 'articul',
        'rowIndex' => $rowIndex,
        'file' => $file,
        'product' => $this->getProductData($data),
        'assignment' => $this->getAssignmentData($data),
        'parameter' => $this->getParameterData($data),
        'modification' => array()
      );
    }
    else if( $this->useModification )
    {
      $this->data[$groupIndex]['modification'][] = array(
        'product' => $this->getProductData($data),
        'parameter' => $this->getParameterData($data),
      );
    }
  }

  protected function getProductData($data)
  {
    $productAttributes = array();
    foreach($this->product as $attribute => $columnIndex)
      $productAttributes[$attribute] = $this->dataFilterByAttribute($attribute, $data[$columnIndex]);

    return $productAttributes;
  }

  protected function getAssignmentData($data)
  {
    $associationData = array();

    foreach($this->assignment as $attribute => $columnIndex)
      $associationData[$attribute] = $this->dataFilterByAttribute($attribute, $data[$columnIndex]);

    return $associationData;
  }

  protected function getParameterData($data)
  {
    $parameterData = array();

    foreach($this->parameter as $columnIndex)
    {
      $name = preg_replace('/\:$/', '', $this->header[$columnIndex]);
      $parameterData[$name] = $data[$columnIndex];
    }

    return $parameterData;
  }

  protected function dataFilterByAttribute($attribute, $value)
  {
    switch($attribute)
    {
      case 'price':
      case 'price_old':
        $value = PriceHelper::clear($value);
        break;

      case 'url':
        $value = Utils::translite($value);
        break;

      case 'section_id':
        $value = empty($value) ? '[не задано]' : $value;
        break;
    }

    return $value;
  }
}