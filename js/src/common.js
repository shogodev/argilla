alertify.set({
  buttonReverse: true,
  labels : {
    ok : 'Да',
    cancel : 'Нет'
  }
});

function initCommonScripts() {
  'use strict';

  $('body').on('yiiListViewUpdated', function(){
    var $catalog = $('.catalog');
    var scrollSpeed = Math.abs( $(window).scrollTop() ) * 0.3;
    $(window).scrollTo( $catalog, {
      duration: scrollSpeed
    });
  });

}

$(function() {
  initCommonScripts();
});