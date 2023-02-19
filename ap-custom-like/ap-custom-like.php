<?php
/**
* Plugin Name: AP Custom Like
* Description: Adds like button to posts.
* Version: 0.1
* Author: Ashley Pfeil
**/


	//---- Add like button to bottom of post content
	function ap_post_likes($content) {
		// Check if single post
		if(is_singular('post')) {
			ob_start();

			?>
				<ul class="likes">
					<li class="likes__item likes__item--like">
						<a href="<?php echo add_query_arg('post_action', 'like'); ?>">
							Like (<?php echo ap_get_like_count('likes') ?>)
						</a>
					</li>
				
				</ul>
			<?php

			$output = ob_get_clean();

			return $content . $output;
		}else {
			return $content;
		}
	}

	add_filter('the_content', 'ap_post_likes');

	//---- Get like count
	function ap_get_like_count($type = 'likes') {
		$current_count = get_post_meta(get_the_id(), $type, true);

		return ($current_count ? $current_count : 0);
	}

	//---- Process like
	function ap_process_like() {
		$processed_like = false;
		$redirect       = false;

		// Check if like
		if(is_singular('post')) {
			if(isset($_GET['post_action'])) {
				if($_GET['post_action'] == 'like') {
					// Like
					$like_count = get_post_meta(get_the_id(), 'likes', true);

					if($like_count) {
						$like_count = $like_count + 1;
					}else {
						$like_count = 1;
					}

					$processed_like = update_post_meta(get_the_id(), 'likes', $like_count);
				}

				if($processed_like) {
					$redirect = get_the_permalink();
				}
			}
		}

		

		// Redirect
		if($redirect) {
			wp_redirect($redirect);
			die;
		}
	}

	add_action('template_redirect', 'ap_process_like');
?>