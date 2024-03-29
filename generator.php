<?php
    include_once("functions/creationDB.php");
    include_once("functions/creationAFND.php");
    include_once("functions/creationAFD.php");
    include_once("functions/lexicalAnalyzer.php");
    include_once("functions/parser.php");
    include_once("functions/print.php");

    include_once("classes/AlphabetDAO.php");
    include_once("classes/StateDAO.php");
    include_once("classes/TransitionDAO.php");

    $obja = new AlphabetDAO();
    $objs = new StateDAO();
    $objt = new TransitionDAO();

    $lista = $obja->a_list();
    $listaa = $obja->a_list();
    $lists = $objs->s_list();
    $listt = $objt->t_list();
?>

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

    // Criação do Banco de Dados
    creationDB();

    // Armazendando os arquivos fornecidos no formulário
    $destination = "documents/".$_FILES['field_txt']['name'];
    move_uploaded_file($_FILES['field_txt']['tmp_name'], $destination); 

    // Lendo o arquivo TXT
    $txt = file($destination);

    // Criação do AFND
    $finalstate = creationAFND($txt);

    // Montando a matriz
    $matriz = array();
    foreach($listt as $transition){
        if(!isset($matriz[$transition->getStartState()][$transition->getAlphabet()])){
            $matriz[$transition->getStartState()][$transition->getAlphabet()] = $transition->getEndState();
        }
        else {
            $matriz[$transition->getStartState()][$transition->getAlphabet()] = $matriz[$transition->getStartState()][$transition->getAlphabet()].', '.$transition->getEndState();
        }
    }

    // Criação do AFD
    $array = creationAFD($matriz, $lista, $listaa);
    $matrizAFD = $array[0];  // Matriz do AFD
    $statesVectorAFD = $array[1];  // Vetor com os estado do AFD

    // Verifica quais dos estados novos possuem um estado final e insere no vetor de estados finais
    for ($z=0; $z < count($statesVectorAFD); $z++) { 
        if($statesVectorAFD[$z] > 1) {
            $a = str_split($statesVectorAFD[$z]);
        }
        for ($y=0; $y < count($a); $y++) { 
            if(in_array($a[$y], $finalstate) && !in_array($statesVectorAFD[$z], $finalstate)) {
                $finalstate[] = $statesVectorAFD[$z];
            }
        }
    }

    // Adicionando estado de erro 
    $statesVectorAFD[] = 'xx';
    foreach($statesVectorAFD as $state){
        foreach($lista as $alphabet) {
            if(!isset($matrizAFD[$state][$alphabet->getContent()])) {
                $matrizAFD[$state][$alphabet->getContent()] = 'xx';
            }
        }
    }

    $finalstateAFD = array();  // Vetor com os estado finais do AFD
    foreach($finalstate as $fstate) {
        if(in_array($fstate,  $statesVectorAFD)) {
            $finalstateAFD[] = $fstate;
        }
    }

    // Impressão das tabelas (LFA)
    /* printAFND($lista, $lists, $matriz, $finalstate);
    printAFD($lista, $statesVectorAFD, $matrizAFD, $finalstate); */

    // Armazendando os arquivos fornecidos no formulário
    $destination2 = "documents/".$_FILES['field_txt2']['name'];
    move_uploaded_file($_FILES['field_txt2']['tmp_name'], $destination2); 

    // Lendo o arquivo TXT
    $entry = file_get_contents($destination2);

    // Realiza a análise léxica e obtém a fita, os tokens válidos, os tokens inválidos e a tabela de símbolos
    $resultado = lexicalAnalyzer($entry, $matrizAFD, $finalstateAFD);

    // Lendo o arquivo xml
    $xml = simplexml_load_file("documents/GLC.xml");

    // Realiza a análise sintática
    parser($xml, $resultado['tape'], $resultado['tabelaSimbolos']);
    echo "<br>";
    
    // Exibe a tabela de símbolos
    printTabelaSimbolos($resultado['tabelaSimbolos']);
    ?>
    <a href="program.php"><button>VOLTAR</button></a>
    <br>
</body>
<script src="assets/js/functions.js"></script>
</html>