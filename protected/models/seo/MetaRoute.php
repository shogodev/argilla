<?php

class MetaRoute extends FActiveRecord
{
  public function tableName()
  {
    return '{{meta_route}}';
  }

  public function getData($route)
  {
    return $this->find('route=:route AND visible=:visible', array(':route' => $route, ':visible' => '1'));
  }
}
?>