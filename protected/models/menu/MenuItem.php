<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @method static MenuItem model(string $class = __CLASS__)
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
   * @var integer
   */
  protected $depth;

  /**
   * @var IMenuItem
   */
  protected $model;

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
   * @throws CHttpException
   * @return IMenuItem
   */
  public function getModel()
  {
    if( !isset($this->model) )
      throw new CHttpException(500, "Menu item with id=".$this->id." doesn't have a model");

    return $this->model;
  }

  /**
   * @param IMenuItem $model
   */
  public function setModel(IMenuItem $model)
  {
    $this->model = $model;
  }

  /**
   * @return array
   */
  public function getMenuUrl()
  {
    return $this->getModel()->getMenuUrl();
  }

  /**
   * @return array
   */
  public function getChildren()
  {
    return $this->getModel()->getChildren();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->getModel()->getName();
  }

  /**
   * @param int $depth
   *
   * @return IMenuItem
   */
  public function setDepth($depth = null)
  {
    if( isset($depth) )
    {
      $this->depth = $depth;
    }
  }
}