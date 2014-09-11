<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
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

  public function actionPredictiveSearch()
  {
    $query = Yii::app()->request->getPost('query');

    if( Yii::app()->request->isAjaxRequest && $query )
    {
      $criteria = new CDbCriteria();
      $criteria->select = 'name';
      $criteria->addSearchCondition('name', $query);
      $criteria->limit = 10;

      $command = Yii::app()->db->getCommandBuilder()->createFindCommand(Product::model()->tableName(), $criteria);

      $data = array();
      foreach($command->queryAll() as $value)
        $data[] = $value['name'];

      echo CJSON::encode($data);
    }
  }
}