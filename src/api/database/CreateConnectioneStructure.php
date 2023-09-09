<?php
    abstract class CreateConnection {
        protected function conectaDB(){
            try {
                $con = new PDO("mysql:host=localhost;dbname=phptest","root","");
                return $con;
            } catch(PDOException $erro) {
                echo $erro->getMessage();
            }
        }

    }