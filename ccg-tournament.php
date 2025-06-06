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

function ccg_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ccg_tournaments';

    if ($_POST['ccg_add_tournament']) {
        $wpdb->insert($table, [
            'name' => sanitize_text_field($_POST['tournament_name']),
            'year' => intval($_POST['tournament_year']),
            'user_id' => get_current_user_id()
        ]);
        echo "<div class='notice notice-success'><p>Tournament created!</p></div>";
    }

    // Tournament list
    $tournaments = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

    echo "<div class='wrap'><h1>CCG Tournaments</h1>";

    // Add Tournament Form
    echo '<h2>Add New Tournament</h2>
    <form method="POST">
        <input type="text" name="tournament_name" placeholder="Tournament Name" required>
        <input type="number" name="tournament_year" placeholder="Year" required>
        <input type="submit" class="button button-primary" name="ccg_add_tournament" value="Add Tournament">
    </form>';

    echo '<h2>Existing Tournaments</h2>';
    echo '<table class="widefat"><thead><tr><th>Name</th><th>Year</th><th>Owner</th><th>Actions</th></tr></thead><tbody>';
    foreach ($tournaments as $t) {
        $user = get_userdata($t->user_id);
        echo "<tr>
            <td>{$t->name}</td>
            <td>{$t->year}</td>
            <td>{$user->user_login}</td>
            <td>
                <a href='?page=ccg-tournaments&view={$t->id}' class='button'>View</a>
                <a href='?page=ccg-tournaments&export={$t->id}' class='button'>Export</a>
            </td>
        </tr>";
    }
    echo "</tbody></table></div>";

function ccg_view_tournament($tid) {
    global $wpdb;

    echo "<h2>Players in Tournament #$tid</h2>";

    if ($_POST['ccg_add_player']) {
        $wpdb->insert("{$wpdb->prefix}ccg_players", [
            'tournament_id' => $tid,
            'user_id' => get_current_user_id(),
            'name' => sanitize_text_field($_POST['name']),
            'nickname' => sanitize_text_field($_POST['nickname']),
            'deck' => sanitize_textarea_field($_POST['deck']),
            'avatar_url' => esc_url_raw($_POST['avatar']),
            'character_status' => 'live'
        ]);
        echo "<div class='notice notice-success'><p>Player added.</p></div>";
    }

    $players = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}ccg_players WHERE tournament_id = %d
    ", $tid));

    echo '<h3>Add Player</h3>
    <form method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="nickname" placeholder="Nickname" required>
        <input type="text" name="deck" placeholder="Deck" required>
        <input type="url" name="avatar" placeholder="Avatar URL">
        <input type="submit" class="button" name="ccg_add_player" value="Add Player">
    </form>';

    echo "<h3>Current Players</h3>";
    echo "<table class='widefat'><thead><tr><th>Avatar</th><th>Name</th><th>Nickname</th><th>Deck</th><th>Status</th></tr></thead><tbody>";
    foreach ($players as $p) {
        echo "<tr>
            <td><img src='{$p->avatar_url}' width='40' /></td>
            <td>{$p->name}</td>
            <td>{$p->nickname}</td>
            <td>{$p->deck}</td>
            <td>{$p->character_status}</td>
        </tr>";
    }
    echo "</tbody></table>";
}

    // Export handler
    if (isset($_GET['export'])) {
        $file_url = ccg_export_tournament(intval($_GET['export']));
        echo "<script>window.location.href='$file_url';</script>";
    }

    // View tournament
    if (isset($_GET['view'])) {
        ccg_view_tournament(intval($_GET['view']));
    }
}

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
