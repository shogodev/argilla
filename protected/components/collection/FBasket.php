<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FBasket extends FCollection
{
  public $classListView = '{collectionKey}-list';

  public $classAdd = 'to-{collectionKey}';

  public $classAlreadyInCollection = 'already-in-{collectionKey}';

  public $classForbidAddInCollection = 'forbid-add-{collectionKey}';

  public $classFastOrder = 'fast-order';

  protected $classRemove = 'remove-{collectionKey}';

  protected $classAmount = 'amount-{collectionKey}';

  protected $classChangeAmount = 'change-amount-{collectionKey}';

  protected $submitFunctionName;

  public function init()
  {
    foreach(get_object_vars($this) as $name => $value)
    {
      if( is_string($value) )
        $this->{$name} = strtr($value, array('{collectionKey}' => $this->keyCollection));
    }

    $this->submitFunctionName = 'send'.Utils::toCamelCase($this->keyCollection).'Data';

    $this->registerRemoveButtonScript();
    $this->registerAddButtonScript();
  }

  public function sum()
  {
    $sum = 0;

    foreach($this as $element)
      $sum += $element->sum;

    return $sum;
  }

  public function serviceSum()
  {
    $sum = 0;

    /**
     * @var FCollectionElement $element
     */
    foreach($this as $element)
    {
      if( $element->isEmptyCollectionItems('services') )
        continue;

      foreach($element->collectionItems['services'] as $service)
      {
        $service->setProduct($element);
        $sum += $service->sum * $element->getAmount();
      }
    }

    return $sum;
  }

  public function totalSum()
  {
    return $this->sum() + $this->serviceSum();
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

    $this->registerAmountScript();

    return CHtml::textField('amount', $amount, $htmlOptions);
  }

  /**
   * Строит кнопку добавления в коллекцию. Если установить data-amount, то amountInput будет менять количество
   * Пример: echo $this->basket->addButton('Добавить в корзину', array('class' => 'btn green-btn to-basket-btn', 'data-id' => $data->id, 'data-amount' => 1))
   * @param string $text
   * @param array $htmlOptions
   * @param array $defaultItems
   * @param $forbidAddAgainInCollection запретить добавлять в коллекцию одинаковый элемент несколько раз
   * @return string
   */
  public function addButton($text = '', $htmlOptions = array(), $defaultItems = array(), $forbidAddAgainInCollection = true)
  {
    $classes = isset($htmlOptions['class']) ? array($htmlOptions['class']) : array();
    $classes[] = $this->classAdd;
    $linkText = is_array($text) ? Arr::reset($text) : $text;

    if( $this->isInCollectionData($htmlOptions['data-type'], $htmlOptions['data-id']) )
    {
      $classes[] = $this->classAlreadyInCollection;

      if( $forbidAddAgainInCollection )
        $classes[] = $this->classForbidAddInCollection;

      if( is_array($text) )
        $linkText = is_array($text) ? Arr::get($text, 1) : $text;
    }

    if( !empty($defaultItems) )
      $htmlOptions['data-items'] = CJSON::encode($defaultItems);

    $htmlOptions['data-type'] = Utils::toSnakeCase($htmlOptions['data-type']);
    $htmlOptions['class'] = implode(' ', $classes);

    $this->registerAddButtonScript();

    return CHtml::link($linkText, '#', $htmlOptions);
  }

  public function addButtonModel($text = '', $model, $htmlOptions = array(), $defaultItems = array(), $forbidAddAgainInCollection = true)
  {
    if( empty($htmlOptions['data-id']) )
      $htmlOptions['data-id'] = $model->id;

    if( empty($htmlOptions['data-type']) )
      $htmlOptions['data-type'] = get_class($model);

    return $this->addButton($text, $htmlOptions, $defaultItems, $forbidAddAgainInCollection);
  }

  public function removeButton($element, $text = '', $htmlOptions = array(), $confirm = true)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classRemove :  $htmlOptions['class'].' '.$this->classRemove;

    $htmlOptions['data-id'] = $element->collectionExternalIndex;

    if( $confirm !== false)
      $htmlOptions['data-confirm'] = ($confirm === true ? 'true' : CJavaScript::quote($confirm));

    $this->registerRemoveButtonScript();

    return CHtml::link($text, '#', $htmlOptions);
  }

  public function changeAmountInput($element, $htmlOptions = array())
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classChangeAmount :  $htmlOptions['class'].' '.$this->classChangeAmount;

    if( empty($htmlOptions['data-id']) )
      $htmlOptions['data-id'] = $element->collectionExternalIndex;

    $this->registerChangeAmountScript();

    return CHtml::textField('amount', $element->collectionAmount, $htmlOptions);
  }

  public function fastOrderButton($value = '', $htmlOptions = array(), $formId)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classFastOrder :  $htmlOptions['class'].' '.$this->classFastOrder;

    $this->registerFastOrderScript($formId);

    return CHtml::button($value, $htmlOptions);
  }

  protected function registerAmountScript()
  {
    $script = "$('body').on('change', '.{$this->classAmount}', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      $('.{$this->classAdd}[data-amount][data-id=' + id + ']').data('amount', $(this).val());
    });";

    Yii::app()->clientScript->registerScript('AmountScript#'.$this->keyCollection, $script, CClientScript::POS_END);

    $this->registerSubmitScript();
  }

  protected function registerAddButtonScript()
  {
    $script = "$('body').on('click', '.{$this->classAdd}', function(e){
      e.preventDefault();

      if( $(this).hasClass('$this->classForbidAddInCollection') )
        return;

      if( !$(this).hasClass('waitAction') )
        $(this).addClass('waitAction');
      {$this->submitFunctionName}($(this).data(), 'add');
    });";

    Yii::app()->clientScript->registerScript('AddButtonScript#'.$this->keyCollection, $script, CClientScript::POS_END);

    $this->registerSubmitScript();
  }

  protected function registerChangeAmountScript()
  {
    $script = "$('body').on('change', '.{$this->classChangeAmount}', function(e){
      e.preventDefault();
      var data = $(this).data();
      data['amount'] = $(this).val();
      {$this->submitFunctionName}(data, 'changeAmount');
    });";

    Yii::app()->clientScript->registerScript('ChangeAmountScript#'.$this->keyCollection, $script, CClientScript::POS_END);
    $this->registerSubmitScript();
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

      {$this->submitFunctionName}($(this).data(), 'remove');
    });";

    Yii::app()->clientScript->registerScript('RemoveButtonScript#'.$this->keyCollection, $script, CClientScript::POS_END);
    $this->registerSubmitScript();
  }

  protected function registerSubmitScript()
  {
    $params = array(
      'data' => array(
        $this->keyCollection => new CJavaScriptExpression('data'),
        'action' =>  new CJavaScriptExpression('action')
      ),
    );

    $script = "var {$this->submitFunctionName} = function(data, action) {
      var list = $('.{$this->classListView}');
      if( list.length )
        list.yiiListView.update(list.attr('id'), ".CJavaScript::encode($params).");
    };";

    Yii::app()->clientScript->registerScript('SubmitScript#'.$this->keyCollection, $script, CClientScript::POS_END);
  }

  protected function registerFastOrderScript($formId)
  {
    $script = "$('body').on('click', '.{$this->classFastOrder}', function(e){
      e.preventDefault();
      var form = $('#{$formId}');
      var url = form.attr('action');
      var data = {'{$this->keyCollection}' : $(this).data(), 'action' : 'fastOrder'};
      data = $.param(data) + '&' + form.serialize();

      $.post(url, data, function(resp) {
        checkResponse(resp, form);
      }, 'json');
    });";

    Yii::app()->clientScript->registerScript('FastOrderScript', $script, CClientScript::POS_END);
  }
}