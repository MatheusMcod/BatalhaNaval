<?php

class GameModelBot extends CreateConnection {

    public function registerPositionBot($ships) {
        $connection = $this->conectaDB();

        try {
            $connection->beginTransaction();

            foreach($ships as $ship) {
                $stmt = $connection->prepare("INSERT INTO botshipsnames (name) VALUES (:shipName)");
                $stmt->bindParam(':shipName', $ship->name);
                $stmt->execute();
                $shipId = $connection->lastInsertId();

                $stmt = $connection->prepare("INSERT INTO botshipspositions (position, shipName) VALUES (:position, :shipNameID)");
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