<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 *
 * Пример:
 * <div>
 * 'relatedFormElements' => array(
 *   'class' => 'frontend.models.behaviors.RelatedFormElements',
 *   'rules' => array(
 *      array('show', 'delivery_id', 'address', 'values' => array(1, 2, 4)),
 *      array('show', 'delivery_id', 'addressMap', 'values' => array(3))
 *   )
 * )
 */
class RelatedFormElements extends CActiveRecordBehavior
{
  public $rules = array();

  public function registerInitScript($formId)
  {
    $jsRules = $this->getJavaScriptRules($formId, $this->rules);

    $script = "
      $(function() {
        checkRules(".CJavaScript::jsonEncode($jsRules).");
      });";

    $clientScript = Yii::app()->clientScript;
    $clientScript->registerScript($formId.'#init', $script, CClientScript::POS_READY);
  }

  public function registerScriptByElement(FFormInputElement $element)
  {
    if( $rules = $this->getRulesByAttribute($element->name) )
    {
      $this->registerCommonScript();

      /**
       * @var CActiveForm $activeForm
       */
      $activeForm = $element->getParent()->getActiveFormWidget();
      $formId = $activeForm->id;

      $jsRules = $this->getJavaScriptRules($formId, $rules);

      $selector = $this->getSelector($formId, $element->name);
      $script = "$('{$selector}').on('change', function() {
        checkRules(".CJavaScript::jsonEncode($jsRules).");
      });";

      $clientScript = Yii::app()->clientScript;
      $clientScript->registerScript($formId.'#'.$element->name, $script, CClientScript::POS_READY);

      $this->registerInitScript($formId);
    }
  }

  private function registerCommonScript()
  {
    $script = "
      var checkRules = function(rules)
      {
        for(i in rules)
        {
          checkRule(rules[i]);
        }
      }

      var checkRule = function(rule)
      {
        //debugger;
        if( rule.action == 'show' )
        {
          if( checkValues(rule.srcSelector, rule.values) )
          {
            $(rule.destSelector).parent().parent().show();
          }
          else
          {
            $(rule.destSelector).val('');
            $(rule.destSelector).parent().parent().hide();
          }
        }
      }

      var checkValues = function(selector, values)
      {
        var value = getValue(selector);

        for(i in values)
        {
          if( value == values[i] )
            return true;
        }

        return false;
      }

      var getValue = function(selector)
      {
        var element = $(selector + '[type!=hidden]');
        var value = '';

        switch (element.attr('type'))
        {
          case 'radio':
            value = $(selector + ':checked').val();
          break;

          default:
            value = $(selector).val();
          break;
        }

        return value;
      }";

    $clientScript = Yii::app()->clientScript;
    $clientScript->registerScript('relatedFormElementsScript', $script, CClientScript::POS_READY);
  }

  private function getSelector($formId, $attribute, $variable = false)
  {
    $name = $this->normalizeSelector(CHtml::resolveName($this->owner, $attribute), $variable);

    return "form#{$formId} [name={$name}]";
  }

  private function normalizeSelector($selector, $variable)
  {
    $escape = $variable ? "\\" : "\\\\";

    return strtr($selector, array('[' => $escape.'[', ']' => $escape.']'));
  }

  private function getRulesByAttribute($attribute)
  {
    $rules = $this->rules;

    array_walk($rules, function($element, $key) use(&$rules, $attribute) {
      if( Arr::get($element, 1) != $attribute )
        unset($rules[$key]);
    });

    return $rules;
  }

  private function getJavaScriptRule($formId, $rule)
  {
    $ruleName = $rule[0];
    $sourceAttribute = $rule[1];
    $destAttribute = $rule[2];

    return array(
      'action' => $ruleName,
      'srcSelector' => $selector = $this->getSelector($formId, $sourceAttribute, true),
      'destSelector' => $selector = $this->getSelector($formId, $destAttribute, true),
      'values' => $rule['values']
    );
  }

  private function getJavaScriptRules($formId, $rules)
  {
    $jsRules = array();

    foreach($this->rules as $rule)
    {
      $jsRules[] = $this->getJavaScriptRule($formId, $rule);
    }

    return $jsRules;
  }
}