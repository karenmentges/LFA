<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Karen Mentges">
    <title>PP - LFA</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="Shortcut Icon" href="assets/images/logo_karen.ico" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'> <!-- referente aos icones usados na página -->
</head>
<body>
    <img id="logouffs" src="assets/images/logo_uffs.png" alt="Logo da UFFS">
    <p id="first">Linguagens Formais e Autômatos<br>Karen Ruver Mentges e Izabela Fusieger</p>
    <h1 id="second">Projeto Prático</h1>

    <?php

    // Criando o banco de dados e as tabelas
    try {
        $conn = new PDO("mysql:host=localhost", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DROP DATABASE IF EXISTS databaselfa;
                CREATE DATABASE databaselfa DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
                USE databaselfa;
                DROP USER IF EXISTS 'admin'@'localhost';
                CREATE USER 'admin'@'localhost' IDENTIFIED BY '123456789!'; 
                GRANT SELECT, INSERT, UPDATE, DELETE ON databaselfa.* TO 'admin'@'localhost';";
        $conn->exec($sql);
        $sql = "CREATE TABLE IF NOT EXISTS alphabet(
                    id integer auto_increment primary key,
                    content text not null
                );
                CREATE TABLE IF NOT EXISTS state(
                    id integer auto_increment primary key,
                    content text not null,
                    reference text
                );
                CREATE TABLE IF NOT EXISTS transition(
                    id integer auto_increment primary key,
                    alphabet text not null,
                    start_state text not null,
                    end_state text not null
                );";
        $conn->exec($sql);
    }
    catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    // Armazendando os arquivos fornecidos no formulário
    $destination = "documents/".$_FILES['field_txt']['name'];
    move_uploaded_file($_FILES['field_txt']['tmp_name'], $destination); 

    include_once("classes/AlphabetDAO.php");
    $obja = new AlphabetDAO();

    include_once("classes/StateDAO.php");
    $objs = new StateDAO();

    include_once("classes/TransitionDAO.php");
    $objt = new TransitionDAO();
     
    // Lendo o arquivo TXT
    $txt = file($destination);
    
    $newalphabet = new Alphabet();
    $bdalphabet = new AlphabetDAO();
    $newstate = new State();
    $bdstate = new StateDAO();
    $newtransition = new Transition();
    $bdtransition = new TransitionDAO();

    $newstate->setContent('S');
    $newstate->setReference('S');
    $bdstate->s_insert($newstate);
    
    $r = 65;
    $finalstate = array();

    for ($i=0; $i < count($txt); $i++) {
        // Se na linha do texto contem uma gramática
        if(substr($txt[$i], 0, 1) == '<') {
            // Pular o estado S
            if($r == 83) {
                $r++;
            }
            // Armazena o estado de referência
            $state = substr($txt[$i],1,1);
            
            $string = $txt[$i];
            $string = substr($string,8,strlen($string));
            $array = explode("|", $string);  
            
            for ($j=0; $j < count($array); $j++) {
                $flag = 0;
                $arr = str_split($array[$j]);
                $s = NULL;
                $a = NULL;
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

                $s = trim($s);
                $a = trim($a);

                // Verifica qual o estado novo para o estado de referência da gramática
                $statef = $objs->s_searchByReference($state);
                // Verifica se há um estado cadastrado que referencia o estado encontrado
                $sf = $objs->s_searchByReference($s);

                if($s == NULL) {
                    $flag = 1;
                }
                if($a == 'ε'){
                    if(array_search($statef->getContent(), $finalstate) == NULL) {
                        $finalstate[] = $statef->getContent();
                    }
                    break;
                }

                if($sf == False) {
                    // Cria um novo estado
                    $newstate->setContent(chr($r));
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
                    $newtransition->setEndState(chr($r));
                    $bdtransition->t_insert($newtransition);

                    $r++;
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

                /*
                // Armazena o estado de referência
                $state = $array[1];
                $alphabets = array();
                $l = 0;
                // Seleciona apenas os simbolos e os estados
                for ($k=8; $k < count($array); $k++) {
                    if($array[$k] != '<' && $array[$k] != '>' && $array[$k] != ' '){
                        $alphabets[$l] = $array[$k];
                        $l++;
                    }  
                }
                $ei = 0;
                $e = '';
                for ($k=0; $k < count($alphabets); $k++) {
                    // Pular o estado S
                    if($r == 83) {
                        $r++;
                    }
                    //Verifica se encontrou um estado
                    if(ord($alphabets[$k]) >= 65 && ord($alphabets[$k]) <= 90) {
                        // Verifica qual o estado novo para o estado de referência da gramática
                        $statef = $objs->s_searchByReference($state);
                        // Verifica se há um estado cadastrado que referencia o estado encontrado
                        $sf = $objs->s_searchByReference($alphabets[$k]);
                        if($sf == False) {
                            // Cria um novo estado
                            $newstate->setContent(chr($r));
                            $newstate->setReference($alphabets[$k]);
                            $bdstate->s_insert($newstate);

                            // Se o simbolo ainda não foi cadastrado no banco
                            if($obja->a_search($e)==False) {
                                // Cria um novo simbolo
                                $newalphabet->setContent($e);
                                $bdalphabet->a_insert($newalphabet);
                            }
                                
                            // Cria uma nova transição
                            $newtransition->setAlphabet($e);
                            $newtransition->setStartState($statef->getContent());
                            $newtransition->setEndState(chr($r));
                            $bdtransition->t_insert($newtransition);

                            $r++;
                        }
                        else {
                            // Se o simbolo ainda não foi cadastrado no banco
                            if($obja->a_search($e)==False) {
                                // Cria um novo simbolo
                                $newalphabet->setContent($e);
                                $bdalphabet->a_insert($newalphabet);
                            }

                            // Cria uma nova transição
                            $newtransition->setAlphabet($e);
                            $newtransition->setStartState($statef->getContent());
                            $newtransition->setEndState($sf->getContent());
                            $bdtransition->t_insert($newtransition);
                        }
                        $ei = 0;
                    }
                    // Se encontrou um simbolo
                    else {
                        // Se é o primeiro da sequencia, sobreescreve a variável
                        if($ei == 0) {
                            $e = $alphabets[$k];
                            $ei = 1;
                        }
                        // Se não, concatena com o que já existe na variável
                        else {
                            $e = $e.$alphabets[$k];
                        }
                    }
                }
                */
        }
        // Se na linha do texto contem um token
        else {
            $array = str_split($txt[$i]);
            array_pop($array);
            array_pop($array);

            for ($j=0; $j < count($array); $j++) {
                // Pular o estado S
                if($r == 83) {
                    $r++;
                }
                $flag = 1;

                // Cria um novo estado
                $newstate->setContent(chr($r));
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
                    $newtransition->setEndState(chr($r));
                    $bdtransition->t_insert($newtransition);
                }
                else {
                    if($r == 84) {
                        // Cria uma nova transição
                        $newtransition->setAlphabet($array[$j]);
                        $r--;
                        $r--;
                        $newtransition->setStartState(chr($r));
                        $r++;
                        $r++;
                        $newtransition->setEndState(chr($r));
                        $bdtransition->t_insert($newtransition);
                    }
                    else {
                        // Cria uma nova transição
                        $newtransition->setAlphabet($array[$j]);
                        $r--;
                        $newtransition->setStartState(chr($r));
                        $r++;
                        $newtransition->setEndState(chr($r));
                        $bdtransition->t_insert($newtransition);
                    }
                }
                $r++;           
            }
        }
        if($flag == 1) {
            if($r == 84) {
                $r--;
                $r--;
                $finalstate[] = chr($r);
                $r++;
                $r++;
            }
            else {
                $r--;
                $finalstate[] = chr($r);
                $r++;
            }
        }
        
    }

    $lista = $obja->a_list();
    $lists = $objs->s_list();
    $listt = $objt->t_list();

    $array = array();
    foreach($listt as $transition){
        if(!isset($array[$transition->getStartState()][$transition->getAlphabet()])){
            $array[$transition->getStartState()][$transition->getAlphabet()] = $transition->getEndState();
        }
        else {
            $array[$transition->getStartState()][$transition->getAlphabet()] = $array[$transition->getStartState()][$transition->getAlphabet()].', '.$transition->getEndState();
        }
    }

    ?>
    <br>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>δ</th>
                <?php
                foreach($lista as $alphabet){
                ?>
                    <th><?=$alphabet->getContent()?></th>
                <?php
                }
                ?>
            </tr>
            <?php
            foreach($lists as $state){
            ?>
                <tr>
                    <?php
                    $flag = False;
                    for ($i=0; $i < count($finalstate); $i++) {
                        if($finalstate[$i] == $state->getContent()){
                            $flag = True;
                        }
                    }
                    if($flag == True) {
                    ?>
                        <td>*<?=$state->getContent()?></td>
                    <?php
                    }
                    else {
                    ?>
                        <td><?=$state->getContent()?></td>
                    <?php
                    }
                    ?>
                    <?php
                    foreach($lista as $alphabet){
                        if(isset($array[$state->getContent()][$alphabet->getContent()])){
                    ?>
                        <td><?=$array[$state->getContent()][$alphabet->getContent()]?></td>
                    <?php
                        }
                        else {
                        ?>
                            <td></td>
                        <?php    
                        }
                    }
                    ?>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
</body>
<script src="assets/js/functions.js"></script>
</html>