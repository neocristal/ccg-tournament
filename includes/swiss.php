<?php
require_once __DIR__ . '/scoring.php';

function ccg_calculate_rounds($count) {
    if ($count <= 8) return 3;
    if ($count <= 16) return 4;
    if ($count <= 32) return 5;
    if ($count <= 64) return 6;
    if ($count <= 128) return 7;
    if ($count <= 212) return 8;
    return 9;
}

function ccg_generate_swiss_pairings($tournament_id, $round) {
    global $wpdb;

    $players = $wpdb->get_results($wpdb->prepare("
        SELECT id FROM {$wpdb->prefix}ccg_players 
        WHERE tournament_id = %d AND character_status = 'live'
    ", $tournament_id));

    $scored = [];

    foreach ($players as $p) {
        $scored[] = [
            'id' => $p->id,
            'score' => ccg_calculate_player_points($p->id, $tournament_id)
        ];
    }

    usort($scored, fn($a, $b) => $b['score'] - $a['score']);

    $used = [];
    while (count($scored) > 1) {
        $p1 = array_shift($scored);
        foreach ($scored as $i => $p2) {
            $already = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->prefix}ccg_matches
                WHERE tournament_id = %d AND (
                    (player1_id = %d AND player2_id = %d) OR 
                    (player1_id = %d AND player2_id = %d)
                )
            ", $tournament_id, $p1['id'], $p2['id'], $p2['id'], $p1['id']));

            if (!$already) {
                $wpdb->insert("{$wpdb->prefix}ccg_matches", [
                    'tournament_id' => $tournament_id,
                    'round_number' => $round,
                    'player1_id' => $p1['id'],
                    'player2_id' => $p2['id'],
                    'result' => null,
                    'story' => ''
                ]);
                array_splice($scored, $i, 1);
                break;
            }
        }
    }
}
