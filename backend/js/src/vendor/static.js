/**
 * Интерфейсные функции
 */

/**
 * Удобные шапка и сайдбар
 */
function fixLayout() {
  var $header  = $('.s-header'),
      $subnav  = $('.s-subnav'),
      $shader  = $('.s-shader'),
      hHeight  = parseInt($header.outerHeight(), 10),
      snHeight = parseInt($subnav.outerHeight(), 10),
      sHeight  = parseInt($shader.outerHeight(), 10);

  var fixSubNav = function() {
    if($(window).scrollTop() > hHeight) {
      $shader.show();
      if(($(document).height() - $(window).height()) > (hHeight + snHeight + sHeight)) {
        $(document.body).addClass('mininize-header').css('paddingTop', snHeight);
        $shader.css('top', snHeight);
      }
    } else {
      $(document.body).removeClass('mininize-header').css('paddingTop', hHeight + snHeight);
      $shader.hide();
    }
  }();

  var fixSidebar = function() {
    var sidebar = $('aside#sidebar'),
        content = $('#content');

    if(sidebar.length) {
      if($(window).scrollTop() > content.find('.table').offset().top - snHeight && sidebar.height() <= $(window).height() - snHeight) {
        var originalWidth = sidebar.outerWidth();
        sidebar.addClass('fixed').css({
          top: snHeight,
          width: originalWidth
        });
      } else {
        sidebar.removeClass('fixed').css({
          paddingTop: content.find('.table').offset().top - content.offset().top,
          width: ''
        });
      }
    }
  }();
}