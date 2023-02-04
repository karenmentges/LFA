<?php
    class State{

        private $id;
        private $content;
        private $reference;


        public function getId(){
            return $this->id;
        }

        public function setId($id){
            $this->id = $id;
        }

        public function getContent(){
            return $this->content;
        }

        public function setContent($content){
            $this->content = $content;
        }

        public function getReference(){
            return $this->reference;
        }

        public function setReference($reference){
            $this->reference = $reference;
        }

        public function validate(){
            $erros = array();
            return $erros;                                  
        }
    }
?>