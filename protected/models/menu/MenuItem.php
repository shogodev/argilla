<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @property int $id
 * @property int $menu_id
 * @property int $item_id
 * @property string $type
 * @property string $frontend_model
 * @property int $position
 */
class MenuItem extends FActiveRecord implements IMenuItem
{
  /**
   * @var IMenuItem
   */
  protected $model;

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu_item}}';
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    return array(
      'order' => 'position',
    );
  }

  /**
   * Загрузка модели
   *
   * @return IMenuItem
   */
  public function loadModel()
  {
    if( empty($model) )
    {
      /**
       * @var FActiveRecord $class
       */
      $class       = $this->frontend_model;
      $this->model = $class::model()->findByPk($this->item_id);
    }

    return $this->getModel();
  }

  /**
   * @return IMenuItem
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   * Получение массива для формирования url к элементу
   *
   * @return array
   */
  public function getMenuLink()
  {
    return $this->model->getMenuLink();
  }

  public function afterFind()
  {
    $this->loadModel();
    parent::afterFind();
  }

  /**
   * @return array
   */
  public function getChildren()
  {
    return $this->model->getChildren();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->model->getName();
  }

  /**
   * @param int $d
   *
   * @return IMenuItem
   */
  public function setDepth($d)
  {
    return $this->model();
  }
}