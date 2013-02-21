<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static BVacancy model(string $class = __CLASS__)
 */
class BVacancy extends BActiveRecord
{
  public $date_from;

  public $date_to;

  public function relations()
  {
    return array(
      'files' => array(self::HAS_MANY, 'BVacancyFile', 'parent'),
    );
  }

  public function rules()
  {
    return array(
      array('name, phone', 'required'),
      array('name, phone', 'length', 'max' => 255),
      array('content', 'safe'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'files' => 'Прикрепленные файлы',
      'name' => 'Имя',
      'content' => 'Комментарий',
    ));
  }

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('phone', $this->phone, true);
    $criteria->compare('name', $this->name, true);

    if( !empty($this->date_from) || !empty($this->date_to) )
      $criteria->addBetweenCondition('date', Utils::dayBegin($this->date_from), Utils::dayEnd($this->date_to));

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}