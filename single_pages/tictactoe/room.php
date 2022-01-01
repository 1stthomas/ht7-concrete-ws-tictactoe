<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section class="ht7-game-room">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc('ht7_c5_ws_tictactoe', 'Tic Tac Toe - Room'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <?php
    View::element(
        'lobby/room',
        compact(
            'pkgHandle',
            'pkgHandleBase',
            'cH',
            'game',
            'hasChat',
            'isAdmin',
            'appId',
            'u',
            'ui',
            'wsUrl'
        ),
        $pkgHandle
    );
    ?>
</section>
