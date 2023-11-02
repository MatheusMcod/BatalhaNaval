<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelBot.php';
require_once '/wamp64/www/project/batalhaNaval/src/api/models/GameModelUser.php';
class GameController {

    private $modelBot;
    private $modelUser;

    public function __construct() {
        $this->modelBot = new GameModelBot;
        $this->modelUser = new GameModelUser;
    }

    private function randomPositionsBot() {
        $ships = [new Ships('Porta Avioes', 5),
                  new Ships('Navio-Tanque', 4), new Ships('Navio-Tanque', 4),
                  new Ships('Contratorpedeiro', 3), new Ships('Contratorpedeiro', 3), new Ships('Contratorpedeiro', 3),
                  new Ships('Submarinos', 2), new Ships('Submarinos', 2), new Ships('Submarinos', 2), new Ships('Submarinos', 2)];
        $grid = array_fill(0, 99, 0);

        foreach($ships as $ship) {
            $botPositions = array();
            $size = $ship->getSize();
        
            for($i=0; $i < $size; $i++) {
                $positions = rand(0, 99);
    
                while ($positions + $size > 99 || ($positions + $size)%10 > $positions%10 || $grid[$positions] == -1) {
                    $positions = rand(0, 99);
                }

                array_slice($grid, $positions, $size);

                for ($j = 0; $j < $size; $j++) {
                    $grid[$positions + $j] = -1;
                }

                $botPositions[$i] = $positions;
            }
            $ship->setPositions($botPositions);
        }
        return $ships;
    }

    public function PositionsUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'));

            if ($data) {
              $this->modelUser->registerPositionUser($data);
    
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
        $this->modelBot->registerPositionBot($this->randomPositionsBot());
        $this->PositionsUser();
    }
}