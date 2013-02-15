<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
 */
class ProductController extends FController
{
  public $pageSize = 9;

  public $selectionPageSize = 12;

  public $sizeRange;


  public function beforeAction($action)
  {
    return parent::beforeAction($action);
  }

  public function init()
  {
    parent::init();

    $params          = Yii::app()->session->get($this->id);
    $this->pageSize  = Arr::get($params, 'pageSize', $this->pageSize);
    $this->sizeRange = Arr::reflect(array(9, 18, 27, 36, 45));
  }

  public function actionSections()
  {
    $this->breadcrumbs = array('Продукты');

    $criteria = new CDbCriteria();

    $productList = new ProductList($criteria);

    $this->render('products_content', array(
      'productList'  => $productList,
      'sectionsMenu' => ProductType::model()->getMenu()
    ));
  }

  public function actionType($type)
  {
    $typeModel = ProductType::model()->findByAttributes(array('url' => $type));

    if( !$typeModel )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      'Продукты' => array('product/sections'),
      $typeModel->name
    );

    $criteria = new CDbCriteria();
    $criteria->compare('a.type_id', '='.$typeModel->id);

    $productList = new ProductList($criteria);

    $this->render('products_content', array(
      'typeModel' => $typeModel,
      'productList' => $productList,
      'sectionsMenu' => ProductType::model()->getMenu()
    ));
  }

  public function actionOne($url)
  {
    $model = Product::model()->visible()->findByAttributes(array('url' => $url));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->activeUrl = array(
      'product/type',
      'type' => $model->type->url
    );

    $this->breadcrumbs = array(
      'Продукты' => array('product/sections'),
      $model->type->name => array('product/type', 'type' => $model->type->url),
      $model->name,
    );

    $criteria = new CDbCriteria();
    $criteria->compare('visible', '=1');
    $criteria->compare('product_id', '='.$model->id);

    $this->render('product', array(
      'model' => $model,
      'sectionsMenu' => ProductType::model()->getMenu()
    ));
  }
}