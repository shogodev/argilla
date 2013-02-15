<?php

/**
 * Класс для работы с полями из контроллера
 * необходимо добавить в метод CController::actions()
 *
 * @package onFlyEdit
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 22.08.2012
 */
class OnFlyEditAction extends CAction
{
  /**
   * Объект модели
   *
   * @var BActiveRecord
   */
  protected $model;

  /**
   * PK записи в базе
   *
   * @var int
   */
  protected $id;

  /**
   * Название поля в базе / свойства модели
   *
   * @var string
   */
  protected $field;

  /**
   * Новое значение для свойства модели
   *
   * @var string
   */
  protected $value;

  public function run()
  {
    if( !empty($_POST['id']) && !empty($_POST['field']) && isset($_POST['value']) )
    {
      $gridId = Yii::app()->request->getPost('gridId');
      $this->init($_POST['id'], $_POST['field'], $_POST['value'], $gridId)->process();
    }
  }

  /**
   * Присваивание нового значения для выбранной модели, с заданным полем и ID
   *
   * @return mixed
   */
  protected function process()
  {
    $field = $this->field;

    $this->model->$field = $this->value;

    if( $this->model->save() )
      echo CJSON::encode(array('data' => $this->model->$field));
    else
      echo CJSON::encode(array('error' => 'Невозможно сохранить запись', 'message' => $this->model->getError($field)));
  }

  /**
   * Инициализация свойств
   *
   * @param $id
   * @param $field
   * @param $value
   * @param $gridId
   *
   * @return OnFlyEditAction
   */
  protected function init($id, $field, $value, $gridId)
  {
    $this->id    = $id;
    $this->field = $field;
    $this->value = $value;

    if( !empty($gridId) )
    {
      $this->model = $this->parseGridId($gridId)->findByPk($this->id);
    }
    else
      $this->model = $this->controller->loadModel($this->id);

    return $this;
  }

  /**
   * @param $gridId
   *
   * @return BActiveRecord
   */
  protected function parseGridId($gridId)
  {
    preg_match("/(\w+)_(\w+)-(\w+)$/U", $gridId, $matches);
    if( !empty($matches) )
    {
      $model = $matches[1];
      $table = $matches[2];
      $type  = $matches[3];

      if( $type === 'files' && isset(Yii::app()->db->schema->tables[Yii::app()->db->tablePrefix.$table]) )
      {
        $model = new UploadModel($table);
      }
    }

    return $model;
  }
}