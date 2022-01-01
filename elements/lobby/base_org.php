<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var $game \Concrete\Package\Ht7C5WsChatSimple\Entity\TicTacToe */

//$parameters = compact('pkgHandle');
?>
<section class="ht7-tictactoe" id="">
    <header class="row">
        <div class="col-xs-12 col-md-8">
            <ul>
                <?php foreach ($games as $game) { ?>
                    <li class="clearfix">
                        <span class="title">Game with uID: <?= $game->getAdminId(); ?></span>
                        <a href="/lobby/room/<?= $game->getHash(); ?>">
                            <?= tc($pkgHandle, 'participate'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <nav class="col-xs-12 col-md-4">
            <ul>
                <li>
                    <a class="btn btn-primary" href="/lobby/ki" style="margin-bottom: 5px;">
                        <?= tc($pkgHandle, 'Play against KI'); ?>
                    </a>
                    <a class="btn btn-primary" href="/lobby/human">
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
    </article>
    <article>
        <div
            class="ht7-chat-container"
            data-identifier="<?= $identifier; ?>"
            data-token="<?= $token; ?>"
            data-ws-url="<?= $wsUrl; ?>"
            data-user-avatar=""
            data-user-color=""
            data-user-id="<?= $uId; ?>"
            data-user-name="<?= $uName; ?>"
            id="ht7-ws-chat-simple">
                <?php
                View::element(
                        'chat/base',
                        compact(
                                'pkgHandle',
                                'pkgHandleBase'
                        ),
                        $pkgHandle
                );
                ?>
        </div>
        <script>
            $(function() {
                ht7.ws.chat.simple.init();
            });
        </script>
    </article>
    <footer class="clearfix">
        <p>footer...</p>
    </footer>
    <?php // View::element('chat/templates', [], $pkgHandle); ?>
</section>
<div class="ht7-ws-overlay"></div>
