<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

function ccg_import_excel($file_path, $tournament_id) {
    global $wpdb;

    $spreadsheet = IOFactory::load($file_path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $playerMap = [];
    $matches = 0;
    $players = 0;

    foreach ($rows as $i => $row) {
        if ($i === 0) continue;
        [$round, $p1_name, $p2_name, $result, $story] = $row;

        foreach ([$p1_name, $p2_name] as $name) {
            if (!isset($playerMap[$name])) {
                $wpdb->insert("{$wpdb->prefix}ccg_players", [
                    'name' => $name,
                    'nickname' => $name,
                    'deck' => '',
                    'avatar_url' => '',
                    'tournament_id' => $tournament_id,
                    'user_id' => get_current_user_id(),
                    'character_status' => 'live'
                ]);
                $playerMap[$name] = $wpdb->insert_id;
                $players++;
            }
        }

        $wpdb->insert("{$wpdb->prefix}ccg_matches", [
            'tournament_id' => $tournament_id,
            'round_number' => $round,
            'player1_id' => $playerMap[$p1_name],
            'player2_id' => $playerMap[$p2_name],
            'result' => $result,
            'story' => $story
        ]);
        $matches++;
    }

    return ['players' => $players, 'matches' => $matches];
}
