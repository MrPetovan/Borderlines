-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 11 Février 2008 à 21:56
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.0
-- 
-- Base de données: `test`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `model_base`
-- 

DROP TABLE IF EXISTS `model_base`;
CREATE TABLE IF NOT EXISTS `model_base` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(30) NOT NULL,
  `description` varchar(250) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `model_base`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `model_base_dependances`
-- 

DROP TABLE IF EXISTS `model_base_dependances`;
CREATE TABLE IF NOT EXISTS `model_base_dependances` (
  `id_page` int(11) NOT NULL,
  `type_model` varchar(10) NOT NULL,
  `index` int(11) NOT NULL,
  `chemin` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `model_base_dependances`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `model_page`
-- 

DROP TABLE IF EXISTS `model_page`;
CREATE TABLE IF NOT EXISTS `model_page` (
  `id` int(11) NOT NULL auto_increment,
  `id_page_base` int(11) default NULL,
  `active` tinyint(1) NOT NULL,
  `code` varchar(30) NOT NULL,
  `description` varchar(250) default NULL,
  `type_user` int(11) NOT NULL,
  `titre` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Contenu de la table `model_page`
-- 

INSERT INTO `model_page` (`id`, `id_page_base`, `active`, `code`, `description`, `type_user`, `titre`) VALUES 
(1, NULL, 1, 'accueil', 'page d''accueil', 0, 'ben titre'),
(2, NULL, 1, 'transac', NULL, 0, NULL),
(3, NULL, 1, 'login', NULL, 0, 'Veuillez vous identifier'),
(4, NULL, 1, 'admin', NULL, 3, NULL);

-- --------------------------------------------------------

-- 
-- Structure de la table `model_page_dependances`
-- 

DROP TABLE IF EXISTS `model_page_dependances`;
CREATE TABLE IF NOT EXISTS `model_page_dependances` (
  `id_page` int(11) NOT NULL,
  `type_model` varchar(10) NOT NULL,
  `index` int(11) NOT NULL,
  `chemin` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `model_page_dependances`
-- 

INSERT INTO `model_page_dependances` (`id_page`, `type_model`, `index`, `chemin`) VALUES 
(1, 'DSPCONTENT', 1, 'content/accueil.php'),
(2, 'TITLE', 1, 'title/transac.php'),
(3, 'DSPCONTENT', 1, 'content/login.php'),
(4, 'TITLE', 1, 'title/admin.php'),
(3, 'ACTION', 1, 'action/login.php'),
(4, 'DSPCONTENT', 1, 'content/admin.php'),
(4, 'SCRIPT', 1, 'scripts\\admin.php');

-- --------------------------------------------------------

-- 
-- Structure de la table `type_model`
-- 

DROP TABLE IF EXISTS `type_model`;
CREATE TABLE IF NOT EXISTS `type_model` (
  `code` char(10) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `type_model`
-- 

INSERT INTO `type_model` (`code`, `libelle`) VALUES 
('ACTION', 'Actions PHP de la page'),
('TITLE', 'définissent GetTitle() pour donner le titre de la page'),
('SCRIPT', 'script Javascript de la page'),
('DSPHEADER', 'partie haute de la page visuelle'),
('DSPLEFT', 'bordure gauche de la page visuelle'),
('DSPCONTENT', 'partie centrale de la page visuelle'),
('DSPRIGHT', 'bordure droite de la page visuelle'),
('DSPFOOTER', 'bas de la page visuelle'),
('LINK', 'liens CSS de la page');

-- --------------------------------------------------------

-- 
-- Structure de la table `type_utilisateur`
-- 

DROP TABLE IF EXISTS `type_utilisateur`;
CREATE TABLE IF NOT EXISTS `type_utilisateur` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Contenu de la table `type_utilisateur`
-- 

INSERT INTO `type_utilisateur` (`id`, `libelle`) VALUES 
(0, 'Visiteur');

-- --------------------------------------------------------

-- 
-- Structure de la table `utilisateur`
-- 

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `type_user` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `utilisateur`
-- 

