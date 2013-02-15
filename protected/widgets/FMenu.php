<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 03.10.12
 */

Yii::import('zii.widgets.CMenu');

class FMenu extends CMenu
{
  public $route;

  public $onlyActiveItems = false;

  public function init()
  {
    $this->htmlOptions['id'] = $this->getId();
    $route                   = !empty($this->controller->activeUrl) ? $this->controller->activeUrl : $this->controller->route;
    $this->items             = $this->normalizeItems($this->items, $route, $hasActiveChild);
  }

  protected function isItemActive($item, $route)
  {
    if( is_array($route) )
    {
      $params = array_slice($route, 1);
      $route  = $route[0];
    }
    else
      $params = $_GET;

    if( isset($item['url']) && is_array($item['url']) && !strcasecmp(trim($item['url'][0], '/'), $route) )
    {
      if( isset($item['url']['#']) )
        unset($item['url']['#']);
      if( count($item['url']) > 1 )
      {
        $url = $item['url'];

        foreach(array_splice($url, 1) as $name => $value)
        {
          if( !isset($params[$name]) || $params[$name] != $value )
            return false;
        }
      }
      return true;
    }

    return false;
  }

  protected function renderMenuRecursive($items)
  {
    if( $this->onlyActiveItems )
    {
      foreach($items as $i => $item)
        if( !$item['active'] )
          $items[$i]['items'] = array();
    }

    parent::renderMenuRecursive($items);
  }
}