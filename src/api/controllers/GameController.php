<?php  

require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelBot.php';
require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelUser.php';
require_once '/wamp64/www/project/batalhaNaval/src/api/bot/NavalBotCreat.php';
require_once '/wamp64/www/project/batalhaNaval/src/api/bot/NavalDifficultyBot.php';
require_once __DIR__. '/Ships.php';
class GameController {

    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    private function hasUnitChanged($value1, $value2) {
        $sum = $value1 + $value2;
    
        $lastDigitValue1 = $value1 % 10;
        $lastDigitValue2 = $value2 % 10;
        $lastDigitSum = $sum % 10;

        if ($lastDigitValue1 + $lastDigitValue2 >= 10 || $lastDigitSum < $lastDigitValue1 || $lastDigitSum < $lastDigitValue2) {
            return true;
        }
    
        return false;
    }

    private function randomPositionsBot() {
        $ships = [new Ships('PortaAvioes', 5),
                  new Ships('Navio_Tanque', 4), new Ships('Navio_Tanque', 4),
                  new Ships('Contratorpedeiro', 3), new Ships('Contratorpedeiro', 3), new Ships('Contratorpedeiro', 3),
                  new Ships('Submarinos', 2), new Ships('Submarinos', 2), new Ships('Submarinos', 2), new Ships('Submarinos', 2)];
        $grid = array_fill(0, 99, 0);

        foreach($ships as $ship) {
            $botPositions = array();
            $size = $ship->getSize();
        
            do {
                $positions = rand(0, 99);
            } while ($positions + $size > 99 || $this->hasUnitChanged($positions, $size) || $grid[$positions] == -1);

            for ($i = 0; $i < $size; $i++) {
                $botPositions[] = $positions+$i;
                $grid[$positions + $i] = -1;
            }
            
            $ship->setPositions($botPositions);
        }
        return $ships;
    }

    public function PositionsUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'));

            if ($data) {
              $this->modelUser->registerInicialPositionUser($data);
    
              http_response_code(200);
              echo json_encode(array('Response' => 'Sucessful'));
            } else {
              http_response_code(400);
              echo json_encode(array('Response' => 'Invalide Data'));
            }
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }

    public function startGame() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->modelBot->registerInicialPositionBot($this->randomPositionsBot());
            $this->PositionsUser();
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }

    public function getBotShips(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {    
            $botShips = $this->modelBot->getAllShips();
                
            http_response_code(200);
            echo json_encode($botShips);    
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }   

    public function getUserShips() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {    
            $userShips = $this->modelUser->getAllShips();
                
            http_response_code(200);
            echo json_encode($userShips); 
                
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }

    public function userMove() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'));

            if ($data) {
                $move = $data[0]->move;
                $shot = $data[0]->shotType;

                if ($shot == "normal") {
                    $response = $this->processNormalUserMove($move, $shot);
                } else {
                    $response = $this->processEspeciallUserMove($move, $shot);
                }

                http_response_code(200);
                echo json_encode(array('Response' => $response)); 
            } else {
              http_response_code(400);
              echo json_encode(array('Response' => 'Invalide Data'));
            }
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }

    private function processNormalUserMove($move, $shotType) {
        try {
            foreach ($move as $position) {
                $response = $this->modelBot->removePositionBot($position);
                $target = $response ? 'hit' : 'miss';
                $this->modelUser->registerUserMove($position, $shotType, $target);
            } 
            return "Successful";  
        } catch(PDOException $error) {
            error_log($error->getMessage());
            return "Erro na Operação";
        }
    }

    private function processEspeciallUserMove($move, $shotType) {
        $adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];
        try {
            foreach ($adjacentPositions as $adjacent) {
                $newMove = $move + $adjacent;
    
                if (($move % 10 == 0 && in_array($adjacent, [-11, -1, 9])) ||
                    ($move % 10 == 9 && in_array($adjacent, [11, 1, -9]))) {
                    continue;
                }
    
                if ($this->modelUser->userCheckMovExist($newMove) == false && $newMove >= 0 && $newMove <= 99) {
                    $response = $this->modelBot->removePositionBot($newMove);
                    $target = $response ? 'hit' : 'miss';
                    $this->modelUser->registerUserMove($newMove, $shotType, $target);
                }
            }
            return "Successful";
        } catch(PDOException $error) {
            error_log($error->getMessage());
            return "Erro na Operação";
        }      
    }

    public function botMove() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if(isset($_GET['gridSize']) && isset($_GET['difficulty'])) {
                $gridSize = $_GET['gridSize'];
                $difficulty = $_GET['difficulty'];

                $bot = BotCreat::createBot($difficulty);
                $movBot = $bot->makeMove($gridSize);
                
                http_response_code(200);
                echo json_encode($movBot); 
            } else {
                http_response_code(400);
                echo json_encode(array('Response' => 'Invalid Data'));
            }
        } else {
            http_response_code(405); 
            echo json_encode(array('mensagem' => 'Método não permitido.'));
        }
    }

    
}