<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelBot.php';
require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelUser.php';
class EasyBot {
    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    public function makeMove($gridSize) {
        do {
            $position = rand(0, $gridSize);
            $checkMoveExist = $this->modelBot->checkMovBot($position);
        } while ($checkMoveExist != false);
        
        $tipeShot = ["normal", "especial"];
        $tipeShot = $this->modelBot->botCheckShotQuantity() < 2 ? $tipeShot[rand(0, 1)] : $tipeShot[0];
        $move = ["move" => $position, "shot" => $tipeShot];
    
        $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];
        if ($tipeShot == "especial") {
            $moves = array();
            foreach ($adjacentPositions as $adjacent) {
                if ($position % 10 == 0 && ($adjacent == -11 || $adjacent == -1 || $adjacent == 9)) {
                    continue;
                }
                if ($position % 10 == 9 && ($adjacent == 11 || $adjacent == 1 || $adjacent == -9)) {
                    continue; 
                }
                $moveParam = $move;
                $moveParam["move"] += $adjacent; 
                if ($moveParam["move"] >= 0 && $moveParam["move"] < $gridSize) {
                    $this->modelBot->registerMovBot($moveParam);
                    $moveParam["target"] = $this->modelUser->removePositionUser($moveParam["move"]) ? "hit" : "miss";
                    $moves[] = $moveParam;
                }
            }
            return $moves;
        } else {
            $move["target"] = $this->modelUser->removePositionUser($position) ? "hit" : "miss";
            $this->modelBot->registerMovBot($move);
            return $move;
        }
    }
}

class MediumBot{
    public function makeMove($gridSize) {
        // Lógica do bot médio
    }
}

class HardBot{
    public function makeMove($gridSize) {
        // Lógica do bot difícil
    }
}