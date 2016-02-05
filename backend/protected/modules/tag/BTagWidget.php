<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.tag.models.*');
/**
 * Class BTagWidget
 */
class BTagWidget extends CWidget
{
  public $group;

  public $itemId;

  public $urlAppend = '/backend/tag/tag/appendTag';

  //public $urlAppendTagItem = '/backend/tag/tag/appendTagIten';

  public $urlDelete = '/backend/tag/tag/deleteTagItem';

  /**
   * @var Tag[] $tagList
   */
  private $allTagList = array();

  /**
   * @var TagItem[] $tagList
   */
  private $tagItemList = array();

  private $idButton;

  private $idInput;

  private $appendTagItemClass = 'js-tag-append-item';

  private $deleteTagClass = 'js-tag-delete';

  public function init()
  {
    $criteriaItem = new CDbCriteria();
    $criteriaItem->with = array('tag');
    $criteriaItem->order = 'tag.name';
    $criteriaItem->compare('item_id', $this->itemId);

    if( !empty($criteriaItem->group) )
      $criteriaItem->compare('`group`', $this->group);

    $this->tagItemList = TagItem::model()->findAll($criteriaItem);

    $criteria = new CDbCriteria();
    $criteria->order = 'name';
    $criteria->addNotInCondition('id', CHtml::listData($this->tagItemList, 'tag_id', 'tag_id'));

    if( !empty($this->group) )
      $criteria->compare('`group`', $this->group);

    $this->allTagList = Tag::model()->findAll($criteria);

    $this->idInput = 'append-new-tag-'.$this->id.'-input';
    $this->idButton = 'append-new-tag-'.$this->id.'-button';

    Yii::app()->clientScript->registerScript($this->id.'#appendTagHandler', "
      $('#{$this->idButton}').on('click', function(e) {
        e.preventDefault();
        var data = {
          'appendTag' : {
            'name' : $('#{$this->idInput}').val(),
            'group' : '$this->group',
            'itemId' : '$this->itemId'
          }
        };

        $.post('{$this->urlAppend}', data, function() {
          $.get('', function(response) {
            var replaceId = '{$this->id}';
            var content = $('<div>' + response + '</div>');
            $('#' + replaceId).replaceWith(content.find('#' + replaceId));
            $('#{$this->idInput}').val('')
          });
        });
      });
    ");

    Yii::app()->clientScript->registerScript($this->id.'#appendTagItemHeandler', "
      $('body').on('click', '.{$this->appendTagItemClass}', function(e) {
        e.preventDefault();

        var data = {
          'appendTag' : {
            'name' : $(this).data('tag-name'),
            'group' : $(this).data('tag-group'),
            'itemId' : $(this).data('tag-item-id')
          }
        };

        var replaceId = $(this).data('widget-id');

        $.post('{$this->urlAppend}', data, function() {
          $.get('', function(response) {
            var content = $('<div>' + response + '</div>');
            $('#' + replaceId).replaceWith(content.find('#' + replaceId));
          });
        });
      });
    ");

    Yii::app()->clientScript->registerScript($this->id.'#deleteTagHeandler', "
      $('body').on('click', '.{$this->deleteTagClass}', function(e) {
        e.preventDefault();

        var data = {
          'deleteTag' : {
            'id' : $(this).data('tag-id'),
            'group' : $(this).data('tag-group'),
            'itemId' : $(this).data('tag-item-id')
          }
        };

        var replaceId = $(this).data('widget-id');

        $.post('{$this->urlDelete}', data, function() {
          $.get('', function(response) {
            var content = $('<div>' + response + '</div>');
            $('#' + replaceId).replaceWith(content.find('#' + replaceId));
          });
        });
      });
    ");
  }

  public function run()
  {
    echo CHtml::openTag('div', array('id' => $this->id));

    foreach($this->tagItemList as $item)
    {
      /*      $this->widget('OnFlyWidget', array(
        'ajaxUrl' => Yii::app()->createUrl('/tag/tag/onflyedit'),
        'attribute' => 'name',
        'primaryKey' => $item->item_id,
        'value' => $item->tag->name
      ));*/

      echo $item->tag->name;

      echo CHtml::link('[x]', '#', array(
        'class' => $this->deleteTagClass,
        'data-tag-id' => $item->tag_id,
        'data-tag-item-id' => $item->item_id,
        'data-tag-group' => $item->group,
        'data-widget-id' => $this->id
      ));
      echo end($this->tagItemList)->tag_id != $item->tag_id ? ',&nbsp;&nbsp;&nbsp;&nbsp;' : '';
    }

    echo CHtml::openTag('div');

    foreach($this->allTagList as $tag)
    {
      echo $tag->name;
      echo CHtml::link('[+]', '#', array(
        'class' => $this->appendTagItemClass,
        'data-tag-name' => $tag->name,
        'data-tag-item-id' => $this->itemId,
        'data-tag-group' => $this->group,
        'data-widget-id' => $this->id
      ));
      echo end($this->allTagList)->id != $tag->id ? ',&nbsp;&nbsp;&nbsp;&nbsp;' : '';
    }
    echo CHtml::closeTag('div');

    echo CHtml::closeTag('div');

    echo '<br/>'.CHtml::label('Новый тег:', $this->idInput).CHtml::activeTextField(new Tag(), 'name', array('class' => 'span3', 'id' => $this->idInput));

     $this->widget('BButton', array(
      'htmlOptions' => array('id' => $this->idButton),
      'label' => 'Добавить',
      'type' => 'info',
      'popupDepended' => true,
    ));
  }
}