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

    $lista = $obja->a_list();
    $listaa = $obja->a_list();
    $lists = $objs->s_list();
    $listt = $objt->t_list();

    // Montando a matriz
    $array = array();
    foreach($listt as $transition){
        if(!isset($array[$transition->getStartState()][$transition->getAlphabet()])){
            $array[$transition->getStartState()][$transition->getAlphabet()] = $transition->getEndState();
        }
        else {
            $array[$transition->getStartState()][$transition->getAlphabet()] = $array[$transition->getStartState()][$transition->getAlphabet()].', '.$transition->getEndState();
        }
    }


    // Criação do AFD
    $array2 = array();  // Matriz do AFD
    $array3 = array();  // Vetor com os estado do AFD
    foreach($lista as $alphabet) {
        // Verifica se possui conteúdo na posição desejada
        if(isset($array['S'][$alphabet->getContent()]) && $array['S'][$alphabet->getContent()] != NULL){
            // Verifica se o conteúdo tem tamanho maior que 1
            if(strlen($array['S'][$alphabet->getContent()]) > 1) {
                $arr = explode(", ", $array['S'][$alphabet->getContent()]);
                for($a=0; $a<count($arr); $a++) {
                    // Se não existe conteúdo na posição
                    if(!isset($array2['S'][$alphabet->getContent()])) {
                        $array2['S'][$alphabet->getContent()] = $arr[$a];
                    }
                    // Se existe conteúdo na posição, concatena
                    else {
                        $array2['S'][$alphabet->getContent()] = $array2['S'][$alphabet->getContent()].'.'.$arr[$a];
                    }
                }
            }
            else {
                $array2['S'][$alphabet->getContent()] = $array['S'][$alphabet->getContent()];
            }
        }
    }
    // Insere o estado S no vetor para realizar o processo de determinização
    $array3[] = 'S';
    for($j=0; $j < count($array3); $j++) {
        foreach($lista as $alphabet) {
            // Verifica se possui conteúdo na posição desejada
            if(isset($array2[$array3[$j]][$alphabet->getContent()])){
                $content = $array2[$array3[$j]][$alphabet->getContent()];
            }
            else {
                continue;
            }

            // Verifica se o conteúdo tem tamanho maior que 1
            if(strlen($content) > 1) {
                // Divide a string e armazena em um vetor
                $c = explode('.', $content);
                
                // Se o estado ainda não existe no vetor de determinização
                if(!in_array($content, $array3)) {
                    for($k=0; $k < count($c); $k++) { 
                        foreach($listaa as $aalphabet) {
                            if(isset($array[$c[$k]][$aalphabet->getContent()]) && $array[$c[$k]][$aalphabet->getContent()] != NULL){
                                $cont = $array[$c[$k]][$aalphabet->getContent()];
                                // Verifica se o conteúdo tem tamanho maior que 1
                                if(strlen($cont) > 1) {
                                    $cc = explode(", ", $cont);
                                    for ($v=0; $v < count($cc); $v++) { 
                                        // Se a  posição da matriz não possui conteúdo
                                        if(!isset($array2[$content][$aalphabet->getContent()])) {
                                            $array2[$content][$aalphabet->getContent()] = $cc[$v];
                                        }
                                        else { 
                                            $vv = str_split($array2[$content][$aalphabet->getContent()]);
                                            $have = 0;
                                            for ($z=0; $z < count($vv); $z++) { 
                                                // Verifica se o estado encontrado já não existe nessa posição
                                                if($cc[$v] == $vv[$z]) {
                                                    $have = 1;
                                                }
                                            }
                                            // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                            if($have == 0) {
                                                $array2[$content][$aalphabet->getContent()] = $array2[$content][$aalphabet->getContent()].'.'.$cc[$v];
                                            }
                                        }
                                    }
                                }
                                else {
                                    // Se a  posição da matriz não possui conteúdo
                                    if(!isset($array2[$content][$aalphabet->getContent()])) {
                                        $array2[$content][$aalphabet->getContent()] = $cont;
                                    }
                                    else {
                                        $vv = str_split($array2[$content][$aalphabet->getContent()]);
                                        $have = 0;
                                        for ($z=0; $z < count($vv); $z++) { 
                                            // Verifica se o estado encontrado já não existe nessa posição
                                            if($cont == $vv[$z]) {
                                                $have = 1;
                                            }
                                        }
                                        // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                        if($have == 0) {
                                            $array2[$content][$aalphabet->getContent()] = $array2[$content][$aalphabet->getContent()].'.'.$cont;
                                        }
                                    }  
                                }
                            }
                        }
                    }
                    $array3[] = $content;
                }
            }
            else {
                // Se o estado ainda não existe no vetor de determinização
                if(!in_array($content, $array3)) {
                    foreach($listaa as $aalphabet) {
                        if(isset($array[$content][$aalphabet->getContent()]) && $array[$content][$aalphabet->getContent()] != NULL){
                            $cont = $array[$content][$aalphabet->getContent()];
                            // Verifica se o conteúdo tem tamanho maior que 1
                            if(strlen($cont) > 1) {
                                $cc = explode(", ", $cont);
                                for ($v=0; $v < count($cc); $v++) {
                                    // Se a  posição da matriz não possui conteúdo
                                    if(!isset($array2[$content][$aalphabet->getContent()])) {
                                        $array2[$content][$aalphabet->getContent()] = $cc[$v];
                                    }
                                    else {
                                        $vv = str_split($array2[$content][$aalphabet->getContent()]);
                                        $have = 0;
                                        for ($z=0; $z < count($vv); $z++) { 
                                            // Verifica se o estado encontrado já não existe nessa posição
                                            if($cc[$v] == $vv[$z]) {
                                                $have = 1;
                                            }
                                        }
                                        // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                        if($have == 0) {
                                            $array2[$content][$aalphabet->getContent()] = $array2[$content][$aalphabet->getContent()].'.'.$cc[$v];
                                        }
                                    }
                                }
                            }
                            else {
                                // Se a  posição da matriz não possui conteúdo
                                if(!isset($array2[$content][$aalphabet->getContent()])) {
                                    $array2[$content][$aalphabet->getContent()] = $cont;
                                }
                                else {
                                    $vv = str_split($array2[$content][$aalphabet->getContent()]);
                                    $have = 0;
                                    for ($z=0; $z < count($vv); $z++) { 
                                        // Verifica se o estado encontrado já não existe nessa posição
                                        if($cont == $vv[$z]) {
                                            $have = 1;
                                        }
                                    }
                                    // Se não existe, concatena o conteúdo da posição com o estado a ser inserido
                                    if($have == 0) {
                                        $array2[$content][$aalphabet->getContent()] = $array2[$content][$aalphabet->getContent()].'.'.$cont;
                                    }
                                }
                            }
                        }
                    }
                    $array3[] = $content;
                }
            }
        }
    }

    // Verifica quais dos estados novos possuem um estado final e insere no vetor de estados finais
    for ($z=0; $z < count($array3); $z++) { 
        if($array3[$z] > 1) {
            $a = str_split($array3[$z]);
        }
        for ($y=0; $y < count($a); $y++) { 
            if(in_array($a[$y], $finalstate) && !in_array($array3[$z], $finalstate)) {
                $finalstate[] = $array3[$z];
            }
        }
    }
    

    /* // Eliminação de estados mortos (Não testado)
    $array4 = array();  // Vetor com estados não mortos
    $length = 0;
    for ($z=0; $z < count($array3); $z++) { 
        if (in_array($array3[$z], $finalstate)) {
            $array4[] = $array3[$z];  
        }
    }
    while ($length != count($array4)) {
        $length = count($array4);
        foreach($array3 as $state){
            foreach($lista as $alphabet) {
                if(isset($array2[$state][$alphabet->getContent()])) {
                    if(in_array($array2[$state][$alphabet->getContent()], $array4)) {
                        if(!in_array($state, $array4)) {
                            $array4[] = $state;
                        }
                    }
                }
            }
        }
    }
    for ($z=0; $z < count($array3); $z++) { 
        if(!in_array($array3[$z], $array4)) {
            unset($array3[$z]);
            foreach($lista as $alphabet) {
                unset($array2[$array3[$z]][$alphabet->getContent()]);
            }
        }
    } */


    // Adicionando estado de erro 
    $array3[] = 'xx';
    foreach($array3 as $state){
        foreach($lista as $alphabet) {
            if(!isset($array2[$state][$alphabet->getContent()])) {
                $array2[$state][$alphabet->getContent()] = 'xx';
            }
        }
    }
    

    // Impressão das tabelas
    ?>
    <H3>AFND</H3>
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
                        $stateTable = implode("", explode("_", $state->getContent()));
                    ?>
                        <td>*<?= $stateTable?></td>
                    <?php
                    }
                    else {
                        $stateTable = implode("", explode("_", $state->getContent()));
                    ?>
                        <td><?= $stateTable?></td>
                    <?php
                    }
                    ?>
                    <?php
                    foreach($lista as $alphabet){
                        if(isset($array[$state->getContent()][$alphabet->getContent()])){
                            $contentTable = implode("", explode("_", $array[$state->getContent()][$alphabet->getContent()]));
                    ?>
                        <td><?=$contentTable?></td>
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
    <div class="table-wrapper">
        <H3>AFD</H3>
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
            foreach($array3 as $state){
            ?>
                <tr>
                    <?php
                    $flag = False;
                    for ($i=0; $i < count($finalstate); $i++) {
                        if($finalstate[$i] == $state){
                            $flag = True;
                        }
                    }
                    if($flag == True) {
                        if(count(explode(".", $state)) > 1) {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td>*<?='['.$stateTable.']'?></td>
                    <?php
                        }
                        else {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td>*<?=$stateTable?></td>
                    <?php
                        }
                    }
                    else {
                        if(count(explode(".", $state)) > 1 && $state != 'xx') {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td><?='['.$stateTable.']'?></td>
                    <?php
                        }
                        else {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td><?=$stateTable?></td>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    foreach($lista as $alphabet){
                        if(isset($array2[$state][$alphabet->getContent()])){
                            if(count(explode(".", $array2[$state][$alphabet->getContent()])) > 1 && $array2[$state][$alphabet->getContent()] != 'xx') {
                                $contentTable = implode("", explode("_", implode("", explode(".", $array2[$state][$alphabet->getContent()]))));
                    ?>
                                <td><?='['.$contentTable.']'?></td>
                    <?php
                            }
                            else {
                                $contentTable = implode("", explode("_", implode("", explode(".", $array2[$state][$alphabet->getContent()]))));
                    ?>
                                <td><?=$contentTable?></td>
                    <?php
                            } 
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
    <a href="program.php"><button>VOLTAR</button></a>
    <br>
    <br>
    <br>
    <br>
    <br>
</body>
<script src="assets/js/functions.js"></script>
</html>