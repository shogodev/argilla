/**
 Связывает обработчик события с текстовыми полями onFlyEdit.
 */
function bindTextOnFlyHandler(args)
{
  var gridId = args.hasOwnProperty('gridId') ? args.gridId : '';
  var urlToPost = args.hasOwnProperty('urlToPost') ? args.urlToPost : '';

  $(function() {
    $('.onfly-edit').onfly({apply: function() {
      var matches = $(this).attr('data-onflyedit').match(/(\w+)-(\d+)/);
      var data = {};

      data.action = 'onflyedit';
      data.field = matches[1];
      data.id = matches[2];
      data.value = $(this).text();
      data.gridId = gridId;

      $.post(urlToPost, data, '', 'json');
    }});
  });
}
