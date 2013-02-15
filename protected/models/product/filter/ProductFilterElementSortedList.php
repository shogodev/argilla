<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.01.13
 */
class ProductFilterElementSortedList extends ProductFilterElementList
{
  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    uasort($items, function($a, $b){
      if( strpos($a->label, "<") !== false )
        return -1;
      if( strpos($b->label, "<") !== false )
        return 1;

      return strnatcmp($a->label, $b->label);
    });

    return $items;
  }
}