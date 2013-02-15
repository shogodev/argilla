(function($) {

  var methods = {

    init : function(options)
    {
      return this.each(function()
      {
        methods.createOnFlyEditing(this, options);
      });
    },

    createOnFlyEditing : function(elem, options)
    {
      $(elem).addClass('onfly-edit');

      $(elem).on('click', function(e)
      {
        e.stopPropagation();
        methods.buildInput(this, options);
      });
    },

    buildInput : function(elem, options)
    {
      if(elem.previousSibling &&
         elem.previousSibling.className &&
         elem.previousSibling.className == 'onfly-edit-fixed'
        )
        return false;

      var inp              = document.createElement('textarea');
      inp.innerHTML        = elem.innerHTML.replace(/<br>|<br \/>/g, '\n');
      inp.className        = 'onfly-edit-fixed';
      inp.style.fontFamily = $(elem).css('fontFamily');
      inp.style.fontSize   = $(elem).css('fontSize');
      inp.style.lineHeight = $(elem).css('lineHeight');

      var old_text = $(inp).text();
      if( $(inp).text() === '[не задано]' )
        $(inp).text('');

      elem.parentNode.insertBefore(inp, elem);

      $(window).on('click.onfly', function(e)
      {
        if( $(e.target).hasClass('onfly-edit-fixed') )
          return false;

        methods.removeInput(elem, inp, options);
        $(this).unbind(e);
      });

      $(inp).on('keydown', function(e)
      {
        // Переход на новую строку при ctrl+enter или shift+enter
        if( (e.ctrlKey || e.shiftKey) && e.keyCode == 13 )
          inp.value += "\n";

        // Тут будем на лету проверять нажатую клавишу регуляркой (строчные и прописные буквы НЕ РАЗЛИЧАЮТСЯ)
        if( options && options.check )
        {
          for( var i in options.check )
          {
            var regexp = new RegExp(i, '');
            if( options.check[i] && $.isFunction(options.check[i]) && String.fromCharCode(e.keyCode).match(regexp) )
            {
              options.check[i]();
              return false;
            }
          }
        }

        if( !e.ctrlKey && !e.altKey && !e.shiftKey )
        {
          switch(e.which)
          {
            // esc
            case 27:
              methods.removeInput(elem, inp, options);
            break;

            //tab
            case 9:
              methods.removeInput(elem, inp, options);

              if( options && options.tab_key )
              {
                e.preventDefault();
                methods.tab_key($(elem));
              }
            break;

            // enter
            case 10:
            case 13:
              methods.submitInput(elem, inp, options);

              if( options && options.apply && $.isFunction(options.apply) )
                options.apply(elem, old_text);

              if( options && options.enter_key )
              {
                e.preventDefault();
                methods.enter_key($(elem));
              }
            break;

            // cursors
            case 37:
            case 38:
            case 39:
            case 40:
              if( options && options.arrow_key )
              {
                e.preventDefault();
                methods.removeInput(elem, inp, options);

                var key = {37: 'left', 38: 'up', 39: 'right', 40: 'down'};
                methods.arrow_key($(elem), key[e.which]);
              }
            break;
          }
        }
      });

      $(elem).hide();
      try
      {
        inp.focus();
      } catch(err) {};

      if( options && options.oncreate && $.isFunction(options.oncreate) )
        options.oncreate(inp);
    },

    submitInput : function(elem, inp, options)
    {
      var oldval = elem.innerHTML;

      if( !inp.value || !$.trim(inp.value) )
      {
        methods.removeInput(elem, inp, options);
        return null;
      }

      elem.innerHTML = inp.value;
      elem.innerHTML = elem.innerHTML.replace(/\n/g, '<br />');
      inp.value      = '';

      methods.removeInput(elem, inp, options);
    },

    removeInput : function(elem, inp, options)
    {
      if( options && options.ondestroy && $.isFunction(options.ondestroy) )
        options.ondestroy(inp);

      $(elem).show();
      $(inp).remove();
      $(window).off('.onfly');
    },

    arrow_key : function(elem, key)
    {
      var tr = $(elem).parents('tr');
      var td = $(elem).parents('td');
      var index = $(tr).find('td').index(td);

      switch(key)
      {
        case 'up':
          var up_tr   = $(tr).prev('tr');
          var cur_td  = $(up_tr).find('td').get(index);
        break;

        case 'down':
          var down_tr = $(tr).next('tr');
          var cur_td  = $(down_tr).find('td').get(index);
        break;

        case 'right':
          var right_td = $(tr).find('td').get(index + 1);
          var cur_td   = $(right_td);
        break;

        case 'left':
          var left_td = $(tr).find('td').get(index - 1);
          var cur_td  = $(left_td);
        break;

      }

      $(cur_td).find('.onfly-edit').click();
      $(cur_td).find('.onfly-edit').prev('textarea').focus().select();
    },

    enter_key : function(elem)
    {
      methods.arrow_key(elem, 'down');
    },

    tab_key : function(elem)
    {
      methods.arrow_key(elem, 'right');
    }
  };

  $.fn.onfly = function( method )
  {
    if( methods[method] )
    {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    }
    else if( $.isPlainObject(method) || ! method )
    {
      return methods.init.apply(this, arguments);
    }
    else
    {
      $.error('Method ' + method + ' does not exist on jQuery.onfly');
    }
  };
})(jQuery);