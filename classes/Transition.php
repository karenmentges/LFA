<?php
    class Transition{

        private $id;
        private $alphabet;
        private $start_state;
        private $end_state;


        public function getId(){
            return $this->id;
        }

        public function setId($id){
            $this->id = $id;
        }

        public function getAlphabet(){
            return $this->alphabet;
        }

        public function setAlphabet($alphabet){
            $this->alphabet = $alphabet;
        }

        public function getStartState(){
            return $this->start_state;
        }

        public function setStartState($start_state){
            $this->start_state = $start_state;
        }

        public function getEndState(){
            return $this->end_state;
        }

        public function setEndState($end_state){
            $this->end_state = $end_state;
        }

        public function validate(){
            $erros = array();
            return $erros;                                  
        }
    }
?>