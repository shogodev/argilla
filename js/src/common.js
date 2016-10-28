(function() { 'use strict';

  // Const
  // -----
  window.SMALL_MOBILE_WIDTH = 480;
  window.MOBILE_WIDTH = 800;
  window.TABLET_WIDTH = 1024;
  window.SMALL_NOTEBOOK_WIDTH = 1200;
  window.NOTEBOOK_WIDTH = 1400;
  window.HEADER_HEIGHT = $('.header').height();

  // selectors
  window.$WINDOW = $(window);
  window.$DOCUMENT = $(document);
  window.$HTML = $(document.documentElement);
  window.$BODY = $(document.body);

  // tosrus default settings
  window.TOSRUS_DEFAULTS = {
    buttons: {
      next: true,
      prev: true
    },

    keys: {
      prev: 37,
      next: 39,
      close: 27
    },

    wrapper: {
      onClick: 'close'
    }
  };


  // Helpers
  // -------

  window.WINDOW_WIDTH = window.innerWidth || $WINDOW.width();
  window.WINDOW_HEIGHT = $WINDOW.height();
  $WINDOW.resize(function() {
    WINDOW_WIDTH = window.innerWidth || $WINDOW.width();
    WINDOW_HEIGHT = $WINDOW.height();
  });

  window.IS_DESKTOP_WIDTH = function() {
    return WINDOW_WIDTH > NOTEBOOK_WIDTH;
  };
  window.IS_NOTEBOOK_WIDTH = function() {
    return ( WINDOW_WIDTH > SMALL_NOTEBOOK_WIDTH && WINDOW_WIDTH <= NOTEBOOK_WIDTH );
  };
  window.IS_SMALL_NOTEBOOK_WIDTH = function() {
    return ( WINDOW_WIDTH > TABLET_WIDTH && WINDOW_WIDTH <= SMALL_NOTEBOOK_WIDTH );
  };
  window.IS_TABLET_WIDTH = function() {
    return ( WINDOW_WIDTH > MOBILE_WIDTH && WINDOW_WIDTH <= TABLET_WIDTH );
  };
  window.IS_MOBILE_WIDTH = function() {
    return WINDOW_WIDTH <= MOBILE_WIDTH;
  };
  window.IS_SMALL_MOBILE_WIDTH = function() {
    return WINDOW_WIDTH <= SMALL_MOBILE_WIDTH;
  };
  window.IS_TOUCH_DEVICE = 'ontouchstart' in document;


  // Masked input
  // ------------

  if (IS_DESKTOP) {
    $('input[type="date"]').attr('type', 'text');

    // Phone
    $('input[type="tel"]').mask('+7 (999) 999-99-99', {
      autoclear: false
    });

    // Date
    $('.js-date-mask').mask('99/99/9999', {
      placeholder: 'дд.мм.гггг',
      autoclear: false
    });

    // Time
    $('.js-time-mask').mask('99:99', {
      placeholder: 'чч:мм',
      autoclear: false
    });
  }

  // Overlay loader
  // --------------

  // open popup
  $DOCUMENT.on('click.overlay-open', '.js-overlay', function(e) {
    e.preventDefault();
    $.overlayLoader(true, $($(this).attr('href')));
  });

  // autofocus
  $DOCUMENT.on('overlayLoaderShow', function(e, $node) {
    $node.find('.js-autofocus-inp').focus();
  });

  // close popup
  $DOCUMENT.on('click.overlay-close', '.js-popup-close', function(e) {
    e.preventDefault();
    $.overlayLoader(false, $(this).closest('.js-popup'));
  });


  // Selectric
  // ---------

  // init selectric
  $DOCUMENT.on('initSelectric yiiListViewUpdated', function() {
    $('select').selectric({
      disableOnMobile: true
    });
  }).trigger('initSelectric');


  // Scroll to
  // ---------

  $DOCUMENT.on('click.scroll-to', '.js-scroll-to', function(e) {
    e.preventDefault();

    var $lnk = $(this);
    var $elemToScroll = $($lnk.attr('href'));
    var speed = $lnk.data('speed') || 150;
    var offset = $lnk.data('offset') || 0;

    $WINDOW.scrollTo($elemToScroll, {duration: speed, offset: offset});
  });


  // Menus
  // -----

  (function() { // Чтобы потом с тачем не запариваться
    var $menus = $('.js-menu');

    if (IS_DESKTOP) {
      $menus.on('mouseenter.js-menu', 'li', function() {
        var self = $(this);
        clearTimeout(self.data('hoverTimeout'));
        self.addClass('is-hovered');
      });

      $menus.on('mouseleave.js-menu', 'li', function() {
        var self = $(this);
        self.data('hoverTimeout', setTimeout(function() {
          self.removeClass('is-hovered');
        }, 200));
      });
    }

    if (IS_MOBILE) {
      $menus.on('click.js-m-menu', 'a', function(e) {
        e.preventDefault();

        var $anchor = $(this);
        var $parent = $anchor.parent();

        var isWithDropdown = $parent.hasClass('with-dropdown');
        var isOnHover = $parent.hasClass('is-hovered');

        $parent.siblings().removeClass('is-hovered');

        if (!isWithDropdown) {
          location.href = $anchor.attr('href');
        } else {
          if (isOnHover) {
            location.href = $anchor.attr('href');
          } else {
            $parent.addClass('is-hovered');
          }
        }
      });
    }
  }());


  // Tabs
  // ----

  $('.js-tabs .tabs-nav li a').click(function(e) {
    e.preventDefault();

    var $self = $(this);
    var $panel = $( $self.attr('href') );

    $self.closest('li').addClass('active').siblings().removeClass('active');
    $panel.closest('.tabs').find('.tabs-panel').hide();
    $panel.fadeIn();
  });


  // Galleries
  // ---------

  // init tosrus static gallery
  $('.js-gallery').each(function() {
    $(this).find('.js-gallery-item').tosrus(TOSRUS_DEFAULTS);
  });


  // Rotators
  // --------

  $('.js-slideshow').each(function() {
    var $self = $(this);

    var tos = $self.tosrus({
      effect: 'slide',
      slides: {
        visible: 1
      },
      autoplay: {
        play: true,
        timeout: 7500
      },
      infinite: true,
      pagination: {
        add: true
      }
    });
  });


  // Scrolling to top
  // ----------------

  if ( !IS_MOBILE_WIDTH() ) {
    var goTopBtn = $('<div class="go-top-btn"></div>');
    goTopBtn.click(function() {
      $WINDOW.scrollTo(0, 200);
    });
    $WINDOW.scroll(function() {
      var scrollTop = $WINDOW.scrollTop();
      if ( scrollTop > 0 ) {
        goTopBtn.addClass('visible');
      } else {
        goTopBtn.removeClass('visible');
      }
    });
    $BODY.append( goTopBtn );
  }

})();
