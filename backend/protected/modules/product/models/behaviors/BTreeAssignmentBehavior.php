<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
 */

/**
 * Class BTreeAssignmentBehavior
 *
 * Поведение для задания родительских разделов у элементов каталога продукции
 * (для привязки секций к типам, коллекций к брендам и т.д.)
 *
 * Examples:
 * <pre>
 *
 * Привязка моделей BProductType к BProductSection
 *
 * models/BProductType.php
 *
 * public function behaviors()
 * {
 *   return array(
 *     'tree' => array('class' => 'BTreeAssignmentBehavior', 'parentModel' => 'BProductSection'),
 *   );
 * }
 *
 * public function attributeLabels()
 * {
 *   return CMap::mergeArray(parent::attributeLabels(), array(
 *     'parent_id' => 'Раздел',
 *   ));
 * }
 *
 * views/productType/_form.php:
 *
 * echo $form->dropDownListDefaultRow($model, 'parent_id', CHtml::listData($model->getParents(), 'id', 'name'));
 *
 * views/productType/index.php
 *
 * array('name' => 'parent_id', 'value' => '$data->parent ? $data->parent->name : null', 'filter' => CHtml::listData($model->getParents(), 'id', 'name')),
 *
 * </pre>
 *
 * @property BProductStructure $owner
 * @property BProductStructure $parent
 */
class BTreeAssignmentBehavior extends SActiveRecordBehavior
{
  /**
   * @var integer
   */
  public $parent_id;

  /**
   * @var string
   */
  public $parentModel;

  /**
   * @var BProductTreeAssignment
   */
  private $model;

  /**
   * @var string
   */
  private $rowName;

  /**
   * @var string
   */
  private $relationName;

  /**
   * @var string
   */
  private $ownerName;

  /**
   * @throws CHttpException
   */
  public function init()
  {
    if( !isset($this->parentModel) )
    {
      throw new CHttpException(500, 'Не задан атрибут parentModel');
    }

    $this->initAttributes();
    $this->attachRelations();
    $this->attachValidators();
    $this->attachEvents();
  }

  public function beforeSearch(CEvent $event)
  {
    /**
     * @var CDbCriteria $criteria
     */
    $criteria = $event->params['criteria'];
    $criteria->together = true;
    $criteria->with = array('parent');
    $criteria->compare('parent.id', $this->parent_id);

    return $criteria;
  }

  public function afterSave($event)
  {
    $model = $this->getModel();
    $model->dst_id = $event->sender->parent_id;
    $model->src_id = $event->sender->getPrimaryKey();

    return $model->save();
  }

  public function afterDelete($event)
  {
    $this->getModel()->delete();
  }

  public function getParents()
  {
    /**
     * @var BProductStructure $model
     */
    $model = new $this->parentModel;
    return $model->findAll();
  }

  private function initAttributes()
  {
    $parent = new $this->parentModel;

    $this->ownerName = $this->owner->getRelationName(get_class($this->owner));
    $this->relationName = $this->owner->getRelationName(get_class($parent));
    $this->rowName = $this->owner->getRowName(get_class($parent));
    $this->parent_id = $this->getModel()->dst_id;
  }

  private function attachRelations()
  {
    $params = array(
      ':src' => $this->ownerName,
      ':dst' => $this->relationName,
    );

    $this->owner->getMetaData()->addRelation('treeAssignment', array(
      BActiveRecord::HAS_MANY, 'BProductTreeAssignment', 'src_id', 'on' => 'src=:src', 'params' => $params,
    ));

    $this->owner->getMetaData()->addRelation('parent', array(
      BActiveRecord::HAS_ONE, $this->parentModel, 'dst_id', 'on' => 'dst=:dst', 'through' => 'treeAssignment', 'params' => $params,
    ));
  }

  private function attachValidators()
  {
    $this->owner->getValidatorList()->add(
      CValidator::createValidator('CRequiredValidator', $this->owner, 'parent_id')
    );
  }

  private function attachEvents()
  {
    $this->owner->attachEventHandler('onBeforeSearch', array($this, 'beforeSearch'));
  }

  /**
   * @return BProductTreeAssignment
   */
  private function getModel()
  {
    if( !isset($this->model) )
    {
      $attributes = array(
        'src' => $this->ownerName,
        'src_id' => $this->owner->getPrimaryKey(),
        'dst' => $this->relationName,
      );

      if( !$this->model = BProductTreeAssignment::model()->findByAttributes($attributes) )
      {
        $this->model = new BProductTreeAssignment();
        $this->model->setAttributes($attributes);
      }
    }

    return $this->model;
  }
}