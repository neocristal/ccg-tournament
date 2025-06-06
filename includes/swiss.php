<?php

function ccg_generate_swiss_pairings($tournament_id, $round) {
    global $wpdb;

    $players = $wpdb->get_results($wpdb->prepare("
        SELECT p.id, (
            SELECT COUNT(*) FROM {$wpdb->prefix}ccg_matches m 
            WHERE (m.player1_id = p.id AND m.result = 'player1') 
               OR (m.player2_id = p.id AND m.result = 'player2')
        ) as wins
        FROM {$wpdb->prefix}ccg_players p
        WHERE p.tournament_id = %d
        ORDER BY wins DESC
    ", $tournament_id));

    $used = [];
    $pairings = [];

    foreach ($players as $p1) {
        if (in_array($p1->id, $used)) continue;

        foreach ($players as $p2) {
            if ($p1->id === $p2->id || in_array($p2->id, $used)) continue;

            $alreadyPlayed = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->prefix}ccg_matches
                WHERE tournament_id = %d AND (
                    (player1_id = %d AND player2_id = %d) OR
                    (player1_id = %d AND player2_id = %d)
                )
            ", $tournament_id, $p1->id, $p2->id, $p2->id, $p1->id));

            if (!$alreadyPlayed) {
                $pairings[] = [$p1->id, $p2->id];
                $used[] = $p1->id;
                $used[] = $p2->id;
                break;
            }
        }
    }

    foreach ($pairings as $pair) {
        $wpdb->insert("{$wpdb->prefix}ccg_matches", [
            'tournament_id' => $tournament_id,
            'round_number' => $round,
            'player1_id' => $pair[0],
            'player2_id' => $pair[1],
            'result' => null,
            'story' => ''
        ]);
    }

    return $pairings;
}
