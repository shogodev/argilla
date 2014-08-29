<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductCurrency model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property float $multiplier
 * @property float $rate
 * @property string $autorate_id
 */
class BProductCurrency extends BActiveRecord
{
  const RUB = 1;

  public function rules()
  {
    return array(
      array('name, rate', 'required'),
      array('rate, multiplier', 'numerical'),
      array('name, title', 'length', 'max' => 255),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'title' => 'Отображение',
      'rate' => 'Курс',
      'multiplier' => 'Множитель',
    ));
  }

  public function getAutoRate()
  {
    $rate = null;

    if( !empty($this->autorate_id) && file_exists($this->getPath()) )
    {
      $rate = file_get_contents($this->getPath());
    }

    return round($rate / 10000, 2);
  }

  /**
   * @return bool
   */
  public function updateAutoRate()
  {
    if( !empty($this->autorate_id) )
    {
      $this->rate = $this->getAutoRate();
      return $this->save();
    }

    return false;
  }

  public function getHints()
  {
    return CMap::mergeArray(parent::getHints(), $this->getAutoRate() ? array(
      'rate' => 'Автоматический курс: '.$this->getAutoRate(),
    ) : array());
  }

  public function getRate()
  {
    return $this->rate * (Utils::isDecimalEmpty($this->multiplier) ? 1 : $this->multiplier);
  }

  public function getPath()
  {
    return '/tmp/'.$this->autorate_id.'4.txt';
  }

  protected function afterSave()
  {
    $builder = Yii::app()->db->getCommandBuilder();
    $command = $builder->createSqlCommand("UPDATE {{product}} SET `price` = :rate * `price_raw` WHERE `currency_id` = :id;");
    $command->execute(array(':rate' => $this->getRate(), ':id' => $this->id));

    parent::afterSave();
  }
}