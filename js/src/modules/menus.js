import { IS_DESKTOP, IS_MOBILE } from './globals';

const $menus = $('.js-menu');

if (IS_DESKTOP) {
  $menus.on('mouseenter.js-menu', 'li', function() {
    const $this = $(this);
    clearTimeout($this.data('hoverTimeout'));
    $this.addClass('is-hovered');
  });

  $menus.on('mouseleave.js-menu', 'li', function() {
    const $this = $(this);
    $this.data('hoverTimeout', setTimeout(function() {
      $this.removeClass('is-hovered');
    }, 200));
  });
}

if (IS_MOBILE) {
  $menus.on('click.js-m-menu', 'a', function(e) {
    e.preventDefault();

    const $anchor = $(this);
    const $parent = $anchor.parent();
    const has_dropdown = $parent.hasClass('has-dropdown');
    const is_hovered = $parent.hasClass('is-hovered');

    $parent.siblings().removeClass('is-hovered');

    if (!has_dropdown) {
      location.href = $anchor.attr('href');
    } else {
      if (is_hovered) {
        location.href = $anchor.attr('href');
      } else {
        $parent.addClass('is-hovered');
      }
    }
  });
}
