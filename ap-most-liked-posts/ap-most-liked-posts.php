<?php
/**
* Plugin Name: AP Most Liked Posts
* Description: This will display posts based on like count. Default is top 3.
* Version: 0.1
* Author: Ashley Pfeil
**/
function ap_popularposts($atts){
     // safely extract custom arguments and set default values
	extract( shortcode_atts(
        array(
            'numberposts'		=> 3,
            'post_type'			=> 'post',
            'meta_key'		=> 'likes',
            'orderby'	=> 'meta_value_num',
            'order'			=> 'DESC',
        ),
        $atts,
        'popularposts'
    ) );
    // Check if the transient exists
$popposts = get_transient( 'popularposts' );
    if( false === $popposts ) {
        

    $args = array(
        'numberposts'		=> $numberposts,
            'post_type'			=> 'post',
            'meta_key'		=> 'likes',
            'orderby'	=> 'meta_value_num',
            'order'			=> 'DESC',
    );

    $popposts = get_posts( $args );
   
    set_transient( 'popularposts', $popposts, DAY_IN_SECONDS );
    } 

		$output = '<ol>';
		foreach ( $popposts as $p ){
           
			$output .= '<li><a href="' 
			. get_permalink( $p->ID ) . '">' 
            . '<img src="'.get_the_post_thumbnail_url($p->ID).'" style="max-width:100%;"/>'
            . 'Likes: ' . $p->likes . '<br />'
            . date('F d, Y', strtotime($p->post_date)) .'<br />'
            . get_the_author_meta('display_name', $p->post_author) .'<br />'
			. $p->post_title .'<br />'
            . get_the_excerpt($p->ID) .'<br />'
            . '</a></li>';
		}

		$output .= '</ol>';
	
    
    
    return $output;
        
    
    
}
function ap_shortcodes_init(){
	add_shortcode( 'popularposts','ap_popularposts' );
}
add_action('init', 'ap_shortcodes_init');

?>