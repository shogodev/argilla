<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac.models
 *
 * @method static BRbacTask model(string $class = __CLASS__)
 */
class BRbacTask extends BAuthItem
{
  private static $tasks;

  /**
   * @param string $task
   *
   * @return bool
   */
  public static function taskExists($task)
  {
    $tasks = self::getTasks();

    return isset($tasks[$task]);
  }

  /**
   * Получение массива вида name=>title объектов задач
   *
   * @return array
   */
  public static function getTasks()
  {
    if( !is_null(self::$tasks) )
      return self::$tasks;

    self::$tasks = array();

    $tasks = self::model()->findAll();
    foreach($tasks as $task)
    {
      self::$tasks[$task->name] = !empty($task->title) ? $task->title : $task->name;
    }

    return self::$tasks;
  }

  public static function checkTask($task, $userId)
  {
    $assignments = AccessHelper::getAssignments($userId);
    $childList = AccessHelper::getChildList();

    foreach($assignments as $name => $assignment)
    {
      if( !isset($childList[$name]) )
        continue;

      if( isset($childList[$name][$task]) )
        return true;

    }
    return false;
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    return [
      'condition' => 'type = '.CAuthItem::TYPE_TASK,
    ];
  }

  /**
   * Получение всех операции для задачи
   *
   * @return array
   */
  public function getOperations()
  {
    $children = Yii::app()->authManager->getItemChildren($this->name);

    $data = array();
    foreach( $children as $child )
    {
      $operation = BRbacOperation::model()->findByAttributes(array('name' => $child->name));

      $data[$operation->name] = $operation->name;
    }

    return $data;
  }

  /**
   * Добавление всех новых операции
   *
   * @param array $operations
   */
  public function setOperations(array $operations)
  {
    $this->clearOperations();

    if( empty($operations) )
      return;

    foreach( $operations as $operation )
    {
      Yii::app()->authManager->addItemChild($this->name, $operation);
    }
  }

  /**
   * Удаление всех операций из задачи
   */
  public function clearOperations()
  {
    $auth = Yii::app()->authManager;

    foreach( $this->operations as $id => $operation )
    {
      $auth->removeItemChild($this->name, $id);
    }
  }

  public function rules()
  {
    return CMap::mergeArray(parent::rules(), array(array('operations', 'safe')));
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array('operations' => 'Действия'));
  }

  protected function beforeSave()
  {
    $this->type = CAuthItem::TYPE_TASK;
    return parent::beforeSave();
  }
}