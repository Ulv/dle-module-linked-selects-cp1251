### Linked selects module for dle 9.6
3 linked selects ("education" ,"region", "High school").
Version with cp-1251 encoding

Gets data from mysql db.
ajax, jquery

Usage:
include in template 

    {include file="engine/modules/education.php"}

Mysql tables:

    CREATE TABLE IF NOT EXISTS `region` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;


    CREATE TABLE IF NOT EXISTS `spec` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=47 ;

    CREATE TABLE IF NOT EXISTS `spectovuz` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `spec_id` int(11) NOT NULL,
      `region_id` int(11) NOT NULL,
      `vuz_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `spectovuz_FI_1` (`spec_id`),
      KEY `spectovuz_FI_2` (`region_id`),
      KEY `spectovuz_FI_3` (`vuz_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=954 ;

    CREATE TABLE IF NOT EXISTS `vuz` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=268 ;

admin-links.php - admin script for managing links. Use existing dle user credentials to login.
