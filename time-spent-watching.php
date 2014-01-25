<?php

function total_time_spent_anime($user_id, $current_id) {


	$anime_entries = new WP_Query(array(
		'posts_per_page' => -1,	
		'post_type' => 'anime-list-entry',
		'author' => $user_id,
		));

	global $total_minutes;

	if ( $anime_entries->have_posts() ) :

		while ( $anime_entries->have_posts() ) : $anime_entries->the_post();

			$status = get_post_meta(get_the_ID(), 'status_list_entry', true);
			$episode_seen = get_post_meta(get_the_ID(), 'episodes_seen_list_entry', true);
			$anime_id = get_post_meta(get_the_ID(), 'anime_id_list_entry', true);
			$total_episodes = get_post_meta($anime_id, 'anime_total_episodes', true);
			$episode_duration = get_post_meta($anime_id, 'episode_duration', true);
	
			if ($status == 'Completed') {
				$total_minutes += ($total_episodes * $episode_duration);
			} else if ($status == 'On hold') { 
				$total_minutes += ($episode_seen * $episode_duration);
			} else if ($status == 'Dropped') {
				$total_minutes += ($episode_seen * $episode_duration);
			} else if ($status == 'Watching') {
				$total_minutes += ($episode_seen * $episode_duration);		
			}
			
		endwhile;
		wp_reset_postdata();
	

	// Converting minutes into other forms for time watched
	$minutes_left = $total_minutes % 60;
	$total_hours = floor(($total_minutes % 1440) / 60);
	$total_days = floor(($total_minutes % 10080) / 1440);
	$total_weeks = floor(($total_minutes % 43829.1) / 10080);
	$total_months = floor(($total_minutes % 525949) / 43829.1);
	$total_years = floor($total_months / 525949);

	if ($minutes_left == 1) { $minutes_string = "minute"; } else { $minutes_string = "minutes"; }
	if ($total_hours == 1) { $hours_string = "hour"; } else { $hours_string = "hours"; }
	if ($total_days == 1) { $days_string = "day"; } else { $days_string = "days"; }
	if ($total_weeks == 1) { $weeks_string = "week"; } else { $weeks_string = "weeks"; }
	if ($total_months == 1) { $months_string = "month"; } else { $months_string = "months"; }
	if ($total_years == 1) { $years_string = "year"; } else { $years_string = "years"; }

	if ($user_id == $current_id)
		echo 'You\'ve watched ';
	else
		echo get_user_by('id', $user_id)->user_login . " has watched ";
 
	if ($total_years != 0)
		echo $total_years . ' ' . $years_string . ', ';
	if ($total_months != 0) 
		echo $total_months . ' ' . $months_string . ', ';
	if ($total_weeks != 0) 
		echo $total_weeks . ' ' . $weeks_string . ', ';
	if ($total_days != 0) 
		echo $total_days . ' ' . $days_string . ', ';
	if ($total_hours != 0) 
		echo $total_hours . ' ' . $hours_string . ', '; 
	echo $minutes_left . ' ' . $minutes_string . ' of Anime.';
	


	else:

	echo 'No anime added'; 

	endif;

}

?>
