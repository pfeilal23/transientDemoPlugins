<?php 
/**
* Plugin Name: AP Pexels API Integration
* Description: This plugin pulls in random photos from Pexels.
* Version: 0.1
* Author: Ashley Pfeil
**/
function ap_pexels($atts){
          // safely extract custom arguments and set default values
    extract( shortcode_atts(
        array(
          'category' => 'animals',
            'photos' => '15',
        ),
        $atts,
        'pexels'
    ) );
    
    $pexels = get_transient( 'pexels' );
    if( false === $pexels ) {
        //sleep for 3 seconds
sleep(3);
     
    $args = array(
       'category' => $category,
       'photos'	=> $photos,
    );

        $ch = curl_init();
$category = $args['category'];
$categories = array($category, 'cats', 'dogs', 'horses', 'lions');
$randomize = $categories[array_rand($categories)];
$photos = $args['photos'];

curl_setopt($ch, CURLOPT_URL, "https://api.pexels.com/v1/search?query=$randomize&per_page=$photos");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "Accept: application/json";
$headers[] = 'Authorization: yourApiKey';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$info = curl_getinfo($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$decoded = json_decode($result);
   
$pexels = $decoded->photos;

set_transient( 'pexels', $pexels, DAY_IN_SECONDS );
    }
    
$output = '<ol>';
foreach ( $pexels as $img ){
   
    $output .= '<li>
    <img src="'.$img->src->medium.'" style="max-width:100%;"/>
    <p>Photographer: '.$img->photographer.'</p>
    <p><a href="'.$img->photographer_url.'" target="_blank">Photographer profile</a></p>
    <p><a href="'.$img->url.'" target="_blank">Original photo</a></p>
    </li>';
}

$output .= '</ol>';

   return $output;
}

function ap_shortcodes_init3(){
	add_shortcode( 'pexels','ap_pexels' );
}
add_action('init', 'ap_shortcodes_init3');
?>