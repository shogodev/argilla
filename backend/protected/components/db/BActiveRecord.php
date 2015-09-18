<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.db
 */
abstract class BActiveRecord extends CActiveRecord implements IHasFrontendModel
{
  protected $_hints;

  protected $_popupHints;

  /**
   * @param string $className
   *
   * @return BActiveRecord|CActiveRecord
   */
  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  /**
   * Получение CHtml::listData по стандартным ключам $key => $value
   *
   * @param string $key
   * @param string|callable $value
   * @param CDbCriteria $criteria
   *
   * @return array
   */
  public static function listData($key = 'id', $value = 'name', CDbCriteria $criteria = null)
  {
    if( is_null($criteria) )
      $criteria = new CDbCriteria();
    /**
     * @var BActiveRecord
     */
    $model = static::model();

    if( empty($criteria->order) && Arr::get($model->tableSchema->columns, 'name') )
      $criteria->order = $model->getTableAlias(false, false).'.name';

    $data = array();
    foreach($model->findAll($criteria) as $entry)
    {
      $data[$entry->{$key}] = is_callable($value) ? $value($entry) : $entry->{$value};
    }

    return $data;
  }

  /**
   * Получение имени таблицы по имени текущей модели в виде {{class_name}}
   *
   * @return string
   */
  public function tableName()
  {
    return '{{'.Utils::toSnakeCase(BApplication::cutClassPrefix(get_class($this))).'}}';
  }

  /**
   * @param bool $runValidation
   * @param null $attributes
   * @return bool
   */
  public function save($runValidation = true, $attributes = null)
  {
    if( $this->isNestedSetModel() )
    {
      if( $this->isNewRecord )
      {
        $modelName = get_class($this);
        $parent    = $modelName::model()->findByPk($this->parent);
        $result    = $this->appendTo($parent);
      }
      else
        $result = $this->saveNode($runValidation, $attributes);

      return $result;
    }

    return parent::save($runValidation, $attributes);
  }

  /**
   * Сохраняем данные в моделях, связанных через отношение
   *
   * @param $relationName
   * @param array $relatedData
   * @param bool $ignoreEmptyItems
   *
   * @return bool
   * @throws CHttpException
   */
  public function saveRelatedModels($relationName, array $relatedData, $ignoreEmptyItems = true)
  {
    $models = $this->prepareModels($relationName, $relatedData, $ignoreEmptyItems);

    if( !$this->validateRelatedModels($models) )
      return false;

    foreach($models as $id => $model)
    {
      if( !$model->save(false) )
        throw new CHttpException(500, "Не удается сохранить зависимую модель ".get_class($model));
    }

    return true;
  }

  /**
   * @param BActiveRecord[] $models
   *
   * @return bool
   */
  public function validateRelatedModels(array $models)
  {
    $validationError = false;

    foreach($models as $id => $model)
    {
      if( !$model->validate() )
      {
        foreach($model->errors as $errors)
          foreach($errors as $value)
            $this->addError('relatedModelErrors', $value);

        $validationError = true;
      }
    }

    return !$validationError;
  }

  /**
   * @param $relationName
   * @param array $relatedData
   * @param bool|true $ignoreEmptyItems
   *
   * @return BActiveRecord[]
   */
  public function prepareModels($relationName, array $relatedData, $ignoreEmptyItems = true)
  {
    $models = array();
    $relation = $this->getActiveRelation($relationName);
    $className = $relation ? $relation->className : null;

    if( is_null($className) )
      return $models;

    if( is_array(reset($relatedData)) )
    {
      foreach($relatedData as $id => $attributes)
      {
        $value = trim(implode("", $attributes));

        if( empty($value) && $ignoreEmptyItems )
          continue;

        $models[$id] = $this->prepareModel($className, $relation, $id, $attributes);
      }
    }
    else
    {
      if( $relatedModel = $this->{$relationName} )
      {
        $models[$relatedModel->primaryKey] = $this->prepareModel($className, $relation, $relatedModel->primaryKey, $relatedData);
      }
      else
      {
        $models[] = $this->prepareModel($className, $relation, null, $relatedData);
      }
    }

    return $models;
  }

  /**
   * @param string $className
   * @param CActiveRelation $relation
   * @param integer $id
   * @param array $attributes
   *
   * @return BActiveRecord
   */
  private function prepareModel($className, $relation, $id, $attributes)
  {
    /**
     * @var BActiveRecord $model
     */
    $model = $className::model()->findByPk($id);

    if( !$model )
    {
      $model = new $className();
      $model->{$relation->foreignKey} = $this->getPrimaryKey();
    }

    $model->setAttributes(Arr::trim($attributes));

    return $model;
  }

  public function getFormId()
  {
    return get_called_class().'-form';
  }

  public function yesNoList()
  {
    return array(
      array('id' => 1, 'name' => 'Да'),
      array('id' => 0, 'name' => 'Нет'),
    );
  }

  public function getImageTypes()
  {
    return array();
  }

  public function relations()
  {
    return array(
      'associations' => array(self::HAS_MANY, 'BAssociation', 'src_id', 'on' => 'src="'.get_class($this).'"'),
    );
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return BActiveDataProvider
   */
  public function search(CDbCriteria $criteria = null)
  {
    if( !isset($criteria) )
      $criteria = new CDbCriteria();

    $this->onBeforeSearch(new CEvent($this, array('criteria' => $criteria)));

    $criteria = $this->getSearchCriteria($criteria);
    $params = $this->getSearchParams();

    return new BActiveDataProvider($this, CMap::mergeArray(array(
      'criteria' => $criteria), $params
    ));
  }

  /**
   * @param $event
   */
  public function onBeforeSearch($event)
  {
    $this->raiseEvent('onBeforeSearch', $event);
  }

  public function attributeLabels()
  {
    return array(
      'id'               => '#',
      'section_id'       => 'Раздел',
      'category_id'      => 'Категория',
      'type_id'          => 'Тип',
      'collection_id'    => 'Коллекция',
      'country_id'       => 'Страна',
      'year_id'          => 'Год',
      'position'         => 'Позиция',
      'url'              => 'Url',
      'reference'        => 'Ссылка',
      'date'             => 'Дата',
      'notice'           => 'Анонс',
      'name'             => 'Заголовок',
      'articul'          => 'Артикул',
      'content'          => 'Полный текст',
      'img'              => 'Изображение',
      'upload'           => 'Изображения',
      'template'         => 'Шаблон',
      'visible'          => 'Вид',
      'main'             => 'На главной',
      'novelty'          => 'Новинка',
      'discount'         => 'Скидка',
      'delivery'         => 'Доставка',
      'dump'             => 'Наличие',
      'siblings'         => 'Соседи',
      'children'         => 'Потомки',
      'menu'             => 'В меню',
      'sitemap'          => 'На карту сайта',
      'tag'              => 'Теги',
      'price'            => 'Цена',
      'price_old'        => 'Старая цена',
      'key'              => 'Ключ',
      'location'         => 'Расположение',
      'group'            => 'Группа',
      'date_from'        => 'Дата с...',
      'date_to'          => 'по ...',
      'comment'          => 'Комментарий',
      'sum'              => 'Сумма',
      'email'            => 'E-mail',
      'phone'            => 'Телефон',
      'address'          => 'Адрес',
      'date_create'      => 'Дата создания',
      'status'           => 'Статус',
      'subject'          => 'Тема',
      'message'          => 'Сообщение',
      'password'         => 'Пароль',
      'type'             => 'Тип',
      'sysname'          => 'Системное имя',
      'model'            => 'Модель',
      'attribute'        => 'Атрибут'
    );
  }

  /**
   * Задает popup подсказки
   * @return array
   */
  public function getPopupHints()
  {
    if( $this->_popupHints === null )
    {
      $items = BHint::model()->findAllByAttributes(
        array(
          'model' => get_class($this),
          'popup' => '1'
        ));

      $this->_popupHints = CHtml::listData($items, 'attribute', 'content');
    }

    return $this->_popupHints;
  }

  /**
   * Получает popup подсказку для атрибута
   * @param $attribute
   * @return null
   */
  public function getPopupHint($attribute)
  {
    $hints = $this->popupHints;

    return isset($hints[$attribute]) ? $hints[$attribute] : null;
  }

  /**
   * Задает подсказки
   * @return array
   */
  public function getHints()
  {
    if( $this->_hints === null )
    {
      $items = BHint::model()->findAllByAttributes(
        array(
          'model' => get_class($this),
          'popup' => '0'
        ));

      $this->_hints = CHtml::listData($items, 'attribute', 'content');
    }

    return $this->_hints;
  }

  /**
   * Получает подсказку для атрибута
   * @param $attribute
   * @return null
   */
  public function getHint($attribute)
  {
    $hints = $this->hints;

    return isset($hints[$attribute]) ? $hints[$attribute] : null;
  }

  /**
   * @return string
   */
  public function getFrontendModelName()
  {
    return preg_replace('/^(B)/', '', get_class($this));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    return $criteria;
  }

  /**
   * @return array of search params
   */
  protected function getSearchParams()
  {
    return array();
  }

  /**
   * Проверка на поддержку моделью nested set
   * @return bool
   */
  private function isNestedSetModel()
  {
    return $this->asa('nestedSetBehavior') !== null;
  }
}