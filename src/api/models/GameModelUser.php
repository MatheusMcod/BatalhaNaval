<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelUser extends CreateConnection {

    public function registerPositionUser($ships) {
        $connection = $this->conectaDB();

        try {
            $connection->beginTransaction();

            foreach($ships as $ship) {
                $stmt = $connection->prepare("INSERT INTO usershipsnames (name) VALUES (:shipName)");
                $stmt->bindParam(':shipName', $ship->name);
                $stmt->execute();
                $shipId = $connection->lastInsertId();

                $stmt = $connection->prepare("INSERT INTO usershipspositions (position, shipName) VALUES (:position, :shipNameID)");
                foreach ($ship->positions as $position) {
                    $stmt->bindParam(':position', $position);
                    $stmt->bindParam(':shipNameID', $shipId);
                    $stmt->execute();
                }
            }

            $connection->commit();
        } catch (PDOException $error) {
            $connection->rollBack();
            echo $error->getMessage();
        }
    }

    public function registerUserMove($move) {

    }

    public function getPositionUser() {
        //Buscar posição do usuário
    }

    public function removePositionUser() {
        //remove a posição do usuário
    }
    
    public function resetGameUser() {
        //reseta os dados do jogo para um jogo novo
    }
}