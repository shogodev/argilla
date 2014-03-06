<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
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