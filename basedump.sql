-- MySQL dump 9.11
--
-- Host: localhost    Database: kalangohp
-- ------------------------------------------------------
-- Server version	4.0.22-nt

--
-- Table structure for table `files`
--

CREATE TABLE files (
  id int(11) NOT NULL auto_increment,
  name varchar(100) default NULL,
  type varchar(100) default NULL,
  size int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Dumping data for table `files`
--


--
-- Table structure for table `forum`
--

CREATE TABLE forum (
  timePost timestamp(14) NOT NULL,
  name varchar(100) NOT NULL default '',
  content blob NOT NULL,
  PRIMARY KEY  (timePost,name)
) TYPE=MyISAM;

--
-- Dumping data for table `forum`
--

INSERT INTO forum VALUES (20041209220820,'Edison Eduardo','E tambem agrupadas por nome ou e-mail.');
INSERT INTO forum VALUES (20041209220720,'Edison Eduardo','As notas são ordenadas por ordem decrescente do \r\ntempo de postagem.');
INSERT INTO forum VALUES (20041209220604,'Edison Eduardo','A conclusão do forum esta prestes a acontecer.');
INSERT INTO forum VALUES (20041209220931,'dinho.abreu@gmail.com','Essa é uma demonstração do agrupamento \r\nde notas do forum.');
INSERT INTO forum VALUES (20041209221314,'dinho.abreu@gmail.com','As mensagens ou notações são criadas dinamicamente.');
INSERT INTO forum VALUES (20041209221433,'Edison Eduardo','Para deletar alguma notação, o usuário de estar\r\nautenticado, ou seja, logado.');

--
-- Table structure for table `links`
--

CREATE TABLE links (
  href varchar(200) NOT NULL default '',
  name varchar(100) NOT NULL default '',
  description blob NOT NULL,
  PRIMARY KEY  (href)
) TYPE=MyISAM;

--
-- Dumping data for table `links`
--

INSERT INTO links VALUES ('http://agdn.pyrosoftware.net/main/','agdn.pyrosoftware.net','Site sobre desenvolvimento de jogos');
INSERT INTO links VALUES ('http://www.gamasutra.com','www.gamasutra.com','Site sobre desenvolvimento de jogos');
INSERT INTO links VALUES ('http://www.mgbr.net/','www.mgbr.net','Site brasileiro sobre o mugen');
INSERT INTO links VALUES ('http://www.unidev.com.br','www.unidev.com.br','Site nacional sobre desenvolvimento de jogos');
INSERT INTO links VALUES ('http://www.unidev.com.br/artigos/programandozoinho000.asp?id=424','www.unidev.com.br','Programando sozinho');
INSERT INTO links VALUES ('http://www.allegro.cc/forums/view_thread.php?_id=342381&page=0\r\n','www.allegro.cc','Formatos dos arquivos do mugem');

--
-- Table structure for table `login`
--

CREATE TABLE login (
  email varchar(50) NOT NULL default '',
  name varchar(20) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  PRIMARY KEY  (email)
) TYPE=MyISAM;

--
-- Dumping data for table `login`
--

INSERT INTO login VALUES ('ediedu@terra.com.br','Edison Eduardo de Ab','dbe7583c401677555f4ce86dc3572f1e');
INSERT INTO login VALUES ('aronrodrigues@yahoo.com.br','Aron Rodrigues','4dbe202e13668f34bbed53dad1c0b03a');
INSERT INTO login VALUES ('andrekazuo@gmail.com','André Kazuo Mukudai','47486dbfba96f738cdede5502ec77fb2');

--
-- Table structure for table `news`
--

CREATE TABLE news (
  id int(11) NOT NULL auto_increment,
  tempo timestamp(14) NOT NULL,
  titulo varchar(200) NOT NULL default '',
  descricao blob NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Dumping data for table `news`
--

INSERT INTO news VALUES (1,20041121122426,'18/06/2004 - Início do site do projeto','Resolvi criar vergonha na cara e criar o site do projeto para centralizar as informações do mesmo.');
INSERT INTO news VALUES (2,20041121122451,'16/06/2004 - Análise do projeto pela banca examinadora','Hoje os professores Flávio Tonidandel, Maria Inês Brosso e Enéas Carvalho, analisaram o nosso projeto. Basicamente o projeto foi aprovado e foram sugeridas algumas alterações na documentaçao.');
INSERT INTO news VALUES (3,20041121171044,'22/11/2004 - Criação da parte de administração de conteúdo','A interface ainda está precaria mas está em constante evolução.\r\n');
INSERT INTO news VALUES (20,20041204133547,'Adicionando um usuario','Andre kazuo mukudai');

