DROP DATABASE IF EXISTS aurajoen_courses_new;
CREATE DATABASE IF NOT EXISTS aurajoen_courses_new DEFAULT CHARSET utf8;

USE aurajoen_courses_new;

CREATE TABLE account (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL
);

CREATE TABLE person (
	
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	account_id INTEGER NOT NULL,
	
	first_name VARCHAR(255) NOT NULL,
	last_name VARCHAR(255) NOT NULL,
	birthday DATE NOT NULL,
	street_address VARCHAR(255) NOT NULL,
	zipcode INTEGER NOT NULL,
	post_office VARCHAR(255) NOT NULL,

	FOREIGN KEY (account_id) REFERENCES account(id)
);

CREATE TABLE sibling (
	person_id INTEGER NOT NULL,
	sibling_id INTEGER NOT NULL,
	PRIMARY KEY (person_id, sibling_id),
	FOREIGN KEY (person_id) REFERENCES person(id),
	FOREIGN KEY (sibling_id) REFERENCES person(id)
);

INSERT INTO account (id, email, password)
VALUES (
	1,
	'foo@bar.com',
	'asdfasdf'
);

INSERT INTO person (id, account_id, first_name, last_name, birthday, street_address, zipcode, post_office)
VALUES (
	1, 1, 'Eka', 'Uimari', '2004-05-09', 'Eka osoite', '12345', 'Turku'
);

INSERT INTO person (id, account_id, first_name, last_name, birthday, street_address, zipcode, post_office)
VALUES (
	2, 1, 'Toka', 'Uimari', '2002-04-30', 'Eka osoite', '12345', 'Turku'
);

INSERT INTO sibling (person_id, sibling_id)
VALUES (1, 2);

INSERT INTO sibling (person_id, sibling_id)
VALUES (2, 1);