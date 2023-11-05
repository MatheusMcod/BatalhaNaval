<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelUser extends CreateConnection {

    public function registerPositionUser($ships) {
        $connection = $this->conectaDB();

        try {
            $connection->beginTransaction();

            foreach($ships as $ship) {
                $stmt = $connection->prepare("INSERT INTO usershipsnames (name) VALUES (:shipName)");
                $stmt->bindValue(':shipName', $ship->name);
                $stmt->execute();
                $shipId = $connection->lastInsertId();

                $stmt = $connection->prepare("INSERT INTO usershipspositions (position, shipName) VALUES (:position, :shipNameID)");
                foreach ($ship->positions as $position) {
                    $stmt->bindValue(':position', $position);
                    $stmt->bindValue(':shipNameID', $shipId);
                    $stmt->execute();
                }
            }

            $connection->commit();
        } catch (PDOException $error) {
            $connection->rollBack();
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function registerUserMove($move, $shotType) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO user_plays (Play, Tipe_Shot) VALUES (:move, :shotType)");
            $stmt->bindValue(':move', $move);
            $stmt->bindValue(':shotType', $shotType);
            $stmt->execute();

            return true;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }

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