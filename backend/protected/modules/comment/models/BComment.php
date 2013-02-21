<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.comment
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $model
 * @property integer $item
 * @property string $message
 * @property integer $visible
 *
 * @method static BComment model(string $class = __CLASS__)
 */
class BComment extends BActiveRecord
{
  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('user_id, model, item, message', 'required', 'except' => 'convert'),
      array('visible', 'numerical', 'integerOnly' => true),
    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'BFrontendUser', 'user_id'),
    );
  }

  /**
   * @return array
   */
  public function defaultScope()
  {
    return array(
      'order' => '`date` DESC'
    );
  }

  /**
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'user_id' => 'Пользователь',
      'model'   => 'Модель',
      'item'    => 'ID',
    ));
  }

  /**
   * @return null
   */
  public function getModelSectionName()
  {
    $sections = array(
      'Info' => 'Информация',
      'News' => 'Новости',
    );

    if( in_array($this->model, array_keys($sections)) )
      return $sections[$this->model];

    return null;
  }

  /**
   * Попытка получить название связанной с комментарием модели
   *
   * @return null|string
   * @throws CException
   */
  public function getItemName()
  {
    if( empty($this->model) || empty($this->item) )
      throw new CException('У комментария нет связи');

    $className = $this->model;
    $model     = $className::model()->findByPk($this->item);

    if( empty($model) )
      throw new CException('Неверная связь с моделью');

    $name = null;

    if( !empty($model->name) )
      $name = $model->name;
    elseif( !empty($model->title) )
      $name = $model->title;

    if( $name === null )
      throw new CException('Не удалось получить название записи');

    return $name;
  }

  /**
   * @return BDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('model', $this->model);
    $criteria->compare('visible', $this->visible);
    $criteria->compare('user_id', $this->user_id);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

}