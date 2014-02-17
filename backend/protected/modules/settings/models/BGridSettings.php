<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.settings
 */
Yii::import('backend.modules.product.models.*');

/**
 * @method static BGridSettings model(string $class = __CLASS__)
 * @method static BGridSettings visible()
 *
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property string $header
 * @property string $class
 * @property integer $type
 * @property integer $filter
 * @property integer $visible
 */
class BGridSettings extends BActiveRecord
{
  public $gridModel = 'BProduct';

  public function tableName()
  {
    return "{{settings_grid}}";
  }

  public function rules()
	{
		return array(
      array('name', 'required'),
      array('position, visible, filter', 'numerical', 'integerOnly' => true),
      array('name, header, class, type, filter', 'length', 'max' => 255),
      array('id, position, name, header', 'safe', 'on' => 'search'),
		);
	}

  public function scopes()
  {
    $alias = $this->getTableAlias();

    return array(
      'visible' => array(
        'condition' => $alias.'.visible=1'
      )
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Столбец',
      'header' => 'Заголовок',
      'class' => 'Действие',
      'type' => 'Вид',
      'filter' => 'Фильтр',
    ));
  }

  public function getFilter()
  {
    if( !empty($this->filter) )
    {
      switch($this->class)
      {
        default:
          return null;
      }
    }

    return false;
  }

  public function getClasses()
  {
    return array(
      'OnFlyEditField' => 'onfly редактирование',
      'JToggleColumn' => 'переключатель',
      'BEditColumn' => 'редактирование записи',
      'BProductAssignmentColumn' => 'привязки продукта',
    );
  }

  public function getTypes()
  {
    return array(
      'html' => 'html',
    );
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('id', $this->id, true);
    $criteria->compare('position', $this->position);
    $criteria->compare('filter', $this->filter);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('header', $this->header, true);

    return $criteria;
  }
}