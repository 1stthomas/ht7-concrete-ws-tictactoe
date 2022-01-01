<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section class="ht7-ffhs-main ht7-games" id="ht7-tictactoe-lobby">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc('ht7_c5_ws_tictactoe', 'Tic Tac Toe - Lobby'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <?php
    View::element('widgets/action_alert', ['fb' => $fb], 'ht7_c5_base');

    View::element(
        'lobby/base',
        compact(
            'pkgHandle',
//            'pkgHandleBase',
            'appId',
            'games',
            'hasChat',
            'cH',
//            'identifier',
            'u',
            'ui',
            'wsUrl'
//            'uId',
//            'uName'
        ),
        $pkgHandle
    );
    ?>
</section>
