<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelBot extends CreateConnection {

    public function registerPositionBot($ships) {
        $connection = $this->conectaDB();

        try {
            $connection->beginTransaction();

            foreach($ships as $ship) {
                $stmt1 = $connection->prepare("INSERT INTO botshipsnames(name) VALUES (:shipName)");
                $stmt1->bindValue(':shipName', $ship->getName());
                $stmt1->execute();
                $shipId = $connection->lastInsertId();

                $stmt2 = $connection->prepare("INSERT INTO botshipspositions (position, shipName) VALUES (:position, :shipNameID)");
                foreach ($ship->getPositions() as $position) {
                    $stmt2->bindValue(':position', $position);
                    $stmt2->bindValue(':shipNameID', $shipId);
                    $stmt2->execute();
                }
            }

            $connection->commit();
        } catch (PDOException $error) {
            $connection->rollBack();
            echo $error->getMessage();
        }
    }

    public function getPositionBot() {
        //Buscar posição do bot
    }

    public function removePositionUser() {
        //remover posição do bot
    }

    public function resetGameBot() {
        //reseta os dados do jogo para um jogo novo
    }
}