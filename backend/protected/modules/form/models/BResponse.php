<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static BResponse model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $product_id
 * @property string $name
 * @property string $email
 * @property string $content
 * @property integer $visible
 *
 * @property BProduct $product
 */
class BResponse extends BActiveRecord
{
  public $date_from;

  public $date_to;

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('visible', 'numerical', 'integerOnly'=>true),
      array('product_id', 'length', 'max'=>10),
      array('name, email', 'length', 'max'=>255),
      array('content', 'safe'),
      array('date_from, date_to', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'product' => array(self::BELONGS_TO, 'BProduct', 'product_id'),
    );
  }

  public function attributeLabels()
  {
    return Cmap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя'
    ));
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('visible', '='.$this->visible);
    $criteria->compare('name', $this->email, true);

    if( !empty($this->date_from) || !empty($this->date_to) )
      $criteria->addBetweenCondition('date', Utils::dayBegin($this->date_from), Utils::dayEnd($this->date_to));

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}