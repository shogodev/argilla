<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 02.10.12
 * @package Compare
 */
class CompareController extends FController
{
  public function actionIndex()
  {
    $criteria = new CDbCriteria();
    $criteria->addCondition('(assignment.type_id IS NULL OR assignment.type_id = 0)');
    $parameters[0] = ProductParam::model()->getParameters($criteria);

    $this->render('compare', array(
      'parameters' => $parameters
    ));
  }

  public function actionAdd($id)
  {
    if( Yii::app()->request->isAjaxRequest )
      Yii::app()->compare->add($id);
  }

  public function actionRemove($id)
  {
    if( Yii::app()->request->isAjaxRequest )
      Yii::app()->compare->remove($id);
  }

  public function actionClear()
  {
    if( Yii::app()->request->isAjaxRequest )
      Yii::app()->compare->clear();
  }

  public function actionClearGroup($id)
  {
    if( Yii::app()->request->isAjaxRequest )
      Yii::app()->compare->clearGroup($id);
  }

  public function actionCount()
  {
    if( Yii::app()->request->isAjaxRequest )
      echo Yii::app()->compare->count;
  }
}
