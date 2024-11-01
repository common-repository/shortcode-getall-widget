(function($) {

	//alert(getAllwidgetShortcode.widgets);
	tinymce.PluginManager.add( 'getAllwidgetShortcode', function( editor, url ) {
		var widgetsList = [];
		
		// page Load
		$.each( getAllwidgetShortcode.widgets, function( i, v ){
			//alert(v.id+" "+v.title);
				var widgets = {
					'text' : v.title.name,
					'body': {
						'type': v.title.name
					},
					'onclick' : function(){
						editor.insertContent( '[Shortcodewidget widget="' + v.id + '"]' );
					}
				};
				widgetsList.push( widgets );
			} );

		// Add Button to Visual Editor Toolbar
		editor.addButton('getAllwidgetShortcode', {
			title: 'Get All Widget Shortcode',
			cmd: 'getAllwidgetShortcode',
			image: url + '/cogwheel.png',			
			type : 'menubutton',
			menu : widgetsList
		});	
	});

})(jQuery);
