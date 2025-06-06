<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function ccg_export_tournament($tournament_id) {
    global $wpdb;

    $matches = $wpdb->get_results($wpdb->prepare("
        SELECT m.*, p1.name as player1, p2.name as player2
        FROM {$wpdb->prefix}ccg_matches m
        JOIN {$wpdb->prefix}ccg_players p1 ON m.player1_id = p1.id
        JOIN {$wpdb->prefix}ccg_players p2 ON m.player2_id = p2.id
        WHERE tournament_id = %d
    ", $tournament_id));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Round', 'Player 1', 'Player 2', 'Result', 'Story'], NULL, 'A1');

    $row = 2;
    foreach ($matches as $m) {
        $sheet->fromArray([$m->round_number, $m->player1, $m->player2, $m->result, $m->story], NULL, "A$row");
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $file = WP_CONTENT_DIR . "/uploads/ccg-tournament-{$tournament_id}.xlsx";
    $writer->save($file);

    return content_url("uploads/ccg-tournament-{$tournament_id}.xlsx");
}
