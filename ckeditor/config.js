/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
  config.filebrowserBrowseUrl = '/ckeditor/ckfinder/ckfinder.html';
  config.filebrowserImageBrowseUrl = '/ckeditor/ckfinder/ckfinder.html?Type=Images';
  config.filebrowserFlashBrowseUrl = '/ckeditor/ckfinder/ckfinder.html?Type=Flash';
  config.filebrowserUploadUrl = '/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
  config.filebrowserImageUploadUrl = '/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
  config.filebrowserFlashUploadUrl = '/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';

  // http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar

  config.toolbar = 'simple_toolbar';
  config.toolbar_simple_toolbar =
  [
      ['Source'],
      ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'],
      ['Bold','Italic','Underline','Strike','-'],
      ['NumberedList','BulletedList'],
      ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
      ['Link','Unlink','Anchor'],
      ['TextColor','BGColor','-','Maximize', 'ShowBlocks','Smiley'],
      [ 'Image','Flash','SpecialChar','PageBreak']
  ];
};
