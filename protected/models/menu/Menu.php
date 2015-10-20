<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 *
 * @method static Menu model(string $class = __CLASS__)
 *
 * @property int $id
 * @property string $name
 * @property string $sysname
 * @property string $url
 * @property int $visible
 *
 * @property MenuItem[] $items
 */
class Menu extends FActiveRecord implements IMenuItem
{
  protected $depth;

  /**
   * @var FActiveRecord[]|IMenuItem[]
   */
  protected $models;

  public function defaultScope()
  {
    return array(
      'with' => 'items'
    );
  }

  public function relations()
  {
    return array(
      'items' => array(self::HAS_MANY, 'MenuItem', 'menu_id', 'order' => 'items.position, items.id'),
    );
  }

  /**
   * Получение массива элементов меню по системному имени
   *
   * @param string $sysname
   * @param integer $depth
   *
   * @return array
   */
  public function getMenu($sysname, $depth = null)
  {
    $data = array();

    /**
     * @var Menu $menu
     */

    $menu = $this->findByAttributes(array('sysname' => $sysname));

    if( $menu )
    {
      $menu->setDepth($depth);
      $data = $menu->build();
    }

    return $data;
  }

  /**
   * @return array
   */
  public function build()
  {
    $data = array();
    $this->loadModels();

    foreach($this->items as $item)
    {
      if( $this->depth === 0 )
        continue;

      $item->setDepth($this->depth - 1);
      $data[] = $this->buildItem($item);
    }

    return $data;
  }

  /**
   * @param int $depth
   */
  public function setDepth($depth = null)
  {
    if( isset($depth) )
    {
      $this->depth = $depth;
    }
  }

  /**
   * @return array
   */
  public function getMenuUrl()
  {
    if( empty($this->url) )
      return '';

    if( substr($this->url, 0, 1) === '/' && substr($this->url, -1, 1) === '/' )
      return $this->url;
    else
      return array($this->url);
  }

  /**
   * @return array
   */
  public function getChildren()
  {
    return $this->build();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  protected function buildItem(MenuItem $item)
  {
    $model = preg_replace("/([A-Z][a-z]+)\w*/", "$1", $item->frontend_model);

    return array(
      'label' => $item->getName(),
      'url'   => $item->getMenuUrl(),
      'items' => $item->getChildren(),
      'itemOptions' => array(
        'class' => 'icn-menu-'.strtolower($model).$item->item_id
      ),
    );
  }

  protected function loadModels()
  {
    if( $this->models === null )
    {
      $this->models = array();
      $loadedModels = CHtml::listData($this->items, 'item_id', 'item_id', 'frontend_model');

      foreach($loadedModels as $modelClass => $modelPk)
      {
        /**
         * @var FActiveRecord $model
         */
        $model = $modelClass::model();
        $this->models = CMap::mergeArray($this->models, $model->findAllByPk($modelPk));
      }

      $this->setModels();
    }
  }

  protected function setModels()
  {
    foreach($this->models as $model)
    {
      foreach($this->items as $menuItem)
      {
        if( $menuItem->item_id === $model->getPrimaryKey() && $menuItem->frontend_model === get_class($model) )
        {
          $menuItem->setModel($model);
          $model->setDepth(isset($this->depth) ? $this->depth - 1 : null);
          break;
        }
      }
    }
  }
}