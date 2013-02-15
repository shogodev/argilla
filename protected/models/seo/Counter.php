<?php
class Counter extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_counters}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  public function __toString()
  {
    return $this->code;
  }
}