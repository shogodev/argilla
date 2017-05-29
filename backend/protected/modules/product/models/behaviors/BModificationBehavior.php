<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример:
 * 'modificationBehavior' => array('class' => 'BModificationBehavior')
 */
/**
 * Class BModificationBehavior
 *
 * @property BProduct $owner
 * @property BProduct[] $modifications
 * @property BProduct|null $parentModel
 */
class BModificationBehavior extends SActiveRecordBehavior
{
  const SCENARIO_MODIFICATION = 'modification';

  public $gridTableRowHtmlOptions = array('class' => 'modification');

  public function init()
  {
    if( $this->owner->isNewRecord )
    {
      $this->owner->parent = Yii::app()->request->getParam('modificationParent');
      $this->owner->parentModel = BProduct::model()->findByPk($this->owner->parent);
    }

    $this->attachRelations();

    $this->owner->attachEventHandler('onBeforeSearch', array($this, 'beforeSearch'));
    $this->attachEventHandler('onAfterRenderTableRow', array($this, 'onAfterRenderTableRow'));
  }

  public function beforeSearch(CEvent $event)
  {
    /**
     * @var CDbCriteria $criteria
     */
    $criteria = $event->params['criteria'];

    $criteria->addCondition('t.parent IS NULL');

    return $criteria;
  }

  public function beforeValidate($event)
  {
    if( $this->isModification() )
      $this->owner->scenario = self::SCENARIO_MODIFICATION;

    return parent::beforeValidate($event);
  }

  public function beforeSave($event)
  {
    if( $this->isModification() )
    {
      $this->owner->detachEventHandler('onAfterSave', array(Yii::app()->controller, 'saveProductAssignment'));

      if( $this->owner->isNewRecord ) {
        $this->owner->getEventHandlers('onAfterSave')->insertAt(1, array($this, 'saveModificationAssignment'));
      }
    }
  }

  public function afterSave($event)
  {
    foreach($this->getModifications() as $modification) {
      $modification->saveModificationAssignment();
    }
  }

  /**
   * @return BProduct
   */
  public function getParentModel()
  {
    return $this->owner->parentModel;
  }

  /**
   * @return bool
   */
  public function isModification()
  {
    return !empty($this->owner->parent);
  }

  /**
   * @return bool
   */
  public function isParent()
  {
    return count($this->getModifications()) > 0;
  }

  /**
   * @return Product[]
   */
  public function getModifications()
  {
    return $this->owner->modifications;
  }

  public function getFacetProductIdList()
  {
    if( !$this->isParent() && !$this->isModification() )
      return array($this->owner->id);
    else if( $this->isParent() )
      return CHtml::listData($this->getModifications(), 'id', 'id');
    else if( $this->isModification() )
    {
      $parent = $this->getParentModel();
      return CHtml::listData($parent->getModifications(), 'id', 'id');
    }
  }

  public function getParentId()
  {
    if( $this->isParent() )
      return $this->owner->id;
    else if( $this->isModification() )
      return $this->owner->getParentModel()->id;

    return null;
  }

  private function attachRelations()
  {
    $this->owner->getMetaData()->addRelation('modifications', array(
      BActiveRecord::HAS_MANY, 'BProduct', array('parent' => 'id'), 'order' => 'modifications.dump DESC, IF(modifications.price=0, 1, 0), modifications.price ASC'
    ));

    $this->owner->getMetaData()->addRelation('parentModel', array(
      BActiveRecord::HAS_ONE, 'BProduct', array('id' => 'parent'),
    ));
  }

  protected function saveModificationAssignment()
  {
    $model = BProductAssignment::model();

    $assignments = array();
    foreach($model->getFields() as $field)
    {
      $attribute = $field->name;
      $assignments[$attribute] = $this->getParentModel()->{$attribute};
    }

    $model->saveAssignments($this->owner, $assignments);
  }

  protected function onAfterRenderTableRow(CEvent $event)
  {
    if( empty($this->owner->modifications) || Yii::app()->controller->popup)
      return;

    /**
     * @var BGridView $grid
     */
    $grid = $event->sender;
    $oldDataProvider = $grid->dataProvider;
    $oldRowCssClassExpression = $grid->rowCssClassExpression;

    $dataProvider = new CArrayDataProvider($this->owner->modifications, array('pagination' => false));
    $grid->dataProvider = $dataProvider;
    $grid->rowCssClassExpression = '"modification"';

    foreach($grid->dataProvider->getData() as $row => $data)
    {
      $grid->renderTableRow($row);
    }

    $grid->dataProvider = $oldDataProvider;
    $grid->rowCssClassExpression = $oldRowCssClassExpression;
  }
}