DROP DATABASE IF EXISTS aurajoen_courses_new;
CREATE DATABASE IF NOT EXISTS aurajoen_courses_new DEFAULT CHARSET utf8;

USE aurajoen_courses_new;

CREATE TABLE IF NOT EXISTS account (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS person (
	
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

CREATE TABLE IF NOT EXISTS sibling (
	person_id INTEGER NOT NULL,
	sibling_id INTEGER NOT NULL,
	PRIMARY KEY (person_id, sibling_id),
	FOREIGN KEY (person_id) REFERENCES person(id),
	FOREIGN KEY (sibling_id) REFERENCES person(id)
);

CREATE TABLE IF NOT EXISTS membership (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	season_id INTEGER NOT NULL,
	price DECIMAL(3,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS membership (
	
	id INTEGER NOT NULL PRIMARY KEY,
	membership_id INTEGER NOT NULL,
	person_id INTEGER NOT NULL,

	FOREIGN KEY (membership_id) REFERENCES membership(id),
	FOREIGN KEY (person_id) REFERENCES person(id),
	UNIQUE (membership_id, person_id)
);

CREATE TABLE IF NOT EXISTS season (

	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
	season_start YEAR NOT NULL,
	season_end YEAR NOT NULL,

	UNIQUE (season_start, season_end)
);

CREATE TABLE IF NOT EXISTS period (

	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    season_id INTEGER NOT NULL,
    quarter ENUM('Q1','Q2','Q3','Q4','SUMMER'),

    FOREIGN KEY (season_id) REFERENCES season(id),
    UNIQUE (season_id, quarter)
);

CREATE TABLE IF NOT EXISTS place (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS product (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	description TEXT NOT NULL default ''
);

CREATE TABLE IF NOT EXISTS event (

	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	product_id INTEGER NOT NULL,
	season_id INTEGER NOT NULL,
	place_id INTEGER NOT NULL,
	start_time TIME,
	end_time TIME,

	regstartdate DATE NOT NULL,
	start_date DATE NOT NULL,

	max_participants INTEGER,
	price DECIMAL(3,2) NOT NULL,
	member_price DECIMAL(3,2) NOT NULL,

	FOREIGN KEY (product_id) REFERENCES product(id),
	FOREIGN KEY (season_id) REFERENCES season(id),
	FOREIGN KEY (place_id) REFERENCES place(id)
);

-- *************************

-- use aurajoen_courses;

-- set charset latin1;

-- insert into aurajoen_courses_new.place (name)
-- select distinct(convert(place using utf8))
-- from aurajoen_courses.course
-- where regstartdate > '2015-00-00'
-- 	and lower(name) not like '%j%sen%' and lower(type) not like '%j%sen%'
--     and trim(place) not like ''
-- ;
