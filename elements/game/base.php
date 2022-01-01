<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<section class="ht7-tictactoe">
    <table class="game layout-open<?= $isYourTurn ? ' your-turn' : ''; ?>">
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div id="ht7-ws-tictactoe-overlay">
        <div class="bg"></div>
        <div class="text-container">
            <div class="text">
                <span class="pre"><?= tc('ht7_c5_ws_tictactoe', 'Player count: '); ?></span>
                <span class="count">0</span>
            </div>
        </div>
        <div class="loader-container">
            <div class="loader">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <template id="ht7-tictactoe-player-1">
        <i class="fa fa-times"></i>
    </template>
    <template id="ht7-tictactoe-player-2">
        <i class="fa fa-circle"></i>
    </template>
</section>
<div class="ht7-ws-overlay"></div>
