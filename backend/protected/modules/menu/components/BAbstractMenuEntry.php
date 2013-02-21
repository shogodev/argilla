<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */

Yii::import('backend.modules.menu.models.*');

abstract class BAbstractMenuEntry extends BActiveRecord implements IBFrontendMenuEntry
{
  /**
   * Удаление всех записей меню, которые связаны с текущей моделью
   */
  protected function afterDelete()
  {
    parent::afterDelete();

    $criteria = new CDbCriteria();
    $criteria->compare('item_id', $this->getId());
    $criteria->compare('type', get_class($this));
    $criteria->compare('frontend_model', $this->getFrontendModelName());

    BFrontendMenuItem::model()->deleteAll($criteria);
  }
}