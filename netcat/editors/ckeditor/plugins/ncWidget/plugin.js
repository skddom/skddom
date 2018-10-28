
CKEDITOR.plugins.add( 'ncWidget',
{
	requires : [ 'dialog' ],

	init : function( editor )
	{
		editor.addCommand( 'ncWidget', new CKEDITOR.dialogCommand( 'ncWidget' ) );
		editor.ui.addButton( 'ncWidget',
			{
				label : 'NetCat Widgets',
				command : 'ncWidget',
        icon: this.path + 'images/i_widget.gif'
			});
		CKEDITOR.dialog.add( 'ncWidget', this.path + 'dialogs/ncwidget.js' );
	}
} );

