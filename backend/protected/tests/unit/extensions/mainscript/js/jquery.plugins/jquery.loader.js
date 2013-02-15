/*
Name:      Loader
Use with:  jQuery
Version:   0.2.6 (25.10.2012)
Author:    Andrey Sidorov, Grigory Zarubin (Shogo.RU)


Отображает/скрывает ajax картинку, которая двигается вслед за мышью:
$.mouseLoader(bool);

Отображает/скрывает загрузчик или модальное окно с затемнением всего экрана:
$.overlayLoader(bool, options); // необязательный хэш параметров options может содержать три ключа:
                                   node - id объекта или сам объект, который должен быть выведен в режиме модального окна;
                                   onShow - коллбэк-функция, вызываемая сразу после открытия;
                                   onHide - коллбэк-функция, вызываемая сразу после закрытия.
*/

/* globals $t */
;(function($)
{
  function screensizes()
  {
    return [$(window).width(), $(window).height(), $(document).width(), $(document).height()];
  }

  var path   = 'i/ajax/';
  var loaders = {'loader': path + 'loader.gif', 'ajax': path + 'ajax.' + ($.browser.msie ? 'gif' : 'png')};
  var x = 0,
      y = 0;

  // Отслеживание последнего клика мышью
  var click_handler = function(e)
  {
    x = e.pageX - 16;
    y = e.pageY - 16;
  };
  $(function()
  {
    $(window).on('click.loader', click_handler);
  });


  // Картинка, двигающаяся за курсором
  $.mouseLoader = function(state)
  {
    var ssz = screensizes();
    var image = 'ajax';
    var $node = $('#loader_'+image);

    if( !x )
    {
      x = Math.ceil(ssz[0] / 2);
    }
    if( !y )
    {
      y = Math.ceil(ssz[1] / 2);
    }

    if( state ) // показать элемент
    {
      // создаем узел, если его нет
      if( !$node.length )
      {
        $node = $('<img src="'+loaders[image]+'" id="loader_'+image+'" style="display:none;left:'+x+'px;top:'+y+'px;position:absolute;z-index:9999" alt="" />');
        $(document.body).append($node);
        $node.data('move_handler', function(e) {
          $node.css({
            'left' : e.pageX - 16,
            'top'  : e.pageY - 16
          });
        });
        $node.data('depth', 0);
      }
      if( !$node.data('depth') )
      {
        $node.css({
          'left' : x - 16,
          'top'  : y - 16
        });
        $node.show();
        $(window).on('mousemove.loader', $node.data('move_handler'));
      }
      $node.data('depth', $node.data('depth') + 1);
    }
    else // скрыть элемент
    {
      $node.data('depth', $node.data('depth') - 1);
      if( !$node.data('depth') )
      {
        $node.hide();
        $(window).off('mousemove.loader', $node.data('move_handler'));
      }
    }
  };

  // Затемнение экрана (или режим вывода модального окна)
  $.overlayLoader = function(state, options)
  {
    var ssz = screensizes();
    var image = 'loader';
    var $node = $('#loader_' + image);
    var getNode = function(node) {
      if( typeof node === 'string' )
      {
        $node = $('#' + node);
      }
      else
      {
        $node = $(node);
      }
      return $node;
    };
    var opts = {
      'onShow': $.noop,
      'onHide': $.noop
    };

    if( options )
    {
      if( $.isPlainObject(options) )
      {
        $.extend(opts, options);
        if(opts.node)
        {
          getNode(opts.node);
          delete opts.node;
        }
      }
      else
      {
        getNode(options);
      }
    }

    var $overlay = $('#loader_overlay');
    var x = (window.scrollX || document.documentElement.scrollLeft || document.body.scrollLeft) + Math.ceil(ssz[0] / 2),
        y = (window.scrollY || document.documentElement.scrollTop || document.body.scrollTop) + Math.ceil(ssz[1] / 2);

    if( state ) // показать элемент
    {
      // создаем узел, если его нет
      if( !$overlay.length )
      {
        var ie = $.browser.msie;
        $overlay = $('<div id="loader_overlay" style="display:none;position:'+(ie ? 'absolute' : 'fixed')+';top:0;left:0;z-index:9998;background:#000000;width:'+(ie ? ssz[2]+'px' : '100%')+';height:'+(ie ? ssz[3]+'px' : '100%')+'" />').css('opacity', 0).on('click', function() {
          $.overlayLoader(false, $overlay.data('loader_node'));
        });
        if( !$node.length )
        {
          $node = $('<div id="loader_'+image+'" style="display:none;position:absolute;z-index:9999;font-family:Tahoma,Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-size-adjust:none;font-style:normal;font-variant:normal;font-weight:normal;line-height:120%"><p style="color:#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Загрузка. Пожалуйста, подождите...</p><img src="'+loaders[image]+'" /></div>');
        }
        $(document.body).append($overlay).on('keypress', function(e) {
          if( $overlay.is(':visible') && e.keyCode == 27 )
          {
            $.overlayLoader(false, $overlay.data('loader_node'));
          }
        });

        $overlay.data('depth', 0);
      }

      if( !$overlay.data('depth') )
      {
        $overlay.data({
          'loader_node': $node,
          'loader_onHide': opts.onHide
        }).show();
        $t($overlay, { 'opacity' : 0.5, 'time' : 0.2, 'transition' : 'easeNone', 'onComplete' : function() {
          $(document.body).append($node);
          $node.show();

          var w = $node.outerWidth(),
              h = $node.outerHeight();
          $node.css({
            'left' : (x - w/2) < 0 ? 0 : (x - w/2) + 'px',
            'top'  : (y - h/2) < 0 ? 0 : (y - h/2) + 'px'
          });

          opts.onShow();
        }}).tween();
      }

      $overlay.data('depth', $overlay.data('depth') + 1);
    }
    else if( $overlay.data('depth') ) // скрыть элемент
    {
      if( $node.length == 0 )
      {
        setTimeout(function() { // это хуета какая-то бессмысленная
          $.overlayLoader(false, $overlay.data('loader_node'));
        }, 200);
        return;
      }

      $overlay.data('depth', $overlay.data('depth') - 1);
      if( !$overlay.data('depth') )
      {
        $t($overlay, { 'opacity' : 0, 'time' : 0.2, 'transition' : 'easeNone', 'onComplete' : function() {
          $node.hide();
          $overlay.hide();
          $overlay.data('loader_onHide')();
        }}).tween();
      }
    }
  };
})(jQuery);