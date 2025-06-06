<?php

function ccg_calculate_player_points($player_id, $tournament_id) {
    global $wpdb;

    $matches = $wpdb->get_results($wpdb->prepare("
        SELECT result, player1_id, player2_id
        FROM {$wpdb->prefix}ccg_matches
        WHERE tournament_id = %d AND (player1_id = %d OR player2_id = %d)
        ORDER BY id ASC
    ", $tournament_id, $player_id, $player_id));

    $points = 0;
    $streak = 0;

    foreach ($matches as $m) {
        $is_p1 = $m->player1_id == $player_id;
        if ($m->result === 'draw') {
            $points += 1;
            $streak = 0;
        } elseif (($m->result === 'player1' && $is_p1) || ($m->result === 'player2' && !$is_p1)) {
            $points += 3;
            $streak++;
            if ($streak === 3) {
                $points += 1; // bonus point
            }
        } else {
            $streak = 0;
        }
    }

    return $points;
}
