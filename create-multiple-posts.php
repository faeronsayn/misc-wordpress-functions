function createMultiEpisode($start_episode, $end_episode, $date, $anime_id) {
	
	// get anime information
	$anime_name = get_the_title($anime_id);
	$anime_type = get_field("anime_type", $anime_id);
	$old_total_episodes = get_field("anime_total_episodes", $anime_id);
	if ($old_total_episodes) {} else { $old_total_episodes = 0; }

	if ($anime_type == 'TV')
		$generic_type = 'Episode';
	else 
		$generic_type = $anime_type;

	// Update the total_episodes field in the anime

	if ($old_total_episodes < $end_episode)
		update_field("field_51f51d815c109", $end_episode, $anime_id);
	

	$current_episode = $start_episode;
	$air_date_stamp = strtotime($date);
	$current_time_stamp = time(); 

	$current_mysql_timestamp = current_time( 'timestamp' );

	while ($current_episode <= $end_episode)
	{
		$current_time = date("Y-m-d H:i:s", $current_time_stamp);
		$air_date_format = date("Ymd", $air_date_stamp);

		$post_title = $anime_name . " Online " . $generic_type . " " . $current_episode;

		// Create post object
		$my_post = array(
		  'post_title'    => $post_title,
		  'post_status'   => 'publish',
		  'post_author'   => $current_user,
		  'post_type'     => 'post',
		  'post_date'     => $current_time 
		);

		// Insert the post into the database
		$added_post_id = wp_insert_post($my_post);

		// Initialize the multiple episodes field to no
		update_field("field_51411b7baa555", 'No', $added_post_id);
		
		// Set Episode Number to whatever start episode is...
		update_field("field_46", $current_episode, $added_post_id);

		// assume $date given is in yymmdd format
		if ($air_date_stamp > 0)
			update_field("field_17", $air_date_format, $added_post_id);
		
		// Set Already_aired to no initially
		update_field("field_28", 'no', $added_post_id);
		 		
		p2p_type( 'posts_to_anime' )->connect( $added_post_id, $anime_id, array(
		'date' => date("Y-m-d H:i:s", $current_mysql_timestamp)
		) );


		$current_episode++;
		$current_time_stamp += 1;
		$current_mysql_timestamp += 1;

		if ($air_date_stamp > 0)
			$air_date_stamp += 604800; // 7 days

	}

}
