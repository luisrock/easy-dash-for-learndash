<?php
//Define options (keys) and fields vitals (values)

$options_array = [

    'tred_top_boxes_to_hide' => [
        'type' => 'select',
        'kind' => 'multiple',
        'options' => [
            'total_students_box' => __('Students', 'learndash-easy-dash'),
            'total_groups_box' => __('Groups', 'learndash-easy-dash'),
            'total_comments_box' => __('Comments', 'learndash-easy-dash'),
            'total_courses_box' => __('Courses', 'learndash-easy-dash'),
            'total_lessons_box' => __('Lessons', 'learndash-easy-dash'),
            'total_topics_box' => __('Topics', 'learndash-easy-dash'),
            'total_quizzes_box' => __('Quizzes', 'learndash-easy-dash'),
            'course_enrolls_box' => __('Course Enrolls', 'learndash-easy-dash'),
            'course_starts_box' => __('Course Starts', 'learndash-easy-dash'),
            'course_completions_box' => __('Course Completions', 'learndash-easy-dash'),
            'essays_pending_box' => __('Essays Pending', 'learndash-easy-dash'),
            'assignments_pending_box' => __('Assignments Pending', 'learndash-easy-dash'),
        ],
        'default' => [],
        'description'=> __('Hide selected top boxes widgets on the dashboard.', 'learndash-easy-dash'),
        'obs' => __('select as many as you like, by Ctrl+Click (deselect by Ctrl+Click as well).', 'learndash-easy-dash'),
        'final' => __('Default: all top boxes widgets will be displayed.', 'learndash-easy-dash'),
        'order' => 1, 
    ],

    'tred_charts_to_hide' => [
        'type' => 'select',
        'kind' => 'multiple',
        'options' => [
            'chart_open_courses_students_completions'  => __('Open Courses (#enrolled)', 'learndash-easy-dash'), 
            'chart_free_courses_students_completions'  => __('Free Courses (#enrolled)', 'learndash-easy-dash'), 
            'chart_paynow_courses_students_completions'  => __('Paynow Courses (#enrolled)', 'learndash-easy-dash'), 
            'chart_recurring_courses_students_completions'  => __('Recurring Courses (#enrolled)', 'learndash-easy-dash'), 
            'chart_closed_courses_students_completions'  => __('Closed Courses (#enrolled)', 'learndash-easy-dash'), 
            'chart_groups_students'  => __('Groups (#members)', 'learndash-easy-dash'), 
            'chart_most_active_students'  => __('Most Active Students', 'learndash-easy-dash'), 
            'chart_most_commenting_users'  => __('Most Commenting Users', 'learndash-easy-dash'), 
            'chart_most_completed_courses'  => __('Most Completed Courses', 'learndash-easy-dash'), 
            'chart_most_completed_lessons'  => __('Most Completed Lessons', 'learndash-easy-dash'), 
            'chart_courses_with_more_comments'  => __('Courses With More Comments', 'learndash-easy-dash'), 
            'courses_completions_same_day_courses'  => __('Courses Completed in the Same Day', 'learndash-easy-dash'),
            'courses_stats_over_time'  => __('Courses Stats Over Time', 'learndash-easy-dash'), 
        ],
        'default' => [],
        'description'=> __('Hide selected charts widgets on the dashboard.', 'learndash-easy-dash'),
        'obs' => __('select as many as you like, by Ctrl+Click (deselect by Ctrl+Click as well).', 'learndash-easy-dash'),
        'final' => __('Default: all charts widgets will be displayed.', 'learndash-easy-dash'),
        'order' => 2, 
    ],

    'tred_tables_to_hide' => [
        'type' => 'select',
        'kind' => 'multiple',
        'options' => [
            'table_completion_course_stats' => __('Courses Completions Stats','learndash-easy-dash'),
            'table_students_activity' => __('Students Activity','learndash-easy-dash'),
        ],
        'default' => [],
        'description'=> __('Hide selected tables widgets on the dashboard.', 'learndash-easy-dash'),
        'obs' => __('select as many as you like, by Ctrl+Click (deselect by Ctrl+Click as well).', 'learndash-easy-dash'),
        'final' => __('Default: all tables widgets will be displayed.', 'learndash-easy-dash'),
        'order' => 3, 
    ],

    'tred_last_x_days' => [
        'type' => 'select',
        'kind' => '',
        'options' => ['7','10','14','30','60'],
        'default' => '30',
        'description'=> __('Some widgets in the dashboard limit queries to the last x days. Choose here how many days you want to limit.', 'learndash-easy-dash'),
        'obs' => __('All widgets with queries limited by days will be affected.', 'learndash-easy-dash'),
        'final' => __('Default: 30 days.', 'learndash-easy-dash'),
        'order' => 4, 
    ],
    'tred_cache_x_hours' => [
        'type' => 'select',
        'kind' => '',
        'options' => ['1','3','6','12','24','48'],
        'default' => '6',
        'description'=> __('Some queries are kept in cache for x hours. Choose here how many hours until cache should be refreshed.', 'learndash-easy-dash'),
        'obs' => __('All queries will be affected next time they are cached.', 'learndash-easy-dash'),
        'final' => __('Default: 6 hours.', 'learndash-easy-dash'),
        'order' => 5, 
    ],
    'tred_select_x_items' => [
        'type' => 'select',
        'kind' => '',
        'options' => ['3','5','10','15','30'],
        'default' => '10',
        'description'=> __('Some queries select only x items in the database. Choose here how many items you want to see selected', 'learndash-easy-dash'),
        'obs' => __('All queries that limit the number of items to be selected will be affected. Please note that your chart may look bad if too many items are queried.', 'learndash-easy-dash'),
        'final' => __('Default: 10 items.', 'learndash-easy-dash'),
        'order' => 6, 
    ],
];

define("TRED_OPTIONS_ARRAY", $options_array);
foreach(TRED_OPTIONS_ARRAY as $op => $vals) {
  $option = (get_option($op)) ? get_option($op) : $vals['default'];
  define(strtoupper($op),$option); 
}

function tred_admin_menu() {
    global $tred_settings_page;
    $tred_settings_page = add_submenu_page(
                            'learndash-lms', //The slug name for the parent menu
                            __( 'Easy Dash', 'learndash-easy-dash' ), //Page title
                            __( 'Easy Dash', 'learndash-easy-dash' ), //Menu title
                            'manage_options', //capability
                            'learndash-easy-dash', //menu slug 
                            'tred_admin_page' //function to output the content
    );
}
add_action( 'admin_menu', 'tred_admin_menu' );

function tred_register_plugin_settings() {
    foreach(TRED_OPTIONS_ARRAY as $op => $vals) {
        register_setting( 'tred-settings-group', $op );
    } 
}
//call register settings function
add_action( 'admin_init', 'tred_register_plugin_settings' );


function tred_admin_page() { ?>

<div class="tred-head-panel">
    <div id="tred-easydash-tabs" class="tred-tab-buttons">
        <a href="#" class="button active" data-target-content="tred-easydash-tab-dash">
            <?php esc_html_e('Dash', 'learndash-easy-dash'); ?>
        </a>
        <a href="#" class="button" data-target-content="tred-easydash-tab-settings">
            <?php esc_html_e('Settings', 'learndash-easy-dash'); ?>
        </a>
        <a href="#" class="button" data-target-content="tred-easydash-tab-shortcode">
            <?php esc_html_e('Shortcode', 'learndash-easy-dash'); ?>
        </a>
    </div>
</div>

<div class="bg-gray-100 font-sans leading-normal tracking-normal" data-new-gr-c-s-check-loaded="14.1019.0"
    data-gr-ext-installed="" cz-shortcut-listen="true">

    <div class="flex flex-col md:flex-row">

        <?php include_once('tred-dash.php'); ?>
        <!-- end tred-main-content tred-easydash-tab-dash -->

        <div class="main-content flex-1 bg-gray-100 mt-12 md:mt-2 pb-24 md:pb-5 tred-main-content tred-easydash-tab"
            id="tred-easydash-tab-settings" style="display:none">

            <div class="wrap tred-wrap-grid" style="display: flex;flex-wrap: wrap;justify-content: flex-start;align-items: flex-start;">
                <form method="post" action="options.php">

                    <?php settings_fields( 'tred-settings-group' ); ?>
                    <?php do_settings_sections( 'tred-settings-group' ); ?>

                    <div class="tred-form-fields">

                        <div class="tred-settings-title">
                            <?php esc_html_e( 'Easy Dash for LearnDash - Settings', 'learndash-easy-dash' ); ?>
                        </div>

                        <?php foreach(TRED_OPTIONS_ARRAY as $op => $vals)  { ?>

                        <div class="tred-form-fields-label">
                            <?php esc_html_e( $vals['description'], 'learndash-easy-dash' ); ?>
                            <?php if(!empty($vals['obs'])) { ?>
                            <span>* <?php esc_html_e( $vals['obs'], 'learndash-easy-dash' ); ?></span>
                            <?php } ?>
                        </div>
                        <div class="tred-form-fields-group">
                            <?php if($vals['type'] === 'select') { ?>
                            <!-- select -->
                            <div class="tred-form-div-select">
                                <label>
                                    <select
                                        name="<?php echo ($vals['kind'] === 'multiple') ? esc_attr( $op ) . '[]' : esc_attr( $op ); ?>"
                                        <?php echo esc_attr($vals['kind']); ?>>
                                        <?php if(empty($vals['options'])) {$vals['options'] = $vals['get_options']();} 
                                    foreach($vals['options'] as $v => $label) { 
                                        $pt = (is_integer($v)) ? $label : $v;
                                        ?>
                                        <option value="<?php echo esc_attr($pt); ?>" <?php
                                        if( empty(get_option($op)) && $vals['default'] === $pt ) {
                                            echo esc_attr('selected');
                                        } else if( $vals['kind'] === 'multiple' ) {
                                            if( is_array(get_option($op)) && in_array($pt,get_option($op)) ) {
                                                echo esc_attr('selected');
                                            }
                                        } else {
                                            selected($pt, get_option($op), true);
                                        }
                                    ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                        <?php } //end foreach ?>
                                    </select>
                                </label>
                            </div>
                            <?php } else if ($vals['type'] === 'text') { ?>
                            <!-- text -->
                            <input type="text" placeholder="<?php echo esc_attr($vals['default']); ?>" class=""
                                value="<?php echo esc_attr( get_option($op) ); ?>"
                                name="<?php echo esc_attr( $op ); ?>">
                            <?php } else if ($vals['type'] === 'textarea') { ?>
                            <!-- textarea -->
                            <textarea class="large-text" cols="80" rows="10"
                                name="<?php echo esc_attr( $op ); ?>"><?php echo esc_html( get_option($op) ); ?></textarea>
                            <?php } else if ($vals['type'] === 'checkbox') { ?>
                            <!-- checkbox -->
                            <div class="tred-form-div-checkbox">
                                <label>
                                    <input class="tred-checkbox" type="checkbox" name="<?php echo esc_attr( $op ); ?>"
                                        value="1" <?php checked(1, get_option( $op ), true); ?>>
                                    <?php if(!empty($vals['label'])) { ?>
                                    <span class="tred-form-fields-style-label">
                                        <?php esc_html_e( $vals['label'], 'tred-grid-button' ); ?>
                                    </span>
                                    <?php } ?>
                                </label>
                            </div>
                            <?php } ?>

                            <?php if(!empty($vals['final'])) { ?>
                            <span><?php esc_html_e($vals['final'], 'learndash-easy-dash' ); ?></span>
                            <?php } ?>
                        </div>
                        <hr>
                        <?php } //end foreach TRED_OPTIONS_ARRAY ?>

                        <?php submit_button(); ?>

                        <!-- <div style="float:right; margin-bottom:20px">
                            Contact Luis Rock, the author, at
                            <a href="mailto:lurockwp@gmail.com">
                                lurockwp@gmail.com
                            </a>
                        </div> -->

                    </div> <!-- end form fields -->
                </form>
                <?php tred_template_wptrat_links(); ?>
            </div> <!-- end tred-wrap-grid -->


        </div>
        <!-- end tred-main-content tred-easydash-tab-settings -->
        
        <?php include_once('tred-shortcode.php'); ?>
        <!-- end tred-main-content tred-easydash-tab-shortcode -->

    </div>
    <!-- end flex-row -->
</div>
<!-- end outter div -->









<?php } ?>