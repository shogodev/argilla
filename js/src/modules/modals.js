import { $DOCUMENT } from './globals';

// Open popup
$DOCUMENT.on('click.overlay-open', '.js-overlay', function(e) {
  e.preventDefault();

  const $popup = $(this).attr('href');

  $.overlayLoader(true, {
    node: $popup,
    hideSelector: '.js-close-popup',
    fixedElements: '.js-fixed-element',
  });
});

// Autofocus
$DOCUMENT.on('overlayLoaderShow', (e, $node) => {
  $node.find('.js-autofocus-inp').focus();
});
