<?php

function ccg_create_tables() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta("
        CREATE TABLE {$wpdb->prefix}ccg_tournaments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            year INT,
            user_id BIGINT UNSIGNED,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;

        CREATE TABLE {$wpdb->prefix}ccg_players (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tournament_id INT,
            user_id BIGINT UNSIGNED,
            name VARCHAR(255),
            nickname VARCHAR(255),
            team VARCHAR(255),
            deck TEXT,
            avatar_url TEXT,
            character_status ENUM('live','in_prison','dead','withdrawn','banned') DEFAULT 'live'
        ) $charset;

        CREATE TABLE {$wpdb->prefix}ccg_matches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tournament_id INT,
            round_number INT,
            player1_id INT,
            player2_id INT,
            result ENUM('player1','player2','draw') DEFAULT NULL,
            story LONGTEXT,
            date_played DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;
    ");
}
