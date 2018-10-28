

CKEDITOR.dialog.add( 'ncWidget', function( editor )
{

  var current = '';

  var previews = [];

    var onClick = function( evt ) {
        var target = evt.data.getTarget();
        var title = target.getAttribute('title');

        current = "%NC_WIDGET_SHOW('" + title + "')%";
        $(".widget_href").css("font-weight", "normal");
        $("#href_"+title).css("font-weight", "bold");
        if(previews[title])
        {
            document.getElementById('preview').contentWindow.document.open();
            document.getElementById('preview').contentWindow.document.write("");
            document.getElementById('preview').contentWindow.document.close();
            document.getElementById('preview').contentWindow.document.open();
            document.getElementById('preview').contentWindow.document.write(previews[title]);
            document.getElementById('preview').contentWindow.document.close();
        }
    };


  var html = [ '<table><tr><td style="vertical-align: top; text-align:right;">'];
  var current_group = '';

  $.ajax({
        url: ADMIN_PATH + "widget/index.php?phase=100",
        async : false,
        dataType : "json",
        success: function( data ){
          var i;
          for ( i = 0; i < data.length; i++) {
            html.push( '<div>' );
            if ( current_group != data[i]['Category'] ) {
              current_group = data[i]['Category'];
              html.push('<div style="font-size: 140%">' + current_group + '</div>');
            }
            html.push ('<a class="widget_href" id="href_'+data[i]['Keyword']+'" style="margin-left: 7px;cursor: pointer;" title="'+ data[i]['Keyword'] +'" href="javascript: void(0);" onclick="CKEDITOR.tools.callFunction(' + onClick + ', this); return false;" >');
            html.push( data[i]['Name'] );
            html.push('</a> </div>' );
            previews[ data[i]['Keyword'] ] = data[i]['Result'];

          }
        }
      });

  html.push('</td>');
  html.push('<td style="vertical-align: top;"><iframe id="preview" style="text-align: right;  height: 500px"></iframe></td></tr></table>');

	var widgetSelector =
  {
		type : 'html',
		html : html.join( '' ),
	

		onClick : onClick,
		style : 'width: 100%; border-collapse: separate;'
	};

	return {
		title : 'NetCat Widgets',
		minWidth : 500,
		minHeight : 300,
		contents : [
			{
				id : 'tab1',
				label : '',
				title : '',
				expand : true,
				padding : 0,
				elements : [
						widgetSelector
					]
			}
		],
    onOk : function () {
      if ( current.length ) editor.insertText( current );
    }

	};
} );

