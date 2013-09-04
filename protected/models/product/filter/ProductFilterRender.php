<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
        return action[0] + '?' + data;
      };

      var submitForm_{$this->parent->filterKey} = function(data, url)
      {
        url = decodeURIComponent(url ? url : buildUrl($('{$form}'), data));

        var ajaxUpdate = '{$ajaxUpdate}' && window.History.enabled;
        var changeUrl  = url && url.match(/\?/) ? false : true;

        if( ajaxUpdate && changeUrl )
        {
          window.History.pushState(null, document.title, url);
        }
        else
        {
          if( changeUrl )
            document.location.href = url;
          else
            $('#yw0').yiiListView.update('yw0', {'url' : url});
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

    $script = "$('body').on('click', '.removeElement', function(e) {
      e.preventDefault();

      var id = $(this).data('remove');

      if( $('#' + id).length )
      {
        $('#' + id).click();
      }
      else
      {
        var form = $('{$form}');
        var data = $(this).attr('name') + '=' + $(this).val() + '&{$this->parent->filterKey}[remove]=1';

        $.post(form.attr('action'), data, function(resp) {
          if(typeof {$this->finishCallback} == 'function')
          {
            {$this->finishCallback}(resp);
          }
          else
           location.reload();
        }, 'html');
      }
    });";

    Yii::app()->clientScript->registerScript('removeElementsScript_'.$this->parent->filterKey, $script, CClientScript::POS_LOAD);
  }
}