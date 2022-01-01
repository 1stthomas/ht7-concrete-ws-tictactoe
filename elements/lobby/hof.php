<?php
defined('C5_EXECUTE') or die('Access Denied.');

//$parameters = compact('pkgHandle');
//foreach ($games as $game) {
?>
<ul class="well ht7-games-item-list">
    <?php
    if (empty($players)) {
        ?>
        <li class="alert alert-info">
            <p><?= tc($pkgHandle, 'No players found.'); ?></p>
        </li>
        <?php
    } else {
        ?>
        <li class="title">
            <h4 class="clearfix">
                <div class="ranking"><?= tc($pkgHandle, '#'); ?></div>
                <div class="name"><?= tc($pkgHandle, 'Name'); ?></div>
                <div class="stats"><?= tc($pkgHandle, 'stats'); ?></div>
            </h4>
        </li>
        <?php
        $i = 1;

        foreach ($players as $player) {
            ?>
            <li class="item clearfix">
                <div class="ranking"><?= $i; ?></div>
                <div class="name"><?= $player->getEntity()->getUser()->getUserName(); ?></div>
                <div class="stats">
                    <span class="quotation" title="<?= tc($pkgHandle, 'Winning quota'); ?>">
                        <?= tc($pkgHandle, '%s&#37;', $player->getQuotation()); ?>
                    </span>&nbsp;|&nbsp;
                    <span title="<?= tc($pkgHandle, 'Games played'); ?>">
                        <?= $player->getPlayed(); ?>
                    </span>&nbsp;|&nbsp;
                    <span title="<?= tc($pkgHandle, 'Games won'); ?>">
                        <?= $player->getWon(); ?>
                    </span>&nbsp;|&nbsp;
                    <span title="<?= tc($pkgHandle, 'Game draws'); ?>">
                        <?= $player->getDraw(); ?>
                    </span>&nbsp;|&nbsp;
                    <span title="<?= tc($pkgHandle, 'Games lost'); ?>">
                        <?= $player->getLost(); ?>
                    </span>
                </div>
            </li>
            <?php
            $i++;
        }
    }
    ?>
</ul>
