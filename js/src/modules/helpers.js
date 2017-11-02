import {
  $WINDOW,
  SMALL_MOBILE_WIDTH,
  MOBILE_WIDTH,
  LANDSCAPE_MOBILE_WIDTH,
  PORTRAIT_TABLET_WIDTH,
  TABLET_WIDTH,
  SMALL_NOTEBOOK_WIDTH,
  NOTEBOOK_WIDTH
} from './globals';

export let WINDOW_WIDTH = window.innerWidth || $WINDOW.width();
export let WINDOW_HEIGHT = $WINDOW.height();
$WINDOW.resize(() => {
  WINDOW_WIDTH = window.innerWidth || $WINDOW.width();
  WINDOW_HEIGHT = $WINDOW.height();
});

export const IS_DESKTOP_WIDTH = () => {
  return WINDOW_WIDTH > NOTEBOOK_WIDTH;
};
export const IS_NOTEBOOK_WIDTH = () => {
  return ( WINDOW_WIDTH > SMALL_NOTEBOOK_WIDTH && WINDOW_WIDTH <= NOTEBOOK_WIDTH );
};
export const IS_SMALL_NOTEBOOK_WIDTH = () => {
  return ( WINDOW_WIDTH > TABLET_WIDTH && WINDOW_WIDTH <= SMALL_NOTEBOOK_WIDTH );
};
export const IS_TABLET_WIDTH = () => {
  return ( WINDOW_WIDTH >= PORTRAIT_TABLET_WIDTH && WINDOW_WIDTH <= TABLET_WIDTH );
};
export const IS_MOBILE_WIDTH = () => {
  return WINDOW_WIDTH <= MOBILE_WIDTH;
};
export const IS_LANDSCAPE_MOBILE_WIDTH = () => {
  return WINDOW_WIDTH <= LANDSCAPE_MOBILE_WIDTH;
};
export const IS_SMALL_MOBILE_WIDTH = () => {
  return WINDOW_WIDTH <= SMALL_MOBILE_WIDTH;
};
