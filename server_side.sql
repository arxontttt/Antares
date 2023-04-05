DELIMITER $$

CREATE PROCEDURE `GetIpData` ( IN `uid` INT, OUT `ipdata1` VARCHAR( 1000 ) ) NOT DETERMINISTIC NO SQL SQL SECURITY DEFINER BEGIN START TRANSACTION;

SELECT ipdata
INTO ipdata1
FROM users
WHERE ID = uid;

COMMIT ;

END$$

CREATE DEFINER = `root`@`localhost` PROCEDURE `AddLoginLog` ( IN `uid` INT, IN `login1` VARCHAR( 30 ) , IN `ip1` VARCHAR( 30 ) , IN `act` INT ) NOT DETERMINISTIC NO SQL SQL SECURITY DEFINER BEGIN DECLARE tmp_log VARCHAR( 30 ) ;

START TRANSACTION;

IF( login1 IS NULL OR login1 = '' ) THEN SELECT `name` 
INTO tmp_log
FROM users
WHERE `ID` = uid;

ELSE SET tmp_log = login1;

END IF ;

INSERT INTO login_log( `data` , `ip` , `userid` , `login` , `action` ) 
VALUES (now( ) , ip1, uid, tmp_log, act
);

COMMIT ;

END$$

DELIMITER ;

ALTER TABLE `users` CHANGE `Prompt` `Prompt` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `answer` `answer` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

CREATE TABLE IF NOT EXISTS `antibrut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(55) NOT NULL,
  `last_date_fail` bigint(20) NOT NULL,
  `fail_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `changepass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `ip` varchar(55) CHARACTER SET cp1251 NOT NULL,
  `data` datetime NOT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `donate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inv_id` int(11) NOT NULL,
  `p_sys_id` VARCHAR(20) NOT NULL DEFAULT '',
  `don_system` VARCHAR( 30 ) NOT NULL DEFAULT 'UnitPay',
  `data` datetime NOT NULL,
  `out_summ` float(16,2) NOT NULL,
  `don_kurs` float(16,2) NOT NULL,
  `money` int(11) NOT NULL,
  `act_bonus` int(11) NOT NULL DEFAULT '0',
  `bonus_money` int(11) NOT NULL DEFAULT '0',
  `login` varchar(25) NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` varchar(55) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `klan` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `desc` varchar(50) CHARACTER SET utf8 NOT NULL,
  `level` smallint(11) NOT NULL,
  `masterid` int(11) NOT NULL,
  `mastername` varchar(50) CHARACTER SET utf8 NOT NULL,
  `members` int(11) NOT NULL,
  `terr1` tinyint(4) NOT NULL DEFAULT '0',
  `terr2` tinyint(4) NOT NULL DEFAULT '0',
  `terr3` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `klan_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `klanid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `maxcount` int(11) NOT NULL DEFAULT '1',
  `data` varchar(3000) NOT NULL,
  `client_size` int(11) NOT NULL DEFAULT '0',
  `proctype` int(11) NOT NULL DEFAULT '0',
  `expire` int(11) NOT NULL DEFAULT '0',
  `costgold` int(11) NOT NULL DEFAULT '0',
  `costsilver` int(11) NOT NULL DEFAULT '0',
  `cost_item_id` int(11) NOT NULL,
  `cost_item_count` int(11) NOT NULL,
  `remove_no_klan` TINYINT NOT NULL DEFAULT '0',
  `desc` varchar(100) CHARACTER SET utf8 NOT NULL,
  `buycount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `lklogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `ip` varchar(55) NOT NULL,
  `gold` int(11) NOT NULL DEFAULT '0',
  `silver` int(11) NOT NULL DEFAULT '0',
  `gold_rest` int(11) NOT NULL,
  `silver_rest` int(11) NOT NULL,
  `desc` varchar(1024) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `ip` varchar(55) NOT NULL,
  `userid` int(11) NOT NULL,
  `login` varchar(30) NOT NULL,
  `action` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mmotop_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `ip` varchar(55) NOT NULL,
  `name` varchar(25) CHARACTER SET utf8 NOT NULL,
  `login` varchar(25) CHARACTER SET utf8 NOT NULL,
  `userid` int(11) NOT NULL,
  `vote_type` tinyint(4) NOT NULL,
  `points` varchar(20) NOT NULL,
  `send_item` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `qtop_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `name` varchar(25) NOT NULL,
  `userid` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `vote_id` int(11) NOT NULL,
  `vote_type` tinyint(4) NOT NULL,
  `points` varchar(20) NOT NULL,
  `send_item` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shop_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '1',
  `maxcount` int(11) NOT NULL DEFAULT '1',
  `data` varchar(3000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `client_size` int(11) NOT NULL DEFAULT '0',
  `proctype` int(11) NOT NULL DEFAULT '0',
  `subcat` int(11) NOT NULL DEFAULT '0',
  `cost_timeless` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `cost_expire` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `expire` int(11) NOT NULL DEFAULT '0',
  `discount_data` varchar(1000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `desc` varchar(1000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `rest` int(11) NOT NULL DEFAULT '-1',
  `buycount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop_names` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `icon` varchar(128) CHARACTER SET utf8 NOT NULL,
  `list` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shop_subcat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '1',
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `top` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_date` datetime DEFAULT NULL,
  `roleid` int(4) NOT NULL,
  `userid` int(11) NOT NULL,
  `rolename` varchar(100) CHARACTER SET utf8 NOT NULL,
  `rolelevel` int(4) NOT NULL DEFAULT '0',
  `reborn` TINYINT NOT NULL DEFAULT '0',
  `pkvalue` int(11) NOT NULL DEFAULT '0',
  `rolegender` int(4) NOT NULL,
  `roleprof` int(4) NOT NULL,
  `rolerep` int(8) NOT NULL,
  `factionid` int(11) NOT NULL,
  `factionrole` smallint(6) NOT NULL,
  `hp` int(11) NOT NULL,
  `mp` int(11) NOT NULL,
  `timeused` bigint(20) NOT NULL,
  `cashadd` int(11) NOT NULL,
  `cashtotal` int(11) NOT NULL,
  `cashused` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roleid` (`roleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251;

ALTER TABLE `users` ADD `lkgold` INT NOT NULL DEFAULT '0', ADD `lksilver` INT NOT NULL DEFAULT '0', ADD `referal` INT NOT NULL DEFAULT '0', ADD `ref_status` INT NOT NULL DEFAULT '0', ADD `ref_bonus` INT NOT NULL DEFAULT '0', ADD `bonus_data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , ADD `ipdata` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 NOT NULL,
  `expire` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `bonus_money_gold` int(11) NOT NULL DEFAULT '0',
  `bonus_money_silver` int(11) NOT NULL DEFAULT '0',
  `bonus_item_id` int(11) NOT NULL DEFAULT '0',
  `bonus_item_count` int(11) NOT NULL DEFAULT '0',
  `bonus_item_max_count` int(11) NOT NULL DEFAULT '0',
  `bonus_item_data` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `bonus_item_client_size` int(11) NOT NULL DEFAULT '0',
  `bonus_item_proctype` int(11) NOT NULL DEFAULT '0',
  `bonus_item_expire` int(11) NOT NULL DEFAULT '0',
  `multi_user` tinyint(4) NOT NULL DEFAULT '0',
  `used_userid` int(11) NOT NULL DEFAULT '0',
  `desc` varchar(500) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `klan_pic` (`klanid` int(11) NOT NULL, `servid` int(11) NOT NULL DEFAULT '1', `pic` blob NOT NULL, KEY `klanid` (`klanid`), KEY `servid` (`servid`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `users` ADD `session_data` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `users` ADD `vkid` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `passwd`, ADD `vkname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `vkid`, ADD `steamid` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `vkname`, ADD `steamname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `steamid`;
ALTER TABLE `users` ADD `vkphoto` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `vkname`;
ALTER TABLE `users` ADD `steamphoto` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `steamname`;
ALTER TABLE `users` CHANGE `vkid` `vkid` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `vkname` `vkname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `vkphoto` `vkphoto` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `steamid` `steamid` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `steamname` `steamname` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `steamphoto` `steamphoto` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `referal` `referal` INT(11) NOT NULL DEFAULT '0', CHANGE `ipdata` `ipdata` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `session_data` `session_data` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `users` CHANGE `passwd` `passwd` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';