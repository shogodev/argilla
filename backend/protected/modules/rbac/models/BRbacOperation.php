<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @method static BRbacOperation model(string $class = __CLASS__)
 */
class BRbacOperation extends BAuthItem
{
  /**
   * @param string $operation
   *
   * @return bool
   */
  public static function operationExists($operation)
  {
    return self::model()->findByPk($operation) !== null;
  }

  /**
   * @return array
   */
  public static function getOperations()
  {
    /**
     * @var BRbacOperation[] $operations
     */
    $operations = Yii::app()->authManager->getOperations();

    $data = array();

    foreach( $operations as $operation )
    {
      $item = self::model()->findByPk($operation->name);
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
      'condition' => 'type='.CAuthItem::TYPE_OPERATION,
    );
  }

  /**
   * @return bool
   */
  protected function beforeSave()
  {
    $this->type = CAuthItem::TYPE_OPERATION;
    return parent::beforeSave();
  }
}