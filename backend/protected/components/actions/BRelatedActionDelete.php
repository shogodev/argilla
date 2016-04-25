<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.actions
 */
class BRelatedActionDelete extends CAction
{
  public function run()
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $result = $this->tryDelete();

      if( !$result )
        throw new CHttpException(500, 'Не могу удалить запись.');
    }
    else
      throw new CHttpException(500, 'Некорректный запрос.');
  }

  private function tryDelete()
  {
    $id = Yii::app()->request->getPost('id');
    $relation = Yii::app()->request->getPost('relation');

    /**
     * @var BActiveRecord $model
     * @var BActiveRecord $relatedModel
     */
    $model = new $this->controller->modelClass;
    $className = $model->getActiveRelation($relation)->className;
    $relatedModel = $className::model()->findByPk($id);

    try
    {
      $result = $relatedModel->delete();
    }
    catch(CDbException $e)
    {
      if( strpos($e->getMessage(), 'update a parent row: a foreign key constraint fails') )
      {
        if( !$message = $this->parseError($e->getMessage(), $id) )
          throw $e;

        echo $message;
        Yii::app()->end();
      }
    }

    return $result;
  }

  private function parseError($error, $pk)
  {
    if( !preg_match('/a foreign key constraint fails \((.+)\). The SQL/', $error, $matchesQuery) )
      return null;

    if( !preg_match('/`(\w+)`.`(\w+)`.+FOREIGN KEY.+`(\w+)`.+REFERENCES+.`(\w+)`/', $matchesQuery[1], $matches) )
      return null;

    $db = $matches[1];
    $table = $matches[2];
    $field = $matches[3];

    if( !$records = $this->getRelatedRecords($table, $field, $pk) )
      return null;

    return $this->createResponse($table, $records);
  }

  private function getRelatedRecords($table, $field, $pk)
  {
    $criteria = new CDbCriteria();
    $criteria->compare($field, $pk);

    $command = Yii::app()->db->getSchema()->commandBuilder->createFindCommand($table, $criteria);
    return $command->queryAll();
  }

  private function createResponse($table, $records)
  {
    switch($table)
    {
      case $this->getFullTableName(BProductParam::model()->tableName()):
        $urls = array();
        foreach(CHtml::listData($records, 'product_id', 'product_id') as $productId)
        {
          $urls[$productId] = CHtml::link('Товар id='.$productId, '/backend/product/product/update/'.$productId, array('target' => '_blank'));
        }
        $out = 'Товары имеющие сввязанные параметры:<br/>';
        $out .= implode('<br/>', $urls);
      break;
    }

    return $out;
  }

  private function getFullTableName($shortName)
  {
    $normalShortName = trim($shortName, '{}');
    return Yii::app()->db->tablePrefix.$normalShortName;
  }
}