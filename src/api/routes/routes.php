<?php

require 'GameController.php';

$difficulty = $_GET['difficulty'] ?? 'medium'; //Requerir a dificuldade do front

$model = new GameModel();
$controller = new GameController($model, $difficulty);

// Roteamento com base na URL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_move'])) {
    // Rota para processar a jogada do jogador
    $playerMove = $_POST['player_move'];
    $controller->processPlayerMove($playerMove);

    // Retorna as coordenadas da jogada do bot para serem processadas no front-end
    $gameState = $model->getGameState();
    $botMove = $controller->getBotMove();

    echo json_encode(['playerMove' => $playerMove, 'botMove' => $botMove]);
} else {
    // Rota de erro ou qualquer outra rota que você desejar
    http_response_code(404);
    echo "Rota não encontrada.";
}