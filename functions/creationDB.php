<?php

function creationDB() {
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
}

?>