<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */

$uId = 0;
$pId = 0;

if (is_object($player)) {
    $pId = $player->getId();
    $uId = $player->getUser()->getUserID();
}
?>
<section id="ht7-tictactoe-mygames">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc('ht7_c5_ws_tictactoe', 'Tic Tac Toe - My Games'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <div id="ht7-ws-tictactoe" data-player-id="<?= $pId; ?>" data-user-id="<?= $uId; ?>">
        <?php
        View::element(
            'lobby/mygames',
            compact(
                'pkgHandle',
                'pkgHandleBase',
                'player'
            ),
            $pkgHandle
        );
        ?>
    </div>
    <?php View::element('widgets/body_overlay', [], 'ht7_c5_base'); ?>
</section>
