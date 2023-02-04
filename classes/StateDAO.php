<?php
    require_once "Connection.php";
    require_once "State.php";

    class StateDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function s_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM state ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "State");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function s_search($content){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM state WHERE content = :content");
                $consulta->bindValue(":content", $content);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "State");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function s_searchByReference($reference){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM state WHERE reference = :reference");
                $consulta->bindValue(":reference", $reference);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "State");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        } 

        public function s_insert(State $state){
            try{
                $consulta = $this->connection->prepare("INSERT INTO state VALUES (NULL, :content, :reference)");
                $consulta->bindValue(":content", $state->getContent());
                $consulta->bindValue(":reference", $state->getReference());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function s_update(State $state){
            try{
                $consulta = $this->connection->prepare("UPDATE state SET content=:content, reference=:reference WHERE id=:id");
                $consulta->bindValue(":content", $state->getContent());
                $consulta->bindValue(":reference", $state->getReference());
                $consulta->bindValue(":id", $state->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function s_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM state WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 