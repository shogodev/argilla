<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class ProductController extends FController
{
  public $sorting = 'position_up';

  public $pageSize = 10;

  /**
   * @var ProductFilter
   */
  private $filter;

  public function beforeAction($action)
  {
    return parent::beforeAction($action);
  }

  public function init()
  {
    parent::init();

    $params = Yii::app()->session->get($this->id);
    $this->sorting = Arr::get($params, 'sorting', $this->sorting);
    $this->pageSize = $this->getSettings('product_page_size', $this->pageSize);
  }

  public function actionSection($section)
  {
    $model = ProductSection::model()->findByAttributes(array('url' => $section));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->name,
    );

    $criteria = new CDbCriteria();
    $criteria->compare('a.section_id', $model->id);

    $productList = new ProductList($criteria, $this->sorting, true, $this->filter);
    $dataProvider = $productList->getProducts();

    $data = array(
      'model' => $model,
      'dataProvider' => $dataProvider,
    );

    if( Yii::app()->request->isAjaxRequest )
      $this->renderPartial('content', $data);
    else
      $this->render('content', $data);
  }

  public function actionOne($url)
  {
    /**
     * @var Product $model
     */
    $model = Product::model()->findByAttributes(array('url' => $url));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->section->name => array('product/section', 'section' => $model->section->url),
      $model->name,
    );

    $data = array(
      'model' => $model,
    );

    if( Yii::app()->request->isAjaxRequest )
      $this->renderPartial('one/product', $data);
    else
      $this->render('one/product', $data);
  }
}