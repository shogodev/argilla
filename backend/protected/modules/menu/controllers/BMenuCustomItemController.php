<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class BMenuCustomItemController extends CController
{
  /**
   * Отображать ли контроллер как пункт меню
   *
   * @var bool
   */
  public $enabled = false;

  public $name = 'MenuCustomItem';

  /**
   * @return array
   */
  public function filters()
  {
    return array(
      'ajaxOnly + getData',
      'ajaxOnly + save',
    );
  }

  /**
   * Получение данных для конкретной записи
   */
  public function actionGetData()
  {
    $id    = Yii::app()->request->getPost('id');
    $model = BFrontendCustomMenuItem::model()->findByPk($id);

    if( empty($model) )
      echo CJSON::encode(array('error' => 'Запись не найдена'));
    else
    {
      echo CJSON::encode(array('model' => $model, 'data' => $model->data));
    }
  }

  /**
   * Сохранение параметров для записи
   *
   * Пример входящих данных
   * @example
   * $data = array(
   *  'name'    => '',
   *  'url'     => '',
   *  'menu_id' => '',
   *
   *  'data' => array(
   *    array(
   *      'name'  => '',
   *      'value' => '',
   *    )
   *  ),
   * );
   */
  public function actionSave()
  {
    $data = Yii::app()->request->getPost('BFrontendCustomMenuItem');

    if( empty($data['id']) )
      $customMenuItem = new BFrontendCustomMenuItem();
    else
      $customMenuItem = BFrontendCustomMenuItem::model()->findByPk($data['id']);

    if( !empty($customMenuItem) )
    {
      $customMenuItem->name = $data['name'];
      $customMenuItem->url  = $data['url'];
      $customMenuItem->save();

      if( !empty($data['data']) )
      {
        $customMenuItem->clearData();
        $customMenuItem->appendData($data['data']);
      }

      $menu = BFrontendMenu::model()->findByPk($data['menu_id']);

      if( !$menu->hasCustomMenuItem($customMenuItem) )
      {
        $menuItem          = new BFrontendMenuItem();
        $menuItem->menu_id = $menu->id;
        $menuItem->setModel($customMenuItem);
        $menuItem->save();
      }

      echo CJSON::encode(array('model' => $customMenuItem));
    }
    else
    {
      echo CJSON::encode(array('error' => 'Не возможно загрузить запись'));
    }
  }
}