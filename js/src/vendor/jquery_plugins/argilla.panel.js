/**
 * Created by tatarinov on 30.11.15.
 * После обровления панели генерируется событие argillaPanelUpdated у body
 *
 * Пример использования:
 *   $('#js-argilla-panel')['argillaPanel']({
 *     selectors : {
 *       'left': {
 *         header: '#js-panel-header-left',
 *         body: '#js-panel-body-left',
 *         footer: '#js-panel-footer-left',
 *         carousel: $('#js-panel-carousel-left').panelCarousel({
 *           controls : {
 *             container : '.js-carousel-container',
 *             buttonPrev: '.js-carousel-prev',
 *             buttonNext: '.js-carousel-next'
 *           },
 *           items: 5
 *         })
 *       },
 *       'right': {
 *         header: '#js-panel-header-right',
 *         body: '#js-panel-body-right',
 *         footer: '#js-panel-footer-right',
 *         carousel: $('#js-panel-carousel-right').panelCarousel({
 *           controls : {
 *             container : '.js-carousel-container',
 *             buttonPrev: '.js-carousel-prev',
 *             buttonNext: '.js-carousel-next'
 *           },
 *           items: 5
 *         })
 *       }
 *     },
 *     ajaxUpdateSelectors : [],
 *     hidePanelButton: '#panel-hide-button',
 *     activeClass : 'active',
 *     collapseClass : 'collapsed',
 *     panelItemElement : 'li'
 *   });
 *
 *   // обработчик события argillaPanelUpdated
 *   $('body').on('argillaPanelUpdated', function(event, id, response) {
 *      $('#' + id).find('input[type="tel"], .phone-input').each(function() {
 *       var $_this = $(this), val;
 *       $_this.mask('+7 (999) 999-99-99', {autoclear: false});
 *      })
 *   });
 *
 *  Пример использования анимации
 *  $this->basket->addAfterAjaxScript(new CJavaScriptExpression("
 *    if( action == 'add' )
 *    {
 *      image = element.closest('.product, .product-card, .full-view-container').find('.animate-image');
 *      $('#js-argilla-panel')['argillaPanel']('animate', image, 'right');
 *    }
 *    $('#js-argilla-panel')['argillaPanel']('update', response);
 *  "));
 */
;(function($, undefined) { 'use strict';

  var pluginName = 'argillaPanel';
  var pluginData = 'jquery_plugin_' + pluginName;
  var pluginDefaultSelector = '#js-argilla-panel';
  var pluginDefaults = {
    selectors : {
      'left': {
        header: '#panel-header-left',
        body: '#panel-header-left',
        footer: '#panel-footer-left',
        carousel: undefined
      },
      'right': {
        header: '#panel-header-right',
        body: '#panel-header-right',
        footer: '#panel-footer-right',
        carousel: undefined
      }
    },
    ajaxUpdateSelectors : [],
    hidePanelButton: '#panel-hide-button',
    activeClass : 'active',
    collapseClass : 'collapsed',
    panelItemElement : 'li'
  };

  var Plugin = (function() {

    function Plugin(element, options) {
      this.element = $(element);
      this.panelElements = {};
      this.hideButton = null;
      this.afterAjaxUpdate = function (id, response) {
        $('body').trigger('argillaPanelUpdated', [id, response]);
      };
      this.config = $.extend(false, pluginDefaults, options || {});
      this.init();
    }

    return Plugin;
  }());

  $.extend(Plugin.prototype, {

    init: function() {
      var widget = this;
      var config = widget.config;
      var selectors = config.selectors;

      for(var panelIndex in selectors)
      {
        if( !selectors.hasOwnProperty(panelIndex) )
          continue;

        widget.panelElements[panelIndex] = {header : null, body : null, footer : null, carousel : null};
        for(var elementName in widget.panelElements[panelIndex])
        {
          widget.panelElements[panelIndex][elementName] = $(selectors[panelIndex][elementName]);
        }
        widget.panelElements[panelIndex]['carousel'] = selectors[panelIndex]['carousel'];

        widget.panelElements[panelIndex].header.data('body-index', panelIndex);
        widget.panelElements[panelIndex].header.on('click', function(e) {
          e.preventDefault();
          widget._clickByHeader($(this), widget.panelElements[$(this).data('body-index')].body);
        });
      }

      if( widget.hideButton = $(config.hidePanelButton) ) {
        widget.hideButton.on('click', function (e) {
          e.preventDefault();
          widget._collapse();
        });
      }

      if( !widget.element.hasClass(config.collapseClass) )
        widget.element.addClass(config.collapseClass);

      widget._checkEmptyPanel();
    },

    _clickByHeader : function(header, boody) {
      if( !header.hasClass(this.config.activeClass) )
      {
        this._deactivateHeader();
        header.addClass(this.config.activeClass);
        boody.show();
        this._showPanel();
      }
      else
        this._collapse();
    },

    _collapse : function() {
      this._deactivateHeader();
      if( !this.element.hasClass(this.config.collapseClass) )
        this.element.addClass(this.config.collapseClass);
    },

    _deactivateHeader : function() {
      for(var panelIndex in this.panelElements)
      {
        if( !this.panelElements.hasOwnProperty(panelIndex) )
          continue;

        this.panelElements[panelIndex].header.removeClass(this.config.activeClass);
        this.panelElements[panelIndex].body.hide();
      }
    },

    _showPanel : function() {
      this.element.removeClass(this.config.collapseClass);
    },

    _checkEmptyPanel : function() {

      var panelsEmpty = true;

      for(var panelIndex in this.panelElements)
      {
        if( !this.panelElements.hasOwnProperty(panelIndex) )
          continue;

        var panelNotEmpty = !this._isEmptyBody(this.panelElements[panelIndex].body);

        if( panelsEmpty && panelNotEmpty )
          panelsEmpty = false;

        if( panelNotEmpty )
        {
          this._visible(this.panelElements[panelIndex].header);
          this.panelElements[panelIndex].footer.show();
        }
        else
        {
          this._invisible(this.panelElements[panelIndex].header);
          this.panelElements[panelIndex].footer.hide();
        }
      }

      if( !panelsEmpty )
      {
        this._visible(this.element);
      }
      else
      {
        this._collapse();
        this._invisible(this.element);
      }
    },

    _isEmptyBody : function(body) {
      return body.find(this.config.panelItemElement).length > 0 ? false : true;
    },

    _updateCarousel : function(panelIndex, content) {
      var element = this.panelElements[panelIndex];
      var carousel = this.panelElements[panelIndex].carousel;

      this.panelElements[panelIndex]

      if( carousel != undefined )
      {

        var selector = carousel.panelCarousel('getSelector') + ' ul';
        var carouselItems = element.body.find(selector);
        var newCarouselItems = content.find(element.body.selector + ' ' + selector).html();
        var emptyBeforeUpdate = this._isEmptyBody(element.body);
        carouselItems.html(newCarouselItems);
        var emptyAfterUpdate = this._isEmptyBody(element.body);

        if( !emptyBeforeUpdate && emptyAfterUpdate )
          this._collapse();

        carousel.panelCarousel('update');
      }
    },

    _invisible : function(element){
      element.css({'opacity' : 0, 'visibility' : 'hidden'});
    },

    _visible : function(element){
      element.css({'opacity' : 1, 'visibility' : 'visible'});
    },

    update : function(response)
    {
      var content = $('<div>' + response + '</div>');
      var panel = $(content.find('#' + this.element.attr('id')));

      for(var panelIndex in this.panelElements) {
        if (!this.panelElements.hasOwnProperty(panelIndex))
          continue;

        this.panelElements[panelIndex].header.html(panel.find(this.panelElements[panelIndex].header.selector).html())
        this.panelElements[panelIndex].footer.html(panel.find(this.panelElements[panelIndex].footer.selector).html())

        this._updateCarousel(panelIndex, panel);
      }

      for(var i in this.config.ajaxUpdateSelectors)
      {
        if( this.config.ajaxUpdateSelectors.hasOwnProperty(i) )
        {
          var oldElement = $(this.config.ajaxUpdateSelectors[i]);
          var newElement = panel.find(this.config.ajaxUpdateSelectors[i]);

          if( oldElement.length > 0 &&  newElement.length > 0 )
          {
            oldElement.replaceWith(newElement);
          }
        }
      }

      this._checkEmptyPanel();

      this.afterAjaxUpdate(this.element.attr('id'), response);
    },

    animate : function(pic, panelIndex) {
      $(this.panelElements[panelIndex].header.selector).addInCollection(pic)
    },

    destroy: function() {
    }

  });

  $.fn[pluginName] = function(options) {
    var args = arguments;

    if (options === undefined || typeof options === 'object') {
      return this.each(function() {
        if (!$.data(this, pluginData)) {
          $.data(this, pluginData, new Plugin(this, $.extend(options, $(this).data())));
        }
      });

    } else if (typeof options === 'string') {
      var returns;

      this.each(function() {
        var instance = $.data(this, pluginData);

        if (instance instanceof Plugin && typeof instance[options] === 'function') {
          returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
        }
      });

      return returns !== undefined ? returns : this;
    }
  };
}(jQuery));