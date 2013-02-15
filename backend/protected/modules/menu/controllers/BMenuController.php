<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class BMenuController extends BController
{
  public $name = 'Меню';

  public $modelClass = 'BFrontendMenu';

  public function actionSort()
  {
    $menu_id = Yii::app()->request->getPost('menu_id');
    $data    = Yii::app()->request->getPost('data');

    $menu = BFrontendMenu::model()->findByPk($menu_id);

    foreach( $data as $item )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('menu_id', $menu->id);
      $criteria->compare('item_id', $item['id']);
      $criteria->compare('type', $item['type']);

      $entry           = BFrontendMenuItem::model()->find($criteria);
      $entry->position = $item['position'];
      $entry->save();
    }
  }

  /**
   * Добавление / удаление записи меню
   */
  public function actionSwitchEntry()
  {
    BFrontendMenu::loadExtraModels();

    $type    = Yii::app()->request->getPost('type');
    $menu_id = Yii::app()->request->getPost('menu_id');
    $id      = Yii::app()->request->getPost('id');

    if( !class_exists($type) )
      $error = 'Класс не существует';
    elseif( !BFrontendMenu::model()->exists('id = :id', array(':id' => $menu_id)) )
      $error = 'Неверный id меню';
    else
    {
      $menu  = BFrontendMenu::model()->findByPk($menu_id);
      $model = $type::model()->findByPk($id);
      $menu->switchMenuEntryStatus($model);
    }

    if( empty($error) )
      echo CJSON::encode(array('status' => 'ok'));
    else
      echo CJSON::encode(array('error' => $error));
  }
}