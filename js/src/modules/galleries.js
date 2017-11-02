import TOSRUS_DEFAULTS from './globals';

(function() { 'use strict';

  // init tosrus static gallery
  $('.js-gallery').each(function() {
    $(this).find('.js-gallery-item').tosrus(TOSRUS_DEFAULTS);
  });

}());
