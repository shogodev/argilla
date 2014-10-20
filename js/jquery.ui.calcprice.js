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
 *    <span class="s34 bb dynamic-price" data-price="900">900</span>
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
    price : {
      elementSelector : null,
      dataKey : 'price',
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
    options.priceOld['element'] = parentElement.find( options.priceOld.elementSelector);
    options.economy['element'] = parentElement.find( options.economy.elementSelector);

    var widget = this;
    options.amount.bind('change', function() {
      widget._calc(parentElement, options);
    });

    options.price.element.bind('change', function() {
      widget._calc(parentElement, options);
    });
   },

  _calc: function(parentElement, options) {
    var price = this._calcPrice(parentElement, options.price, options.amount);
    var priceOld = this._calcPriceOld(parentElement, options.priceOld, options.amount, price);
    this._calcEconomy(price, priceOld, parentElement, options.economy);
  },

  _calcPrice: function(parentElement, options, amountElement) {
    var price = this._getComponentsPrice(parentElement, options.dataKey);

    if( price == 0 && options.element.data(options.dataKey) > 0 )
      price = options.element.data(options.dataKey);

    var sumTotal = price * amountElement.val();
    options.element.text(number_format(sumTotal) + options.suffix);

    return sumTotal;
  },

  _calcPriceOld: function(parentElement, options, amountElement, newPrice) {
    var price = this._getComponentsPrice(parentElement, options.dataKey);

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
  },

  _getComponentsPrice: function(parentElement, dataKey) {
    var sum = 0;
    var self = this;
    parentElement.find('._price_element').each(function() {
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
        price = element.data(dataKey) * $(this).val();
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