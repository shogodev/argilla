<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FBasket extends FCollectionUI
{
  public $classFastOrder = 'fast-order';

  public function serviceSum()
  {
    $sum = 0;

    /**
     * @var FCollectionElement $element
     */
    foreach($this as $element)
    {
      if( !$element->isNotEmptyCollection('services') )
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

  public function fastOrderButton($value = '', $htmlOptions = array(), $formId)
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classFastOrder :  $htmlOptions['class'].' '.$this->classFastOrder;

    $this->registerFastOrderScript($formId);

    return CHtml::button($value, $htmlOptions);
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