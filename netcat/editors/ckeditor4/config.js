/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.replace( 'editor1', {
    customConfig: ''
});
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.enterMode = CKEDITOR.ENTER_P;
    config.autoParagraph = true;
    config.fillEmptyBlocks = false; // $nbsp fix
    config.forcePasteAsPlainText = true;
    config.templates_files = [ '/netcat_template/template/thebuilt/tpl/abzac-header.js' ];
    config.templates = 'thebuilt';
};
