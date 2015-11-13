<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения:
 * 'parameterGridBehavior' => array('class' => 'backend.modules.product.modules.parameterGrid.ParameterGridBehavior', 'parameterKey' => 'basket')
 */

/**
 * Class ParameterGridBehavior
 * @property BProduct $owner
 */
class ParameterGridBehavior extends SActiveRecordBehavior
{
  public $parameterKey;

  private $parametersDataProvider;

  public function init()
  {
    if( empty($this->parameterKey) )
      throw new RequiredPropertiesException(get_class($this), 'parameterKey');
  }

  /**
   * @param $key
   *
   * @return BActiveDataProvider
   */
  public function getParametersDataProvider($key = null)
  {
    if( is_null($key) )
      $key = $this->parameterKey;

    if( !isset($this->parametersDataProvider[$key]) )
    {
      $criteriaParamName = new CDbCriteria();
      $criteriaParamName->compare('t.key', $key);
      $paramIds = BProductParamName::model()->listData('id', 'id', $criteriaParamName);

      $criteria = new CDbCriteria();
      $criteria->compare('t.product_id', $this->owner->id);
      $criteria->addInCondition('t.param_id', $paramIds);
      $criteria->with = array('variant');
      $criteria->order = 'IF(variant.position=0, 1, 0), variant.position';

      $this->parametersDataProvider[$key] = new BActiveDataProvider('BProductParam', array('criteria' => $criteria, 'pagination' => false));
    }

    return $this->parametersDataProvider[$key];
  }

  public function beforeSave($event)
  {
    $this->updatePrice();
    $this->updateAvailable();

    parent::beforeSave($event);
  }

  protected function updatePrice()
  {
    if( $this->getParametersDataProvider($this->parameterKey)->totalItemCount > 0 )
      $this->owner->price = Arr::reset($this->getParametersDataProvider($this->parameterKey)->getData())->price;
  }

  protected function updateAvailable()
  {
    if( $this->owner->dump == BProductDump::AVAILABLE_ORDER )
      return;

    $available = false;

    if( $this->getParametersDataProvider($this->parameterKey)->totalItemCount == 0)
    {
      if( !empty($this->owner->articul) )
        return;
    }
    else
    {
      foreach($this->getParametersDataProvider($this->parameterKey)->getData() as $productParam)
      {
        if( $productParam->dump )
        {
          $available = true;
          break;
        }
      }
    }

    $this->owner->dump = $available ? BProductDump::AVAILABLE : BProductDump::NOT_AVAILABLE;
  }
}