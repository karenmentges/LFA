<?php
    abstract class Connection{
        
        public static $connection;

        public static function connect(){
            if(!isset(self::$connection)){
                try{
                    self::$connection = new PDO("mysql:host=localhost; dbname=databaselfa", "admin", "123456789!");
                }
                catch(PDOException $e){
                    echo "Erro de conexão: ". $e->getMessage();
                    die();
                }
            }
            return self::$connection;
        }
    }
?>   