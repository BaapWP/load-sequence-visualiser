jQuery( 'document' ).ready( function ( $ ) {

    if ( load_sequence !== "undefined" ) {

	var output_button = $( '<input type="button" value="LSV Output" class="output-button"/>' );
	var output_div = $( '<div class="lsv-output" style="display:none;" />' );
	var output_list = $( '<ul />' );

	var js_object = JSON.parse( JSON.stringify( load_sequence ) );

	$.each( js_object, lsv_display );

	function lsv_display( key, value ) {

	    class_name = '';
	    if ( 'file' === value ) {
		class_name = "lsv-include";
	    } else if ( 'constant' === value ) {
		class_name = "lsv-constant";
	    } else if ( 'global' === value ) {
		class_name = "lsv-global";
	    } else {
		class_name = "lsv-hook";
	    }
	    output_list.append( "<li class=" + class_name + ">" + key + "</li>" );
	}
	output_div.append( output_list );
	$( "#content.site-content" ).append( output_button );
	$(".output-button").css( {
	    'position': 'fixed',
	    'right': '5px',
	    'bottom': '5px'
	} );
	$( "#content.site-c1ontent" ).append( output_div );
	$( ".output-button" ).on( 'click', function () {
	    $( "div.lsv-output" ).slideToggle();
	} );

    }

} );