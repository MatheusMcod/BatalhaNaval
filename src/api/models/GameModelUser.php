<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelUser extends CreateConnection {

    public function registerInicialPositionUser($ships) {
        $connection = $this->conectaDB();

        try {
            $connection->beginTransaction();

            foreach($ships as $ship) {
                $stmt = $connection->prepare("INSERT INTO usershipsnames (name, shipSize) VALUES (:shipName, :shipSize)");
                $stmt->bindValue(':shipName', $ship->name);
                $stmt->bindValue(':shipSize', $ship->size);
                $stmt->execute();
                $shipId = $connection->lastInsertId();

                $stmt = $connection->prepare("INSERT INTO usershipspositions (position, shipName) VALUES (:position, :shipNameID)");
                foreach ($ship->position as $position) {
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

    public function registerUserMove($move, $shotType, $target) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO user_plays (Play, Tipe_Shot, target) VALUES (:move, :shotType, :target)");
            $stmt->bindValue(':move', $move);
            $stmt->bindValue(':shotType', $shotType);
            $stmt->bindValue(':target', $target);
            $stmt->execute();

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function getShip($position) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("
                SELECT u.shipID, u.name, u.shipSize
                FROM usershipsnames u
                INNER JOIN usershipspositions p ON u.shipID = p.shipName
                WHERE p.position = :position
            ");
            $stmt->bindValue(':position', $position);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }

    public function getAllShips() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("
                SELECT u.*, p.position FROM usershipsnames u
                INNER JOIN usershipspositions p ON u.shipID = p.shipName;
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

    public function getPositionsShip($id) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT position FROM usershipspositions WHERE shipName = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }

    public function userCheckMovExist($position) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT Play FROM user_plays WHERE Play = :position");
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


    public function removePositionUser($position) {
        $connection = $this->conectaDB();
        try {
                $stmt = $connection->prepare("DELETE FROM usershipspositions WHERE position = :positionShip");
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

    public function percentageHitsUser() {
        $connection = $this->conectaDB();

        try {
            $stmtHit = $connection->prepare("SELECT COUNT(*) as total_hit FROM user_plays WHERE Target = 'hit'");
            $stmtHit->execute();
            $resultadoHit = $stmtHit->fetch(PDO::FETCH_ASSOC);
            $totalHit = $resultadoHit['total_hit'];

            $stmtTotal = $connection->prepare("SELECT COUNT(*) as total FROM user_plays");
            $stmtTotal->execute();
            $resultadoTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);
            $totalRegistros = $resultadoTotal['total'];

            if ($totalRegistros > 0) {
                $percentagHits = ($totalHit / $totalRegistros) * 100;
                return $percentagHits;
            } else {
                return 0;
            }

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
            return false;
        }
    }
    
    public function resetGameUser() {
        //reseta os dados do jogo para um jogo novo
    }
}