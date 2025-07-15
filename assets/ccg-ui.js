jQuery(document).ready(function ($) {
    $('.ccg-add-btn').on('click', function () {
        $('#player_id').val('');
        $('#player_name').val('');
        $('#player_nickname').val('');
        $('#player_deck').val('');
        $('#player_avatar').val('');
        $('#player_team').val('');
        $('#ccg-modal').fadeIn();
    });

    $('.ccg-edit-btn').on('click', function () {
        $('#player_id').val($(this).data('id'));
        $('#player_name').val($(this).data('name'));
        $('#player_nickname').val($(this).data('nick'));
        $('#player_deck').val($(this).data('deck'));
        $('#player_avatar').val($(this).data('avatar'));
        $('#player_team').val($(this).data('team'));
        $('#ccg-modal').fadeIn();
    });

    $('.ccg-delete-btn').on('click', function () {
        if (!confirm('Delete this player?')) return;
        $.post(ccgAjax.ajaxurl, {
            action: 'ccg_delete_player',
            nonce: ccgAjax.nonce,
            id: $(this).data('id')
        }, function () {
            location.reload();
        });
    });

    $('#ccg-player-form').on('submit', function (e) {
        e.preventDefault();
        $.post(ccgAjax.ajaxurl, {
            action: 'ccg_save_player',
            nonce: ccgAjax.nonce,
            id: $('#player_id').val(),
            name: $('#player_name').val(),
            nickname: $('#player_nickname').val(),
            deck: $('#player_deck').val(),
            avatar: $('#player_avatar').val(),
            team: $('#player_team').val()
        }, function () {
            location.reload();
        });
    });

    $('.ccg-close-modal').on('click', function () {
        $('#ccg-modal').fadeOut();
    });
});