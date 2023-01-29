<?php
    require_once "Connection.php";
    require_once "Rule.php";

    class RuleDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function r_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM rule ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Rule");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function r_search($id){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM rule WHERE id = :id");
                $consulta->bindValue(":id", $id);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Rule");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function r_insert(Rule $rule){
            try{
                $consulta = $this->connection->prepare("INSERT INTO rule VALUES (NULL, :content, :reference)");
                $consulta->bindValue(":content", $rule->getContent());
                $consulta->bindValue(":reference", $rule->getReference());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function r_update(Rule $rule){
            try{
                $consulta = $this->connection->prepare("UPDATE rule SET content=:content, reference=:reference WHERE id=:id");
                $consulta->bindValue(":content", $rule->getContent());
                $consulta->bindValue(":reference", $rule->getReference());
                $consulta->bindValue(":id", $rule->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function r_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM rule WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 