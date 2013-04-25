<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Класс для работы с полями из контроллера необходимо добавить в метод CController::actions()
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
    $request = Yii::app()->request;
    $id = $request->getPost('id');
    $field = $request->getPost('field');
    $value = $request->getPost('value');
    $gridId = Yii::app()->request->getPost('gridId');

    if( !empty($id) && !empty($field) && isset($value) )
    {
      $this->init($id, $field, $value, $gridId)->process();
    }
  }

  /**
   * Присваивание нового значения для выбранной модели, с заданным полем и ID
   *
   * @throws CHttpException Бросается в случае ошибки при сохранении модели.
   */
  protected function process()
  {
    $field = $this->field;

    $this->model->setAttributes(array($field => $this->value));

    if( $this->model->save() )
    {
      if( Yii::app()->request->isAjaxRequest )
      {
        echo $this->model->$field;
      }
    }
    else
    {
      throw new CHttpException(500, implode("; ", $this->model->getErrors($field)));
    }
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