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
                    content char not null
                );
                CREATE TABLE IF NOT EXISTS rule(
                    id integer auto_increment primary key,
                    element integer not null,
                    content char not null
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
    

    // Lendo o arquivo TXT
    $txt = file($destination);
    //print_r($txt);

    for ($i=0; $i < count($txt); $i++) { 
        echo($i."<br>");
        $array = str_split($txt[$i]);
        foreach($array as $data) {
            echo($data."<br>");
            if($data == "\n") {
                echo("Achei");
            }
        }
    }
    
    //$array = str_split($txt[5]);
    //foreach($array as $data) {
    //    echo($data."<br>");
    //}

    //parse_str($txt[0], $array);


    ?>
</body>
<script src="assets/js/functions.js"></script>
</html>