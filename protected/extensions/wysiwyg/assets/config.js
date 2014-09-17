/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  config.toolbar =
    [
      ['Source'],
      ['Undo','Redo', '-'],
      ['Bold','Italic','Underline','Strike','Underline','Subscript','-'],
      ['NumberedList','BulletedList', 'Outdent','Indent'],
      ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
      ['Find','Replace','-','SpellChecker', 'Scayt'],
      ['Link','Unlink','Anchor'],
      ['TextColor','BGColor','-','Maximize', 'ShowBlocks'],
      ['Image','Flash','SpecialChar','PageBreak'],
      ['Cut','Copy','Paste','PasteText','PasteFromWord']
    ];

  // Allow everything (disable ACF)
  config.allowedContent = true;

  // Simplify the dialog windows.
  config.removeDialogTabs = 'image:advanced;link:advanced;';
};
