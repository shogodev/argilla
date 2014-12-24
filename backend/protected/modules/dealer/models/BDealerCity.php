<?php
/**
 * @method static BDealerCity model(string $className = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property integer $parent_id
 * @property integer $visible
 */
class BDealerCity extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('name', 'required'),
      array('visible', 'safe'),
    );
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('visible', $this->visible);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}