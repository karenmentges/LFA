<?php
    require_once "Connection.php";
    require_once "Transition.php";

    class TransitionDAO{
        
        public $connection;

        public function __construct(){
            $this->connection = Connection::connect();
        }

        public function t_list(){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM transition ORDER BY id");
                $consulta->execute();
                $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Transition");
                return $array;
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }

        public function t_search($id){
            try{
                $consulta = $this->connection->prepare("SELECT * FROM transition WHERE id = :id");
                $consulta->bindValue(":id", $id);
                $consulta->execute();
                $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Transition");
                if(count($resultado) == 1)
                    return $resultado[0];
                else
                    return false;    
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }
        }   

        public function t_insert(Transition $transition){
            try{
                $consulta = $this->connection->prepare("INSERT INTO transition VALUES (NULL, :alphabet, :start_state, :end_state)");
                $consulta->bindValue(":alphabet", $transition->getAlphabet());
                $consulta->bindValue(":start_state", $transition->getStartState());
                $consulta->bindValue(":end_state", $transition->getEndState());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }                
        }

        public function t_update(Transition $transition){
            try{
                $consulta = $this->connection->prepare("UPDATE transition SET alphabet=:alphabet, start_state=:start_state, end_state=:end_state WHERE id=:id");
                $consulta->bindValue(":alphabet", $transition->getAlphabet());
                $consulta->bindValue(":start_state", $transition->getStartState());
                $consulta->bindValue(":end_state", $transition->getEndState());
                $consulta->bindValue(":id", $transition->getId());
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            } 
        }

        public function t_delete($id){
            try{
                $consulta = $this->connection->prepare("DELETE FROM transition WHERE id=:id");
                $consulta->bindValue(":id", $id);
                return $consulta->execute();
            }
            catch(PDOException $e){
                echo "ERRO: ".$e->getMessage();
            }               
        }
    }
?> 