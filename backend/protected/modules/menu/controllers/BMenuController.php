<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.controllers
 */
class BMenuController extends BController
{
  public $name = 'Меню';

  public $modelClass = 'BFrontendMenu';

  public function actionSetPosition()
  {
    BFrontendMenu::loadExtraModels();

    $gridId = Yii::app()->request->getPost('gridId');
    $id = Yii::app()->request->getPost('id');
    $menuId = BFrontendMenuGridView::getIdFromGridViewId($gridId);
    $position = Yii::app()->request->getPost('value');

    /**
     * @var BFrontendMenuItem $item
     */
    $item = BFrontendMenuItem::model()->findByAttributes(['menu_id' => $menuId, 'item_id' => $id]);

    if( $item !== null )
    {
      $item->position = $position;
      $item->save();

      echo $item->position;
      Yii::app()->end();
    }
  }

  /**
   * Добавление / удаление записи меню
   */
  public function actionSwitchEntry($type, $item_id, $menu_id)
  {
    BFrontendMenu::loadExtraModels();

    if( class_exists($type) && BFrontendMenu::model()->exists('id = :id', array(':id' => $menu_id)) )
    {
      $menu  = BFrontendMenu::model()->findByPk($menu_id);
      $model = $type::model()->findByPk($item_id);
      $menu->switchMenuEntryStatus($model);
    }
  }
}