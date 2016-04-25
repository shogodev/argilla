<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.behaviors.BaseDaoBehavior');

class DaoParametersBehavior extends BaseDaoBehavior
{
  protected $variants;

  protected $parameters;

  protected $parametersName;

  /**
   * @param string $condition
   * @param array $parameters
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getVariantsByCondition($condition = '', array $parameters = array(), $refresh = false, $indexBy = 'id')
  {
    $criteria = new CDbCriteria();
    $criteria->condition = $condition;
    $criteria->params = $parameters;

    return $this->getVariantsByCriteria($criteria, $refresh, $indexBy);
  }

  /**
   * @param CDbCriteria $criteria
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getVariantsByCriteria(CDbCriteria $criteria, $refresh = false, $indexBy = null)
  {
    $key = serialize($criteria);

    if( !isset($this->variants[$key]) || $refresh )
    {
      $command = $this->builder->createFindCommand(self::VARIANT_TABLE, $criteria);
      $this->variants[$key] = $this->indexData($command->queryAll(), $indexBy);
    }

    return $this->variants[$key];
  }

  /**
   * @param string $condition
   * @param array $parameters
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getParametersNameByCondition($condition = '', array $parameters = array(), $refresh = false, $indexBy = 'id')
  {
    $criteria = new CDbCriteria();
    $criteria->condition = $condition;
    $criteria->params = $parameters;

    return $this->getParametersNameByCriteria($criteria, $refresh, $indexBy);
  }

  /**
   * @param CDbCriteria $criteria
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getParametersNameByCriteria(CDbCriteria $criteria, $refresh = false, $indexBy = null)
  {
    $key = serialize($criteria);

    if( !isset($this->parametersName[$key]) || $refresh )
    {
      $command = $this->builder->createFindCommand(self::PARAMETER_NAME_TABLE, $criteria);
      $this->parametersName[$key] = $this->indexData($command->queryAll(), $indexBy);
    }

    return $this->parametersName[$key];
  }

  /**
   * @param CDbCriteria $criteria
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getParametersByCriteria(CDbCriteria $criteria, $refresh = false, $indexBy = 'id')
  {
    $key = serialize($criteria);

    if( !isset($this->parameters[$key]) || $refresh )
    {
      $command = $this->builder->createFindCommand(self::PARAMETER_TABLE, $criteria);
      $this->parameters[$key] = $this->indexData($command->queryAll(), $indexBy);
    }

    return $this->parameters[$key];
  }

  /**
   * @param string $condition
   * @param array $parameters
   * @param bool $refresh сбросить кэш
   * @param string|null $indexBy использовать это поле в качестве ключа в выходном массиве
   *
   * @return array
   */
  public function getParametersByCriteriaByCondition($condition = '', array $parameters = array(), $refresh = false, $indexBy = null)
  {
    $criteria = new CDbCriteria();
    $criteria->condition = $condition;
    $criteria->params = $parameters;

    return $this->getParametersByCriteriaByCondition($criteria, $refresh, $indexBy);
  }
}