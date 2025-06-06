<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function ccg_export_tournament($tournament_id) {
    global $wpdb;

    $matches = $wpdb->get_results($wpdb->prepare("
        SELECT m.*, p1.nickname as player1, p2.nickname as player2
        FROM {$wpdb->prefix}ccg_matches m
        JOIN {$wpdb->prefix}ccg_players p1 ON m.player1_id = p1.id
        JOIN {$wpdb->prefix}ccg_players p2 ON m.player2_id = p2.id
        WHERE m.tournament_id = %d
    ", $tournament_id));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Round', 'Player 1', 'Player 2', 'Result', 'Story'], NULL, 'A1');

    $row = 2;
    foreach ($matches as $m) {
        $sheet->fromArray([
            $m->round_number,
            $m->player1,
            $m->player2,
            $m->result,
            $m->story
        ], NULL, "A{$row}");
        $row++;
    }

    $file = WP_CONTENT_DIR . "/uploads/ccg-tournament-{$tournament_id}.xlsx";
    $writer = new Xlsx($spreadsheet);
    $writer->save($file);

    return content_url("uploads/ccg-tournament-{$tournament_id}.xlsx");
}

function ccg_export_stats($tournament_id) {
    global $wpdb;
    require_once __DIR__ . '/scoring.php';

    $players = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}ccg_players WHERE tournament_id = %d
    ", $tournament_id));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Player', 'Team', 'Wins', 'Losses', 'Draws', 'Win %'], NULL, 'A1');

    $row = 2;
    foreach ($players as $p) {
        $stats = ccg_get_player_stats($p->id, $tournament_id);
        $sheet->fromArray([
            $p->nickname,
            $p->team,
            $stats['wins'],
            $stats['losses'],
            $stats['draws'],
            $stats['winrate']
        ], NULL, "A{$row}");
        $row++;
    }

    $file = WP_CONTENT_DIR . "/uploads/ccg-player-stats-{$tournament_id}.xlsx";
    $writer = new Xlsx($spreadsheet);
    $writer->save($file);

    return content_url("uploads/ccg-player-stats-{$tournament_id}.xlsx");
}

function ccg_get_player_stats($player_id, $tournament_id) {
    global $wpdb;

    $stats = $wpdb->get_row($wpdb->prepare("
        SELECT 
            SUM(CASE WHEN (result = 'player1' AND player1_id = %d) OR (result = 'player2' AND player2_id = %d) THEN 1 ELSE 0 END) AS wins,
            SUM(CASE WHEN (result = 'player1' AND player2_id = %d) OR (result = 'player2' AND player1_id = %d) THEN 1 ELSE 0 END) AS losses,
            SUM(CASE WHEN result = 'draw' AND (player1_id = %d OR player2_id = %d) THEN 1 ELSE 0 END) AS draws
        FROM {$wpdb->prefix}ccg_matches
        WHERE tournament_id = %d
    ", $player_id, $player_id, $player_id, $player_id, $player_id, $player_id, $tournament_id));

    $total = $stats->wins + $stats->losses + $stats->draws;
    $winrate = $total > 0 ? round(($stats->wins / $total) * 100, 1) . '%' : '0%';

    return [
        'wins' => $stats->wins,
        'losses' => $stats->losses,
        'draws' => $stats->draws,
        'winrate' => $winrate
    ];
}
