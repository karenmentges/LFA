DROP DATABASE IF EXISTS databaselfa;

CREATE DATABASE databaselfa DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE databaselfa;

DROP USER IF EXISTS 'admin'@'localhost';

CREATE USER 'admin'@'localhost' IDENTIFIED BY '123456789!'; 

GRANT SELECT, INSERT, UPDATE, DELETE ON databaselfa.* TO 'admin'@'localhost';


create table alphabet(
   id integer auto_increment primary key,
   content text not null
);

create table state(
   id integer auto_increment primary key,
   content text not null,
   reference text
);

create table transition(
   id integer auto_increment primary key,
   alphabet text not null,
   start_state text not null,
   end_state text not null
);


insert into alphabet values (NULL, 's');
insert into alphabet values (NULL, 'e');
insert into alphabet values (NULL, 'n');
insert into alphabet values (NULL, 't');
insert into alphabet values (NULL, 'a');
insert into alphabet values (NULL, 'o');
insert into alphabet values (NULL, 'i');
insert into alphabet values (NULL, 'u');

insert into state values (NULL, 'S', NULL);
insert into state values (NULL, 'A', NULL);
insert into state values (NULL, 'B', NULL);
insert into state values (NULL, 'C', NULL);
insert into state values (NULL, 'D', NULL);
insert into state values (NULL, 'E', NULL);
insert into state values (NULL, 'F', NULL);
insert into state values (NULL, 'G', NULL);
insert into state values (NULL, 'H', NULL);
insert into state values (NULL, 'I', NULL);
insert into state values (NULL, 'J', NULL);
insert into state values (NULL, 'K', NULL);
insert into state values (NULL, 'L', NULL);
insert into state values (NULL, 'M', NULL);

insert into transition values (NULL, 's', 'S', 'A');
insert into transition values (NULL, 's', 'S', 'H');
insert into transition values (NULL, 'e', 'S', 'C');
insert into transition values (NULL, 'e', 'S', 'M');
insert into transition values (NULL, 'e', 'A', 'B');
insert into transition values (NULL, 'e', 'H', 'I');
insert into transition values (NULL, 'e', 'M', 'M');
insert into transition values (NULL, 'n', 'C', 'D');
insert into transition values (NULL, 'n', 'I', 'J');
insert into transition values (NULL, 't', 'D', 'E');
insert into transition values (NULL, 'a', 'S', 'M');
insert into transition values (NULL, 'a', 'E', 'F');
insert into transition values (NULL, 'a', 'J', 'K');
insert into transition values (NULL, 'a', 'M', 'M');
insert into transition values (NULL, 'o', 'S', 'M');
insert into transition values (NULL, 'o', 'F', 'G');
insert into transition values (NULL, 'o', 'K', 'L');
insert into transition values (NULL, 'o', 'M', 'M');
insert into transition values (NULL, 'i', 'S', 'M');
insert into transition values (NULL, 'i', 'M', 'M');
insert into transition values (NULL, 'u', 'S', 'M');
insert into transition values (NULL, 'u', 'M', 'M');