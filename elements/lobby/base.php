<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */

//$parameters = compact('pkgHandle');
?>
<section
    class="ht7-tictactoe"
    data-identifier="<?= $appId; ?>"
    data-token="<?= $ui->getUserPassword(); ?>"
    data-user-id="<?= $u->getUserID(); ?>"
    data-user-name="<?= $ui->getUserName(); ?>"
    data-ws-url="<?= $wsUrl; ?>"
    id="ht7-game-container">
    <header class="row" style="border-bottom: none;">
        <div class="col-xs-12 col-md-8 container-games">
            <h3><?= tc($pkgHandle, 'Open Games'); ?></h3>
            <div class="well">
                <ul class="game-list games-open ht7-games-item-list">
                    <li class="clearfix empty-list"<?= empty($games) ? '' : ' style="display: none;"' ?>>
                        <div class="alert alert-warning">
                            <p>
                                <?= tc($pkgHandle, 'No open games'); ?>
                            </p>
                        </div>
                    </li>
                    <?php
                    foreach ($games as $game) {
                        $u = User::getByUserID($game->getAdminId());
                        ?>
                        <li
                            class="item clearfix"
                            data-hash="<?= $game->getHash(); ?>">
                            <a href="/tictactoe/lobby/room/<?= $game->getHash(); ?>">
                                <span class="title">
                                    <?= tc($pkgHandle, 'Player %s', $u->getUserName()); ?>
                                </span>
                                <i
                                    aria-hidden="true"
                                    class="fa fa-sign-in-alt"
                                    title="<?= tc($pkgHandle, 'participate'); ?>"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <template id="ht7-game-open-row">
                    <li
                        class="item clearfix"
                        data-hash="#hash#">
                        <a href="/tictactoe/lobby/room/">
                            <span class="title">
                                <?= tc($pkgHandle, 'Player '); ?>
                            </span>
                            <i
                                aria-hidden="true"
                                class="fa fa-sign-in-alt"
                                title="<?= tc($pkgHandle, 'participate'); ?>"></i>
                        </a>
                    </li>
                </template>
            </div>
        </div>
        <nav class="col-xs-12 col-md-4 container-action">
            <ul>
                <li>
                    <a class="btn btn-primary game-action" href="/tictactoe/lobby/ki">
                        <?= tc($pkgHandle, 'Play against KI'); ?>
                    </a>
                    <a class="btn btn-primary game-action" href="/tictactoe/lobby/human">
                        <?= tc($pkgHandle, 'Start a new Game'); ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <article class="clearfix">
        <section class="open-games-container row">
            <template>
                <div class="row>">
                    <div class="col-xs-12">

                    </div>
                </div>
            </template>
            <?php
//            foreach ($openGames as $game) {
//
//            }
            ?>
        </section>
        <section class="restore-games-container row">

        </section>
        <section></section>
        <script>
            $(function() {
                ht7.ws.games.tictactoe.lobby.init();
            });
        </script>
    </article>
    <article>
        <?php echo $cH->render(); ?>
    </article>
    <?php // View::element('chat/templates', [], $pkgHandle); ?>
</section>
<div class="ht7-ws-overlay"></div>
