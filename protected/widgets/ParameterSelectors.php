<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 11.10.12
 */

class ParameterSelectors extends CWidget
{
  public $type;

  public $parameter;

  public $currentValue;

  public function init()
  {
    $sessionParams = Yii::app()->session['product'];

    if( isset($sessionParams['selection']) )
      $this->currentValue = Arr::get($sessionParams['selection'], $this->parameter['id'], null);

    Yii::app()->clientScript->registerScript('selection', $this->getWidgetScripts(), CClientScript::POS_READY);
    Yii::app()->clientScript->registerScript('selection_sliders', $this->getSlidersScript(), CClientScript::POS_READY);
  }

  public function run()
  {
    switch($this->type)
    {
      case 'color':
        $this->buildColor();
        break;

      case 'country':
        $this->buildCountry();
        break;

      case 'waterproof':
        $this->buildWaterproof();
        break;

      case 'checkbox':
        $this->buildCheckbox();
        break;

      case 'select':
        $this->buildSelect();
        break;

      case 'price':
        $this->buildPrice();
        break;
    }
  }

  protected function buildColor()
  {
    echo '<div id="div_color" class="m20">';
    echo '<div class="m7 s11 bb" style="padding-left:5px">'.$this->parameter['name'].':</div>';
    echo '<div class="clearfix" id="colors">';

    foreach($this->parameter['variants'] as $variant)
    {
      echo '<div class="left">';
      echo '<input type="hidden" id="color-'.$variant['id'].'" name="selectionParam['.$this->parameter['id'].']['.$variant['id'].']" disabled="disabled" />';
      echo '<a href="#" class="color_item'.(!empty($this->currentValue[$variant['id']]) ? ' checked' : '').'" id="color-btn-'.$variant['id'].'"><span style="background: '.preg_replace("/.*\((#\w+)\)/", "$1", $variant['name']).';"></span></a>';
      echo '</div>';
    }

    echo '</div></div>';
  }

  protected function buildCountry()
  {
    echo '<div id="div_country" class="m5"><div class="clearfix">';

    foreach($this->parameter['variants'] as $variant)
    {
      echo '<div class="left country_cb" style="margin: 7px 13px 7px 0px;">';
      echo '<input '.(!empty($this->currentValue[$variant['id']]) ? 'checked ' : '').'type="checkbox" id="country-'.$variant['id'].'" name="selectionParam['.$this->parameter['id'].']['.$variant['id'].']" />';
      echo '<label for="country-'.$variant['id'].'"><img src="i/flags/'.$variant['id'].'.png" title="'.$variant['name'].'" /></label>';
      echo '</div>';
    }

    echo '</div></div>';
  }

  protected function buildWaterproof()
  {
    $min = Arr::get($this->currentValue, 'min', 0);
    $max = Arr::get($this->currentValue, 'max', 0);

    echo '<div id="div_waterproof" class="m20">';
    echo '<div class="m7 s11 bb" style="padding-left:5px">'.$this->parameter['name'].':</div>';
    echo '<div style="position:relative">';
    echo '<div class="slider-numbers m7">';
    echo '<span>от&nbsp;<input name="selectionParam['.$this->parameter['id'].'][min]" data-value="'.$min.'" value="'.$this->parameter['interval']['min'].'" type="text" class="inp param_min" style="width:25px" /></span>';
    echo '<span style="padding-left:50px">до&nbsp;<input name="selectionParam['.$this->parameter['id'].'][max]" value="'.$this->parameter['interval']['max'].'" data-value="'.$max.'"  type="text" class="inp param_max" style="width:25px" /></span>&nbsp;мм';
    echo '</div>';
    echo '<div class="slider-range" id="waterproofness"></div>';
    echo '</div>';
    echo '</div>';
  }

  protected function buildCheckbox()
  {
    echo '<div id="div_param_'.$this->parameter['id'].'" class="m5">';
    echo '<div class="m7 s11 bb" style="padding-left:5px">'.$this->parameter['name'].':</div>';
    echo '<div class="clearfix">';

    foreach($this->parameter['variants'] as $variant)
    {
      echo '<div class="left" style="margin:0 2px 7px 0; width: 100px;">';
      echo '<input '.(!empty($this->currentValue[$variant['id']]) ? 'checked ' : '').'type="checkbox" id="param-'.$variant['id'].'" name="selectionParam['.$this->parameter['id'].']['.$variant['id'].']" />&nbsp;';
      echo '<label for="param-'.$variant['id'].'>">'.$variant['name'].'</label>';
      echo '</div>';
    }

    echo '</div>';
    echo '</div>';
  }

  protected function buildSelect()
  {
    echo '<div class="m10" id="div_param_'.$this->parameter['id'].'">';
    echo '<div class="m7 s11 bb" style="padding-left:5px">'.$this->parameter['name'].':</div>';
    echo '<span class="selector"><span>';
    echo '<select name="selectionParam['.$this->parameter['id'].']">';
    echo '<option value="">Не важно</option>';

    foreach($this->parameter['variants'] as $variant)
      echo '<option '.($this->currentValue == $variant['id'] ? 'selected ' : '').'value="'.$variant['id'].'">'.$variant['name'].'</option>';

    echo '</select>';
    echo '</span></span>';
    echo '</div>';
  }

  protected function buildPrice()
  {
    $min = Arr::get($this->currentValue, 'min', 0);
    $max = Arr::get($this->currentValue, 'max', 0);

    echo '<div id="div_price" class="m30">';
    echo '<div class="m7 s11 bb" style="padding-left:5px">Цена:</div>';
    echo '<div style="position:relative">';
    echo '<div class="slider-numbers m7">';
    echo '<span>от&nbsp;<input name="selectionParam[price][min]" data-value="'.$min.'" type="text" class="inp param_min" style="width:54px;padding:3px 0;" /></span>';
    echo '<span style="padding-left:5px">до&nbsp;<input name="selectionParam[price][max]" data-value="'.$max.'" type="text" class="inp param_max" style="width:54px;padding:3px 0;" /></span>&nbsp;руб.';
    echo '</div>';
    echo '<div class="slider-range" id="price"></div>';
    echo '</div>';
    echo '</div>';
  }

  protected function getWidgetScripts()
  {
    return <<<SRC
// Выбор цветов
$('#colors a').on('click', function (e) {
  e.preventDefault();
  $(this).toggleClass('active');
  var key = $(this).attr('id').match(/color-btn-(\w+)/)[1];
  if ($('#color-' + key).val() == 'on')
    $('#color-' + key).val('').attr('disabled', 'disabled');
  else
    $('#color-' + key).val('on').removeAttr('disabled');
});
$('#colors a.checked').trigger('click');

// Закрытые списки
$('.filters .dotted').on('click', function (e) {
  e.preventDefault();
  $(this).parent().next().slideToggle();
});
$('#selection-submit').on('click', function (e) {
  e.preventDefault();
  $('#product-selection').submit();
});
SRC;
  }

 protected function getSlidersScript()
 {
  return <<<SRC
// Слайдеры водонепроницаемости и цены
var settings = {
  waterproofness:[0, 12, 1, [0, 12]],
  price:[0, 120000, 500, [1000, 45000]]
};
$('.slider-range').each(function ()
{
  var self = this,
          tid = $(this).attr('id');

  var min = $(this).parent().find('input.param_min');
  var max = $(this).parent().find('input.param_max');

  lim_min = (parseInt(min.val()) ? parseInt(min.val()) : 0);
  lim_max = (parseInt(max.val()) ? parseInt(max.val()) : 100000);

  cur_min = (parseInt(min.data('value')) ? parseInt(min.data('value')) : lim_min);
  cur_max = (parseInt(max.data('value')) ? parseInt(max.data('value')) : lim_max);

  $(this).slider({
    range:  true,
    min:    lim_min,
    max:    lim_max,
    step:   settings[tid][2],
    values: [cur_min, cur_max],

    create:function (e, ui) {
      $(e.target).prepend('<div class="ui-slider-image"><div class="ui-slider-image-left"></div></div>');
      $(e.target).find('a:first').addClass('ui-state-left');
    },
    slide:function (e, ui) {
      $(self).prev().find('.param_min').val(ui.values[0]);
      $(self).prev().find('.param_max').val(ui.values[1]);
    }
  });
  $(this).prev().find('.param_min').val($(this).slider('values', 0)).keyup(function () {
    $(self).slider('values', 0, $(this).val());
  });
  $(this).prev().find('.param_max').val($(this).slider('values', 1)).keyup(function () {
    $(self).slider('values', 1, $(this).val());
  });
});
SRC;
 }
}