/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection.assets
 */

;(function($) {

  var collectionSettings = {};

  $.fn.collection = function(keyCollection, inputSettings) {
    /**
     * settings
     * ajaxUrl - урл сабмита по умолчвнию
     * ajaxUpdate[] - масссив id элементов которые будут обновлены после сабмита
     * classWaitAction - класс элемента ожидиющего действия
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
    };

/*    this.addButtonListener = function(keyCollection)
    {
       var self = this;
       var settings = this.collectionSettings[keyCollection];

       $('body').on('click', '.' + settings.classAdd , function(e){
         e.preventDefault();

        if( $(this).hasClass(settings.classDoNotAddInCollection) )
          return;

        var options = {
          'action' : 'add',
          'element' : $(this),
          'data' : $(this).data(),
          'url' : settings.addButtonAjaxUrl
        };

         self.send(options);
      });
    }*/

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
