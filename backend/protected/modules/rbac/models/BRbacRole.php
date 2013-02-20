<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @method static BRbacRole model(string $class = __CLASS__)
 */
class BRbacRole extends BAuthItem
{
  /**
   * Получение всех доступных ролей
   *
   * @return array
   */
  public static function getRoles()
  {
    $data = array();
    $roles = Yii::app()->authManager->getRoles();

    foreach( $roles as $role )
    {
      $item = BRbacRole::model()->findByPk($role->name);

      $data[$item->name] = $item->title;
    }

    return $data;
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    return array(
      'condition' => 'type='.CAuthItem::TYPE_ROLE,
    );
  }

  /**
   * @return array
   */
  public function getTasks()
  {
    $children = Yii::app()->authManager->getItemChildren($this->name);

    $data = array();
    foreach( $children as $child )
    {
      $task = BRbacTask::model()->findByPk($child->name);

      $data[$task->name] = $task->name;
    }

    return $data;
  }

  /**
   * Добавление задач к текущей роли
   *
   * @param array $tasks
   */
  public function setTasks(array $tasks)
  {
    $this->clearTasks();

    if( empty($tasks) )
      return;

    foreach( $tasks as $task )
    {
      Yii::app()->authManager->addItemChild($this->name, $task);
    }
  }

  /**
   * Удаление всех элементов заданий для роли
   */
  public function clearTasks()
  {
    $auth = Yii::app()->authManager;

    foreach( $this->tasks as $id => $task )
    {
      $auth->removeItemChild($this->name, $id);
    }
  }

  /**
   * @return array
   */
  public function rules()
  {
    return CMap::mergeArray(parent::rules(), array(array('tasks', 'safe')));
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array('tasks' => 'Задачи'));
  }

  /**
   * @return bool
   */
  protected function beforeSave()
  {
    $this->type = CAuthItem::TYPE_ROLE;
    return parent::beforeSave();
  }
}