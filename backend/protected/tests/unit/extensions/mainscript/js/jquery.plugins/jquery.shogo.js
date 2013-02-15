/*
Name:      Shogo
Use with:  jQuery
Version:   0.4.7 (12.09.2012)
Author:    Grigory Zarubin, Andrey Sidorov, Sergey Glagolev (Shogo.RU)


Различный функционал, используемый на наших сайтах.
*/

//-----------------------------------------------------------------------------
// PNG IE6 Fix
//-----------------------------------------------------------------------------
var msie6 = !!(jQuery.browser.msie && (jQuery.browser.version && jQuery.browser.version < 7 || /MSIE 6.0/.test(navigator.userAgent)));
var bgFixer = function(el, type) {
  var tmp = el.currentStyle.backgroundImage.match(/url\(['"]?(.+\.png)['"]?\)/i);
  if(tmp && msie6) {
    tmp = tmp[1];
    if(!tmp.match(/http:\/\//) && jQuery('head base').length) tmp = '/' + tmp;
    if(el.currentStyle.width=='auto') jQuery(el).css('width', jQuery(el).width() + 'px');
    if(el.currentStyle.height=='auto') jQuery(el).css('height', jQuery(el).height() + 'px');
    el.runtimeStyle.backgroundImage = 'none';
    el.runtimeStyle.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + tmp + '",sizingMethod="' + type + '")';
    jQuery(el).find('a').css('position', 'relative');
  }
};
function fixBgPNG_c(el) { bgFixer(el, 'crop'); }
function fixBgPNG_s(el) { bgFixer(el, 'scale'); }

jQuery(function() {
  var fix = function(el) {
    var tmp = jQuery(el).attr('src');
    if(!jQuery(el).attr('width')) jQuery(el).attr('width', jQuery(el).width());
    if(!jQuery(el).attr('height')) jQuery(el).attr('height', jQuery(el).height());
    if(!tmp.match(/http:\/\//) && jQuery('head base').length) tmp = '/' + tmp;
    jQuery(el).attr('src', 'i/sp.gif');
    el.runtimeStyle.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + tmp + '",sizingMethod="crop")';
  };

  // Для всех ослов
  if(jQuery.browser.msie) {
    jQuery('img.pngfix_all[src$=".png"], input.pngfix_all[src$=".png"]').each(function() {
      fix(this);
    });
  }

  if(msie6) {
    jQuery('img.pngfix[src$=".png"], input.pngfix[src$=".png"]').each(function() {
      fix(this);
    });
  }
});
//-----------------------------------------------------------------------------
// this_url
//-----------------------------------------------------------------------------
var this_url = document.location.href.replace(/#.*$/, '')+(document.location.href.match(/\?/) == null ? '?' : '&' )+'$js='+(new Date()).valueOf();
//-----------------------------------------------------------------------------
// Highslide Gallery
//-----------------------------------------------------------------------------
jQuery(function()
{
  if('hs' in window && hs.expand) {
    jQuery('a.highslide').click(function(e) {
      e.preventDefault();
      hs.expand(this);
    });
  }
});
//-----------------------------------------------------------------------------
// Element's Check
//-----------------------------------------------------------------------------
var gbi = function(el) { return !!document.getElementById(el); };
//-----------------------------------------------------------------------------
// $HAR(resp)
//-----------------------------------------------------------------------------
function $HAR(resp) // (handle ajax response) стандартный вывод сообщений об ошибках
{
  var result = false;
  var defmsg = 'Произошла неизвестная ошибка!';
  if( resp && resp.status )
  {
    switch( resp.status )
    {
      case 'ok':
        result = true;
        break;

      case 'error':
        alert(resp.message || defmsg);
        break;

      default:
        alert(defmsg);
    }
    if( resp.evaluate )
      eval(resp.evaluate);
  }
  return result;
}
//-----------------------------------------------------------------------------
// check_required(required)
//-----------------------------------------------------------------------------
function check_required(required)
{
  for( var i = 0, l = required.length; i < l; i++ )
  {
    if( !jQuery('#'+required[i]['id']).val().replace(/^\s+/, '').replace(/\s+$/, '').length )
    {
      alert(required[i]['name']+' не может быть пустым!');
      jQuery('#'+required[i]['id']).focus();
      return false;
    }
    if( required[i]['email'] && !jQuery('#'+required[i]['id']).val().match(/\S+@\S+\.\S+/) )
    {
      alert(required[i]['name']+' содержит некорректное значение!');
      jQuery('#'+required[i]['id']).focus();
      return false;
    }
    if( required[i]['numeric'] && !jQuery('#'+required[i]['id']).val().match(/[\d]+/) )
    {
      alert(required[i]['name']+' содержит некорректное значение!');
      jQuery('#'+required[i]['id']).focus();
      return false;
    }
    if( required[i]['group_1_2_3'] && !jQuery('#'+required[i]['id']).val().match(/(1|2|3)(,(1|2|3))*/) )
    {
      alert(required[i]['name']+' содержит некорректное значение!');
      jQuery('#'+required[i]['id']).focus();
      return false;
    }
  }
  return true;
}
//-----------------------------------------------------------------------------
// number_format
//-----------------------------------------------------------------------------
function number_format( number, decimals, dec_point, thousands_sep ) {
  var i, j, kw, kd, km;

  // input sanitation & defaults
  if( isNaN(decimals = Math.abs(decimals)) ){
    decimals = 0;//or 2 or 3...после запятой
  }
  if( dec_point == undefined ){
    dec_point = ",";
  }
  if( thousands_sep == undefined ){
    thousands_sep = " ";
  }

  i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

  if( (j = i.length) > 3 ){
    j = j % 3;
  } else{
    j = 0;
  }

  km = (j ? i.substr(0, j) + thousands_sep : "");
  kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
  //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
  kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

  return km + kw + kd;
}
//-----------------------------------------------------------------------------
// Input Fields Universal Placeholder & Checker
//-----------------------------------------------------------------------------
jQuery(function() {
  jQuery('.fbhandler-activate').each(function() {
    var eid = jQuery(this).attr('id');
    if(!eid) return;

    var isPlaceholderSupported = 'placeholder' in document.createElement('input') && 'placeholder' in document.createElement('textarea'),
        cid = '#' + eid,
        inp = jQuery('input, textarea', cid).not('[type=hidden], :radio, :checkbox, :submit, :image, :reset, :button, :file, .fbhandler-exclude');

    inp.each(function() {
      var placeholder = jQuery(this).attr('title') || '';

      if(isPlaceholderSupported) {
        jQuery(this).removeAttr('title').attr('placeholder', placeholder); // хак для сохранения валидности документа
      } else {
        // Хитро клонируем инпуты с паролями, чтобы для них тоже показывать подсказки :)
        if(jQuery(this).is(':password')) {
          // Для получения правильных координат сам элемент равно как и его родители должны быть не скрыты
          var getCoords = function(el) {
            var hidden = null;
            if(jQuery(el).is(':hidden')) {
              hidden = jQuery(el).css('display')=='none' ? jQuery(el) : jQuery(el).parents(':hidden', document.body).map(function() {
                return jQuery(this).css('display')=='none' ? this : null;
              });
            }
            if(hidden) hidden.show();
            var coords = jQuery(el).position();
            if(hidden) hidden.hide();
            return coords;
          };

          var pid = jQuery(this).attr('id'),
              pname = jQuery(this).attr('name');
          jQuery(this).after(jQuery(this).clone().css({
              'position' : 'absolute',
              'zIndex' : 100,
              'top' : -10000,
              'left' : -10000,
              'display' : 'none'
            }).wrap('<div />').parent()[0].innerHTML.replace(/type=['"]?password['"]?/i, 'type="text"')).next().attr({
              'value' : placeholder,
              'tabindex' : -1,
              'autocomplete' : 'off'
            }).on('focus', function() {
              jQuery(this).hide().prev().trigger('focus');
          });
          if(pid) jQuery(this).next().attr('id', pid.replace(/\[/g, '-').replace(/\]/g, '') + '-hint');
          if(pname) jQuery(this).next().attr('name', pname.replace(/\[/g, '-').replace(/\]/g, '') + '-hint');

          // Узнаём координаты после загрузки всех картинок и фиксим их при ресайзе окна (сам метод будет глобальным, для простого вызова при необходимости)
          window.fbhandlerUpdate = function(el) {
            if(!el) return;
            var coords = getCoords(el);
            jQuery(el).next().css({
              'top'  : coords.top,
              'left' : coords.left,
              'width' : jQuery(el).width()
            });
          };

          var self = this;
          jQuery(window).on('load resize', function() {
            fbhandlerUpdate(self);
          });

          // Глобальный обсервер подсказок для полей с паролями (чтобы при использовании всяких автозаполнялок и менеджеров паролей всё хорошо было)
          if(!('fbhandlerObserverElements' in window)) {
            window.fbhandlerObserverElements = jQuery('.fbhandler-activate').find(':password:not(.fbhandler-exclude)');
            setInterval(function() {
              fbhandlerObserverElements.each(function() {
                if(jQuery(this).val() && jQuery(this).val()!=jQuery(this).attr('title')) {
                  jQuery(this).next().hide();
                } else {
                  if(jQuery(this).is(':not(:focus)')) jQuery(this).next().show();
                }
              });
            }, 111);
          }
        }

        if(!jQuery(this).val()) jQuery(this).val(placeholder);
        jQuery(this).on('focus', function() {
          if(jQuery(this).val()==placeholder) jQuery(this).val('');
          if(jQuery(this).is(':password')) jQuery(this).next().hide();
        }).on('blur', function() {
          if(!jQuery(this).val() || jQuery(this).val()==placeholder) {
            jQuery(this).val(placeholder);
            if(jQuery(this).is(':password')) jQuery(this).next().show();
          }
        });
      }
    });

    jQuery('form', cid).on('submit', function() {
      var check = false;
      inp.each(function() {
        if(!jQuery(this).val() || jQuery(this).val()==jQuery(this).attr(isPlaceholderSupported ? 'placeholder' : 'title')) check = true;
      });
      return !check;
    });

    var pseudo_submit = jQuery('form a:has(img)', cid);
    if(pseudo_submit.length) {
      pseudo_submit.on('click', function(e) {
        e.preventDefault();
        jQuery('form', cid).trigger('submit');
      });
    }
  });
});
//-----------------------------------------------------------------------------
// Advanced preventDefault()
// USAGE: if($.preventDefaultEvent(e)) return;
//-----------------------------------------------------------------------------
jQuery.preventDefaultEvent = function(e, options) {
  options = options || {shift:1, ctrl:1, alt:1, meta:1};
  var href = e.currentTarget.getAttribute('href');
  if(((options.shift && e.shiftKey)
      || (options.alt && e.altKey)
      || (options.ctrl && e.ctrlKey)
      || (options.meta && e.metaKey))
      && href && href.indexOf('#') != 0
      && href.indexOf('javascript:') != 0
  ) return true;
  e.preventDefault();
  return false;
};
//-----------------------------------------------------------------------------
// Some strings extensions
//-----------------------------------------------------------------------------
String.prototype.find = function(string)
{
  return (this.indexOf(string) !== -1 ? true : false);
};
//-----------------------------------------------------------------------------
// Scroll to selector
//-----------------------------------------------------------------------------
function scroll_to(selector, speed)
{
  var destination = jQuery(selector).offset().top;
  jQuery(jQuery.browser.webkit ? document.body : 'html').animate({scrollTop: destination}, speed ? speed : 0);
}
//-----------------------------------------------------------------------------
// Backward compatibility
//-----------------------------------------------------------------------------
jQuery(function() {
  if(jQuery.fn.unifloat) {
    jQuery.menu = function() { // for jQuery.menu
      var selector = arguments[0],
          settings = arguments[1] || {};
      if(settings.mask) {
        settings.rel = settings.mask;
        delete(settings.mask);
      }
      if(settings.show_prepare) {
        settings.onHover = settings.show_prepare;
        delete(settings.show_prepare);
      }
      if(settings.show_ready) {
        settings.onShow = settings.show_ready;
        delete(settings.show_ready);
      }
      if(settings.hide_callback) {
        settings.onHide = settings.hide_callback;
        delete(settings.hide_callback);
      }
      return jQuery(selector).unifloat(settings);
    };
    jQuery.pos = function() { // for jQuery.pos
      var source = arguments[0],
          target = arguments[1],
          posTop = arguments[2] || {},
          posLeft = arguments[3] || {},
          relative = !arguments[4] || false;
      if(typeof source==='string') source = $((source.match(/#/) ? '' : '#') + source);
      if(typeof target==='string') target = $((target.match(/#/) ? '' : '#') + target);
      return jQuery(target).unifloat('pos', {
        rel: source,
        posTop: posTop,
        posLeft: posLeft,
        manipulation: relative
      });
    };
    jQuery.showpos = function() { // for jQuery.showpos
      var source = arguments[0],
          target = arguments[1],
          posTop = arguments[2] || {},
          posLeft = arguments[3] || {},
          relative = !arguments[4] || false,
          effect = arguments[5]===undefined ? 'fast' : arguments[5],
          callback = arguments[6] || jQuery.noop;
      if(typeof source==='string') source = $((source.match(/#/) ? '' : '#') + source);
      if(typeof target==='string') target = $((target.match(/#/) ? '' : '#') + target);
      return jQuery(target).unifloat('show', {
        rel: source,
        posTop: posTop,
        posLeft: posLeft,
        manipulation: relative,
        effect: effect,
        onShow: callback
      });
    };
  }
});
//-----------------------------------------------------------------------------