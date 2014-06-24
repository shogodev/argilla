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
  static $templateCounter = 0;

  public $classFastOrderButton = 'fast-order-{keyCollection}';

  public $classSubmitFastOrderButton = 'fast-order-submit-{keyCollection}';

  public $classRepeatOrderButton = 'repeat-order-{keyCollection}';

  public $fastOrderFormId = 'fast-order-form-{keyCollection}';

  public $fastOrderFormSuccessId = 'fast-order-form-success-{keyCollection}';

  protected $fastOrderFormPopupId = 'fast-order-popup-{keyCollection}';

  protected $fastOrderPopupContainer = 'div';

  protected $templates;

  protected $templateId = 'template-{keyCollection}-';

  public function serviceSum()
  {
    $sum = 0;

    /**
     * @var FCollectionElementBehavior $element
     */
    foreach($this as $element)
    {
      if( $element->collectionItems['services'] )
      {
        foreach($element->collectionItems['services'] as $service)
        {
          $service->setProduct($element);
          $sum += $service->sum * $element->getAmount();
        }
      }
    }

    return $sum;
  }

  public function totalSum()
  {
    return $this->sum() + $this->serviceSum();
  }

  /**
   * Строит кнопку для быстрого заказа
   * Пример:
   * <pre>
   *  $this->basket->buttonFastOrder(
   *    $model,
   *   'Купить в один клик',
   *   array('class' => 'red'),
   *   array(
   *     'name' => $data->name,
   *     'url' => $data->url,
   *     'img' => $image ? $image->pre : '',
   *     'description:selector' => '.parent-block .description'
   *  ));
   * </pre>
   * @param array|FCollectionElementBehavior|CActiveRecord $model
   * @param string $text текст кнопки
   * @param array $htmlOptions
   * @param array $formData массив данных которые будут подставлятся в попап быстрого заказа. Вместо данных можно использовать селектор для копирования содержимого блока. Формат задания селектора array('description:selector' => '.parent-block .description')
   *
   * @return string
   */
  public function buttonFastOrder($model, $text = '', $htmlOptions = array(), $formData = array())
  {
    $this->appendHtmlOption($htmlOptions, $this->classFastOrderButton);

    if( !empty($formData) )
      $this->appendHtmlOption($htmlOptions, CJSON::encode($formData), 'data-form-data');

    return CHtml::link($text, '#', CMap::mergeArray($this->prepareInputData($model), $htmlOptions));
  }

  public function buttonSubmitFastOrder($text = '', $htmlOptions = array())
  {
    $this->appendHtmlOption($htmlOptions, $this->classSubmitFastOrderButton);

    return CHtml::button($text, $htmlOptions);
  }

  public function buttonRepeatOrder($orderId, $text = '', $htmlOptions = array())
  {
    $this->appendHtmlOption($htmlOptions, $this->classRepeatOrderButton);
    $this->appendHtmlOption($htmlOptions, $orderId, 'data-order-id');

    return CHtml::link($text, '#', $htmlOptions);
  }

  public function beginFastOrderPopup($htmlOptions = array())
  {
    $this->appendHtmlOption($htmlOptions, $this->fastOrderFormPopupId, 'id');
    echo CHtml::tag($this->fastOrderPopupContainer, $htmlOptions, false, false);
  }

  public function endFastOrderPopup()
  {
    echo CHtml::closeTag($this->fastOrderPopupContainer);
  }

  public function beginTemplate($htmlOptions = array(), $tag = 'div')
  {
    $htmlOptions['id'] = empty($htmlOptions['id']) ? $this->templateId.self::$templateCounter++ : $htmlOptions['id'];
    $this->templates[$htmlOptions['id']] = '';

    echo CHtml::tag($tag, $htmlOptions, false, false);
    ob_start();
  }

  public function endTemplate($tag = 'div')
  {
    end($this->templates);
    $id = key($this->templates);
    $this->templates[$id] = trim(strtr(ob_get_contents(), array("\n" => '', "\r" => '')));
    ob_end_clean();

    echo CHtml::closeTag($tag);
  }

  protected function registerScripts()
  {
    parent::registerScripts();

    $this->registerScriptButtonFastOrder();
    $this->registerScriptButtonSubmitFastOrder();
    $this->registerScriptButtonRepeatOrder();
  }

  protected function registerScriptButtonFastOrder()
  {
    $this->registerScript("$('body').on('click', '.{$this->classFastOrderButton}', function(e){
      e.preventDefault();
      var templates = ".CJSON::encode($this->templates).";
      var element = $(this).clone();
      var data = element.data();

      if( data != undefined && data['formData'] != undefined )
      {
        for(templateId in templates)
        {
          if( templates.hasOwnProperty(templateId) )
          {
            var template = templates[templateId];

            for(key in data['formData'])
            {
              if( data['formData'].hasOwnProperty(key) )
              {
                var selectorKey = key.match(/(\w+):selector/)

                if( selectorKey )
                {
                  var replacedElement = $(data['formData'][key]);
                  if( replacedElement.length > 0 )
                    template = template.replace(new RegExp('{' + selectorKey[1] + '}', 'gi'), replacedElement.html());
                }
                else
                {
                  template = template.replace(new RegExp('{' + key + '}', 'gi'), data['formData'][key]);
                }
              }
            }

            $('#' + templateId).html(template);
          }
        }

        delete data['formData'];
      }

      var popupSelector = '#{$this->fastOrderFormPopupId}';
      $.overlayLoader(true, $(popupSelector));

      $('#{$this->fastOrderFormId}').show();
      $('#{$this->fastOrderFormSuccessId}').hide();

      var classSubmitButton = '{$this->classSubmitFastOrderButton}';
      $('.' + classSubmitButton).data(data);
    });");
  }

  protected function registerScriptButtonSubmitFastOrder()
  {
    $this->registerScript("$('body').on('click', '.{$this->classSubmitFastOrderButton}', function(e){
      e.preventDefault();

      var form = $('#{$this->fastOrderFormId}');
      var url = form.attr('action');
      var data = {'{$this->keyCollection}' : $(this).data(), 'action' : 'fastOrder'};

      $.post(url, $.param(data) + '&' + form.serialize(), function(resp) {
        checkResponse(resp, form);
      }, 'json');
    });");
  }

  protected function registerScriptButtonRepeatOrder()
  {
    $url = Yii::app()->controller->createUrl('basket/repeatOrder');

    $this->registerScript("$('body, .{$this->classRepeatOrderButton}').on('click', '.{$this->classRepeatOrderButton}', function(e){
      e.preventDefault();

      var collection = $.fn.collection('{$this->keyCollection}');

      collection.send({
        'url' : '{$url}',
        'data' : {'order-id' : $(this).data('order-id')}
      });
    });");
  }
}