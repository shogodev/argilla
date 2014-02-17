<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @property string $name
 * @property string $title
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 *
 * @method static BAuthItem model(string $class = __CLASS__)
 */
abstract class BAuthItem extends BActiveRecord
{
  /**
   * @return string
   */
  public function getId()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{auth_item}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
        array('title, name', 'required'),
        array('type', 'numerical', 'integerOnly' => true),
        array('name', 'length', 'max' => 64),
        array('title, description, bizrule, data', 'safe'),
    );
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('title', $this->title, true);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
        'title'       => 'Название',
        'name'        => 'Системное имя',
        'description' => 'Описание',
        'bizrule'     => 'Бизнес-логика',
        'data'        => 'Данные',
    ));
  }
}