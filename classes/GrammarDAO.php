<?php
    require_once "Connection.php";
    require_once "Grammar.php";

    class GrammarDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function g_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM grammar ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Grammar");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function g_search($id){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM grammar WHERE id = :id");
                $consulta->bindValue(":id", $id);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Grammar");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function g_insert(Grammar $grammar){
            try{
                $consulta = $this->connection->prepare("INSERT INTO grammar VALUES (NULL, :element, :start_rule, :end_rule)");
                $consulta->bindValue(":element", $grammar->getElement());
                $consulta->bindValue(":start_rule", $grammar->getStartRule());
                $consulta->bindValue(":end_rule", $grammar->getEndRule());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function g_update(Grammar $grammar){
            try{
                $consulta = $this->connection->prepare("UPDATE grammar SET element=:element, start_rule=:start_rule, end_rule=:end_rule WHERE id=:id");
                $consulta->bindValue(":element", $grammar->getElement());
                $consulta->bindValue(":start_rule", $grammar->getStartRule());
                $consulta->bindValue(":end_rule", $grammar->getEndRule());
                $consulta->bindValue(":id", $grammar->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function g_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM grammar WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 