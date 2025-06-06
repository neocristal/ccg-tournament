<?php
/*
Plugin Name: CCG Tournament Manager
Description: Full Swiss tournament system with players, teams, brackets, emails, stats, REST, and visualizations.
Version: 2.0
Author: WordPressㆍCopilot
*/

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/db-schema.php';
require_once __DIR__ . '/includes/swiss.php';
require_once __DIR__ . '/includes/scoring.php';
require_once __DIR__ . '/includes/export.php';
require_once __DIR__ . '/includes/import.php';
require_once __DIR__ . '/includes/ajax-handlers.php';

register_activation_hook(__FILE__, 'ccg_create_tables');

add_action('init', function () {
    add_rewrite_rule('^ccg-tournament-manager/?', 'index.php?ccg_manager=1', 'top');
    add_rewrite_rule('^ccg-bracket/([0-9]+)/?', 'index.php?ccg_bracket=$matches[1]', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'ccg_manager';
    $vars[] = 'ccg_bracket';
    return $vars;
});
add_action('template_redirect', function () {
    if (get_query_var('ccg_manager')) {
        include plugin_dir_path(__FILE__) . '/templates/user-manager.php';
        exit;
    }
    if (get_query_var('ccg_bracket')) {
        echo do_shortcode('[ccg_bracket id="' . get_query_var('ccg_bracket') . '"]');
        exit;
    }
});
add_action('admin_menu', function () {
    add_menu_page('CCG Tournaments', 'CCG Tournaments', 'manage_options', 'ccg-tournaments', 'ccg_admin_page');
});
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'toplevel_page_ccg-tournaments') {
        wp_enqueue_style('ccg-admin', plugin_dir_url(__FILE__) . 'assets/ccg-ui.css');
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        wp_enqueue_script('ccg-sortable', plugin_dir_url(__FILE__) . 'assets/admin-sort.js', [], null, true);
    }
});
add_action('wp_enqueue_scripts', function () {
    if (is_page('ccg-tournament-manager')) {
        wp_enqueue_style('ccg-ui', plugin_dir_url(__FILE__) . 'assets/ccg-ui.css');
        wp_enqueue_script('ccg-ui', plugin_dir_url(__FILE__) . 'assets/ccg-ui.js', ['jquery'], null, true);
        wp_localize_script('ccg-ui', 'ccgAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ccg_nonce')
        ]);
    }
});

// Shortcodes
add_shortcode('ccg_user_dashboard', function () {
    ob_start();
    include plugin_dir_path(__FILE__) . '/templates/user-manager.php';
    return ob_get_clean();
});
add_shortcode('ccg_bracket', function ($atts) {
    $tid = intval($atts['id']);
    return ccg_render_bracket_view($tid);
});

// REST API
add_action('rest_api_init', function () {
    register_rest_route('ccg/v1', '/tournament/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'ccg_rest_tournament_view',
        'permission_callback' => '__return_true'
    ]);
});
function ccg_rest_tournament_view($data) {
    global $wpdb;
    $id = intval($data['id']);
    return [
        'tournament' => $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ccg_tournaments WHERE id = %d", $id)),
        'players' => $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ccg_players WHERE tournament_id = %d", $id)),
        'matches' => $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ccg_matches WHERE tournament_id = %d", $id)),
    ];
}
