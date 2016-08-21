##Basic steps to setup an arc database in MYSQL
CREATE DATABASE 'arc_db';
CREATE USER 'arc2user'@'localhost' IDENTIFIED BY '****';
grant all privileges on arc_db.* to 'arc2user'@'localhost';
