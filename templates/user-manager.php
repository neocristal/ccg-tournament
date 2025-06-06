<?php
get_header();

if (!is_user_logged_in()) {
    echo "<p>Please log in to manage your team.</p>";
    get_footer();
    exit;
}

$user_id = get_current_user_id();
global $wpdb;

$players = $wpdb->get_results($wpdb->prepare("
    SELECT * FROM {$wpdb->prefix}ccg_players WHERE user_id = %d
", $user_id));
?>

<div class="ccg-dashboard">
    <h2>Your Team</h2>
    <button class="ccg-add-btn">+ Add Player</button>
    <table class="ccg-table">
        <tr><th>Avatar</th><th>Name</th><th>Nickname</th><th>Deck</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($players as $p): ?>
            <tr>
                <td><img src="<?= esc_url($p->avatar_url) ?>" style="width:40px; border-radius:50%;"></td>
                <td><?= esc_html($p->name) ?></td>
                <td><?= esc_html($p->nickname) ?></td>
                <td><?= esc_html($p->deck) ?></td>
                <td><?= esc_html($p->character_status) ?></td>
                <td>
                    <button class="ccg-edit-btn"
                        data-id="<?= $p->id ?>"
                        data-name="<?= esc_attr($p->name) ?>"
                        data-nick="<?= esc_attr($p->nickname) ?>"
                        data-deck="<?= esc_attr($p->deck) ?>"
                        data-avatar="<?= esc_url($p->avatar_url) ?>">✏️</button>
                    <button class="ccg-delete-btn" data-id="<?= $p->id ?>">🗑️</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Modal -->
<div class="ccg-modal" id="ccg-modal">
    <div class="ccg-modal-content">
        <form id="ccg-player-form">
            <input type="hidden" id="player_id" value="">
            <p><label>Name<br><input type="text" id="player_name" required></label></p>
            <p><label>Nickname<br><input type="text" id="player_nickname" required></label></p>
            <p><label>Deck<br><textarea id="player_deck" required></textarea></label></p>
            <p><label>Avatar URL<br><input type="url" id="player_avatar"></label></p>
            <p><button type="submit">Save</button> <button type="button" class="ccg-close-modal">Cancel</button></p>
        </form>
    </div>
</div>

<?php get_footer(); ?>