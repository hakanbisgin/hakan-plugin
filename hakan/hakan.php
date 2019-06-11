<?php
/*
Plugin Name: hakan demo plugin
Description: One day will use by millions.
Version: 0.0.1
*/
global $hakan_db_version;
$hakan_db_version = '0.0.1';
$cookie_suffix = '';
$method_no = null;
function install_plugin()
{
    global $wpdb;
    global $hakan_db_version;

    $table_name = $wpdb->prefix . 'post_likes';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		post_id int(11) NOT NULL,
		user_id char(36) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('hakan_db_version', $hakan_db_version);
}

function init_plugin()
{
    ob_start();
    global $cookie_suffix;

    $cookie_suffix2 = wp_generate_uuid4();
    add_option('hakan_user_cookie_suffix', $cookie_suffix2);
    global $method_no;
    $method_no = get_option('hakan_method_type');
    $user_id = get_user_id_with_method($method_no);
    add_option('hakan_method_type', 0);
    global $method_no;
    $method_no = get_option('hakan_method_type');
    $cookie_suffix = get_option('hakan_user_cookie_suffix');
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.4.1.js');
    wp_enqueue_script('hakan_js', plugin_dir_url(__FILE__) . "/inc/hakan.js", ['jquery']);
    wp_enqueue_style('hakan_css', plugin_dir_url(__FILE__) . "/inc/hakan.css");
    wp_localize_script('hakan_js', 'ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'), 'we_value' => 1234));
    if ($method_no == '0') {
        add_action('wp_ajax_like_form', 'like_form_submitted');
    } elseif ($method_no == '1') {
        add_action('wp_ajax_nopriv_like_form', 'like_form_submitted');
        add_action('wp_ajax_like_form', 'like_form_submitted');
    }
}

//plugin menu stats

function hakan_plugin_menu()
{
    add_options_page('Hakan stats', 'Hakan Plugin Stats', 'manage_options',
        'hakan-stats', 'stats_page');
}

function stats_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    require_once plugin_dir_path(__FILE__) . '/inc/stats_page.php';
}

function hakan_register_widgets()
{
    require_once plugin_dir_path(__FILE__) . "/inc/hakan_widget.php";
    register_widget('HakanWidget');
}

function add_like_button($content)
{
    global $method_no;
    $current_user_id = get_user_id_with_method($method_no);
    if ($current_user_id == false) {
        return $content;
    }
    $post = get_post();
    if ($post->post_type != 'post') {
        return $content;
    }
    $postID = !empty($post) ? $post->ID : false;

    $form = "<form action='' name='like_button_form' method='post'>
<input type='hidden' name='action' value='like_form'>
<input type='hidden' name='postidentificator' value='{$postID}'>
<button type='button' onclick='like_button_clicked(this,{$postID})'>";
    if (is_liked_before($postID, $current_user_id) == 1) {
        $form .= 'unlike';
    } else {
        $form .= 'like';
    }
    $form .= "</button>
            </form>";

    $content .= $form;
    return $content;
}

function like_form_submitted()
{
    global $method_no;
    $user_id = get_user_id_with_method($method_no);
    $post_id = $_POST['post_id'];
    if (is_liked_before($post_id, $user_id) == 0) {
        set_like_status($post_id, $user_id, 1);
        echo('liked');
    } else {
        set_like_status($post_id, $user_id, 0);
        echo('unliked');
    };

    wp_die();
}

function is_liked_before($post_id, $user_id)
{
    global $wpdb;
    $query = $wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->prefix}post_likes WHERE post_id= %d AND user_id= %s", $post_id, $user_id);
    return (int)$wpdb->get_var($query);
}

function set_like_status($post_id, $user_id, $like_status)
{
    global $wpdb;
    if ($like_status == 1) {//like
        $wpdb->insert($wpdb->prefix . 'post_likes', ['post_id' => $post_id, 'user_id' => $user_id], ['%d', '%s']);
    } else {
        $wpdb->delete($wpdb->prefix . 'post_likes', ['post_id' => $post_id, 'user_id' => $user_id]);
    }

    return $like_status;
}

function get_user_id_with_method($method_number = '0')
{
    if ($method_number == '0') {
        return get_current_user_id();
    } else {
        return get_or_create_user_cookie();
    }
}

function get_or_create_user_cookie()
{
    global $cookie_suffix;
    $cookie_value = $_COOKIE['hakan_' . $cookie_suffix];
    if (!isset($cookie_value) || empty($cookie_value) || is_null($cookie_value)) {
        $user_id = wp_generate_uuid4();
        setcookie('hakan_' . $cookie_suffix, $user_id, time() + (3 * 365 * 24 * 60 * 60), '/');
        return $user_id;
    }

    return $cookie_value;
}


add_action('init', 'init_plugin');

register_activation_hook(__FILE__, 'install_plugin');

add_action('admin_menu', 'hakan_plugin_menu');

add_action('widgets_init', 'hakan_register_widgets');

add_filter('the_content', 'add_like_button');