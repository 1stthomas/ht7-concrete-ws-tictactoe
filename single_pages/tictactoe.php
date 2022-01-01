<?php
defined('C5_EXECUTE') or die('Access Denied.');

$u = app(\Concrete\Core\User\User::class);
?>
<section class="ht7-ffhs-main" id="ht7-tictactoe-intro">
    <header>
        <h1>
            <?= tc('ht7_c5_ws_server', 'Tic Tac Toe'); ?>
        </h1>
    </header>
    <article class="row" id="ht7-ws-tictactoe">
        <header class="col-xs-12 col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <h2>Try a game against the best of the world!</h2>
        </header>
        <section class="col-xs-12 col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <div>
                <img
                    alt="Image of a TicTacToe game"
                    src="/packages/ht7_c5_ws_tictactoe/images/tictactoe_intro.PNG"
                    />
            </div>
        </section>
        <footer class="col-xs-12 col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <?php if ($isRunning) { ?>
                <?php if (is_object($u) && $u->isRegistered()) { ?>
                    <div>
                        <a class="btn btn-success" href="/tictactoe/lobby">Let's play!</a>
                    </div>
                <?php } else { ?>
                    <div>
                        <a class="btn btn-primary" href="/register">Register first ...</a>
                        <span>&nbsp;&nbsp;&nbsp;</span>
                        <a class="btn btn-primary" href="/login">... or login</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-danger">
                    <p style="text-align: left;">
                        <?= tc('ht7_c5_ws_tictactoe', 'The WebSocket server is offline.'); ?>
                    </p>
                </div>
            <?php } ?>
            <?php if ($u->isRegistered()) { ?>
                <div class="additional-links">
                    <p>&nbsp;</p>
                    <p>
                        <a href="/tictactoe/hof">
                            <?= tc('ht7_c5_ws_tictactoe', 'hall of fame'); ?>
                        </a>
                        <?php if ($player !== null || true) { ?>
                            <span>&nbsp;|&nbsp;</span>
                            <a href="/tictactoe/mygames">
                                <?= tc('ht7_c5_ws_tictactoe', 'my games'); ?>
                            </a>
                        <?php } ?>
                    </p>
                </div>
            <?php } ?>
        </footer>
    </article>
</section>
