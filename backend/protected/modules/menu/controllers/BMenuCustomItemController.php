<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class BMenuCustomItemController extends BController
{
  /**
   * @var bool
   */
  public $enabled = false;

  /**
   * @var string
   */
  public $name = 'BFrontendMenuCustomItem';

  /**
   * @var string
   */
  public $modelClass = 'BFrontendCustomMenuItem';

  /**
   * @param BFrontendCustomMenuItem $model
   *
   * @return mixed|void
   */
  protected function actionSave($model)
  {
    $this->saveData($model);
    parent::actionSave($model);
  }

  /**
   * @param BFrontendCustomMenuItem $model
   */
  protected function saveData(BFrontendCustomMenuItem $model)
  {
    foreach( $model->data as $entry )
    {
      $entry->delete();
    }

    $data = Yii::app()->request->getPost('BFrontendCustomMenuItemData');

    if( $data !== null )
    {
      foreach( $data as $i )
      {
        if( empty($i) ) continue;

        $e         = new BFrontendCustomMenuItemData();
        $e->parent = $model->getId();
        $e->name   = $i['name'];
        $e->value  = $i['value'];
        $e->save();
      }
    }
  }
}