/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

;(function($) {

  var collectionSettings = {};

  $.fn.collection = function(keyCollection, inputSettings) {
    /**
     * settings
     * ajaxUrl - урл сабмита по умолчвнию
     * ajaxUpdate[] - масссив id элементов которые будут обновлены после сабмита
     */
    this.settings = {};

    this.debug = function() {
      console.log(keyCollection);
      console.log(this.settings);
    };

    /**
     * Отправка данных на сервер
     * @param options
     *  - action - действие
     *  - element - элемет инициатор события
     *  - data - данные для сабмита
     *  - url - альтернативный url сабмита
     */
    this.send = function(options) {
      var settings = this.settings;
      var url = (options.url !== undefined && options.url != '') ? options.url : settings.ajaxUrl;
      var data = {};
      data[keyCollection] = options.data;
      data['action'] = options.action;

      if( !$('#loader_ajax').is('show') )
        $.mouseLoader(true);
      else
        return;

      if( settings.beforeAjaxScript !== undefined && settings.beforeAjaxScript(options.element, options.data, options.action) === false )
        return;

      if( options.beforeAjaxScript !== undefined && options.beforeAjaxScript(options.element, options.data, options.action) === false )
        return;

      var jqxhr = $.post(url, data, function(response) {
        var content = $('<div></div>').append(response);

        for(i in settings.ajaxUpdate)
          $('#' + settings.ajaxUpdate[i]).replaceWith(content.find('#' + settings.ajaxUpdate[i]));

        if( settings.afterAjaxScript !== undefined )
          settings.afterAjaxScript(options.element, options.data, options.action, response);

        if( options.afterAjaxScript !== undefined )
          options.afterAjaxScript(options.element, options.data, options.action, response);
      });

      jqxhr.error(function(XHR, textStatus, errorThrown) {
        var err;
        if (XHR.readyState === 0 || XHR.status === 0) {
          return;
        }
        switch (textStatus) {
          case 'timeout':
            err = 'The request timed out!';
            break;
          case 'parsererror':
            err = 'Parser error!';
            break;
          case 'error':
            if (XHR.status && !/^\s*$/.test(XHR.status)) {
              err = 'Error ' + XHR.status;
            } else {
              err = 'Error';
            }
            if (XHR.responseText && !/^\s*$/.test(XHR.responseText)) {
              err = err + ': ' + XHR.responseText;
            }
            break;
        }

        if (settings.ajaxUpdateError !== undefined) {
          settings.ajaxUpdateError(XHR, textStatus, errorThrown, err);
        } else if (err) {
          alert(err);
        }
      });

      jqxhr.always(function() {
        $.mouseLoader(false);
      });

    };

    this.getElementsByData = function(data, selector)
    {
      if(data == undefined || data.id == undefined || data.type == undefined )
        return false;

      var elements = $((selector == undefined ? '' : selector) + '[data-id=' + data.id + '][data-type=' + data.type + ']');

      return elements.length > 0 ? elements : false;
    }

    if( inputSettings !== undefined )
    {
      collectionSettings[keyCollection] = inputSettings;
      this.settings = inputSettings;
    }
    else
    {
      this.settings = collectionSettings[keyCollection];
      return this;
    }

    return this;
  };

})(jQuery);
