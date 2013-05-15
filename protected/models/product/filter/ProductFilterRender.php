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

  public function begin()
  {
    $htmlOption = array(
      'name' => $this->parent->filterKey,
      'autocomplete' => 'off'
    );

    return CHtml::form(Yii::app()->controller->getCurrentUrl(), null, $htmlOption);
  }

  public function end()
  {
    return CHtml::endForm();
  }

  public function getFinishCallback()
  {
    return 'callback_'.$this->parent->filterKey;
  }

  public function registerOnChangeScript($live = true)
  {
    $form = "form[name={$this->parent->filterKey}]";

    if( $live )
      $script = "$('body').on('change', '{$form} select, {$form} input', function(e) {";
    else
      $script = "$('{$form} select, {$form} input').on('change', function(e) {";

    $script .= "
        var form = $('{$form}');
        submitForm_{$this->parent->filterKey}(form.serialize());
      });

      var submitForm_{$this->parent->filterKey} = function(data)
      {
        var form = $('{$form}');
        data += '&{$this->parent->filterKey}[submit]=1';

        $.post(form.attr('action'), data, function(resp) {
          if(typeof {$this->finishCallback} == 'function')
          {
            {$this->finishCallback}(resp);
          }
          else
           location.reload();
      }, 'html');
    }
    ";

    Yii::app()->clientScript->registerScript('onChangeScript_'.$this->parent->filterKey, $script, CClientScript::POS_LOAD);
  }

  public function registerRemoveElementsScript()
  {
    $form = "form[name={$this->parent->filterKey}]";

    $script = "$('body').on('change', '.removeElement', function(e) {
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

      });
    ";

    Yii::app()->clientScript->registerScript('removeElementsScript_'.$this->parent->filterKey, $script, CClientScript::POS_LOAD);
  }
}