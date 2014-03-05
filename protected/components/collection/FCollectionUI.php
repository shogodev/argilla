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
  public $classAdd = 'to-{keyCollection}';

  public $classAlreadyInCollection = 'already-in-{keyCollection}';

  public $classDoNotAddInCollection = 'do-not-add-{keyCollection}';

  public $classWaitAction = 'process{keyCollection}';

  public $ajaxUrl;

  public $addButtonAjaxUrl;

  public $ajaxUpdate = array();

  public $beforeAjaxScript = array();

  public $afterAjaxScript = array();

  protected $classRemove = 'remove-{keyCollection}';

  protected $classAmount = 'amount-{keyCollection}';

  protected $classChangeAmount = 'change-amount-{keyCollection}';

  public function init()
  {
    $this->replaceKeyCollection();

    Yii::app()->getClientScript()->attachEventHandler('onBeforeRenderClientScript', function($event){
      $this->registerScripts();
    });
  }

  public function sum()
  {
    $sum = 0;

    foreach($this as $element)
      $sum += $element->sum;

    return $sum;
  }

  public function totalSum()
  {
    return $this->sum();
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
   * Строит кнопку добавления в коллекцию. Если установить data-amount, то amountInput будет менять количество
   * Пример: echo $this->basket->addButton('Добавить в корзину', array('class' => 'btn green-btn to-basket-btn', 'data-id' => $data->id, 'data-amount' => 1))
   * @param string|array $text - 'текст кнопки' или массим array('текст кнопки', 'текст активной кнопки')
   * @param array $htmlOptions
   * @param array $defaultItems
   * @param boolean $doNotAddInCollection запретить добавлять в коллекцию одинаковый элемент несколько раз
   *
   * @return string
   */
  public function addButton($text = '', $htmlOptions = array(), $defaultItems = array(), $doNotAddInCollection = true)
  {
    $classes = isset($htmlOptions['class']) ? array($htmlOptions['class']) : array();
    $classes[] = $this->classAdd;
    if( is_array($text) )
    {
      $linkText =  Arr::reset($text);
      $htmlOptions['data-not-added-text'] = htmlspecialchars(Arr::get($text, 0));
      $htmlOptions['data-added-text'] = htmlspecialchars(Arr::get($text, 1));
    }
    else
      $linkText = $text;

    if( $doNotAddInCollection )
      $htmlOptions['data-do-not-add'] = 1;

    if( $this->isInCollectionData($htmlOptions['data-type'], $htmlOptions['data-id']) )
    {
      $classes[] = $this->classAlreadyInCollection;

      if( $doNotAddInCollection )
        $classes[] = $this->classDoNotAddInCollection;

      if( is_array($text) )
        $linkText = is_array($text) ? Arr::get($text, 1) : $text;
    }

    if( !empty($defaultItems) )
      $htmlOptions['data-items'] = CJSON::encode($defaultItems);

    $htmlOptions['data-type'] = Utils::toSnakeCase($htmlOptions['data-type']);
    $htmlOptions['class'] = implode(' ', $classes);

    return CHtml::link($linkText, '#', $htmlOptions);
  }

  public function addButtonModel($text = '', $model, $htmlOptions = array(), $defaultItems = array(), $doNotAddInCollection = true)
  {
    if( empty($htmlOptions['data-id']) )
      $htmlOptions['data-id'] = $model->id;

    if( empty($htmlOptions['data-type']) )
      $htmlOptions['data-type'] = get_class($model);

    return $this->addButton($text, $htmlOptions, $defaultItems, $doNotAddInCollection);
  }

  public function removeButton($element, $text = '', $htmlOptions = array(), $confirm = true)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classRemove :  $htmlOptions['class'].' '.$this->classRemove;

    $htmlOptions['data-id'] = $element->collectionExternalIndex;

    if( $confirm !== false)
      $htmlOptions['data-confirm'] = ($confirm === true ? 'true' : CJavaScript::quote($confirm));

    return CHtml::link($text, '#', $htmlOptions);
  }

  public function changeAmountInput($element, $htmlOptions = array())
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classChangeAmount :  $htmlOptions['class'].' '.$this->classChangeAmount;

    if( empty($htmlOptions['data-id']) )
      $htmlOptions['data-id'] = $element->collectionExternalIndex;

    return CHtml::textField('amount', $element->collectionAmount, $htmlOptions);
  }

  /**
   * Строит input меняющий количество на кнопке добавления в коллекцию. Менят количество только у тех инпутов у которых установлено значение data-amount.
   * Пример: echo $this->basket->amountInput(array('data-id' => $data->id))
   * @param array $htmlOptions
   * @param int $amount количество
   * @return string
   */
  public function amountInput($htmlOptions = array(), $amount = 1)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classAmount :  $htmlOptions['class'].' '.$this->classAmount;

    return CHtml::textField('amount', $amount, $htmlOptions);
  }

  protected function registerScripts()
  {
    $settings = array(
      'ajaxUrl' => $this->ajaxUrl,
      'ajaxUpdate' => $this->ajaxUpdate,
      'beforeAjaxScript' => $this->buildBeforeAjaxScript(),
      'afterAjaxScript' => $this->buildAfterAjaxScript(),
      'classWaitAction' => $this->classWaitAction,
    );

    $script = "$.fn.collection('{$this->keyCollection}', ".CJavaScript::encode($settings).");";
    Yii::app()->clientScript->registerScript('initScript#'.Utils::ucfirst($this->keyCollection), $script, CClientScript::POS_END);

    $this->registerAddButtonScript();
    $this->registerChangeAmountScript();
    $this->registerAmountScript();
    $this->registerRemoveButtonScript();
  }

  protected function registerAddButtonScript()
  {
    $script = "$('body').on('click', '.{$this->classAdd}', function(e){
      e.preventDefault();

      var classDoNotAddInCollection = '{$this->classDoNotAddInCollection}';
      if( $(this).hasClass(classDoNotAddInCollection) )
        return;

      var classWaitAction = '{$this->classWaitAction}';
      var classAlreadyInCollection = '{$this->classAlreadyInCollection}';

      $.fn.collection('{$this->keyCollection}').send({
        'action' : 'add',
        'element' : $(this),
        'data' : $(this).data(),
        'url' : '{$this->addButtonAjaxUrl}',
        'beforeAjaxScript' : function(element, data, action) {
          if( element.length > 0 && !element.hasClass(classWaitAction) )
            element.addClass(classWaitAction);
        },
        'afterAjaxScript' : function(element, data, action, response) {
          var waitActionElement = $('.' + classWaitAction);
          if( waitActionElement.length > 0 )
          {
            waitActionElement.removeClass(classWaitAction);
            if( !waitActionElement.hasClass(classAlreadyInCollection) )
              waitActionElement.addClass(classAlreadyInCollection);
            if( waitActionElement.data('do-not-add') && !waitActionElement.hasClass(classDoNotAddInCollection) )
              waitActionElement.addClass(classDoNotAddInCollection);
            if( waitActionElement.data('added-text') )
               waitActionElement.text(waitActionElement.data('added-text'));
          }
        }
      });
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
  }

  protected function registerAmountScript()
  {
    $script = "$('body').on('change', '.{$this->classAmount}', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      $('.{$this->classAdd}[data-amount][data-id=' + id + ']').data('amount', $(this).val());
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
  }

  protected function registerChangeAmountScript()
  {
    $script = "$('body').on('change', '.{$this->classChangeAmount}', function(e){
      e.preventDefault();
      var data = $(this).data();
      data['amount'] = $(this).val();

      $.fn.collection('{$this->keyCollection}').send({
          'action' : 'changeAmount',
          'data' : data,
        });
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
  }

  protected function registerRemoveButtonScript()
  {
    $script = "$('body, .{$this->classRemove}').on('click', '.{$this->classRemove}', function(e){
      e.preventDefault();

      var confirmValue = $(this).data('confirm');

      if( confirmValue !== undefined )
      {
        if( !confirm(confirmValue === true ? 'Вы уверены?' : confirmValue) )
          return;
      }

      $.fn.collection('{$this->keyCollection}').send({
          'action' : 'remove',
          'data' : $(this).data(),
        });
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
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
}