/**
 * Created by tatarinov on 02.10.14.
 *
 *  Пример:
 *
 *  <div class="product-block">
 *    <div class="price-old-block">
 *      <span class="old-price grey dynamic-price-old" data-price-old="1000">1000</span>
 *      <span class="economy dynamic-economy">- 100</span>
 *    </div>
 *    <input type="text" value="1" class="inp amount-input">
 *    <span class="s34 bb dynamic-price _price_element" data-price="900">900</span>
 *  </div>
 *  <script>
 *   $(function() {
 *      $('.product-block .dynamic-price').calcPrice( {
 *        parentBlockSelector : '.product-block',
 *        amountSelector : '.amount-input',
 *        priceOld : {
 *          parentBlockSelector : '.price-old-block',
 *          elementSelector : '.dynamic-price-old'
 *        },
 *        economy : {elementSelector : '.dynamic-economy'}
 *      });
 *    });
 *  </script>
 *
 *  Можно пересчитать цену вызвав событие change:
 *  $('.product-block .dynamic-price').trigger('change');
 */
;$.widget('argilla.calcPrice', {
  options : {
    parentBlockSelector : null,
    priceComponentClass : '_price_component',
    price : {
      elementSelector : null,
      dataKey : 'price',
      newDataKey : null,
      suffix : ' руб.'
    },
    priceOld : {
      parentBlockSelector : null,
      elementSelector : null,
      dataKey : 'price-old',
      suffix : ' руб.'
    },
    economy : {
      parentBlockSelector : null,
      elementSelector : null,
      prefix : '- ',
      suffix : ' руб.'
    },
    amountSelector : null
  },

  _create: function() {
    var options = this.options;
    var parentElement = $(options.parentBlockSelector);

    options.price['element'] = options.price.elementSelector === null ? this.element : parentElement.find(options.price.elementSelector);
    options.amount = parentElement.find(options.amountSelector);
    if (options.amount.length == 0)
      options.amount = $('<input value=1 style="display: none" type="hidden">');
    options.priceOld['element'] = parentElement.find( options.priceOld.elementSelector);
    options.economy['element'] = parentElement.find( options.economy.elementSelector);

    var widget = this;
    options.amount.bind('change', function(e, changeAmount) {
      widget._calc(parentElement, options);
      if( changeAmount !== false )
        parentElement.trigger('amountChange', $(this).val());
    });

    options.price.element.bind('change', function() {
      widget._calc(parentElement, options);
    });
  },

  _calc: function(parentElement, options) {
    var price = this._calcPrice(parentElement, options.price, options.amount, options.priceComponentClass);

    if( options.priceOld['element'].length > 0 )
      var priceOld = this._calcPriceOld(parentElement, options.priceOld, options.amount, price, options.priceComponentClass);

    if( options.economy['element'].length > 0 )
      var economy = this._calcEconomy(price, priceOld, parentElement, options.economy);

    $(options.price['element']).trigger('endCalc', {'price' : price, 'priceOld' : priceOld, 'economy' : economy, 'amount' : options.amount.val()});
  },

  _calcPrice: function(parentElement, options, amountElement, priceComponentClass) {
    var price = this._getComponentsPrice(parentElement, options.dataKey, priceComponentClass);

    if( price == 0 && options.element.data(options.dataKey) > 0 )
      price = options.element.data(options.dataKey);

    var sumTotal = price * amountElement.val();
    options.element.text(number_format(sumTotal) + options.suffix);
    if( options.newDataKey )
      this.element.data(options.newDataKey, sumTotal);

    return sumTotal;
  },

  _calcPriceOld: function(parentElement, options, amountElement, newPrice, priceComponentClass) {
    var price = this._getComponentsPrice(parentElement, options.dataKey, priceComponentClass);

    if( price == 0 && options.element.data(options.dataKey) > 0 )
      price = options.element.data(options.dataKey);

    var priceOldParentElement = parentElement.find(options.parentBlockSelector);

    var sumTotal = price * amountElement.val();
    options.element.text(number_format(sumTotal) + options.suffix);

    if( sumTotal > 0 && !(newPrice == sumTotal))
    {
      priceOldParentElement.show();
    }
    else
    {
      priceOldParentElement.hide();
    }

    return sumTotal;
  },

  _calcEconomy: function(price, priceOld, parentElement, options) {
    var economy = priceOld - price;
    var economyParentElement = parentElement.find(options.parentBlockSelector);

    if(priceOld > 0 && price > 0 && economy > 0 )
    {
      options.element.text(options.prefix + number_format(economy) + options.suffix);
      economyParentElement.show();
    }
    else
    {
      economyParentElement.hide();
    }

    return economy;
  },

  _getComponentsPrice: function(parentElement, dataKey, priceComponentClass) {
    var sum = 0;
    var self = this;
    parentElement.find('.' + priceComponentClass).each(function() {
      var price = 0;
      if( this.tagName == 'SELECT' )
      {
        var element = $(this).children('[value=' + $(this).val() + ']');
        self._checkDataKey(element, dataKey);
        price = element.data(dataKey);
      }
      else if( this.tagName == 'INPUT' )
      {
        var element = $(this);
        self._checkDataKey(element, dataKey);
        if( element.attr('type') == 'checkbox' )
        {
          if( !element.prop('checked') )
            return;
          price = element.data(dataKey) * element.data('amount');
        }
        else
        {
          price = element.data(dataKey) * $(this).val();
        }
      }
      else if( $(this).data(dataKey) )
      {
        price = $(this).data(dataKey);
      }
      sum += price;
    });

    return sum;
  },

  _checkDataKey: function(element, dataKey)
  {
    if( element.data(dataKey) === undefined )
    {
      throw new Error('Элемент id=' + element.attr('id') +' class=' + element.attr('class') + ' должен иметь атрибут data-' + dataKey);
    }
  },

  destroy: function() {
    $.Widget.prototype.destroy.call(this);
  }
});