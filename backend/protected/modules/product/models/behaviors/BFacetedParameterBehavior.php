<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.behaviors
 *
 * @property BProductParamName $owner
 */
class BFacetedParameterBehavior extends CModelBehavior
{
  /**
   * @var integer
   */
  private $selection;

  public function events()
  {
    return CMap::mergeArray(parent::events(), array(
      'onBeforeSave' => 'beforeSave',
      'onAfterSave' => 'afterSave',
    ));
  }

  public function beforeSave()
  {
    $this->selection = $this->owner->selection;
  }

  public function afterSave()
  {
    $this->isSelectionChanged() && $this->owner->selection ? $this->add() : $this->remove();
  }

  private function add()
  {
    if( $this->find() === null )
    {
      $model = new BFacetedParameter();
      $model->parameter = $this->owner->id;
      $model->save();

      $this->reindex();
    }
  }

  private function remove()
  {
    if( $this->find() )
    {
      BFacetedParameter::model()->deleteAll($this->getCriteria());
      $this->reindex();
    }
  }

  /**
   * @return bool
   */
  private function isSelectionChanged()
  {
    return $this->selection == $this->owner->selection;
  }

  /**
   * @return BFacetedParameter
   */
  private function find()
  {
    return BFacetedParameter::model()->find($this->getCriteria());
  }

  /**
   * @return CDbCriteria
   */
  private function getCriteria()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('parameter', $this->owner->id);

    return $criteria;
  }

  /**
   * @return string
   */
  private function reindex()
  {
    $runner = new CConsoleCommandRunner();
    $runner->commands = array(
      'indexer' => array(
        'class' => 'frontend.commands.IndexerCommand',
      ),
    );

    ob_start();
    $runner->run(array('yiic', 'indexer', 'refresh'));
    return ob_get_clean();
  }
}