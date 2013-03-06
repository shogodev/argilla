<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
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