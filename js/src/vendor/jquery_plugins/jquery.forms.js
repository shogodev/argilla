/* Custom form elements */
/* Created by Vladimir Khodakov (web-interface.info team) */
/* Modified by Grigory Zarubin (Shogo.Ru) */

;(function($) {
  $.fn.forms = function(options) {
    var opt = $.extend({}, $.fn.forms.defaults, options);

    return this.each(function() {
      var el = $(this);
      el.type = el.attr('type');
      if(el.is('input') && opt[el.type] && !el.next().hasClass(el.type)) {
        var tname = el.attr('name');
        var tid = el.attr('id');
        el.name = (tname !== undefined && tname != '') ? tname.replace(/[\[\]]/g, '') : ((tid !== undefined && tid != '') ? tid.replace(/[\[\]]/g, '') : (Math.round(Math.random()*10000)+1));
        el.replace = (el.type != 'file') ? '<span class="'+el.type+' el-name-'+el.name+'"></span>' : '<span class="'+el.type+'"><span class="input"></span><span class="button">'+opt.file_bt+'</span></span>';
        el.handle = el.after(el.replace);
        el.hide();
        switch(el.type) {
          case 'checkbox':
            if(el.prop('checked')) {
              el.next().addClass('check_'+el.type);
            }
            if(el.prop('disabled')) {
              el.next().addClass('disabled_'+el.type);
            }
            el.next().click(function() {
              if (!el.prop('disabled')) {
                $(this).toggleClass('check_'+el.type);
                if(!el.prop('checked')) {
                  el.prop('checked', true).trigger('change');
                } else {
                  el.prop('checked', false).trigger('change');
                }
              }
            });
            el.click(function() {
              if (!el.prop('disabled')) {
                el.next().toggleClass('check_'+el.type);
              }
            });
            $('label[for="' + tid + '"]').click(function(e) {
              if (!el.prop('disabled')) {
                e.preventDefault();
                var inp = $('#' + $(this).attr('for'));
                inp.next().toggleClass('check_'+el.type);
                if(!inp.prop('checked')) {
                  inp.prop('checked', true).trigger('change');
                } else {
                  inp.prop('checked', false).trigger('change');
                }
              }
            });
            break;
          case 'radio':
            if(el.prop('checked')) {
              el.next().addClass('check_'+el.type);
            }
            if(el.prop('disabled')) {
              el.next().addClass('disabled_'+el.type);
            }
            el.next().click(function() {
              if(!el.prop('checked') && !el.prop('disabled')) {
                $('.el-name-'+el.name).removeClass('check_'+el.type);
                el.prop('checked', true).trigger('change');
                $(this).addClass('check_'+el.type);
              }
            });
            el.click(function() {
              if (!el.prop('disabled')) {
                $('.el-name-'+el.name).removeClass('check_'+el.type);
                el.prop('checked', true).trigger('change');
                el.next().addClass('check_'+el.type);
              }
            });
            el.on('change', function() {
              var element = $(this);
              if (!element.prop('disabled')) {
                if( !element.prop('checked') )
                  element.next().removeClass('check_' + element.attr('type'));
              }
            });
            break;
          case 'file':
            el.id = el.attr('id');
            el.show();
            el.emulate = el.next();
            el.emulate.append(el);
            el.emulate.css({'position':'relative', 'overflow':'hidden'});
            el.emulate.children('input').css({'opacity':'0', 'font-size':'199px', 'top':'0', 'left':'0', 'position':'absolute', 'cursor':'pointer', 'padding':'0', 'margin':'0 0 0 -550px', 'border':'none', 'z-index':'10000', 'background':'#000000', 'direction':'rtl'});
            el.emulate.find('input').change(function() {
              el.emulate.find('.input').html($(this).val());
            });
            break;
        }
      }
    });
  };

  $.fn.forms.defaults = {
    checkbox : true,
    radio    : true,
    file     : true,
    file_bt  : 'Обзор'
  };
})(jQuery);