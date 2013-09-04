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
  public $url;
  public $panelUrl;
  public $listViewClass = '{collectionKey}-list';

  public $classAdd = 'to-{collectionKey}';
  public $classInCollection = 'already-in-{collectionKey}';
  public $classFastOrder = 'fast-order';

  protected $classRemove = 'remove-{collectionKey}';
  protected $classAmount = 'amount-{collectionKey}';
  protected $classChangeAmount = 'change-amount-{collectionKey}';

  protected $submitFunctionName;

  public function init()
  {
    $controller = Yii::app()->controller;
    $this->url = $controller->createUrl('basket/index');
    $this->panelUrl = $controller->createUrl('basket/panel');

    $this->listViewClass = strtr($this->listViewClass, array('{collectionKey}' => $this->keyCollection));
    $this->classAdd = strtr($this->classAdd, array('{collectionKey}' => $this->keyCollection));
    $this->classInCollection = strtr($this->classInCollection, array('{collectionKey}' => $this->keyCollection));
    $this->classRemove = strtr($this->classRemove, array('{collectionKey}' => $this->keyCollection));
    $this->classAmount = strtr($this->classAmount, array('{collectionKey}' => $this->keyCollection));
    $this->classChangeAmount = strtr($this->classChangeAmount, array('{collectionKey}' => $this->keyCollection));

    $this->submitFunctionName = 'submitData'.Utils::ucfirst($this->keyCollection);

	  $this->registerRemoveButtonScript();
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

    foreach($this as $element)
    {
      if( isset($element->collectionItems['service']) )
        foreach($element->collectionItems['service'] as $service)
          $sum += $service->sum;
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
   * @param $checkInCollection добавлять в коллекцию 1 раз
   * @return string
   */
  public function addButton($text = '', $htmlOptions = array(), $checkInCollection = true)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classAdd :  $htmlOptions['class'].' '.$this->classAdd;

    if( empty($htmlOptions['data-type']) )
      $htmlOptions['data-type'] = 'product';

    if( $this->isInCollectionData($htmlOptions['data-type'], $htmlOptions['data-id']) )
      $htmlOptions['class'] .= ' '.$this->classInCollection;

    $this->registerAddButtonScript($checkInCollection);

    return CHtml::link($text, '#', $htmlOptions);
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

  protected function registerAddButtonScript($checkInCollection)
  {
    if( $checkInCollection )
      $script = "$('body').on('click', '.{$this->classAdd}:not(.{$this->classInCollection})', function(e){";
    else
      $script = "$('body').on('click', '.{$this->classAdd}', function(e){";

    $script .= "
      e.preventDefault();
      if( !$(this).hasClass('waitAction') )
        $(this).addClass('waitAction');
      {$this->submitFunctionName}($(this).data(), 'add');
    });
    $('body').on('click', '.{$this->classAdd}.{$this->classInCollection}', function(e){
      e.preventDefault();
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

    // key коды  http://www.hiox.org/32037-javascript-char-codes-key-codes.php
/*    $keyScript = "$('body').on('keydown','.{$this->classChangeAmount}',function(e){

      $(this).data('old-value', this.value) ;

      var keyCode = e.keyCode;

      if( (keyCode >= 48 && keyCode <= 57) // 0 - 9
          || (keyCode >= 96 && keyCode <= 105) // 0 - 9 numpad
          || keyCode == 8 // backspace
          || keyCode == 46 // delete
          || keyCode == 37 // left arrow
          || keyCode == 39 // right arrow
          || keyCode == 35 // end
          || keyCode == 36 // home
          || keyCode == 45  // insert
         ) return true;

       e.preventDefault();
       return false;
    });

    $('body').on('keyup','.{$this->classChangeAmount}',function(e){
      if( this.value == '' || $(this).data('old-value') == this.value )
        return;
       }
    );";*/

    //Yii::app()->clientScript->registerScript('ChangeAmountKeyboardScript', $keyScript, CClientScript::POS_END);

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
      var list = $('.{$this->listViewClass}');
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