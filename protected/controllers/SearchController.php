<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
 */
class SearchController extends FController
{
  public function actionIndex()
  {
    $model = new Search();

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array('Результаты поиска');

    $pages = new FPagination($model->itemsCount);
    $pages->pageSize = $model->pageSize;
    $pages->setCurrentPage($model->page);

    $this->render('search', array(
      'model' => $model,
      'pages' => $pages,
    ));
  }
}