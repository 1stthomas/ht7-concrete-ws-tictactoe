<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */
?>
<section class="ht7-games" id="ht7-tictactoe-hof">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc($pkgHandle, 'Tic Tac Toe - Hall of Fame'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <div id="ht7-ws-tictactoe">
        <?php
        View::element(
            'lobby/hof',
            compact(
                'pkgHandle',
                'pkgHandleBase',
                'games',
                'players'
            ),
            $pkgHandle
        );
        ?>
    </div>
    <?php View::element('widgets/body_overlay', [], 'ht7_c5_base'); ?>
</section>
