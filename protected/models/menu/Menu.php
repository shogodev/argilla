<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
  protected $depth = 1;

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
      'items' => array(self::HAS_MANY, 'MenuItem', 'menu_id'),
    );
  }

  /**
   * Получение массива элементов меню по системному имени
   *
   * @param string $sysname
   *
   * @return array
   */
  public function getMenu($sysname)
  {
    /**
     * @var Menu $menu
     */
    $menu = $this->findByAttributes(array('sysname' => $sysname));
    return $menu ? $menu->build() : array();
  }

  /**
   * @param int $depth
   */
  public function setDepth($depth)
  {
    $this->depth = $depth;
  }

  /**
   * @return array
   */
  public function getMenuUrl()
  {
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

  protected function afterFind()
  {
    parent::afterFind();
    $this->loadModels();
  }

  /**
   * @return array
   */
  protected function build()
  {
    $data = array();

    if( $this->depth > 0 )
    {
      foreach($this->items as $item)
      {
        $item->setDepth(--$this->depth);
        $data[] = $this->buildItem($item);
      }
    }

    return $data;
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
        $this->models = CMap::mergeArray($this->models, $model->findAllByPk(Arr::reset($modelPk)));
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
          break;
        }
      }
    }
  }
}