<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.controllers
 */
class BContactController extends BController
{
  public $name = 'Контакты';

  public $modelClass = 'BContact';

  public $position = 1;

  /**
   * Сохранение сортировки
   */
  public function actionSort()
  {
    switch( Yii::app()->request->getPost('type') )
    {
      case 'field':
        $this->sortFields();
        break;
      case 'textblock':
        $this->sortTextBlocks();
        break;
    }
  }

  /**
   * Удаление поля из группы полей
   */
  public function actionDelete()
  {
    $data = Yii::app()->request->getPost('delete');

    if( !empty($data['id']) )
    {
      /**
       * Может приходить всего 3 типа данных на удаление
       * field - поле в группе
       * group - группа полей
       * textbloc - текстовой блок
       */
      switch( $data['type'] )
      {
        case 'field':
          $field = BContactField::model()->findByPk($data['id']);
          $field->delete();
          break;

        // Для группы полей так же удаляем все дочерние элементы
        case 'group':
          $group = BContactGroup::model()->findByPk($data['id']);
          $group->delete();
          break;

        case 'textblock':
          $textblock = BContactTextBlock::model()->findByPk($data['id'])->delete();
          break;
      }
    }
  }

  /**
   * Сортировка полей группы
   */
  protected function sortFields()
  {
    foreach( Yii::app()->request->getPost('sort') as $sortItem )
    {
      $field           = BContactField::model()->findByPk($sortItem['id']);
      $field->position = $sortItem['position'];
      $field->save();
    }
  }

  /**
   * Сортировка текстовых блоков
   */
  protected function sortTextBlocks()
  {
    foreach( Yii::app()->request->getPost('sort') as $sortItem )
    {
      $textblock = BContactTextBlock::model()->findByPk($sortItem['id']);
      $textblock->position = $sortItem['position'];
      $textblock->save();
    }
  }

  /**
   * @param array $models
   * @param bool $extendedSave
   */
  protected function saveModels($models, $extendedSave = true)
  {
    $model = Arr::reset($models);

    Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );

    $this->performAjaxValidation($model);
    $attributes = Yii::app()->request->getPost(get_class($model));

    if( isset($attributes) )
    {
      $model->setAttributes($attributes);

      if( $model->save() )
      {
        $this->saveGroup($model);
        $this->saveField();

        $this->saveTextblock($model);

        Yii::app()->user->setFlash('success', 'Запись успешно '.($model->isNewRecord ? 'создана' : 'сохранена').'.');

        if( Yii::app()->request->getParam('action') )
          $this->redirect($this->getBackUrl());
        else
          $this->redirect(array('update', 'id' => $model->id));
      }
    }
  }

  /**
   * Сохранение текстовых блоков для записи контактов
   *
   * @param BContact $model
   */
  protected function saveTextblock(BContact $model)
  {
    $textblocks = Yii::app()->request->getPost('ContactTextBlock');

    if( !empty($textblocks['new']) )
    {
      $this->createTextBlock($model, $textblocks['new']);
      unset($textblocks['new']);
    }

    if( !empty($textblocks) )
    {
      foreach( $textblocks as $id => $values )
      {
        $textblock = BContactTextBlock::model()->findByPk($id);

        if( !empty($values['delete']) )
          $textblock->delete();
        else
        {
          if( empty($values['visible']) )
          $values['visible'] = 0;

          $textblock->setAttributes($values);
          $textblock->save();
        }
      }
    }
  }

  /**
   * Создание новых текстовых блоков
   *
   * @param BContact $model
   * @param array $data
   */
  public function createTextBlock(BContact $model, array $data)
  {
    foreach( $data as $item )
    {
      $textblock = new BContactTextBlock();
      $textblock->setAttributes($item);
      $textblock->contact_id = $model->id;
      $textblock->save();
    }
  }

  /**
   * Сохранение групп для текущей записи контактов
   *
   * @param array $model
   */
  protected function saveGroup($model)
  {
    $groups = Yii::app()->request->getPost('BContactGroup');

    if( empty($groups) ) return;

    foreach( $groups as $group )
    {
      $contactGroup             = new BContactGroup();
      $contactGroup->name       = $group['name'];
      $contactGroup->sysname    = $group['sysname'];
      $contactGroup->contact_id = $model->id;

      $contactGroup->save();
    }
  }

  /**
   * Сохранение полей для групп полей
   * Так же осуществляется создание новых полей по ключу 'new'
   */
  protected function saveField()
  {
    $fields = Yii::app()->request->getPost('BContactField');

    if( empty($fields) ) return;

    foreach( $fields as $id => $field )
    {
      // Если в качестве ключа используется PK, то обновляем запись
      if( is_numeric($id) )
      {
        $contactField              = BContactField::model()->findByPk($id);
        $contactField->value       = $field['value'];
        $contactField->description = $field['description'];
        $contactField->save();
      }
      // Если используется ключ new, создаем навые записи
      elseif( $id == 'new' )
      {
        foreach( $field as $newFields )
        {
          foreach( $newFields as $groupId => $params )
          {
            $contactField              = new BContactField();
            $contactField->value       = $params['value'];
            $contactField->description = $params['description'];
            $contactField->group_id    = $groupId;
            $contactField->save();
          }
        }
      }
    }
  }
}