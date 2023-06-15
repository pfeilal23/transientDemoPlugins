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

    /*If the person setting the shortcode changes their mind, this helps to cover that. However, if you're using multiple instances of the shortcode, say on different pages, you're on your own. I couldn't wrap my brain around that one. If they had previously set attribute values and then removed the attributes altogether, you're also on your own. We did not go over this in the demo. */
    $previous_category_attribute = get_transient('category_attribute');

if ( false === $previous_category_attribute ){

    /*When using the extract function it automatically creates the $category and $photos variables. */
    set_transient('category_attribute', $category); /*If no expiration is specified, it doesn't expire. This is fine for this particular use case. */
}

    $previous_photo_attribute = get_transient('photo_attribute');

    if ( false === $previous_photo_attribute ){
        set_transient('photo_attribute', $photos);
    }


    
    $pexels = get_transient( 'pexels' );

    /*If the pexels transient doesn not exist or if any of the shortcode attributes have been updated, run the api call. */
    if(( false === $pexels ) || ($photos !== $previous_photo_attribute) || ($category !== $previous_category_attribute )) {

        /*Sleep for 3 seconds. That was for demonstration purposes only, to more clearly illustrate page speed savings when a transient is set. Pexels API is actually fairly quick. If you're making a real plugin, there is no need for the sleep function.*/
sleep(3);
     
    $args = array(
       'category' => $category,
       'photos'	=> $photos,
    );

        $ch = curl_init();
$category2 = $args['category'];
$categories = array($category2, 'cats', 'dogs', 'horses', 'lions');
$randomize = $categories[array_rand($categories)];
$photos2 = $args['photos'];

curl_setopt($ch, CURLOPT_URL, "https://api.pexels.com/v1/search?query=$randomize&per_page=$photos2");

/*This returns the data as a string, as opposed to outputting it directly. */
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "Accept: application/json";

/* In a real plugin, store the API key on an options page. DO NOT JUST PUT IT IN THIS STRING!! That's a huge security issue. */
$headers[] = 'Authorization: yourApiKey';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$info = curl_getinfo($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$decoded = json_decode($result);
   
/*By reading the Pexels API documentation I know there will be an array called photos. */
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