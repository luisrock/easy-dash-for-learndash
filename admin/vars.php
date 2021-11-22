<?php
/*
* TOP BOXES
*/
$top_boxes = [];

$last_x_days = sprintf( __( 'Last %s days', 'learndash-easy-dash' ), TRED_LAST_X_DAYS );

$total_students_box = [
  'title' => __('Students', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-students-total',
  'color' => 'green',
  'icon_class' => 'user-graduate',
  'widget_name' => 'total_students_box'
];
$top_boxes[] = $total_students_box;

$total_groups_box = [
  'title' => __('Groups', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-groups-total',
  'color' => 'purple',
  'icon_class' => 'users',
  'widget_name' => 'total_groups_box'
];
$top_boxes[] = $total_groups_box;

$total_comments_box = [
  'title' => __('Comments', 'learndash-easy-dash'),
  'obs' => 'Total for LD',
  'id' => 'top-comments-total',
  'color' => 'yellow',
  'icon_class' => 'comments',
  'widget_name' => 'total_comments_box'
];
$top_boxes[] = $total_comments_box;

$total_courses_box = [
  'title' => __('Courses', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-courses-total',
  'color' => 'blue',
  'icon_class' => 'chalkboard-teacher',
  'widget_name' => 'total_courses_box'
];
$top_boxes[] = $total_courses_box;

$total_lessons_box = [
  'title' => __('Lessons', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-lessons-total',
  'color' => 'pink',
  'icon_class' => 'chalkboard',
  'widget_name' => 'total_lessons_box'
];
$top_boxes[] = $total_lessons_box;

$total_topics_box = [
  'title' => __('Topics', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-topics-total',
  'color' => 'indigo',
  'icon_class' => 'clipboard-list',
  'widget_name' => 'total_topics_box'
];
$top_boxes[] = $total_topics_box;

$total_quizzes_box = [
  'title' => __('Quizzes', 'learndash-easy-dash'),
  'obs' => 'Total',
  'id' => 'top-quizzes-total',
  'color' => 'green',
  'icon_class' => 'question',
  'widget_name' => 'total_quizzes_box'
];
$top_boxes[] = $total_quizzes_box;

$course_enrolls_box = [
  'title' => __('Course Enrolls', 'learndash-easy-dash'),
  'obs' => $last_x_days,
  'id' => 'top-course-enrolls',
  'color' => 'purple',
  'icon_class' => 'door-open',
  'widget_name' => 'course_enrolls_box'
];
$top_boxes[] = $course_enrolls_box;

$course_starts_box = [
  'title' => __('Course Starts', 'learndash-easy-dash'),
  'obs' => $last_x_days,
  'id' => 'top-course-starts',
  'color' => 'yellow',
  'icon_class' => 'play',
  'widget_name' => 'course_starts_box'
];
$top_boxes[] = $course_starts_box;

$course_completions_box = [
  'title' => __('Course Completions', 'learndash-easy-dash'),
  'obs' => $last_x_days,
  'id' => 'top-course-completions',
  'color' => 'blue',
  'icon_class' => 'check',
  'widget_name' => 'course_completions_box'
];
$top_boxes[] = $course_completions_box;

$essays_pending_box = [
  'title' => __('Essays Pending', 'learndash-easy-dash'),
  'obs' => '',
  'id' => 'top-essays-pending',
  'color' => 'pink',
  'icon_class' => 'pen',
  'widget_name' => 'essays_pending_box'
];
$top_boxes[] = $essays_pending_box;

$assignments_pending_box = [
  'title' => __('Assignments Pending', 'learndash-easy-dash'),
  'obs' => '',
  'id' => 'top-assignments-pending',
  'color' => 'indigo',
  'icon_class' => 'upload',
  'widget_name' => 'assignments_pending_box'
];
$top_boxes[] = $assignments_pending_box;


/*
* TABLES
*/
$tables = [];

$table_completion_course_stats = [
    'title' => __('Courses Completions Stats', 'learndash-easy-dash'),
    'table_id' => 'table-completion-course-stats',
    'widget_name' => 'table_completion_course_stats'
];
$tables[] = $table_completion_course_stats;

$table_students_activity = [
    'title' => __('Students Activity', 'learndash-easy-dash' ) . ' (' . $last_x_days . ')',
    'table_id' => 'table-students-activity-last-x-days',
    'widget_name' => 'table_students_activity'
];
$tables[] = $table_students_activity;


/*
* CHARTS
*/
$charts = [];

$existant_access_modes = tred_get_access_modes_existent(); //array(4) { [0]=> string(6) "closed" [1]=> string(9) "subscribe" [2]=> string(4) "open" [3]=> string(6) "paynow" }
if(in_array('open', $existant_access_modes)) {
  $open_courses_students = [
    'id' => "chart-open-courses-students-completions",
    'title' => sprintf( __( 'Open Courses - Top %s (#enrolled)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
    'obs' => "",
    'type' => "bar",
    'indexAxis' => "x",
    'widget_name' => "chart_open_courses_students_completions",
  ];
  $charts[] = $open_courses_students; 
}

if(in_array('free', $existant_access_modes)) {
  $free_courses_students = [
    'id' => "chart-free-courses-students-completions",
    'title' => sprintf( __( 'Free Courses - Top %s (#enrolled)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
    'obs' => "",
    'type' => "bar",
    'indexAxis' => "x",
    'widget_name' => "chart_free_courses_students_completions",
  ];
  $charts[] = $free_courses_students; 
}

if(in_array('paynow', $existant_access_modes)) {
  $paynow_courses_students = [
    'id' => "chart-paynow-courses-students-completions",
    'title' => sprintf( __( 'Paynow Courses - Top %s (#enrolled)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
    'obs' => "",
    'type' => "bar",
    'indexAxis' => "x",
    'widget_name' => "chart_paynow_courses_students_completions",
  ];
  $charts[] = $paynow_courses_students; 
}

if(in_array('subscribe', $existant_access_modes)) {
  $subscribe_courses_students = [
    'id' => "chart-subscribe-courses-students-completions",
    'title' => sprintf( __( 'Subscribe Courses - Top %s (#enrolled)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
    'obs' => "",
    'type' => "bar",
    'indexAxis' => "x",
    'widget_name' => "chart_subscribe_courses_students_completions",
  ];
  $charts[] = $subscribe_courses_students; 
}

if(in_array('closed', $existant_access_modes)) {
  $closed_courses_students = [
    'id' => "chart-closed-courses-students-completions",
    'title' => sprintf( __( 'Closed Courses - Top %s (#enrolled)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
    'obs' => "",
    'type' => "bar",
    'indexAxis' => "x",
    'widget_name' => "chart_closed_courses_students_completions",
  ];
  $charts[] = $closed_courses_students; 
}


//GROUPS TOP X #STUDENTS
$chart_groups_students = [
  'id' => 'chart-groups-students',
  'title' => sprintf( __( 'Groups - Top %s (#members)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
  'obs' => '',
  'type' => 'bar',
  'indexAxis' => 'y',
  'widget_name' => 'chart_groups_students'
];
$charts[] = $chart_groups_students;


//MOST ACTIVE STUDENTS
$chart_most_active_students = [
  'id' => 'chart-most-active-students',
  'title' => __('Most Active Students', 'learndash-easy-dash' ),
  'obs' => __('All time', 'learndash-easy-dash' ),
  'type' => 'bar',
  'indexAxis' => 'x',
  'widget_name' => 'chart_most_active_students'
];
$charts[] = $chart_most_active_students;


// //LEAST ACTIVE STUDENTS (30 DAYS)
// $chart_least_active_students = [
//   'id' => 'chart-least-active-students',
//   'title' => 'Least Active Students (last ' . TRED_LAST_X_DAYS . ' days)',
//   'obs' => '',
//   'type' => 'bar', 
//   'indexAxis' => 'x',
//   'widget_name' => 'chart_least_active_students'
// ];
// $charts[] = $chart_least_active_students;


//MOST COMMENTING USERS
$chart_most_commenting_users = [
  'id' => 'chart-most-commenting-users',
  'title' => __('Most Commenting Users', 'learndash-easy-dash' ),
  'obs' => __('approved or on hold, all time', 'learndash-easy-dash' ),
  'type' => 'bar',
  'indexAxis' => 'y',
  'widget_name' => 'chart_most_commenting_users'
];
$charts[] = $chart_most_commenting_users;


//MOST COMPLETED COURSES (30 DAYS)
$chart_most_completed_courses = [
  'id' => 'chart-most-completed-courses',
  'title' => sprintf( __( 'Most Completed Courses (last %s days)', 'learndash-easy-dash' ), TRED_LAST_X_DAYS ),
  'obs' => '',
  'type' => 'bar',
  'indexAxis' => 'x',
  'widget_name' => 'chart_most_completed_courses'
];
$charts[] = $chart_most_completed_courses;


//MOST COMPLETED LESSONS (30 DAYS)
$chart_most_completed_lessons = [
  'id' => 'chart-most-completed-lessons',
  'title' => sprintf( __( 'Most Completed Lessons (last %s days)', 'learndash-easy-dash' ), TRED_LAST_X_DAYS ),
  'obs' => '',
  'type' => 'bar',
  'indexAxis' => 'y',
  'widget_name' => 'chart_most_completed_lessons'
];
$charts[] = $chart_most_completed_lessons;

//COURSES WITH MORE COMMENTS
$chart_courses_with_more_comments = [
  'id' => 'chart-courses-with-more-comments',
  'title' => __('Courses With More Comments', 'learndash-easy-dash' ),
  'obs' => __('Comments in course or in its content (lessons, topics, quizzes...)', 'learndash-easy-dash' ),
  'type' => 'bar',
  'indexAxis' => 'y',
  'widget_name' => 'chart_courses_with_more_comments'
];
$charts[] = $chart_courses_with_more_comments;


//COURSES COMPLETED IN THE SAME DAY #TIMES
$courses_completions_same_day_courses = [
  'id' => 'chart-courses-completions-same-day',
  'title' =>  sprintf( __( 'Courses Completed in The Same Day - Top %s (#times)', 'learndash-easy-dash' ), TRED_SELECT_X_ITEMS ),
  'obs' => '',
  'type' => 'bar',
  'indexAxis' => 'x',
  'widget_name' => 'courses_completions_same_day_courses'
];
$charts[] = $courses_completions_same_day_courses;

//COURSES ENROLLS OVERTIME
$courses_stats_over_time = [
  'id' => 'chart-courses-stats-over-time',
  'title' =>  __( 'Courses Stats Over Time', 'learndash-easy-dash' ),
  'obs' => '',
  'type' => 'bar',
  'indexAxis' => 'x',
  'widget_name' => 'courses_stats_over_time'
];
$charts[] = $courses_stats_over_time;

//numbering widgets
$i = 1;
foreach ($top_boxes as $k => $tb) {
  $top_boxes[$k]['number'] = $i;
  $top_boxes[$k]['widget_type'] = 'box';
  $i++;
}

foreach ($charts as $k => $ch) {
  $charts[$k]['number'] = $i;
  $charts[$k]['widget_type'] = 'chart';
  $i++;
}

foreach ($tables as $k => $ta) {
  $tables[$k]['number'] = $i;
  $tables[$k]['widget_type'] = 'table';
  $i++;
}