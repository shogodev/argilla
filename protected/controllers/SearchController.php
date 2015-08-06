<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class SearchController extends FController
{
  public function actionIndex()
  {
    $this->breadcrumbs = array('Результаты поиска');

    $this->render('search', array(
      'yandexSearchId' => Yii::app()->params['yandexSearchId'],
    ));
  }

  public function actionPredictive()
  {
    $query = Yii::app()->request->getPost('query');
    $query = trim($query, ' ');

    if( Yii::app()->request->isAjaxRequest && !empty($query) )
    {
      $criteria = new CDbCriteria();
      $criteria->select = 'id';
      $criteria->addSearchCondition('name', $query);
      $criteria->limit = 10;

      $this->createIndexTable();

      $command = Yii::app()->db->schema->commandBuilder->createFindCommand('{{product_search_index}}', $criteria);
      $ids = $command->queryColumn();

      $data = array();
      foreach(Product::model()->visible()->findAllByPk($ids) as $product)
      {
        $data[] = array(
          'label' => $this->createTemplate($product, $query),
          'value' => $product->name,
          'url' => $product->getUrl(),
        );
      }

      echo CJSON::encode($data);
    }
  }

  private function createTemplate(Product $product, $query)
  {
    $name = array();

    if( !empty($product->section) )
      $name[] = $product->section->name;
    if( isset($product->category) )
      $name[] = $product->category->name;
    $name[] = $product->name;

    return $this->renderPartial('_popup_item', array(
      'name' => $this->markKeyword(implode('&nbsp;', $name), $query),
      'price' => $product->getPrice(),
      'image' => $product->getImage() ? $product->getImage()->pre : null,
      'articul' => $this->markKeyword($product->articul, $query)
    ), true);
  }

  private function createIndexTable()
  {
    if( !isset(Yii::app()->db->schema->tables[Yii::app()->db->tablePrefix.'product_search_index']) )
    {
      $command = Yii::app()->db->schema->commandBuilder->createSqlCommand(
        "CREATE VIEW {{product_search_index}} AS SELECT p.id, CAST(CONCAT_WS(' ', p.name, p.articul, s.name, c.name) AS CHAR) AS name  FROM {{product}} AS p
        JOIN {{product_assignment}} AS a ON p.id = a.product_id
        LEFT OUTER JOIN {{product_section}} AS s ON a.section_id = s.id
        LEFT OUTER JOIN {{product_category}} AS c ON a.category_id = c.id"
      );
      $command->execute();
    }
  }

  private function markKeyword($value, $query)
  {
    $value = preg_replace('/('.$query.')/', '<strong>$1</strong>', $value);

    return $value;
  }
}