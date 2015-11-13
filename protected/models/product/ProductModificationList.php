<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property CDbCriteria $criteria
 */
class ProductModificationList extends ProductList
{
  public $parameterNameModel = 'ProductParameterName';

  public $parameterModel = 'ProductParameter';

  /**
   * @var CDbCriteria
   */
  public $parametersCriteria;

  public function init()
  {
    $this->criteria->compare('t.visible', 1);

    $prefix = $this->getTablePrefix();
    $this->parametersCriteria = new CDbCriteria();
    $this->parametersCriteria->addColumnCondition(array($prefix.'.section' => 1, $prefix.'.section_list' => 1, $prefix.'.key' => ProductParameter::BASKET_KEY), 'OR');
  }

  protected function afterFetchData($event)
  {
    $this->setImages();
    $this->setParameters();
  }

  protected function getModelName()
  {
    return 'Product';
  }
}