<?php
/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $phone
 * @property string $person
 * @property string $img
 * @property integer $visible
 */
class BDealer extends BActiveRecord
{
  const TYPE_DEALER = 'dealer';

  public function behaviors()
  {
    return array('uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'));
  }

  public function rules()
  {
    return array(
      array('name', 'required'),
      array('phone, person, visible', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'user' => array(self::HAS_ONE, 'BFrontendUser', 'user_id')
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Имя',
      'person' => 'Контактное лицо',
    ));
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria = new CDbCriteria;
    $criteria->compare('visible', $this->visible);

    $criteria->compare('name', $this->name, true);
    $criteria->compare('phone', $this->phone, true);
    $criteria->compare('person', $this->person, true);

    return $criteria;
  }
}