import { $DOCUMENT } from './globals';

// Selectric
// ---------

$DOCUMENT.on('initSelectric yiiListViewUpdated', () => {
  $('select').selectric({
    disableOnMobile: false,
    nativeOnMobile: true,
  });
}).trigger('initSelectric');


// Checkboxes
// ----------

$('.checkbox input').on('change initCheckboxes', function() {
  const $inp = $(this);
  const $label = $inp.closest('.checkbox');

  if ($inp.prop('checked')) {
    $label.addClass('checked');
  } else {
    $label.removeClass('checked');
  }
}).trigger('initCheckboxes');


// Radio buttons
// -------------

$('.radio input').on('change initRadio', function() {
  const $inp = $(this);
  const $group = $('[name="' + $inp.attr('name') + '"]');
  const $labels = $group.closest('.radio');
  const $selected_item = $labels.find('input').filter(':checked').closest('.radio');

  $labels.removeClass('checked');
  $selected_item.addClass('checked');
}).trigger('initRadio');
