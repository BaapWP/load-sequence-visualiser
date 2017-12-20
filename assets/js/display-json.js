jQuery( 'document' ).ready( function ( $ ) {
    
    var js_object = JSON.parse( JSON.stringify( load_sequence ) );
        
    $.each( js_object, lsv_display);
    
    function lsv_display(key, value) {
	
	if ( value !== null && typeof (vaule) !== "object" ) {
	    console.log(value);
	}
	
	if( value !== null && typeof ( value) === "object" ) {
	    $.each( value, lsv_display);
	}
	
    }

} );