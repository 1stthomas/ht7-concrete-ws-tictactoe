<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\GameSettings;

/**
 * Game layout message.
 */
class GameLayout extends GameSettings
{
    public function __construct(array $msg)
    {
        parent::__construct($msg);

        $this->action = 'game_layout';
    }
}
