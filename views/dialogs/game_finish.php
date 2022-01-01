<?php
defined('C5_EXECUTE') or die('Access Denied.');

$urlReplay = $isKi ? '/tictactoe/lobby/ki' : '/tictactoe/lobby/human';
?>
<div style="text-align: center;">
    <?php
    if ($winner === 0) {
        ?>
        <div style="margin-bottom: 30px; margin-top: 430px;">
            <?= tc('', 'No one has won the game.'); ?>
        </div>
        <?php
    } elseif ($winner === 1) {
        ?>
        <h1><?= tc('', 'Congrats'); ?></h1>
        <img src="/packages/ht7_c5_ws_tictactoe/images/cup.PNG" alt="winner cup" style="height: 350px;" />
        <p><?= tc('', 'You have won the game!'); ?></p>
        <?php
    } elseif ($winner === 2) {
        ?>
        <div style="margin-bottom: 30px; margin-top: 430px;">
            <?= tc('', 'You have lost the game.'); ?>
        </div>
        <?php
    }
    ?>
    <div class="action-bar">
        <a class="btn btn-primary" href="/tictactoe/lobby">
            <?= tc('ht7_c5_ws_tictactoe', 'Lobby'); ?>
        </a>
        <a class="btn btn-primary" href="<?= $urlReplay; ?>">
            <?= tc('ht7_c5_ws_tictactoe', 'New Game'); ?>
        </a>
    </div>
</div>
