/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config
    config.height = 400;

    // The toolbar groups arrangement, optimized for two toolbar rows.
    config.toolbarGroups = [
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'insert' },
        // { name: 'forms' },
        { name: 'tools' },
        // { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'mode',	   groups: [ 'mode' ] },
        { name: 'others' },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        // { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align'] },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'FontSize' },
        { name: 'TextColor' },
    ];

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Subscript,Superscript';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:upload;link:upload';

    config.baseFloatZIndex = 20000;
    // config.uiColor = '#AADC6E';

    config.filebrowserUploadUrl= "/upload/ckEditorUpload"; //待会要上传的action或servlet
};
