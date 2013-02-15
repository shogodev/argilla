<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 26.09.12
 */
Yii::import('zii.widgets.CBreadcrumbs');

class FBreadcrumbs extends CBreadcrumbs
{
  public $separator = ' / ';

  public $homeLink;

  public function init()
  {
    $this->homeLink = CHtml::link('Главная', Yii::app()->homeUrl);
  }
}