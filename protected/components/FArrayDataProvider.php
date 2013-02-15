<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FArrayDataProvider extends CArrayDataProvider
{
  /**
   * @param array $rawData
   * @param array $config
   */
  public function __construct($rawData, $config = array())
  {
    if( !isset($config['pagination']) )
      $config['pagination'] = new FPagination();

    $controller = Yii::app()->controller;
    if( isset($controller->pageSize) )
      $config['pagination']->pageSize = $controller->pageSize;

    parent::__construct($rawData, $config);
  }
}