<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SActiveDataProvider extends CActiveDataProvider
{
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