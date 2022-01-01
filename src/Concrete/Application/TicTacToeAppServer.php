<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Application;

use Concrete\Core\Support\Facade\Application;
use Concrete\Package\Ht7C5WsServer\Application\AbstractAppServer;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Concrete\Package\Ht7C5WsServer\Messages\Initial;
use Concrete\Package\Ht7C5WsServer\Messages\MessageBaseInterface;
use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;
use Concrete\Package\Ht7C5WsTictactoe\Messages\Error;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameDelete;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameFinish;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameLayout;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameLeave;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameLost;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameSettings;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameStart;
use Concrete\Package\Ht7C5WsTictactoe\Messages\GameTurn;
use Concrete\Package\Ht7C5WsTictactoe\Messages\Hello;
use Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyAdd;
use Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyGameAdd;
use Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyGameRemove;
use Concrete\Package\Ht7C5WsTictactoe\Messages\Welcome;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe as TicTacToeEntity;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\ConnectionInterface;

class TicTacToeAppServer extends AbstractAppServer
{
    protected $clients;

    /**
     *
     * @var TicTacToeEntity[]
     */
    protected $games;
    protected $lobby;

    /**
     * @var \Concrete\Package\Ht7C5WsTictactoe\Services\Winner
     */
    protected $wH;

    public function __construct(array $validUsers = [])
    {
        $application = (new Provider())->getApplicationByHandle('tictactoe');
        $identifier = $application->getIdentifier();

        $this->games = [];
        $this->lobby = [];
        $this->wH = Application::getFacadeApplication()->make('ht7/ws/tictactoe/winner');

        \Log::addEntry('fuege tictactoe server hinzu - class: ' . get_class($this));

        parent::__construct($identifier, $validUsers, $application->getValidUserGroups());
    }
    public function addField(string $hash, int $player, array $coor)
    {
        $this->games[$hash]['fields'][$coor[0]][$coor[1]] = $player;
    }
    public function addGame(TicTacToeEntity $entity)
    {
        $hash = $entity->getHash();

        $this->games[$hash] = $entity->jsonSerialize();
        $this->games[$hash]['borders_outer'] = 0;
        $this->games[$hash]['entity'] = $entity;
        $this->games[$hash]['fields'] = [[], [], []];
        $this->games[$hash]['isRunning'] = 0;
        $this->games[$hash]['moves'] = [];
        $this->games[$hash]['timelimit'] = 0;
        $this->games[$hash]['whosTurn'] = 1;
//        $this->games[$entity->getHash()] = $entity;
    }
    public function createMessageInstance(Initial $msg)
    {
        if (in_array($msg->getReleaser(), ['user', 'server'])) {
            $raw = $msg->getRaw();

            if ($msg->getAction() === 'hello') {

                return new Hello($raw);
            } elseif ($msg->getAction() === 'delete') {

                return new GameDelete($raw);
            } elseif ($msg->getAction() === 'finish') {

                return new GameFinish($raw);
            } elseif ($msg->getAction() === 'game_settings') {

                return new GameSettings($raw);
            } elseif ($msg->getAction() === 'leave') {

                return new GameLeave($raw);
            } elseif ($msg->getAction() === 'lobby_add') {

                return new LobbyAdd($raw);
            } elseif ($msg->getAction() === 'lobby_game_add') {

                return new LobbyGameAdd($raw);
            } elseif ($msg->getAction() === 'lobby_game_remove') {

                return new LobbyGameRemove($raw);
            } elseif ($msg->getAction() === 'lost') {

                return new GameLost($raw);
            } elseif ($msg->getAction() === 'start') {

                return new GameStart($raw);
            } elseif ($msg->getAction() === 'turn') {

                return new GameTurn($raw);
            } elseif ($msg->getAction() === 'welcome') {

                return new Welcome($raw);
            }
        }
    }
    public function getGame(string $hash)
    {
        return empty($this->games[$hash]) ? null : $this->games[$hash];
    }
    public function handleClose(ConnectionInterface $conn)
    {
        if (in_array($conn, $this->lobby)) {
            unset($this->lobby[array_search($conn, $this->lobby)]);
        }

        parent::handleClose($conn);
    }
    public function handleMessage(ConnectionInterface $from, $msg = null)
    {
        if (empty($msg)) {
            return;
        }

        $msgInstance = $this->createMessageInstance($msg);
        $validationResult = $this->validateMessage($from, $msgInstance);

        if ($validationResult === true) {
            $msgString = json_encode($msgInstance);

            if ($msgInstance instanceof Hello) {
                if (empty($msgInstance->getHash())) {
                    // Lobby
                    $this->handleLobbyHello($from, $msgInstance);

                    return;
                }

                $game = $this->validateHello($msgInstance);
                $isRoom = 0;

                if (!is_object($game)) {
                    $e = tc('ht7_c5_ws_tictactoe', 'Unknown game: %s', $msgInstance->getHash());
                    $this->sendError($from, $msgInstance, $e);

                    return;
                }

                $hash = $game->getHash();

                if (!array_key_exists($hash, $this->games)) {
                    $this->addGame($game);

                    $this->games[$hash]['ps'] = [];
                }

                $this->games[$hash]['ps'][$msgInstance->getPlayer()] = [
                    'conn' => $from,
                    'token' => $msgInstance->getToken(),
                    'userId' => $msgInstance->getUserId(),
                    'userName' => $msgInstance->getUserName(),
                ];

                \Log::addEntry('count: ' . count($this->games[$hash]['ps']));
                if (count($this->games[$hash]['ps']) === 1) {
                    $this->sendAddLobbyGame([
                        'hash' => $hash,
                        'userId' => $msgInstance->getUserId(),
                        'userName' => $msgInstance->getUserName(),
                    ]);
                } elseif (count($this->games[$hash]['ps']) === 2) {
                    $this->sendRemoveLobbyGame([
                        'hash' => $hash,
                    ]);
                }

                $arr = $msgInstance->jsonSerialize();
                $arr['datetime'] = '';
                $arr['isRoom'] = $isRoom;
                $arr['releaser'] = '';

                $welcome = json_encode(new Welcome($arr));

                foreach ($this->games[$hash]['ps'] as $ps) {
                    $ps['conn']->send($welcome);

                    if ($this->games[$hash]['isRunning'] === 1) {
                        $this->updatePlayground($ps['conn'], $this->games[$hash], $msgInstance);
                    }
                }
            } elseif ($msgInstance instanceof GameDelete) {
                $hash = $msgInstance->getHash();

                if (empty($this->games[$hash])) {
                    $this->sendError($from, $msgInstance, 'No Game found.');
                } elseif (empty($this->games[$hash]['ps'][$msgInstance->getPlayer()])) {
                    $this->sendError($from, $msgInstance, 'No user found.');
                }

                if (!empty($this->games[$hash]['ps'][2])) {
                    // Make sure player 2 returns to the lobby.
                    $this->games[$hash]['ps'][2]['conn']->send($msgString);
                }

                unset($this->games[$hash]);
            } elseif ($msgInstance instanceof GameLeave) {
                $hash = $msgInstance->getHash();

                if (empty($this->games[$hash])) {
                    $this->sendError($from, $msgInstance, 'No Game found.');
                } elseif (empty($this->games[$hash]['ps'][$msgInstance->getPlayer()])) {
                    $this->sendError($from, $msgInstance, 'No user found.');
                }

                unset($this->games[$hash]['ps'][$msgInstance->getPlayer()]);

                $this->sendAddLobbyGame([
                    'hash' => $hash,
                    'userId' => $msgInstance->getUserId(),
                    'userName' => $msgInstance->getUserName(),
                ]);

                foreach ($this->games[$hash]['ps'] as $ps) {
                    $ps['conn']->send($msgString);
                }
            } elseif ($msgInstance instanceof GameLost) {
                if (empty($this->games[$msgInstance->getHash()])) {
                    $this->sendError($from, $msgInstance, 'No Game found.');

                    return;
                }

                $entity = $this->games[$msgInstance->getHash()]['entity'];
                $entity->setIsFinished(true);
                $entity->setWinnerId($msgInstance->getUserId());
                $entity->setIsRunning(false);
                $entity->save();

                foreach ($this->games[$msgInstance->getHash()]['ps'] as $ps) {
                    if ($ps['conn'] !== $from) {
                        $ps['conn']->send($msgString);
                    }
                }
            } elseif ($msgInstance instanceof GameSettings) {
                $hash = $msgInstance->getHash();

                if ($msgInstance->getItem() === 'time-limit') {
                    $this->games[$hash]['timelimit'] = (int) $msgInstance->getValue();
                    $this->games[$hash]['entity']->setTimeLimit((int) $msgInstance->getValue());

                    $this->games[$hash]['entity']->save();
                } elseif ($msgInstance->getItem() === 'show-borders') {
                    $this->games[$hash]['borders_outer'] = (int) $msgInstance->getValue();
                }

                foreach ($this->games[$hash]['ps'] as $ps) {
                    if ($ps['conn'] !== $from) {
                        $ps['conn']->send($msgString);
                    }
                }
            } elseif ($msgInstance instanceof GameStart) {
                $val = $this->validateStart($msgInstance);

                if (is_string($val) || !$val) {
                    $this->sendError($from, $msgInstance, $val);

                    return;
                }

                $this->games[$msgInstance->getHash()]['isRunning'] = 1;

                $arr = $msgInstance->jsonSerialize();
                $arr['releaser'] = '';
                $arr['token'] = '';
                $arr['userId'] = '';
                $arr['userName'] = '';
                $arr['url'] = '/tictactoe/game/play/' . $msgInstance->getHash();
                $new = new GameStart($arr);
                $newStr = json_encode($new);

                // Let all players redirect to the game.
                foreach ($this->games[$msgInstance->getHash()]['ps'] as $ps) {
                    $ps['conn']->send($newStr);
                }
            } elseif ($msgInstance instanceof GameTurn) {
                $val = $this->validateTurn($msgInstance);

                if (is_string($val) || !$val) {
                    $this->sendError($from, $msgInstance, $val);

                    return;
                }

                $hash = $msgInstance->getHash();
                $this->addField($hash, $msgInstance->getPlayer(), explode(',', $msgInstance->getMove()));

                // Update the game on the server memory.
                $this->games[$hash]['moves'][] = $msgInstance->getMove();
                $this->games[$hash]['whosTurn'] = $msgInstance->getPlayer() === 1 ? 2 : 1;

                $gt = $msgInstance->jsonSerialize();
                $gt['winner'] = $this->wH->checkWinner(
                    $this->games[$hash],
                    $this->games[$hash]['moves'],
                    $this->games[$hash]['fields']
                );
                $msgString = json_encode($gt);

                // Let all players update the game with the latest turn.
                foreach ($this->games[$hash]['ps'] as $ps) {
                    $ps['conn']->send($msgString);
                }
            } elseif ($msgInstance instanceof LobbyAdd) {
                $this->lobby[$msgInstance->getUserId()] = $from;

                $msg = [
//                    '' => '',
                ];
                $welcome = new Welcome($msg);

                $from->send(json_encode($welcome));
            } elseif ($msgInstance instanceof LobbyGameAdd) {
                foreach ($this->lobby as $conn) {
                    $conn->send($msgString);
                }
            } elseif ($msgInstance instanceof LobbyGameRemove) {
                foreach ($this->lobby as $conn) {
                    $conn->send($msgString);
                }
            } elseif ($msgInstance instanceof Welcome) {
                $game = $this->validateHello($msgInstance);

                if (!is_object($game)) {
                    return;
                }

                $hash = $game->getHash();

                foreach ($this->games[$hash]['ps'] as $ps) {
                    if ($ps['conn'] !== $from) {
                        $ps['conn']->send($msgString);
                    }
                }
            }
        } else {
            \Log::addEntry('e: ' . $validationResult);
        }
    }
    public function validateMessage(ConnectionInterface $conn, MessageBaseInterface $msg = null)
    {
        if (is_object($msg) && $msg->getAppId() === $this->appId) {
            if ($this->isValidUser($msg->getUserId(), $msg->getToken())) {
                return true;
            } else {
                'The userId ' . $msg->getUserId() . ' has no access to appId ' . $this->appId . '.';
            }
        } else {
            return 'Wrong app id.';
        }
    }
    protected function handleLobbyHello(ConnectionInterface $conn, AbstractGameMessage $msg)
    {
        $this->lobby[$msg->getUserId()] = $conn;
    }
    protected function sendAddLobbyGame(array $arr)
    {
        $arr['releaser'] = 'server';

        $msg = json_encode(new LobbyGameAdd($arr));

        foreach ($this->lobby as $conn) {
            $conn->send($msg);
        }
    }
    protected function sendError(ConnectionInterface $conn, AbstractGameMessage $msg, $val)
    {
        $err = json_encode(new Error([
                'appId' => $msg->getAppId(),
                'hash' => $msg->getHash(),
                'move' => method_exists($msg, 'getMove') ? $msg->getMove() : '',
                'player' => $msg->getPlayer(),
                'text' => $val === false ? '' : $val,
        ]));

        $conn->send($err);
    }
    protected function sendRemoveLobbyGame(array $arr)
    {
        $msg = new LobbyGameRemove($arr);
        $msgString = json_encode($msg);

        foreach ($this->lobby as $conn) {
            $conn->send($msgString);
        }
    }
    protected function updatePlayground(ConnectionInterface $conn, array $game, AbstractGameMessage $msg)
    {
        $arr = [
            'appId' => $msg->getAppId(),
            'hash' => $msg->getHash(),
            'item' => 'show-borders',
            'value' => $game['borders_outer'],
        ];

        $msgNew = new GameLayout($arr);

        $conn->send(json_encode($msgNew));
    }
    protected function validateHello(AbstractGameMessage $msg)
    {
        $hash = $msg->getHash();

        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToeEntity::class)
            ->findOneBy(['hash' => $hash]);

        if (!is_object($game)) {
            \Log::addEntry('kein obj - hash: ' . $hash);
            \Log::addEntry(print_r($msg, true));
            return false;
        }

        if (!empty($this->games[$hash]) && !empty($this->games[$hash]['ps'])) {
            if (!empty($this->games[$hash]['ps'][$msg->getPlayer()])) {
                $player = $this->games[$hash]['ps'][$msg->getPlayer()];
                if ($player['token'] !== $msg->getToken() || $player['userId'] !== $msg->getUserId()) {
                    \Log::addEntry('token falsch: ' . $hash);
                    return false;
                }
            }
        }

        return $game;
    }
    protected function validateStart(GameStart $msg)
    {
        $game = $this->getGame($msg->getHash());

        if ($game === null) {
            return tc('ht7_c5_ws_tictactoe', 'Unknown game');
        } elseif ($game['isRoom'] === 0) {
            return tc('ht7_c5_ws_tictactoe', 'Faulty message data');
        } elseif ($game['adminId'] !== $msg->getUserId()) {
            // @todo: compare user-token with the one stored.
            return tc('ht7_c5_ws_tictactoe', 'Permission denied.');
        }

        return true;
    }
    protected function validateTurn(GameTurn $msg)
    {
        $game = $this->getGame($msg->getHash());

        \Log::addEntry('validate: ' . $game['whosTurn'] . ' - ' . $msg->getPlayer());

        if ($game === null) {
            return tc('ht7_c5_ws_tictactoe', 'Unknown game');
        } elseif ($game['isRoom'] === 1) {
            return tc('ht7_c5_ws_tictactoe', 'Faulty message data');
        } elseif (in_array($msg->getMove(), $game['moves'])) {
            return tc('ht7_c5_ws_tictactoe', 'Occupied');
        } elseif ($game['whosTurn'] !== $msg->getPlayer()) {
            return tc('ht7_c5_ws_tictactoe', 'Not your turn');
        }
        \Log::addEntry(print_r($game['moves'], true) . ' - move' . $msg->getMove());

        return true;
    }
}
