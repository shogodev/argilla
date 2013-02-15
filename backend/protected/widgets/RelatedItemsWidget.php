<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.RelatedItemsWidget
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

    $this->getLabel();
    $this->getElements();
    $this->getScript();
  }

  protected function getLabel()
  {
    echo CHtml::openTag('th', array('class' => 'multi-list'));
    echo CHtml::tag('label', array(), $this->model->getAttributeLabel($this->relation));
    echo CHtml::tag('div', array(), CHtml::tag('span', array('class' => 'btn btn-info action', 'id' => 'add-item-btn-'.$this->relation), 'Добавить'));
    echo CHtml::closeTag('th');
  }

  protected function getElements()
  {
    echo CHtml::openTag('td', array('class' => 'multi-list'));
    echo CHtml::openTag('ul');

    foreach($this->model->{$this->relation} as $element)
    {
      $id = $element->getPrimaryKey();

      echo CHtml::tag('li', array('id' => get_class($element).'-'.$id), false, false);

      foreach($this->attributes as $key => $attribute)
      {
        $name  = is_array($attribute) ? $key : $attribute;
        $type  = Arr::get($attribute, 'type', 'text');
        $class = Arr::get($attribute, 'class', 'span4');

        echo CHtml::tag('input', array(
          'type' => $type,
          'name' => "{$this->className}[{$id}][{$name}]",
          'data-id' => $id,
          'value' => $element->$name,
          'class' => $class,
        ));
        echo '&nbsp;';
      }

      $this->getAjaxButton($id);
    }

    echo CHtml::closeTag('li');
    echo CHtml::closeTag('ul');
    echo CHtml::closeTag('td');
  }

  protected function getAjaxButton($id)
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

  protected function getScript()
  {
    $attributes = json_encode($this->attributes);
    Yii::app()->clientScript->registerScript(__CLASS__.$this->relation, <<<EOD
$('#add-item-btn-{$this->relation}').on('click', function()
  {
    var attrs = {$attributes};
    var cl = '{$this->className}';
    var ul = $(this).parents('tr').find('td ul');
    var li = $('<li>');
    var j  = $(ul).find('li').length;

    for(var i in attrs)
    {
      if(attrs.hasOwnProperty(i))
      {
        var name = typeof (attrs[i]) == 'object' ? i : attrs[i];
        var type = attrs[i] && attrs[i].type ? attrs[i].type : 'text';
        var cls  = attrs[i] && attrs[i].class ? attrs[i].class : 'span4';
        var inp  = $('<input type="'+ type +'">');

        $(li).append(inp);
        $(li).append('&nbsp;');
        $(inp).attr('name', cl + '[new' + j + ']['+ name +']');
        $(inp).addClass(cls);
      }
    }

    li.append('<a class="btn btn-alone delete" rel="tooltip" href="#" data-original-title="Удалить вариант">');
    $(li).find('a').on('click', function(e){e.preventDefault();$(this).parents('li').remove()});
    $(ul).append(li);
  });
EOD
    ,CClientScript::POS_READY);
  }
}