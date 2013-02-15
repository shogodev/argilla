/*!
 * Input Extender
 * Bind to <input> some actions
 *
 * @requires jQuery v1.7 or newer
 *
 * @author Grigory Zarubin
 * @link https://github.com/Craigy-
 * @version 1.0.0
 * @date 22.08.2012
 *
 */

(function($) {
  var extender = {
    init: function(options) {
      var opts = $.extend(true, {}, extender.defaults, options);

      return this.each(function() {
        var data = $(this).data().extender;
        if(data) {
          data = data.split(',');
          for(var i=0,l=data.length; i<l; i++) {
            extender[$.trim(data[i])].call(this, opts);
          }
        }
      });
    },


    link: function(options) {
      if(!$(this).data('extended-link')) {
        $(this).data('extended-link', options);

        var self = this,
            link = $('<a href="#" class="extender-button extender-link-link" rel="tooltip" title="Обернуть текст в ссылку"></a>'),
            unlink = $('<a href="#" class="extender-button extender-link-unlink" rel="tooltip" title="Удалить ссылку"></a>');

        var inject = $().add(link).add(unlink);
        inject.insertAfter(this).wrapAll('<span class="extender-container" />');

        link.on('click', function(e) {
          e.preventDefault();

          var url = false;
          do {
            url = prompt('Введите URL:', 'http://');
          } while(url === 'http://');

          if(url && (url !== 'http://' && url !== 'https://' && url !== 'ftp://')) {
            $(self).val('<a href="' + url + '">' + $(self).val() + '<\/a>');
          }
        });

        unlink.on('click', function(e) {
          e.preventDefault();
          $(self).val($(self).val().replace(/<a[\s\S]*?>([\s\S]*?)<\/a>/gi, "$1"));
        });
      }
    },


    translit: function(options) {
      if(!$(this).data('extended-translit')) {
        $(this).data('extended-translit', options);

        var self = this,
            button = $('<a href="#" class="extender-button extender-translit-apply" rel="tooltip" title="Применить транслитерированный URL"></a>'),
            result = $('<span class="extender-translit-result">[]</span>');

        var inject = $().add(button).add(result),
            source = $(options.source || $(this).attr('data-source') || this),
            setTranslit = function(input) {
              result.html('[' + extender._transliteration($.trim($(input).val())) + ']');
            };

        inject.insertAfter(this).wrapAll('<span class="extender-container" />');
        setTranslit(source);

        source.on('keyup keydown keypress', function() {
          setTranslit(this);
        });

        button.on('click', function(e) {
          e.preventDefault();
          $(self).val(result.html().match(/^\[(.*)\]$/)[1]);
        });
      }
    },


    _transliteration: function(str) {
      var trans = {'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'jo','ж':'zh','з':'z','и':'i',
                   'й':'j','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f',
                   'х':'h','ц':'c','ч':'ch','ш':'sh','щ':'th','ъ':'','ь':'','ы':'y','э':'e','ю':'ju','я':'ya',
                   'a':'a','b':'b','c':'c','d':'d','e':'e','f':'f','g':'g','h':'h','i':'i','j':'j',
                   'k':'k','l':'l','m':'m','n':'n','o':'o','p':'p','q':'q','r':'r','s':'s','t':'t','u':'u','v':'v',' ':'_',
                   'w':'w','x':'x','y':'y','z':'z', '0':'0','1':'1','2':'2','3':'3','4':'4','5':'5','6':'6','7':'7','8':'8','9':'9'};

      var ext_replaces = {'iya':'ia'};

      str = str.toLowerCase();
      var new_str = '';
      for(var i=0,l=str.length; i<l; i++) {
        var tmp = str.charAt(i);
        if(trans[tmp]) {
          new_str += trans[tmp];
        } else {
          new_str += '';
        }
      }

      for(i in ext_replaces) {
        while(true)
        {
          tmp = new_str.replace(i,ext_replaces[i]);
          if(tmp==new_str) break;
          new_str = tmp;
        }
      }

      return new_str;
    },



    defaults: {}
  };


  $.fn.extender = function(options) {
    return extender.init.call(this, options);
  };
  $.translit_rus_en = extender._transliteration; // globalize
})(jQuery);