<?php
defined('C5_EXECUTE') or die('Access Denied.');

//$parameters = compact('pkgHandle');
$hash = $game->getHash();
$disabled = $isAdmin ? '' : ' disabled="disabled"';
?>
<section
    class="ht7-tictactoe"
    data-identifier="<?= $appId; ?>"
    data-game-hash="<?= $hash; ?>"
    data-player-no="<?= $isAdmin ? '1' : '2'; ?>"
    data-token="<?= $ui->getUserPassword(); ?>"
    data-user-id="<?= $u->getUserID(); ?>"
    data-user-name="<?= $ui->getUserName(); ?>"
    data-ws-url="<?= $wsUrl; ?>"
    id="ht7-game-container">
    <header class="row">
        <!--<header class="clearfix">-->
        <div class="col-xs-12 col-sm-8 col-md-9">
            <div class="settings-container row">
                <div class="settings-general col-xs-12 col-md-4">
                    <div class="form-group">
                        <label class="control-label" style="display: block;">
                            <div><?= tc($pkgHandle, 'Time limit in s'); ?></div>
                            <input
                                class="form-control"<?= $disabled; ?>
                                max="50"
                                min="0"
                                name="time-limit"
                                type="number"
                                value="<?= $game->getTimeLimit(); ?>" />
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="control-label" style="display: block;">
                            <input
                                class="checkbox"<?= $disabled; ?>
                                name="show-borders"
                                style="display: inline;"
                                type="checkbox"
                                value="1" />
                            <span><?= tc($pkgHandle, 'Show outer borders'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="settings-personal col-xs-12 col-md-8">
                    <div>
                        <label class="control-label">
                            <?= tc($pkgHandle, 'Players'); ?>
                        </label>
                    </div>
                    <ul class="players well">
                        <!-- will be field with the players -->
                    </ul>
                </div>
            </div>
            <template id="settings-player-item">
                <li>
                    <div class="row">
                        <div class="game-icon col-xs-1"></div>
                        <div class="username col-xs-11"></div>
                        <!--                    <div class="avatar col-xs-3"></div>
                                            <div class="username col-xs-3"></div>
                                            <div class="game-icon col-xs-3"></div>-->
                    </div>
                </li>
            </template>
        </div>
        <nav class="col-xs-12 col-sm-4 col-md-3">
            <ul>
                <?php if ($isAdmin) { ?>
                    <li>
                        <a
                            class="btn btn-success game-action"
                            href="/tictactoe/game/launch/<?= $hash; ?>"
                            id="ht7-game-launch"
                            style="display: none;">
                                <?= tc($pkgHandle, 'launch'); ?>
                        </a>
                        <a
                            class="btn btn-primary game-action"
                            href="/tictactoe/lobby/abort/<?= $hash; ?>"
                            id="ht7-game-abort">
                                <?= tc($pkgHandle, 'abort'); ?>
                        </a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a
                            class="btn btn-primary game-action"
                            href="/tictactoe/game/leave/<?= $hash; ?>"
                            id="ht7-game-leave">
                                <?= tc($pkgHandle, 'leave'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </header>
    <script>
        $(function() {
            ht7.ws.games.tictactoe.room.init();
        });
    </script>
    <?php if ($hasChat) { ?>
        <article>
            <?php echo $cH->render(); ?>
        </article>
    <?php } ?>
    <?php // View::element('chat/templates', [], $pkgHandle); ?>
</section>
<div class="ht7-ws-overlay"></div>
