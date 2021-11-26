<?php

function tred_year_month_numbers_to_string_month_slash_year($year_month) {
  if(empty($year_month)) {
    return '';
  }
  $array = explode('_',$year_month);
  $year = substr($array[0], -2);
  $month = intval($array[1]);
  $month_ext = TRED_MONTHS[$month];
  return "$month_ext/$year";
}

//https://stackoverflow.com/questions/8230538/pass-extra-parameters-to-usort-callback
function tred_sort_desc(&$arrayToSort, $key) {
  usort($arrayToSort, function($a, $b) use ($key) {
    return $b[$key] - $a[$key];  
  });
}

function tred_get_from_array($array, $key, $default_value = 0) {
  if(is_array($array) && array_key_exists($key, $array)) {
      return $array[$key]; 
  }
  return $default_value;
}

//Get this mont and last 12 months and year
function tred_get_last_12_months_and_year_array() {
  $output = [];
  for ($i = 0; $i <= 12; $i++) {
      $item = [];
      $year_month = date("Y_m", strtotime( date( 'Y-m-01' )." -$i months"));
      $item['year_month'] = $year_month; 
      $array = explode("_",$year_month);
      $item['year'] = $array[0];
      $item['month'] = $array[1];
      $output[] = $item;
  }
  return $output; 
}


function tred_check_if_timestamp_belongs_to_year_month($date,$year_month) { 
  return date('Y_m',intval($date)) === $year_month;
}


function tred_pecentage($partial, $total, $decimals = 2) {
    if( empty($partial) || empty($total)) {
        return '';
    }
    return round(($partial / $total) * 100, $decimals) . "%";
}


function tred_timestamp($seconds, $unity = '') {
    $output = [];
    $output['days'] = 0;
    $output['hours'] = 0;
    $output['minutes'] = 0;
    if(is_numeric($seconds)) {
        $output['days'] = (int) round($seconds / (60 * 60 * 24));
        $output['hours'] = (int) round($seconds / (60 * 60));
        $output['minutes'] = (int) round($seconds / 60);
    }
    if (in_array($unity,['days','hours','minutes'])) {
        return $output[$unity];
    }
    return $output;
}


/*
* NUMBER OF STUDENTS ENROLLED
*/
function tred_get_students_number( $course_id, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {

  $tred_get_students_number = get_transient( 'tred_get_students_number_' . $course_id );
  if ( !$force_refresh && $tred_get_students_number ) { 
      return $tred_get_students_number;
  }

	$transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
	
	$course_enrolled_students_number = get_transient( 'tred_get_students_number_' . $course_id );
	if ( true === $force_refresh || false === $course_enrolled_students_number ) {
	  $members_arr = learndash_get_users_for_course( $course_id, [], false );
	  if ( ( $members_arr instanceof \WP_User_Query ) && ( property_exists( $members_arr, 'total_users' ) ) && ( ! empty( $members_arr->total_users ) ) ) {
		$course_enrolled_students_number = $members_arr->total_users;
	  } else {
		$course_enrolled_students_number = 0;
	  }
	  set_transient( 'tred_get_students_number_' . $course_id, $course_enrolled_students_number, $transient_expire_time );
	}
	return (int) $course_enrolled_students_number;		
}


//Get all access modes existent by courses
function tred_get_access_modes_existent($only_published = true, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {

    $tred_get_access_modes_existent = get_transient( 'tred_get_access_modes_existent' );
    if ( !$force_refresh && $tred_get_access_modes_existent ) { 
        return $tred_get_access_modes_existent;
    }
    $transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
    $output = [];
	$args = [
		'post_type' => 'sfwd-courses',
		'fields' => 'ids',
        'numberposts' => -1 
	];
	if($only_published) {
		$args['post_status'] = 'publish';
	}
	$courses_posts = get_posts($args);
	if(empty($courses_posts)) {
		return $output;
	}
	foreach($courses_posts as $course_id) {
        $access_mode = get_post_meta($course_id, '_ld_price_type', true);
        if(!in_array($access_mode,$output)) {
            $output[] = $access_mode;
        } 
    } //end foreach
    set_transient( 'tred_get_access_modes_existent', $output, $transient_expire_time );
	return $output;
}


//Get total number of students, courses and students in each course
function tred_get_students_number_all_courses($only_published = true, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {
    $tred_get_students_number_all_courses = get_transient( 'tred_get_students_number_all_courses' );
    if ( !$force_refresh && $tred_get_students_number_all_courses ) { 
        return $tred_get_students_number_all_courses;
    }
    $transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
    $output = [];
    $courses = [];
    $students = [];

    $args = [
      'post_type' => 'sfwd-courses',
      'fields' => 'ids',
          'numberposts' => -1 
    ];
    if($only_published) {
      $args['post_status'] = 'publish';
    }
    $courses_posts = get_posts($args);
    
    $courses['total'] = count($courses_posts);

    $items = [];
    $total_students = 0;
    foreach($courses_posts as $course_id) {
      $access_mode = get_post_meta($course_id, '_ld_price_type', true);
      $c = [];
      $c['title'] = get_the_title($course_id);
      $c['students'] = (int) tred_get_students_number($course_id, 6, $force_refresh);
      $c['students_completed'] = (int) tred_get_users_completed_number($course_id);
      if(empty($total_students) && $access_mode === 'open' && !empty($c['students'])) {
        //if the course is open, all students can access it, so we have the total
        $total_students = $c['students'];
      }
      if(!isset($items[$access_mode])) {
        $items[$access_mode] = [];      
      }
      $items[$access_mode][] = $c;
    } //end foreach

    $students['total'] = (!empty($total_students)) ? $total_students : learndash_students_enrolled_count();
    $courses['items'] = $items;
    $output['courses'] = $courses;
    $output['students'] = $students;
    set_transient( 'tred_get_students_number_all_courses', $output, $transient_expire_time );
    
    return $output;
}


//Get total number of lessons, topics and quizzes
function tred_get_lessons_topics_quizzes_number($only_published = true, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {
    $tred_get_lessons_topics_quizzes_number = get_transient( 'tred_get_lessons_topics_quizzes_number' );
    if ( !$force_refresh && $tred_get_lessons_topics_quizzes_number ) { 
        return $tred_get_lessons_topics_quizzes_number;
    }
    
    $transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
    $output = [];
    $output['lessons'] = 0;
    $output['topics'] = 0;
    $output['quizzes'] = 0;

	$args = [
		'post_type' => ['sfwd-lessons','sfwd-topic', 'sfwd-quiz'],
        'numberposts' => -1 
	];
	if($only_published) {
		$args['post_status'] = 'publish';
	}
	$ld_posts = get_posts($args);
	if(empty($ld_posts)) {
		return $output;  
  }
  foreach($ld_posts as $post) {
      if($post->post_type == 'sfwd-lessons') {
          $output['lessons'] += 1;
      } else if($post->post_type == 'sfwd-topic') {
          $output['topics'] += 1;
      } else if($post->post_type == 'sfwd-quiz') {
          $output['quizzes'] += 1;
      }
  }
  set_transient( 'tred_get_lessons_topics_quizzes_number', $output, $transient_expire_time );
	return $output;
}


//Get total number of students, courses and students in each course
function tred_get_students_number_all_groups($only_published = false, $force_refresh = false ) {
	$output = [];
	$groups = [];
	$students = [];

	$args = array(
		'post_type'   => 'groups',
		'nopaging'    => true,
		'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ),
		'fields' => 'ids'
	);

	if($only_published) {
		$args['post_status'] = 'publish';
	}
	$groups_posts = get_posts($args);

	$groups['total'] = count($groups_posts);
	
	$items = [];
	$students_sum = 0;
	foreach($groups_posts as $group_id) {
		$c = [];
		$c['title'] = get_the_title($group_id);
		$c['students'] = count(learndash_get_groups_user_ids( $group_id, $force_refresh ));
		$students_sum += $c['students'];
		$items[] = $c;
	}
	$students['total'] = $students_sum;
	$groups['items'] = $items;
	$output['groups'] = $groups;
	$output['students'] = $students;
	return $output;
}



//Get course completions (all time)
function tred_learndash_get_course_completions( $days = 0, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {
    $tred_learndash_get_course_completions = get_transient( 'tred_learndash_get_course_completions' );
    if ( !$force_refresh && $tred_learndash_get_course_completions ) { 
        return $tred_learndash_get_course_completions;
    }
    $transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
    global $wpdb;
    $where_course_completed = 'activity_type = "course"';
    $where_course_completed .= 'AND activity_completed IS NOT NULL AND activity_completed != "" ';
    $where_course_completed .= 'AND activity_started IS NOT NULL AND activity_started != "" ';
    if($days) {
        $where_course_completed .= 'AND DATEDIFF(NOW(), FROM_UNIXTIME(activity_completed)) < %d';
    }
	
	$sql_select = 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE ';
    $sql_select .= $where_course_completed;
    if($days) {
        $sql_str = $wpdb->prepare( $sql_select, $days );
        $activity = $wpdb->get_results( $sql_str );
    } else {
        $activity = $wpdb->get_results( $sql_select );
    }
	
	if ( $activity ) {
        set_transient( 'tred_learndash_get_course_completions', $activity, $transient_expire_time );
		return $activity;	
	}
	return false;
}

function tred_learndash_get_course_completions_stats( $days = 0 ) { 
    $output = [];
    $output['negatives'] = 0;
    $output['zeros'] = 0;
    $output['total'] = 0;
    $output['total_seconds'] = 0;
    $output['average_seconds'] = 0;
    $output['average_minutes'] = 0;
    $output['average_hours'] = 0;
    $output['average_days'] = 0;
    $output['courses'] = [];
    
    $output['same_day'] = [];
    $output['same_day']['total'] = 0;
    $output['same_day']['total_seconds'] = 0;
    $output['same_day']['average_seconds'] = 0;
    $output['same_day']['average_minutes'] = 0;
    $output['same_day']['average_hours'] = 0;
    $output['same_day']['courses'] = [];

    $completions = tred_learndash_get_course_completions( $days );
    if(!$completions || !is_array($completions)) {
        return $output;
    }

    foreach($completions as $comp) {

        $course_diff_seconds = $comp->activity_completed - $comp->activity_started;        
        if($course_diff_seconds == 0) {
            //started and completed at the same time? Impossible... Anyway, let's forget it
            $output['zeros'] += 1;
            continue;
        }
        if($course_diff_seconds < 0) {
            //student must be restarting course...Let's forget it
            $output['negatives'] += 1;
            continue;
        }
        $course_title = get_the_title($comp->course_id);
        if(empty($course_title)) {
            //maybe the course was removed...Let's forget it
            continue;
        }

        $output['total'] += 1;
        $output['total_seconds'] += $course_diff_seconds;
        $course_diff_days = tred_timestamp($course_diff_seconds, 'days');
        
        if ($course_diff_days == 0) {
            //student started and completed course in the same day
            $output['same_day']['total'] += 1;
            $output['same_day']['total_seconds'] += $course_diff_seconds;
            if(empty($output['same_day']['courses'][$course_title])) {
                $output['same_day']['courses'][$course_title] = 0;
            }
            $output['same_day']['courses'][$course_title] += 1;
        }

        if(empty($output['courses'][$comp->course_id])) {
            $output['courses'][$comp->course_id]['id'] = $comp->course_id;
            $output['courses'][$comp->course_id]['title'] = $course_title;
            $output['courses'][$comp->course_id]['students'] = (int) tred_get_students_number($comp->course_id);
            $output['courses'][$comp->course_id]['mode'] = get_post_meta($comp->course_id, '_ld_price_type', true);
            $output['courses'][$comp->course_id]['total_completed'] = 0;
            $output['courses'][$comp->course_id]['total_seconds'] = 0;
            $output['courses'][$comp->course_id]['average_seconds'] = 0;
            $output['courses'][$comp->course_id]['user_data'] = []; 
        }
        $user_data = [];
        $user_data['user_id'] = $comp->user_id;
        $user_data['started'] = $comp->activity_started;
        $user_data['completed'] = $comp->activity_completed;
        $user_data['seconds_to_complete'] = $course_diff_seconds;

        $output['courses'][$comp->course_id]['total_completed'] += 1;
        $output['courses'][$comp->course_id]['total_seconds'] += $course_diff_seconds;
        $output['courses'][$comp->course_id]['average_seconds'] = $output['courses'][$comp->course_id]['total_seconds'] / $output['courses'][$comp->course_id]['total_completed'];
        $output['courses'][$comp->course_id]['user_data'][] = $user_data; 
    } //End foreach

    foreach($output['courses'] as $course_id => $val) {
        $output['courses'][$course_id]['total_completed_percentage'] = tred_pecentage($output['courses'][$course_id]['total_completed'],$output['courses'][$course_id]['students']);
        $output['courses'][$course_id]['average_days'] = tred_timestamp($output['courses'][$course_id]['average_seconds'], $unity = 'days');
    }

    $output['same_day']['average_seconds'] = $output['same_day']['total_seconds'] / $output['same_day']['total'];
    $converted = tred_timestamp($output['same_day']['average_seconds']);
    $output['same_day']['average_minutes'] = $converted['minutes'];
    $output['same_day']['average_hours'] = $converted['hours'];

    $output['average_seconds'] = $output['total_seconds'] / $output['total'];
    $converted = tred_timestamp($output['average_seconds']);
    $output['average_minutes'] = $converted['minutes'];
    $output['average_hours'] = $converted['hours'];
    $output['average_days'] = $converted['days'];

    return $output;
}


//Get activity on the last $days
function tred_learndash_get_activity( $days = TRED_LAST_X_DAYS, $hours = TRED_CACHE_X_HOURS, $force_refresh = false ) {
  $tred_learndash_get_activity = get_transient( 'tred_learndash_get_activity_' . $days . '_days' );
  if ( !$force_refresh && $tred_learndash_get_activity ) { 
      return $tred_learndash_get_activity;
  }
  $transient_expire_time = (int)$hours * HOUR_IN_SECONDS;
  global $wpdb;
	$where_updated = '(activity_updated IS NOT NULL AND activity_updated != "" AND DATEDIFF(NOW(), FROM_UNIXTIME(activity_updated)) < %d)';
	$sql_select = 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE ';
    $sql_select .= $where_updated;
	$sql_str = $wpdb->prepare( $sql_select, $days );
	$activity = $wpdb->get_results( $sql_str );
	if ( $activity ) {    
    set_transient( 'tred_learndash_get_activity_' . $days . '_days', $activity, $transient_expire_time );
	}
	return $activity;	
}


function tred_learndash_rank_courses_by_activity($activity) { 
	if(!is_array($activity)) {
		return false;
	}
  $keys = ['course_id','total','course','lesson','topic','quiz'];
  $actions = ['enrolls','starts','completions'];

  $courses = [];
	foreach($activity as $act) {
    
		if( empty($act->activity_type) || empty($act->course_id) ) {
			continue;
		}
    
    //Setting the course_id and its tree
    if( !isset($courses[$act->course_id]) ) {
      $courses[$act->course_id] = [];
      foreach($keys as $t) {
        if($t == 'total') {
          $courses[$act->course_id][$t] = 0; 
        } else if($t == 'course_id') {
          $courses[$act->course_id][$t] = $act->course_id; 
        } else {
          $courses[$act->course_id][$t] = [];
          foreach($actions as $a) {
            $courses[$act->course_id][$t][$a] = 0; 
          } //end subinner foreach (actions)
        } //end if/else ($t == 'total') 
      } //end inner foreach (keys) 
    } //end if/else (!isset($courses[$act->course_id])) 
    
    $courses[$act->course_id]['total'] += 1;
          
    if( $act->activity_type === 'access' ) {  
      $courses[$act->course_id]['course']['enrolls'] += 1;
      continue;
    }
    
    $key = ( empty($act->activity_completed) ) ? 'starts' : 'completions';
    $courses[$act->course_id][$act->activity_type][$key] += 1;
	} //end foreach
  
  usort($courses, function ($a, $b) {
      return $b['total'] - $a['total'];
  });
    
  return $courses;  	
}


//Get course completions on the last $days
function tred_learndash_rank_courses_items_by_completion($activity) { 
	if(!is_array($activity)) {
		return false;
	}

  $courses = [];
  $lessons = [];
  $topics = [];
  $quizzes = [];
  $keys = ['id','title','total'];

    
	foreach($activity as $act) {
    
		if( empty($act->activity_type) || empty($act->activity_completed)) {
			continue;
		}
    
    $activity_type = $act->activity_type;
    
    if( $activity_type == 'course' && !empty($act->course_id) ) {
      //Setting the course_id and its tree
      if( !isset($courses[$act->course_id]) ) {
        $courses[$act->course_id] = [];
        $courses[$act->course_id]['total'] = 0; 
        $courses[$act->course_id]['id'] = $act->course_id; 
        $courses[$act->course_id]['title'] = get_the_title($act->course_id);
      }
        $courses[$act->course_id]['total'] += 1;
    }
    
    if( $activity_type == 'lesson' && !empty($act->post_id) ) {
      //Setting the post_id and its tree
      if( !isset($lessons[$act->post_id]) ) {
        $lessons[$act->post_id] = [];
        $lessons[$act->post_id]['total'] = 0; 
        $lessons[$act->post_id]['id'] = $act->post_id; 
        $lessons[$act->post_id]['title'] = get_the_title($act->post_id);
      }
      $lessons[$act->post_id]['total'] += 1;
    }

    if( $activity_type == 'topic' && !empty($act->post_id) ) {
      //Setting the post_id and its tree
      if( !isset($topics[$act->post_id]) ) {
        $topics[$act->post_id] = [];
        $topics[$act->post_id]['total'] = 0; 
        $topics[$act->post_id]['id'] = $act->post_id; 
        $topics[$act->post_id]['title'] = get_the_title($act->post_id);
      }
      $topics[$act->post_id]['total'] += 1;
    }

    if( $activity_type == 'quiz' && !empty($act->post_id) ) {
      //Setting the post_id and its tree
      if( !isset($quizzes[$act->post_id]) ) {
        $quizzes[$act->post_id] = [];
        $quizzes[$act->post_id]['total'] = 0; 
        $quizzes[$act->post_id]['id'] = $act->post_id; 
        $quizzes[$act->post_id]['title'] = get_the_title($act->post_id);
      }
      $quizzes[$act->post_id]['total'] += 1;
    }
    
  } //end foreach

    
  usort($courses, function ($a, $b) {
      return $b['total'] - $a['total'];
  });
  
  usort($lessons, function ($a, $b) {
      return $b['total'] - $a['total'];
  });

  usort($topics, function ($a, $b) {
    return $b['total'] - $a['total'];
  });

  usort($quizzes, function ($a, $b) {
      return $b['total'] - $a['total'];
  });
  
  $output = [ 
    'courses' => $courses,
    'lessons' => $lessons,
    'topics' => $topics,
    'quizzes' => $quizzes
  ];
    
  return $output;  	
}



function is_in_the_last_x_days($timestamp,$days = TRED_LAST_X_DAYS) {
  if(!is_numeric($timestamp) || !is_numeric($days)) {
    return false;
  }
  $startDate = new DateTime("-$days days");
  $dt = new DateTime();
  $dt->setTimestamp($timestamp); 
  if ($dt > $startDate) {
    return true;    
  }
  return false;
}


//Get user activities on the last $days
function tred_learndash_get_users_all_activities($activity) { 
	if(!is_array($activity)) {
		return false;
	}
  
  $users = [];
  $keys = ['user_id','total','course','lesson','topic','quiz'];
  $actions = ['enrolls','starts','completions'];
  
	foreach($activity as $key => $act) {
    
		if( empty($act->activity_type) || empty($act->user_id) ) {
			continue;
		}
    
    //Setting the user_id and its tree
    if( !isset($users[$act->user_id]) ) {
      $users[$act->user_id] = [];
      foreach($keys as $t) {
        if($t == 'total') {
          $users[$act->user_id][$t] = 0; 
        } else if($t == 'user_id') {
          $users[$act->user_id][$t] = $act->user_id; 
        } else {
          $users[$act->user_id][$t] = [];
          foreach($actions as $a) {
            $users[$act->user_id][$t][$a] = 0; 
          } //end subinner foreach (actions)
        } //end if/else ($t == 'total') 
      } //end inner foreach (keys) 
    } //end if/else (!isset($users[$act->user_id])) 
    
    $users[$act->user_id]['total'] += 1;
          
    if( $act->activity_type == 'access' ) {  
      $users[$act->user_id]['course']['enrolls'] += 1;
      continue;
    }
    
    if(!empty($act->activity_started) && is_in_the_last_x_days($act->activity_started)) {
      $users[$act->user_id][$act->activity_type]['starts'] += 1;
    }
    
    if(!empty($act->activity_completed) && is_numeric($act->activity_completed) ) {
      $users[$act->user_id][$act->activity_type]['completions'] += 1;
    }
	} //end foreach
  return $users;  	
}


function tred_learndash_rank_users_all_activities($activity) {
  $users = tred_learndash_get_users_all_activities($activity);
  
  if(empty($users) || !is_array($users)) {
    return false;
  }
    
  usort($users, function ($a, $b) {
      return $b['total'] - $a['total'];
  });
     
  $output = [
    'emails' => [],
    'enrolls' => [],
    'starts' => [],
    'completions' => [],
    'totals' => []
  ];
  
  $keys = ['course', 'lesson', 'topic', 'quiz'];
  foreach($users as $data) {
    $user = get_user_by('id', $data['user_id']);
    
    if(!$user || $user->has_cap( 'edit_posts' )) {
      continue;
    }
    
    $user_email = $user->user_email;
    $output['emails'][] = $user_email;
    $output['totals'][] = $data['total'];
    $output['enrolls'][] = $data['course']['enrolls'];
    $starts = 0;
    $completions = 0;
    foreach($keys as $key) {
      $starts += $data[$key]['starts'];
      $completions += $data[$key]['completions'];
    }
    $output['starts'][] = $starts;
//     $output['starts'][] = $data['lesson']['starts']+$data['topic']['starts']+$data['quiz']['starts'];
    $output['completions'][] = $completions;
//     $output['totals'][] = $starts + $completions;
  } //end outter foreach
  
  return $output;
}


function tred_learndash_get_item_all_activities( $activity, $item, $activities = [] ) { 
	if(!is_array($activity)) {
		return false;
	}
  $specified = !empty($activities);
	$item_activity = [];
  $completions = [];
  $starts = [];
  $enrolls = [];
  $types = [$item];
  if ($item == 'course') {
    $types[] = 'access';
  }
	foreach($activity as $key => $act) {
    if( empty($act->post_id) && empty($act->course_id) ) {
      //one of them had to be present...
      continue;
    }
		if( empty($act->activity_type) || !in_array($act->activity_type,$types) ) {
      //if not related to item chosen, move on...
			continue;
		}
    if(!$specified) {
      $item_activity[] = $act;
      continue;
    }
    
    if(in_array('enrolls', $activities)) {
      if( $act->activity_type == 'access' ) {
        $enrolls[] = $act;
        continue;
      }
    } 
    
    if(in_array('starts', $activities)) {
      if( !empty($act->activity_started) ) {
        $starts[] = $act;
      }
    }
    
    if(in_array('completions', $activities)) {
      if( !empty($act->activity_completed) && !empty($act->activity_status) ) {
        $completions[] = $act;
      }
    }

	} //end foreach
    
  if($specified) {
    $item_activity['completions'] = $completions;
    $item_activity['starts'] = $starts;  
    $item_activity['enrolls'] = $enrolls;  
  }
  
  return $item_activity;  	
}



function tred_learndash_get_item_activities_number( $activity, $item ) {
	
	$item_activities = tred_learndash_get_item_all_activities( $activity, $item, ['completions','starts', 'enrolls'] );
  $final_array = [];
  $final_array['completions'] = (is_array($item_activities)) ? count($item_activities['completions']) : 0;
  $final_array['starts'] = (is_array($item_activities)) ? count($item_activities['starts']) : 0;
  $final_array['enrolls'] = (is_array($item_activities)) ? count($item_activities['enrolls']) : 0;
  
	return $final_array;
}


function tred_get_courses_completed() {	
	global $wpdb;
	$count = $wpdb->get_results(
		"SELECT * FROM $wpdb->usermeta
		WHERE meta_key LIKE '%course_completed%';"
	);
	return $count;
}

function tred_get_users_completed_number($course_id = 0) {	
	if(!is_numeric($course_id)) {
		return "0";
	}
	global $wpdb;
	$value = ($course_id) ? "%course_completed_$course_id%" : "%course_completed_%";
	$select_statement = ($course_id) ? "SELECT COUNT(DISTINCT(user_id)) " : "SELECT COUNT(*) ";
	$select_statement .= "FROM $wpdb->usermeta WHERE meta_key LIKE %s";
	$count = $wpdb->get_var( 
		$wpdb->prepare( 
			$select_statement, $value 
			) 	
		);
	return $count;
}

function tred_get_courses_completed_number() {	
	global $wpdb;
	$value = "%course_completed_%";
	$select_statement = "SELECT COUNT(DISTINCT(meta_key)) FROM $wpdb->usermeta WHERE meta_key LIKE %s";
	$count = $wpdb->get_var( 
		$wpdb->prepare( 
			$select_statement, $value 
			) 	
		);
	return $count;
}


function tred_get_learndash_post_types_comments() {
        
    $output = [];
    $args = array(
        'post_type' => ['sfwd-courses','sfwd-lessons','sfwd-topic', 'sfwd-quiz'],
        'status' => ['hold', 'approve'],
    );
    $comments = get_comments( $args );
    $output['total'] = count($comments);
    $output['items'] = $comments;
    
    return $output;
}

function tred_comments_by_course($comment_items) {
    if(empty($comment_items)) {
        return $output;
    }
    $output = [];
    $output['users'] = [];
    $output['courses'] = [];
    
    //if lesson or topic or quiz, check to which course belongs
    foreach($comment_items as $com) {
        $post_id = $com->comment_post_ID;
        $post_type = get_post_type($post_id);
        $course_id = ('sfwd-courses' === $post_type) ? $post_id : learndash_get_course_id($post_id);
        if(empty($course_id)) {
            continue;
        }
        $title = get_the_title($course_id); 
        if(empty($title)) {
            continue;
        }
        if(!isset($output['courses'][$course_id])) {
            $output['courses'][$course_id] = [];
            $output['courses'][$course_id]['course_title'] = $title;
            $output['courses'][$course_id]['total'] = 0;
            $output['courses'][$course_id]['approve'] = 0;
            $output['courses'][$course_id]['hold'] = 0;
        }
        $output['courses'][$course_id]['total'] += 1;
        if(!empty($com->comment_approved)) {
            $output['courses'][$course_id]['approve'] += 1;
        } else {
            $output['courses'][$course_id]['hold'] += 1;
        }
        $user_email = $com->comment_author_email;
        if(!isset($output['users'][$user_email])) {
            $output['users'][$user_email] = 0;
        }
        $output['users'][$user_email] += 1;
    } //end outter foreach

    arsort($output['users']);
    tred_sort_desc($output['courses'],'total');

    return $output;
}

function tred_learndash_get_activity_last_12_months() {

  $output = [];
  $last_12_months = tred_get_last_12_months_and_year_array(); //[ [ "year_month" => "2021_10", "year" => "2021", "month" => "10" ] ... ]
  $where_date = '(';
  $where_inside = '';
  
  //from the last 12 months array, exclude the ones that already have a correspondent transient
  foreach ($last_12_months as $k => $array) {
      
      if(!is_numeric($array['year']) || !is_numeric($array['month'])) {
          continue;
      }
      $year = $array['year'];
      $month = $array['month'];
      $year_month = $array['year_month'];

      $output[$year_month] = [];

      //mount the SQL where condition with the year_month values remaining on the array
      if(!empty($where_inside)) {
          $where_inside .= ' OR ';
      }
      $where_inside .= '(YEAR(FROM_UNIXTIME(activity_updated)) = "' . $year . '" AND MONTH(FROM_UNIXTIME(activity_updated)) = "' . $month . '")';
  } //end foreach

  if(empty($output)) {
      return $output;
  }

  //need to query
  $where_date .= $where_inside . ')';
  global $wpdb;
  $where_updated = '(activity_updated IS NOT NULL AND activity_updated != "" AND ' . $where_date . ')';
  $sql_select = 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE ';
  $activity = $wpdb->get_results( $sql_select .= $where_updated);
  
  foreach($activity as $act) {  
    $updated = $act->activity_updated;
    if(empty($updated)) {
        continue;
    }
    $m = date('m', $updated);
    $y = date('Y', $updated);
    $ym = $y . "_" . $m;
    $output[$ym][] = $act;
  } //end foreach

  return $output;    
}


//TEMPLATES FUNCTIONS
function tred_template_mount_box($box) { ?>
  <div class="w-full md:w-1/2 xl:w-1/3 p-6">
      <!--Metric Card-->
      <div
          class="bg-gradient-to-b from-<?php echo esc_attr($box['color']); ?>-200 to-<?php echo esc_attr($box['color']); ?>-100 border-b-4 border-<?php echo esc_attr($box['color']); ?>-600 rounded-lg shadow-xl p-5">
          <div class="flex flex-row items-center">
              <div class="flex-shrink pr-4">
                  <div class="rounded-full p-5 bg-<?php echo esc_attr($box['color']); ?>-600">
                      <i class="fa fa-<?php echo esc_attr($box['icon_class']); ?> fa-2x fa-inverse"></i>
                  </div>
              </div>
              <div class="flex-1 text-right md:text-center">
                  <h5 class="font-bold uppercase text-gray-600">
                      <?php echo esc_html($box['title']); ?>
                  </h5>
                  <h3 class="font-bold text-3xl">
                      <span id="<?php echo esc_attr($box['id']); ?>">
                          <img class="tred-loading-img inline border-none" alt="load"
                              src="<?php echo esc_url(TRED_LOADING_IMG_URL); ?>">
                      </span>
                      <!-- <span class="font-medium text-xs pl-2 text-green-600" id="top-students-total-variation">73%</span> 
        <span class="text-green-500">
        <i class="fas fa-caret-up" id="<?php //echo esc_attr($box['id']); ?>-variation-icon"></i>
        </span> -->
                  </h3>
                  <span class="font-thin text-xs">
                      <?php echo esc_html($box['obs']); ?>
                  </span>
              </div>
          </div>
      </div>
      <!--/Metric Card-->
  </div><?php
}

function tred_template_mount_chart($chart) { ?>
  <div class="w-full md:w-1/2 xl:w-1/2 p-6">
      <!--Graph Card-->
      <div class="bg-white border-transparent rounded-lg shadow-xl">
          <div
              class="bg-gradient-to-b from-gray-300 to-gray-100 uppercase text-gray-800 border-b-2 border-gray-300 rounded-tl-lg rounded-tr-lg p-2">
              <h5 class="font-bold uppercase text-gray-600">
                  <?php echo esc_html($chart['title']); ?>
              </h5>
          </div>
          <div class="p-5">
              <div class="chartjs-size-monitor">
                  <div class="chartjs-size-monitor-expand">
                      <div class=""></div>
                  </div>
                  <div class="chartjs-size-monitor-shrink">
                      <div class=""></div>
                  </div>
              </div>
              <canvas id="<?php echo esc_attr($chart['id']); ?>" class="chartjs chartjs-render-monitor"
                  width="625" height="312" style="display: block; width: 625px; height: 312px;"></canvas>
              <div class="text-center">
                  <span class="font-thin pl-2" id="<?php echo esc_attr($chart['id']); ?>-obs"
                      style="font-size: 0.8em;">
                      <?php echo esc_html($chart['obs']); ?>
                  </span>
                  <!-- <span class="font-thin pl-2" style="font-size: 0.6em;">
                    Atualização: 05/03/2021, às 10h16
                  </span> -->
              </div>
          </div>
      </div>
      <!--/Graph Card-->
  </div>

  <script>
    jQuery(document).ready(function($) {
      chartStatus = Chart.getChart("<?php echo esc_attr($chart['id']); ?>");
      if (chartStatus != undefined) {
          chartStatus.destroy();
      }
      new Chart(document.getElementById("<?php echo esc_attr($chart['id']); ?>"), {
          "type": "<?php echo esc_attr($chart['type']); ?>",
          "data": {
              "labels": ['Label A', 'Label B'],
              "datasets": [{
                  'label': 'Dataset Label',
                  'data': [20, 40],
                  'borderColor': 'rgb(54, 162, 235)',
                  'backgroundColor': 'rgba(255, 99, 132, 0.2)'
              }],
          },
          "options": {
              "indexAxis": "<?php echo esc_attr($chart['indexAxis']); ?>",
          }
      });
    });
  </script><?php
}

function tred_template_mount_table($table) { ?>
  <div class="w-full md:w-full xl:w-full p-6">
      <!--Table Card-->
      <div class="bg-white border-transparent rounded-lg shadow-xl">
          <div
              class="bg-gradient-to-b from-gray-300 to-gray-100 uppercase text-gray-800 border-b-2 border-gray-300 rounded-tl-lg rounded-tr-lg p-2">
              <h5 class="font-bold uppercase text-gray-600"><?php echo esc_html($table['title']); ?></h5>
          </div>
          <div class="p-5">
              <table class="w-full text-gray-700 table-auto border"
                  id="<?php echo esc_attr($table['table_id']); ?>">

                  <thead>
                      <tr class="text-sm">
                          <!-- ajax -->
                      </tr>
                  </thead>

                  <tbody>
                      <!-- ajax -->
                  </tbody>

              </table>

              <div class="clear"></div>
              <div class="dt-buttons tred-table-buttons"> 
                  <button class="dt-button tred-table-button" data-notify-html type="button"><span>Copy</span></button>
                  <button class="dt-button tred-table-button" type="button"><span>CSV</span></button>
                  <button class="dt-button tred-table-button" type="button"><span>Excel</span></button>
                  <button class="dt-button tred-table-button" type="button"><span>PDF</span></button>
                  <button class="dt-button tred-table-button" type="button"><span>Print</span></button>
              </div>
              
              <span class="py-2 tred-obs-table" id="obs-<?php echo esc_attr($table['table_id']); ?>">
                  <!-- ajax -->
              </span>
              <div style="clear: both"></div>
          </div>
      </div>
      <!--/table Card-->
  </div><?php
}

function tred_template_wptrat_links() { ?>

  <div class="tred-wptrat-announcements">
    <h3>More plugins from the WP Trat</h3>
    <ul>
        <li><a href="https://wptrat.com/easy-settings-for-learndash/" target="_blank">Easy Settings for LearnDash</a></li>
        <li><a href="https://wptrat.com/unenroll-for-learndash/" target="_blank">Unenroll for LearnDash</a></li>
        <li><a href="https://wptrat.com/next-step-for-learndash/" target="_blank">Next Step for LearnDash</a></li>
        <li><a href="https://wptrat.com/students-count-for-learndash/" target="_blank">Students Count for LearnDash</a></li>
        <li><a href="https://wptrat.com/plugins/course-completed-for-learndash/" target="_blank">Course Completed for LearnDash</a></li>
        <li><a href="https://wptrat.com/plugins/image-taxonomify-for-learndash/" target="_blank">Image Taxonomify for LearnDash</a></li>
        <li><a href="https://wptrat.com/plugins/grid-button-for-learndash/" target="_blank">Grid Button for LearnDash</a></li>
        <li><a href="https://wptrat.com/plugins/restrict-comments/" target="_blank">Restrict Comments</a></li>
        <li><a href="https://wptrat.com/plugins/master-paper-collapse-toggle/" target="_blank">Master Paper Collapse Toggle</a></li>
    </ul>
    <div class="tred-wptrat-arrows">
      <p>⇨ See more at <a href="https://wptrat.com?from=tred_settings_page">WPTrat</a></p>
      <p>⇨ Get support at <a href="mailto:luisrock@wptrat.com">support@wptrat.com</a></p>
    </div> 
  </div><?php
}