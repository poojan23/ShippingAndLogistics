/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
	config.language = $(this).attr('data-lang');
	config.filebrowserWindowWidth = '800';
	config.filebrowserWindowHeight = '500';
	config.resize_enabled = false;
	config.htmlEncodeOutput = false;
	config.entities = false;
	config.extraPlugins = 'codemirror,popaya,ckeditor-gwf-plugin'; //
	config.codemirror_theme = 'monokai';
	config.toolbar = 'Custom';
	config.contentsCss = 'https://fonts.googleapis.com/css?family=Comfortaa:300,400,600,700|Open+Sans:300,400,400i,700';
	config.font_names = ';Comfortaa/Comfortaa';
	config.font_names = config.font_names + ';Open Sans/Open Sans';
	config.font_names = config.font_names + ';GoogleWebFonts';

	config.toolbar_Custom = [
		['Source'],
		['Maximize'],
		['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
		['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull'],
		['SpecialChar'],
		'/',
		['Undo', 'Redo'],
		['Font', 'FontSize'],
		['TextColor', 'BGColor'],
		['Link', 'Unlink', 'Anchor'],
		['Image', 'Popaya', 'Table', 'HorizontalRule']
	];
};