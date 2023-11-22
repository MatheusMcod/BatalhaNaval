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
        session_start();
        $moves = array();
        if (!isset($_SESSION['especial'])) {
            $_SESSION['especial'] = 2;
        }
        $typeShot = ["normal", "especial"];
        $typeShot = $_SESSION['especial'] != 0 ? $typeShot[rand(0, 1)] : $typeShot[0];
        $move = ["shot" => $typeShot, "move" => 0, "target" => "miss"];

        if ($typeShot == "normal") {
            for ($i=0; $i < 3; $i++) {
                do {
                    $position = rand(0, $gridSize);
                    $checkMoveExist = $this->modelBot->checkMovBot($position);
                    $checkMoveCarried = in_array($position, $moves);
                } while ($checkMoveExist != false || $checkMoveCarried);
                $move["move"] = $position;

                $move["target"] = $this->modelUser->removePositionUser($move["move"]) ? "hit" : "miss";
                $this->modelBot->registerMovBot($move);
                $moves[] = $move;
            }
        } else {
            $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];
            foreach ($adjacentPositions as $adjacent) {
                do {
                    $position = rand(0, $gridSize);
                    $checkMoveExist = $this->modelBot->checkMovBot($position);
                    $checkMoveCarried = in_array($position, $move);
                } while ($checkMoveExist != false || $checkMoveCarried);
                $move["move"] = $position;

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
        }
        if ($_SESSION["especial"] != 0) {
            $_SESSION["especial"] -= 1;
        }

        return $moves;     
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
        session_start();
        $adjacentPositions = array();
        $moves = array();
        $lastPosition = isset($_SESSION['hitships']);
        $_SESSION['adjacentPositions'] = ["left" => [-1,-2,-3,-4],"rigth" => [1,2,3,4], "top" => [-10,-10,-10,-10], "bottom" => [10,10,10,10]];
        
        if (!isset($_SESSION['especial'])) {
            $_SESSION['especial'] = 2;
        }
        $typeShot = ["normal", "especial"];
        $typeShot = $_SESSION['especial'] != 0 ? $typeShot[rand(0, 1)] : $typeShot[0];
        $move = ["shot" => $typeShot, "move" => 0, "target" => "miss"];
        if ($typeShot == "normal") {
            if ($lastPosition) {
                $toRemove = array();
                foreach ($_SESSION['adjacentPositions'] as $positionKey => $adjacent) {
                    foreach($adjacent as $valueKey => $value) {
                            $adjacentPositions[] = $value;
                            $toRemove[] = ['position' => $positionKey, 'value' => $valueKey];

                            if (count($adjacentPositions) == 3) {
                                break 2;
                            }
                        }
                    } 

                foreach ($toRemove as $keys) {
                    unset($_SESSION['adjacentPositions'][$keys['position']][$keys['value']]);
                }
                
                for($i=0; $i<3; $i++) {
                    $valid=false;
                    $position = $_SESSION['hitships'][0]['move'] + $adjacentPositions[$i];
                    do {
                        if ($position > $gridSize || $position < 0 || $this->modelBot->checkMovBot($position) != false || in_array($position, $moves)) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $moves);
                            $checkMoveExist != false || $checkMoveCarried ? $valid = true : $valid = false;
                        } else if ($position % 10 == 0 && ($adjacentPositions[$i] == -1 || $adjacentPositions[$i] == -2 || $adjacentPositions[$i] == -3 ||$adjacentPositions[$i] == -4)) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $moves);
                            $checkMoveExist != false || $checkMoveCarried ? $valid = true : $valid = false;
                        } else if ($position % 10 == 9 && ($adjacentPositions[$i] == 1 || $adjacentPositions[$i] == 2 || $adjacentPositions[$i] == 3 || $adjacentPositions[$i] == 4)) {
                            $position = rand(0, $gridSize);
                            $checkMoveExist = $this->modelBot->checkMovBot($position);
                            $checkMoveCarried = in_array($position, $moves);
                            $checkMoveExist != false || $checkMoveCarried ? $valid = true : $valid = false;
                        } 
                    } while($valid);
                    $move["move"] = $position;

                    $ship = $this->modelUser->getShip($position);
                    $exist = isset($_SESSION['hitships']);
                    $move["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($move["target"] == "hit") {
                        if($exist) {
                            if ($_SESSION['hitships'][0]["shipSize"] == 0) {
                                array_shift($_SESSION['hitships']);
                                if(count($_SESSION['hitships']) == 0) {
                                    unset($_SESSION['hitships']);
                                    unset($_SESSION['adjacentPositions']);
                                    $_SESSION['adjacentPositions'] = ["left" => [1,2,3,4],"rigth" => [-1,-2,-3,-4], "top" => [-10,-10,-10,-10], "bottom" => [10,10,10,10]];
                                }
                            } else {
                                $_SESSION['hitships'][0]["shipSize"] -= 1;
                            }
                        } else {
                            $ship["move"] = $position;
                            $_SESSION['hitships'][] = $ship;
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
                        $checkMoveCarried = in_array($position, $moves);
                    } while ($checkMoveExist != false || $checkMoveCarried);
                    $move["move"] = $position;

                    $ship = $this->modelUser->getShip($position);
                    $exist = isset($_SESSION['hitships']);
                    $move["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($move["target"] == "hit") {
                        if($exist) {
                            if ($_SESSION['hitships'][0]["shipSize"] == 0) {
                                array_shift($_SESSION['hitships']);
                                if(count($_SESSION['hitships']) == 0) {
                                    unset($_SESSION['hitships']);
                                    unset($_SESSION['adjacentPositions']);
                                    $_SESSION['adjacentPositions'] = ["left" => [1,2,3,4],"rigth" => [-1,-2,-3,-4], "top" => [-10,-10,-10,-10], "bottom" => [10,10,10,10]];
                                }
                            } else {
                                $_SESSION['hitships'][0]["shipSize"] -= 1;
                            }
                        } else {
                            $ship["move"] = $position;
                            $_SESSION['hitships'][] = $ship;
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
                $checkMoveCarried = in_array($position, $move);
            } while ($checkMoveExist != false || $checkMoveCarried);
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
                    $exist = isset($_SESSION['hitships']);
                    $moveParam["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($moveParam["target"] == "hit") {
                        if($exist) {
                            if ($_SESSION['hitships'][0]["shipSize"] == 0) {
                                array_shift($_SESSION['hitships']);
                                if(count($_SESSION['hitships']) == 0) {
                                    unset($_SESSION['hitships']);
                                    unset($_SESSION['adjacentPositions']);
                                    $_SESSION['adjacentPositions'] = ["left" => [1,2,3,4],"rigth" => [-1,-2,-3,-4], "top" => [-10,-10,-10,-10], "bottom" => [10,10,10,10]];
                                }
                            } else {
                                $_SESSION['hitships'][0]["shipSize"] -= 1;
                            }
                        } else {
                            $ship["move"] = $position;
                            $_SESSION['hitships'][] = $ship;
                        }
                    }
                    $this->modelBot->registerMovBot($moveParam);
                    $this->modelUser->removePositionUser($moveParam["move"]);
                    
                    $moves[] = $moveParam;
                }
            }
            if ($_SESSION["especial"] != 0) {
                $_SESSION["especial"] -= 1;
            }
            return $moves;
        }
    }
}

class HardBot {
    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    public function makeMove($gridSize) {
        session_start();
        $moves = array();
        if (!isset($_SESSION['especial'])) {
            $_SESSION['especial'] = 2;
        }
        $typeShot = ["normal", "especial"];
        $typeShot = $_SESSION['especial'] != 0 ? $typeShot[/*rand(0, 1)*/0] : $typeShot[0];
        $move = ["shot" => $typeShot, "move" => 0, "target" => "miss"];

        if ($typeShot == "normal") {
            for ($i=0; $i < 3; $i++) {
                if (!isset($_SESSION['nextPosition'])) {
                    do {
                        $position = rand(0, $gridSize);
                        $checkMoveExist = $this->modelBot->checkMovBot($position);
                        $checkMoveCarried = in_array($position, $moves);
                    } while ($checkMoveExist != false || $checkMoveCarried);
                } else {
                    $position = $_SESSION['nextPosition'][0];
                }
                $move["move"] = $position;

                $ship = $this->modelUser->getShip($position);
                $exist = isset($_SESSION['hitships']);
                $move["target"] = count($ship) != 0 ? "hit" : "miss";
                if ($move["target"] == "hit") {
                    if($exist) {
                        if ($_SESSION['hitships'][0]["shipSize"] == 0) {
                            array_shift($_SESSION['hitships']);
                            if(count($_SESSION['hitships']) == 0) {
                                unset($_SESSION['hitships']);
                            }
                        } else {
                            $_SESSION['hitships'][0]["shipSize"] -= 1;
                        }
                    } else {
                        $ship[0]["move"] = $position;
                        $_SESSION['hitships'][] = $ship;
                    }
                }
                $this->modelBot->registerMovBot($move);
                $this->modelUser->removePositionUser($move["move"]);

                if (!isset($_SESSION['nextPosition'])) {
                    $_SESSION['nextPosition'] = $this->modelUser->getPositionsShip($_SESSION['hitships'][0]['shipID']);
                } else if (isset($_SESSION['nextPosition'])) {
                    unset($_SESSION['nextPosition'][$move["move"]]); 
                }
                
                $moves[] = $move;
            }
        } else {
            $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];
            foreach ($adjacentPositions as $adjacent) {
                do {
                    $position = rand(0, $gridSize);
                    $checkMoveExist = $this->modelBot->checkMovBot($position);
                    $checkMoveCarried = in_array($position, $move);
                } while ($checkMoveExist != false || $checkMoveCarried);
                $move["move"] = $position;

                if ($position % 10 == 0 && ($adjacent == -11 || $adjacent == -1 || $adjacent == 9)) {
                    continue;
                }
                if ($position % 10 == 9 && ($adjacent == 11 || $adjacent == 1 || $adjacent == -9)) {
                    continue; 
                }
                $moveParam = $move;
                $moveParam["move"] += $adjacent; 
                if ($moveParam["move"] >= 0 && $moveParam["move"] < $gridSize) {
                    $ship = $this->modelUser->getShip($moveParam["move"]);
                    $exist = isset($_SESSION['hitships']);
                    $moveParam["target"] = count($ship) != 0 ? "hit" : "miss";
                    if ($moveParam["target"] == "hit") {
                        if($exist) {
                            if ($_SESSION['hitships'][0]["shipSize"] == 0) {
                                array_shift($_SESSION['hitships']);
                                if(count($_SESSION['hitships']) == 0) {
                                    unset($_SESSION['hitships']);
                                }
                            } else {
                                $_SESSION['hitships'][0]["shipSize"] -= 1;
                            }
                        } else {
                            $ship[0]["move"] = $position;
                            $_SESSION['hitships'][] = $ship;
                        }
                    }
                    $this->modelBot->registerMovBot($moveParam);
                    $this->modelUser->removePositionUser($moveParam["move"]);
                    
                    if ($exist && !isset($_SESSION['nextPosition'])) {
                        $_SESSION['nextPosition'] = $this->modelUser->getPositionsShip($_SESSION['hitships'][0]['shipID']);
                    } else if ($exist && isset($_SESSION['nextPosition'])) {
                        unset($_SESSION['nextPosition'][$move["move"]]);
                    }

                    $moves[] = $moveParam;
                }
            }   
        }
        if ($_SESSION["especial"] != 0) {
            $_SESSION["especial"] -= 1;
        }
        
        return $moves;     
    }
}