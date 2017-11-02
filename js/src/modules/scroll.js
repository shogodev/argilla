import { $DOCUMENT, $WINDOW, $BODY } from './globals';
import { IS_MOBILE_WIDTH } from './helpers';

// Scroll to
// ---------

$DOCUMENT.on('click.scroll-to', '.js-scroll-to', function(e) {
  e.preventDefault();

  const $lnk = $(this);
  const $elem_to_scroll = $($lnk.attr('href'));
  const speed = $lnk.data('speed') || 150;
  const offset = $lnk.data('offset') || 0;

  $WINDOW.scrollTo($elem_to_scroll, { duration: speed, offset: offset });
});

// Scrolling to top
// ----------------

if (!IS_MOBILE_WIDTH()) {
  const $go_top_btn = $('<div class="go-top-btn"></div>');

  $go_top_btn.click(() => {
    $WINDOW.scrollTo(0, 200);
  });

  $WINDOW.scroll(() => {
    const scroll_top = $WINDOW.scrollTop();
    if (scroll_top > 0) {
      $go_top_btn.addClass('visible');
    } else {
      $go_top_btn.removeClass('visible');
    }
  });

  $BODY.append($go_top_btn);
}
