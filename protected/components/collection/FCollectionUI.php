<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FCollectionUI extends FCollection
{
  /**
   * Добавить элемент в коллекцию только 1 раз
   */
  const BT_ADD_ONCE = 1;

  /**
   * Добавлять элемент в коллекцию всегда
   */
  const BT_ADD_ALWAYS = 2;

  /**
   * Работа в режиме переключателя
   */
  const BT_TOGGLE = 3;

  public $ajaxUrl;

  public $addButtonAjaxUrl;

  public $addThroughButtonAjaxUrl = array('product/addThroughPopup');

  public $ajaxUpdate = array();

  public $beforeAjaxScript = array();

  public $afterAjaxScript = array();

  protected $classAdd = 'to-{keyCollection}';

  protected $classAlreadyInCollection = 'already-in-{keyCollection}';

  protected $classRemove = 'remove-{keyCollection}';

  protected $classAmount = 'amount-{keyCollection}';

  protected $classChangeAmount = 'change-amount-{keyCollection}';

  protected $classAddThroughPopupButton = 'add-through-popup-{keyCollection}';

  protected $popupAutofocusFieldId = 'autofocus-field';

  public function getSum()
  {
    $sum = 0;

    foreach($this as $element)
    {
      $sum += $element->getSum();
    }

    return $sum;
  }

  public function getSumTotal()
  {
    return $this->getSum();
  }

  /**
   * @param string|array $elements - id элемента или масив id
   *
   * @return $this
   */
  public function ajaxUpdate($elements)
  {
    $elements = is_array($elements) ? $elements : array($elements);

    $this->ajaxUpdate = CMap::mergeArray($this->ajaxUpdate, $elements);

    return $this;
  }

  public function addBeforeAjaxScript(CJavaScriptExpression $script)
  {
    $this->beforeAjaxScript = CMap::mergeArray($this->beforeAjaxScript, array($script));
  }

  public function addAfterAjaxScript(CJavaScriptExpression $script)
  {
    $this->afterAjaxScript = CMap::mergeArray($this->afterAjaxScript, array($script));
  }

  /**
   * Строит кнопку добавления в коллекцию.
   * Пример:
   * <pre>
   *  echo $this->basket->buttonAdd($model, 'Добавить в корзину', array('class' => 'btn, 'data-amount' => 1))
   *  echo $this->basket->buttonAdd(array('id' => 1, 'type' => 'product'), array('Добавить в корзину', 'В корзине'), array('class' => 'btn), FCollectionUI::BT_ADD_ONCE)
   * </pre>
   * @param array|FCollectionElementBehavior|CActiveRecord $data
   * @param array|string $text
   * @param array $htmlOptions
   * @param int $buttonType тип кнопки (может быть: self::BT_ADD_ONCE | self::BT_ADD_ALWAYS | self::BT_TOGGLE). По умодчанию BT_ADD_ALWAYS
   *
   * @return string
   */
  public function buttonAdd($data, $text = '', $htmlOptions = array(), $buttonType = self::BT_ADD_ALWAYS)
  {
    $this->appendHtmlOption($htmlOptions, $this->classAdd);

    return $this->createButtonAdd($data, $text, $htmlOptions, $buttonType);
  }

  /**
   * @param array|FCollectionElementBehavior|CActiveRecord $data
   * @param string $text
   * @param array $htmlOptions
   * @param bool $confirm
   *
   * @return string
   */
  public function buttonRemove($data, $text = '', $htmlOptions = array(), $confirm = true)
  {
    $preparedData = $this->prepareInputData($data);

    $this->appendHtmlOption($htmlOptions, $this->classRemove);

    if( $confirm !== false)
      $htmlOptions['data-confirm'] = ($confirm === true ? 'true' : CJavaScript::quote($confirm));

    return CHtml::link($text, '#', CMap::mergeArray($preparedData, $htmlOptions));
  }

  public function buttonAddThroughPopup($data, $text = '', $htmlOptions = array(), $buttonType = self::BT_ADD_ALWAYS)
  {
    $this->appendHtmlOption($htmlOptions, $this->classAddThroughPopupButton);

    return $this->createButtonAdd($data, $text, $htmlOptions, $buttonType);
  }

  /**
   * Строит input меняющий количество на кнопке добавления в коллекцию.
   * Пример:
   * <pre>
   *   echo $this->basket->inputAmountButtonAdd('#button-id', array('class' => 'inp'))
   *   или
   *   echo $this->basket->inputAmountButtonAdd('.calculator-popup')
   * </pre>
   *
   * @param string $targetSelector селектор кнопки добавления в коллекцию или селектор блока содержащего кнопку добавления в коллекцию
   * @param array $htmlOptions
   * @param int $defaultAmount количество по умолчанию
   * @param int $step
   *
   * @return string
   */
  public function inputAmountButtonAdd($targetSelector, $htmlOptions = array(), $defaultAmount = 1, $step = 1)
  {
    $this->appendHtmlOption($htmlOptions, $this->classAmount);
    $this->appendHtmlOption($htmlOptions, $targetSelector, 'data-target-selector');
    $this->appendHtmlOption($htmlOptions, '.'.$this->classAdd, 'data-button-selector');
    $this->appendHtmlOption($htmlOptions, $step, 'data-step');

    return CHtml::textField('amount', $defaultAmount, $htmlOptions);
  }

  /**
   * Строит input меняющий количество у элемента с селектором $targetSelector
   *
   * @param $targetSelector
   * @param array $htmlOptions
   * @param int $defaultAmount
   * @param int $step
   *
   * @return string
   */
  public function inputAmountElement($targetSelector, $htmlOptions = array(), $defaultAmount = 1, $step = 1)
  {
    $this->appendHtmlOption($htmlOptions, $this->classAmount);
    $this->appendHtmlOption($htmlOptions, $targetSelector, 'data-target-selector');
    $this->appendHtmlOption($htmlOptions, $step, 'data-step');

    return CHtml::textField('amount', $defaultAmount, $htmlOptions);
  }

  /**
   * Инпут смены количества в коллекции
   *
   * @param $element - элемент коллекции
   * @param array $htmlOptions
   * @param int $step
   *
   * @return string
   */
  public function inputAmountCollection($element, $htmlOptions = array(), $step = 1)
  {
    $this->appendHtmlOption($htmlOptions, $this->classChangeAmount);
    $this->appendHtmlOption($htmlOptions, $step, 'data-step');

    if( empty($htmlOptions['data-index']) )
      $htmlOptions['data-index'] = $element->collectionIndex;

    return CHtml::textField('amount', $element->collectionAmount * $step, $htmlOptions);
  }

  protected function createButtonAdd($data, $text = '', $htmlOptions = array(), $buttonType = self::BT_ADD_ALWAYS)
  {
    $preparedData = $this->prepareInputData($data);
    $isInCollection = $this->isInCollection($preparedData['data-type'], $preparedData['data-id']);

    switch ($buttonType)
    {
      case self::BT_ADD_ONCE:
        $this->appendHtmlOption($htmlOptions, $isInCollection ? 1 : 0, 'data-do-not-add');
        break;

      case self::BT_TOGGLE:
        $this->appendHtmlOption($htmlOptions, $isInCollection ? 1 : 0, 'data-do-not-add');
        $this->appendHtmlOption($htmlOptions, $isInCollection ? 1 : 0, 'data-remove-toggle');
       break;
    }

    if( $isInCollection )
    {
      $this->appendHtmlOption($htmlOptions, $this->classAlreadyInCollection);
    }

    return CHtml::link($this->prepareText($text, $isInCollection, $htmlOptions), '#', CMap::mergeArray($preparedData, $htmlOptions));
  }

  /**
   * @param array|CModel|FCollectionElementBehavior $data
   *
   * @return array
   */
  public function prepareInputData($data)
  {
    $preparedData = array();

    if( $data instanceof CModel )
    {
      $preparedData['data-id'] = $data->primaryKey;
      $preparedData['data-type'] = Utils::toSnakeCase(get_class($data));

      if( isset($data->collectionIndex) )
        $preparedData['data-index'] = $data->collectionIndex;

      if( $items = $data->defaultCollectionItems() )
        $preparedData['data-items'] = CJSON::encode($items);
    }
    else
    {
      foreach($data as $key => $item)
      {
        if( !preg_match('/^data-(.*)/', $key) )
          $preparedData['data-'.$key] = $item;
        else
          $preparedData[$key] = $item;
      }
    }

    return $preparedData;
  }

  /**
   * @param string|array $text
   * @param $inCollection
   * @param $htmlOptions
   *
   * @return array|mixed
   */
  protected function prepareText($text, $inCollection, &$htmlOptions)
  {
    if( is_array($text) )
    {
      $linkText = Arr::reset($text);
      $this->appendHtmlOption($htmlOptions, htmlspecialchars(Arr::get($text, 0)), 'data-not-added-text');
      $this->appendHtmlOption($htmlOptions, htmlspecialchars(Arr::get($text, 1)), 'data-added-text');
    }
    else
      $linkText = $text;

    if( $inCollection )
    {
      if( is_array($text) )
        $linkText = is_array($text) ? Arr::get($text, 1) : $text;
    }

    return $linkText;
  }

  protected function init()
  {
    $this->replaceKeyCollection();

    Yii::app()->getClientScript()->attachEventHandler('onBeforeRenderClientScript', function($event){
      $this->registerScripts();
    });
  }

  protected function registerScripts()
  {
    $settings = array(
      'ajaxUrl' => $this->ajaxUrl,
      'ajaxUpdate' => $this->ajaxUpdate,
      'beforeAjaxScript' => $this->buildBeforeAjaxScript(),
      'afterAjaxScript' => $this->buildAfterAjaxScript(),
    );

    $script = "$.fn.collection('{$this->keyCollection}', ".CJavaScript::encode($settings).");";
    Yii::app()->clientScript->registerScript('initScript#'.Utils::ucfirst($this->keyCollection), $script, CClientScript::POS_END);

    $this->registerScriptButtonAdd();
    $this->registerScriptButtonRemove();
    $this->registerScriptInputAmountButton();
    $this->registerScriptInputAmountCollection();
    $this->registerScriptButtonAddThroughPopup();
  }

  protected function registerScriptButtonAdd()
  {
    $this->registerScript("$('body').on('click', '.{$this->classAdd}', function(e){
      e.preventDefault();

      if( $(this).data('do-not-add') == 1 || $(this).data('remove-toggle') == 1 )
        return;

      var classAlreadyInCollection = '{$this->classAlreadyInCollection}';
      var collection = $.fn.collection('{$this->keyCollection}');

      collection.send({
        'action' : 'add',
        'element' : $(this),
        'data' : $(this).data(),
        'url' : '{$this->addButtonAjaxUrl}',
        'afterAjaxScript' : function(element, data, action, response) {
          var elements = $.extend(
            {},
            collection.getElementsByData(data, '.{$this->classAdd}'),
            collection.getElementsByData(data, '.{$this->classAddThroughPopupButton}')
          );
          if( elements.length > 0 )
          {
            elements.each(function () {
              var e = $(this);

              if( e.hasClass(classAlreadyInCollection) )
                return;

              if( !e.hasClass(classAlreadyInCollection) )
                e.addClass(classAlreadyInCollection);

              if( e.data('do-not-add') == 0 )
                e.data('do-not-add', 1);

              if( e.data('remove-toggle') == 0 )
                e.data('remove-toggle', 1);

              if( e.data('added-text') )
                e.html(e.data('added-text'));
            });
          }
        }
      });
    });");
  }

  protected function registerScriptButtonRemove()
  {
    $this->registerScript("$('body, .{$this->classRemove}').on('click', '.{$this->classRemove}, .{$this->classAdd}[data-remove-toggle], .{$this->classAddThroughPopupButton}[data-remove-toggle]', function(e){
      e.preventDefault();

      if( $(this).data('remove-toggle') == 0 )
        return;

      var self = this;

      var makeAction = function()
      {
        var classAlreadyInCollection = '{$this->classAlreadyInCollection}';
        var collection = $.fn.collection('{$this->keyCollection}');

        collection.send({
          'element' : $(self),
          'action' : 'remove',
          'data' : $(self).data(),
          'afterAjaxScript' : function(element, data, action, response) {
            var elements = $.extend(
              {},
              collection.getElementsByData(data, '.{$this->classAdd}'),
              collection.getElementsByData(data, '.{$this->classAddThroughPopupButton}')
            );
            if( elements.length > 0 )
            {
              elements.each(function () {
                var e = $(this);

                if( !e.hasClass(classAlreadyInCollection) )
                  return;

                if( e.hasClass(classAlreadyInCollection) )
                  e.removeClass(classAlreadyInCollection);

                if( e.data('do-not-add') == 1 )
                  e.data('do-not-add', 0);

                if( e.data('remove-toggle') == 1 )
                  e.data('remove-toggle', 0);

                if( e.data('not-added-text') )
                  e.html(e.data('not-added-text'));
              });
            }
          }
        });
      }

      var confirmValue = $(this).data('confirm');

      if( confirmValue !== undefined )
      {
        alertify.confirm(confirmValue === true ? 'Вы уверены?' : confirmValue, function(answer) {
          if( answer )
          {
            makeAction();
          }
        });
      }
      else
      {
        makeAction();
      }
    });");
  }

  protected function registerScriptButtonAddThroughPopup()
  {
    $url = is_array($this->addThroughButtonAjaxUrl) ? CHtml::normalizeUrl($this->addThroughButtonAjaxUrl) : $this->addThroughButtonAjaxUrl;

    $this->registerScript("$(document.body).on('click', '.{$this->classAddThroughPopupButton}', function(e){
      e.preventDefault();

      if( $(this).data('do-not-add') == 1 || $(this).data('remove-toggle') == 1 ) return;

      $('.forwardElement').removeClass('forwardElement');
      $(this).addClass('forwardElement');

      $.post('{$url}', {'id' : $(this).data('id')}, function(resp) {
        var popup = $('#add-through-popup');
        popup.find('.add-through-popup-block').replaceWith('<div class=\"add-through-popup-block\">' + resp + '</div>');
        $.overlayLoader(true, popup);
        setTimeout(function() {
          popup.find('#{$this->popupAutofocusFieldId}').focus();
        }, 300);
      }, 'html');
    });");
  }

  protected function registerScriptInputAmountButton()
  {
    $this->registerScript("$('body').on('change', '.{$this->classAmount}', function(e){
      e.preventDefault();
      var targetSelector = $(this).data('target-selector');
      var buttonSelector = $(this).data('button-selector');
      if( buttonSelector )
      {
        var target = $(targetSelector + buttonSelector).length > 0 ? $(targetSelector + buttonSelector) : $(this).closest(targetSelector).find(buttonSelector);
      }
      else
      {
        var target = $(targetSelector);
      }

      target.data('amount', $(this).val() / $(this).data('step')).change();
    });");
  }

  protected function registerScriptInputAmountCollection()
  {
    $this->registerScript("$('body').on('change', '.{$this->classChangeAmount}', function(e){
      e.preventDefault();
      var data = $(this).data();
      data['amount'] = $(this).val() / $(this).data('step');

      $.fn.collection('{$this->keyCollection}').send({
          'action' : 'changeAmount',
          'data' : data,
        });
    });");
  }

  protected function buildBeforeAjaxScript()
  {
    return new CJavaScriptExpression("function(element, data, action){".
      implode("\n\r", $this->beforeAjaxScript).
      "return true;
    }");
  }

  protected function buildAfterAjaxScript()
  {
    return new CJavaScriptExpression("function(element, data, action, response){".implode("\n\r", $this->afterAjaxScript)."}");
  }

  protected function replaceKeyCollection()
  {
    foreach(get_object_vars($this) as $name => $value)
    {
      if( is_string($value) )
        $this->{$name} = strtr($value, array('{keyCollection}' => $this->keyCollection));
    }
  }

  /**
   * @param $htmlOptions
   * @param $value
   * @param string $key - по умолчанию 'class'
   */
  protected function appendHtmlOption(&$htmlOptions, $value, $key = 'class')
  {
    $htmlOptions[$key] = !isset($htmlOptions[$key]) ? $value : $htmlOptions[$key].' '.$value;
  }

  /**
   * @param $script
   * @param $position
   */
  protected function registerScript($script, $position = CClientScript::POS_END)
  {
    $calls = debug_backtrace(0, 2);
    Yii::app()->clientScript->registerScript($calls[1]['function'].'#'.$this->keyCollection, $script, $position);
  }
}