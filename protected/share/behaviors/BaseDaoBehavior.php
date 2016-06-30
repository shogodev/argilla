<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BaseDaoBehavior extends SBehavior
{
  const VARIANT_TABLE = '{{product_param_variant}}';

  const PARAMETER_NAME_TABLE = '{{product_param_name}}';

  const PARAMETER_TABLE = '{{product_param}}';

  const PRODUCT_TABLE = '{{product}}';

  /**
   * @var CDbCommandBuilder $builder
   */
  protected $builder;

  public function init()
  {
    parent::init();

    $this->builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
  }

  /**
   * @param array $array
   * @param string $index
   *
   * @return array
   */
  protected function indexData(array $array, $index)
  {
    if( is_null($index) )
      return $array;

    $indexedData = array();
    foreach($array as $data)
    {
      $indexedData[$data[$index]] = $data;
    }

    return $indexedData;
  }
}