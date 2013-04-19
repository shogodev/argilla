Backend.modules.onFly = function(box) {
  /**
   * Инициализация onfly'ев.
   *
   * @param {function} $ jQuery функция.
   */
  box.init = function($) {
    // Привязать текстовые поля.
    bindTextFields($);

    // Привязать drop down'ы.
    $('body').on('change', 'select.onfly-edit-dropdown', function(_, elem) {
      handler($, elem);
    });
  };

  /**
   * Востанавливает привязки событий для текстовых полей.
   *
   * @param {function} $ jQuery функция.
   */
  box.reinstall = function($) {
    bindTextFields($);
  };

  var bindTextFields = function($) {
    $('.onfly-edit').onfly({apply: function(elem, oldText) {
      handler($, elem, oldText);
    }});
  };

  var handler = function($, elem, oldText) {
    var wrappedElem = $(elem),
        gridId = wrappedElem.data('grid-id'),
        ajaxUrl = wrappedElem.data('ajax-url'),
        matches = wrappedElem.data('onflyedit').match(/(\w+)-(\d+)/),
        data = {};

    data.action = 'onflyedit';
    data.field = matches[1];
    data.id = matches[2];
    data.gridId = gridId;
    data.value = wrappedElem.is("span") ? wrappedElem.text() : wrappedElem.val(); // Проверяем текстовое поле или список.

    $.post(ajaxUrl, data, '', 'json')
      .done(function(resp) {
        if (data.value == resp)
        {
          wrappedElem.removeClass('text-error');
        }
        else
        {
          reportError(wrappedElem, oldText);
        }
      })
      .fail(function(xhr) {
        ajaxUpdateError(xhr);
        reportError(wrappedElem, oldText);
      });
  };

  var reportError = function(elem, oldText)
  {
    elem.addClass('text-error');
    if (elem.is("span") && oldText)
    {
      elem.text(oldText);
    }
  }
};