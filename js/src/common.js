alertify.set({
  buttonReverse: true,
  labels : {
    ok : 'Да',
    cancel : 'Нет'
  }
});

function initCommonScripts() {
  'use strict';

  $(document.body)
    .on('yiiListViewUpdated', function(event, id, data) {
      var $catalog = $('.catalog');
      var scrollSpeed = Math.abs( $(window).scrollTop() ) * 0.3;
      $(window).scrollTo( $catalog, {
        duration: scrollSpeed
      });
    })
    .on('overlayLoaderShow', function(e, $node) {
      $node.find('.autofocus-inp').focus();
    });
}

$(function() {
  initCommonScripts();

  $('body').on('click', '.js-show-product-details', function(e) {
    // Показ деталей товара на разводных
    e.preventDefault();

    var dataBlock = $(this).closest('.product').find('.js-product-details-data');

    if( dataBlock.html() == '' ) {
      $.mouseLoader(true);
      var self = $(this);
      $.post(self.data('url'), null, function (content) {
        self.closest('.product').find('.js-product-details-data').replaceWith('<div class=".js-product-details-data">' + content + '</div>');
        $('select').sSelect();
        self.closest('.product').addClass('active').siblings().removeClass('active');
        $.mouseLoader(false);
      });
    }
    else
    {
      $(this).closest('.product').addClass('active').siblings().removeClass('active');
    }
  });

  $('body').on('click', '.js-hide-product-details', function() {
    $(this).closest('.product').removeClass('active');
  });

  // OverlayLoader init
  $(document).on('click.overlay', '.js-overlay', function(e) {
    e.preventDefault();
    $.overlayLoader(true, $($(this).attr('href')));
  });
});