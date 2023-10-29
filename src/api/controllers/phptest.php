<?php
include("database/CreateConnection.php");

class phptest extends CreateConnection {
    public function exibir() {
        $BFetch=$this->conectaDB()->prepare("select * from names");
        $BFetch->execute();

        $j = [];
        $i = 0;

        while($Fetch=$BFetch->fetch(PDO::FETCH_ASSOC)) {
            $j[$i] = [
                "name" => $Fetch['name']
            ];
            $i++;
        }

        header("Access-Control-Allow-Origin:*");
        header("Content-type: application/json");
        echo json_encode($j);
    }
}