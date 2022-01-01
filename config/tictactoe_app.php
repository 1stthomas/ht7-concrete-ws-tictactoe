<?php

return [
    'entity' => [
        'controllerClassName' => \Concrete\Package\Ht7C5WsTictactoe\Application\Games::class,
        'appServerClassName' => \Concrete\Package\Ht7C5WsTictactoe\Application\TicTacToeAppServer::class,
        'handle' => 'tictactoe',
        'name' => 'TicTacToe Game',
        'validUsers' => [1, 2, 3, 4, 5],
        'validUserGroups' => [
            'Registered Users'
//            tc('ht7_c5_ws_server-group_name', 'Ws Admin'),
//            tc('ht7_c5_ws_chat_simple-group_name', 'Ws Chat Admin'),
//            tc('ht7_c5_ws_chat_simple-group_name', 'Ws Chat User'),
        ],
    ],
    'messages' => [
        'mappings' => [
            'delete' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameDelete::class,
            'error' => \Concrete\Package\Ht7C5WsTictactoe\Messages\Error::class,
            'finish' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameFinish::class,
            'hello' => \Concrete\Package\Ht7C5WsTictactoe\Messages\Hello::class,
            'layout' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameLayout::class,
            'leave' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameLeave::class,
            'lost' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameLost::class,
            'lobby_add' => \Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyAdd::class,
            'lobby_game_remove' => \Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyGameAdd::class,
            'lobby_game_remove' => \Concrete\Package\Ht7C5WsTictactoe\Messages\LobbyGameRemove::class,
            'start' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameStart::class,
            'turn' => \Concrete\Package\Ht7C5WsTictactoe\Messages\GameTurn::class,
            'welcome' => \Concrete\Package\Ht7C5WsTictactoe\Messages\Welcome::class,
        ]
    ]
];
