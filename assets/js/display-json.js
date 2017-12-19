jQuery( 'document' ).ready( function ( $ ) {
    
    var output = $( '<div class="lsv-output" />' );

    output.html(JSON.parse(JSON.stringify(load_sequence)));

    $("content.site-content").append(output);

} );