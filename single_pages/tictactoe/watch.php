<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */
$winner = $youWon ? tc($pkgHandle, 'You') :
    (is_object($winner) ? $winner->getUserName() :
    ($game->getIsFinished() ? tc($pkgHandle, 'Draw') :
    tc($pkgHandle, 'Still running')));
?>
<section id="ht7-tictactoe-playground">
    <header>
        <h2 class="naved">
            <span class="title">
                <?= tc('ht7_c5_ws_tictactoe', 'Tic Tac Toe - Replay'); ?>
            </span>
            <span class="links">
                <?php
                View::element('partials/nav_meta', [], $pkgHandle);
                ?>
            </span>
        </h2>
    </header>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-8">
            <div
                data-fields="<?= htmlentities(json_encode($game->getFields())); ?>"
                data-game-hash="<?= $game->getHash(); ?>"
                data-token="<?= $ui->getUserPassword(); ?>"
                data-user-id="<?= $u->getUserID(); ?>"
                data-user-name="<?= $ui->getUserName(); ?>"
                id="ht7-ws-tictactoe">
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
                            'token',
                            'wsUrl',
                            'uId',
                            'uName'
                        ),
                        $pkgHandle
                    );
                    ?>
                <script>
                    $(function() {
                        ht7.ws.games.tictactoe.watch.init();
                    });
                </script>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <h3><?= tc($pkgHandle, 'Winner: %s', $winner); ?></h3>
            <p><?php
                echo tc(
                    $pkgHandle,
                    'From: %s <br/>to: %s',
                    $game->getCreatedAt()->format('d.m.Y - H:i:s'),
                    $game->getUpdatedAt()->format('d.m.Y - H:i:s')
                );
                ?></p>
        </div>
    </div>
</section>
