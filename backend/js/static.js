/**
 * Интерфейсные функции
 */

/**
 * Удобные шапка и сайдбар
 */
function fixLayout() {
  var $header  = $('.s-header'),
      $subnav  = $('.s-subnav'),
      $shader  = $('.s-shader'),
      hHeight  = parseInt($header.outerHeight(), 10),
      snHeight = parseInt($subnav.outerHeight(), 10),
      sHeight  = parseInt($shader.outerHeight(), 10);

  var fixSubNav = function() {
    if($(window).scrollTop() > hHeight) {
      $shader.show();
      if(($(document).height() - $(window).height()) > (hHeight + snHeight + sHeight)) {
        $(document.body).addClass('mininize-header').css('paddingTop', snHeight);
        $shader.css('top', snHeight);
      }
    } else {
      $(document.body).removeClass('mininize-header').css('paddingTop', hHeight + snHeight);
      $shader.hide();
    }
  }();

  var fixSidebar = function() {
    var sidebar = $('aside#sidebar'),
        content = $('#content');

    if(sidebar.length) {
      if($(window).scrollTop() > content.find('.table').offset().top - snHeight && sidebar.height() <= $(window).height() - snHeight) {
        var originalWidth = sidebar.outerWidth();
        sidebar.addClass('fixed').css({
          top: snHeight,
          width: originalWidth
        });
      } else {
        sidebar.removeClass('fixed').css({
          paddingTop: content.find('.table').offset().top - content.offset().top,
          width: ''
        });
      }
    }
  }();
}

/**
 * IFrame для привязки контента
 *
 * @type {Object}
 */
var assigner =
{
  /**
   * @param url
   * @param params
   */
  open: function(url, params)
  {
    var options    = {};
    options.width  = '';
    options.height = '';

    options.addButton = params && params.addButton;

    if( typeof params === 'function' )
      options = { callback: params };
    else
      $.extend(options, params);

    var getsizes = function()
    {
      return [$(window).width(), $(window).height(), $(document).width(), $(document).height()];
    };

    var ie  = $.browser.ie;
    var szs = getsizes();

    $('body').append('<div id="saver"></div>');
    $('#saver').css({
      'height'     : (ie ? szs[3] + 'px' : '100%'),
      'width'      : (ie ? szs[2] + 'px' : '100%'),
      'position'   : (ie ? 'absolute' : 'fixed'),
      'opacity'    : '0.20',
      'top'        : 0,
      'left'       : 0,
      'z-index'    : 1040,
      'background' : '#000000'
    }).on('click', function(){assigner.close();});

    $('body').append('<div id="main-assign" style="display:none; background-color: white;" class="SUI_editor_popup"><div id="main-assign-inner" style="position:relative; background-color: white;"></div></div>')

    $('#main-assign-inner').append('<div id="main-assign-buttons-top" style="position: absolute; left: 0; top : 0; width: 100%; padding: 10px 0; text-align: right; border-top: 0; border-left: 0; border-right: 0;" class="SUI_editor_popup"></div>');
    $('#main-assign-inner').append('<div id="main-assign-buttons-bottom" style="position: absolute; left: 0; bottom : 0; width: 100%; padding: 10px 0; text-align: right; border-bottom: 0; border-left: 0; border-right: 0;" class="SUI_editor_popup"></div>');

    var btn_add = '<a href="#" class="btn btn-primary popup_add">Выбрать</a>';
    var btn_close = '<a href="#" class="btn btn-danger popup_close">Закрыть</a>';

    if( options.addButton )
    {
      $('#main-assign-buttons-top').append(btn_add);
      $('#main-assign-buttons-bottom').append(btn_add);
    }

    $('#main-assign-buttons-top').append(btn_close);
    $('#main-assign-buttons-bottom').append(btn_close);

    $('#main-assign-inner').append('<iframe id="assign-frame"></iframe>');
    $('#assign-frame').attr('scrolling', 'auto');
    $('#assign-frame').attr('frameborder', '0');
    $('#assign-frame').attr('src', url);
    $('#assign-frame').css({'width' : options.width ? options.width : (szs[0] - 24) + 'px', 'height' : options.height ? options.height : (szs[1] - 26 - 43 - 43) + 'px', 'margin' : '44px 0', 'z-index' : '11'});

    $('#main-assign').show();
    $('#main-assign').css({
      'width'    : options.width  ? options.width : (szs[0] - 24) + 'px',
      'height'   : options.height ? options.height: (szs[1] - 24) + 'px',
      'position' : (ie ? 'absolute' : 'fixed'),
      'text-align' : 'center',
      'top'      : 10,
      'left'     : 10,
      'z-index'  : 2000
    });

    // Фиксим размеры при ресайзе окна
    $(window).on('resize', function() {
      if( document.getElementById('main-assign') )
      {
        var szs = getsizes();
        if( !options.width )
        {
          $('#main-assign').css('width', (szs[0] - 24) + 'px');
          $('#assign-frame').css('width', (szs[0] - 24) + 'px');
        }
        if( !options.height )
        {
          $('#main-assign').css('height', (szs[1] - 24) + 'px');
          $('#assign-frame').css('height', (szs[1] - 26 - 43 - 43) + 'px');
        }
      }
    });

    $('#main-assign .popup_close').on('click', function(e){
      e.preventDefault();
      assigner.close();
    });

    $('#main-assign .popup_add').on('click', function(e){
      e.preventDefault();
      var els = $('iframe').contents().find('input[type=checkbox].select:checked');

      if( options.callback )
        options.callback(els);

      assigner.close();
    });

    $('iframe').load(function()
    {
      if( options.iframe_load )
        options.iframe_load();
    });

    if( options.after_show )
      options.after_show();
  },

  /**
   * закрывает страницу привязки
   */
  close: function()
  {
    $('#saver').remove();
    $('#main-assign').remove();
  },

  ajaxHandler: function(element, params)
  {
    iframeUrl = $(element).data('iframeurl');
    ajaxUrl   = $(element).data('ajaxurl');

    var callback = function(elements)
    {
      var ids = [];
      $(elements).each(function(){
        ids.push($(this).attr('id').match(/pk_(\d+)/)[1]);
      });

      $.post(ajaxUrl, {'ids' : ids}, false, 'json');
    };

    options = {'callback' : callback};
    $.extend(options, params);

    assigner.open(iframeUrl, options);
  }
};