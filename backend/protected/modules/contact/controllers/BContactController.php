<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.contact.controllers
 */
class BContactController extends BController
{
  public $name = 'Контакты';

  public $modelClass = 'BContact';

  public $position = 1;

  /**
   * @param $id
   */
  public function actionUpdate($id)
  {
    Yii::app()->clientScript->registerScript(
      'BContactFormScripts', "
      $(function(){
        //-----------------------------------------------------------------------------
        // Добавить тестовое поле
        var textblockCounter = 0;
        $('.action.btn[data-action=\"add-textblock\"]').on('click', function(){
          var name    = $('<input type=\"text\" style=\"width:250px\" name=\"BContactTextBlock[new][' + textblockCounter + '][name]\"    placeholder=\"Заголовок\" />');
          var sysname = $('<input type=\"text\" style=\"width:250px\" name=\"BContactTextBlock[new][' + textblockCounter + '][sysname]\" placeholder=\"Системное имя\" />');
      
          // создание кнопки \"Удалить\"
          var button_delete = $('<a href=\"#\" rel=\"tooltip\" title=\"Удалить текстовый блок\" class=\"btn btn-alone delete\" />');
          button_delete.on('click', function(e) {
            e.preventDefault();
            $(this).parent('div').remove();
          });
      
          var content = $('<div style=\"margin-bottom:5px\" />');
          content.append(name);
          $(content).append(' ');
          content.append(sysname);
          $(content).append(' ');
          $(content).append(button_delete);
          $('#textblock-add-cell').append(content);
      
          textblockCounter++;
        });
      
        //-----------------------------------------------------------------------------
        // Добавление группы полей
        var group_counter = 1;
        $('.action.btn[data-action=\"group\"]').on('click', function(){
          // создание поля с названием поля
          var name    = $('<input type=\"text\" style=\"width:250px\" name=\"BContactGroup[' + group_counter + '][name]\" placeholder=\"Название\" />');
      
          // создание поля с системным именем поля
          var sysname = $('<input type=\"text\" style=\"width:250px\" name=\"BContactGroup[' + group_counter + '][sysname]\" placeholder=\"Системное имя\" />');
      
          // создание кнопки \"Удалить\"
          var button_delete = $('<a href=\"#\" rel=\"tooltip\" title=\"Удалить поле\" class=\"btn btn-alone delete\" />');
          button_delete.on('click', function(e) {
            e.preventDefault();
            $(this).parent('div').remove();
          });
      
          var content = $('<div style=\"margin-bottom:5px\" />');
          content.append(name);
          $(content).append(' ');
          content.append(sysname);
          $(content).append(' ');
          $(content).append(button_delete);
      
          $('#group-add-cell').append(content);
          group_counter++;
        });
      
        //-----------------------------------------------------------------------------
        // Добавление поля к группе
        $('.action.btn[data-action=\"field\"]').on('click', function(){
          var group = $(this).attr('data-group'),
              list = $('.field-add-cell[data-group=' + group + '] ul');
          var last_id = list.find('li').size();
      
          // создание иконки
          var icon = $('<i class=\"icon-move\" style=\"background:none;\"/>');
      
          // создание поля со значением поля
          var name = $('<input type=\"text\" style=\"width:250px\" name=\"BContactField[new][' + last_id + '][' + group + '][value]\" />');
      
          // создание поля с описанием поля
          var description = $('<input type=\"text\" style=\"width:500px\" name=\"BContactField[new][' + last_id + '][' + group + '][description]\" />');
      
          // создание кнопки \"Удалить\"
          var button_delete = $('<a href=\"#\" rel=\"tooltip\" title=\"Удалить поле\" style=\"background:none;content:none;\" class=\"btn btn-alone delete\" />');
          button_delete.on('click', function(e) {
            e.preventDefault();
            $(this).parent('li').remove();
          });
      
          // добавляем все созданные элементы в список
          var content = $('<li class=\"not-sortable\" style=\"margin-bottom:5px;cursor:default;\" />');
      
          $(content).append(icon);
          $(content).append(' ');
          $(content).append(name);
          $(content).append(' ');
          $(content).append(description);
          $(content).append(' ');
          $(content).append(button_delete);
      
          list.append(content);
        });
      
        // -----------------------------------------------------------------------------------------
        // Сортировка полей в группе
        $('.sortable').sortable({
          items: 'li:not(.not-sortable)',
          update: function(event, ui) {
            sortFields(ui.item);
          }
        });
      
        function sortFields(item) {
          var url      = '" . $this->createUrl('sort') . "';
          var list     = [];
          var position = 1;
          var hasError = false;
      
          // создание массива полей
          $(item).parents('ul').children('li:not(.not-sortable)').each(function(){
            var listItem         = {};
            listItem['id']       = $(this).attr('data-fid');
            listItem['position'] = position;
      
            list.push(listItem);
            position++;
          });
      
          if( !hasError )
            $.post(url, {'sort' : list, 'type' : 'field'});
        }
      
        // -----------------------------------------------------------------------------------------
        // Сортировка текстовых блоков
        $('.textblock-container').sortable({
          update: function(event, ui) {
            sortTextBlocks(ui.item);
          }
        });
      
        function sortTextBlocks(item)
        {
          var url      = '" . $this->createUrl('sort') . "';
          var list     = [];
          var position = 1;
          var hasError = false;
      
          console.log(item);
      
          // создание массива полей
          $(item).parents('ul').children('li').each(function(){
            var listItem         = {};
            listItem['id']       = $(this).attr('data-textblock-id');
            listItem['position'] = position;
      
            list.push(listItem);
            position++;
          });
      
          if( !hasError )
            $.post(url, {'sort' : list, 'type' : 'textblock'});
        }
      
        // -------------------------------------------------------------------------------------------
        // Удаление полей
        $('.btn.delete').on('click', function(e){
          e.preventDefault();
      
          var id = $(this).attr('data-fid');
          var type = 'field';
      
          if( id === undefined )
          {
            id = $(this).attr('data-textblock-id');
            type = 'textblock';
          }
      
          if( id === undefined )
            return false;
      
          var url  = '" . $this->createUrl('delete') . "';
          var self = $(this);
      
          var callback = function(resp){
            $(self).parents('li').fadeOut();
          };
      
          $.post(url, {'delete' : {'id' : id, 'type' : type}}, callback);
        });
      
        // -------------------------------------------------------------------------------------------
        // Удаление группы полей
        $('span.delete-group').on('click', function(){
          if( !confirm('Вы уверены, что хотите удалить группу полей?') )
            return false;
      
          var url = '" . $this->createUrl('delete') . "';
          var id  = $(this).attr('data-group');
      
          var callback = function(resp){
            $('td.field-add-cell[data-group=' + id + ']').parents('tr').fadeOut();
          };
      
          $.post(url, {'delete' : {'id' : id, 'type' : 'group'}}, callback);
        });
      
      });",
      CClientScript::POS_END
    );

    parent::actionUpdate($id);
  }

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
   * @inheritdoc
   */
  protected function saveModels($models, $extendedSave = true, $redirectToUpdate = true)
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

        if( $redirectToUpdate )
        {
          Yii::app()->user->setFlash('success', 'Запись успешно '.($model->isNewRecord ? 'создана' : 'сохранена').'.');

          if( Yii::app()->request->getParam('action') )
            $this->redirect($this->getBackUrl());
          else
            $this->redirect(array('update', 'id' => $model->id));
        }
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
    $textblocks = Yii::app()->request->getPost('BContactTextBlock');

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