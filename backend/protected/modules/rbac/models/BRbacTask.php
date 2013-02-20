<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @method static BRbacTask model(string $class = __CLASS__)
 */
class BRbacTask extends BAuthItem
{
  /**
   * @param string $task
   *
   * @return bool
   */
  public static function taskExists($task)
  {
    return self::model()->findByPk($task) !== null;
  }

  /**
   * Получение массива вида name=>title объектов задач
   *
   * @return array
   */
  public static function getTasks()
  {
    $data = array();
    $tasks = self::model()->findAll();

    foreach( $tasks as $task )
    {
      $data[$task->name] = $task->title;
    }

    return $data;
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
      $operation = BRbacOperation::model()->findByPk($child->name);

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