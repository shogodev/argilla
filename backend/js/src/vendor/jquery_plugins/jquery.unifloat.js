/*!
 * Unifloat
 * Universal jQuery plugin for simply creating floating elements such as Dropdown menu and Tooltips
 *
 * @requires jQuery v1.4.3 or newer
 *
 * @author Grigory Zarubin
 * @link http://craigy-.github.com/Unifloat/
 * @version 3.0.0
 * @date 19.04.2012
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

(function($) {
  var unifloat = {
    init: function(options) {
      var opts = $.extend(true, {}, unifloat.defaults, options),
          cache = [];

      // Hides all animated elements
      var hideAll = function() {
        for(var i=0,l=cache.length; i<l; i++) {
          var el = $(cache[i]);
          if(el.is(':animated')) {
            $(el).stop(true, true);
            $(el).hide();
          }
        }
      };

      return this.each(function() {
        if(!$(this).data('unifloat')) {
          var self = this,
              targel = unifloat._getTarget.call(this, opts);

          if(!targel) return;

          $(this).data('unifloat', opts); // against multiple instantiations
          if(opts.manipulation) $(document.body).append($(targel));
          cache.push(targel);

          if(opts.move) {
            var mouseCoords = function(e) { // returns coordinates of the mouse cursor with a given offset (in the format of expressions for method .unifloat('pos'))
              var x = e.pageX,
                  y = e.pageY,
                  x_offset = opts.move[1] || 15,
                  y_offset = opts.move[0] || 15;
              return {
                'top' : {
                  value : y + y_offset,
                  auto  : y < 0 ? '0' : String(y - y_offset) + '-%%THISHEIGHT%%'
                },
                'left' : {
                  value : x + x_offset,
                  auto  : x < 0 ? '0' : String(x - x_offset) + '-%%THISWIDTH%%'
                }
              };
            };
          }

          $(this).hover(
            function(e) {
              if($(targel).is(':animated') || $(targel).is(':visible')) return;
              hideAll();

              if(opts.move) {
                var mpos = mouseCoords(e);
              }

              unifloat.show.call($(targel), $.extend(true, {}, opts, {
                rel : this,
                posTop : opts.move ? mpos.top : opts.posTop,
                posLeft : opts.move ? mpos.left : opts.posLeft,
                onShow : function(source, target) {
                  if(opts.move) {
                    var mpos = mouseCoords(e);
                    $(targel).css(unifloat.pos.call($(targel), $.extend(true, {}, opts, {
                      rel : self,
                      posTop : mpos.top,
                      posLeft : mpos.left
                    })));
                  }
                  opts.onShow(source, target);
                }
              }));
            },
            function(e) {
              var check = ($(e.relatedTarget).attr('id') && '#'+$(e.relatedTarget).attr('id')==targel) || $(e.relatedTarget).parents(targel).length!=0;
              if(check && !opts.move) return;
              $(targel).hide();
              hideAll();
              opts.onHide(this, targel);
            }
          );

          if(opts.move) {
            $(this).mousemove(function(e) {
              if(!$(targel).is(':animated')) {
                var mpos = mouseCoords(e);
                $(targel).css(unifloat.pos.call($(targel), $.extend(true, {}, opts, {
                  rel : self,
                  posTop : mpos.top,
                  posLeft : mpos.left
                })));
              }
            });
          }

          $(targel).mouseleave(function(e) {
            var check = false;
            $(e.relatedTarget).parents().each(function() {
              if(this===self) check = true;
            });
            if(e.relatedTarget===self || check) return;
            $(this).hide();
            opts.onHide(self, this);
          });
        }
      });
    },


    pos: function(options) {
      var opts = $.extend(true, {}, unifloat.defaults, options);

      var source = unifloat._getSource.call(this, opts) || document.body,
          coords = opts.manipulation ? $(source).offset() : $(source).position(),
          tw = $(this).outerWidth(),
          th = $(this).outerHeight(),
          sw = $(source).outerWidth(),
          sh = $(source).outerHeight();

      var countValue = function(str, sideTop) { // parses and calculates expression
        var aliasTop = {
          'above'  : coords.top - th,
          'top'    : coords.top,
          'center' : coords.top + sh / 2,
          'bottom' : coords.top + sh - th,
          'under'  : coords.top + sh
        },
        aliasLeft = {
          'before' : coords.left - tw,
          'left'   : coords.left,
          'center' : coords.left + sw / 2,
          'right'  : coords.left + sw - tw,
          'after'  : coords.left + sw
        },
        templates = {
          '%%SOURCEWIDTH%%'    : sw,
          '%%SOURCEHEIGHT%%'   : sh,
          '%%THISWIDTH%%'      : tw,
          '%%THISHEIGHT%%'     : th,
          '%%WINDOWWIDTH%%'    : $(window).width(),
          '%%WINDOWHEIGHT%%'   : $(window).height(),
          '%%DOCUMENTWIDTH%%'  : $(document).width(),
          '%%DOCUMENTHEIGHT%%' : $(document).height()
        },
        keys = [];

        for(var i in sideTop ? aliasTop : aliasLeft) keys.push(i);
        var parsed_aliases = new RegExp('(' + keys.join('|') + ')', 'g');
        var ns = str.toString().replace(/(%%[A-Z]+%%)/g, function($0) {
          return templates[$0];
        }).replace(parsed_aliases, function($0) {
          return (sideTop ? aliasTop : aliasLeft)[$0];
        });

        try {
          return eval('(' + ns + ')');
        } catch(err) {
          $.error('Unifloat: method \'pos\' can\'t get the correct coordinates, please check your expressions!');
        }
      };

      var isFit = function(x, y) { // check whether the target is placed within the browser window
        var dsl = $(document).scrollLeft(),
            dst = $(document).scrollTop();
        return {
          'top'  : y >= dst && ($(window).height() + dst) >= (y + th),
          'left' : x >= dsl && ($(window).width() + dsl) >= (x + tw)
        };
      };

      var tt = countValue(opts.posTop.value, true), tl = countValue(opts.posLeft.value);
      return {
        'top'  : opts.posTop.auto ? (isFit(tl, tt).top ? tt : countValue(opts.posTop.auto, true)) : tt,
        'left' : opts.posLeft.auto ? (isFit(tl, tt).left ? tl : countValue(opts.posLeft.auto)) : tl
      };
    },


    show: function(options) {
      var opts = $.extend(true, {}, unifloat.defaults, options);

      return this.each(function() {
        var coords = unifloat.pos.call(this, opts),
            source = unifloat._getSource.call(this, opts) || document.body;

        opts.onHover($(source), this);
        $(this).css(coords).show(opts.effect ? opts.effect : 0, function() {
          opts.onShow($(source), this);
        });
      });
    },


    _getSource: function(options) {
      var bid = $(this).attr('id'),
          source;

      if(!bid && !$(options.rel).length) return false;
      if($(options.rel).length) {
        source = options.rel;
      } else {
        source = '#' + bid.replace(options.rel, '');
      }

      return source;
    },


    _getTarget: function(options) {
      var bid = $(this).attr('id'),
          target;

      if(!bid && !$(options.rel).length) return false;
      if($(options.rel).length) {
        target = options.rel;
      } else {
        target = '#' + bid + options.rel;
      }

      return target;
    },


    defaults: {
      rel           : '_content',
      posTop        : {
        value : 'under',
        auto  : 'above'
      },
      posLeft       : {
        value : 'left',
        auto  : 'right'
      },
      move          : false,
      effect        : 'fast',
      manipulation  : true,
      onHover       : $.noop,
      onShow        : $.noop,
      onHide        : $.noop
    }
  };


  $.fn.unifloat = function(method) {
    if(unifloat[method]) {
      return unifloat[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if($.isPlainObject(method) || !method) {
      return unifloat.init.apply(this, arguments);
    } else {
      $.error('Unifloat: method ' +  method + ' doesn\'t exist!');
    }
  };
})(jQuery);