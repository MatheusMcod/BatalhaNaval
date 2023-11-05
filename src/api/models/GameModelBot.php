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
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function getPositionBot() {
        //Buscar posição do bot
    }

    public function removePositionBot($position) {
        $connection = $this->conectaDB();
        try {
                $stmt = $connection->prepare("DELETE FROM botshipspositions WHERE position = :positionShip");
                $stmt->bindValue(':positionShip', $position);
                $stmt->execute();
                $rowsDeleted = $stmt->rowCount();

                if($rowsDeleted > 0) {
                    return true;
                } else {
                    return false;
                }
                
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }

    public function resetGameBot() {
        //reseta os dados do jogo para um jogo novo
    }
}