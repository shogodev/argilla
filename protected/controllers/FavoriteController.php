<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class FavoriteController extends FController
{
  public function init()
  {
    $this->processFavoriteAction();
  }

  public function actionIndex()
  {
    $this->forward('basket/add');
  }

  public function actionMergeWithBasket()
  {
    foreach($this->favorite as $item)
    {
      $this->basket->add($item);
    }
    $this->favorite->clear();

    $this->forward('basket/add');
  }

  protected function processFavoriteAction()
  {
    $request = Yii::app()->request;

    if( !$request->isAjaxRequest )
      return;

    $data = $request->getPost($this->favorite->keyCollection);
    $action = $request->getPost('action');

    if( $data && $action )
    {
      switch($action)
      {
        case 'remove':
          $index = Arr::get($data, 'index');
          if( !$index )
            $index = $this->favorite->getIndex($data);
          $this->favorite->remove($index);
        break;

        case 'add':
          if( !$this->favorite->isInCollection($data) )
          {
            $this->favorite->add($data);
          }
        break;
      }
    }
  }
}