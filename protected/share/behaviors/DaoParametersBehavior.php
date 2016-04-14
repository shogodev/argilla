<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class DaoParametersBehavior extends SBehavior
{
  const VARIANT_TABLE = '{{product_param_variant}}';

  const PARAMETER_NAME_TABLE = '{{product_param_name}}';

  protected $variants;

  protected $parametersName;

  /**
   * @var CDbCommandBuilder $builder
   */
  protected $builder;

  public function init()
  {
    parent::init();

    $this->builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
  }

  public function getVariantsByCondition($condition = '', array $parameters = array(), $refresh = false)
  {
    $criteria = new CDbCriteria();
    $criteria->condition = $condition;
    $criteria->params = $parameters;

    return $this->getVariantsByCriteria($criteria, $refresh);
  }

  public function getVariantsByCriteria(CDbCriteria $criteria, $refresh = false)
  {
    $key = serialize($criteria);

    if( !isset($this->variants[$key]) || $refresh )
    {
      $variants = array();
      $command = $this->builder->createFindCommand(self::VARIANT_TABLE, $criteria);
      foreach($command->queryAll() as $data)
      {
        $variants[$data['id']] = $data;
      }

      $this->variants[$key] = $variants;
    }

    return $this->variants[$key];
  }

  public function getParametersNameByCondition($condition = '', array $parameters = array(), $refresh = false)
  {
    $criteria = new CDbCriteria();
    $criteria->condition = $condition;
    $criteria->params = $parameters;

    return $this->getParametersNameByCriteria($criteria, $refresh);
  }

  public function getParametersNameByCriteria(CDbCriteria $criteria, $refresh = false)
  {
    $key = serialize($criteria);

    if( !isset($this->parametersName[$key]) || $refresh )
    {
      $variants = array();
      $command = $this->builder->createFindCommand(self::PARAMETER_NAME_TABLE, $criteria);
      foreach($command->queryAll() as $data)
      {
        $parametersName[$data['id']] = $data;
      }

      $this->parametersName[$key] = $variants;
    }

    return $this->parametersName[$key];
  }
}