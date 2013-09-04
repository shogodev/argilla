<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controlers
 */
class FavoriteController extends FController
{
  public function init()
  {
    $this->processFavoriteAction();
  }

  public function actionIndex()
  {
    $this->renderPartial('/product_panel');
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
          $this->favorite->remove(Arr::get($data, 'id'));
          break;

        case 'add':
          if( !$this->favorite->isInCollectionData(Arr::get($data, 'type'), Arr::get($data, 'id')) )
            $this->favorite->add($data);
        break;
      }
    }
  }
}