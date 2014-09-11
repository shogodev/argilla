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
 *   'content' => array('class' => 'span8'),
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
    echo CHtml::openTag('td', array('class' => 'multi-list'));
    echo CHtml::openTag('ul');

    $this->renderElement(new $this->className, array('id' => 'template', 'style' => 'display: none;'));

    foreach($this->model->{$this->relation} as $element)
      $this->renderElement($element);

    echo CHtml::closeTag('ul');
    echo CHtml::closeTag('td');
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

    foreach($this->attributes as $key => $attribute)
    {
      $name = is_array($attribute) ? $key : $attribute;
      $tag  = Arr::get($attribute, 'tag', 'input');

      $options = Arr::get($attribute, 'htmlOptions', array('class' => 'span4'));

      if( isset($attribute['class']) )
        $options['class'] = $attribute['class'];

      $options['name']    = "{$this->className}[{$id}][{$name}]";
      $options['value']   = $element->$name;

      if( is_string($tag) )
      {
        if( $tag === 'input' )
          $options['type'] = Arr::get($options, 'type', 'text');

        echo CHtml::tag($tag, $options);
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
        var ul        = tr.find('td ul');
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
    ", CClientScript::POS_READY);
  }
}