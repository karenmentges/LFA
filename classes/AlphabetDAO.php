<?php
    require_once "Connection.php";
    require_once "Alphabet.php";

    class AlphabetDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function a_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM alphabet ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Alphabet");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function a_search($content){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM alphabet WHERE content = :content");
                $consulta->bindValue(":content", $content);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Alphabet");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function a_insert(Alphabet $alphabet){
            try{
                $consulta = $this->connection->prepare("INSERT INTO alphabet VALUES (NULL, :content)");
                $consulta->bindValue(":content", $alphabet->getContent());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function a_update(Alphabet $alphabet){
            try{
                $consulta = $this->connection->prepare("UPDATE alphabet SET content=:content WHERE id=:id");
                $consulta->bindValue(":content", $alphabet->getContent());
                $consulta->bindValue(":id", $alphabet->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function a_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM alphabet WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 