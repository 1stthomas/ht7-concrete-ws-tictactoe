<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Application;

use Concrete\Package\Ht7C5WsServer\Application\AbstractStartStop;
use Concrete\Package\Ht7C5WsServer\Definitions\ApplicationStatus;
use Concrete\Package\Ht7C5WsServer\Definitions\ApplicationRunType;
use Concrete\Package\Ht7C5WsServer\Entity\WsApplication as WsApplicationEntity;
use Concrete\Package\Ht7C5WsServer\Messages\Error;
use Concrete\Package\Ht7C5WsServer\Messages\Success;

class Games extends AbstractStartStop
{
    public function __construct(WsApplicationEntity $entity)
    {
        $this->msgStartTitle = tc('ht7_c5_ws_tictactoe', 'Start TicTacToe');
        $this->msgStopTitle = tc('ht7_c5_ws_tictactoe', 'Stop TicTacToe');

        parent::__construct($entity);
    }
    public function handleFailedBaseValidation(int $runType)
    {
        switch ($runType) {
            case ApplicationRunType::APPLICATION_STOP:
                $title = $this->msgStopTitle;
                $msg = tc(
                    'ht7_c5_ws_tictactoe',
                    'Stoping tictactoe application failed due to missing permission.'
                );

                break;
            case ApplicationRunType::APPLICATION_START:
            default:
                $title = $this->msgStartTitle;
                $msg = tc(
                    'ht7_c5_ws_tictactoe',
                    'Starting tictactoe application failed due to missing permission.'
                );

                break;
        }

        $this->messageContainer = new Error($title, [$msg]);
    }
    public function start()
    {
//        exec('concrete/bin/concrete5 ht7:ws:start');

        $msg = tc('ht7_c5_ws_tictactoe', 'Starting tictactoe');

        $this->messageContainer = new Success($this->msgStartTitle, [$msg]);

        $this->changeStatus(ApplicationStatus::APPLICATION_STARTING);
    }
    public function stop()
    {

        $msg = tc('ht7_c5_ws_tictactoe', 'Stoping tictactoe');

        $this->messageContainer = new Success($this->msgStopTitle, [$msg]);

        $this->changeStatus(ApplicationStatus::APPLICATION_STOPING);
    }
}
