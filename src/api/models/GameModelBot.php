<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelBot extends CreateConnection {

    public function registerInicialPositionBot($ships) {
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

    public function registerMovBot($move) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO bot_plays(Play, Tipe_Shot, Target) VALUES (:move, :shot, :target)");
            $stmt->bindValue(':move', $move["move"]);
            $stmt->bindValue(':shot', $move["shot"]);
            $stmt->bindValue(':target', $move["target"]);
            $stmt->execute();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação8";
        }
    }

    public function getPositionBot() {
        //Buscar posição do bot
    }

    public function checkMovBot($position) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT Play FROM bot_plays WHERE Play = :position");
            $stmt->bindValue(':position', $position);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }

    public function botCheckShotQuantity() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT COUNT(*) FROM bot_plays WHERE Play = 'especial'");
            $stmt->execute();
            $quantity = $stmt->fetchColumn();
            
            return $quantity;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        } 
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