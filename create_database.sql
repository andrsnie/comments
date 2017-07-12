CREATE DATABASE comments;

USE comments;

CREATE TABLE header
(
	title VARCHAR(20) NOT NULL,
	poster VARCHAR(20) NOT NULL,
	posted DATETIME NOT NULL,
	postid INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE body
(
	postid INT UNSIGNED NOT NULL PRIMARY KEY,
	message TEXT
);

GRANT SELECT, INSERT, UPDATE, DELETE
ON comments.*
TO comments@localhost IDENTIFIED BY 'PASSWORD';


/*SQL-компонент для создания
базы данных*/
