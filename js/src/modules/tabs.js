$('.js-tabs .tabs-nav li a').click(function(e) {
  e.preventDefault();

  const $this = $(this);
  const $panel = $( $this.attr('href') );

  $this.closest('li').addClass('active').siblings().removeClass('active');
  $panel.closest('.tabs').find('.tabs-panel').hide();
  $panel.fadeIn();
});
