<?php

/**
 * @Project NUKEVIET 3.0
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES.,JSC. All rights reserved
 * @Createdate 31/05/2010, 00:36
 */
define( 'NV_ADMIN', true );
require_once ( str_replace( '\\\\', '/', dirname( __file__ ) ) . '/mainfile.php' );
require_once ( NV_ROOTDIR . "/includes/core/admin_functions.php" );
$global_config['new_version'] = "3.0.13";

if ( defined( "NV_IS_GODADMIN" ) )
{
    if ( nv_version_compare( $global_config['version'], "3.0.05" ) < 0 )
    {
        die( "program support from only version: 3.0.05" );
    }
    if ( nv_version_compare( $global_config['version'], "3.0.06" ) < 0 )
    {
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_users` ADD `md5username` VARCHAR( 32 ) NOT NULL DEFAULT '' AFTER `username`" );
        $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_users` SET `md5username` = MD5(username)" );
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_users` ADD UNIQUE (`md5username`)" );
        
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_users_reg` ADD `md5username` VARCHAR( 32 ) NOT NULL DEFAULT '' AFTER `username`" );
        $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_users_reg` SET `md5username` = MD5(username)" );
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_users_reg` ADD UNIQUE (`md5username`)" );
        
        $db->sql_query( "DELETE FROM `" . $db_config['prefix'] . "_config` WHERE `module` = 'global' AND `config_name` = 'site_logo'" );
        
        $array_block_title = array();
        $array_block_title['en'][1] = "Hot News";
        $array_block_title['en'][2] = "Top News";
        
        $array_block_title['vi'][1] = "Tin tiêu điểm";
        $array_block_title['vi'][2] = "Tin mới nhất";
        
        $array_block_title['fr'][1] = "Populairs";
        $array_block_title['fr'][2] = "Récents";
        
        $sql = "SELECT lang, setup FROM `" . $db_config['prefix'] . "_setup_language`";
        $result = $db->sql_query( $sql );
        $array_lang_setup = array();
        while ( $row = $db->sql_fetchrow( $result ) )
        {
            $lang_data_i = $row['lang'];
            
            $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_news_block_cat` ADD `number` INT( 11 ) NOT NULL DEFAULT '4' AFTER `adddefault`" );
            $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('" . $lang_data_i . "', 'global', 'site_logo', 'logo.png')" );
            $array_block_title_lang = ( isset( $array_block_title[$lang_data_i] ) ) ? $array_block_title[$lang_data_i] : $array_block_title['en'];
            $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_data_i . "_news_block_cat` SET `title` = '" . $db->dbescape_string( $array_block_title_lang[1] ) . "' WHERE `bid` =1" );
            $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_data_i . "_news_block_cat` SET `title` = '" . $db->dbescape_string( $array_block_title_lang[2] ) . "' WHERE `bid` =2" );
        }
    }
    if ( nv_version_compare( $global_config['version'], "3.0.08" ) < 0 )
    {
        $db->sql_query( "CREATE TABLE IF NOT EXISTS `" . $db_config['prefix'] . "_banip` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `ip` varchar(32) DEFAULT NULL,
							  `mask` tinyint(4) NOT NULL DEFAULT '0',  
							  `area` tinyint(3) NOT NULL,
							  `begintime` int(11) DEFAULT NULL,
							  `endtime` int(11) DEFAULT NULL,
							  `notice` varchar(255) NOT NULL,
							  PRIMARY KEY (`id`),
							  UNIQUE KEY `ip` (`ip`)
							) ENGINE=MyISAM" );
        $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_config` SET `config_value` = '" . min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ) ) . "',  `config_name` = 'nv_max_size' WHERE `lang` = 'sys' AND `module` = 'global' AND `config_name` = 'security_tags'" );
    }
    
    if ( nv_version_compare( $global_config['version'], "3.0.09" ) < 0 )
    {
        $db->sql_query( "CREATE TABLE IF NOT EXISTS `" . NV_AUTHORS_GLOBALTABLE . "_config` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `keyname` varchar(32) DEFAULT NULL,
	  `mask` tinyint(4) NOT NULL DEFAULT '0',
	  `begintime` int(11) DEFAULT NULL,
	  `endtime` int(11) DEFAULT NULL,
	  `notice` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `keyname` (`keyname`)
	) ENGINE=MyISAM" );
        
        $db->sql_query( "ALTER TABLE `nv3_cronjobs` DROP `cron_name`" );
        
        $array_cron_name = array();
        $array_cron_name['en'][1] = 'Delete expired online status';
        $array_cron_name['en'][2] = 'Automatic backup database';
        $array_cron_name['en'][3] = 'Empty temporary files';
        $array_cron_name['en'][4] = 'Delete IP log files';
        $array_cron_name['en'][5] = 'Delete expired error_log log files';
        $array_cron_name['en'][6] = 'Send error logs to admin';
        $array_cron_name['en'][7] = 'Delete expired referer';
        
        $array_cron_name['vi'][1] = 'Xóa các dòng ghi trạng thái online đã cũ trong CSDL';
        $array_cron_name['vi'][2] = 'Tự động lưu CSDL';
        $array_cron_name['vi'][3] = 'Xóa các file tạm trong thư mục tmp';
        $array_cron_name['vi'][4] = 'Xóa IP log files Xóa các file logo truy cập';
        $array_cron_name['vi'][5] = 'Xóa các file error_log quá hạn';
        $array_cron_name['vi'][6] = 'Gửi email các thông báo lỗi cho admin';
        $array_cron_name['vi'][7] = 'Xóa các referer quá hạn';
        
        $array_cron_name['fr'][1] = 'Supprimer les anciens registres du status en ligne dans la base de données';
        $array_cron_name['fr'][2] = 'Sauvegarder automatique la base de données';
        $array_cron_name['fr'][3] = 'Supprimer les fichiers temporaires du répertoire tmp';
        $array_cron_name['fr'][4] = 'Supprimer les fichiers ip_logs expirés';
        $array_cron_name['fr'][5] = 'Supprimer les fichiers error_log expirés';
        $array_cron_name['fr'][6] = 'Envoyer à l\'administrateur l\'e-mail des notifications d\'erreurs';
        $array_cron_name['fr'][7] = 'Supprimer les referers expirés';
        
        $sql = "SELECT lang FROM `" . $db_config['prefix'] . "_setup_language`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $lang_i ) = $db->sql_fetchrow( $result_lang ) )
        {
            $sql = "ALTER TABLE `" . $db_config['prefix'] . "_cronjobs` ADD `" . $lang_i . "_cron_name` VARCHAR( 255 ) NOT NULL DEFAULT ''";
            $db->sql_query( $sql );
            
            $array_cron_name_lang = ( isset( $array_cron_name[$lang_i] ) ) ? $array_cron_name[$lang_i] : $array_cron_name['en'];
            
            $result = $db->sql_query( "SELECT `id`, `run_func` FROM `" . $db_config['prefix'] . "_cronjobs` ORDER BY `id` ASC" );
            while ( list( $id, $run_func ) = $db->sql_fetchrow( $result ) )
            {
                $cron_name = ( isset( $array_cron_name_lang[$id] ) ) ? $array_cron_name_lang[$id] : $run_func;
                $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_cronjobs` SET `" . $lang_i . "_cron_name` =  " . $db->dbescape_string( $cron_name ) . " WHERE `id`=" . $id );
            }
            $db->sql_freeresult();
        }
    }
    
    if ( nv_version_compare( $global_config['version'], "3.0.10" ) < 0 )
    {
        // add keywords module about
        $sql = "SELECT lang FROM `" . $db_config['prefix'] . "_setup_language`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $lang_i ) = $db->sql_fetchrow( $result_lang ) )
        {
            $sql = "SELECT module_data FROM `" . $db_config['prefix'] . "_" . $lang_i . "_modules` WHERE `module_file`='about'";
            $result_mod = $db->sql_query( $sql );
            while ( list( $module_data_i ) = $db->sql_fetchrow( $result_mod ) )
            {
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_i . "_" . $module_data_i . "` ADD `keywords` MEDIUMTEXT NOT NULL AFTER `bodytext`" );
            }
            $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_i . "_modules` SET `admin_file` = '1' WHERE `title` = 'rss'" );
            
            $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_" . $lang_i . "_modfuncs` (`func_id`, `func_name`, `func_custom_name`, `in_module`, `show_func`, `in_submenu`, `subweight`, `layout`, `setting`) VALUES
        		(NULL, 'addads', 'Addads', 'banners', 1, 0, 1, 'left-body-right', ''),
        		(NULL, 'cledit', 'Cledit', 'banners', 0, 0, 0, '', ''),
        		(NULL, 'clientinfo', 'Clientinfo', 'banners', 1, 0, 2, 'left-body-right', ''),
        		(NULL, 'clinfo', 'Clinfo', 'banners', 0, 0, 0, '', ''),
        		(NULL, 'logininfo', 'Logininfo', 'banners', 0, 0, 0, '', ''),
        		(NULL, 'stats', 'Stats', 'banners', 1, 0, 3, 'left-body-right', ''),
        		(NULL, 'viewmap', 'Viewmap', 'banners', 0, 0, 0, '', '')" );
        }
        $db->sql_freeresult();
        //end add keywords module about
        $forbid_extensions = $global_config['forbid_extensions'];
        $forbid_extensions[] = "php";
        $forbid_extensions[] = "php3";
        $forbid_extensions[] = "php4";
        $forbid_extensions[] = "php5";
        $forbid_extensions[] = "phtml";
        $forbid_extensions[] = "inc";
        $forbid_extensions = array_unique( $forbid_extensions );
        $forbid_extensions = implode( ',', $forbid_extensions );
        
        $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_config` SET `config_value`=" . $db->dbescape_string( $forbid_extensions ) . " WHERE `config_name` = 'forbid_extensions' AND `lang` = 'sys' AND `module`='global' LIMIT 1" );
    }
    if ( nv_version_compare( $global_config['version'], "3.0.11" ) < 0 )
    {
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_banners_clients` ADD `uploadtype` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `last_agent`" );
        
        $sql = "SELECT lang FROM `" . $db_config['prefix'] . "_setup_language`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $lang_i ) = $db->sql_fetchrow( $result_lang ) )
        {
            $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_i . "_modules` ADD `rss` TINYINT( 4 ) NOT NULL DEFAULT '1'" );
        }
    }
    if ( nv_version_compare( $global_config['version'], "3.0.12" ) < 0 )
    {
        // add userid to table comments module news
        $sql = "SELECT lang FROM `" . $db_config['prefix'] . "_setup_language`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $lang_i ) = $db->sql_fetchrow( $result_lang ) )
        {
            $sql = "SELECT module_data FROM `" . $db_config['prefix'] . "_" . $lang_i . "_modules` WHERE `module_file`='news'";
            $result_mod = $db->sql_query( $sql );
            while ( list( $module_data_i ) = $db->sql_fetchrow( $result_mod ) )
            {
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_i . "_" . $module_data_i . "_comments` ADD `userid` INT( 11 ) NOT NULL DEFAULT '0' AFTER `post_time`" );
            }
            $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_" . $lang_i . "_modfuncs` (`func_id`, `func_name`, `func_custom_name`, `in_module`, `show_func`, `in_submenu`, `subweight`, `layout`, `setting`) VALUES
	            (NULL, 'search', 'Search', 'download', 1, 0, 1, 'left-body-right', ''),
				(NULL, 'viewcat', 'Viewcat', 'download', 1, 0, 2, 'left-body-right', ''),
				(NULL, 'viewfile', 'Viewfile', 'download', 1, 0, 3, 'left-body-right', '')" );
        }
        $db->sql_freeresult();
        
        // config module authors
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'authors_detail_main', '0')" );
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'spadmin_add_admin', '1')" );
        
        $db->sql_query( "CREATE TABLE `" . $db_config['prefix'] . "_logs` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`lang` varchar(10) NOT NULL,
			`module_name` varchar(150) NOT NULL,
			`name_key` varchar(255) NOT NULL,
			`note_action` text NOT NULL,
			`link_acess` varchar(255) NOT NULL,
			`userid` int(11) NOT NULL,
			`log_time` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM" );
    }
    
    if ( nv_version_compare( $global_config['version'], "3.0.13" ) < 0 )
    {
        $sql = "SELECT lang FROM `" . $db_config['prefix'] . "_setup_language`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $lang_data_i ) = $db->sql_fetchrow( $result_lang ) )
        {
            //update logo site
            $db->sql_query( "REPLACE INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('" . $lang_data_i . "', 'global', 'site_logo', 'images/logo.png')" );
            
            //update alias module about
            $sql = "SELECT module_data FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_modules` WHERE `module_file`='about'";
            $result_mod = $db->sql_query( $sql );
            while ( list( $module_data_i ) = $db->sql_fetchrow( $result_mod ) )
            {
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "` DROP INDEX `title`" );
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "` ADD UNIQUE (`alias`)" );
            }
            
            //update config module news
            $sql = "SELECT module_data FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_modules` WHERE `module_file`='news'";
            $result_mod = $db->sql_query( $sql );
            while ( list( $module_data_i ) = $db->sql_fetchrow( $result_mod ) )
            {
                $db->sql_query( "CREATE TABLE IF NOT EXISTS `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_config_post` (
  										  `pid` mediumint(9) NOT NULL auto_increment,
                                          `member` tinyint(4) NOT NULL,
                                          `group_id` mediumint(9) NOT NULL,
                                          `addcontent` tinyint(4) NOT NULL,
                                          `postcontent` tinyint(4) NOT NULL,
                                          `editcontent` tinyint(4) NOT NULL,
                                          `delcontent` tinyint(4) NOT NULL,
                                          PRIMARY KEY  (`pid`),
                                          UNIQUE KEY `member` (`member`,`group_id`)
                                        ) ENGINE=MyISAM" );
                
                $db->sql_query( "CREATE TABLE IF NOT EXISTS `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_admins` (
										  `userid` int(11) NOT NULL default '0',
										  `catid` int(11) NOT NULL default '0',
										  `admin` tinyint(4) NOT NULL default '0',
										  `add_content` tinyint(4) NOT NULL default '0',
										  `pub_content` tinyint(4) NOT NULL default '0',
										  `edit_content` tinyint(4) NOT NULL default '0',
										  `del_content` tinyint(4) NOT NULL default '0',
										  `comment` tinyint(4) NOT NULL default '0',
										  UNIQUE KEY `userid` (`userid`,`catid`)
										) ENGINE=MyISAM" );
                // update rating
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_rows` ADD `total_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `hitscm`, ADD `click_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `total_rating`" );
                $result = $db->sql_query( "SELECT `id`, `ratingdetail` FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_rows`" );
                while ( list( $id, $ratingdetail ) = $db->sql_fetchrow( $result ) )
                {
                    $array_rating = array_map( "intval", explode( "|", $ratingdetail ) );
                    $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_rows` SET `total_rating`=" . $array_rating[0] . ", `click_rating`=" . $array_rating[1] . " WHERE `id`=" . $id );
                }
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_rows` DROP `ratingdetail`, DROP `hitslm`" );
                
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_log` ADD `total_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `hitscm`, ADD `click_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `total_rating`" );
                $result = $db->sql_query( "SELECT `id`, `ratingdetail` FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_log`" );
                while ( list( $id, $ratingdetail ) = $db->sql_fetchrow( $result ) )
                {
                    $array_rating = array_map( "intval", explode( "|", $ratingdetail ) );
                    $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_log` SET `total_rating`=" . $array_rating[0] . ", `click_rating`=" . $array_rating[1] . " WHERE `id`=" . $id );
                }
                $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_log` DROP `ratingdetail`, DROP `hitslm`" );
                
                $result = $db->sql_query( "SELECT `catid` FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_cat` ORDER BY `order` ASC" );
                while ( list( $catid_i ) = $db->sql_fetchrow( $result ) )
                {
                    $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_" . $catid_i . "` ADD `total_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `hitscm`, ADD `click_rating` INT( 11 ) NOT NULL DEFAULT '0' AFTER `total_rating`" );
                    $result_catid_i = $db->sql_query( "SELECT `id`, `ratingdetail` FROM `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_" . $catid_i . "`" );
                    while ( list( $id, $ratingdetail ) = $db->sql_fetchrow( $result_catid_i ) )
                    {
                        $array_rating = array_map( "intval", explode( "|", $ratingdetail ) );
                        $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_" . $catid_i . "` SET `total_rating`=" . $array_rating[0] . ", `click_rating`=" . $array_rating[1] . " WHERE `id`=" . $id );
                    }
                    $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_" . $module_data_i . "_" . $catid_i . "` DROP `ratingdetail`, DROP `hitslm`" );
                }
            }
            //config Sitemap
            $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_" . $lang_data_i . "_modfuncs` (`func_id`, `func_name`, `func_custom_name`, `in_module`, `show_func`, `in_submenu`, `subweight`, `layout`, `setting`) VALUES 
            (NULL, 'Sitemap', 'Sitemap', 'news', 0, 0, 0, '', ''),
            (NULL, 'Sitemap', 'Sitemap', 'about', 0, 0, 0, '', ''),
            (NULL, 'Sitemap', 'Sitemap', 'download', 0, 0, 0, '', ''),
            (NULL, 'Sitemap', 'Sitemap', 'weblinks', 0, 0, 0, '', '')" );
            
            //create table block
            $db->sql_query( "CREATE TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_blocks_groups` (
				  `bid` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `theme` varchar(55) NOT NULL,
				  `module` varchar(55) NOT NULL,
				  `file_name` varchar(55) DEFAULT NULL,
				  `title` varchar(255) DEFAULT NULL,
				  `link` varchar(255) DEFAULT NULL,
				  `template` varchar(55) DEFAULT NULL,
				  `position` varchar(55) DEFAULT NULL,
				  `exp_time` int(11) DEFAULT '0',
				  `active` tinyint(4) DEFAULT '0',
				  `groups_view` varchar(255) DEFAULT '',
				  `all_func` tinyint(4) NOT NULL DEFAULT '0',
				  `weight` int(11) NOT NULL DEFAULT '0',
				  `config` text,
				  PRIMARY KEY (`bid`),
				  KEY `theme` (`theme`),
				  KEY `module` (`module`),
				  KEY `position` (`position`),
				  KEY `exp_time` (`exp_time`)	 
				) ENGINE=MyISAM" );
            
            $db->sql_query( "CREATE TABLE `" . $db_config['prefix'] . "_" . $lang_data_i . "_blocks_weight` (
				  `bid` int(11) NOT NULL DEFAULT '0',
				  `func_id` int(11) NOT NULL DEFAULT '0',
				  `weight` int(11) NOT NULL DEFAULT '0',
				  UNIQUE KEY `bid` (`bid`,`func_id`)	 
				) ENGINE=MyISAM" );
            
            // update block
            $array_funcid = array();
            $func_result = $db->sql_query( "SELECT `func_id` FROM `" . NV_MODFUNCS_TABLE . "` WHERE `show_func` = '1' ORDER BY `in_module` ASC, `subweight` ASC" );
            while ( list( $func_id_i ) = $db->sql_fetchrow( $func_result ) )
            {
                $array_funcid[] = $func_id_i;
            }
            
            $block_result = $db->sql_query( "SELECT * FROM `" . NV_BLOCKS_TABLE . "` GROUP BY `groupbl` ORDER BY `bid` ASC" );
            while ( $brow = $db->sql_fetchrow( $block_result ) )
            {
                list( $maxweight ) = $db->sql_fetchrow( $db->sql_query( "SELECT MAX(weight) FROM `" . NV_BLOCKS_TABLE . "_groups` WHERE theme =" . $db->dbescape( $brow['theme'] ) . " AND `position`=" . $db->dbescape( $brow['position'] ) ) );
                $brow['weight'] = intval( $maxweight ) + 1;
                
                $brow['file_name'] = $brow['file_path'];
                $brow['config'] = "";
                if ( $brow['type'] == "banner" )
                {
                    $brow['config'] = "a:1:{s:12:\"idplanbanner\";i:" . $brow['file_path'] . ";}";
                    $brow['file_name'] = "global.banners.php";
                    $brow['module'] = "banners";
                }
                elseif ( $brow['type'] == "html" )
                {
                    $block_config = array();
                    $block_config['htmlcontent'] = $brow['file_path'];
                    
                    $brow['config'] = serialize( $block_config );
                    $brow['file_name'] = "global.html.php";
                    $brow['module'] = "global";
                }
                elseif ( $brow['type'] == "rss" )
                {
                    $array_rrs = explode( "#@#", $brow['file_path'] );
                    $block_config = array();
                    $block_config['url'] = $array_rrs[0];
                    $block_config['number'] = $array_rrs[1];
                    $block_config['isdescription'] = $array_rrs[2];
                    $block_config['ishtml'] = $array_rrs[3];
                    $block_config['ispubdate'] = $array_rrs[4];
                    $block_config['istarget'] = $array_rrs[5];
                    
                    $brow['config'] = serialize( $block_config );
                    $brow['file_name'] = "global.rss.php";
                    $brow['module'] = "rss";
                }
                elseif ( $brow['module'] == "global" )
                {
                    if ( $brow['file_name'] == "global.counter.php" )
                    {
                        $brow['module'] = "statistics";
                    }
                    elseif ( $brow['file_name'] == "global.about.php" )
                    {
                        $brow['module'] = "about";
                    }
                    elseif ( $brow['file_name'] == "global.voting.php" )
                    {
                        $brow['module'] = "voting";
                        $brow['file_name'] = "global.voting_random.php";
                    }
                    elseif ( $brow['file_name'] == "global.login.php" )
                    {
                        $brow['module'] = "users";
                    }
                }
                
                $brow['bid'] = $db->sql_query_insert_id( "INSERT INTO `" . NV_BLOCKS_TABLE . "_groups` (`bid`, `theme`, `module`, `file_name`, `title`, `link`, `template`, `position`, `exp_time`, `active`, `groups_view`, `all_func`, `weight`, `config`) VALUES ( NULL, " . $db->dbescape( $brow['theme'] ) . ", " . $db->dbescape( $brow['module'] ) . ", " . $db->dbescape( $brow['file_name'] ) . ", " . $db->dbescape( $brow['title'] ) . ", " . $db->dbescape( $brow['link'] ) . ", " . $db->dbescape( $brow['template'] ) . ", " . $db->dbescape( $brow['position'] ) . ", '" . $brow['exp_time'] . "', '" . $brow['active'] . "', " . $db->dbescape( $brow['groups_view'] ) . ", '" . $brow['all_func'] . "', '" . $brow['weight'] . "', " . $db->dbescape( $brow['config'] ) . " )" );
                
                if ( $brow['all_func'] )
                {
                    $func_list = $array_funcid;
                }
                else
                {
                    $func_list = array();
                    $func_result = $db->sql_query( "SELECT `func_id` FROM `" . NV_BLOCKS_TABLE . "` WHERE `groupbl` = '" . $brow['groupbl'] . "'" );
                    while ( list( $func_id_i ) = $db->sql_fetchrow( $func_result ) )
                    {
                        $func_list[] = $func_id_i;
                    }
                }
                foreach ( $func_list as $func_id )
                {
                    $sql = "SELECT MAX(t1.weight) FROM `" . NV_BLOCKS_TABLE . "_weight` AS t1 INNER JOIN `" . NV_BLOCKS_TABLE . "_groups` AS t2 ON t1.bid = t2.bid WHERE t1.func_id=" . $func_id . " AND t2.theme=" . $db->dbescape( $brow['theme'] ) . " AND t2.position=" . $db->dbescape( $brow['position'] ) . "";
                    list( $weight ) = $db->sql_fetchrow( $db->sql_query( $sql ) );
                    $weight = intval( $weight ) + 1;
                    
                    $db->sql_query( "INSERT INTO `" . NV_BLOCKS_TABLE . "_weight` (`bid`, `func_id`, `weight`) VALUES ('" . $brow['bid'] . "', '" . $func_id . "', '" . $weight . "')" );
                }
            }
            
            //Insert site_keywords lang
            $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('" . $lang_data_i . "', 'global', 'site_keywords', " . $db->dbescape_string( $global_config['site_keywords'] ) . ")" );
        }
        
        //update module banner
        $db->sql_query( "ALTER TABLE `" . NV_BANNERS_ROWS_GLOBALTABLE . "` ADD `weight` INT( 11 ) NOT NULL DEFAULT '0'" );
        
        $sql = "SELECT id, file_name FROM `" . NV_BANNERS_ROWS_GLOBALTABLE . "`";
        $result_lang = $db->sql_query( $sql );
        while ( list( $id, $file_name ) = $db->sql_fetchrow( $result_lang ) )
        {
            $file_name = str_replace( "uploads/banners/", "", $file_name );
            $db->sql_query( "UPDATE `" . NV_BANNERS_ROWS_GLOBALTABLE . "` SET `file_name`=" . $db->dbescape_string( $file_name ) . " WHERE `id` = '" . $id . "'" );
        }
        
        //Website Optimization
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'optActive', '1')" );
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'googleAnalyticsID', '')" );
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'googleAnalyticsSetDomainName', '0')" );
        
        //upload_checking_mode
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'upload_checking_mode', 'strong')" );
        
        //Them tu dong cap nhat Thu hang site
        $id = $db->sql_query_insert_id( "INSERT INTO `" . NV_CRONJOBS_GLOBALTABLE . "` (`id`, `start_time`, `interval`, `run_file`, `run_func`, `params`, `del`, `is_sys`, `act`, `last_time`, `last_result`) VALUES (NULL, " . ( NV_CURRENTTIME - 86400 ) . ", 1440, 'siteDiagnostic_update.php', 'cron_siteDiagnostic_update', '', 0, 1, 1, 0, 0)" );
        $columns = array();
        $result = $db->sql_query( "SHOW COLUMNS FROM `" . NV_CRONJOBS_GLOBALTABLE . "`" );
        while ( $row = $db->sql_fetch_assoc( $result ) )
        {
            unset( $matches );
            if ( preg_match( "/^(.*?)\_cron\_name$/", $row['Field'], $matches ) )
            {
                $columns[] = ( $matches[1] == "vi" ) ? "`" . $row['Field'] . "`=" . $db->dbescape( "Cập nhật thứ hạng site theo máy chủ tìm kiếm" ) : "`" . $row['Field'] . "`=" . $db->dbescape( "Update site diagnostic" );
            }
        }
        $db->sql_freeresult( $result );
        $columns = implode( ", ", $columns );
        $db->sql_query( "UPDATE `" . NV_CRONJOBS_GLOBALTABLE . "` SET " . $columns . " WHERE `id`=" . $id );
        
        //TABLE config: config_value varchar(255) => MEDIUMTEXT
        $db->sql_query( "ALTER TABLE `" . $db_config['prefix'] . "_config` CHANGE `config_value` `config_value` MEDIUMTEXT NOT NULL" );
        
        //Closed site
        $db->sql_query( "INSERT INTO `" . $db_config['prefix'] . "_config` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'global', 'closed_site', '0')" );
        $db->sql_query( "DELETE FROM `" . $db_config['prefix'] . "_config` WHERE `module` = 'global' AND `config_name` = 'disable_site'" );
        
        //delete site_keywords system
        $db->sql_query( "DELETE FROM `" . $db_config['prefix'] . "_config` WHERE `lang`='sys' AND module` = 'global' AND `config_name` = 'site_keywords'" );
    }
    $db->sql_query( "UPDATE `" . $db_config['prefix'] . "_config` SET `config_value` = '" . $global_config['new_version'] . "' WHERE `lang` = 'sys' AND `module` = 'global' AND `config_name` = 'version'" );
    nv_save_file_config_global();
    
    die( "Update successfully, you should immediately delete this file." );
}
else
{
    die( "You need login with god administrator" );
}

?>