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
        $tipeShot = $this->modelBot->checkBotShotQuantity() < 2 ? $tipeShot[rand(0, 1)] : $tipeShot[0];
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
                    $moveParam["target"] = $this->modelUser->removePositionUser($moveParam["move"]) ? "hit" : "miss";
                    $this->modelBot->registerMovBot($moveParam);
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
    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    public function makeMove($gridSize) {
        $adjacentPositions = array();
        $moves = array();
        $lastPosition = $this->modelBot->checkHitShips();

        session_start();
        if (!isset($_SESSION['especial'])) {
            $_SESSION['especial'] = 2;
        }
        $typeShot = ["normal", "especial"];
        $typeShot = $_SESSION['especial'] != 0 ? $typeShot[rand(0, 1)] : $typeShot[0];
        echo $_SESSION['especial'];
        $move = ["shot" => $typeShot];
        if ($typeShot == "normal") {
            if ($lastPosition != false) {        
                $adjacents = $this->modelBot->checkAdjacentePositions();
                if ($adjacents[1]["value1"] != null && $adjacents[2]["value1"] != null && $adjacents[3]["value1"] != null) {
                    $inicialAdjacents = [$adjacents[1],$adjacents[2],$adjacents[3]];
                    foreach ($inicialAdjacents as $adjacent) {
                        print_r($adjacent);
                        $adjacentPositions[] = $adjacent["value1"];
                        $this->modelBot->removeAdjacentePositions($adjacent["id"], $adjacent["value1"], "value1");
                    }
                } else {
                    foreach ($adjacents as $adjacent) {
                        $keys=null;
                        $val=null;
                        foreach($adjacent as $key => $value) {
                            if(!is_null($value)) {
                                if ($key == "value1" || $key == "value2" || $key == "value3" || $key == "value4") {
                                    $val = $value;
                                    $keys = $key; 
                                    $adjacentPositions[] = $value;
                                    break;
                                }
                            } 
                        }
                        $this->modelBot->removeAdjacentePositions($adjacent["id"], $val, $keys);
                        if (count($adjacentPositions) == 3) {
                            break;
                        }
                    }
                }

                for($i=0; $i<3; $i++) {
                    do {
                        $position = $lastPosition["position"] + $adjacentPositions[$i];
                        if ($position % 10 == 0 && $adjacentPositions[$i] == -1 && ($position > $gridSize || $position < 0)) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $move);
                            $checkMoveExist != false and $checkMoveCarried != false ? $valid = false : $valid = true;
                        } else if ($position % 10 == 9 && $adjacentPositions[$i] == 1) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $move);
                            $checkMoveExist != false and $checkMoveCarried ? $valid = false : $valid = true;
                        } else if ($this->modelBot->checkMovBot($position) != false) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $move);
                            $checkMoveExist != false and $checkMoveCarried ? $valid = false : $valid = true;
                        }
                    } while($valid);
                    $move["move"] = $position;

                    $ship = $this->modelUser->getShip($move["move"]);
                    $exist = $this->modelBot->checkHitShipExist($ship[0]["shipID"]);
                    $move["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($move["target"] == "hit") {
                        if($exist != false) {
                            if ($exist["size"] == 0) {
                                $this->modelBot->removeShipHit($exist["id"]);
                            } else {
                                $this->modelBot->updateHitShipSize($exist);
                            }
                        } else {
                            $referenceKey = $this->modelBot->registerHitShips($ship[0], $move["move"]);
                            $this->modelBot->registerAdjacentPositions($referenceKey);
                        }
                    }
                    $this->modelBot->registerMovBot($move);
                    $this->modelUser->removePositionUser($move["move"]);
                    $moves[] = $move;
                }
            } else {
                for($i=0; $i<3; $i++) {
                    do {
                        $position = rand(0, $gridSize);
                        $checkMoveExist = $this->modelBot->checkMovBot($position);
                        $checkMoveExist = in_array($position, $move);
                    } while ($checkMoveExist != false);
                    $move["move"] = $position;

                    $ship = $this->modelUser->getShip($move["move"]);
                    $exist = $this->modelBot->checkHitShipExist($ship[0]["shipID"]);
                    $move["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($move["target"] == "hit") {
                        if($exist != false) {
                            if ($exist["size"] == 0) {
                                $this->modelBot->removeShipHit($exist["id"]);
                            } else {
                                $this->modelBot->updateHitShipSize($exist);
                            }
                        } else {
                            $referenceKey = $this->modelBot->registerHitShips($ship[0], $move["move"]);
                            $this->modelBot->registerAdjacentPositions($referenceKey);
                        }
                    }
                    $this->modelBot->registerMovBot($move);
                    $this->modelUser->removePositionUser($move["move"]);
                    $moves[] = $move;
                }
            }
            return $moves;
        } else {
            $position = null;
            $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];

            do {
                $position = rand(0, $gridSize);
                $checkMoveExist = $this->modelBot->checkMovBot($position);
                $checkMoveExist = in_array($position, $move);
            } while ($checkMoveExist != false);
            $move["move"] = $position;

            $moves = array();
            foreach ($adjacentPositions as $adjacent) {
                if ($move["move"] % 10 == 0 && ($adjacent == -11 || $adjacent == -1 || $adjacent == 9)) {
                    continue;
                }
                if ($move["move"] % 10 == 9 && ($adjacent == 11 || $adjacent == 1 || $adjacent == -9)) {
                    continue; 
                }
                $moveParam = $move;
                $moveParam["move"] = $position + $adjacent; 
                if ($moveParam["move"] >= 0 && $moveParam["move"] < $gridSize) {
                    $ship = $this->modelUser->getShip($moveParam["move"]);
                    $exist = $this->modelBot->checkHitShipExist($ship[0]["shipID"]);
                    $moveParam["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($moveParam["target"] == "hit") {
                        if($exist != false) {
                            if ($exist["size"] == 0) {
                                $this->modelBot->removeShipHit($exist["id"]);
                            } else {
                                $this->modelBot->updateHitShipSize($exist);
                            }
                        } else {
                            $referenceKey = $this->modelBot->registerHitShips($ship[0], $moveParam["move"]);
                            $this->modelBot->registerAdjacentPositions($referenceKey);
                        }
                    }
                    $this->modelBot->registerMovBot($moveParam);
                    $this->modelUser->removePositionUser($moveParam["move"]);
                    if ($_SESSION["especial"] != 0) {
                        $_SESSION["especial"] -= 1;
                    }
                    $moves[] = $moveParam;
                }
            }
            return $moves;
        }
    }
}

class HardBot {
    /*
    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    public function makeMove($gridSize) {
        $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];

        do {
            $lastPosition = $this->modelBot->checkLastMove();
            if ($lastPosition["Target"] == "hit" && $lastPosition["Tipe_Shot"] != "especial") {
                do {
                    $adjacent = $adjacentPositions[rand(0,count($adjacentPositions))];
                    $position = $lastPosition["Play"] + $adjacent;
                    if ($position % 10 == 0 && ($adjacent == -11 || $adjacent == -1 || $adjacent == 9)) {
                        $positionValid = false;
                    } else if ($position % 10 == 9 && ($adjacent == 11 || $adjacent == 1 || $adjacent == -9)) {
                        $positionValid = false; 
                    } else {
                        $positionValid = true;
                    }
                } while(!$positionValid);
            } else {
                $position = rand(0, $gridSize);
            };
            $checkMoveExist = $this->modelBot->checkMovBot($position);
        } while ($checkMoveExist != false);

        $tipeShot = ["normal", "especial"];
        $tipeShot = $this->modelBot->checkBotShotQuantity() < 2 ? $tipeShot[rand(0, 1)] : $tipeShot[0];
        $move = ["move" => $position, "shot" => $tipeShot];
        
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
    */
}