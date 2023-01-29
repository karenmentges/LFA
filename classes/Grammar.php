<?php
    class Grammar{

        private $id;
        private $element;
        private $start_rule;
        private $end_rule;


        public function getId(){
            return $this->id;
        }

        public function setId($id){
            $this->id = $id;
        }

        public function getElement(){
            return $this->element;
        }

        public function setElement($element){
            $this->element = $element;
        }

        public function getStartRule(){
            return $this->start_rule;
        }

        public function setStartRule($start_rule){
            $this->start_rule = $start_rule;
        }

        public function getEndRule(){
            return $this->end_rule;
        }

        public function setEndRule($end_rule){
            $this->end_rule = $end_rule;
        }

        public function validate(){
            $erros = array();
            return $erros;                                  
        }
    }
?>