<?php
/**
 * @method static BDirPayment model(string $className = __CLASS__)
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string $notice
 * @property int $visible
 */
class BDirPayment extends BActiveRecord
{
  const CASH = 1;

  const NON_CASH  = 2;

  const EPAY = 3;

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{dir_payment}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('notice, position, visible', 'safe'),
    );
  }

  /**
   * @return SActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria();

    $criteria->compare('name', $this->name, true);
    $criteria->compare('visible', $this->visible);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}