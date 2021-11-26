<?php
/**
 * Plugin Name:  Easy Dash for LearnDash
 * Plugin URI: https://wptrat.com/easy-dash-for-learndash /
 * Description:  Easy Dash for LearnDash: an improved (and easy) dashboard for your LearnDash site
 * Author: Luis Rock
 * Author URI: https://wptrat.com/
 * Version: 1.4.0
 * Text Domain: learndash-easy-dash
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   Easy Dash for LearnDash
 */

//Template: https://www.tailwindtoolbox.com/templates/admin-template

if ( ! defined( 'ABSPATH' ) ) exit;

define("TRED_VERSION", "1.4.0");

// Check if LearnDash is active. If not, deactivate...
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( !is_plugin_active('sfwd-lms/sfwd_lms.php' ) ) {
    add_action( 'admin_init', 'tred_deactivate' );
    add_action( 'admin_notices', 'tred_admin_notice' );
    function tred_deactivate() {
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
    // Notice
    function tred_admin_notice() { ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <strong>
                    <?php echo esc_html_e( 'LearnDash LMS is not active: EASY DASH FOR LEARNDASH needs it, that\'s why was deactivated', 'learndash-easy-dash' ); ?>
                </strong>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">
                    Dismiss this notice.
                </span>
            </button>
        </div><?php
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] ); 
                }        
    } //end function tred_admin_notice
} //end if( !is_plugin_active('sfwd-lms/sfwd_lms.php' ) )


add_action( 'init', 'tred_load_textdomain' );
function tred_load_textdomain() {
  load_plugin_textdomain( 'learndash-easy-dash', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

$tredActions = [
    'tred_ld_posts',
    'tred_ld_students_courses',
    'tred_ld_essays_assignments',
    'tred_ld_groups',
    'tred_ld_comments',
    'tred_ld_activity',
    'tred_ld_courses_completions_stats',
    'tred_ld_items_stats_over_time'
];

$tredColors = [
    'red' => 'rgb(255, 99, 132)',
    'red_t' => 'rgb(255, 99, 132, 0.2)',
    'orange' => 'rgb(255, 159, 64)',
    'orange_t' => 'rgb(255, 159, 64, 0.2)',
    'yellow' => 'rgb(255, 205, 86)',
    'yellow_t' => 'rgb(255, 205, 86, 0.2)',
    'green' => 'rgb(75, 192, 192)',
    'green_t' => 'rgb(75, 192, 192, 0.2)',
    'blue' => 'rgb(54, 162, 235)',
    'blue_t' => 'rgb(54, 162, 235, 0.2)',
    'purple' => 'rgb(153, 102, 255)',
    'purple_t' => 'rgb(153, 102, 255, 0.2)',
    'grey' => 'rgb(201, 203, 207)',
    'grey_t' => 'rgb(201, 203, 207, 0.2)',
    'pink' => 'rgb(251, 207, 232)',
    'pink_t' => 'rgb(251, 207, 232, 0.2)',
];


$tredMonths = [
    1 => __('jan','learndash-easy-dash'),
    2 => __('feb','learndash-easy-dash'),
    3 => __('mar','learndash-easy-dash'),
    4 => __('apr','learndash-easy-dash'),
    5 => __('may','learndash-easy-dash'),
    6 => __('jun','learndash-easy-dash'),
    7 => __('jul','learndash-easy-dash'),
    8 => __('aug','learndash-easy-dash'),
    9 => __('sep','learndash-easy-dash'),
    10 => __('oct','learndash-easy-dash'),
    11 => __('nov','learndash-easy-dash'),
    12 => __('dec','learndash-easy-dash'),
  ];

define("TRED_ACTIONS", $tredActions);
define("TRED_COLORS", $tredColors);
define("TRED_MONTHS", $tredMonths);
define("TRED_LOADING_IMG_URL", admin_url('images/wpspin_light.gif'));
define("TRED_PRO_ACTIVATED", is_plugin_active('easy-dash-for-learndash-pro/learndash-easy-dash-pro.php' ));

// Requiring plugin files
require_once('admin/tred-settings.php');
require_once('includes/functions.php');
require_once('includes/callbacks-actions.php');
require_once('admin/vars.php');

//define constant if not defined
if(!defined('TRED_TOP_BOXES')){
    define('TRED_TOP_BOXES', $top_boxes);
}
if(!defined('TRED_CHARTS')){
    define('TRED_CHARTS', $charts);
}
if(!defined('TRED_TABLES')){
    define('TRED_TABLES', $tables);
}

function tred_register_all_scripts_and_styles() {
    wp_register_script('tred_admin_js', plugins_url('assets/js/tred-admin.js',__FILE__ ), ['chartjs','jquery'],'1.0.0',true);
    wp_register_script('chartjs', plugins_url('assets/js/Chart.js',__FILE__ ), [],'3.4.1',false);
    wp_register_script( 'datatables_js', plugins_url('assets/DataTables/datatables.min.js',__FILE__ ),['jquery'],'',true);
    wp_register_style( 'datatables_css', plugins_url('assets/DataTables/datatables.min.css',__FILE__ ) );
    wp_register_style( 'fontawsome', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
    wp_register_style('tred_tailwind_css', plugins_url('assets/css/tred-output.css',__FILE__ ));
    wp_register_style('tred_admin_css', plugins_url('assets/css/tred-admin.css',__FILE__ ));
    wp_register_script('notify_js', plugins_url( 'assets/js/notify.min.js', __FILE__ ),[ 'jquery' ],'1.0.0',false);
    wp_localize_script( 'tred_admin_js', 'tred_js_object',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            '_wpnonce' => wp_create_nonce('tred_nonce'),
            'sliceNumber' => TRED_SELECT_X_ITEMS,
            'tredActions' => TRED_ACTIONS,
            'tredColors' => TRED_COLORS
        )
    );
}
add_action( 'wp_loaded', 'tred_register_all_scripts_and_styles' );


//Scripts end styles
function tred_enqueue_admin_script( $hook ) {

    global $tred_settings_page;
    if( $hook != $tred_settings_page ) {
        return;
    }
    wp_enqueue_style('tred_tailwind_css');
    wp_enqueue_style('tred_admin_css');
    wp_enqueue_style('fontawsome');
    wp_enqueue_script('tred_admin_js');
    wp_enqueue_script('chartjs');
    wp_enqueue_script('notify_js');
    wp_enqueue_style('datatables_css');
    wp_enqueue_script('datatables_js');
}
add_action( 'admin_enqueue_scripts', 'tred_enqueue_admin_script' );

//add hooks and actions
foreach(TRED_ACTIONS as $action) {
    add_action( "wp_ajax_$action", $action );
}