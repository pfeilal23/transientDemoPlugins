<?php 
/**
* Plugin Name: AP Quotes
* Description: This plugin pulls in random quotes from the Zen Quotes API.
* Version: 0.1
* Author: Ashley Pfeil
**/
function ap_quote(){
    $quote = get_transient( 'quote' );

    if( false === $quote ) {
         
    $response = wp_remote_get( 'https://zenquotes.io/api/random' );
    
    $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

    $quote = $api_response[0]["h"];

   set_transient( 'quote', $quote, HOUR_IN_SECONDS );

    }

    $output = '<p>'.$quote.'</p><p>Inspirational quotes provided by <a href="https://zenquotes.io/" target="_blank">ZenQuotes API</a></p>';


return $output;
}

function ap_shortcodes_init2(){
	add_shortcode( 'quote','ap_quote' );
}
add_action('init', 'ap_shortcodes_init2');
?>