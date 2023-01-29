<?php
    require_once "Connection.php";
    require_once "Element.php";

    class ElementDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function e_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM element ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Element");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function e_search($content){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM element WHERE content = :content");
                $consulta->bindValue(":content", $content);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Element");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function e_insert(Element $element){
            try{
                $consulta = $this->connection->prepare("INSERT INTO element VALUES (NULL, :content)");
                $consulta->bindValue(":content", $element->getContent());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function e_update(Element $element){
            try{
                $consulta = $this->connection->prepare("UPDATE element SET content=:content WHERE id=:id");
                $consulta->bindValue(":content", $element->getContent());
                $consulta->bindValue(":id", $element->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function e_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM element WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 