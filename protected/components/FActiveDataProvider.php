<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class FActiveDataProvider extends CActiveDataProvider
{
  /**
   * @param mixed $modelClass
   * @param array $config
   */
  public function __construct($modelClass, $config = array())
  {
    if( !isset($config['pagination']) )
      $config['pagination'] = new FPagination();

    $controller = Yii::app()->controller;
    if( isset($controller->pageSize) && $config['pagination'] )
      $config['pagination']->pageSize = $controller->pageSize;

    parent::__construct($modelClass, $config);
  }

  public function fetchData()
  {
    $data = parent::fetchData();
    $this->setData($data);
    $this->afterFetchData();
    return $data;
  }

  public function afterFetchData()
  {
    if( $this->hasEventHandler('onAfterFetchData') )
      $this->onAfterFetchData(new CEvent($this));
  }

  public function onAfterFetchData($event)
  {
    $this->raiseEvent('onAfterFetchData', $event);
  }
}