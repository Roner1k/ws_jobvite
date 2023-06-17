<?php
/**
 * Plugin Name: WS Jobvite
 * Plugin URI:  http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Joblist using jobvite API. UPD 2023
 * Version:     1.1
 * Author:      Ravi Kumar
 * Author URI:  http://URI_Of_The_Plugin_Author
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: my-toolset
 */

// Create custom plugin settings menu.
add_action('admin_menu', 'ws_jobvite_create_menu');

/**
 * Add a admin menu to jobvite settings.
 */
function ws_jobvite_create_menu()
{
    // Create new top-level menu.
    add_menu_page('WS Jobvite Settings', 'Jobvite Settings', 'administrator', 'jobvite', 'ws_jobvite_settings_page', 'dashicons-admin-generic');

    // Call register settings function.
    add_action('admin_init', 'register_ws_jobvite_plugin_settings');
}

/**
 * Register settings function.
 */
function register_ws_jobvite_plugin_settings()
{
    // Register our settings.
    register_setting('ws-jobvite-settings-group', 'jobvite_api_key');
    register_setting('ws-jobvite-settings-group', 'jobvite_secret_key');
    register_setting('ws-jobvite-settings-group', 'jobvite_company_id');
    register_setting('ws-jobvite-settings-group', 'jobvite_single_post_slug');
    register_setting('ws-jobvite-settings-group', 'jobvite_career_page_slug');
}

/**
 * Displays the page content for the jobvite admin menu page.
 */
function ws_jobvite_settings_page()
{
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('Jobvite Config'); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('ws-jobvite-settings-group'); ?>
            <?php do_settings_sections('ws-jobvite-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Jobvite API Key'); ?></th>
                    <td><input type="text" name="jobvite_api_key"
                               value="<?php echo esc_attr(get_option('jobvite_api_key')); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Jobvite Secret Key'); ?></th>
                    <td><input type="text" name="jobvite_secret_key"
                               value="<?php echo esc_attr(get_option('jobvite_secret_key')); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Jobvite Company ID'); ?></th>
                    <td><input type="text" name="jobvite_company_id"
                               value="<?php echo esc_attr(get_option('jobvite_company_id')); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Jobvite Single Posts Slug'); ?></th>
                    <td><input type="text" name="jobvite_single_post_slug" placeholder="open-position-page"
                               value="<?php echo esc_attr(get_option('jobvite_single_post_slug')); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Jobvite Career Page Slug'); ?></th>
                    <td><input type="text" name="jobvite_career_page_slug" placeholder="careers/current-opportunities"
                               value="<?php echo esc_attr(get_option('jobvite_career_page_slug')); ?>"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

/**
 * Register   scripts
 * */
function ws_jobvite_reg_files()
{
    wp_register_script('ws-jv-magnific-popup-js', plugins_url('js/jquery.magnific-popup.min.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('ws-jv-magnific-popup-js');
    wp_register_script('ws-jobvite-js', plugins_url('js/ws-jobvite.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('ws-jobvite-js');

    wp_enqueue_style('ws-jobvite-css', plugins_url('css/ws-jobvite.css', __FILE__), '', '1.0');
}

add_action('wp_enqueue_scripts', 'ws_jobvite_reg_files');
add_action('widgets_init', 'ws_jobvite_widgets_init', PHP_INT_MAX);
function ws_jobvite_widgets_init()
{

    register_sidebar(array(
        'name' => 'WS Jobvite Single Share',
        'id' => 'ws_jobvite_share',
        'before_widget' => '<div class="widget widget-ws_jobvite_share">',
        'after_widget' => '</div>',
        'before_title' => '<h5>',
        'after_title' => '</h5>',
    ));

}


/**
 * Display the content of jobvite Shortcode.
 *
 * @param array $atts containing attributes of shortcode.
 * @return string $output containing html of joblists
 */
function ws_jobvite_shortcode($atts)
{
    $output = ws_jobvite_list();
    return $output;
}

// Add hook for shortcode tag.
add_shortcode('ws-jobvite', 'ws_jobvite_shortcode');

/**
 * Jobvite API for the given region.
 *
 * @param string $region containing name of region.
 */
function ws_jobvite_query_api($iDs = '')
{
    $jobvite_api = get_option('jobvite_api_key');
    $jobvite_sc = get_option('jobvite_secret_key');
    $jobvite_company_id = get_option('jobvite_company_id');

    $query = array(
        'api' => $jobvite_api,
        'sc' => $jobvite_sc,
        'companyId' => $jobvite_company_id,
    );
    if (!empty($iDs)) $query['ids'] = $iDs;

    $jobvite_url = add_query_arg(
        $query,
        //'https://api.jobvite.com/v1/jobFeed'
        'https://api.jobvite.com/api/v2/job'
    );
    $response = wp_remote_get($jobvite_url);

    // Is the API up?
    if (!200 === wp_remote_retrieve_response_code($response)) {
        return false;
    }
    $body = wp_remote_retrieve_body($response);

    // Decode json.
    $data = json_decode(wp_remote_retrieve_body($response));

    // Ensure that the region exists.
    if (isset($data->error)) {
        return false;
    }
    $jobs = array();

    foreach ($data->requisitions as $job) {
        $jobs[] = $job;
    }
    return $jobs;
}

function ws_jobvite_create_options($k, $v)
{
    $b = "<option value='$k' name='$k'>$v</option>";
    return $b;
}

/**
 * Structures Jobvite data for display.
 *
 * @param string $region containg name of region.
 *
 * @return array
 *   A prepared render array of available jobs.
 */

function ws_jobvite_list()
{
    $jobs_list = '';
    $api_data = ws_jobvite_query_api();
    $jobs_qty = count($api_data);
    $jobvite_single_post_slug = '/' . rtrim(ltrim(get_option('jobvite_single_post_slug'), '/'), '/');

    $departments = array();
    $locations = array();
    $roles = array();

    foreach ($api_data as $job) {

        $d_key = sanitize_title($job->category);
        $l_key = sanitize_title($job->location);
        $r_key = sanitize_title($job->jobType);
        if (!in_array($job->category, $departments)) $departments[$d_key] = ws_jobvite_create_options($d_key, $job->category);;
        if (!in_array($job->location, $locations)) $locations[$l_key] = ws_jobvite_create_options($l_key, $job->location);
        if (!in_array($job->jobType, $roles)) $roles[$r_key] = ws_jobvite_create_options($r_key, $job->jobType);
    }

    ksort($departments);
    ksort($locations);
    ksort($roles);

    $departments = implode('', $departments);
    $locations = implode('', $locations);
    $roles = implode('', $roles);

    $jobs_list .= "<div class='ws-jv-wrap'>
<form class='ws-jv-filter-form'><fieldset class='jv-select-set'><select name='jv_departments' id='jv_departments'><option value='all' selected>All Departments</option>$departments</select></fieldset>
<fieldset class='jv-select-set'><select name='jv_locations' id='jv_locations'><option value='all' selected>All Locations</option>$locations</select></fieldset>
<fieldset class='jv-select-set'><select name='jv_roles' id='jv_roles'><option value='all' selected>All Roles</option>$roles</select></fieldset>
<fieldset><button type='submit' class='et_pb_button simple-btn' id='jv_submit'>Save Filter</button></fieldset></form>";

    $jobs_list .= "<div class='jv-jobs-count'>Results: <span class='jv-jobs-count-value'>$jobs_qty</span> open jobs</div>";

    $jobs_list .= "<div class='jv-jobvite-post-grid' data-ws-jv-qty='$jobs_qty'>";

    $data_keys = array_column($api_data, 'title');
    array_multisort($data_keys, SORT_ASC, $api_data);

    foreach ($api_data as $key => $job) {
        $single_url = $jobvite_single_post_slug . '?jobID=' . $job->eId;
        $jobs_list .= '<article class="jv-job-item" data-jvfilter-dep="' . sanitize_title($job->category) . '" data-jvfilter-loc="' . sanitize_title($job->location) . '" data-jvfilter-role="' . sanitize_title($job->jobType) . '" >';
        $jobs_list .= "<h4 class='jv-job-title'><a href='$single_url'>$job->title</a></h4>";
        $jobs_list .= "<div class='jv-job-location'>$job->location</div>";
        $jobs_list .= "<div class='jv-job-type'>$job->jobType</div>";
        $jobs_list .= "<div class='jv-job-detail'><a href='$single_url'><span>View Detail</span></a></div>";
        $jobs_list .= '</article>';
    }
    $jobs_list .= '</div><!-- jobvite-post-grid --> <p class="jv-no-results">No jobs were found for the selected filter criteria. </p>';

    $jobs_list .= '</div>';
    return $jobs_list;
}


/**
 * Template for the dynamic page.
 */
function ws_echo($str)
{
    if (!isset($str) || empty($str)) {
        echo '-';
        return false;
    }
    echo $str;
}

function ws_echo_if($str)
{
    if (!isset($str) || empty($str)) return false;

    return true;
}

function ws_jobvite_dynamic_page()
{
    $page_slug = ltrim(get_option('jobvite_single_post_slug'), '/');

    $uri = $_SERVER['REQUEST_URI'];
    $path = wp_parse_url($uri, PHP_URL_PATH);
    $pathQuery = parse_url($uri);

    if ('/' . trailingslashit($page_slug) === trailingslashit($path)) {
        parse_str($pathQuery['query'], $params);
        $careers_page_slug = '/' . ltrim(get_option('jobvite_career_page_slug'), '/');
        $cJob = ws_jobvite_query_api($params['jobID']);
        include 'ws-jobvite-single-template.php';
//         exit;

    }
}

add_action('et_before_main_content', 'ws_jobvite_dynamic_page');