/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

;(function($) {

  $.fn.addInCollection = function(pic) {

    this.move = function(pic, targetBlock) {

      if ( pic == undefined || pic.size() == 0 )
        return;

      if(targetBlock == undefined || targetBlock.size() == 0)
        return;

      var clonedPic = pic.clone().css({
        position: 'fixed',
        left: pic.offset().left - $(window).scrollLeft(),
        top: pic.offset().top - $(window).scrollTop(),
        height: pic.height(),
        width: pic.width()
      }).addClass('movin-to-basket');
      $('body').append(clonedPic);

      var targetPos = { top: targetBlock.offset().top - $(window).scrollTop(), left: targetBlock.offset().left - $(window).scrollLeft() };
      clonedPic.css({
        top: targetPos.top,
        left: targetPos.left,
        opacity: 0
      }).addClass('scale');

      setTimeout(function(){
        clonedPic.remove();
      }, 500)
    };

    this.move(pic, this);

    return this;
  };

})(jQuery);
