<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');

Yii::import('frontend.share.*');
Yii::import('frontend.share.validators.*');
Yii::import('frontend.extensions.upload.components.*');

abstract class AbstractConvertCommand extends CConsoleCommand
{
  /**
   * @var string update|clear
   */
  protected $mode;

  /**
   * @var array Таблицы с исходными данными
   */
  protected $srcTable = array();

  /**
   * @var array Таблицы, куда будут помещены новые данные
   */
  protected $dstTables = array();

  /**
   * @var bool Выводим ли в консоль сообщения об успешном сохранении записи
   */
  protected $showSuccessMessages = true;

  public function actionIndex($mode = 'update')
  {
    $this->mode = $mode;

    if( $this->mode === 'clear' && !empty($this->dstTables) )
    {
      if( !$this->confirm("Вы дейстивительно хотите очистить данные таблиц: ".implode(", ", $this->dstTables)."?".PHP_EOL."Все текущие записи будут потеряны!") )
        return;

      $this->clearAll();
    }

    $data = $this->findAll();
    foreach($data as $item)
    {
      if( !$this->create($item) )
        if( !$this->confirm('Желаете продолжить?', true) )
          break;
    }
  }

  protected function clearAll()
  {
    foreach($this->dstTables as $table)
    {
      $command = Yii::app()->db->createCommand("TRUNCATE TABLE ".$table);
      $command->execute();
    }
  }

  protected function save(BActiveRecord $model)
  {
    if( $model->save() )
    {
      if( $this->showSuccessMessages )
        echo 'Обработка '.get_class($model).' c id='.$model->id.' завершена ('.memory_get_usage().')'.PHP_EOL;

      return true;
    }
    else
    {
      echo 'Не удалось сохранить '.get_class($model).' c id='.$model->id.PHP_EOL;
      echo strip_tags(CHtml::errorSummary($model)).PHP_EOL;

      return false;
    }
  }

  /**
   * $criteria = new CDbCriteria();
   * $criteria->order = 'id ASC, name';
   *
   * $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
   * $command = $builder->createFindCommand($this->srcTable[SOME_KEY], $criteria);
   *
   * return $command->queryAll();
   */
  abstract protected function findAll();

  /**
   * @param mixed $data
   * @return bool $result
   *
   * $product = BProduct::model()->findByPk($data['id']);
   * if( $product )
   *   return true;
   *
   * $product       = new Product('convert');
   * $product->id   = $data['id'];
   * $product->name = $data['name'];
   * return $product->save($product);
   */
  abstract protected function create($data);
}