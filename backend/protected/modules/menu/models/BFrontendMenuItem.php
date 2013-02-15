<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 *
 * @property integer $id
 * @property integer $menu_id
 * @property integer $item_id
 * @property string $type
 * @property string $frontend_model
 * @property integer $position
 *
 * @method static BFrontendMenuItem model(string $class = __CLASS__)
 */
class BFrontendMenuItem extends BActiveRecord
{

  /**
   * @var IFrontendMenuEntry
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
   * Является ли модель записи CustomMenuItem
   *
   * @return boolean
   */
  public function getIsCustom()
  {
    return $this->getModel() instanceof BFrontendCustomMenuItem;
  }

  /**
   * Добавление конкретной модели
   *
   * @param IBFrontendMenuEntry $model
   *
   * @return BFrontendMenuItem
   */
  public function setModel(IBFrontendMenuEntry $model)
  {
    if( empty($this->model) )
      $this->model = $model;

    return $this;
  }

  /**
   * Получение имени текущей модели
   *
   * @return string
   */
  public function getModelClass()
  {
    return get_class($this->model);
  }

  /**
   * Загружает модель для текущей записи
   *
   * @return BFrontendMenuItem
   */
  public function loadModel()
  {
    if( !empty($this->type) && class_exists($this->type) )
    {
      $className   = $this->type;
      $this->setModel($className::model()->findByPk($this->item_id));
    }

    return $this;
  }

  /**
   * @return IFrontendMenuEntry
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('menu_id, item_id, type', 'safe')
    );
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.position'
    );
  }

  /**
   * @return bool
   * @throws BFrontendMenuException
   */
  protected function beforeSave()
  {
    if( empty($this->type) || empty($this->item_id) )
    {
      if( !empty($this->model) )
      {
        $this->type    = $this->getModelClass();
        $this->item_id = $this->getModel()->getId();
      }
      else
        throw new BFrontendMenuException('Невозможно сохранить запись', BFrontendMenuException::EMPTY_PROPERTIES);
    }

    if( empty($this->menu_id) )
      throw new BFrontendMenuException('Невозможно сохранить запись', BFrontendMenuException::EMPTY_PARENT);

    return parent::beforeSave();
  }

  protected function afterFind()
  {
    $this->loadModel();
    parent::afterFind();
  }
}