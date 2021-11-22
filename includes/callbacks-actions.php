<?php
//AJAX ACTIONS CALLBACKS
function tred_ld_students_courses() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']);
    $data_students_courses = tred_get_students_number_all_courses();
    if($data_students_courses) {    
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];
        $response['data']['top_boxes']['top-students-total'] = $data_students_courses['students']['total'];
        $response['data']['top_boxes']['top-courses-total'] = $data_students_courses['courses']['total'];
        
        $students_courses_items = (!empty($data_students_courses['courses']['items'])) ? $data_students_courses['courses']['items'] : [];

        foreach ($students_courses_items as $access_mode => $sci) { 
            //order by students, descending
            tred_sort_desc($sci,'students');
            $courses_titles = array_map( function( $val ) {
                return $val['title'];
            }, $sci );
            $courses_students = array_map( function( $val ) {
                return $val['students'];
            }, $sci );
            $courses_students_completed = array_map( function( $val ) {
                return $val['students_completed'];
            }, $sci ); 

            $chart = [];
            $chart['id'] = "chart-$access_mode-courses-students-completions";
            $chart['labels'] = $courses_titles;
            $datasets = [
                [
                    'label' => 'Students',
                    'data' =>  $courses_students,
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
                ],
                [
                    "label" => "Completions",
                    "data" => $courses_students_completed,
                    "type" => "line",
                    "fill" => false,
                    "borderColor" => "rgb(54, 162, 235)"
                ] 
            ];
            $chart['datasets'] = $datasets;
            $response['data']['charts'][] = $chart;
        } //end foreach
     
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_posts() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']);
    $ld_posts = tred_get_lessons_topics_quizzes_number();
    if($ld_posts) {    
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];
        $response['data']['top_boxes']['top-lessons-total'] = $ld_posts['lessons'];
        $response['data']['top_boxes']['top-topics-total'] = $ld_posts['topics'];
        $response['data']['top_boxes']['top-quizzes-total'] = $ld_posts['quizzes'];
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_essays_assignments() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']);
    $essays_pending_count = learndash_get_essays_pending_count();
    $assignments_pending_count = learndash_get_assignments_pending_count();    
    if(is_numeric($essays_pending_count) && is_numeric($assignments_pending_count)) {    
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];
        $response['data']['top_boxes']['top-essays-pending'] = $essays_pending_count;
        $response['data']['top_boxes']['top-assignments-pending'] = $assignments_pending_count;
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_groups() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']);
    $data_students_groups = tred_get_students_number_all_groups();
    $students_groups_total = (!empty($data_students_groups['students']['total'])) ? $data_students_groups['students']['total'] : 0;
    $groups_total = (!empty($data_students_groups['groups']['total'])) ? $data_students_groups['groups']['total'] : 0;
    $students_groups_items = (!empty($data_students_groups['groups']['items'])) ? $data_students_groups['groups']['items'] : [];
    tred_sort_desc($students_groups_items,'students');
    $groups_titles = array_map( function( $val ) {
            return $val['title'];
        }, $students_groups_items );
    $groups_students = array_map( function( $val ) {
            return $val['students'];
        }, $students_groups_items );

    if($data_students_groups) {    
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['top_boxes']['top-groups-total'] = $groups_total;
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];

        //First chart
        $chart = [];
        $chart['id'] = 'chart-groups-students';
        $chart['labels'] = $groups_titles;
        $chart['datasets'] = [];
        $dataset = [    
            'label' => 'Students',    
            'data' => $groups_students,
            'borderColor' => 'rgb(54, 162, 235)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
        ];
        $chart['datasets'][] = $dataset;
        $chart['obs'] = sprintf( __( 'Total students in groups: %s', 'learndash-easy-dash' ), $students_groups_total );
        //End first chart
        $response['data']['charts'][] = $chart;
        
        
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_comments() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']); 

    $comments_learndash_post_types = tred_get_learndash_post_types_comments();
    if($comments_learndash_post_types) {
        $comments_total = (!empty($comments_learndash_post_types['total'])) ? $comments_learndash_post_types['total'] : 0;
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];
        $response['data']['top_boxes']['top-comments-total'] = $comments_total;
        $comments_by_course = tred_comments_by_course($comments_learndash_post_types['items']);
        $comments_authors = (!empty($comments_by_course['users'])) ? $comments_by_course['users'] : [];
        $comments_courses = (!empty($comments_by_course['courses'])) ? $comments_by_course['courses'] : [];
        $most_commenting_courses_titles = array_map( function( $val ) {return $val['course_title'];}, $comments_courses );
        $most_commenting_courses_totals = array_map( function( $val ) {return $val['total'];}, $comments_courses );
        $most_commenting_courses_approveds = array_map( function( $val ) {return $val['approve'];}, $comments_courses );
        $most_commenting_courses_holds = array_map( function( $val ) {return $val['hold'];}, $comments_courses );
        $most_commenting_users_emails = array_keys( $comments_authors );
        $most_commenting_users_totals = array_values( $comments_authors );

        $chart = [];
        $chart['id'] = 'chart-most-commenting-users';
        $chart['labels'] = $most_commenting_users_emails;
        $datasets = [
            [
                'label' => 'Comments',
                'data' => $most_commenting_users_totals,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;

        $chart = [];
        $chart['id'] = 'chart-courses-with-more-comments';
        $chart['labels'] = $most_commenting_courses_titles;
        $datasets = [
            [
                'label' => 'Comments',
                'data' => $most_commenting_courses_totals,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],
            [
                "label" => "Approved",
                "data" => $most_commenting_courses_approveds,
                "type" => "line",
                "fill" => false,
                "borderColor" => "#44976A"
            ],
            [
                "label" => "Hold",
                "data" => $most_commenting_courses_holds,
                "type" => "line",
                "fill" => false,
                "borderColor" => "#D9782A"
            ]
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;

    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_activity() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']); 
    $response['result'] = 'success';
    $response['data'] = [];
    $response['data']['top_boxes'] = [];
    $response['data']['charts'] = [];
    $response['data']['tables'] = [];

    $activity = tred_learndash_get_activity();
    if(is_array($activity)) { 
        $course_activities_number = tred_learndash_get_item_activities_number( $activity, 'course' );
        
        $course_completions_last_x_days = tred_get_from_array($course_activities_number, 'completions');
        $course_starts_last_x_days = tred_get_from_array($course_activities_number, 'starts');
        $course_enrolls_last_x_days = tred_get_from_array($course_activities_number, 'enrolls');
        $users_activities_last_x_days = tred_learndash_rank_users_all_activities($activity);
        $users_activities_emails = tred_get_from_array($users_activities_last_x_days, 'emails', []);
        $users_activities_totals = tred_get_from_array($users_activities_last_x_days, 'totals', []);
        $users_activities_starts = tred_get_from_array($users_activities_last_x_days, 'starts', []);
        $users_activities_enrolls = tred_get_from_array($users_activities_last_x_days, 'enrolls', []);
        $users_activities_completions = tred_get_from_array($users_activities_last_x_days, 'completions', []);

        $response['data']['top_boxes']['top-course-completions'] = $course_completions_last_x_days;
        $response['data']['top_boxes']['top-course-starts'] = $course_starts_last_x_days;
        $response['data']['top_boxes']['top-course-enrolls'] = $course_enrolls_last_x_days;
        $response['data']['users_activities_last_x_days'] = $users_activities_last_x_days;

        $courses_ranked_by_activity_last_x_days = tred_learndash_rank_courses_by_activity($activity);
        $items_ranked_by_completions_last_x_days = tred_learndash_rank_courses_lessons_by_completion($activity);
        $courses_ranked_by_completions_last_x_days = tred_get_from_array($items_ranked_by_completions_last_x_days, 'courses');
        $lessons_ranked_by_completions_last_x_days = tred_get_from_array($items_ranked_by_completions_last_x_days, 'lessons');
        
        $most_completed_courses_titles = array_map( function( $val ) {return $val['title'];}, $courses_ranked_by_completions_last_x_days );
        $most_completed_courses_totals = array_map( function( $val ) {return $val['total'];}, $courses_ranked_by_completions_last_x_days );
        $most_completed_lessons_titles = array_map( function( $val ) {return $val['title'];}, $lessons_ranked_by_completions_last_x_days );
        $most_completed_lessons_totals = array_map( function( $val ) {return $val['total'];}, $lessons_ranked_by_completions_last_x_days ); 

        $response['data']['most_completed_courses_titles'] = $most_completed_courses_titles;
        $response['data']['most_completed_courses_totals'] = $most_completed_courses_totals;
        $response['data']['most_completed_lessons_titles'] = $most_completed_lessons_titles;
        $response['data']['most_completed_lessons_totals'] = $most_completed_lessons_totals;

        $chart = [];
        $chart['id'] = 'chart-most-active-students';
        $chart['labels'] = $users_activities_emails;
        $datasets = [
            [
            'label' => 'all',
            'data' => $users_activities_totals,
            'borderColor' => 'rgb(54, 162, 235)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],
            [
                "label" => "Enrolls",
                "data" => $users_activities_enrolls,
                "type" => "line",
                "fill" => false,
                "borderColor" => "#44976A"
            ],
            [
                "label" => "Starts",
                "data" => $users_activities_starts,
                "type" => "line",
                "fill" => false,
                "borderColor" => "#D9782A"
            ],
            [
                "label" => "Completions",
                "data" => $users_activities_completions,
                "type" => "line",
                "fill" => false,
                "borderColor" => "rgb(54, 162, 235)"
            ],      
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;

        $table_data = [];
        for ($i = 0; $i < count($users_activities_emails); $i++) {
            $a = [];
            $a['email'] = $users_activities_emails[$i];
            // $a['total'] = $users_activities_last_x_days['totals'][$i];
            $a['enrolls'] = $users_activities_enrolls[$i];
            $a['starts'] = $users_activities_starts[$i];
            $a['completions'] = $users_activities_completions[$i];
            $table_data[] = $a;
        }

        $table = [];
        $table['id'] = 'table-students-activity-last-x-days';
        $table['data'] = $table_data;
        $table['keys_labels'] = [
            'email' => 'Email',
            // 'total' => 'Total',
            'enrolls' => 'Enrolls',
            'starts' => 'Starts',
            'completions' => 'Completions'
        ];
        $table['obs'] = 'Enrolls: courses | Starts and completions: courses, lessons, topics, quizzes';
        $response['data']['tables'][] = $table;



        $chart = [];
        $chart['id'] = 'chart-most-completed-courses';
        $chart['labels'] = $most_completed_courses_titles;
        $datasets = [
            [
                'label' => 'Completions',
                'data' => $most_completed_courses_totals,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],      
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;

        $chart = [];
        $chart['id'] = 'chart-most-completed-lessons';
        $chart['labels'] = $most_completed_lessons_titles;
        $datasets = [
            [
                'label' => 'Completions',
                'data' => $most_completed_lessons_totals,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],      
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_courses_completions_stats() {
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']);
    
    $course_completions_stats = tred_learndash_get_course_completions_stats();
    
    if($course_completions_stats) { 
        $response['result'] = 'success';
        $response['data'] = [];
        $response['data']['top_boxes'] = [];
        $response['data']['charts'] = [];
        $response['data']['tables'] = [];

        $course_completions_same_day = $course_completions_stats['same_day'];
        $course_completions_same_day_courses = $course_completions_same_day['courses'];
        //order by times, descending
        arsort($course_completions_same_day_courses);
        $c_sd_titles = array_keys($course_completions_same_day_courses);
        $c_sd_values = array_values($course_completions_same_day_courses);

        $chart = [];
        $chart['id'] = 'chart-courses-completions-same-day';
        $chart['labels'] = $c_sd_titles;
        $datasets = [
            [
                'label' => 'times completed',
                'data' => $c_sd_values,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
            ],       
        ];
        $chart['datasets'] = $datasets;
        $response['data']['charts'][] = $chart;

        $table = [];
        $table['id'] = 'table-completion-course-stats';
        $table['data'] = $course_completions_stats['courses'];
        $table['keys_labels'] = [
            'title' => 'Course',
            'mode' => 'Mode',
            'students' => '#Enrolled',
            'total_completed' => '#Completed',
            'total_completed_percentage' => 'Students',
            'average_days' => '#Days/avg'
        ];
        $table['obs'] = 'completion average days (all courses): ' . $course_completions_stats['average_days'];
        $response['data']['tables'][] = $table;
 
    } else {
        $response['result'] = 'error';
    }
    echo json_encode($response);

   die();
}

function tred_ld_courses_stats_over_time() {
    
    if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'tred_nonce' ) ) {
        die( __( 'Security check', 'learndash-easy-dash' ) ); 
    }
    $response = [];
    $response['action'] = sanitize_text_field($_REQUEST['action']); 
    $response['result'] = 'success';
    $response['data'] = [];
    $response['data']['top_boxes'] = [];
    $response['data']['charts'] = [];
    $response['data']['tables'] = [];

    $activity_array = tred_learndash_get_activity_last_12_months();
    if(!is_array($activity_array) || empty($activity_array)) {
        echo json_encode($response);
        die();    
    }
    //sort activity by key date	
    uksort($activity_array, function ($a, $b) {
		$atime = DateTime::createFromFormat("Y_m", $a);
		$btime = DateTime::createFromFormat("Y_m", $b);
		return $atime->getTimestamp() - $btime->getTimestamp();
	});

    $act_array = [];
    $types = ['course','lesson','topic','quiz'];
    foreach($activity_array as $key => $activities) {
        $act_array[$key] = [];
        $act_array[$key]['course_enrolls'] = 0;
        foreach($types as $type) {
            $act_array[$key][$type . '_starts'] = 0;
            $act_array[$key][$type . '_completions'] = 0;
        }
        //foreach here
        foreach($activities as $act) {
            if( empty($act->post_id) && empty($act->course_id) ) {
                //one of them had to be present...
                continue;
            }
            if( empty($act->activity_type) ) {
                continue;
            }
            if( $act->activity_type == 'access' ) {
                $act_array[$key]['course_enrolls'] += 1;
                continue;
            }
            if( !in_array($act->activity_type, $types) ) {
                continue;
            }
            if(!empty($act->activity_completed) && !empty($act->activity_status)) {
                $act_array[$key][$act->activity_type  . '_completions'] += 1;
                continue;
            }
            if(!empty($act->activity_started) && empty($act->activity_status)) {
                $act_array[$key][$act->activity_type  . '_starts'] += 1;
                continue;
            }
        } //end inner foreach (activities)
    } //end outter foreach

    $chart = [];
    $chart['id'] = 'chart-courses-stats-over-time';
    $chart['labels'] = array_map(function($v){return tred_year_month_numbers_to_string_month_slash_year($v);},array_keys($act_array));
    $chart['slice'] = 'last';

    $datasets = [
        [
        'label' => 'Enrolls',
        'data' => array_values(array_map(function($v){return $v['course_enrolls'];},$act_array)),
        'borderColor' => 'rgb(54, 162, 235)',
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
        ],
        [
            "label" => "Starts",
            "data" => array_values(array_map(function($v){return $v['course_starts'];},$act_array)),
            "type" => "line",
            "fill" => false,
            "borderColor" => "#44976A"
        ],
        [
            "label" => "Completions",
            "data" => array_values(array_map(function($v){return $v['course_completions'];},$act_array)),
            "type" => "line",
            "fill" => false,
            "borderColor" => "#D9782A"
        ],      
    ];
    $chart['datasets'] = $datasets;
    $response['data']['charts'][] = $chart;
        
    echo json_encode($response);
    die();
}