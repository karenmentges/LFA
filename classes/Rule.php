<?php
    class Rule{

        private $id;
        private $element;
        private $content;


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

        public function getContent(){
            return $this->content;
        }

        public function setContent($content){
            $this->content = $content;
        }
        
        public function validate(){
            $erros = array();
            return $erros;                                  
        }
    }
?>