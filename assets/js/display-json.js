jQuery( 'document' ).ready( function ( $ ) {

    if ( load_sequence !== "undefined" ) {
	var output_div = $( '<div class="lsv-output" style="background-color:red" />' );
	
	
	var js_object = JSON.parse( JSON.stringify( load_sequence ) );

	$.each( js_object, lsv_display );

	function lsv_display( key, value ) {

	    output_div.append( value);
	    output_div.append( key );
	    console.log( value );
	    console.log( key );
	}
	$( "footer.site-footer" ).prepend( output_div );
    }   
    
} );