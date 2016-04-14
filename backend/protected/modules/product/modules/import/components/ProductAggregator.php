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

/**
 * Class ProductAggregator
 */
class ProductAggregator extends AbstractAggregator
{
  public $useModification = true;

  /**
   * @var array $product
   * пример:
   * ImportHelper::convertColumnIndexes(array(
   *  'content' => 's',
   *  'test' => array('t', 'defaultValue' => 1),
   *  'price' => array('o', 'value' => 5000),
   *  'visible' => array('q', 'value' => 1),
   *  'dump' => array('p', 'callback' => function($value, $data) { return preg_match('/^A/', $data[10]) ? 1 : 0; }),
   * ));
   */
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
   * @var string $parameterVariantsDelimiter разделитель вариантов параметров(для типов с множественными привязками, например checkbox)
   */
  public $parameterVariantsDelimiter = ',';

  /**
   * @var string $assignmentDelimiter разделитель для множественных привязок к assignment
   */
  public $assignmentDelimiter = ',';

  /**
   * @var array
   */
  public $parameter = array();

  /**
   * @var array $parameterCommon общие параметры, также доэны быть указанны в $parameter
   */
  public $parameterCommon = array();

  public $basketParameter = array();

  public $defaultParameterType = 'checkbox';

  public $defaultBasketParameterKey = 'basket';

  private $parameterAttributesCache = array();

  public function beforeProcessNewFile()
  {
    parent::beforeProcessNewFile();

    $this->parameterAttributesCache = array();
  }

  public function process($data, $rowIndex, $file, $groupIndex)
  {
    if( !isset($this->data[$groupIndex]) )
    {
      $this->data[$groupIndex] = array(
        'uniqueIndex' => $data[$this->product[$this->writer->uniqueAttribute]],
        'uniqueAttribute' => $this->writer->uniqueAttribute,
        'rowIndex' => $rowIndex,
        'file' => $file,
        'product' => $this->getProductData($data),
        'assignment' => $this->getAssignmentData($data),
        'parameter' => $this->getParameterData($data),
        'modification' => array()
      );
    }
    else
    {
      $url = isset($this->product['url']) ? Utils::translite($data[$this->product['url']]) : null;

      if( $this->useModification && $url && $this->data[$groupIndex]['product']['url'] != $url )
      {
        if( empty($this->data[$groupIndex]['modification']) )
        {
          $this->data[$groupIndex]['modification'][] = array(
            'product' => $this->data[$groupIndex]['product'],
            'parameter' => $this->data[$groupIndex]['parameter'],
          );
        }

        $this->data[$groupIndex]['modification'][] = array(
          'product' => $this->getProductData($data),
          'parameter' => $this->getParameterData($data),
        );
      }
      else if( !empty($this->basketParameter) )
      {
        foreach($this->basketParameter as $basketParameter)
        {
          $this->data[$groupIndex]['basketParameter'][] = $this->getBasketParameter($data, $basketParameter);
        }
      }
    }
  }

  protected function getBasketParameter($data, $basketParameter)
  {
    $basketParameters = array(
      //'common' => true,
      'key' => $this->defaultBasketParameterKey,
      'type' => 'checkbox'
    );

    foreach(array('articul', 'price', 'dump', 'external_id') as $attribute)
    {
      $columnIndex = $this->product[$attribute];
      $basketParameters[$attribute] = $this->dataFilterByAttribute($attribute, $data[$columnIndex]);
    }

    if( preg_match('/([А-Яа-я\s]+):(.+)/u', $data[$basketParameter], $matches) )
    {
      $basketParameters['name'] = trim($matches[1]);
      $basketParameters['value'] = trim($this->prepareParameterValue($matches[2], $basketParameters['type']));
    }

    return $basketParameters;
  }

  protected function getProductData($data)
  {
    $productAttributes = array();

    foreach($this->product as $attribute => $columnConfig)
    {
      $value = $this->preProcessValue($columnConfig, $data);
      $productAttributes[$attribute] = $this->dataFilterByAttribute($attribute, $value);
    }

    if( isset($productAttributes['price'], $productAttributes['price_old']) && $productAttributes['price_old'] <= $productAttributes['price'] )
      $productAttributes['price_old'] = 0;

    return $productAttributes;
  }

  protected function extractColumnIndex($columnConfig)
  {
    return is_array($columnConfig) ? Arr::get($columnConfig, 0) : $columnConfig;
  }

  protected function preProcessValue($columnConfig, $data)
  {
    if( !isset($data[$this->extractColumnIndex($columnConfig)]) )
      return '';

    $value = $data[$this->extractColumnIndex($columnConfig)];

    if( empty($value) && isset($columnConfig['defaultValue']) )
    {
      $value = $columnConfig['defaultValue'];
    }
    if( isset($columnConfig['value']) )
    {
      $value = $columnConfig['value'];
    }
    if( isset($columnConfig['callback']) && is_callable($columnConfig['callback']) )
    {
      $value = $columnConfig['callback']($value, $data);
    }

    return $value;
  }

  protected function getAssignmentData($data)
  {
    $associationData = array();

    foreach($this->assignment as $attribute => $columnConfig)
    {
      $value = $this->preProcessValue($columnConfig, $data);
      $value = $this->dataFilterByAttribute($attribute, $value);

      $associationData[$attribute] = $value;
    }

    return $associationData;
  }

  protected function getParameterData($data)
  {
    $parametersData = array();

    foreach($this->parameter as $columnConfig)
    {
      $value = $this->preProcessValue($columnConfig, $data);
      if( empty($value) )
        continue;

      $columnIndex = $this->extractColumnIndex($columnConfig);
      $parameterData = $this->getParameterAttributes($columnIndex);
      $parameterData['value'] = $this->prepareParameterValue($value, $parameterData['type']);

      $parametersData[] = $parameterData;
    }

    return $parametersData;
  }

  protected function getParameterAttributes($columnIndex)
  {
    if( !isset($this->parameterAttributesCache[$columnIndex]) )
    {
      if( preg_match('/(.*):(checkbox|text|select|radio)/ui', $this->header[$columnIndex], $matches) )
      {
        $this->parameterAttributesCache[$columnIndex] = array(
          'name' => $matches[1],
          'type' => $matches[2]
        );
      }
      else
      {
        $this->parameterAttributesCache[$columnIndex] = array(
          'name' => $this->header[$columnIndex],
          'type' => $this->defaultParameterType
        );
      }

      if( $index = array_search($columnIndex, $this->parameterCommon) )
      {
        $this->parameterAttributesCache[$columnIndex]['common'] = true;
      }
    }

    return $this->parameterAttributesCache[$columnIndex];
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

      case 'dump':
        $value = intval(intval($value) > 0);
        break;
    }

    return $value;
  }

  protected function prepareParameterValue($value, $type = null)
  {
    if( isset($type) && $type != 'text' && strpos($value, $this->parameterVariantsDelimiter) !== false )
      $value = explode($this->parameterVariantsDelimiter, $value);

    if( is_array($value) )
    {
      array_map(array($this, 'clearParameter'), $value);

      return $value;
    }

    return $this->clearParameter($value);
  }

  protected function clearParameter($value)
  {
    return trim($value);
  }
}