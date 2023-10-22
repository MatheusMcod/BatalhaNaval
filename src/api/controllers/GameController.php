<?php

class GameController {
    private $modelBot;
    private $modelUser;
    private $bot;

    public function __construct($modelBot, $modelUser, $difficulty) {
        $this->modelBot = $modelBot;
        $this->modelUser = $modelUser;
        $this->bot = BotCreat::createBot($difficulty);
    }

    private function randomPositionsBot() {
        //posições do bot
    }

    public function startUser($positionsUser) {
        $this->modelUser->registerPositionUser($positionsUser);
    }

    public function startBot($positionsBot) {
        $this->modelBot->registerPositionBot($positionsBot);
    }

    public function startGame($positionsUser) {
        $this->startUser($positionsUser);
        $this->startBot($this->randomPositionsBot());
    }
}