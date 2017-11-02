import MEDIA_QUERIES from '../media-queries-config.json';

// Const
// -----

export const IS_MOBILE = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) != null;
export const IS_DESKTOP = !IS_MOBILE;
export const IS_IOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false);
export const IS_IE = navigator.appVersion.indexOf("MSIE") !== -1 || navigator.userAgent.match(/Trident.*rv[ :]*11\./);
export const IS_TOUCH_DEVICE = 'ontouchstart' in document;

let HTML = document.documentElement;

HTML.classList.remove('no-js');

if (IS_MOBILE) HTML.classList.add('is-mobile');
if (IS_DESKTOP) HTML.classList.add('is-desktop');
if (IS_IOS) HTML.classList.add('is-ios');
if (IS_IE) HTML.classList.add('is-ie');
if (IS_TOUCH_DEVICE) HTML.classList.add('is-touch-device');

export const SMALL_MOBILE_WIDTH = MEDIA_QUERIES.mobile.small;
export const MOBILE_WIDTH = MEDIA_QUERIES.mobile.portrait;
export const LANDSCAPE_MOBILE_WIDTH = MEDIA_QUERIES.mobile.landscape;
export const PORTRAIT_TABLET_WIDTH = MEDIA_QUERIES.tablet.portrait;
export const TABLET_WIDTH = MEDIA_QUERIES.tablet.landscape;
export const SMALL_NOTEBOOK_WIDTH = MEDIA_QUERIES.notebook.small;
export const NOTEBOOK_WIDTH = MEDIA_QUERIES.notebook.normal;

export const HEADER_HEIGHT = $('.header').height();


// Selectors
// ---------

export const $WINDOW = $(window);
export const $DOCUMENT = $(document);
export const $HTML = $(document.documentElement);
export const $BODY = $(document.body);


// Tosrus default settings
// -----------------------

export const TOSRUS_DEFAULTS = {
  buttons: {
    next: true,
    prev: true
  },

  keys: {
    prev: 37,
    next: 39,
    close: 27
  },

  wrapper: {
    onClick: 'close'
  }
};
