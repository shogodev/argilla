/**
 * Created by tatarinov on 09.10.14.
 *
 * rules : {
 *  {
 *    'action' : 'show',
 *    'des' : 'Model[attribute]',
 *    'src' : 'Model[attribute]',
 *    'srcValues' : [1, 2, 5,undefined] или 'srcValues' : 1
 *  },
 *  {
 *    'action' : 'call',
 *    'callback' : function(element, value) {}),
 *    'src' : 'Model[attribute]',
 *  }
 *  если в srcValues написать undefined, при отсутствии поля будет воспринято как true
 */
;$.widget('argilla.relatedFields', {

  options : {
    rules : {}
  },

  bind : {},

  _create: function() {
    var rules = this.options.rules;
    for(var i in rules)
    {
      if( rules[i].action == 'show' && !$.isArray(rules[i].srcValues) ) {
        var value = rules[i].srcValues;
        rules[i].srcValues = [];
        rules[i].srcValues.push(value);
      }

      this._bind(rules[i].src);
      this._checkRule(rules[i]);
    }
  },

  _check : function(src) {
    var rules = this._findRulesBySource(src);
    for(var i in rules)
    {
      this._checkRule(rules[i]);
    }
  },

  _findRulesByDestination : function(dest) {
    var rules = [];

    for(var i in this.options.rules)
    {
      if( this.options.rules[i].dest == dest )
        rules.push(this.options.rules[i]);
    }

    return rules;
  },

  _findRulesBySource : function(src) {
    var rules = [];

    for(var i in this.options.rules)
    {
      if( this.options.rules[i].src == src )
        rules.push(this.options.rules[i]);
    }

    return rules;
  },

  _bind : function(nameElement) {
    if( this.bind[nameElement] )
      return;

    this.bind[nameElement] = nameElement;

    var self = this;
    this._getElementByName(nameElement).bind('change', function () {
      self._check($(this).attr('name'));
    });
  },

  _getSelectorByName : function(name){
    return '[name=' + name.replace('[', '\\[').replace(']', '\\]') + ']';
  },

  _getElementByName :function(name) {
    return this.element.find(this._getSelectorByName(name));
  },

  _checkRule : function(rule)
  {
    switch (rule.action)
    {
      case 'show':
        var destRules = this._findRulesByDestination(rule.dest);
        var equals = false;
        for(var i in destRules)
        {
          if( this._checkValues(destRules[i].src, destRules[i].srcValues) )
          {
            equals = true;
          }
          else
          {
            equals = false;
            break;
          }
        }

        var destElement = this._getElementByName(rule.dest);
        if( equals ) {
          destElement.parent().parent().show();
        }
        else {
          this._clearSelectedElements(destElement);
          destElement.parent().parent().hide();
        }
      break;

      case 'call':
        rule.callback(this._getElementByName(rule.src), this._getValue(rule.src));
      break;
    }
  },

  _clearSelectedElements : function(elements) {
    var self = this;
    elements.each(function () {
      self._clearSelectedElement($(this));
    });
  },

  _clearSelectedElement : function(element) {
    if( element.prop('tagName') == 'INPUT' ) {
      switch (element.attr('type'))
      {
        case 'text':
          element.val('').change();
        break;

        case 'checkbox':
        case 'radio':
          element.prop('checked', false).change();
        break;
      }
    }
    else if( element.prop('tagName') == 'SELECT' ) {
      element.prop('selectedIndex', 0).change();
    }
  },

  _checkValues : function(selector, values)
  {
    var value = this._getValue(selector);

    for(i in values)
    {
      if( value == values[i] )
        return true;
    }

    return false;
  },

  _getValue : function(name) {
    var selector = this._getSelectorByName(name);
    var element = this.element.find(selector + '[type!=hidden]');
    var value = '';

    switch (element.attr('type'))
    {
      case 'radio':
        value = this.element.find(selector + ':checked').val();
      break;

      default:
        value = this.element.find(selector).val();
      break;
    }

    return value;
  },

  destroy: function() {
    $.Widget.prototype.destroy.call(this);
  }
});





