<?php
/*
Plugin Name: CCG Tournament Manager
Description: Swiss tournament system with players, stats, avatars, storyline and Excel export.
Version: 1.0
Author: EnGarde Games, MB
*/

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/db-schema.php';
require_once __DIR__ . '/includes/swiss.php';
require_once __DIR__ . '/includes/export.php';
require_once __DIR__ . '/includes/ajax-handlers.php';

// Setup database
register_activation_hook(__FILE__, 'ccg_create_tables');

// Friendly URL for frontend dashboard
add_action('init', function () {
    add_rewrite_rule('^ccg-tournament-manager/?', 'index.php?ccg_manager=1', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'ccg_manager';
    return $vars;
});

add_action('template_redirect', function () {
    if (get_query_var('ccg_manager')) {
        include plugin_dir_path(__FILE__) . '/templates/user-manager.php';
        exit;
    }
});

// Admin menu
add_action('admin_menu', function () {
    add_menu_page('CCG Tournaments', 'CCG Tournaments', 'manage_options', 'ccg-tournaments', function () {
        global $wpdb;
        $tournaments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ccg_tournaments");
        echo "<div class='wrap'><h1>CCG Tournaments</h1><table class='widefat'><tr><th>Name</th><th>Year</th><th>Owner</th></tr>";
        foreach ($tournaments as $t) {
            $user = get_userdata($t->user_id);
            echo "<tr><td>{$t->name}</td><td>{$t->year}</td><td>{$user->user_login}</td></tr>";
        }
        echo "</table></div>";
    });
});

// Frontend assets
add_action('wp_enqueue_scripts', function () {
    if (is_page('ccg-tournament-manager')) {
        wp_enqueue_style('ccg-ui', plugin_dir_url(__FILE__) . 'assets/ccg-ui.css');
        wp_enqueue_script('ccg-ui', plugin_dir_url(__FILE__) . 'assets/ccg-ui.js', ['jquery'], null, true);
        wp_localize_script('ccg-ui', 'ccgAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('ccg_nonce')
        ]);
    }
});
