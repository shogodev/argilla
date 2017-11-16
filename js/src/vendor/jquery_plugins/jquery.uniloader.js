/*!
 * Uniloader
 * Shows mouse or overlay loaders (with a modal window optionally)
 *
 * @requires jQuery v1.4.3 or newer
 *
 * @author Grigory Zarubin (http://craigy.ru/)
 * @version 1.1.0
 * @date 01.09.2016
 *
 * Dual licensed under the MIT or GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

(function ($) {
  var uniloader = {
    APNGSupported: false,
    actualResizer: 'resize',

    init: function () {
      // Supports 'throttledresize' event
      if ('throttledresize' in jQuery.event.special) {
        this.actualResizer = 'throttledresize';
      }

      this._checkAPNGSupport();
    },

    // Check browser supported APNG or not
    _checkAPNGSupport: function () {
      var APNGTest = new Image(),
          cv = document.createElement('canvas');
      APNGTest.onload = function () {
        if (cv.getContext) {
          var ctx = cv.getContext('2d');
          ctx.drawImage(APNGTest, 0, 0);
          uniloader.APNGSupported = (ctx.getImageData(0, 0, 1, 1).data[3] === 0);
        }
      };
      APNGTest.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACGFjVEwAAAABAAAAAcMq2TYAAAANSURBVAiZY2BgYPgPAAEEAQB9ssjfAAAAGmZjVEwAAAAAAAAAAQAAAAEAAAAAAAAAAAD6A+gBAbNU+2sAAAARZmRBVAAAAAEImWNgYGBgAAAABQAB6MzFdgAAAABJRU5ErkJggg==';
    },

    // Calculate dimensions of the browser window
    _getWindowDimensions: function () {
      return {
        width: $(window)[0].innerWidth || $(window).width(),
        height: $(window)[0].innerHeight || $(window).height()
      };
    },

    // Calculate the scrollbar width (0 - no scrollbar)
    _getScrollbarWidth: function () {
      var wd = this._getWindowDimensions();
      if ($(document).height() - wd.height <= 0) {
        return 0;
      }

      var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
          child = parent.children();
      var scrollbarWidth = child.innerWidth() - child.height(99).innerWidth();
      parent.remove();

      return scrollbarWidth;
    },

    // Centering node in the browser window
    _centerNode: function (node, scrollable) {
      var wd = this._getWindowDimensions(),
          w = node.outerWidth(),
          h = node.outerHeight(),
          x = (scrollable ? $(window).scrollLeft() : 0) + Math.ceil(wd.width / 2),
          y = (scrollable ? $(window).scrollTop() : 0) + Math.ceil(wd.height / 2);

      return {
        'left' : (x - w / 2) < 0 ? 0 : (x - w / 2) + 'px',
        'top'  : (y - h / 2) < 0 ? 0 : (y - h / 2) + 'px'
      };
    },

    defaults: {
      hideSelector: '.modal-close',
      effectSpeed:  200,
      onStart: $.noop,
      onShow:  $.noop,
      onHide:  $.noop
    }
  };

  // Mouse loader
  $.mouseLoader = function (state, options) {
    var $node = $('#uniloader-mouse');
    var opts = $.extend(true, {}, uniloader.defaults, options);

    // Show mouse loader
    if (state) {
      // Create node
      if (!$node.length) {
        $node = $('<div id="uniloader-mouse" />').data({
          'uniloader-mousemove': function (e) {
            $node.css({
              'top'  : e.pageY - ($node.outerHeight() / 2),
              'left' : e.pageX - ($node.outerWidth() / 2)
            });
          },
          'uniloader-onHide': opts.onHide
        });
        $(document.body).append($node);
        if (uniloader.APNGSupported) {
          $node.css('backgroundImage', $node.css('backgroundImage').replace(/ajax\.gif/gi, 'ajax.png'));
        }
      }

      // Show node
      if ($node.is(':visible')) {
        return;
      }

      opts.onStart();

      var coords = uniloader._centerNode($node, true);
      $node.css({
        'top'  : coords.top,
        'left' : coords.left
      }).show(opts.effectSpeed, function () {
        opts.onShow();
      });

      $(document.body).on('mousemove.uniloader scroll.uniloader', $node.data('uniloader-mousemove'));
    } else {
      // Hide mouse loader
      $node.hide(opts.effectSpeed, function () {
        $node.data('uniloader-onHide')();
      });

      $(document.body).off('mousemove.uniloader scroll.uniloader', $node.data('uniloader-mousemove'));
    }
  };

  $.isMouseLoaderActive = function() {
    var $node = $(document.body).find('#uniloader-mouse:visible');
    return $node.length > 0;
  };

  // Overlay loader
  $.overlayLoader = function (state, options) {
    var $overlay = $('#uniloader-overlay'),
        $node = $('#uniloader-overlay-content'),
        isModal = false;

    // 'node' is optional
    if (options && options.node) {
      $node = $(options.node);
      isModal = true;
      delete options.node;
    }

    var opts = $.extend(true, {}, uniloader.defaults, options);

    // Show overlay loader or modal window
    if (state) {
      // Create overlay
      if (!$overlay.length) {
        $overlay = $('<div id="uniloader-overlay" />');
        $(document.body).append($overlay);
      }

      // Create overlay loader node
      if (!$node.length) {
        $node = $('<div id="uniloader-overlay-content"><div class="uniloader-overlay-content-text" /></div>');
      }

      // Show node
      if ($overlay.is(':visible')) {
        $.overlayLoader(false, {
          effectSpeed: 0
        });
      }

      opts.onStart();

      if (isModal) {
        $overlay.on('click.uniloader', function (e) {
          if ( $(e.target).attr('id') == 'uniloader-overlay' ) {
            $.overlayLoader();
          }
        });

        $(document.body).on('keypress.uniloader', function (e) {
          if (e.keyCode == 27) {
            $.overlayLoader();
          }
        });

        $node.find(opts.hideSelector).on('click.uniloader', function (e) {
          e.preventDefault();
          $.overlayLoader();
        });
      }
      $(window).on(uniloader.actualResizer + '.uniloader gestureend.uniloader', function () {
        var coords = uniloader._centerNode($node);
        $node.css({
          'top'  : coords.top,
          'left' : coords.left
        });
      });

      var scrollbarWidth = uniloader._getScrollbarWidth();
      $('html').addClass('uniloader-overlay-html').css('margin-right', scrollbarWidth);
      $(opts.fixedElements).css('padding-right', scrollbarWidth);

      $overlay.data({
        'uniloader-ismodal': isModal,
        'uniloader-node': $node,
        'uniloader-node-parent': $node.parent().length ? $node.parent() : $(document.body),
        'uniloader-onHide': opts.onHide,
        'uniloader-fixedElements': opts.fixedElements
      }).append($node).fadeTo(opts.effectSpeed, 1, function () {
        var coords = uniloader._centerNode($node);
        $node.css({
          'top'  : coords.top,
          'left' : coords.left
        }).show(opts.effectSpeed, function () {
          $(document.body).trigger('overlayLoaderShow', [$node]);
          opts.onShow();
        });
      });
    } else {
      // Hide node
      $node = $($overlay.data('uniloader-node'));

      if ($overlay.data('uniloader-ismodal')) {
        $overlay.off('.uniloader');
        $(document.body).off('keypress.uniloader');
        $node.find(opts.hideSelector).off('.uniloader');
      }
      $(window).off(uniloader.actualResizer + '.uniloader gestureend.uniloader');

      $node.hide(opts.effectSpeed);
      $overlay.fadeOut(opts.effectSpeed, function () {
        $($overlay.data('uniloader-node-parent')).append($node);
        $('html').removeClass('uniloader-overlay-html').css('margin-right', '');
        $($overlay.data('uniloader-fixedElements')).css('padding-right', '');
        $overlay.data('uniloader-onHide')();
      });
    }
  };

  uniloader.init();

})(jQuery);
