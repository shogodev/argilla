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
    }

    $this->attachRelations();

    $this->owner->attachEventHandler('onBeforeSearch', array($this, 'beforeSearch'));
    $this->attachEventHandler('onAfterRenderTableRow', array($this, 'onAfterRenderTableRow'));

    //to do: добавить параметр блокирующей отключение одного значения
    $this->owner->attachBehavior('radioToggleBehavior', array(
      'class' => 'RadioToggleBehavior',
      'conditionAttribute' => 'parent',
      'toggleAttribute' => 'default_modification'
    ));

    $this->owner->enableBehavior('radioToggleBehavior');
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

  private function attachRelations()
  {
    $this->owner->getMetaData()->addRelation('modifications', array(
      BActiveRecord::HAS_MANY, 'BProduct', array('parent' => 'id'),
    ));

    $this->owner->getMetaData()->addRelation('parentModel', array(
      BActiveRecord::HAS_ONE, 'BProduct', array('id' => 'parent'),
    ));
  }

  protected function onAfterRenderTableRow(CEvent $event)
  {
    if( empty($this->owner->modifications) && Yii::app()->controller->popup)
      return;

    /**
     * @var BGridView $grid
     */
    $grid = $event->sender;
    $oldDataProvider = $grid->dataProvider;
    $oldRowCssClassExpression = $grid->rowCssClassExpression;

    $dataProvider = new CArrayDataProvider($this->owner->modifications);
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