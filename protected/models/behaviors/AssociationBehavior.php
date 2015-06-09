<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * 
 * public function behaviors()
 * {
 *   return array(
 *     'associationBehavior' => array('class' => 'AssociationBehavior'),
 *   );
 * }
 */

/**
 * Class AssociationBehavior
 *
 * @property FActiveRecord $owner
 */
class AssociationBehavior extends SActiveRecordBehavior
{
  /**
   * Возвращает привязки к текушей модели (destination)
   * @param string $modelName
   * @return Association
   */
  public function getAssociationForMe($modelName = '')
  {
    $model = new Association();
    return $model->setSource($this->owner, $modelName);
  }

  /**
   * Возвращает привязки в которых найдена текущая модели (source)
   * @param string $modelName
   * @return Association
   */
  public function getAssociationWithMe($modelName = '')
  {
    $model = new Association();
    return $model->setDestination($this->owner, $modelName);
  }
}