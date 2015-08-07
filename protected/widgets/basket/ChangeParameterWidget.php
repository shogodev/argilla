<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class ChangeParameterWidget виджет меняет параметры элемента в корзине
 * Пример подключения:
 * $this->widget('ChangeParameterWidget', array('element' => $data, 'items' => $data->getPersons()))
 */
class ChangeParameterWidget extends CWidget
{
  const DROPDOWN_LIST = 'activeDropDownList';

  /**
   * @var BActiveRecord|FCollectionElementBehavior элемент корзины
   */
  public $element;

  public $items;

  public $type = self::DROPDOWN_LIST;

  public $parameterItemIndex = 'parameter';

  public $cssClass = 'js-change-parameter';

  public $data;

  public $htmlOptions = array();

  public $beforeAjaxScript = '';

  public $afterAjaxScript = '';

  private $render = true;

  public function init()
  {
    if( !isset($this->element) )
      throw new CHttpException(500, 'Ошибка. Не задано свойство element');

      /**
       * @var ProductParameter $parameter
       */
    if( !($parameter = $this->element->getCollectionItems($this->parameterItemIndex)) || empty($this->items) )
    {
      $this->render = false;
      return;
    }

    if( !isset($this->htmlOptions['data-index']) )
      $this->htmlOptions['data-index'] = $this->element->collectionIndex;

    $this->data = CHtml::listData($this->items, 'id', function($parameter) {
      return $parameter->variant->name;
    });

    if( !isset($this->htmlOptions['data-type']) )
      $this->htmlOptions['data-type'] = Utils::modelToSnakeCase($parameter);

    if( !isset($this->htmlOptions['options']) )
    {
      $this->htmlOptions['options'] = CHtml::listData($this->items, 'id', function($item) use($parameter)
      {
        $optionAttributes = array();

        if( $item->id == $parameter->id )
          $optionAttributes['selected'] = 'selected';

          return $optionAttributes;
      });
    }
    $this->htmlOptions['class'] = !isset($this->htmlOptions['class']) ? $this->cssClass : $this->htmlOptions['class'].' '.$this->cssClass;

  }

  public function run()
  {
    if( $this->render )
    {
      $this->registerScript();
      echo CHtml::activeDropDownList($this->element, 'id', $this->data, $this->htmlOptions);
    }
  }

  private function registerScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__.'#Script', "
      $('body').on('change', '.{$this->cssClass}', function() {
        var data = {
          'index' : $(this).data('index'),
          {$this->parameterItemIndex} : {'type' : $(this).data('type'), 'id' : $(this).val()}
        };

        $.fn.collection('".Yii::app()->controller->basket->keyCollection."').send({
          'action' : 'changeItems',
          'data' : data,
          'beforeAjaxScript' : function(element, data, action) {{$this->beforeAjaxScript}},
          'afterAjaxScript' : function(element, data, action, response) {{$this->afterAjaxScript}}
        });
      });
    ");
  }
}