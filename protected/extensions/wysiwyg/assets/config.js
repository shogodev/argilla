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

  // The toolbar groups arrangement, optimized for two toolbar rows.
/*  config.toolbarGroups = [
    { name: 'source',	   groups: [ 'mode' ] },
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'links' },
    { name: 'insert' },
    { name: 'tools' },
    { name: 'others' }
  ];*/

  // Remove some buttons provided by the standard plugins, which are
  // not needed in the Standard(s) toolbar.
  //config.removeButtons = 'Underline,Subscript';

/*
  config.toolbar = [
    {name: 'document', items: [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
    {name: 'clipboard', items: [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
    {name: 'editing', items: [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
    {name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
    '/',
    {name: 'basicstyles', items: [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
    {name: 'paragraph', items: [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
    {name: 'links', items: [ 'Link','Unlink','Anchor' ] },
    {name: 'insert', items: [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] }, '/',
    {name: 'styles', items: [ 'Styles','Format','Font','FontSize' ] },
    {name: 'colors', items: [ 'TextColor','BGColor' ] },
    {name: 'tools', items: [ 'Maximize', 'ShowBlocks','-','About' ] }
  ];
*/

  // Set the most common block elements.
  config.format_tags = 'p;h1;h2;h3;pre';

  // Simplify the dialog windows.
  config.removeDialogTabs = 'image:advanced;link:advanced;';
};
