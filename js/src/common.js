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
    .on('yiiListViewUpdated', function(){
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
});