<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets
 *
 * Examples:
 * <pre>
 * echo $form->relatedItemsRow($model, 'steps', array(
 *   'position' => array('class' => 'span1'),
 *   'content' => array('class' => 'span8', 'label' => 'Текст'),
 *   'visible' => array('type' => 'checkbox'),
 *   'image' =>  array('tag' => 'image')
 * ));
 *
 * echo $form->relatedItemsRow($model, 'steps', array(
 *   'content' => array('tag' => function($model, $options) use($form) {
 *     $options['class'] = 'span10';
 *     echo CHtml::textArea(Arr::cut($options, 'name'), Arr::cut($options, 'value'), $options);
 *   }),
 * ));
 *
 * echo $form->relatedItemsRow($model, 'steps', array(
 *   'sections' => array('tag' => function($model, $options) use($form) {
 *     echo CHtml::dropDownList(Arr::cut($options, 'name'), Arr::cut($options, 'value'), CHtml::listData(Section::model()->findAll(), 'id', 'name'), $options);
 *   }),
 * ));
 * </pre>
 */
class RelatedItemsWidget extends CWidget
{
  /**
   * @var BActiveRecord
   */
  public $model;

  public $relation;

  public $attributes = array('name');

  protected $className;

  public function run()
  {
    if( !isset($this->model) )
    {
      throw new CHttpException(500, '"model" have to be set!');
    }

    $this->className = $this->model->getActiveRelation($this->relation)->className;

    $this->renderLabel();
    $this->renderElements();
    $this->renderCloneScript();
  }

  protected function renderLabel()
  {
    echo CHtml::openTag('th', array('class' => 'multi-list'));
    echo CHtml::tag('label', array(), $this->model->getAttributeLabel($this->relation));
    echo CHtml::tag('div', array(), CHtml::tag('span', array('class' => 'btn btn-info action', 'id' => 'add-item-btn-'.$this->relation), 'Добавить'));
    echo CHtml::closeTag('th');
  }

  protected function renderElements()
  {
    $element = new $this->className;

    echo CHtml::openTag('td', array('class' => 'multi-list'));
    $this->renderHeader($element);

    echo CHtml::openTag('ul', array('class' => 'multi-list-items'));
    $this->renderElement($element, array('id' => 'template', 'style' => 'display: none;'));

    foreach($this->model->{$this->relation} as $element)
      $this->renderElement($element);

    echo CHtml::closeTag('ul');
    echo CHtml::closeTag('td');
  }

  /**
   * @param BActiveRecord $element
   */
  protected function renderHeader($element)
  {
    echo CHtml::openTag('ul', array('class' => 'multi-list-header clearfix'));
    foreach($this->attributes as $key => $attributeOptions)
    {
      if( is_null($attributeOptions) )
        continue;

      $name = is_array($attributeOptions) ? $key : $attributeOptions;
      $label = Arr::cut($attributeOptions, 'label', $element->getAttributeLabel($name));
      echo CHtml::tag('li', array('class' => 'multi-list-header-col'), $label, false);
      echo '&nbsp;';
    }

    echo CHtml::closeTag('ul');
  }

  /**
   * @param BActiveRecord $element
   * @param array $htmlOptions
   *
   * @internal param null $id
   */
  protected function renderElement(BActiveRecord $element, $htmlOptions = array())
  {
    $id = Arr::get($htmlOptions, 'id', $element->getPrimaryKey());
    $htmlOptions['id'] = get_class($element).'-'.$id;

    echo CHtml::tag('li', $htmlOptions, false, false);

    foreach($this->attributes as $key => $attributeOptions)
    {
      if( is_null($attributeOptions) )
        continue;

      $attribute = is_array($attributeOptions) ? $key : $attributeOptions;
      $tag = Arr::get($attributeOptions, 'tag', 'input');
      $type = Arr::get($attributeOptions, 'type', 'text');

      $options = Arr::get($attributeOptions, 'htmlOptions', array('class' => 'span4'));

      if( isset($attributeOptions['class']) )
        $options['class'] = $attributeOptions['class'];

      $options['name'] = "{$this->className}[{$id}][{$attribute}]";
      $options['value'] = $element->$attribute;

      if( is_string($tag) )
      {
        switch($tag)
        {
          case 'image':
            echo CHtml::openTag('span', array('style' => 'display: inline-block;'));
            echo CHtml::openTag('span', array('style' => 'display: inline-block; width: 24px; margin-right: 7px;'));
            echo CHtml::image($element->getImage($attribute), '', Arr::get($attributeOptions, 'imageOptions', array('style' => 'max-width: 24px; max-height: 24px;')));
            echo CHtml::closeTag('span');
            echo CHtml::fileField($options['name'], $options['value']);
            echo CHtml::closeTag('span');
          break;

          case 'input':
            $options['type'] = Arr::get($options, 'type', $type);

            if( $options['type'] == 'checkbox' )
            {
              $options['value'] = CheckBoxBehavior::CHECKED_VALUE;
              if( !empty($element->$attribute) )
                $options['checked'] = 'checked';
            }

          default:
            echo CHtml::tag($tag, $options);
        }
      }
      elseif( is_callable($tag) )
      {
        call_user_func_array($tag, array($element, $options));
      }

      echo '&nbsp;';
    }

    $this->renderAjaxButton($id);
    echo CHtml::closeTag('li');
  }

  protected function renderAjaxButton($id)
  {
    echo CHtml::ajaxLink('', $this->controller->createUrl('deleteRelated'),
      array(
       'type' => 'post',
       'data' => array('id' => $id, 'relation' => $this->relation),
       'update' => '#'.$this->className.'-'.$id,
       'beforeSend' => "function(){return confirm('Вы действительно хотите удалить данный элемент?')}",
       'error' => 'function(){alert("Невозможно удалить элемент!")}'),
      array(
       'class' => 'btn btn-alone delete',
       'rel' => 'tooltip',
       'data-original-title' => 'Удалить элемент'
      )
    );
  }

  protected function renderCloneScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.$this->relation, "
      var className = '{$this->className}';
      var button    = $('#add-item-btn-{$this->relation}');
      var itemsExp  = '[name*=' + className + '\\\\[template\\\\]]';

      button.parents('tr').find(itemsExp).attr('disabled', 'disabled');

      $(button).on('click', function()
      {
        var tr        = $(this).parents('tr');
        var template  = tr.find('#' + className + '-template');
        var ul        = tr.find('td ul.multi-list-items');
        var count     = $(ul).find('li').length;
        var li        = template.clone();
        var re        = /(\w+)\[(\w+)\]\[(\w+)\]/;

        $(li).find(itemsExp).each(function(){
          var name = $(this).attr('name').replace(re, '$1[new' + String(count) + '][$3]');
          $(this).attr('name', name);
          $(this).removeAttr('disabled');
        });

        li.show().removeAttr('id').find('.delete').remove();
        li.append('<a class=\"btn btn-alone delete\" rel=\"tooltip\" href=\"#\" data-original-title=\"Удалить вариант\">');
        $(li).find('a').on('click', function(e){e.preventDefault();$(this).parents('li').remove()});
        $(ul).append(li);
      });

      var setHeaderSizes = function() {
        $('.multi-list-header li').each(function(index) {
          var width = $('ul.multi-list-items li:eq(1)').children().eq(index).outerWidth();
          $(this).width(width);
        });
      };

      setHeaderSizes();

      $(window).on('resize', function() {
        setHeaderSizes()
      });", CClientScript::POS_READY);
  }
}