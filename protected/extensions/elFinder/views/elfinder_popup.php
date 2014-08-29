<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
	</head>
	<body>
  <script type="text/javascript">
    // Helper function to get parameters from the query string.
    function getUrlParam(paramName) {
      var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
      var match = window.location.search.match(reParam) ;

      return (match && match.length > 1) ? match[1] : '' ;
    }

    $().ready(function() {
      var funcNum = getUrlParam('CKEditorFuncNum');
      var settings = <?php echo $settings; ?>;

      settings.getFileCallback = function(file)
      {
        window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
        window.close();
      };

      var elf = $('#elfinder').elfinder(settings).elfinder('instance');
    });
  </script>
		<div id="elfinder"></div>
	</body>
</html>