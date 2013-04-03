/**
 Связывает обработчик события с Drop Down селекторами onFlyEdit.
 */
function bindDropDownOnFlyHandler(args)
{
  var gridId = args.hasOwnProperty('gridId') ? args.gridId : '';
  var urlToPost = args.hasOwnProperty('urlToPost') ? args.urlToPost : '';

  $(function() {
    $('select.onfly-edit-dropdown').change(function () {
      var matches = $(this).attr('data-onflyedit').match(/(\w+)-(\d+)/);
      var data = {};

      data.action = 'onflyedit';
      data.field = matches[1];
      data.id = matches[2];
      data.value = $(this).val();
      data.gridId = gridId;

      $.post(urlToPost, data, '', 'json');
    });
  });
}
