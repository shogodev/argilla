/*
Name:      Extended JavaScript Loader & Image Cacher
Use with:  jQuery
Version:   1.0.1 (14.07.2011)
Author:    Grigory Zarubin (Shogo.RU)


Асинхронно подгружает внешние яваскрипты:
$.loadScript(
  url                                   // урл
                                        // ИЛИ
  ['url1', 'url2', 'url3'],             // массив урлов скриптов
  {
    charset    : 'utf-8',               // кодировка скрипта (используется при несовпадении с основной кодировкой страницы)
    modify     : {                      // готовые модификаторы (можно активировать как один, так и некий набор)

      content  : '#container'           // блокирует все кривые методы document.open/close/write/writeln и самостоятельно выводит результат работы скрипта
                                           (значением параметра должен быть jquery-селектор, возвращающий элемент, в который будет происходить вывод)
                                           ВНИМАНИЕ! Обязательно включайте этот модификатор, если в подгружаемом скрипте используются указанные выше методы!

    },
    onComplete :                        // коллбэк-функция или массив функций, вызываемых после успешной загрузки ВСЕХ скриптов
      function(data, status, jqXHR),
    onError    :                        // коллбэк-функция или массив функций, вызываемых после попыток загрузки ВСЕХ скриптов в случае какой-либо ошибки
      function(jqXHR, status, error)
  }
);

Кэширует картинки:
$.cacheImage(
  url                                   // урл
                                        // ИЛИ
  ['url1', 'url2', 'url3'],             // массив урлов картинок
  {
    onComplete :                        // коллбэк-функция или массив функций, вызываемых после успешной загрузки ВСЕХ картинок
      function(data, status, jqXHR),
    onError    :                        // коллбэк-функция или массив функций, вызываемых после попыток загрузки ВСЕХ картинок в случае, если хотя бы одна картинка не загрузилась
      function(jqXHR, status, error)
  }
);
*/

;(function($) {
  // Общий подгрузчик
  $.loadData = function(url, opts, onComplete, onError) {
    var urls = [];
    if($.type(url)==='string') urls.push(url); else urls = url;

    if(!urls.length) {
      if($.type(onError)==='function') onError({}, 'error', 'no items to load');
      return;
    }

    // Функция, создающая нужное число асинхронных запросов
    var getData = function() {
      var requests = [];
      for(var i=0,l=urls.length; i<l; i++) requests.push($.ajax($.extend({}, {url: urls[i]}, opts)));
      return requests;
    };

    $.when.apply(null, getData()).then(onComplete, onError);
  };



  // Асинхронно подгружает внешние яваскрипты
  $.loadScript = function(url, options) {
    if(!url) return;
    var opts = $.extend(true, {}, $.loadScript.defaults, options);

    var modify = $.loadScript.modify(url, opts); // создаем обработчик модификаторов
    for(var i in opts.modify) modify.prepare(i); // модификаторы: подготовка перед запросом

    var completes = [], errors = [];
    completes.push(function() {
      for(var i in opts.modify) modify.process(i); // модификаторы: непосредственно сам процесс)
    });
    errors.push(function() {
      for(var i in opts.modify) modify.fallback(i); // модификаторы: отмена действий при ошибке загрузки
    });
    if($.isArray(opts.onComplete)) $.merge(completes, opts.onComplete); else completes.push(opts.onComplete);
    if($.isArray(opts.onError)) $.merge(errors, opts.onError); else errors.push(opts.onError);

    $.loadData(url, { dataType : 'script', scriptCharset : opts.charset }, completes, errors);
    return $;
  };

  // Обёртка для запуска модификаторов
  $.loadScript.modify = function(url, opts) {
    var context = {}, // эта переменная будет уникальна и замкнута на все методы возвращаемого объекта
        modify = {    // вынес в отдельную переменную, чтобы запускать cleanup из других методов
          // Подготовка
          prepare: function(i) {
            var val = opts.modify[i];
            switch(i) {
              case 'content':
                context.stack = [];
                context.runtime = {};
                // Пусть лучше будут все в отдельном месте
                $.each(['write', 'writeln', 'open', 'close'], function(index, value) {
                  context.runtime[value] = document[value];
                });
                document.write = function() {
                  // Аргументов может быть несколько: http://www.w3schools.com/jsref/met_doc_write.asp
                  context.stack.push([].slice.call(arguments).join(''));
                };
                document.writeln = function() {
                  // Перенос на всякий случай будем добавлять, чтобы по стандарту было: http://www.w3schools.com/jsref/met_doc_writeln.asp
                  context.stack.push([].slice.call(arguments).join('')+'\r\n');
                };
                document.open = $.noop;
                document.close = $.noop;
              break;
            }
          },
          // Отмена
          fallback: function(i) {
            var val = opts.modify[i];
            switch(i) {
              case 'content':
              break;
            }
            modify.cleanup(i);
          },
          // Отработка
          process: function(i) {
            var val = opts.modify[i];
            switch(i) {
              case 'content':
                $(val).html(context.stack.join(''));
              break;
            }
            modify.cleanup(i);
          },
          // Сброс возможных перехватчиков (вынес сюда, дабы избежать дублирования кода в трёх местах выше)
          cleanup: function(i) {
            switch(i) {
              case 'content':
                if('runtime' in context) {
                  $.each(context.runtime, function(index, value) {
                    document[index] = value;
                  });
                }
              break;
            }
          }
        };
    return modify;
  };

  $.loadScript.defaults = {
    charset    : 'windows-1251',
    modify     : {},
    onComplete : $.noop,
    onError    : $.noop
  };



  // Кэширует картинки
  $.cacheImage = function(url, options) {
    if(!url) return;

    // Позаботимся об обратной совместимости
    var options_ = {};
    if($.isFunction(options)) options_.onComplete = options; else options_ = options;

    var opts = $.extend({}, $.cacheImage.defaults, options_);

    $.loadData(url, { dataType : 'image' }, opts.onComplete, opts.onError);
    return $;
  };

  // Простенький транспорт для подгрузки картинок
  jQuery.ajaxTransport('image', function(s) {
    if(s.type === 'GET' && s.async) {
      var image;
      return {
        send: function(_, callback) {
          image = new Image();

          function done(status) {
            if(image) {
              var textStatus = (status == 200) ? 'success' : 'image not found',
                  tmp = image;
              image = image.onreadystatechange = image.onerror = image.onload = null;
              callback(status, textStatus, { image : tmp });
            }
          }

          image.onreadystatechange = image.onload = function() {
            done(200);
          };
          image.onerror = function() {
            done(404);
          };

          image.src = s.url;
        },

        abort: function() {
          if(image) {
            image = image.onreadystatechange = image.onerror = image.onload = null;
          }
        }
      };
    }
  });

  $.cacheImage.defaults = {
    onComplete : $.noop,
    onError    : $.noop
  };
})(jQuery);