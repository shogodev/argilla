<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BFrontendMenuGridAdapter extends CModel
{
  /**
   * @var bool
   */
  public $active = false;

  /**
   * @var bool
   */
  public $isCustom = false;

  /**
   * @var BAbstractMenuEntry
   */
  public $model;

  /**
   * @var int
   */
  public $position;

  /**
   * @param BFrontendMenuItem $i
   *
   * @return BAbstractMenuEntry
   */
  public static function convertFromMenuEntry(BFrontendMenuItem $i)
  {
    $class = $i->type;
    $id = $i->item_id;

    return $class::model()->findByPk($id);
  }

  /**
   * @param BAbstractMenuEntry $model
   * @param bool $active
   * @param null $position
   */
  public function __construct(BAbstractMenuEntry $model, $active = false, $position = null)
  {
    $this->isCustom = $model instanceof BFrontendCustomMenuItem;
    $this->model = $model;
    $this->active = $active;
    $this->position = $position;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->model->getName();
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->model->getUrl();
  }

  /**
   * @return int|string
   */
  public function getId()
  {
    return $this->model->getId();
  }

  /**
   * @return int|string
   */
  public function getPrimaryKey()
  {
    return $this->getId();
  }

  /**
   * @return string
   */
  public function getType()
  {
    return get_class($this->model);
  }

  /**
   * Returns the list of attribute names of the model.
   * @return array list of attribute names.
   */
  public function attributeNames()
  {
    return array(
      'name' => 'Название',
      'position' => 'Позиция',
      'url' => 'Url',
      'type' => 'Тип',
      'active' => 'Вид',
    );
  }
}