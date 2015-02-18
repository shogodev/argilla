/**
 * Обертка для alertify ( http://fabien-d.github.io/alertify.js/ )
 *
 * С настройками и коллбеком
 * dialog('alert', 'Сообщение...', { labels: { ok: 'Ok'} }, function() {});
 *
 * С коллбеком и настройками
 * dialog('alert', 'Сообщение...', function() {}, { labels: { ok: 'Ok'} });
 *
 * С настройками
 * dialog('alert', 'Сообщение...', { labels: { ok: 'Ok'} });
 *
 * С колбеком
 * dialog('alert', 'Сообщение...', function() {});
 *
 * Без настроек и коллбека
 * dialog('alert', 'Сообщение...');
 */

;(function(window) {

  window.ALERTIFY_DEFAULTS = {
    buttonReverse: true,
    labels : {
      ok : 'Да',
      cancel : 'Нет'
    }
  };

  window.dialog = function(type, msg) {
    var opts, cb;

    if (arguments.length > 2) {

      if (typeof arguments[2] === 'object') {
        opts = arguments[2];
        cb = arguments[3] || function() {};
      } else if (typeof arguments[2] === 'function') {
        cb = arguments[2];
        opts = arguments[3] || {};
      }
    }

    alertify.set(_extend(_clone(ALERTIFY_DEFAULTS), opts));
    alertify[type](msg, cb, opts);
    return alertify.set(ALERTIFY_DEFAULTS);
  };

  function _clone(obj) {
    if (obj === null || typeof obj !== 'object') {
      return obj;
    }

    var temp = obj.constructor();

    for (var key in obj) {
      temp[key] = _clone(obj[key]);
    }

    return temp;
  }

  function _extend(dest, src) {
    for (var prop in src) {
      if (src[prop] && src[prop].constructor && src[prop].constructor === Object) {
        dest[prop] = dest[prop] || {};
        arguments.callee(dest[prop], src[prop]);
      } else {
        dest[prop] = src[prop];
      }
    }

    return dest;
  };

  alertify.set(ALERTIFY_DEFAULTS);

}(window));
