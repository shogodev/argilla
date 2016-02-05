<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */

/**
 * Class ProductController
 * @mixin ProductTextBehavior
 * @mixin ProductFilterBehavior
 * @mixin ProductSortingBehavior
 */
class ProductController extends FController
{
  public $pageSize = 10;

  public function behaviors()
  {
    return CMap::mergeArray(parent::behaviors(), array(
      'productTextBehavior' => array('class' => 'backend.modules.product.modules.text.frontend.ProductTextBehavior'),
      'productFilterBehavior' => array('class' => 'ProductFilterBehavior'),
      'productSortingBehavior' => array(
        'class' => 'ProductSortingBehavior',
        'defaultSorting' => 'default',
        'pageSizeRange' => array(20, 40, 60)
      )
    ));
  }

  public function actionCategories()
  {
    $this->breadcrumbs = array('Производители');

    $categories = ProductCategory::model()->findAll();

    $this->render('categories', array('categories' => $categories));
  }

  public function actionCategory($category)
  {
    /**
     * @var ProductCategory $model
     */
    $model = ProductCategory::model()->findByAttributes(array('url' => $category));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      'Производители' => $this->createUrl('product/categories'),
      $model->name,
    );

    $criteria = new CDbCriteria();
    $criteria->compare('a.category_id', $model->id);

    $this->renderPage(array($model), $criteria);
  }

  public function actionSection($section)
  {
    /**
     * @var ProductSection $model
     */
    $model = ProductSection::model()->findByAttributes(array('url' => $section));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->name,
    );

    $criteria = new CDbCriteria();
    $criteria->compare('a.section_id', $model->id);

    $this->renderPage(array($model), $criteria);
  }

  public function actionType($type)
  {
    /**
     * @var ProductType $model
     */
    $model = ProductType::model()->findByAttributes(array('url' => $type));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->name,
    );

    $criteria = new CDbCriteria();
    $criteria->compare('a.type_id', $model->id);

    $this->renderPage(array($model), $criteria);
  }

  public function actionOne($url)
  {
    /**
     * @var Product $model
     */
    $model = Product::model()->visible()->findByAttributes(array('url' => $url));

    if( !$model )
      throw new CHttpException(404, 'Страница не найдена');

    $this->breadcrumbs = array(
      $model->section->name => array('product/section', 'section' => $model->section->url),
      $model->name,
    );

    $data = array(
      'model' => $model,
      'similarDataProvider' => $model->getSimilarProducts(4),
      'relatedDataProvider' => $model->getRelatedProducts(4),
    );

    if( Yii::app()->request->isAjaxRequest )
      $this->renderPartial('_details', array('data' => $model));
    else
      $this->render('one/product', $data);
  }

  public function isFirstPage()
  {
    $page = Yii::app()->request->getParam('page');
    return $page === null || $page == 1;
  }

  private function renderPage(array $models, CDbCriteria $criteria)
  {
    Yii::app()->meta->addModels($models);

    $productList = new ProductList($criteria, $this->getSorting(), true, $this->getFilter());
    $dataProvider = $productList->getDataProvider();

    $this->getFilter()->setSelectedModels($models);

    $data = array(
      'model' => Arr::reset($models),
      'dataProvider' => $dataProvider,
      'filter' => $this->getFilter(),
      'menus' => array()
    );

    if( Yii::app()->request->isAjaxRequest )
    {
      $this->renderPartial('content', $data);
    }
    else
    {
      $this->render('content', $data);
    }
  }
}