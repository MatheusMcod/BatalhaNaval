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
            echo "Erro na solicitação1";
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
            echo "Erro na solicitação2";
        }
    }

    public function registerHitShips($ship, $position) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO hitships(id, name, position, size) VALUES (:id, :name, :position, :size)");
            $stmt->bindValue(':id', $ship["shipID"]);
            $stmt->bindValue(':name', $ship["name"]);
            $stmt->bindValue(':position', $position);
            $stmt->bindValue(':size', $ship["shipSize"]);
            $stmt->execute();

            return $connection->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação3";
            return false;
        }
    }

    public function registerAdjacentPositions($key) {
        $connection = $this->conectaDB();
        $adjacentePositions = [[-1,-2,-3,-4],[1,2,3,4],[-10,-10,-10,-10],[10,10,10,10]];

        try {
            $connection->beginTransaction();

            foreach ($adjacentePositions as $adjacent) {
                $stmt = $connection->prepare("INSERT INTO hitsadjacents(value1, value2, value3, value4, shipID) VALUES (:value1, :value2, :value3, :value4, :shipID)");
                $stmt->bindValue(':value1', $adjacent[0]);
                $stmt->bindValue(':value2', $adjacent[1]);
                $stmt->bindValue(':value3', $adjacent[2]);
                $stmt->bindValue(':value4', $adjacent[3]);
                $stmt->bindValue(':shipID', $key);
                $stmt->execute();
            }

            $connection->commit();
        } catch (PDOException $error) {
            $connection->rollBack();
            error_log($error->getMessage());
            echo "Erro na solicitação4";
        }
    }

    public function getAllShips() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("
                SELECT u.*, p.position FROM botshipsnames u
                INNER JOIN botshipspositions p ON u.shipID = p.shipName;
            ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }

    public function checkHitShipExist($id){
        $connection = $this->conectaDB();

        try {
            $sql = "SELECT id, size FROM hitships where id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação5";
            return false;
        }    
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
            echo "Erro na solicitação8";
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
            echo "Erro na solicitação13";
            return false;
        }
    }

    public function resetGameBot() {
        //reseta os dados do jogo para um jogo novo
    }
}