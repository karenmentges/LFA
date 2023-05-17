<?php

function creationAFND($txt) {
    include_once("classes/AlphabetDAO.php");
    include_once("classes/StateDAO.php");
    include_once("classes/TransitionDAO.php");

    $obja = new AlphabetDAO();
    $objs = new StateDAO();
    $objt = new TransitionDAO();
    $newalphabet = new Alphabet();
    $bdalphabet = new AlphabetDAO();
    $newstate = new State();
    $bdstate = new StateDAO();
    $newtransition = new Transition();
    $bdtransition = new TransitionDAO();

    $newstate->setContent('S');
    $newstate->setReference('S');
    $bdstate->s_insert($newstate);
    
    $prefix = '';
    $suffix = 65;
    $finalstate = array();
    
    // Criação do AFND
    for ($i=0; $i < count($txt); $i++) {
        // Se na linha do texto contem uma gramática
        if(substr($txt[$i], 0, 1) == '<') {
            // Pula o estado S
            if($suffix == 83 && $prefix == '') {
                $suffix++;
            }
            if($suffix > 90) {
                $suffix = 65;
                if($prefix == '') {
                    $prefix = 65;
                }
                else {
                    $prefix++;
                }
            }
            // Armazena o estado de referência
            $state = substr($txt[$i],1,1);
            
            $string = $txt[$i];
            $string = substr($string,8,strlen($string));
            $array = explode("|", $string);  
            
            for ($j=0; $j < count($array); $j++) {
                $arr = str_split($array[$j]);
                $s = NULL;   // estados
                $a = NULL;   // simbolos  
                for ($k=0; $k < count($arr); $k++) {
                    if($arr[$k] != '<' && $arr[$k] != '>' && $arr[$k] != ' ' && $arr[$k] != '\n'){
                        if(ord($arr[$k]) >= 65 && ord($arr[$k]) <= 90) {
                            $s = $s.$arr[$k];
                        }
                        else {
                            $a = $a.$arr[$k];
                        }
                    }  
                }

                // Limpa os conjuntos de estados e simbolos
                $s = trim($s);
                $a = trim($a);

                // Se não houver estado
                if($s == NULL) {
                    $s = "xxx";
                }

                // Verifica qual o estado novo para o estado de referência da gramática
                $statef = $objs->s_searchByReference($state);
                if($statef == False) {
                    // Cria um novo estado
                    if($prefix == '') {
                        $newstate->setContent(chr($suffix));
                    }
                    else {
                        $newstate->setContent(chr($prefix).'_'.chr($suffix));
                    }
                    $newstate->setReference($state);
                    $bdstate->s_insert($newstate);

                    $suffix++;

                    $statef = $objs->s_searchByReference($state);
                }

                // Verifica se há um estado cadastrado que referencia o estado encontrado
                $sf = $objs->s_searchByReference($s);

                // Se a parte da gramática é um épsilon
                if($a == 'ε'){
                    // Adiciona o estado de referência como final
                    if(array_search($statef->getContent(), $finalstate) == NULL) {
                        $finalstate[] = $statef->getContent();
                    }
                    continue;
                }

                // Se não há um estado cadastrado que referencia o estado encontrado
                if($sf == False) {
                    // Cria um novo estado
                    if($prefix == '') {
                        $newstate->setContent(chr($suffix));
                    }
                    else {
                        $newstate->setContent(chr($prefix).'_'.chr($suffix));
                    }
                    $newstate->setReference($s);
                    $bdstate->s_insert($newstate);

                    // Se o simbolo ainda não foi cadastrado no banco
                    if($obja->a_search($a)==False) {
                        // Cria um novo simbolo
                        $newalphabet->setContent($a);
                        $bdalphabet->a_insert($newalphabet);
                    }
                            
                    // Cria uma nova transição
                    $newtransition->setAlphabet($a);
                    $newtransition->setStartState($statef->getContent());
                    if($prefix == '') {
                        $newtransition->setEndState(chr($suffix));
                    }
                    else {
                        $newtransition->setEndState(chr($prefix).'_'.chr($suffix));
                    }
                    $bdtransition->t_insert($newtransition);

                    $suffix++;
                }
                else {
                    // Se o simbolo ainda não foi cadastrado no banco
                    if($obja->a_search($a)==False) {
                        // Cria um novo simbolo
                        $newalphabet->setContent($a);
                        $bdalphabet->a_insert($newalphabet);
                    }

                    // Cria uma nova transição
                    $newtransition->setAlphabet($a);
                    $newtransition->setStartState($statef->getContent());
                    $newtransition->setEndState($sf->getContent());
                    $bdtransition->t_insert($newtransition);
                }
            }
            // Se existe um estado final no banco
            if($objs->s_searchByReference("xxx") != False) {
                $sff = $objs->s_searchByReference("xxx");
                // Se ainda não se encontra no vetor de estados finais
                if(array_search($sff->getContent(), $finalstate) == NULL) {
                    $finalstate[] = $sff->getContent();
                }
            }
        }
        // Se na linha do texto contem um token
        else {
            // Separa a string em um vetor, e elimina os dois ultimos elementos
            $array = str_split($txt[$i]);
            array_pop($array);
            array_pop($array);

            // Indica que último estado criado é um estado final
            $flag = 1;

            for($j=0; $j < count($array); $j++) {
                // Pula o estado S  
                if($suffix == 83 && $prefix == '') {
                    $suffix++;
                }
                if($suffix > 90) {
                    $suffix = 65;
                    if($prefix == '') {
                        $prefix = 65;
                    }
                    else {
                        $prefix++;
                    }
                }  

                // Cria um novo estado
                if($prefix == '') {
                    $newstate->setContent(chr($suffix));
                }
                else {
                    $newstate->setContent(chr($prefix).'_'.chr($suffix));
                }
                $newstate->setReference(NULL);
                $bdstate->s_insert($newstate);

                // Se o simbolo ainda não foi cadastrado no banco
                if($obja->a_search($array[$j])==False) {                  
                    // Cria um novo simbolo
                    $newalphabet->setContent($array[$j]);
                    $bdalphabet->a_insert($newalphabet); 
                }

                // Se for o primeiro simbolo do token
                if($j == 0) {
                    // Cria uma nova transição
                    $newtransition->setAlphabet($array[$j]);
                    $newtransition->setStartState('S');
                    if($prefix == '') {
                        $newtransition->setEndState(chr($suffix));
                    }
                    else {
                        $newtransition->setEndState(chr($prefix).'_'.chr($suffix));
                    }
                    $bdtransition->t_insert($newtransition);
                }
                else {
                    // Se for o estado T
                    if($suffix == 84) {
                        // Cria uma nova transição
                        $newtransition->setAlphabet($array[$j]);
                        if($prefix == '') {
                            $suffix--;
                            $suffix--;
                            $newtransition->setStartState(chr($suffix));
                            $suffix++;
                            $suffix++;
                            $newtransition->setEndState(chr($suffix));
                        }
                        else {
                            $suffix--;
                            $newtransition->setStartState(chr($prefix).'_'.chr($suffix));
                            $suffix++;
                            $newtransition->setEndState(chr($prefix).'_'.chr($suffix));
                        }
                        $bdtransition->t_insert($newtransition);
                    }
                    else {
                        // Cria uma nova transição
                        $newtransition->setAlphabet($array[$j]);
                        if($prefix == '') {
                            $suffix--;
                            $newtransition->setStartState(chr($suffix));
                            $suffix++;
                            $newtransition->setEndState(chr($suffix));
                        }
                        else {
                            if($suffix == 65) {
                                if($prefix == 65) {
                                    $newtransition->setStartState(chr(90));
                                    $newtransition->setEndState(chr(65).'_'.chr(65));
                                }
                                else {
                                    $prefix--;
                                    $newtransition->setStartState(chr($prefix).'_'.chr(90));
                                    $prefix++;
                                    $newtransition->setEndState(chr($prefix).'_'.chr(65));
                                }
                            }
                            else {
                                $suffix--;
                                $newtransition->setStartState(chr($prefix).'_'.chr($suffix));
                                $suffix++;
                                $newtransition->setEndState(chr($prefix).'_'.chr($suffix));
                            }
                        }
                        $bdtransition->t_insert($newtransition);
                    }
                }

                $suffix++;           
            }
        }
        // Adiciona o ultimo estado criado no vetor de estados finais
        if(isset($flag) && $flag == 1) {
            // Se for o estado T
            if($suffix == 84) {
                if($prefix == '') {
                    $suffix--;
                    $suffix--;
                    if(array_search(chr($suffix), $finalstate) == NULL) {
                        $finalstate[] = chr($suffix);
                    }
                    $suffix++;
                    $suffix++;
                }
                else {
                    $suffix--;
                    if(array_search((chr($prefix).'_'.chr($suffix)), $finalstate) == NULL) {
                        $finalstate[] = chr($prefix).'_'.chr($suffix);
                    }
                    $suffix++;
                }
            }
            else {
                if($prefix == '') {
                    $suffix--;
                    if(array_search(chr($suffix), $finalstate) == NULL) {
                        $finalstate[] = chr($suffix);
                    }
                    $suffix++;
                }
                else {
                    $suffix--;
                    if(array_search((chr($prefix).'_'.chr($suffix)), $finalstate) == NULL) {
                        $finalstate[] = chr($prefix).'_'.chr($suffix);
                    }
                    $suffix++;
                }
            }
        }
    }

    return $finalstate;
}

?>