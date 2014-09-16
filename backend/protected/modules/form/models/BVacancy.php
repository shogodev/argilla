<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static BVacancy model(string $class = __CLASS__)
 */
class BVacancy extends BActiveRecord
{
  public function behaviors()
  {
    return array(
      'dateFilterBehavior' => array(
        'class' => 'DateFilterBehavior',
        'attribute' => 'date',
      )
    );
  }

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

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('phone', $this->phone, true);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}