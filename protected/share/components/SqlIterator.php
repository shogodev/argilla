<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SqlIterator extends CDataProviderIterator
{
  public function __construct(CDbCriteria $criteria, $table, $chunkSize)
  {
    $command = Yii::app()->db->commandBuilder->createFindCommand($table, $criteria);
    $count = Yii::app()->db->commandBuilder->createCountCommand($table, $criteria)->queryScalar();

    $dataProvider = new CSqlDataProvider($command, array('params' => $criteria->params, 'totalItemCount' => $count));

    parent::__construct($dataProvider, $chunkSize);
  }
}