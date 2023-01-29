DROP DATABASE IF EXISTS databaselfa;

CREATE DATABASE databaselfa DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE databaselfa;

DROP USER IF EXISTS 'admin'@'localhost';

CREATE USER 'admin'@'localhost' IDENTIFIED BY '123456789!'; 

GRANT SELECT, INSERT, UPDATE, DELETE ON databaselfa.* TO 'admin'@'localhost';


create table element(
   id integer auto_increment primary key,
   content text not null
);

create table rule(
   id integer auto_increment primary key,
   content text not null,
   reference text
);

create table grammar(
   id integer auto_increment primary key,
   element text not null,
   start_rule text not null,
   end_rule text not null
);


insert into element values (NULL, 's');
insert into element values (NULL, 'e');
insert into element values (NULL, 'n');
insert into element values (NULL, 't');
insert into element values (NULL, 'a');
insert into element values (NULL, 'o');
insert into element values (NULL, 'i');
insert into element values (NULL, 'u');

insert into rule values (NULL, 'S', NULL);
insert into rule values (NULL, 'A', NULL);
insert into rule values (NULL, 'B', NULL);
insert into rule values (NULL, 'C', NULL);
insert into rule values (NULL, 'D', NULL);
insert into rule values (NULL, 'E', NULL);
insert into rule values (NULL, 'F', NULL);
insert into rule values (NULL, 'G', NULL);
insert into rule values (NULL, 'H', NULL);
insert into rule values (NULL, 'I', NULL);
insert into rule values (NULL, 'J', NULL);
insert into rule values (NULL, 'K', NULL);
insert into rule values (NULL, 'L', NULL);
insert into rule values (NULL, 'M', NULL);

insert into grammar values (NULL, 's', 'S', 'A');
insert into grammar values (NULL, 's', 'S', 'H');
insert into grammar values (NULL, 'e', 'S', 'C');
insert into grammar values (NULL, 'e', 'S', 'M');
insert into grammar values (NULL, 'e', 'A', 'B');
insert into grammar values (NULL, 'e', 'H', 'I');
insert into grammar values (NULL, 'e', 'M', 'M');
insert into grammar values (NULL, 'n', 'C', 'D');
insert into grammar values (NULL, 'n', 'I', 'J');
insert into grammar values (NULL, 't', 'D', 'E');
insert into grammar values (NULL, 'a', 'S', 'M');
insert into grammar values (NULL, 'a', 'E', 'F');
insert into grammar values (NULL, 'a', 'J', 'K');
insert into grammar values (NULL, 'a', 'M', 'M');
insert into grammar values (NULL, 'o', 'S', 'M');
insert into grammar values (NULL, 'o', 'F', 'G');
insert into grammar values (NULL, 'o', 'K', 'L');
insert into grammar values (NULL, 'o', 'M', 'M');
insert into grammar values (NULL, 'i', 'S', 'M');
insert into grammar values (NULL, 'i', 'M', 'M');
insert into grammar values (NULL, 'u', 'S', 'M');
insert into grammar values (NULL, 'u', 'M', 'M');