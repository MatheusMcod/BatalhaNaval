<?php

$routes = [
    '/' => 'GameController@startGame',
    '/user/move' => 'GameController@userMove',
    '/bot/move' => 'GameController@botMove',
    '/user/allships' => 'GameController@getUserShips',
    '/bot/allships' => 'GameController@getBotShips',
];
