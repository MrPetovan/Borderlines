SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Structure de la table `page`
--

CREATE TABLE `page` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `act` varchar(255) NOT NULL,
  `dsp` varchar(255) NOT NULL,
  `login_required` tinyint(1) NOT NULL default '0',
  `admin_required` tinyint(1) NOT NULL default '0',
  `tpl` varchar(255) NOT NULL,
  `rewrite_pattern` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Contenu de la table `page`
--

INSERT INTO `page` (`id`, `code`, `act`, `dsp`, `login_required`, `admin_required`, `tpl`, `rewrite_pattern`) VALUES
(1, 'accueil', '', 'data/static/00accueil.dsp.php', 0, 0, '', ''),
(2, 'erreur', '', 'data/static/error.dsp.php', 0, 1, '', ''),
(3, 'register', 'data/member/register.act.php', 'data/member/register.dsp.php', 0, 0, '', ''),
(4, 'login', 'data/member/login.act.php', 'data/member/login.dsp.php', 0, 0, 'SESSION_PAGELAYOUT', ''),
(5, 'logout', 'data/member/login.act.php', 'data/member/login.dsp.php', 1, 0, '', ''),
(6, 'rappel-identifiants', 'data/member/forgotten_password.act.php', 'data/member/forgotten_password.dsp.php', 0, 0, '', ''),
(7, 'mon-compte', '', 'data/member/mon-compte.dsp.php', 1, 0, '', ''),
(8, 'mon-compte-infos', 'data/member/edit_profile.act.php', 'data/member/edit_profile.dsp.php', 0, 0, '', ''),
(9, 'admin_member', 'data/admin/admin_member.act.php', 'data/admin/admin_member.dsp.php', 1, 1, '', ''),
(10, 'admin_member_view', 'data/admin/admin_member_view.act.php', 'data/admin/admin_member_view.dsp.php', 1, 1, '', '{page}/{id}.html'),
(11, 'admin_member_mod', 'data/admin/admin_member_mod.act.php', 'data/admin/admin_member_mod.dsp.php', 1, 1, '', ''),
(12, 'admin_page', 'data/admin/admin_page.act.php', 'data/admin/admin_page.dsp.php', 1, 1, '', ''),
(13, 'admin_page_mod', 'data/admin/admin_page_mod.act.php', 'data/admin/admin_page_mod.dsp.php', 1, 1, '', '{page}/{id}.html'),
(14, 'mon-compte-identifiants', 'data/member/edit_profile.act.php', 'data/member/edit_identifiants.dsp.php', 1, 0, '', '');
