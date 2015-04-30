<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class MenuBuilder
{
  /**
   * @var ProductAssignment
   */
  private $assignment;

  /**
   * @var string
   */
  private $cachePrefix = 'menuBuilder';

  /**
   * @var integer
   */
  private $cacheExpire = 300;

  private $imagePath = '/f/product/';

  public function __construct()
  {
    $this->assignment = ProductAssignment::model();
  }

  /**
   * @param $sysname
   *
   * @return array
   */
  public function getMenu($sysname)
  {
    $key = __METHOD__.$sysname;
    if( $this->cacheExists($key) ) return $this->getCache($key);
    $menu = Menu::model()->getMenu($sysname);
    $this->setCache($key, $menu);

    return $menu;
  }

  /**
   * @return array
   */
  public function getSectionMenu()
  {
    $key = $this->getKey(__METHOD__, func_get_args());
    if( $this->cacheExists($key) ) return $this->getCache($key);

    $menu = array();
    $assignments = $this->assignment->getAssignments();

    foreach($assignments as $item)
    {
      if( !empty($item['section_id']) && !isset($menu[$item['section_id']]) )
        $menu[$item['section_id']] = $this->buildSectionItem($item);
    }

    $this->sortRecursive($menu);
    $this->setCache($key, $menu);

    return $menu;
  }

  /**
   * @return array
   */
  public function getSectionTypeMenu()
  {
    $key = $this->getKey(__METHOD__, func_get_args());
    if( $this->cacheExists($key) ) return $this->getCache($key);

    $menu = array();
    $assignments = $this->assignment->getAssignments();

    foreach($assignments as $item)
    {
      if( !empty($item['section_id']) && !isset($menu[$item['section_id']]) )
      {
        $menu[$item['section_id']] = $this->buildSectionItem($item);
      }

      if( !empty($item['type_id']) && !isset($menu[$item['section_id']]['items'][$item['type_id']]) )
      {
        $menu[$item['section_id']]['items'][$item['type_id']] = $this->buildTypeItem($item);
      }
    }

    $this->sortRecursive($menu);
    $this->setCache($key, $menu);

    return $menu;
  }

  private function buildSectionItem($item)
  {
    return array(
      'label' => $item['section_name'],
      'url' => array('product/section', 'section' => $item['section_url']),
      'items' => array(),
    );
  }

  private function buildTypeItem($item)
  {
    return array(
      'label' => $item['type_name'],
      'url' => array('product/type', 'type' => $item['type_url']),
    );
  }

  private function buildImage($image, $default = '/i/sp.gif')
  {
    return !empty($image) ? $this->imagePath.$image : $default;
  }

  private function sortRecursive(&$menu)
  {
    foreach($menu as $key => $element)
    {
      if( !empty($menu[$key]['items']) )
        $this->sortRecursive($menu[$key]['items']);
    }

    uasort($menu, function($a, $b) {
      if( isset($a['position']) )
      {
        if( $a['position'] == 0 )
          $a['position'] = 999999;

        if( $b['position'] == 0 )
          $b['position'] = 999999;

        if( $a['position'] > $b['position'] )
          return 1;
        else if( $a['position'] < $b['position'] )
          return -1;
        else
          return strcmp($a['label'], $b['label']);
      }

      return strcmp($a['label'], $b['label']);
    });
  }

  private function getKey($method, $args)
  {
    return $method.crc32(serialize($args));
  }

  /**
   * @param string $key
   *
   * @return bool
   */
  private function cacheExists($key)
  {
    return Yii::app()->cache->offsetExists($this->cachePrefix.$key);
  }

  /**
   * @param $key
   *
   * @return mixed
   */
  private function getCache($key)
  {
    return Yii::app()->cache->offsetGet($this->cachePrefix.$key);
  }

  /**
   * @param string $key
   * @param mixed $data
   */
  private function setCache($key, $data)
  {
    Yii::app()->cache->set($this->cachePrefix.$key, $data, $this->cacheExpire);
  }
}