<?php
require_once '/wamp64/www/project/batalhaNaval/src/api/database/CreateConnection.php';
class GameModelData extends CreateConnection{
    public function registerStartGame($name) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO game_data (name, date_start) VALUES (:name, :date_start)");
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(":date_start", date("Y-m-d H:i:s"));
            
            $stmt->execute();

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function registerEndGame($percentage) {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("INSERT INTO game_data (percentage_correct, date_end) VALUES (:percentage, :date_end)");
            $stmt->bindValue(':percentage', $percentage);
            $stmt->bindValue(":date_end", date("Y-m-d H:i:s"));
            
            $stmt->execute();

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function getLogs() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT * FROM game_data");
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function verifyEndGameBot() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT COUNT(*) as total FROM botshipspositions");
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado['total'] > 0) {
                return true; // A tabela tem registros
            } else {
                return false; // A tabela está vazia
            }

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }

    public function verifyEndGameUser() {
        $connection = $this->conectaDB();

        try {
            $stmt = $connection->prepare("SELECT COUNT(*) as total FROM usershipspositions");
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado['total'] > 0) {
                return true; 
            } else {
                return false;
            }

        } catch (PDOException $error) {
            error_log($error->getMessage());
            echo "Erro na solicitação";
        }
    }
    
    
}