<?php

function anime_rating_calculator($anime_id) {

	$anime_user_entries = new WP_Query(array(
		'post_type' => 'anime-list-entry',
		'meta_key' => 'anime_id_list_entry',
		'meta_value' => $anime_id
	));
		
	$average_rating = 0;
	$total_weight = 0;
	$store_score_numbers = array(0,0,0,0,0,0,0,0,0,0);
	$counter_entries = 0;

	if ( $anime_user_entries->have_posts() ) :

		while ( $anime_user_entries->have_posts() ) : $anime_user_entries->the_post();
			$score = get_post_meta(get_the_ID(), 'score_anime_list_entry', true);
			$status = get_post_meta(get_the_ID(), 'status_list_entry', true);
			$episodes_seen = get_post_meta(get_the_ID(), 'episodes_seen_list_entry', true);
			// $total_episodes = get_post_meta($anime_id, 'anime_total_episodes', true);
			
			/*
			if ($status == "Completed")
				$episodes_seen = $total_episodes;
			*/
			
			
			if($score && $status != "Plan to watch") {
				$counter_entries++;
								
				$store_score_numbers[$score] += 1;
				$average_rating += $score * $episodes_seen;
				$total_weight += $episodes_seen; 
				// $score_array[$counter_entries] = $score*$episodes_seen;
				$seen_array[$counter_entries] = $episodes_seen;
									
			}
			
		endwhile;
		wp_reset_postdata();

		if ($total_weight != 0) {
			$average = $average_rating/$total_weight;
		}
	  
		$old_average = get_post_meta($anime_id, 'anime_rating_average', true);
		$old_score_numbers = get_post_meta($anime_id, 'anime_score_numbers', true);
		
		if( $old_average != $average) {
		
			// Adding average into the anime database
			update_post_meta($anime_id, 'anime_rating_average', $average);
		
			// Adding the array of numbers in to the database
			update_post_meta($anime_id, 'anime_score_numbers', $store_score_numbers);
		
			// Add the time when this shit was last updated...
			update_post_meta($anime_id, 'anime_rating_last_updated', time());
			
			return array($average, $store_score_numbers, true);
			
		} else {
		
			return array($average, $store_score_numbers, false);
			
		}

	endif;

}

?>
