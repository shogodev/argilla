/**
 * jQuery Yii ListView extension.
 *
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

;(function($) {
  if (jQuery.fn.yiiListView) {
    /**
     *
     * @param dropDown
     */
    $.fn.yiiListView.pageSizeHandler = function (dropDown) {
      var url = $(dropDown).data('url');
      var params = {'setSorting': 1, 'pageSize': $(dropDown).val()};

      if (window.History.enabled) {
        window.History.pushState(params, document.title, url);
      }
      else {
        $.post(url, params, function(){document.location.href = url;});
      }
    };

    /**
     *
     * @param link
     */
    $.fn.yiiListView.skinHandler = function (link) {
      if( !$(link).hasClass('active') ) {
        $.cookie('lineView', $(link).attr('id') === 'tablet' ? 0 : 1, {path: '/'});
        var list = $('#' + $(link).data('list-id'));
        list.yiiListView.update(list.attr('id'));
      }
      return false;
    };

    /**
     *
     * @param dropDown
     */
    $.fn.yiiListView.sortingHandler = function (dropDown) {
      var sorting = $(dropDown).val() == '0' ? '' : $(dropDown).val();
      var params = {'data' : {'setSorting' : 1, 'sorting' : sorting}};
      var list = $('#' + $(dropDown).data('list-id'));
      list.yiiListView.update(list.attr('id'), params);
    };
  }
})(jQuery);