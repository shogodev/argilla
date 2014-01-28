<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controlers
 */
class CompareController extends FController
{
  public function init()
  {
    parent::init();

    $this->processCompareAction();
  }

  public function actionIndex()
  {
    $selectedSection = $this->getSelectedSection();

    $data = array();
    if( $selectedSection )
    {
      $productList = $this->compare->getProductListByGroup($selectedSection->id);
      $parametersCompare = $this->getParametersCompare($productList);

      $data = array(
        'selectedSection' => $selectedSection,
        'parametersCompare' => $parametersCompare,
        'products' => $productList->getDataProvider()->getData(),
      );
    }

    $this->breadcrumbs = array('Сравнение');

    if( Yii::app()->request->isAjaxRequest )
    {
      $this->renderPartial('/_compare_basket_header');
      $this->renderPartial('compare', $data);
    }
    else
      $this->render('compare', $data);
  }

  public function actionAdd()
  {
    $request = Yii::app()->request;
    $data = $request->getPost($this->compare->keyCollection);

    if( !$request->isAjaxRequest && $request->getPost('action') != 'add'  )
      return;

    /**
     * @var Product $product
     */
    $product = Product::model()->findByPk(Arr::get($data, 'id'));

    if( !$product )
      throw new CHttpException(500, 'Ошибка продукт не найден.');

    if( !$this->compare->isInCollectionClass($product) )
      $this->compare->add($data);

    $this->renderPartial('/_compare_basket_header');

    Yii::app()->end();
  }

  protected function processCompareAction()
  {
    $request = Yii::app()->request;

    if( !$request->isAjaxRequest )
      return;

    $data = $request->getPost($this->compare->keyCollection);
    $action = $request->getPost('action');

    if( $data && $action )
    {
      switch($action)
      {
        case 'remove':
          $id = Arr::get($data, 'id');

          if (!$this->compare->getElementByIndex($id))
            throw new CHttpException(500, 'Данный продукт уже удален. Обновите страницу.');

          $this->compare->remove($id);
        break;
      }
    }
  }

  /**
   * Возвращает не пустые параметры сравнения
   * @param ProductList $productList
   *
   * @return array;
   */
  protected function getParametersCompare($productList)
  {
    $notEmptyParametersCompare = array();

    foreach($productList->getDataProvider()->getData() as $product)
    {
      foreach($product->getParameters() as $parameter)
      {
        if( !empty($parameter->value) )
          $notEmptyParametersCompare[$parameter->id] = $parameter;
      }
    }

    return $notEmptyParametersCompare;
  }

  protected function getProductsForChange($sectionId)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('a.section_id', $sectionId);
    $criteria->order = 't.name';

    return new ProductList($criteria, null, false);
  }

  /**
   * @return FCollectionElement|ProductSection
   */
  protected function getSelectedSection()
  {
    $data = Yii::app()->request->getParam($this->compare->keyCollection, array());

    $sectionId = Arr::get($data, 'groupId');
    if( !$sectionId )
      $sectionId =  Arr::get($data, 'id');

    $selectedSection = null;

    /**
     * @var FCollectionElement|ProductSection $section
     */
    foreach($this->compare->getGroups() as $section)
    {
      if( $section->id == $sectionId  )
      {
        $selectedSection = $section;
        break;
      }
      else if( empty($selectedSection) )
        $selectedSection = $section;
    }

    return $selectedSection;
  }
}