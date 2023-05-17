<?php

// Criação do AFD
function creationAFD($matriz, $lista, $listaa) {
    $matrizAFD = array();  // Matriz do AFD
    $statesVectorAFD = array();  // Vetor com os estado do AFD
    foreach($lista as $alphabet) {
        // Verifica se possui conteúdo na posição desejada
        if(isset($matriz['S'][$alphabet->getContent()]) && $matriz['S'][$alphabet->getContent()] != NULL){
            // Verifica se o conteúdo tem tamanho maior que 1
            if(strlen($matriz['S'][$alphabet->getContent()]) > 1) {
                $arr = explode(", ", $matriz['S'][$alphabet->getContent()]);
                for($a=0; $a<count($arr); $a++) {
                    // Se não existe conteúdo na posição
                    if(!isset($matrizAFD['S'][$alphabet->getContent()])) {
                        $matrizAFD['S'][$alphabet->getContent()] = $arr[$a];
                    }
                    // Se existe conteúdo na posição, concatena
                    else {
                        $matrizAFD['S'][$alphabet->getContent()] = $matrizAFD['S'][$alphabet->getContent()].'.'.$arr[$a];
                    }
                }
            }
            else {
                $matrizAFD['S'][$alphabet->getContent()] = $matriz['S'][$alphabet->getContent()];
            }
        }
    }
    // Insere o estado S no vetor para realizar o processo de determinização
    $statesVectorAFD[] = 'S';
    for($j=0; $j < count($statesVectorAFD); $j++) {
        foreach($lista as $alphabet) {
            // Verifica se possui conteúdo na posição desejada
            if(isset($matrizAFD[$statesVectorAFD[$j]][$alphabet->getContent()])){
                $content = $matrizAFD[$statesVectorAFD[$j]][$alphabet->getContent()];
            }
            else {
                continue;
            }

            // Verifica se o conteúdo tem tamanho maior que 1
            if(strlen($content) > 1) {
                // Divide a string e armazena em um vetor
                $c = explode('.', $content);
                
                // Se o estado ainda não existe no vetor de determinização
                if(!in_array($content, $statesVectorAFD)) {
                    for($k=0; $k < count($c); $k++) { 
                        foreach($listaa as $aalphabet) {
                            if(isset($matriz[$c[$k]][$aalphabet->getContent()]) && $matriz[$c[$k]][$aalphabet->getContent()] != NULL){
                                $cont = $matriz[$c[$k]][$aalphabet->getContent()];
                                // Verifica se o conteúdo tem tamanho maior que 1
                                if(strlen($cont) > 1) {
                                    $cc = explode(", ", $cont);
                                    for ($v=0; $v < count($cc); $v++) { 
                                        // Se a  posição da matriz não possui conteúdo
                                        if(!isset($matrizAFD[$content][$aalphabet->getContent()])) {
                                            $matrizAFD[$content][$aalphabet->getContent()] = $cc[$v];
                                        }
                                        else { 
                                            $vv = str_split($matrizAFD[$content][$aalphabet->getContent()]);
                                            $have = 0;
                                            for ($z=0; $z < count($vv); $z++) { 
                                                // Verifica se o estado encontrado já não existe nessa posição
                                                if($cc[$v] == $vv[$z]) {
                                                    $have = 1;
                                                }
                                            }
                                            // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                            if($have == 0) {
                                                $matrizAFD[$content][$aalphabet->getContent()] = $matrizAFD[$content][$aalphabet->getContent()].'.'.$cc[$v];
                                            }
                                        }
                                    }
                                }
                                else {
                                    // Se a  posição da matriz não possui conteúdo
                                    if(!isset($matrizAFD[$content][$aalphabet->getContent()])) {
                                        $matrizAFD[$content][$aalphabet->getContent()] = $cont;
                                    }
                                    else {
                                        $vv = str_split($matrizAFD[$content][$aalphabet->getContent()]);
                                        $have = 0;
                                        for ($z=0; $z < count($vv); $z++) { 
                                            // Verifica se o estado encontrado já não existe nessa posição
                                            if($cont == $vv[$z]) {
                                                $have = 1;
                                            }
                                        }
                                        // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                        if($have == 0) {
                                            $matrizAFD[$content][$aalphabet->getContent()] = $matrizAFD[$content][$aalphabet->getContent()].'.'.$cont;
                                        }
                                    }  
                                }
                            }
                        }
                    }
                    $statesVectorAFD[] = $content;
                }
            }
            else {
                // Se o estado ainda não existe no vetor de determinização
                if(!in_array($content, $statesVectorAFD)) {
                    foreach($listaa as $aalphabet) {
                        if(isset($matriz[$content][$aalphabet->getContent()]) && $matriz[$content][$aalphabet->getContent()] != NULL){
                            $cont = $matriz[$content][$aalphabet->getContent()];
                            // Verifica se o conteúdo tem tamanho maior que 1
                            if(strlen($cont) > 1) {
                                $cc = explode(", ", $cont);
                                for ($v=0; $v < count($cc); $v++) {
                                    // Se a  posição da matriz não possui conteúdo
                                    if(!isset($matrizAFD[$content][$aalphabet->getContent()])) {
                                        $matrizAFD[$content][$aalphabet->getContent()] = $cc[$v];
                                    }
                                    else {
                                        $vv = str_split($matrizAFD[$content][$aalphabet->getContent()]);
                                        $have = 0;
                                        for ($z=0; $z < count($vv); $z++) { 
                                            // Verifica se o estado encontrado já não existe nessa posição
                                            if($cc[$v] == $vv[$z]) {
                                                $have = 1;
                                            }
                                        }
                                        // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                        if($have == 0) {
                                            $matrizAFD[$content][$aalphabet->getContent()] = $matrizAFD[$content][$aalphabet->getContent()].'.'.$cc[$v];
                                        }
                                    }
                                }
                            }
                            else {
                                // Se a  posição da matriz não possui conteúdo
                                if(!isset($matrizAFD[$content][$aalphabet->getContent()])) {
                                    $matrizAFD[$content][$aalphabet->getContent()] = $cont;
                                }
                                else {
                                    $vv = str_split($matrizAFD[$content][$aalphabet->getContent()]);
                                    $have = 0;
                                    for ($z=0; $z < count($vv); $z++) { 
                                        // Verifica se o estado encontrado já não existe nessa posição
                                        if($cont == $vv[$z]) {
                                            $have = 1;
                                        }
                                    }
                                    // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                    if($have == 0) {
                                        $matrizAFD[$content][$aalphabet->getContent()] = $matrizAFD[$content][$aalphabet->getContent()].'.'.$cont;
                                    }
                                }
                            }
                        }
                    }
                    $statesVectorAFD[] = $content;
                }
            }
        }
    }

    $arr = array($matrizAFD, $statesVectorAFD);
    return $arr;
}
    
?>