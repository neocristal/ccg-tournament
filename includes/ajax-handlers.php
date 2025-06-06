<?php

add_action('wp_ajax_ccg_save_player', function () {
    check_ajax_referer('ccg_nonce', 'nonce');
    global $wpdb;
    $id = intval($_POST['id']);
    $user_id = get_current_user_id();
    $data = [
        'name' => sanitize_text_field($_POST['name']),
        'nickname' => sanitize_text_field($_POST['nickname']),
        'deck' => sanitize_textarea_field($_POST['deck']),
        'avatar_url' => esc_url_raw($_POST['avatar'])
    ];

    if ($id) {
        $wpdb->update("{$wpdb->prefix}ccg_players", $data, ['id' => $id, 'user_id' => $user_id]);
    } else {
        $tournament = $wpdb->get_row($wpdb->prepare("
            SELECT id FROM {$wpdb->prefix}ccg_tournaments WHERE user_id = %d ORDER BY created_at DESC LIMIT 1
        ", $user_id));
        $data['user_id'] = $user_id;
        $data['tournament_id'] = $tournament->id;
        $data['character_status'] = 'live';
        $wpdb->insert("{$wpdb->prefix}ccg_players", $data);
    }

    wp_send_json_success();
});

add_action('wp_ajax_ccg_delete_player', function () {
    check_ajax_referer('ccg_nonce', 'nonce');
    global $wpdb;
    $id = intval($_POST['id']);
    $user_id = get_current_user_id();
    $wpdb->delete("{$wpdb->prefix}ccg_players", ['id' => $id, 'user_id' => $user_id]);
    wp_send_json_success();
});
