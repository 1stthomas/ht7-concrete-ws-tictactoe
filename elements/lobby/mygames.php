<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\User\UserInfoRepository;

/* @var $player Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer */
/* @var $games Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe[] */
$games = $player === null ? [] : $player->getTicTacToes();
$uiRepo = app(UserInfoRepository::class);
?>
<ul class="well game-list">
    <?php
    if (count($games) === 0) {
        ?>
        <li class="alert alert-info">
            <p><?= tc('ht7_c5_ws_tictactoe', 'No games played.'); ?></p>
        </li>
        <?php
    } else {
        $i = 0;

        foreach ($games as $game) {
            if ($game->getIsKi()) {
                continue;
            }

            $ps = $game->getHt7WsPlayers();
            ?>
            <li class="member-item item">
                <span class="no"><?= $i; ?></span>
                <?php
                $i2 = 0;

                foreach ($ps as $p) {
                    ?>
                    <span class="player-<?= ($i2 + 1); ?>">
                        <?= $p->getUser()->getUserName(); ?>
                    </span>
                    <?php if ($i2 === 0) { ?>
                        <span class="vs">&nbsp;<?= tc('ht7_c5_ws_tictactoe', 'vs'); ?>&nbsp;</span>
                        <?php
                    }
                    $i2++;
                }
                if ($game->getIsFinished()) {
                    if ($game->getWinnerId() > 0) {
                        $winner = $uiRepo->getByID($game->getWinnerId())->getUserName();
                        ?>
                        <span class="winner<?= $game->getWinnerId() == $player->getUser()->getUserID() ? ' is-you' : ''; ?>">
                            <?= tc('ht7_c5_ws_tictactoe', 'Winner: %s', $winner); ?>
                        </span>
                        <?php
                    } else {
                        ?>
                        <span class="winner">
                            <?= tc('ht7_c5_ws_tictactoe', 'Draw'); ?>
                        </span>
                        <?php
                    }
                    ?>
                    <span class="game-action">
                        <a
                            href="/tictactoe/game/watch/<?= $game->getHash(); ?>"
                            title="<?= tc('ht7_c5_ws_tictactoe', 'Watch the game at the end.'); ?>">
                                <?= tc('ht7_c5_ws_tictactoe', 'Watch'); ?>
                            <i aria-hidden="true" class="fa fa-sign-in-alt"></i>
                        </a>
                    </span>
                    <?php
                } else {
                    ?>
                    <span class="game-action">
                        <a
                            href="/tictactoe/game/restore/<?= $game->getHash(); ?>"
                            title="<?= tc('ht7_c5_ws_tictactoe', 'Restore the game.'); ?>">
                                <?= tc('ht7_c5_ws_tictactoe', 'Restore'); ?>
                            <i aria-hidden="true" class="fa fa-sign-in-alt"></i>
                        </a>
                    </span>
                    <?php
                }
                ?>
            </li>
            <?php
            $i++;
        }
    }
    ?>
</ul>
