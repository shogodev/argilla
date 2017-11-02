$('.js-slideshow').each(function() {
  const $this = $(this);

  const tos = $this.tosrus({
    effect: 'slide',
    slides: {
      visible: 1,
    },
    autoplay: {
      play: true,
      timeout: 7500,
    },
    infinite: true,
    pagination: {
      add: true,
    },
  });
});
