<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */

$isYourTurn = (int) $game->getAdminId() === (int) $user->getUserID();
$isKi = $game->getIsKi();
$seconds = $game->getTimeLimit();
$ui = $user->getUserInfoObject();
?>
<section id="ht7-tictactoe-playground">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc('ht7_c5_ws_tictactoe', 'Tic Tac Toe - Game'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <div
        data-finish-href="/ccm/ht7/ws/tictactoe/finish"
        data-game-hash="<?= $game->getHash(); ?>"
        data-identifier="<?= $appId; ?>"
        data-is-your-turn="<?= $isYourTurn ? '1' : '0'; ?>"
        data-is-ki="<?= $isKi; ?>"
        data-player-count="0"
        data-player-no="<?= $isYourTurn ? '1' : '2'; ?>"
        data-timelimit="<?= $seconds; ?>"
        data-title-finish-draw="<?= tc('ht7_c5_ws_tictactoe', 'This is a draw'); ?>"
        data-title-finish-lost="<?= tc('ht7_c5_ws_tictactoe', 'You have lost'); ?>"
        data-title-finish-won="<?= tc('ht7_c5_ws_tictactoe', 'You have won'); ?>"
        data-token="<?= $ui->getUserPassword(); ?>"
        data-user-id="<?= $user->getUserID(); ?>"
        data-user-name="<?= $ui->getUserName(); ?>"
        data-ws-url="<?= $wsUrl; ?>"
        id="ht7-ws-tictactoe">
        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <?php
                View::element(
                    'game/base',
                    compact(
                        'pkgHandle',
                        'pkgHandleBase',
                        'game',
                        'hasChat',
                        'identifier',
                        'isAdmin',
                        'isYourTurn',
                        'token',
                        'wsUrl',
                        'uId',
                        'uName'
                    ),
                    $pkgHandle
                );
                ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <section id="ht7-tictactoe-players">
                    <div class="players">
                        <ul class="well"></ul>
                        <template id="settings-player-item">
                            <li>
                                <div class="row">
                                    <span class="user-id" style="display: none;"></span>
                                    <div class="game-icon col-xs-2"></div>
                                    <div class="username col-xs-10"></div>
                                </div>
                            </li>
                        </template>
                    </div>
                    <div class="timer">
                        <?php
                        if ($game->getTimeLimit() > 0) {
                            $callback = 'ht7.ws.games.tictactoe.callbacks.gotTimelimitPassed';
                            $default = $game->getTimeLimit();

                            View::element(
                                'widgets/timer_simple',
                                compact(
                                    'callback',
                                    'default',
                                    'pkgHandle',
                                    'pkgHandleBase',
                                    'seconds'
                                ),
                                $pkgHandleBase
                            );
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
        <script>
            $(function() {
                ht7.ws.games.tictactoe.init();
            });
        </script>
    </div>
    <?php if ($hasChat) { ?>
        <article>
            <?php echo $cH->render(); ?>
        </article>
    <?php } ?>
    <?php View::element('widgets/body_overlay', [], 'ht7_c5_base'); ?>
</section>
