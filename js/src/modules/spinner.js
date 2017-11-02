import { $DOCUMENT } from './globals';

$DOCUMENT.on('mousedown.js-spinner', '.js-spinner-down, .js-spinner-up', function() {
  const $spinner_control = $(this);
  const $input = $spinner_control.siblings('.inp');
  const step = $input.data('step') ? $input.data('step') : 1;
  const may_be_zero = $input.data('zero') ? $input.data('zero') : false;
  let value = parseInt($input.val(), 10);
  let inc_timeout, inc_interval, dec_timeout, dec_interval;

  $spinner_control
    .on('mouseup.js-spinner', clearAll)
    .on('mouseleave.js-spinner', $spinner_control, clearAll);

  if ($spinner_control.hasClass('js-spinner-down')) {
    decVal(); dec_timeout = setTimeout(() => {
      dec_interval = setInterval(decVal, 70);
    }, 300);
  }

  if ($spinner_control.hasClass('js-spinner-up')) {
    incVal(); inc_timeout = setTimeout(() => {
      inc_interval = setInterval(incVal, 70);
    }, 300);
  }

  function incVal() {
    if ($.isMouseLoaderActive()) return;

    value = value + step;
    $input.val(value).change();
  }

  function decVal() {
    if ($.isMouseLoaderActive()) return;

    if (may_be_zero) {
      if (value >= step) {
        value = value - step;
        $input.val(value).change();
      }
    } else {
      if (value > step) {
        value = value - step;
        $input.val(value).change();
      }
    }
  }

  function clearAll() {
    clearTimeout(dec_timeout); clearInterval(dec_interval);
    clearTimeout(inc_timeout); clearInterval(inc_interval);
  }
});

$DOCUMENT.on('keydown', '.js-spinner .inp', function(e) {
  const $input = $(this);

  if (
    e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 27
    || (e.keyCode == 65 && e.ctrlKey === true)
    || (e.keyCode >= 35 && e.keyCode <= 39)
  ) {
    return;
  } else {
    if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  }
});

$DOCUMENT.on('keyup paste', '.js-spinner .inp', function(e) {
  const $input = $(this);
  const may_be_zero = $input.data('zero') ? $input.data('zero') : false;

  if (!may_be_zero && $input.val() === 0) {
    $input.val(1);
  }
});
