<?php
    class Element{

        private $id;
        private $content;


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
        
        public function validate(){
            $erros = array();
            return $erros;                                  
        }
    }
?>