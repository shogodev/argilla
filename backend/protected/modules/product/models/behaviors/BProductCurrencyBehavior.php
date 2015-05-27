<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
*/

/**
 * Class BProductCurrencyBehavior
 *
 * Поведение для рассчета цены товара, заданной в валюте
 *
 * Examples:
 * <pre>
 * models/BProduct.php
 *
 * public function behaviors()
 * {
 *   return array(
 *     'currencyBehavior' => array('class' => 'BProductCurrencyBehavior'),
 *   );
 * }
 *
 * views/product/_form.php:
 *
 * echo $form->dropDownListRow($model, 'currency_id', BProductCurrency::model()->listData());
 * echo $form->textFieldRow($model, 'price_raw', array('class' => 'span4'));
 *
 * migrations/update_product_table
 *
 * $this->execute("ALTER TABLE `{{product}}` ADD `price_raw` DECIMAL( 10, 2 ) NULL AFTER `price`");
 * </pre>
 *
 * @property BProduct $owner
 * @property string $price_raw
 * @property integer $currency_id
 *
 */
class BProductCurrencyBehavior extends SActiveRecordBehavior
{
  /**
   * @throws CHttpException
   */
  public function init()
  {
    /**
     * @var BActiveRecord $owner
     */
    if( !array_key_exists('price_raw', $this->owner->attributes) )
    {
      throw new CHttpException(500, 'В модели отсутствует обязательный атрибут "price_raw"!');
    }
    $this->attachValidators();
  }

  public function beforeSave($event)
  {
    $this->setPrice();
  }

  private function setPrice()
  {
    /**
     * @var BProductCurrency $currency
     */
    if( !empty($this->owner->price_raw) )
    {
      $currency = BProductCurrency::model()->findByPk($this->owner->currency_id);
      $this->owner->price = $this->owner->price_raw * $currency->getRate();
    }
  }

  private function attachValidators()
  {
    $this->owner->getValidatorList()->add(
      CValidator::createValidator('CRequiredValidator', $this->owner, 'currency_id')
    );
    $this->owner->getValidatorList()->add(
      CValidator::createValidator('CNumberValidator', $this->owner, 'price_raw')
    );
  }
}