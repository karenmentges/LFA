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

    // Criando o banco de dados e a tabela 
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
        $sql = "CREATE TABLE IF NOT EXISTS element(
                    id integer auto_increment primary key,
                    content text not null
                );
                CREATE TABLE IF NOT EXISTS rule(
                    id integer auto_increment primary key,
                    content text not null,
                    reference text
                );
                CREATE TABLE IF NOT EXISTS grammar(
                    id integer auto_increment primary key,
                    element text not null,
                    start_rule text not null,
                    end_rule text not null
                );";
        $conn->exec($sql);
    }
    catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    // Armazendando os arquivos fornecidos no formulário
    $destination = "documents/".$_FILES['field_txt']['name'];
    move_uploaded_file($_FILES['field_txt']['tmp_name'], $destination); 

    include_once("classes/ElementDAO.php");
    $obje = new ElementDAO();
    include_once("classes/RuleDAO.php");
    $objr = new RuleDAO();
    include_once("classes/GrammarDAO.php");
    $objr = new GrammarDAO();
    

    // Lendo o arquivo TXT
    $txt = file($destination);
    

    //print_r($txt);
    /*for ($i=0; $i < count($txt); $i++) { 
        echo($i."<br>");
        $array = str_split($txt[$i]);
        foreach($array as $data) {
            echo($data."<br>");
            if($data == "\n") {
                echo("Achei");
            }
        }
    }
    $array = str_split($txt[5]);
    foreach($array as $data) {
        echo($data."<br>");
    }
    parse_str($txt[0], $array);
*/
    $newelement = new Element();
    $bdelement = new ElementDAO();
    $newrule = new Rule();
    $bdrule = new RuleDAO();
    $newgrammar = new Grammar();
    $bdgrammar = new GrammarDAO();

    $newrule->setContent("S");
    $bdrule->r_insert($newrule);
    $r = 65;
    $finalrule = array();

    for ($i=0; $i < count($txt); $i++) {
        $array = str_split($txt[$i]);
        array_pop($array);
        array_pop($array);
        for ($j=0; $j < count($array); $j++) {
            if($array[0] == '<') {
                if($j == 0) {
                    
                }
            }
            else {
                if($obje->e_search($array[$j])==False) {
                    if($j == 0) {
                        $newrule->setContent(chr($r));
                        $bdrule->r_insert($newrule);

                        $newelement->setContent($array[$j]);
                        $bdelement->e_insert($newelement);

                        $newgrammar->setElement($array[$j]);
                        $newgrammar->setStartRule('S');
                        $newgrammar->setEndRule(chr($r));
                        $bdgrammar->g_insert($newgrammar);

                        $r++;
                    }
                    else {
                        $newrule->setContent(chr($r));
                        $bdrule->r_insert($newrule);

                        $newelement->setContent($array[$j]);
                        $bdelement->e_insert($newelement);

                        $newgrammar->setElement($array[$j]);
                        $r--;
                        $newgrammar->setStartRule(chr($r));
                        $r++;
                        $newgrammar->setEndRule(chr($r));
                        $bdgrammar->g_insert($newgrammar);

                        $r++;
                    }
                }
                else {
                    if($j == 0) {
                        $newrule->setContent(chr($r));
                        $bdrule->r_insert($newrule);

                        $newgrammar->setElement($array[$j]);
                        $newgrammar->setStartRule('S');
                        $newgrammar->setEndRule(chr($r));
                        $bdgrammar->g_insert($newgrammar);

                        $r++;
                    }
                    else {
                        $newrule->setContent(chr($r));
                        $bdrule->r_insert($newrule);

                        $newgrammar->setElement($array[$j]);
                        $r--;
                        $newgrammar->setStartRule(chr($r));
                        $r++;
                        $newgrammar->setEndRule(chr($r));
                        $bdgrammar->g_insert($newgrammar);

                        $r++;
                    }    
                }
            }
        }
        $r--;
        $finalrule[] = chr($r);
        $r++;
    }

    // Identificar os estados finais em um array para depois imprimir na primeira coluna da tabela

    include_once("classes/ElementDAO.php");
    include_once("classes/RuleDAO.php");
    include_once("classes/GrammarDAO.php");
    $obje = new ElementDAO();
    $objr = new RuleDAO();
    $objg = new GrammarDAO();
    $liste = $obje->e_list();
    $listr = $objr->r_list();
    $listg = $objg->g_list();

    $array = array();
    foreach($listg as $grammar){
        if(!isset($array[$grammar->getStartRule()][$grammar->getElement()])){
            $array[$grammar->getStartRule()][$grammar->getElement()] = $grammar->getEndRule();
        }
        else {
            $array[$grammar->getStartRule()][$grammar->getElement()] = $array[$grammar->getStartRule()][$grammar->getElement()].', '.$grammar->getEndRule();
        }
    }

    ?>
    <br>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>δ</th>
                <?php
                foreach($liste as $element){
                ?>
                    <th><?=$element->getContent()?></th>
                <?php
                }
                ?>
            </tr>
            <?php
            foreach($listr as $rule){
            ?>
                <tr>
                    <?php
                    $flag = False;
                    for ($i=0; $i < count($finalrule); $i++) {
                        if($finalrule[$i] == $rule->getContent()){
                            $flag = True;
                        }
                    }
                    if($flag == True) {
                    ?>
                        <td>*<?=$rule->getContent()?></td>
                    <?php
                    }
                    else {
                    ?>
                        <td><?=$rule->getContent()?></td>
                    <?php
                    }
                    ?>
                    <?php
                    foreach($liste as $element){
                        if(isset($array[$rule->getContent()][$element->getContent()])){
                    ?>
                        <td><?=$array[$rule->getContent()][$element->getContent()]?></td>
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