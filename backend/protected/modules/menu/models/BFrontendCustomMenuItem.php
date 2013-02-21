<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $visible
 *
 * @property CustomMenuItemData[] $data
 *
 * @method static BFrontendCustomMenuItem model(string $class = __CLASS__)
 */
class BFrontendCustomMenuItem extends BAbstractMenuEntry
{
  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu_custom_item}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name, url', 'required'),
      array('name, url', 'safe'),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'data' => array(self::HAS_MANY, 'BFrontendCustomMenuItemData', 'parent'),
    );
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @return string
   */
  public function getFrontendModelName()
  {
    return 'CustomMenuItem';
  }

  /**
   * Добавление параметров
   *
   * @param array $data
   *
   * @return BFrontendCustomMenuItem
   */
  public function appendData(array $data)
  {
    foreach( $data as $item )
    {
      if( empty($item['name']) || empty($item['value']) )
        continue;

      $menuItemData         = new BFrontendCustomMenuItemData();
      $menuItemData->parent = $this->id;
      $menuItemData->name   = $item['name'];
      $menuItemData->value  = $item['value'];
      $menuItemData->save();
    }

    return $this;
  }

  /**
   * Удаление предыдущих данных для записи
   *
   * @return BFrontendCustomMenuItem
   */
  public function clearData()
  {
    foreach( $this->data as $item )
    {
      $item->delete();
    }

    return $this;
  }
}