DROP DATABASE IF EXISTS databaselfa;

CREATE DATABASE databaselfa DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE databaselfa;

DROP USER IF EXISTS 'admin'@'localhost';

CREATE USER 'admin'@'localhost' IDENTIFIED BY '123456789!'; 

GRANT SELECT, INSERT, UPDATE, DELETE ON databaselfa.* TO 'admin'@'localhost';


create table element(
   id integer auto_increment primary key,
   content char not null
);

create table rule(
   id integer auto_increment primary key,
   element integer not null,
   content char not null
);

--Fazer uma tabela para  os elementos, uma para as regras com sua referencia, e uma para referenciar a regra de partida e a regra de destino