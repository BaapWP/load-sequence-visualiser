jQuery( 'document' ).ready( function ( $ ) {

    if ( load_sequence !== "undefined" ) {
	var output_div = $( '<div class="lsv-output" style="background-color:red" />' );

	output_div.append( "<ul>OUTPUT" );
	var js_object = JSON.parse( JSON.stringify( load_sequence ) );

	$.each( js_object, lsv_display );

	function lsv_display( key, value ) {

	    class_name = '';
	    if ( 'file' === value ) {
		class_name = "lsv-file";
	    } else if ( 'constant' === value ) {
		class_name = "lsv-constant";
	    } else if ( 'global' === value ) {
		class_name = "lsv-global";
	    } else {
		class_name = "lsv-filter";
	    }
	    output_div.append( "<li class=" + class_name + ">" + value + ":" + key + "</li>" );
	    console.log( value + ":" + key );
	}
	output_div.append( "OUTPUT</ul>" );
	$( "footer.site-footer" ).prepend( output_div );
    }

} );