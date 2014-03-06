<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FBasket extends FCollectionUI
{
  public $classFastOrderButton = 'fast-order-{keyCollection}';

  public $classSubmitFastOrderButton = 'fast-order-submit-{keyCollection}';

  public $fastOrderFormId = 'fast-order-form-{keyCollection}';

  public $fastOrderFormSuccessId = 'fast-order-form-success-{keyCollection}';

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

  public function fastOrderButton($text = '', $model = null, $htmlOptions = array())
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classFastOrderButton : $htmlOptions['class'].' '.$this->classFastOrderButton;

    if( empty($htmlOptions['data-id']) && $model )
      $htmlOptions['data-id'] = $model->id;

    if( empty($htmlOptions['data-type']) && $model )
      $htmlOptions['data-type'] = get_class($model);

    return CHtml::link($text, '#', $htmlOptions);
  }

  public function submitFastOrderButton($text = '', $htmlOptions = array())
  {
    $htmlOptions['class'] = empty($htmlOptions['class']) ? $this->classSubmitFastOrderButton : $htmlOptions['class'].' '.$this->classSubmitFastOrderButton;

    return CHtml::button($text, $htmlOptions);
  }

  protected function registerScripts()
  {
    parent::registerScripts();

    $this->registerFastOrderButtonScript();
    $this->registerSubmitFastOrderButtonScript();
  }

  protected function registerFastOrderButtonScript()
  {
    $script = "$('body').on('click', '.{$this->classFastOrderButton}', function(e){
      e.preventDefault();

      $('#{$this->fastOrderFormId}').show();
      $('#{$this->fastOrderFormSuccessId}').hide();

      var classSubmitButton = '{$this->classSubmitFastOrderButton}';
      $('.' + classSubmitButton).data($(this).data());
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
  }

  protected function registerSubmitFastOrderButtonScript()
  {
    $script = "$('body').on('click', '.{$this->classSubmitFastOrderButton}', function(e){
      e.preventDefault();

      var form = $('#{$this->fastOrderFormId}');
      var url = form.attr('action');
      var data = {'{$this->keyCollection}' : $(this).data(), 'action' : 'fastOrder'};

      $.post(url, $.param(data) + '&' + form.serialize(), function(resp) {
        checkResponse(resp, form);
      }, 'json');
    });";

    Yii::app()->clientScript->registerScript(__METHOD__.'#'.$this->keyCollection, $script, CClientScript::POS_END);
  }
}