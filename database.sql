--
-- Host: localhost    Database: kalangohp
---------------------------------------------------------
-- Server version	4.1.0-alpha-max-nt

--
-- Table structure for table 'login'
--

DROP TABLE IF EXISTS login;
CREATE TABLE login (
  email varchar(50) NOT NULL default '',
  name varchar(20) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  PRIMARY KEY  (email)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE news (
	id integer AUTO_INCREMENT NOT NULL,
	tempo timestamp NOT NULL,
	titulo varchar(200) NOT NULL,
	descricao blob NOT NULL,
	primary key (id)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE files (
	id	integer AUTO_INCREMENT,
	name varchar(100),
	type varchar(100),
	size integer,
	PRIMARY KEY (id)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE forum (
	timePost timestamp NOT NULL,
	name varchar(100) NOT NULL,
	content blob NOT NULL,
	PRIMARY KEY (timePost, name)
) TYPE=MyISAM CHARSET=latin1;

CREATE TABLE links (
	href varchar(200) NOT NULL,
	name varchar(100) NOT NULL,
	description blob NOT NULL,
	PRIMARY KEY (href)
) TYPE=MyISAM CHARSET=latin1;
