import { IS_DESKTOP } from './globals';

(function() {

  $('input[type="tel"]').mask('+7 (999) 999-99-99', {
    autoclear: false,
  });

  if (IS_DESKTOP) {
    $('input[type="date"]').attr('type', 'text');

    // Date
    $('.js-date-mask').mask('99/99/9999', {
      placeholder: 'дд.мм.гггг',
      autoclear: false,
    });

    // Time
    $('.js-time-mask').mask('99:99', {
      placeholder: 'чч:мм',
      autoclear: false,
    });
  }

})();
