<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $finishCallback
 */
class ProductFilterRender extends CComponent
{
  /**
   * @var ProductFilter $parent
   */
  protected $parent;

  public function __construct($parent)
  {
    $this->parent = $parent;
  }

  public function begin($action = '')
  {
    $htmlOption = array(
      'name' => $this->parent->filterKey,
      'autocomplete' => 'off'
    );

    if( empty($action) )
      $action = Yii::app()->controller->getCurrentUrl();

    return CHtml::form($action, 'post', $htmlOption);
  }

  public function submitProperty()
  {
    return CHtml::hiddenField($this->parent->filterKey.'[submit]', 1);
  }

  public function end()
  {
    return CHtml::endForm();
  }

  public function getFinishCallback()
  {
    return 'callback_'.$this->parent->filterKey;
  }

  public function registerOnChangeScript($ajaxUpdate = false)
  {
    $ajaxUpdate = intval($ajaxUpdate);

    $form   = "form[name={$this->parent->filterKey}]";
    $script = "
      $('body').on('change', '{$form} select, {$form} input', function(e)
      {
        var form = $('{$form}');
        submitForm_{$this->parent->filterKey}(form.serialize(), $(this).data('url'));
      });

      var buildUrl = function(form, data)
      {
        data += (data ? '&' : '') + encodeURIComponent('{$this->parent->filterKey}[submit]') + '=1';
        var action = form.attr('action').split('?');
        return [action[0], decodeURIComponent(data)];
      };

      var submitForm_{$this->parent->filterKey} = function(data, url)
      {
        url = url ? [url, ''] : buildUrl($('{$form}'), data);
        var ajaxUpdate = {$ajaxUpdate} && window.History.enabled;

        if( ajaxUpdate )
        {
          window.History.pushState($.deparam(url[1]), document.title, url[0]);
        }
        else
        {
          $('#product_list').yiiListView.update('product_list', {'url' : url[0], 'data' : $.deparam(url[1])});
        }
      };

      $('body').on('submit', '{$form}', function(e) {
        e.preventDefault();
        submitForm_{$this->parent->filterKey}($(this).serialize());
      });

      $('body').on('click', 'a#clearFilter', function(e) {
        e.preventDefault();
        submitForm_{$this->parent->filterKey}('');
      });
    ";

    Yii::app()->clientScript->registerScript('onChangeScript_'.$this->parent->filterKey, $script, CClientScript::POS_LOAD);
  }

  public function registerRemoveElementsScript()
  {
    $form = "form[name={$this->parent->filterKey}]";

    $script = "$('body').on('click', '.remove-btn', function(e) {
      e.preventDefault();
      element = $(this);
      var id = element.data('remove');

      if( $('#' + id).length && $('#' + id).attr('type') !== 'hidden' )
      {
        $('#' + id).click();
      }
      else
      {
        var form = $('{$form}');
        form.find('#' + element.data('remove')).val('');
        form.submit();
      }
    });";

    Yii::app()->clientScript->registerScript('removeElementsScript_'.$this->parent->filterKey, $script, CClientScript::POS_READY);
  }
}