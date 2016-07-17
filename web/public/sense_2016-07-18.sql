# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.34)
# Database: sense
# Generation Time: 2016-07-17 17:26:51 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table account_privilege
# ------------------------------------------------------------

DROP TABLE IF EXISTS `account_privilege`;

CREATE TABLE `account_privilege` (
  `Fid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Faction` varchar(30) NOT NULL DEFAULT '' COMMENT 'action',
  `Fname` varchar(30) NOT NULL DEFAULT '' COMMENT '名字',
  `Fparent_id` int(11) unsigned NOT NULL COMMENT '父模块id',
  PRIMARY KEY (`Fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `account_privilege` WRITE;
/*!40000 ALTER TABLE `account_privilege` DISABLE KEYS */;

INSERT INTO `account_privilege` (`Fid`, `Faction`, `Fname`, `Fparent_id`)
VALUES
	(16,'statistics','数据统计',0),
	(32,'company','企业管理',0),
	(33,'index','企业管理',32),
	(34,'user','管理员设置',32),
	(43,'index','访问统计',16),
	(74,'account','权限管理',0),
	(75,'user','用户管理',74),
	(76,'role','角色管理',74),
	(77,'module','模块管理',74),
	(78,'page','单页管理',0),
	(79,'index','单页列表',78),
	(80,'home','首页管理',0),
	(81,'banner','轮播图配置',80),
	(82,'list','首页列表',80),
	(83,'partner','合作伙伴',80),
	(84,'lock','加密锁管理',0),
	(85,'type','产品类别',84),
	(86,'download','资源下载',84),
	(87,'list','产品管理',84),
	(88,'manage','综合管理',0),
	(89,'menu','菜单管理',88),
	(90,'solution','解决方案',88),
	(91,'cloud','资源云授权',88),
	(92,'route','成长历程',88),
	(93,'cert','资质和产权',88),
	(94,'recruit','招聘配置',88),
	(95,'news','新闻配置',88);

/*!40000 ALTER TABLE `account_privilege` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table account_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `account_role`;

CREATE TABLE `account_role` (
  `Fid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Fname` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名',
  `Fcid` varchar(255) NOT NULL DEFAULT '' COMMENT '企业id',
  PRIMARY KEY (`Fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `account_role` WRITE;
/*!40000 ALTER TABLE `account_role` DISABLE KEYS */;

INSERT INTO `account_role` (`Fid`, `Fname`, `Fcid`)
VALUES
	(6,'超级管理员','1'),
	(7,'管理员','1');

/*!40000 ALTER TABLE `account_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table account_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `account_user`;

CREATE TABLE `account_user` (
  `Fid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Fcid` varchar(255) NOT NULL COMMENT '企业id',
  `Frid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '角色id',
  `Faccount` varchar(50) NOT NULL DEFAULT '',
  `Fpassword` varchar(50) NOT NULL DEFAULT '',
  `Fname` varchar(20) NOT NULL DEFAULT '',
  `Fphone` varchar(20) NOT NULL DEFAULT '',
  `Femail` varchar(50) NOT NULL DEFAULT '',
  `Ftime` int(11) NOT NULL COMMENT '创建时间',
  `Fupdate_time` int(11) NOT NULL COMMENT '修改时间',
  `Fdel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0正常 1禁用',
  `Fis_admin` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否是管理员 0否1是',
  PRIMARY KEY (`Fid`),
  UNIQUE KEY `Faccount` (`Faccount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `account_user` WRITE;
/*!40000 ALTER TABLE `account_user` DISABLE KEYS */;

INSERT INTO `account_user` (`Fid`, `Fcid`, `Frid`, `Faccount`, `Fpassword`, `Fname`, `Fphone`, `Femail`, `Ftime`, `Fupdate_time`, `Fdel`, `Fis_admin`)
VALUES
	(1,'1',0,'admin@admin.com','96e79218965eb72c92a549dd5a330112','超级管理员','18500000000','',1458290380,1468685875,0,1),
	(2,'1',7,'zhanghongtao','655bb5c937fa354d936cc42cf6f67033','张宏涛','','',1458619734,0,0,0);

/*!40000 ALTER TABLE `account_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table banners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `sub_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `btn_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `btn_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;

INSERT INTO `banners` (`id`, `title`, `sub_title`, `img`, `url`, `btn_name`, `btn_url`, `sort_num`, `created_at`, `updated_at`)
VALUES
	(1,'深思安全授权平台','助您轻松转变商业模式 变客户为用户','/image/2016/07/16/CR-e7itTuPamB0GyOJ.png','','免费注册','http://developer.senseshield.com/auth/register.jsp',1,'2016-06-13 15:04:29','2016-07-16 08:59:04'),
	(2,'深思安全授权平台','即时授权 提升用户体验 软件保护“零成本”','/image/2016/07/16/CR-Vl5sp5G3QRvOVnI.png','','免费注册','http://developer.senseshield.com/auth/register.jsp',2,'2016-06-13 15:04:29','2016-07-16 09:00:30'),
	(3,'深思安全授权平台','精准掌握所有软件授权使用情况','/image/2016/07/16/CR-PJvjl6GgJse1PNg.png','','免费注册','http://developer.senseshield.com/auth/register.jsp',3,'2016-06-13 15:04:29','2016-07-16 08:59:33'),
	(4,'深思安全授权平台','顶级安全技术全自动加密引擎 远离盗版','/image/2016/07/16/CR-pMrqlQPJCUJxS8j.png','','免费注册','http://developer.senseshield.com/auth/register.jsp',4,'2016-06-13 15:04:29','2016-07-16 08:59:23');

/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table certs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `certs`;

CREATE TABLE `certs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pic` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 知识产权 1公司资质',
  `sort_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `certs` WRITE;
/*!40000 ALTER TABLE `certs` DISABLE KEYS */;

INSERT INTO `certs` (`id`, `pic`, `name`, `type`, `sort_num`)
VALUES
	(1,'/image/2016/07/16/CR-ioCm6idLHPZod0u.jpg','专利号：ZL 2012 1 0384792.8',0,3),
	(4,'/image/2016/07/16/CR-MsBJp2eoQcnAE08.jpg','专利号：ZL 2012 1 0344786.X',0,1),
	(5,'/image/2016/07/16/CR-SCnA2uDejz1JyCd.jpg','专利号：ZL 2012 1 0344765.8',0,2),
	(6,'/image/2016/07/16/CR-PBZZgbU8iHPAHQB.jpg','专利号：ZL 2012 1 0344801.0',0,4),
	(7,'/image/2016/07/16/CR-xkbVKt5jkVAbsMu.jpg','专利号：ZL 2013 1 0389871.2',0,5),
	(8,'/image/2016/07/16/CR-w2EeMDI8YaTSisP.jpg','专利号：ZL 2006 1 0064823.6',0,6),
	(9,'/image/2016/07/16/CR-npVplrnf4EfAKp5.jpg','专利号：ZL 2012 1 0355582.6',0,7),
	(10,'/image/2016/07/16/CR-XApc2jhi5EhbaY7.jpg','专利号：ZL 2012 1 0484132.7',0,8),
	(11,'/image/2016/07/16/CR-DlBl5gmCzSWjbi5.jpg','商品密码产品生产定点单位证书',1,1),
	(12,'/image/2016/07/16/CR-ATFRb6V5P53fOgc.jpg','软件企业认定证书',1,2),
	(13,'/image/2016/07/16/CR-GMKP4MY25Uqi2Vd.jpg','中关村高新技术企业',1,3),
	(14,'/image/2016/07/16/CR-PK9I2Qt8Jp79V4G.jpg','专利试点证书',1,4),
	(15,'/image/2016/07/16/CR-0u1m19bvpXWXKkF.jpg','中关村十佳中小高新技术企业证书',1,5),
	(16,'/image/2016/07/16/CR-rUoeqKbHALGoOo4.jpg','瞪羚计划一星级会员证书',1,6),
	(17,'/image/2016/07/16/CR-MHvnZiiEDXCb8cz.jpg','深思数盾IOS中文证书',1,7),
	(18,'/image/2016/07/16/CR-iWaYpGixfpLeGb7.jpg','精锐4 CE证书',1,8),
	(19,'/image/2016/07/16/CR-ZWGSXv5u5peQxGi.jpg','精锐4 FCC证书',1,9),
	(20,'/image/2016/07/16/CR-ESnXQpti1pToWwj.jpg','C-Tick证书',1,10),
	(21,'/image/2016/07/16/CR-Bv0pGAhSSnT1Pom.jpg','精锐4 REACH检测报告',1,11),
	(22,'/image/2016/07/16/CR-qleXShYPDNaIM3l.jpg','精锐4 WEEE检测报告',1,12),
	(23,'/image/2016/07/16/CR-rwDuK6HqHUFKCRv.jpg','灵锐加密锁ROHS检测报告',1,13),
	(24,'/image/2016/07/16/CR-AA1aCBP7Vvwo40m.jpg','灵锐加密锁CE检测报告',1,14),
	(25,'/image/2016/07/16/CR-R0jrFfbZvo8Gn4V.jpg','灵锐加密锁FCC检测报告',1,15);

/*!40000 ALTER TABLE `certs` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cloud_params
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cloud_params`;

CREATE TABLE `cloud_params` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `cloud_id` int(11) NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cloud_params` WRITE;
/*!40000 ALTER TABLE `cloud_params` DISABLE KEYS */;

INSERT INTO `cloud_params` (`id`, `name`, `content`, `cloud_id`, `sort_num`)
VALUES
	(5,'虚拟化','简单来说就是把程序中的指令，变成在虚拟机中执行的加密代码块，使得整个程序在被调试和跟踪的时候变得极其难懂。到目前为止，这是效果最好的代码逻辑保护技术',1,2),
	(6,'碎片代码执行','深思“碎片代码执行”即是为了解决代码移植不能很好地使用而提出的全新加密理念：利用自身成熟的外壳中的代码提取技术，抽取大量、大段代码，加密混淆后在安全环境中执行，大量的将 Virbox、虚拟化和驱动技术应用于其中，最大程度上减少对加密锁底层技术和功能的依赖，同时大量大段地移植又保证了更高的安全性。 除了安全性，依托 SS 加密中间件，将对硬件的依赖在底层自动处理好，可以做到和深思云锁兼容，未来开发商业务无论如何变化都可以应对。 这是加密技术的一次综合应用，效果上类似于将软件打散执行，让破解者无从下手，这就是“碎片代码执行”，是深思软件保护理念的一次重大突破。',1,1),
	(7,'Virbox 编译器','Virbox 编译器是深思数盾自主研发的安全虚拟机编译引擎。具有多态混淆，反调试和数据保护等重要功能，可针对C/C++语言进行高强度的虚拟化保护。由于直接编译源代码，比传统外壳虚拟化技术有诸多优势： \n1.	对杀毒软件极友好，几乎和普通程序一样 \n2.	相同安全级别下，相比与普通外壳，生成的目标尺寸小、进行速度快 \n3.	由于信息更多，大大扩展了可保护的范围，增加了安全性。 \n目前精锐5加密系统中几乎所有的 API、安全服务、反黑驱动都使用了 Virbox 编译器进行保护。',1,3),
	(8,'SS服务','SS 加密服务中间件是深思安全体系的核心，是一个安装在软件运行计算机上的服务程序，主要作用有： \n1.	屏蔽硬件设备、云锁等底层细节，提供高级抽象的 API \n2.	提供安全通道、安全认证、反破解等服务',1,4),
	(9,'反黑驱动','传统的加密锁厂商，只提供了基础的被动防护功能，如果想要获得更高的安全强度，就需要开发商付出大量额外的工作。在提供上述完整保护功能的同时，和 SS 加密中间件一起，深思同时也提供了反黑驱动，将软件保护的思路，从 API 或外壳被动反调试、混淆，扩展到了全面、深入的主动/被动结合的思路。 \n与普通的反调试驱动不同，深思反黑驱动和深思许可 API、深思外壳保护工具、SS 加密中间有机结合，互相配合，形成一套完整的安全防护体系。反黑驱动在系统底层提供中间件保护、调试工具防护、APP 进程主动保护、内存保护等功能，是整个安全体系中难以绕过的一层坚固防护。',1,5),
	(10,'云锁','目前深思云平台授权可以支持不同的载体，并且体验一致。云锁为其中一种载体，云端虚拟精锐5，基于云锁和精锐5开发的加密方式可以无缝互换。 云锁使用账号登录，从而使用授权，类似QQ，这个时候客户必须实时联网。',1,6),
	(11,'如何开始体验深思云授权？','需要建立深思云账号后，登录账号才可以进入云平台体验深思云授权。登录深思官网，点击右上角的“云账号注册” 后建立账号。',3,1),
	(12,'如何开始加密流程？','简单流程就是下面几步： \n1.	第一步，创建产品,填写许可ID和产品名称。\n 2.	第二步，创建模板，方便大批量发放授权。 \n3.	第三步，下载SDK和外壳工具加密APP。 \n4.	第四步，给最终用户发放授权（加密锁或云锁）。 \n5.	最后一步，用户登录账号开始使用APP。',3,2),
	(13,'如何添加产品并定义许可模板？','添加产品 \n整个加密体系中需要先“创建产品”来定义产品，其中最为关键的就是“产品编号”，系统中标识产品、及对应的许可都使用此编号。上面加密过程中绑定的许可ID也是这个编号。 创建产品时您还可以填入更多的信息，后续所有的操作都要以此为基础， 数据区只能通过许可 API 使用，需要和开发人员确定。 \n定义许可模板 \n添加产品后，您就可以为此产品添加许可模版了，模版的作用是方便发放许可、控制发放许可的范围，即后面的许可签发只能在模版的基础上操作，产品经理或销售经理只要关注许可模版即可。模板分为正式模板和试用模板，区分两者主要目标是为了用户免费试用 不需要经过开发商审批就能自动发送授权。\n具体步骤如下： \n•	创建模板，我们先选正式模板； \n•	填入相关的许可信息即可； \n•	创建用户，云许可需要针对账号发放许可，请先创建账号。',3,3),
	(14,'如何使用许可？','至此，用户锁已经有了相应的合法许可，插入这把用户锁，加密的程序即可以正常进行，否则无法执行。一个完整的保护、许可发布流程即告完成。按照销售策略的不同，可以针对这个产品创建不同的许可模块，以发布不同的许可来满足业务，更进一步，您还可以申请成为深思云开发者，直接发布云许可，给软件用户带来更好的体验，而在这个过程中，加密后的软件不需要任何改动。',3,4);

/*!40000 ALTER TABLE `cloud_params` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table clouds
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clouds`;

CREATE TABLE `clouds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `sort_num` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 列表 1下载',
  `download_ids` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `clouds` WRITE;
/*!40000 ALTER TABLE `clouds` DISABLE KEYS */;

INSERT INTO `clouds` (`id`, `name`, `sort_num`, `type`, `download_ids`)
VALUES
	(1,'名词解释',1,0,'4,8'),
	(2,'下载中心',3,1,'3'),
	(3,'快速入门',2,0,'');

/*!40000 ALTER TABLE `clouds` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company`;

CREATE TABLE `company` (
  `Fid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Fname` varchar(50) NOT NULL DEFAULT '',
  `Fdomain` varchar(100) NOT NULL DEFAULT '',
  `Fcreated_at` int(11) NOT NULL,
  `Fupdated_at` int(11) NOT NULL,
  PRIMARY KEY (`Fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;

INSERT INTO `company` (`Fid`, `Fname`, `Fdomain`, `Fcreated_at`, `Fupdated_at`)
VALUES
	(1,'深思数盾','sense.com',1458290310,1468685851);

/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table company_news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_news`;

CREATE TABLE `company_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `time` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `company_news` WRITE;
/*!40000 ALTER TABLE `company_news` DISABLE KEYS */;

INSERT INTO `company_news` (`id`, `title`, `time`, `content`, `sort_num`, `created_at`, `updated_at`)
VALUES
	(1,'3.21 新序幕拉开,深思与您不见不散!','2016-07-06','传统的软件许可证模式将成为过去式 , 将被授权模式所取代。 现茌 , 我们终于能大声的宣布 , 深思将带您进入软件版权保护3.0时代 !\n作为软件开发商的您 ,\n是否还茌为互联网商业模式的转变而感到日头疼 ?\n是否因为传统软件产品交付模式的漫长周期而导致了用户流失 ?\n是否为了招聘优秀的加密技术人才 , 却屡屡受困于水涨船高的人力资源成本 ?\n......\n您是否期盼着有一天这一切的难题都将迎刃而解 ?\n现在 , 您所期待的这一天就在眼前 !\n3目21日 , 访间深思数盾官网 , 深思与您相约开启软件版权保护行业新序幕 ! 届时参与线上有奖活动还有惊喜送给您哦 !',1,'2016-06-13 15:04:29','2016-07-08 14:14:38'),
	(2,'深思数盾加密锁产品通过CE和FCC认证','2016-07-16','近日，北京深思数盾科技有限公司（以下简称“深思数盾”）多款加密锁产品经过各项严格审查和测试，成功通过了欧盟CE认证及美国联邦通信委员会FCC认证。  \n\nCE是一种安全认证标志，是产品进入欧盟及欧洲贸易自由区国家市场的通行证，也是产品进入欧盟市场的必要条件。FCC认证也是众多无线电应用产品、通讯产品和数字产品进入美国市场的先决条件。这两个认证的通过不仅表示深思数盾产品已达到欧美发达国家的技术水平，更是深思数盾对加密锁用户的质量承诺。  \n\n此次CE认证和FCC认证的通过是对深思数盾多年来坚持科学管理、技术创新、品质至上的肯定，为进军国际市场的规模扩张奠定了良好的基础，也为产品的国际化贴上了标签。未来的深思数盾还将持续提高产品质量，大力开展研发创新，努力开拓国际市场，不断推动企业发展，为成为用户“可信赖的专业技术伙伴”而不断努力！',2,'2016-07-16 14:50:15','2016-07-16 14:50:15'),
	(3,'互联网+ 重新定义加密锁','2016-07-16','5月5日，中国软件版权保护行业领导者，北京深思数盾科技有限公司发布了新一代软件版权保护产品-精锐5。这款产品在行业内首次完全以“互联网+”模式开发，通过智能虚拟化、互联网化及服务化三大特性，为传统软件企业带来了前所未有的加密体验。\n\n深思数盾公司长期专注并服务于软件企业的版权保护，深刻理解软件企业当下所面临的关键问题：\n\n第一， 软件企业的重点是专注于自身业务的发展，对软件的加密保护技术并不擅长。即使花费了巨大的人力、财力及时间成本，但因为加密技术的专业性强、难度高，加密方案的最终效果却通常差强人意；\n\n第二， 互联网浪潮带来了整个产业的变革，促使企业从以“产品”为中心的模式向以“用户”为中心的模式全面转变，但传统软件企业如何向互联网服务化顺利转型、如何解决软件的版权保护和云授权之间的矛盾，还是一堆迷雾...\n\n深思数盾公司新一代软件保护产品精锐5，完美解决了软件企业的两大痛点：首先，智能虚拟化开发工具的使用，使软件企业能快捷高效的完成加密方案的开发，实现高水平的保护；其次，新产品能辅助软件企业向互联网服务化模式转型。相对于传统软件版权保护产品，精锐5由传统单一硬件型产品转向了“软件定义硬件”型产品，颠覆了传统单一硬件型软件版权保护产品的理念，重新定义了加密锁。\n\n业内人士认为，软件版权保护行业的确有待升级。该行业自十五年前起，就一直在按照深思数盾公司创建的第四代技术框架体系发展，多年来从未进行过核心技术的升级，大部分跟随者都只是在硬件参数、成本方面进行“比拼”。互联网时代的到来，将彻底改变传统的软件保护行业。软件版权保护行业的竞争，正在从冷冰冰的硬件竞争，向集“硬件、云、软件、服务”为一体的竞争模式转变，颠覆式的创新将重新构筑行业的竞争格局，使行业向更加良性化的道路发展。\n\n深思数盾公司总经理，孙吉平博士表示：“深思从1995年创立至今，始终坚持以客户为本、持续自主创新，一直引领了行业的技术发展方向。我们的上一代产品非常成功，以至于我们自己长期以来也难以真正超越和颠覆。云和移动互联网时代，给软件企业和我们都带来了新的历史机遇，面向用户的商业模式将彻底颠覆软件保护行业，精锐5就是因此而生。”\n\n从“定义者”到“重新定义者”，深思公司将迎来一个全新的时代。',3,'2016-07-16 14:51:42','2016-07-16 14:51:42'),
	(4,'关于“非正规渠道购买的深思产品无技术支持和售后服务”的通知','2016-07-16','近日，某些非正规渠道购买到的假冒的深思产品在使用时出现了问题。\n深思表示：\n\n每一把出自深思的锁都设置了独立的ID，深思为这些产品提供全面周到的技术支持和售后服务，而通过非正规途径购买的仿造或者假冒的深思产品没有独立的ID，这些仿造或者假冒的产品将得不到任何的技术支持和售后服务，且任何企业或者个人因为使用仿造或者假冒的产品所产生的一切损失与深思无关。如果您需要深思产品，请直接联系我们的客服人员咨询与购买。',4,'2016-07-16 14:52:18','2016-07-16 14:52:18'),
	(5,'热烈庆祝深思喜获北京市专利示范单位','2016-07-16','近日，北京深思洛克软件技术股份有限公司（以下简称“深思洛克”）被北京市知识产权局评为“北京市专利示范单位”。\n\n根据《北京市“十二五”时期知识产权（专利）事业发展规划》和《北京市企事业单位专利示范工作方案（试行）》（京知局【2006】106号）文件规定，深思洛克与国家核电、三一重机、金山软件、小米科技等共计40家企事业单位获批成为北京市第五批专利示范单位，热烈庆祝公司知识产权工作迈上一个新台阶。\n\n北京市知识产权局经过专利管理部门认真严格的综合评审，从全市数百家申报北京市专利示范单位的企业、高校、院所中认定为“北京市专利示范单位”。据悉，获得这一称号的企事业单位将获得对国内外专利申请费用给予资助等诸多优惠政策扶持。\n\n此次被评为“北京市专利示范单位”既是北京市各级知识产权行政部门对深思洛克知识产权工作的肯定，也是公司坚持走自主创新、发展自主知识产权道路的阶段性成果体现。会激励公司同仁在自主创新这条路上走的更加进取、坚定！\n\n关于认定北京市第五批专利示范单位的公示:\n\n <a href=\"http://www.bjipo.gov.cn/zwxx/zwgg/201301/t20130115_26894.html\" class=\"color-blue\">http://www.bjipo.gov.cn/zwxx/zwgg/201301/t20130115_26894.html</a>',5,'2016-07-16 14:53:25','2016-07-16 14:53:25'),
	(6,'深思喜获国家政府专利促进资金','2016-07-16','近日，北京深思洛克软件技术股份有限公司（以下简称“深思洛克”）申请的2011年度专利促进资金国际部分和2012年度专利促进资金国内部分专利均获得批复，全部获得了专项资金支持。\n\n据了解，此次审批的要求非常严格，但深思洛克还是凭借雄厚的技术实力和自主创新能力脱颖而出。从目前国家政策来看，政府对企业的知识产权工作是非常重视的，此次获得国家专利促进资金的支持体现了深思洛克在专利管理和专利工作方面的努力和付出并最终取得了优异的成绩。作为中关村科技型的企业，在目前的利好政策下，深思洛克还会继续增强企业的专利意识和专利管理能力，持续不断的在企业知识产权方面进行投入，同时国际市场正在逐步开拓中，国际知识产权的工作也在逐步提升，深思洛克的市场也将逐渐走向国际，在海外树立良好的企业形象。\n\n在政府的大力扶持下，我们将在强调专利数量的同时，强调专利质量，使知识产权工作为企业的不断发展保驾护航！',6,'2016-07-16 14:53:48','2016-07-16 14:53:48'),
	(7,'深思再次获得国家密码管理局商用密码产品生产定点单位资质','2016-07-16','近日，北京深思洛克软件技术股份有限公司（以下简称“深思洛克”）再添喜讯——深思洛克连续获得了国家密码管理局的批准，再次被指定为“商用密码产品生产定点单位”。\n\n依据《商用密码管理条例》中有关科研和生产管理的规定，商用密码产品由国家密码管理机构指定的单位生产。未经指定，任何单位或者个人不得生产商用密码产品。商用密码产品指定生产单位必须具有与生产商用密码产品相适应的技术力量以及确保商用密码产品质量的设备、生产工艺和质量保证体系。因此，国家密码管理机构对于申请商用密码产品定点生产单位的企业在技术、管理及生产规模等多方面都有严格的要求。\n\n“商用密码产品生产定点单位”资质的获得，这一方面标志着深思洛克在商用密码产品的研发能力、管理水平及生产能力得到国家密码管理机构认可，另一方面也充分显示了深思洛克在业内的雄厚实力及领先优势，将促进深思洛克快速发展，提高自主创新能力方面起到更大的推动作用，为深思洛克在商用密码领域实现跨越发展打下了坚实基础。',7,'2016-07-16 14:54:12','2016-07-16 14:54:12'),
	(8,'深思的发明项目获得巴黎国际发明展览会（列宾竞赛）铜奖','2016-07-16','2011年5月，北京深思洛克软件技术股份有限公司（以下简称“深思洛克”）参加了在法国巴黎举办的巴黎国际发明展览会（列宾竞赛），凭借在软件版权保护领域的发明专利在众多参展企业中脱颖而出获得铜奖。\n\n巴黎国际发明展览会创办于1901年，创始人是巴黎市前警察局长兼发明者列宾，因此该展览会也被称为“列宾竞赛”。它属于巴黎博览会的一部分，巴黎国际发明展览会是法国发明与制造者协会主办的综合性发明展览会，得到法国政府和巴黎市政府的支持，该展会在法国及国外声誉卓著。\n\n深思洛克作为在我国软件保护行业深耕十六年的企业，拥有高素质的管理和研发团队，具有明显的技术优势，在国内软件版权保护领域内一直处于领先地位。凭借着产品的创造性、新颖性和实用用，深思洛克在众多参选企业中脱颖而出获得铜奖。参加本次盛会拓展了深思洛克产品的国际市场，对于深思洛克的技术和产品进入欧洲市场，了解有关技术领域的最新国际动态，学习其他国家的创造与发明经验，提升公司的竞争优势具有重要意义。',8,'2016-07-16 14:54:37','2016-07-16 14:54:37'),
	(9,'深思喜获中关村企业信用等级Azc级','2016-07-16','近日，北京深思洛克软件技术股份有限公司（以下简称“深思洛克”）连续第六次通过中关村企业信用级别评定，并获得了Azc级。这是深思洛克继2010年获得BBBzc级别后的又一次提升。\n\n“中关村企业信用等级评级报告”参照国际惯例，采用新的三等九级标准。获得Azc级别意味着企业的产品及技术在同行业具有先进性特点和较强的竞争力；具备相对完善的现代企业制度；拥有一定的资产规模；业务持续快速发展，盈利能力较强，财务结构稳健；享有一定的社会声誉；偿债能力较强；企业经营处于良性循环状态。\n\n本次中关村企业信用评级，如实评价了我公司的经营状况、财务状况和管理水平，反映出深思洛克综合实力的不断提升，也体现了社会对我公司业界良好信用的认可!',9,'2016-07-16 14:54:56','2016-07-16 14:54:56'),
	(10,'深思项目列入《国家高新技术产业发展项目计划》，并获得专项扶持资金','2016-07-16','根据发改办高技[2009]1886号文件《国家发展改革委员会办公厅关于2009年信息安全产品产业化专项项目的复函》，深思洛克的“多应用软件版权保护系统产业化项目”已成功列入“2009年国家高技术产业发展项目计划”中的信息安全专项，并获得国家专项扶持资金800万元。这是我司继2007年获得国家科技部“技术创新基金”支持、2008年获得“中关村最具发展潜力十佳中小高新技术企业”之后，又一次获得具有里程碑意义的殊荣。\n\n“国家高技术产业发展项目计划”是国民经济和社会发展计划的重要组成部分，是在国家发改委、财政部等部门的支持下，组织动员全社会共同参与的、旨在推动高技术产业发展、促进科技成果产业化的一项重要计划。该计划对项目申报企业的规模、技术开发和项目实施能力、企业资信等级等都有极其严格的要求。而加入该计划的项目将在科技研发、资金支持等方面得到国家的大力支持。我司项目的顺利获批，标志着深思洛克具备了承担国家重大科技项目的能力。\n\n“多应用软件版权保护系统产业化项目”作为信息安全产品产业化专项，旨在改革信息安全行业传统经营模式，探索开创一条直接面对消费者，利用成熟、可靠、先进的智能卡和授权管理技术，基于互联网创建“一Key通共享认证/加密服务平台”，提供软件加密保护/网络高安全性授权认证服务的全新运营模式。该项目的实施将有利于整合行业各方面资源，形成一个产业联盟，实现重新激活潜力巨大的大众通用软件市场的目标。\n\n北京深思洛克软件技术股份有限公司作为在我国软件保护行业深耕15年的企业，拥有高素质的管理和研发团队，具有明显的技术优势，在国内软件版权保护业内一直处于领先地位。依托政府项目资金支持，该项目的产业化不仅有助于进一步提升企业的核心竞争力，更重要的是它将对我国整体信息产业起到重要的带动示范作用。',10,'2016-07-16 14:55:23','2016-07-16 14:55:23'),
	(11,'用友续签深思洛克，精锐护航软件保护','2016-07-16','近日，深思洛克数据保护中心传来喜讯。基于以前的良好合作，用友软件股份有限公司与深思洛克续约。作为用户“可信赖的技术合作伙伴”，深思洛克将秉承对客户负责就是对自己负责的信念，继续为客户提供优质的产品和全方位的服务。\n\n用友软件做为亚太地区最大的管理软件销售及开发商，自1997年始，就与深思洛克就开展了合作关系。用友公司在国内软件产业的领头形象，也为此受到了业界的高度关注。有效地保证其经济利益，避免遭受盗版的侵蚀也成为重要的工作而受到各级领导的重视。深思洛克始终走在技术的前端为用友公司不断地提供适时先进的加密产品长达十年之久，使得用友公司的利益得到了极大的保护，有效地降低了盗版软件所带来的危害。作为用友可信赖的技术合作伙伴，除了在版权保护上给与用友支持以外，深思洛克还支持用友打击盗版的其他工作。而且，深思洛克开发每款新品均会为用友提供最合适的转换方案，有效的降低了用友公司的维护成本，便于用友使用更好的产品保护软件。\n\n我们都希望在未来的时间里，用友公司与深思洛克的这种友好的合作将会继续延伸下去，一起携手共赢！',11,'2016-07-16 14:55:51','2016-07-16 14:55:51');

/*!40000 ALTER TABLE `company_news` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table company_privilege_relation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_privilege_relation`;

CREATE TABLE `company_privilege_relation` (
  `Fcid` varchar(255) NOT NULL COMMENT '企业id',
  `Fpid` int(11) unsigned NOT NULL COMMENT '权限id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `company_privilege_relation` WRITE;
/*!40000 ALTER TABLE `company_privilege_relation` DISABLE KEYS */;

INSERT INTO `company_privilege_relation` (`Fcid`, `Fpid`)
VALUES
	('',23),
	('',23),
	('1',76),
	('',79),
	('',72),
	('1',79),
	('1',87),
	('1',86),
	('1',85),
	('1',75),
	('1',89),
	('1',95),
	('1',93),
	('1',92),
	('1',91),
	('1',77),
	('1',34),
	('1',33),
	('1',94),
	('1',90),
	('1',83),
	('1',82),
	('1',81),
	('1',43);

/*!40000 ALTER TABLE `company_privilege_relation` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table downloads
# ------------------------------------------------------------

DROP TABLE IF EXISTS `downloads`;

CREATE TABLE `downloads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lock_type_id` int(11) DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `btn_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `downloads` WRITE;
/*!40000 ALTER TABLE `downloads` DISABLE KEYS */;

INSERT INTO `downloads` (`id`, `lock_type_id`, `title`, `content`, `url`, `btn_name`, `created_at`, `updated_at`)
VALUES
	(3,2,'标准版/网络版产品所有资源','SS客户端安装包（sense_shield_installer_pub.exe）BuildVersion 8915 是深思精锐5加密锁客户端程序，主要包括：精锐5产品驱动程序、网络锁驱动程序、SS服务主程序、许可管理工具、界面服务程序及CSP模块。','http://115.29.189.225/Files/V5-Setup.zip','点击下载','2016-07-10 15:59:29','2016-07-16 08:51:27'),
	(4,2,'蓝牙版产品所有资源','文件内容包括：适用于Windows系统的开发包、手机Android系统及IOS系统的三种类型开发包、相关工具及说明文档。','http://115.29.189.225/Files/V5-SDK.zip','点击下载','2016-07-10 16:00:28','2016-07-16 08:52:02'),
	(5,3,'标准版/精灵版/U盘版产品所有资源','SDK下载文件内容包括：快速入门、开发指南、函数参考、Delphi应用范例集及发行说明文档。 请点击“SDK下载”，选择“目标另存为”或者使用下载软件下载至本地文件夹，解压缩后即可，使用前请先仔细阅读说明文档。','http://115.29.189.225/Files/V4-v3.4-Res.zip','SDK下载','2016-07-10 16:02:03','2016-07-16 15:42:00'),
	(6,3,'标准版/精灵版/U盘版产品所有文档','资源包文件内容包括：快速入门、开发指南、函数参考、Delphi应用范例集及发行说明文档。 请点击“资源包”，选择“目标另存为”或者使用下载软件下载至本地文件夹，解压缩后即可，使用前请先仔细阅读说明文档。','http://115.29.189.225/Files/V4-v3.4-Doc.zip','资源包','2016-07-10 16:02:27','2016-07-16 15:42:32'),
	(7,3,'网络版所有资源','SDK下载文件内容包括：网络版驱动程序、精锐系列驱动安装包、开发包、开发测试工具、服务管理工具、系统诊断工具、硬件测试工具、用户测试工具及各类说明文档。','http://115.29.189.225/Files/V4Net-v3.4-Res.zip','SDK下载','2016-07-10 16:03:01','2016-07-16 15:41:19'),
	(8,3,'网络版产品所有文档','资源包内容包括：快速开发指南、发行说明。','http://115.29.189.225/Files/V4Net-v3.4-Doc.zip','资源包','2016-07-10 16:03:24','2016-07-16 15:42:22'),
	(9,4,'产品所有资源','SDK下载文件内容包括：驱动安装包、开发包、U盘锁开发包、开发测试工具、设备检测工具、及各类说明文档。 请点击“SDK下载”，选择“目标另存为”或者使用下载软件下载至本地文件夹，解压缩后即可，使用前请先仔细阅读说明文档。','http://115.29.189.225/Files/V1-v1.4-Res.zip','资源包','2016-07-10 16:03:58','2016-07-16 15:42:43'),
	(10,4,'产品所有文档','文档内容包括：快速入门、开发指南、函数参考、Delphi应用范例集及发行说明文档。','http://115.29.189.225/Files/V1-v1.4-Doc.zip','SDK下载','2016-07-10 16:04:21','2016-07-16 15:42:13'),
	(11,3,'时钟版所有资源','SDK下载文件内容包括：时钟版驱动程序、精锐系列驱动安装包、开发包、开发测试工具、服务管理工具、系统诊断工具、硬件测试工具、用户测试工具及各类说明文档。','http://115.29.189.225/Files/V4-v3.4-Res.zip','SDK下载','2016-07-16 15:39:14','2016-07-16 15:40:36'),
	(12,3,'时钟版产品所有文档','资源包内容包括：快速开发指南、发行说明。','http://115.29.189.225/Files/V4-v3.4-Doc.zip','资源包','2016-07-16 15:40:24','2016-07-16 15:40:24'),
	(13,0,'Virbox Protector文档下载','Virbox Protector文档下载','/file/2016/07/16/CR-f1drGFSrYQKBXm3.zip','Virbox Protector文档下载','2016-07-16 19:09:10','2016-07-16 19:09:10');

/*!40000 ALTER TABLE `downloads` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table lock_params
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lock_params`;

CREATE TABLE `lock_params` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lock_id` int(11) NOT NULL,
  `param_1` varchar(200) NOT NULL DEFAULT '',
  `param_2` varchar(200) NOT NULL DEFAULT '',
  `param_3` varchar(200) NOT NULL DEFAULT '',
  `sort_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `lock_params` WRITE;
/*!40000 ALTER TABLE `lock_params` DISABLE KEYS */;

INSERT INTO `lock_params` (`id`, `lock_id`, `param_1`, `param_2`, `param_3`, `sort_num`)
VALUES
	(4,2,'芯片安全等级','CC EAL5+','原封全进口智能卡芯片',1),
	(6,2,'USB','USB 2.0全速','',3),
	(7,2,'CPU','32位 ARM','',2),
	(8,2,'扇区内擦写次数','＞1650万次','',4),
	(9,2,'最低擦写次数','50万次','',5),
	(10,2,'数据存储寿命','＞10年','',6),
	(11,2,'USB通信速度','＞300 KB/秒','',7),
	(12,2,'密码算法','RSA2048,ECC256,AES256,SHA1','',8),
	(13,2,'最大功率','100 mw','',9),
	(14,2,'工作温度范围','-15 ~ 80 ℃','',10),
	(15,2,'工作电压','4.5 ~ 5.5V','',11),
	(16,2,'高压保护','最高可耐受20V输入电压','',12),
	(17,2,'驱动程序','有驱/无驱','',13),
	(18,2,'最大授权数','6000','不同版本',14),
	(19,2,'Windows','Windows XP以上','',15),
	(23,3,'芯片类型','32位智能卡','',1),
	(24,3,'存储容量','256K','',2),
	(25,3,'可擦写次数','50万','',3),
	(26,3,'数据存储年限','10年','',4),
	(27,3,'接口类型','BLE/USB','',5),
	(28,3,'BLE模式待机时间','30天','',6),
	(29,3,'BLE模式连续工作时间','8小时','',7),
	(30,3,'安全算法','RSA、ECC、AES、SHA1、SHA256','',8),
	(31,3,'芯片安全等级','CC EAL5+','',9),
	(32,3,'操作系统','Android 4.3以上(BLE)、iOS 6以上(BLE)、Windows XP以上 (USB)','',10),
	(33,3,'硬件平台','安卓手机(带有蓝牙4.0硬件支持)、苹果手机(iPhone 4S以后)、x86兼容机，带有USB接口','',11),
	(34,3,'工作电压','5V','',12),
	(35,3,'工作温度','0-70℃','',13),
	(36,3,'存储温度','-20-85℃','',14),
	(37,4,'芯片安全等级','CC EAL5+','进口智能卡芯片',1),
	(38,4,'CPU','32位 ARM','',2),
	(39,4,'USB','USB 2.0全速','',3),
	(40,4,'最低可擦写次数','50万次','',4),
	(41,4,'数据存储寿命','10年','',5),
	(42,4,'USB通信速度','>120 KB/秒','',6),
	(43,4,'密码算法','RSA1024,TDES','',7),
	(44,4,'最大功率','100 mw','',8),
	(45,4,'工作温度范围','-15 ~ 75 ℃','',9),
	(46,4,'工作电压','4.5 ~ 5.5V','',10),
	(47,4,'驱动程序','有驱','',11),
	(48,4,'Windows','Windows XP以上','',12),
	(49,4,'Linux','2.4，2.6，3.0','',13),
	(50,4,'Mac OS','10.5以上','',14),
	(51,5,'芯片安全等级','CC EAL5+','进口智能卡芯片',1),
	(52,5,'CPU','32位 ARM','',2),
	(53,5,'USB','USB 2.0全速','',3),
	(54,5,'最低可擦写次数','50万次','',4),
	(55,5,'数据存储寿命','10年','',5),
	(56,5,'USB通信速度','>120 KB/秒','',6),
	(57,5,'密码算法','RSA1024,TDES','',7),
	(58,5,'最大功率','100 mw','',8),
	(59,5,'工作温度范围','-15 ~ 75 ℃','',9),
	(60,5,'工作电压','4.5 ~ 5.5V','',10),
	(61,5,'驱动程序','有驱','',11),
	(62,5,'Windows','Windows XP以上','',12),
	(63,5,'Linux','2.4，2.6，3.0','',13),
	(64,5,'Mac OS','10.5以上','',14),
	(65,6,'芯片安全等级','CC EAL5+','进口智能卡芯片',1),
	(66,6,'CPU','32位 ARM','',2),
	(67,6,'USB','USB 2.0全速','',3),
	(68,6,'最低可擦写次数','50万次','',4),
	(69,6,'数据存储寿命','10年','',5),
	(70,6,'USB通信速度','>120 KB/秒','',6),
	(71,6,'密码算法','RSA1024,TDES','',7),
	(72,6,'最大功率','100 mw','',8),
	(73,6,'工作温度范围','-15 ~ 75 ℃','',9),
	(74,6,'工作电压','4.5 ~ 5.5V','',10),
	(75,6,'驱动程序','有驱','',11),
	(76,6,'网络节点数','≤255','',12),
	(77,6,'Windows','Windows XP以上','',13),
	(78,6,'Linux','2.4，2.6，3.0','',14),
	(79,6,'Mac OS','10.5以上','',15),
	(80,7,'电池类型','锂电池，不可充电','',1),
	(81,7,'时钟寿命','3年以上','若长期插在USB口上，会减缓电池的损耗',2),
	(82,7,'误差范围','0.5小时/年','一般使用情况，每天使用50次',3),
	(83,8,'芯片类型','单片机','',1),
	(84,8,'安全算法','AES、HMAC-SHA1','',2),
	(85,8,'容量规格','8576字节','',3),
	(86,8,'可擦写次数','≥10万次','',4),
	(87,8,'数据存储年限','10年','',5),
	(88,8,'驱动类型','免驱','',6),
	(89,8,'USB','全速，向下兼容','',7),
	(90,8,'开发语言','VC++、C++ Builder、BC、C#、Java、Delphi、VB、PB、AutoCAD......','',8),
	(91,8,'操作系统','WIN8/7/Vista/2012/ 10/XP MacOS（32）/MacOS（64）','',9);

/*!40000 ALTER TABLE `lock_params` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table lock_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lock_types`;

CREATE TABLE `lock_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '产品名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '产品标题',
  `content` text NOT NULL COMMENT '产品描述',
  `img` varchar(200) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `lock_types` WRITE;
/*!40000 ALTER TABLE `lock_types` DISABLE KEYS */;

INSERT INTO `lock_types` (`id`, `name`, `title`, `content`, `img`, `created_at`, `updated_at`)
VALUES
	(2,'精锐5','软件版权保护旗舰产品','• 支持高于6000个独立的软件授权，最高提供5l2K字节的存储空间； \n• 软件授权可在硬件锁、云锁之间转移，互联网化的极致体验； \n• 提供Virbox Protector虚拟化工具，快速实现软件高强度加密保护； \n• 支持碎片代码执行，加密实施更加便捷； \n• 硬件支持标准C语言编程，代码移植； \n• 提供芯片级安全保护； \n• 网络单机一体，避免重复投入； \n• 全球顶级32位智能卡芯片，原封进口。','/image/2016/07/16/CR-hsMI6dbelbdxFCB.jpg','2016-07-10 14:09:01','2016-07-16 08:45:05'),
	(3,'精锐4S','经典产品精锐4升级版','• 业界领先的精锐4加密锁的全新换代产品，品质全面提升； \n• 硬件支持标准C语言编程，代码移植，提供芯片级安全保护； \n• CPU运算性能高达50MIPS（每秒处理的百万级的机器语言指令数）； \n• USB通信升级到2.0全速，通信能力提升50倍； \n• 进口高端智能卡芯片； \n• 完全兼容精锐4。','/image/2016/07/16/CR-cMgg2iCEoK3oZb7.jpg','2016-07-10 14:10:39','2016-07-16 08:44:46'),
	(4,'灵锐1','入门级硬件锁成本优选','• 低成本、高质量、简单易用； \n• USB2.0全速； \n• 进口通用MCU芯片，可靠性高。','/image/2016/07/16/CR-bCzYyeMUlHhWitt.jpg','2016-07-10 14:11:34','2016-07-16 08:44:31');

/*!40000 ALTER TABLE `lock_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `locks`;

CREATE TABLE `locks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lock_type_id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '版本名称',
  `try_status` tinyint(4) NOT NULL COMMENT '0 关闭 1开启',
  `pic` varchar(100) NOT NULL DEFAULT '' COMMENT '产品图',
  `description` text NOT NULL COMMENT '产品信息',
  `feature` text NOT NULL COMMENT '产品特点',
  `download_ids` varchar(100) NOT NULL DEFAULT '' COMMENT '下载资源ids',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `locks` WRITE;
/*!40000 ALTER TABLE `locks` DISABLE KEYS */;

INSERT INTO `locks` (`id`, `lock_type_id`, `version`, `try_status`, `pic`, `description`, `feature`, `download_ids`, `created_at`, `updated_at`)
VALUES
	(2,2,'标准版',1,'/image/2016/07/16/CR-ZMtBu7z7FTI3u0i.png','秉承软件保护行业最先进的硬件技术和设计理念，精锐5是软件企业互联网化过程中保护软件授权的不二之选。 \n\n精锐5与深思云锁在软件保护功能方面100%兼容，软件可以自由选择用精锐5还是深思云锁保护授权，并可以实现在运行时的无缝切换。授权也可以在精锐5和云锁之间转移，以满足用户的不同使用偏好。','全部激光打码，锁内外二码合一\n设备全球唯一序列号\nCC EAL5+安全认证芯片\n32位ARM核CPU\nUSB 2.0全速通信\n与深思云锁无缝兼容','3','2016-07-11 17:58:20','2016-07-17 20:57:19'),
	(3,2,'蓝牙版',0,'/image/2016/07/16/CR-Pf43Wd2Vn1Y8Sn5.png','蓝牙4.0增强安全通信协议，可用于移动平台的身份认证、数据加密等，待机时间长达30天以上。 \n\n通过数据线或者专用的蓝牙适配器与PC连接时，完全兼容USB版本精锐5的全部功能，可以直接当普通精锐5使用。','跨平台，Windows，IOS，Android随意切换；\n使用高强度的安全芯片作为主处理器；\n高安全的通信加密，保证用户数据安全；\n设备访问授权，防止设备被盗用；\n支持USB和蓝牙BLE双接口；\n低功耗，电量可查询，30天超长待机；','4','2016-07-16 15:11:13','2016-07-16 15:11:13'),
	(4,3,'标准版',0,'/image/2016/07/16/CR-fQSUSHQgUfdXJu9.png','精锐4S是加密锁经典产品——精锐4的全面升级版本。精锐4在过去近10年的时间中，真正能对抗解密者的疯狂破译，是一款经得起时间考验的优秀软件保护产品。它具有极高的安全性、稳定性和强大的网络安全功能，可以完成各种软件加密、数据保护和身份认证等任务。 \n\n在更换了更高档的智能卡芯片之后，精锐4S延续了精锐4的辉煌，与精锐4在功能上100%兼容，但性能更强、安全性更高、稳定性更好。','设备全球唯一序列号\nCC EAL5+安全认证芯片\n32位ARM核CPU\nUSB 2.0全速通信','5,6','2016-07-16 15:16:22','2016-07-16 15:16:37'),
	(5,3,'精灵版',0,'/image/2016/07/16/CR-14rcb7vzdXhU8EQ.jpg','精锐4S的“迷你”版本，外形小巧可爱，色彩艳丽时尚。适用于对加密强度、稳定性等方面有极高要求，且能接受较高加密成本的软件，特别适合在笔记本电脑上使用。','小巧便捷，适用于笔记本电脑。','5,6','2016-07-16 15:21:05','2016-07-16 15:21:05'),
	(6,3,'网络版',0,'/image/2016/07/16/CR-SQD5FtepzggThqw.png','硬件内置节点数管理模块，有效管理软件使用并发数量，安全可靠。适用于各种BS及CS结构下的网络软件，提供了简单实用的开发商工具，有较高的安全性和网络环境适应性。 \n\n精锐4S网络版支持“主－从”锁结构，每个主锁可以配套多个从锁进行使用，以提高可支持的并发节点数量。从锁离开主锁不能独立工作，以确保网络节点数的完整和安全。','小巧便捷，适用于笔记本电脑。','7,8','2016-07-16 15:32:08','2016-07-16 15:32:08'),
	(7,3,'时钟版',0,'/image/2016/07/16/CR-dzILxq7yr7fsffZ.png','在精锐4S的基础上，内置高精度时钟芯片和锂电池，可准确控制软件的授权时间。时间不可更改，不依赖于PC系统时间，具有更高的可靠性和安全性。 \n\n精锐4S标准版和精锐4S网络版，都提供时钟版型号。','硬件实时时钟，时间授权的最可靠选择。','11,12','2016-07-16 15:35:12','2016-07-16 15:43:15'),
	(8,4,'标准版',0,'/image/2016/07/16/CR-YUkn29DOeHVV0p2.jpg','● AES、HMAC-SHA1算法保护。 \n● 安全隧道技术。使用AES算法建立的安全通信隧道，并使用随机加扰技术，将设备与软件之间的通信数据全部隐藏起来，让破解者无法通过侦听获得有效信息。 \n● 提供8K超大使用空间。用户可以存储更多的数据，设计更为灵活的保护方案，也可以满足同时保护更多软件产品（模块）的需要。 \n● 执行速度快。由于执行速度快，用户可以设置更多、更复杂的加密点，从而增加解密者的破解难度。 \n● 免驱动安装。灵锐支持HID设备规范，在多数操作系统下不需特意安装驱动，具备兼容性好和使用方便的特点。 \n值得特别说明的是，灵锐1的加密开发工作非常简便，一般情况下您最快只需不到30分钟就可以完成加密开发工作。','支持128位AES高级加密算法，使用AES算法可以验证连接到计算机上的设备是否合法。\n\n安全隧道技术\n利用AES算法建立了与设备通信的“安全隧道”，并使用随机加扰措施，让破解者无法通过数据侦听来获得有效的信息，提高了软件的保护强度。\n\n硬件唯一序列号\n每个灵锐1设备在出厂时都被赋予了一个唯一的硬件序列号，利用此序列号可以实现软件与设备的唯一绑定或者实现产品的跟踪和追溯。','9,10','2016-07-16 15:47:58','2016-07-16 15:47:58');

/*!40000 ALTER TABLE `locks` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table menus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `menus`;

CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1一级菜单 2二级菜单',
  `btn_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 自定义跳转 1单页选择',
  `show_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 折叠 1伸展',
  `target` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `module` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 menu 1map',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;

INSERT INTO `menus` (`id`, `name`, `url`, `page_id`, `type`, `btn_type`, `show_type`, `target`, `parent_id`, `sort_num`, `module`)
VALUES
	(18,'云授权','',0,1,0,1,'_self',0,1,0),
	(19,'云授权平台','',0,0,0,0,'_self',18,1,0),
	(20,'专业工具','',0,0,0,0,'_self',18,2,0),
	(21,'硬件加密锁','',0,0,0,0,'_self',18,3,0),
	(22,'云授权平台','',0,2,0,0,'_self',19,5,0),
	(23,'了解云授权平台','',8,0,1,0,'_self',22,1,0),
	(24,'云锁服务','',9,0,1,0,'_self',22,2,0),
	(25,'SS安全服务','',10,0,1,0,'_self',22,3,0),
	(26,'专业工具','',0,2,0,0,'_self',20,6,0),
	(27,'Virbox Protector','',11,0,1,0,'_self',26,1,0),
	(28,'开发商工具','',12,0,1,0,'_self',26,2,0),
	(29,'许可管理工具','',13,0,1,0,'_self',26,3,0),
	(30,'硬件加密锁','',0,2,0,0,'_self',21,7,0),
	(31,'精锐5','',15,0,1,0,'_self',30,1,0),
	(32,'云授权平台','',0,1,0,0,'_self',0,1,1),
	(33,'了解云授权','',8,0,1,0,'_self',32,1,1),
	(34,'云锁服务','',9,0,1,0,'_self',32,2,1),
	(35,'SS安全服务','',10,0,1,0,'_self',32,3,1),
	(36,'云帐号注册','http://developer.senseshield.com/auth/register.jsp',0,0,0,0,'_blank',32,4,1),
	(37,'专业工具','',0,1,0,0,'_self',0,2,1),
	(38,'Virbox Protector','',11,0,1,0,'_self',37,1,1),
	(39,'开发商工具','',12,0,1,0,'_self',37,2,1),
	(40,'许可管理工具','',13,0,1,0,'_self',37,3,1),
	(41,'硬件加密锁','',14,1,1,0,'_self',0,3,1),
	(42,'精锐5','',15,0,1,0,'_self',41,1,1),
	(43,'精锐4S','',16,0,1,0,'_self',41,2,1),
	(44,'灵锐1','',17,0,1,0,'_self',41,3,1),
	(45,'解决方案','',19,1,1,0,'_self',0,4,1),
	(46,'游戏行业','',20,0,1,0,'_self',45,1,1),
	(47,'管理行业','',21,0,1,0,'_self',45,2,1),
	(48,'建筑行业','',22,0,1,0,'_self',45,3,1),
	(49,'教育和文档','',23,0,1,0,'_self',45,4,1),
	(50,'通用行业','',24,0,1,0,'_self',45,5,1),
	(51,'资源','',0,1,0,0,'_self',0,5,1),
	(52,'下载中心','',28,0,1,0,'_self',51,1,1),
	(53,'建议反馈','',29,0,1,0,'_self',51,2,1),
	(54,'联系我们','',30,0,1,0,'_self',51,3,1),
	(55,'我们','',0,1,0,0,'_self',0,6,1),
	(56,'公司简介','',36,0,1,0,'_self',55,1,1),
	(57,'公司新闻','',31,0,1,0,'_self',55,2,1),
	(58,'成长历程','',33,0,1,0,'_self',55,3,1),
	(59,'知识产权','',34,0,1,0,'_self',55,4,1),
	(60,'公司资质','',35,0,1,0,'_self',55,5,1),
	(61,'诚聘精英','',32,0,1,0,'_self',55,6,1),
	(62,'加密锁','',14,1,1,0,'_self',0,2,0),
	(63,'精锐5','',15,0,1,0,'_self',62,1,0),
	(64,'精锐4S','',16,0,1,0,'_self',62,2,0),
	(65,'灵锐1','',17,0,1,0,'_self',62,3,0),
	(66,'资源','',0,1,0,0,'_self',0,3,0),
	(67,'云授权','',18,0,1,0,'_self',66,1,0),
	(68,'精锐5','',25,0,1,0,'_self',66,2,0),
	(69,'解决方案','',19,0,1,0,'_self',66,3,0),
	(70,'精锐4S','',26,0,1,0,'_self',66,4,0),
	(71,'灵锐1','',27,0,1,0,'_self',66,5,0),
	(72,'我们','',0,1,0,0,'_self',0,4,0),
	(73,'公司简介','',36,0,1,0,'_self',72,1,0),
	(74,'公司新闻','',31,0,1,0,'_self',72,2,0),
	(75,'成长历程','',33,0,1,0,'_self',72,3,0),
	(76,'知识产权','',34,0,1,0,'_self',72,4,0),
	(77,'公司资质','',35,0,1,0,'_self',72,5,0),
	(78,'诚聘精英','',32,0,1,0,'_self',72,6,0),
	(79,'解决方案','',19,2,1,0,'_self',69,8,0),
	(80,'游戏行业','',20,0,1,0,'_self',79,1,0),
	(81,'管理行业','',21,0,1,0,'_self',79,2,0),
	(82,'建筑行业','',22,0,1,0,'_self',79,3,0),
	(83,'教育和文档','',23,0,1,0,'_self',79,4,0),
	(84,'通用行业','',24,0,1,0,'_self',79,5,0);

/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table page_contents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_contents`;

CREATE TABLE `page_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL COMMENT '页面id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '主标题',
  `sub_title` varchar(200) NOT NULL DEFAULT '' COMMENT '副标题',
  `content` text NOT NULL COMMENT '内容',
  `pic` varchar(200) NOT NULL DEFAULT '' COMMENT '图文列表图',
  `position` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 图片居右 1图片左',
  `icon` varchar(200) NOT NULL DEFAULT '' COMMENT '首页icon',
  `icon_active` varchar(200) NOT NULL DEFAULT '' COMMENT '点击状态',
  `sort_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `page_contents` WRITE;
/*!40000 ALTER TABLE `page_contents` DISABLE KEYS */;

INSERT INTO `page_contents` (`id`, `page_id`, `title`, `sub_title`, `content`, `pic`, `position`, `icon`, `icon_active`, `sort_num`)
VALUES
	(27,0,'授权管理','依靠行业领先的、安全灵活的授权管理程序，更快发展软件入云业务。','你所发行的任何软件，用户都可以实现即付即用或全功能的免费试用。通过云授权服务，你发行给用户的软件授权全部都能即时生效。\n用户可以获得全程无障碍体验。是否选用加密锁，由用户决定，版权保护实现“零”成本。','/image/2016/07/16/CR-5EIxbI1IRHUje5C.png',0,'/image/2016/07/16/CR-r22lr6cmZNO4sRP.png','/image/2016/07/16/CR-Bv1WGfv1zEJrXNi.png',1),
	(28,0,'软件加密','在数分钟内，使你的软件获得顶级的加密保护。','全自动的加密工具，内含世界一流的安全虚拟机引擎。通过深思专有的自动代码移植技术，使你的软件拥有最强大的抗破解能力。 不论使用云锁还是硬件加密锁，体验都是一致的，只需要加密一次。','/image/2016/07/16/CR-NXFXxb13mqtD9hq.png',0,'/image/2016/07/16/CR-zqAaC0QjfpAo8tV.png','/image/2016/07/16/CR-GMxEAEUU5NO62vv.png',2),
	(29,0,'统计分析','准确、即时跟踪软件模块的使用情况， 了解用户使用习惯和偏好。','云授权跟踪服务，可以准确跟踪你授权的每个软件模块的实际使用情况，包括使用频率、地域等。 所获取的数据仅涉及授权本身，不会触及任何用户的数据。 \n同时，授权的使用信息也是加密的，只有你所在的组织才能查阅。','/image/2016/07/16/CR-8HA1hVNJEwARsle.jpg',0,'/image/2016/07/16/CR-q0YuPUf03OdFkEF.png','/image/2016/07/16/CR-4FVtuuPZ1L5gHg4.png',3),
	(30,0,'身份认证','保护用户的云帐号安全。','每个用户所持有的精锐5加密锁，都可以提供不亚于网上银行安全等级的身份认证能力。因此，你为用户提供的云服务，例如基于云的业务系统，可以利用加密锁原生的身份认证功能，最大程度保护用户帐号的安全性。 \n每个精锐5出厂时都带有标识唯一身份的密钥和对应的数字证书，你可以直接使用它完成云帐号的身份认证，最大程度简化了身份认证系统的部署难度。','/image/2016/07/16/CR-FZ5G6opUA1BggTE.png',0,'/image/2016/07/16/CR-4AVlgcI1dx1qBbw.png','/image/2016/07/16/CR-u2xzvnJTgpfivjo.png',4),
	(31,8,'','什么是云授权平台','基于云的软件授权管理系统，给任意用户发行软件授权并管理其完整生命周期。用户可以在任何时间、任何地点、任何设备上使用已经获得的软件授权，而不再像过去一样只能将软件安装到特定的计算机上。 \n在云授权平台中，软件授权是用户的“资产”。用户既可以在云端使用，也可以将授权下载到自己的加密锁中以获得更好的体验。这一切都是安全的，无需担心授权被非法复制、滥用或越界，也不再用担心因授权丢失而导致的复杂处理过程。','/image/2016/07/16/CR-DJtVMyxKPv48pyA.jpg',0,'','',1),
	(32,8,'','','过去，做软件不用加密锁，就会被盗。\n用了加密锁，能解决盗版，可是也带来一堆伤脑筋的问题：\n■ 加密锁采购需要成本。\n■ 需要专人管理加密锁：采购、出入库、生产设置…\n■ 发货周期长，用户等得心烦不说，还得负担越来越高的快递费。\n■ 加密是个技术活儿，没有专业人才也没法儿跟黑客持续对抗。','/image/2016/07/16/CR-e7OaTkYEKoVMEgu.jpg',0,'','',2),
	(33,8,'','','现在，有了深思云授权平台，一切都迎刃而解。\n<img class=\"snphc-img\" src=\"/images/platform/plat_2_txt.jpg\">\n软件可以快速交付给用户，再也不用管加密锁的事儿了！\n加密安全交给深思负责，他们有一整套专业的安全软件和服务。','/image/2016/07/16/CR-LfHlHQ8vPg4DlGp.jpg',0,'','',3),
	(34,8,'','','除了省心、省力、省钱，还有意想不到的好处：\n强大的授权管理功能，什么试用、租用、按需付费、弹性授权等，瞬间解决。\n哪些功能用的多，哪些用的少，用户地理分布的情况，全盘掌握。\n软件直接授权给用户的账号，爱在哪儿用就在哪儿用，随心所欲。','/image/2016/07/16/CR-GT6Q1dnbLIZHs4m.jpg',0,'','',4),
	(35,8,'','','要是用户自己想要个加密锁，方便管理和提升体验怎么办？\n用户自己找深思买就得了，需要服务也找深思。\n已经发出的授权可以在云上用，也可以下载到加密锁里用。\n安全么？放心，比过去那样买了加密锁再交给客户，安全性只高不低。','/image/2016/07/16/CR-Td7OTLUC9qEJhht.jpg',0,'','',5),
	(36,8,'','成本与费用','单纯的云授权服务是免费的，例如开发者向用户发行在线的试用授权，双方都无需支付任何费用。 \n如果用户希望使用加密锁以获得更好的离线体验，因为加密锁是100％远程操作的，所以用户可以自己购买。 \n开发者再也不必负担加密锁购买成本、生产管理成本、快递成本、锁售后支持成本！','/image/2016/07/16/CR-mDuFpBjvi7wUY1I.jpg',0,'','',6),
	(37,8,'','如何使用','只需在深思云授权平台上注册开发者账号，并获得正式的开发者ID，就可以立即使用深思云授权平台所带来的一切便利。 \n完整的过程仅需要：加密、发布、授权三个步骤，可以100%通过工具实现，开发者甚至不需要为此编写任何一行代码！ \n我们也提供了完整的SDK和API接口供开发者使用，开发者可以借此满足自己更高级和个性化的需求。','/image/2016/07/16/CR-kPx4zFxDiwaueUO.jpg',0,'','',7),
	(38,9,'','','与精锐5硬件加密锁完全一致的加密服务，永久免费，无需担负加密成本！ 云锁本质上就是一个云端虚拟的精锐5，在功能上与精锐5完全兼容。开发者通过深思云锁开发的加密方案，可以无缝迁移到精锐5加密锁中，反之亦然。 如果用户选择将授权放在云端使用，那么就必须使用云锁进行加密，并且在使用软件的过程中需要保持客户端在线。 云锁通过硬件加密机来保护用户敏感数据的安全性，安全性显著高于普通解决方案，例如开发者自己开发的云授权锁定机制。 本质上，深思云锁是云授权平台的一部分，开发者所开发的软件不能直接访问云端，而必须经过本地的SS服务和授权管理服务，因此不需要关注云锁的太多细节。 开发者在使用Virbox Protector工具或者通过API方式加密完软件之后，必须指定是用加密锁、云锁亦或二者同时来保护软件。','',0,'','',1),
	(40,10,'','','SS即Security Service，是深思提供的一套专业级系统安全服务组件。它运行在客户端，充当整个云授权平台的枢纽，所有软件保护、授权管理的功能，都必需通过SS进行。','',0,'','',1),
	(41,10,'SS服务的主要功能包括：','反逆向保护','逆向分析是黑客破解软件的最主要手段，SS提供了多种有效的抗分析手段，例如Ring 0反调试、代码逻辑保护、调试器检测、调试反击等。','',0,'','',2),
	(42,10,'','加密敏感数据','使用SS之后，所有加密锁都原生可以作为网络锁提供服务。软件既可以访问本机上的加密锁，也可以访问网络上提供服务的其它加密锁，具体策略由开发者在发布软件的时候指定。 在使用单个精锐5的条件下，SS的网络服务可以提供高达1000次/分钟的服务能力。','',0,'','',3),
	(43,10,'','提供网络锁服务','作为云授权推送的客户端，SS服务可以把开发者发给用户的授权及时从云平台下载到加密锁内；可以及时发现被挂失或者被列入黑名单的加密锁，对其采取锁定措施；可以自动对加密锁内部的虚拟时钟进行时间校准，避免时钟的异常变化导致的软件运行问题等。','',0,'','',4),
	(44,10,'','管理加密锁','作为一项长期工作，深思会根据行业技术的发展以及破解技术的进步，持续不断对SS进行升级和维护，确保受保护的软件能够持久安全。','',0,'','',5),
	(45,11,'','VIRBOX PROTECTOR','Virbox Protector与深思云锁或精锐5配套使用，集自动代码移植、虚拟化、外壳加密、数据加密与一身，是业界领先的软件保护工具。Virbox Protector具有高度的灵活性和自动化能力，开发者只需要简单的操作，就可以使被保护的软件安全强度达到专业级水准。 \nVirbox Protector加密后的软件，开发者就可以公开发布，任何用户都可以下载。但是，在开发者使用授权管理工具为某个特定的用户或者组织发放授权之前，软件是不能被使用的。 \n开发者也可以为保护后的软件发放试用授权，允许用户在设定的条件下进行试用。','/image/2016/07/16/CR-LwH3nrCL6EwhUg9.jpg',1,'','',1),
	(46,11,'','试用版','任何开发者都可以在注册后试用Virbox Protector。但是，试用版本只是提供给开发者最基本的演示能力，与正式版相比，试用版的安全性要低很多。。 \n在任何时候都不能将Virbox Protector试用版加密后的软件正式发布，因为任何其他开发者都可以为试用版加密后的软件发布授权。Virbox Protector的试用版本仅用于测试目的，如果需要正式使用，就必须获得正式的开发者ID和正式版本的Virbox Protector。','/image/2016/07/16/CR-jzrHBKv1x3oiRWx.png',1,'','',2),
	(47,11,'','获得正式版','我们为每个正式的开发者提供专用的Virbox Protector版本，以确保每个加密方案都是独特的，这可以最大程度保护软件的安全性。 \n所以，绝对不要把你所获得的正式版Virbox Protector泄露出去，否则将大大不利于你的软件产品的版权保护。','/image/2016/07/16/CR-m7Dql5MUK8t2YLt.png',1,'','',3),
	(48,11,'','了解更多','Virbox Protector对于初学者来说非常简单和易用，几乎不需要特别的加密知识就可以将软件保护做好。对于专家而言，Virbox Protector也提供了强大的定制化能力。','/image/2016/07/16/CR-cQlZzwW32CZQ0ag.png',1,'','',4),
	(49,12,'','','开发商工具用于开发者发放软件授权，通过开发者ID保证授权的安全性。开发商工具非常强大和灵活，开发者可以使用它： \n\n• 将授权直接写入到插到本地计算机上的加密锁\n• 发布授权到深思云授权平台，用户可以立即在云端使用软件授权\n• 发布授权到云推送平台，快速将授权推送到用户的加密锁中\n• 查看所有用户软件授权的使用情况','',0,'','',1),
	(50,12,'获得正式版','','开发者必须获得正式的开发者ID，才能向深思申请正式的Virbox Protector版本。每个开发者所获得的Virbox Protector都经过个性化定制，以最大程度保护软件的安全性。','',0,'','',2),
	(51,12,'使用开发商工具','','开发商工具的离线版，也可以不依赖于深思云授权平台独立运行。对于一些特殊的开发者，例如保密单位，开发商工具可以将所发行的授权保存为“授权包”的形式。开发者可以用适宜的方式（例如邮件）将授权包发送给用户，用户再获得授权包之后，可以通过开发商工具完成授权的安装。','',0,'','',3),
	(52,13,'','','许可管理工具是提供给软件最终使用者的桌面端软件，帮助软件使用者管理自己的软件授权。','',0,'','',1),
	(53,13,'用户控制台','','通过用户控制台，用户可以管理自己的云授权平台账号、软件授权和维护自己的加密锁。这不仅给用户带来了方便性和更好的体验，同时也使得用户能够自己维护加密锁和授权，从而大量减少了软件开发者的维护成本。用户可以： \n\n查看软件授权情况，包括在云端使用的授权和下载到本地加密锁的授权\n管理加密锁，将加密锁注册到云授权平台账号，也可将丢失或者损坏的加密锁挂失\n在云端和加密锁之间转移授权\n一键维护，自动检查、修复和升级加密服务和加密锁驱动','',0,'','',2),
	(54,13,'关于一键维护','','一键维护是自动化智能维护系统，基于深思数盾多年来积累的“问题解决知识库”构建。正常情况下，一键维护能够修复大部分可能出现的故障。对于不能修复的问题，用户可以选择将问题报告发送给深思的维护团队，便于快速解决问题。','',0,'','',3),
	(55,36,'','','北京深思数盾科技股份有限公司注册成立于2013年3月，位于北京市海淀区，实际注册资本2000万，是一家专注于软件授权保护、互联网授权服务和安全产品开发的创新性科技企业。 \n深思的核心产品是“深思云授权平台”，目标是协助中国的软件企业向以用户为中心的互联网模式转变。在为软件企业和中小型软件开发团队带来更广阔的发展空间的同时，也为软件的使用者带来了更佳的使用体验。 \n深思的愿景是成为“企业数据财富的保护者”，在财富快速数字化的互联网时代，致力于保护企业客户的数据财富安全，特别是软件企业的软件产品以及各型企业在云端存储和使用的商业数据。2016年初深思完成了一次定向融资，开始进入新一轮的快速发展轨道。 \n深思在软件保护、互联网安全、硬件安全产品领域具有深厚的技术积累，2013年公司注册时，整合了具有近20年发展历史的深思洛克的全部软件加密锁业务和完整的技术团队。凭借卓越的技术和诚实守信的口碑，深思成为了用友、广联达、金蝶、北大方正、科大讯飞、H3C、和利时、海康威视等一大批国内软件行业领导者的长期合作伙伴，也为摩托罗拉、索尼、西门子、NEC等国际知名企业提供产品和服务。全球有超过5000家软件企业累计使用了超过1300万套深思产品，其中智能卡产品超过1000万套。','',0,'','',1),
	(56,36,'深思愿景','可信赖的专业技术伙伴','价值观： \n\n1.真心诚意尊重、善待每一个人，努力做得更好以对得起自己、同事和家人； \n2.正道经营； \n3.成就客户、成就自我； \n4.围绕客户的需求持续创新； \n5.永存变革之心，不断突破自我；\n 6.坚持奋斗精神，为奋斗者创造更好的发展空间，使奋斗者得合理回报。  \n\n使命： \n\n1.为中小企业提供最好的数据财富保护产品与服务，为中小企业的健康成长保驾护航； \n2.追求全体员工物质和精神两方面的幸福。  \n\n愿景 : \n\n中小企业数据财富的保护者','',0,'','',2);

/*!40000 ALTER TABLE `page_contents` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_links`;

CREATE TABLE `page_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_content_id` int(11) NOT NULL COMMENT '页面id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `target` varchar(20) NOT NULL DEFAULT '',
  `sort_num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `page_links` WRITE;
/*!40000 ALTER TABLE `page_links` DISABLE KEYS */;

INSERT INTO `page_links` (`id`, `page_content_id`, `name`, `url`, `target`, `sort_num`)
VALUES
	(11,27,'了解更多......','','_self',1),
	(12,28,'了解更多......','','_self',1),
	(13,37,'注册开发商帐号 》','http://developer.senseshield.com/auth/register.jsp','_blank',1),
	(14,38,'免费注册云帐号 》','http://developer.senseshield.com/auth/register.jsp','_blank',1),
	(15,44,'免费注册云帐号','http://developer.senseshield.com/auth/register.jsp','_blank',1),
	(16,47,'免费注册云帐号','http://developer.senseshield.com/auth/register.jsp','_blank',1),
	(17,48,'Virbox Protector文档下载 》','/file/2016/07/16/CR-f1drGFSrYQKBXm3.zip','_self',1),
	(18,50,'免费注册云帐号','http://developer.senseshield.com/auth/register.jsp','_blank',1);

/*!40000 ALTER TABLE `page_links` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table page_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_types`;

CREATE TABLE `page_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '页面模块名',
  `url` varchar(50) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 单页 1模块',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否需要 输入',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `page_types` WRITE;
/*!40000 ALTER TABLE `page_types` DISABLE KEYS */;

INSERT INTO `page_types` (`id`, `name`, `url`, `type`, `status`)
VALUES
	(1,'加密锁列表','/locks/',1,0),
	(2,'加密锁详情','/lock-detail/',1,1),
	(3,'解决方案列表','/solution/',1,0),
	(4,'解决方案详情','/solution-detail/',1,1),
	(5,'诚聘精英','/recruit/',1,0),
	(6,'公司新闻','/news/',1,0),
	(7,'建议反馈','/feedback/',1,0),
	(8,'联系我们','/contact/',1,0),
	(9,'资源云授权','/cloud/',1,0),
	(10,'成长历程','/route/',1,0),
	(11,'知识产权','/intellectual/',1,0),
	(12,'公司资质','/property/',1,0),
	(13,'下载中心','/download/',1,0),
	(14,'文件下载','/page/1/',0,1),
	(15,'文本列表','/page/2/',0,1),
	(16,'图文分离列表','/page/3/',0,1),
	(17,'图文混排','/page/4/',0,1);

/*!40000 ALTER TABLE `page_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '页面名称',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT 'title',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'keywords',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'description',
  `banner` varchar(255) NOT NULL DEFAULT '' COMMENT 'banner',
  `extra` text NOT NULL COMMENT '页头内容或下载资源ids',
  `url` varchar(255) NOT NULL DEFAULT '',
  `page_type_id` int(11) NOT NULL COMMENT '页面类型',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;

INSERT INTO `pages` (`id`, `name`, `title`, `keywords`, `description`, `banner`, `extra`, `url`, `page_type_id`, `created_at`, `updated_at`)
VALUES
	(8,'了解云授权平台','了解云授权平台','','','/image/2016/07/16/CR-iTquAPiL4JQU4EA.jpg','','',16,'2016-07-16 16:21:03','2016-07-16 16:21:03'),
	(9,'云锁服务','云锁服务','','','/image/2016/07/16/CR-FWcc3LTsQ3ZPmhU.jpg','/image/2016/07/16/CR-IojlvzRbZOru8CS.png','',17,'2016-07-16 16:33:53','2016-07-16 18:10:43'),
	(10,'SS安全服务','SS安全服务','','','/image/2016/07/16/CR-nyxXrz5DFMqmBOj.jpg','/image/2016/07/16/CR-5k9ifvDdY29xT14.png','',17,'2016-07-16 16:42:19','2016-07-16 18:12:59'),
	(11,'VIRBOX PROTECTOR','VIRBOX PROTECTOR','','','/image/2016/07/16/CR-jex2yLSadYoywSI.png','','',16,'2016-07-16 19:14:40','2016-07-16 19:14:40'),
	(12,'开发商工具','开发商工具','','','/image/2016/07/16/CR-kHZl6dZ7fFPRdPS.png','','',15,'2016-07-16 22:03:33','2016-07-16 22:03:33'),
	(13,'许可管理工具','许可管理工具','','','/image/2016/07/16/CR-TQq8evyoVX0eKtK.jpg','','',15,'2016-07-16 22:06:08','2016-07-16 22:06:08'),
	(14,'加密锁','加密锁','','','/image/2016/07/16/CR-whOdXNNn2ddFswm.jpg','','',1,'2016-07-16 22:07:56','2016-07-16 22:07:56'),
	(15,'精锐5','精锐5','','','/image/2016/07/16/CR-N8xHAvPLREsFLYX.jpg','2','',2,'2016-07-16 22:08:26','2016-07-16 22:08:26'),
	(16,'精锐4S','精锐4S','','','/image/2016/07/16/CR-aNwwIRx8JadRnyV.jpg','3','',2,'2016-07-16 22:08:55','2016-07-16 22:08:55'),
	(17,'灵锐1','灵锐1','','','/image/2016/07/16/CR-ogZowICB8kOhEUe.jpg','4','',2,'2016-07-16 22:09:21','2016-07-16 22:09:21'),
	(18,'云授权','云授权','','','/image/2016/07/16/CR-oo86cs8S8tL1NZY.jpg','','',9,'2016-07-16 22:10:49','2016-07-16 22:10:49'),
	(19,'解决方案','解决方案','','','/image/2016/07/16/CR-cun04y59aCGDLnf.jpg','','',3,'2016-07-16 22:12:27','2016-07-16 22:12:27'),
	(20,'游戏行业','游戏行业','','','/image/2016/07/16/CR-S6821SGC7YkbY6Z.jpg','3','',4,'2016-07-16 22:14:48','2016-07-16 22:14:48'),
	(21,'管理行业','管理行业','','','/image/2016/07/16/CR-jNuTGAKjbn1huHh.jpg','2','',4,'2016-07-16 22:15:26','2016-07-16 22:15:26'),
	(22,'建筑行业','建筑行业','','','/image/2016/07/16/CR-XJ2WBQ0NtsEC3sS.jpg','4','',4,'2016-07-16 22:15:54','2016-07-16 22:15:54'),
	(23,'教育和文档','教育和文档','','','/image/2016/07/16/CR-q7J3HWYUg9IydLm.jpg','5','',4,'2016-07-16 22:16:24','2016-07-16 22:16:24'),
	(24,'通用行业','通用行业','','','/image/2016/07/16/CR-N2T0dVuuSdiQcRY.jpg','6','',4,'2016-07-16 22:16:51','2016-07-16 22:16:51'),
	(25,'资源精锐5','精锐5','','','/image/2016/07/16/CR-Xuo2WftgLpbbBCb.jpg','3,4','',14,'2016-07-16 22:20:50','2016-07-16 22:20:50'),
	(26,'资源精锐4S','精锐4S','','','/image/2016/07/16/CR-IBStM8EIAFmYAFB.jpg','5,6,7,8','',14,'2016-07-16 22:21:52','2016-07-16 22:21:52'),
	(27,'资源灵锐1','灵锐1','','','/image/2016/07/16/CR-xwpUAZeOKC0W20E.jpg','9,10','',14,'2016-07-16 22:22:43','2016-07-16 22:22:43'),
	(28,'下载中心','下载中心','','','/image/2016/07/16/CR-haPr0Z9EVinmvHy.jpg','','',13,'2016-07-16 22:23:27','2016-07-16 22:23:27'),
	(29,'建议反馈','建议反馈','','','/image/2016/07/16/CR-GUt4hjg83eAPd0D.jpg','','',7,'2016-07-16 22:24:40','2016-07-16 22:24:40'),
	(30,'联系我们','联系我们','','','/image/2016/07/16/CR-A6sJrvFmHFkSqqR.jpg','','',8,'2016-07-16 22:25:03','2016-07-16 22:25:03'),
	(31,'公司新闻','公司新闻','','','/image/2016/07/16/CR-qsBpprxybBdXauc.jpg','','',6,'2016-07-16 22:27:11','2016-07-16 22:27:11'),
	(32,'诚聘精英','诚聘精英','','','/image/2016/07/16/CR-t2BbalizKgbLIXV.jpg','','',5,'2016-07-16 22:27:36','2016-07-16 22:27:36'),
	(33,'成长历程','成长历程','','','/image/2016/07/16/CR-eOwDQuaUAXhd7tO.jpg','','',10,'2016-07-16 22:28:18','2016-07-16 22:28:18'),
	(34,'知识产权','知识产权','','','/image/2016/07/16/CR-58n89W80atokfsU.jpg','北京深思数盾是北京市知识产权保护协会会员。掌握信息安全保护领域自有核心技术 , 专业提供软件版权保护产品及信息安全服务 , 具备软硬件自主研发、生产、销售于一体的高新技术企业。 自成立以来已经申请了200多项专利 , 其中已经有近100项已经获得了国家授权 , 多次获得北京市专利试点企业和专利促进资金支持。','',11,'2016-07-16 22:29:07','2016-07-16 22:29:07'),
	(35,'公司资质','公司资质','','','/image/2016/07/16/CR-TlXR1ZnAxE9cwNi.jpg','','',12,'2016-07-16 22:29:55','2016-07-16 22:29:55'),
	(36,'公司简介','公司简介','','','/image/2016/07/16/CR-rhTzLH1ZynNZxTv.jpg','','',15,'2016-07-16 22:39:45','2016-07-16 22:39:45');

/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table partners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `partners`;

CREATE TABLE `partners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `partners` WRITE;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;

INSERT INTO `partners` (`id`, `logo`, `url`, `sort_num`, `created_at`, `updated_at`)
VALUES
	(1,'/image/2016/07/16/CR-QCzNXZjTIGPHv2O.png','http://www.kingdee.gs.cn',0,'2016-06-13 15:04:29','2016-07-16 09:11:39'),
	(2,'/image/2016/07/16/CR-jim9NyuMTlJ29Uk.png','http://www.yonyou.com',0,'2016-06-13 15:04:29','2016-07-16 09:11:49'),
	(3,'/image/2016/07/16/CR-XEXRjzzFwvp27UT.png','http://www.siemens.com/entry/cn/zh/',0,'2016-06-13 15:04:29','2016-07-16 09:11:57'),
	(4,'/image/2016/07/16/CR-8fyXFgNiHePPpGf.png','http://www.sony.com.cn',0,'2016-06-13 15:04:29','2016-07-16 09:12:20'),
	(5,'/image/2016/07/16/CR-kGkBM9Wp0pimtV4.png','http://www.chanjet.com22',0,'2016-06-13 15:04:29','2016-07-16 09:12:29'),
	(6,'/image/2016/07/16/CR-OslkkMO7zErEd4C.png','http://www.founder.com.cn/zh-cn/',0,'2016-07-16 09:12:56','2016-07-16 09:12:56');

/*!40000 ALTER TABLE `partners` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table recruits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recruits`;

CREATE TABLE `recruits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `num` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `experience` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `degree` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nature` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `salary` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `duty` text COLLATE utf8_unicode_ci NOT NULL,
  `requirement` text COLLATE utf8_unicode_ci NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `recruits` WRITE;
/*!40000 ALTER TABLE `recruits` DISABLE KEYS */;

INSERT INTO `recruits` (`id`, `title`, `location`, `num`, `experience`, `degree`, `nature`, `salary`, `duty`, `requirement`, `sort_num`, `created_at`, `updated_at`)
VALUES
	(1,'密码学算法与应用工程师','北京','1人','1年','无','全职','面议','1、 负责公司身份认证产品的技术研究和开发工作;\n2、 负责密码学算法的研究和应用。','1、 本科以上学历 ;\n2、 精通 C/C++ 吾言;\n3、 熟悉 PKI 体系 , 熟悉常用的密码学算法的墓本原理 : 如 ECC、 RSA、 AES 等 ;\n4、 熟悉 PKCS 标准 , 熟悉 CSP 接口 ;\n5、 对密码学算法的原理和应用感兴趣 , 关注业界最新动态;\n6、 熟悉 Linux、 Mac 系统相关开发工作者优先;\n7、 有自我驱动力, 有强烈的责任心 . 良好的沟通能力和团队台作能力.',1,'2016-06-13 15:04:29','2016-07-16 10:06:05'),
	(2,'高级测试工程师','北京','1人','1年','无','全职','面议','1、搭建、维护测试框架，提高测试效率；\n2、设计测试用例，编写测试代码；\n3、需要较好的开发能力，能早期介入项目，能阅读项目代码；\n4、负责 WEB 高性能服务的测试以及周边客户端工具的测试。','1、对测试工作有兴趣，有志于在此方向上深入发展；\n2、精通 Java、C++/C 中任何一门编程语言，熟悉一门脚本语言；\n3、熟悉目前流行的 WEB 开发框架；\n4、有测试框架搭建的经验；\n5、有开发测试工具的经验；\n6、有高性能、高并发网络服务测试经验优先；\n7、有大数据服务测试经验优先；\n8、有自我驱动力，有强烈的责任心，良好的沟通能力和团队合作能力。',2,'2016-06-13 15:04:29','2016-07-16 10:07:16'),
	(3,'开发运维工程师','北京','1人','1年','无','全职','面议','云授权平台巨量数据存储的方案设计、 实现和优化。','1、 本科及以上学历 . 二年以上工作经验 ;\n2、 精通 MySQL 数据库的使用、 优化以及内部运行机制 ;\n3、 精通 MySQL 高可用万案、 集群等相关技术;\n4、 熟悉高速缓存技术 , 熟悉-种以上内存数据库  , 如 Redis 等;\n5、 熟练掌握两种以上开发语言 , 包括 Perl、 Python、 Java等;\n6、 熟练使用 She‖ 脚本;\n7、 有自我驱动力 , 有强烈的责任心 , 良好的沟通能力和团队台作能力。     7、 有自我驱动力, 有强烈的责任心 . 良好的沟通能力和团队台作能力.',3,'2016-06-13 15:04:29','2016-07-16 10:07:58'),
	(4,'销售代表','北京','1人','1年','无','全职','面议','1、为所辖区内与客户建立良好的关系；\n2、重点针对客户的既有业务维系与新需求的开发；\n3、完成相关的销售任务；\n4、协助部门处理销售类其他事物。','1、大专以上学历；\n2、至少1年的相关行业销售类工作经验或有过工业品销售经验，具有一定的销售沟通交    流能力；\n3、熟练使用办公类软件，有一定的计算机基础；\n4、普通话良好、工作认真积极主动、责任心与团队意识强；\n5、市场营销相关专业优先。\n                    \n职业规划：销售代表—客户经理—大客户经理',4,'2016-06-13 15:04:29','2016-07-16 10:09:17'),
	(5,'Java开发工程师','北京','1人','1年','无','全职','面议','1、基于Java相关技术的平台产品开发\n2、负责产品部分模块的设计、开发、测试、维护工作，确保工作进度和质量\n3、负责撰写所属模块的相关文档\n4、维护和升级现有软件产品和系统','1、计算机相关专业本科及以上学历，两年以上相关工作经验\n2、扎实的 Java 技术功底，在多线程、高并发程序开发方面有深厚的经验\n3、熟悉 Java 设计模式，熟悉一种或多种流行开源框架 Struts2、Spring、Hibernate 等\n4、熟练使用 MySQL、Oracle 等其中一种数据库系统，对数据库有较强的设计能力\n5、熟练使用常用 NoSQL 数据库，如 Redis 等\n6、熟悉 Tomcat、Nginx 等服务器环境的部署和调优\n7、有分布式计算、数据挖掘、海量数据处理经验者优先\n8、熟悉前端开发技术优先\n9、热爱技术，对技术有不懈的追求，喜欢研究开源代码\n10、能够自我驱动，具有团队合作精神和良好的沟通能力，有分享精神',5,'2016-06-13 15:04:29','2016-07-16 10:10:34'),
	(6,'解决方案工程师','北京','1人','1年','无','全职','面议','1、 对产品的库和工具进行调试修改 ;\n2、 解决产品的疑难问题 ;\n3、 了解需求编写需求说明书;\n4、 开发完整的加密方案;\n5、 协助培训其他员工。','1、 熟悉Windows linux底层开发;\n2、 熟练掌握 C++ delphi java php C#  等语言中的一种或多种 ;\n3、 对Windows 和 Linux 系统驱动开发有所了解 ;\n4、 对技术感兴趣  主动学习新技术.',6,'2016-06-13 15:04:29','2016-07-16 10:11:21'),
	(7,'应用开发工程师','北京','1人','1年','无','全职','面议','我们做什么\n1、 与客户沟通了解并深挖客户需求 ,  解决客户疑难问题 ;\n2、 开发完整的加密方案;\n3、 开发完善全面的办公OA系统 ;\n4、 承担公司内部培训工作。\n如果你有一年以上工作经验 , 有至少一个完整的中大型java顶目经验 , 熟练掌握前后台相关技术 java,mysql,ssh,html,css+div,js,json,jquery,ajax等 , 欢迎加入我们。\n我们将提供\n1 、 开放轻松的工作环境 ;\n2 、 自由的学习发展空间 ;\n3 、 多样性的晋升途径。','',7,'2016-06-13 15:04:29','2016-07-16 10:13:23'),
	(8,'前端开发工程师','北京','1人','1年','无','全职','面议','负责开发者中心的网站开发和改进 , 井承担一定的设计工作。','1、 精通 JavaScript、 CSS、 HTML , 至少理解-种框架 ( 如JQuery、 Angu|arJS、 React 等) ;\n2、 熟悉 Linux 系统的开发环境 , 井能熟练使用常用命令行工具和开发工具 ;\n3、 基础知识扎实 , 熟悉常用的数据结构和算法 ;\n4、 了解后端开发技术着优先 ;\n5、 热爱技术 , 善于钻研;\n6、 良好的团队台作精神 , 善于沟通和表达。',8,'2016-06-13 15:04:29','2016-07-16 10:14:15'),
	(9,'C++ 开发工程师','北京','1人','1年','无','全职','面议','1、加密锁开发工具包的开发与维护\n2、跨平台API的开发和维护\n3、解决客户疑难问题','1、精通 C 或 C++语言；\n2、精通 Windows 下WIN32 API编程，有GUI开发经验，熟悉Linux等开源系统；\n3、学习能力强，勇于接受新领域挑战；\n4、有责任感和主动性，能独立完成所负责模块的设计与开发工作；\n5、良好的团队合作和沟通能力。',9,'2016-06-13 15:04:29','2016-07-16 10:15:16');

/*!40000 ALTER TABLE `recruits` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role_privilege_relation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role_privilege_relation`;

CREATE TABLE `role_privilege_relation` (
  `Frid` int(11) unsigned NOT NULL COMMENT '角色id',
  `Fpid` int(11) unsigned NOT NULL COMMENT '权限id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `role_privilege_relation` WRITE;
/*!40000 ALTER TABLE `role_privilege_relation` DISABLE KEYS */;

INSERT INTO `role_privilege_relation` (`Frid`, `Fpid`)
VALUES
	(6,43),
	(6,88),
	(6,85),
	(6,87),
	(6,23),
	(6,72),
	(6,73),
	(6,79),
	(6,75),
	(6,76),
	(6,86),
	(7,43),
	(7,88),
	(7,85),
	(7,86),
	(7,87),
	(7,23),
	(7,72),
	(7,73),
	(7,79),
	(7,76),
	(7,75),
	(7,89);

/*!40000 ALTER TABLE `role_privilege_relation` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table routes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `routes`;

CREATE TABLE `routes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;

INSERT INTO `routes` (`id`, `name`, `parent_id`, `sort_num`)
VALUES
	(1,'2015年',0,2),
	(7,'2016年',0,1),
	(8,'深思软件安全二十载，面向互联网再启航',7,1),
	(9,'深思数盾成为高通公司软件版权保护领域忠实合作伙伴',7,2),
	(10,'深思数盾加密锁产品通过CE和FCC认证',1,1),
	(11,'互联网+重新定义加密锁：精锐5加密锁产品问世',1,2),
	(12,'2014年',0,3),
	(13,'深思将32位ARM全球最强芯片用到加密锁，精锐4S—全球第一款 CC EAL5+ 和 EMVo双重安全标准的加密锁诞生',12,1),
	(14,'再次获得了二项发明专利及二项实用新型专利',12,2),
	(15,'让专业更专注，深思助力科大讯飞智能语音及语言技术研究安全性能提升',12,3),
	(16,'深思助力海康威视实现视频处理技术和视频分析技术的版权保护',12,4),
	(17,'2013年',0,4),
	(18,'新的售后服务体系开始运行',17,1),
	(19,'深思全球客户总数突破5000家',17,2),
	(20,'获北京市专利示范单位',17,3),
	(21,'2012年',0,5),
	(22,'获北京市中小企业国际开拓资金支持',21,1),
	(23,'获国家火炬计划产业示范项目证书',21,2),
	(24,'获北京市海淀区专利专项资助资金',21,3),
	(25,'获信用评级A级单位',21,4),
	(26,'2011年',0,6),
	(27,'获中关村2011年度专利商标促进资金支持',26,1),
	(28,'入选中国绿色电子产品生产企业',26,2),
	(29,'获巴黎国际发明展览会（列宾竞赛）铜奖',26,3),
	(30,'2010年',0,7),
	(31,'成为北京市知识产权保护协会会员',30,1),
	(32,'获得海淀区科技项目（基本计划）资金支持',30,2),
	(33,'在研项目列入《国家高新技术产业发展项目计划》，并获得专项扶持资金800万',30,3),
	(34,'与清华大学合作成立“清华-深思图像识别与高速图像处理联合研究中心”',30,4),
	(35,'2009年',0,8),
	(36,'获国家高新技术企业认证',35,1),
	(37,'获得专利促进资金支持',35,2),
	(38,'2007年',0,9),
	(39,'获科技部中小企业创新基金80万元无偿资助',38,1),
	(40,'获中关村创新基金45万元无偿资助',38,2),
	(41,'获“软件中国2006年度风云榜”活动“十大最具创新性技术”奖',38,3),
	(42,'获软件企业资格认定',38,4),
	(43,'2006年',0,10),
	(44,'入选中关村“瞪羚计划”成为“瞪羚”企业',43,1),
	(45,'2005年',0,11),
	(46,'首次通过ISO9001：2008认证',45,1),
	(47,'软件保护解决方案获得中国软件产品认定业',45,2),
	(48,'2003年',0,12),
	(49,'智能卡产品通过中国金融认证中心CFCA认证',48,1),
	(50,'2000年',0,13),
	(51,'产品获得中国人民共和国公安部颁发的销售许可证',50,1),
	(52,'1996年',0,14),
	(53,'参与发起中国软件联盟（CSA）',52,1),
	(54,'1995年',0,15),
	(55,'加入北京市新技术产业开发试验区，成为高新技术产业',54,1);

/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table solutions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `solutions`;

CREATE TABLE `solutions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pic` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `demand` text COLLATE utf8_unicode_ci NOT NULL,
  `plan` text COLLATE utf8_unicode_ci NOT NULL,
  `advantage` text COLLATE utf8_unicode_ci NOT NULL,
  `sort_num` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `solutions` WRITE;
/*!40000 ALTER TABLE `solutions` DISABLE KEYS */;

INSERT INTO `solutions` (`id`, `pic`, `name`, `title`, `demand`, `plan`, `advantage`, `sort_num`, `created_at`, `updated_at`)
VALUES
	(2,'/image/2016/07/16/CR-nYdUYd5MvBEQVVV.png','管理行业','满足软件纯电子化发行和云服务化需要','管理软件通常模块众多，定制性强，且对于“云+端”的形式比其它行业软件都走得更远。这就决定了其软件保护的需求主要就是对授权的需求，例如对大量模块的授权、对客户端数量的授权等。 \n在安全方面的开发和服务，管理软件行业更希望由安全厂商全部负责，开发商只需关注自身业务模块，在“安全”这种专业领域不要牵扯精力。 \n在软件销售流程方面，管理软件行业正在向纯电子化发行方面转变，加密锁的生产、管理和服务越来越成为“负担”。','针对管理软件行业的需求特点，我们提供了以云授权平台＋云锁加密为主体的纯电子化授权解决方案，以精锐5加密锁为离线条件下的授权缓存。 深思云锁和硬件完全兼容，授权即可以在云锁上，又可以在硬件里，所有的细节都由 SS 加密中间件负责，对开发商软件透明，开发商只需要关心授权是否可用即可，在发布和管理过程中，也只需要关心授权本身的内容，无需关心授权的介质。授权的发放完全基于公私钥加密签名体系，安全问题由深思设计实现。 \n精锐 5 硬件性能和容量都大幅提升，可以存储 3000 个完整许可（可以同时设置限时、限次、限并发数等），每个许可又可以设置 64 个子模块，完全可以满足任何开发商的授权需求。 \n基于以上的授权管理体系，用户希望使用什么授权介质完全可以自由选择，用户可以直接在线上使用云锁，也可以在线下使用硬件加密锁，此时直接向深思购买加密锁即可。同时，用户的授权可以在云锁和硬件锁之间自由转移，最大限度地提升用户的使用体验。','• SS 加密中间件提供安全保护，无需开发商关心安全问题。\n• 强大的授权管理功能，满足各种业务需求。 \n• 加密锁采购、设置、管理和服务工作在技术上均可外包，开发商只关心电子授权发放即可，从开发商的角度可以完全实现“去硬件化”和纯电子化发行。',2,'2016-07-12 18:38:25','2016-07-16 09:28:47'),
	(3,'/image/2016/07/16/CR-JKuCZOC4lJhmE5S.png','游戏行业','极致安全的整体解决方案','中国南方动漫产业发达，娱乐游戏软件行业竞争极为激烈，同业抄袭严重，盗版猖獗。以往基于单一加密锁或者安全芯片的解决方案，已经远远无法满足行业中越来越强的安全需求。','针对这种极高的安全需求，深思推出了一套全方位的顶级安全解决方案，包括新一代旗舰级的加密锁精锐5，与之配套的 SS 加密中间件及0磁道全盘加密技术、Virbox源代码级虚拟化加密引擎等。此套方案在游戏行业中得到了高度认可，在反盗版方面的成就非常突出。 \n通过由深思开发的独立加密服务，在不增加开发商工作量的前提下，深思可以为开发商软件提供更强大、更灵活的保护，同时可以通过对抗随时升级加密方案，而无需开发商的任何开发量。','• 直接和锁绑定的全盘加密技术，简单易用但加密效果提升明显。 \n• 独立的 SS 加密中间件，软件只要简单的API调用即可获得驱动级的反破解保护，和硬件的通信信道层层保护，无需开发商任何开发操作 \n• 配合 SS 加密中间件，提供了在硬件内对破解行为进行反制的手段。 \n• 使用 Virbox 加密引擎编译软件的重要部分，由于在编译阶段就介入，所以在安全性和性能的综合指标方面，世界级的虚拟机外壳也是无法比拟的 \n• 硬件性能大幅提升，可编程能力大大加强，一方面可以将实际的软体关键算法移植到硬件中做到万无一失；另一方面也支持更为复杂的定制方案，开发商可以方便添加自己的特定加密方案，进一步提高私密性。',1,'2016-07-14 22:00:38','2016-07-16 09:27:05'),
	(4,'/image/2016/07/16/CR-Pzfd6lt7I931aI4.png','建筑行业','增加正版收入的同时向服务化转型','建筑行业软件从以前的工具类软件，正向信息化、服务化快速发展。大型的开发商已经开始倾向于构建统一身份认证平台，向平台转变，同时，还要保证软件的安全性以保证销售收入。这个过程中，对于加密锁生产、加密锁服务、加密工作的进行、加密方案的持续升级、软件授权的可信执行 ，就需要更为专业的支持，只有保证了这些，在转变的过程中才有可靠的安全基础，开发商才能放心地专注于自己的业务。 \n建筑软件行业的需求，在中国的独立软件开发商中比较有代表性： \n\n• 高安全强度以保证工具软件销售收入，此为转变过程的立身之本 \n• 加密工作、硬件生产和服务由专业的厂商提供，以便可以更专注在自身的业务上 \n• 统一的身份认证支持，以为将来的云服务提供基本设施\n• 云授权，提高软件的购买和使用体验','作为深思云管理平台的一部分，新一代加密锁精锐5，在功能和性能上有了质的飞跃，直接支持授权管理应用，同时也原生支持标准的身份认证功能（深思提供 CSP 支持）。精锐 5 安全体系完全基于 PKI 体系，无需大量的密码管理，出厂即可以安全进行远程升级，因此开发商的加密锁生产和管理操作都可以在深思安全地进行，自然后续的服务也由深思负责。 \n同时深思推出了和精锐 5 配套的 SS 加密中间件，除了和硬件锁配合实现更为高层的业务支持（如授权管理、和云锁的无缝兼容），更为重要是提供为更为专业的、可持续的安全保护，使用深思的 SDK 或加密工具即可自动获得中间件的驱动级反盗版保护，开发商无需在这方面自己进行开发工作。\n除了完善的授权功能、安全功能之外，精锐 5 一如既往地支持硬件编程功能，而且能力更为强大，开发商可以添加自己的特殊需求，也可以通过少量的开发工作完美兼容当前加密方案，更平滑地过渡到新的安全体系。 \n深思云锁提供了和精锐 5 完全的兼容（包括硬件代码执行功能也支持）的功能，加密中间件处理一切底层细节，给开发商和硬件完全一致的开发和使用体验： \n• 开发商授权可发布到云锁，这样用户可以立即使用，软件可获得性好； \n• 也可以发布到精锐 5 硬件锁，方便无法在线的客户需求； \n• 授权还可以在云、精锐 5 之间任意转移，最大限度地提高用户的软件使用体验，让加密不影响软件的用户体验。\n 以上云锁、精锐 5、SS 加密中间件和配套的 SDK 及加密工具，即形成为深思云授权平台，一套完整的软件保护、授权管理解决方案。','• 安全：通过SS中间件，开发商可以随时获得深思的最新反盗版技术，无需自身的开发力量即可与破解者进行持续的技术对抗； 这也使得加密工作外包成为可能。 \n• 授权：云、硬件完全一致的授权开发和使用体验，最大程度地方便了开发商和软件用户，保证安全性的同时提高了软件的使用体验。 \n• 身份认证支持：精锐 5 硬件原生支持标准身份认证功能，为开发商向用户提供云服务打下的坚实的基础。 \n• 精锐 5 硬件生产工作开发商可以完全不必关心，物流、后续服务都可以直接由深思安全地进行。',3,'2016-07-16 09:32:34','2016-07-16 09:32:34'),
	(5,'/image/2016/07/16/CR-aiuDougIdWA8fIT.png','教育和文档','保护数据资产，对内容进行授权','教育行业软件除了软件本身需要反盗版之外，还有三个特征： \n• 比软件本身更重要的是“内容”的保护，如各种教程和课件等 \n• 一些软件，对使用的时间需要严格控制，如某些考试软件必须在特定的时间段才能使用 \n• 内容可能多地使用，如老师可能在学校、家里都使用软件进行备课','深思云授权管理平台是新一代的软件保护方案，在深思提供的安全方案的基础之上，着重解决了软件授权的灵活性、安全性和可获得性问题。\n 云授权平台配套的新一代加密锁精锐 5，可以容纳 3000 条完整授权（可以同时设置限时、限次、限并发数等），每条授权又可以设置 64 个子模块，授权 ID 的命名空间超过 4 亿。每个授权都可以单独加解密数据，开发商可以将授权与被保护的“内容”相对应，对其进行单独的加解密即可完成对“内容”的保护，这无需太多的开发工作，只需要调用简单接口即可，所有的细节都由中间件、云锁或硬件锁处理。 \n除了容量大，授权类型也极为丰富，仅时间授权就有起始时间、结束时间和时间段等多种类型，加上强大的可编程能力，可以满足各种授权需求。 \n深思云锁和硬件锁在授权的使用上完全兼容，授权也可以在云、硬件锁之间安全转移，这就给了用户最大的自由来决定软件的使用，多地访问授权自然不在话下。','• 新一代加密锁授权容量巨大，可以满足众多“内容”的保护需求 \n• 授权类型丰富，可编程能力强，底层能满足几乎所有对授权的需求\n• 和硬件锁全兼容的云锁，软件用户更加自由 \n• 开发量工作极小',4,'2016-07-16 09:34:59','2016-07-16 09:34:59'),
	(6,'/image/2016/07/16/CR-SUy274odjcgjRY1.png','通用行业','协助软件企业互联网化','在互联网广泛普及的今天，云计算也已经深入人心，软件企业竞争越来越激烈，都处于向互联网转型的关键阶段，亟需把所有的精力都用在自身的业务上。\n与此同时，盗版者水平越来越高，传播速度前所未有，软件保护手段的专业性要求越来越强，通常软件开发商即使有心，也无力在主业之外长期维持如此专业的加密团队。 \n再者，在互联网普及的情况下，传统软件的销售方式也正在发生巨大的变化，如“软件租赁”，微软的Office转变成Office 365便是极有代表性的一例。这就需要安全厂商直接提供更为灵活的软件授权管理方案。 \n第三，云服务深入人心，最为重要的东西已经从软件本身转向对用户的了解，对用户的了解才是最宝贵的资源。用户何时、何地、如何使用软件，使用中碰到了什么问题，真正使用的模块有哪些……这些信息对软件开发商改进软件、提供更有价值的服务和产品都极为重要，而这些，也需要精细的授权管理，需要云端的解决方案。\n 最后，加密锁硬件的管理和服务通常非软件开发商所长，这方面需要专业厂商直接提供支持。','深思云授权管理平台，是深思新一代软件保护方案，是将云锁、新一代加密锁硬件、SS 加密中间件、工具化的加密流程有机结合，形成的一整套解决方案。 \n一切的授权管理方案，安全是基础，没有安全一切都无从谈起。深思集十数年软件保护经验，打造了 SS 加密中间件和全自动加密工具，意在减少开发商的开发量的同时又能获得更高的安全强度。深思安全实验室持续和黑客进行技术对抗，通过升级将最新的软件保护技术通过 SS 加密中间件下发，所有受保护的软件立刻可以享受，而这一过程，对开发商来说是透明的，只要使用深思的加密工具或简单调用 SDK ，无需任何额外的开发工作即可享受。 \n授权管理平台提供了强大的授权管理功能： \n• 首先，授权类型多，满足绝大部分软件企业需求； \n• 其次，设备容量大，云锁自不必说，新一代加密锁精锐 5 硬件可以存储多达 3000 条授权完整，每条授权都有限时、限次、限并发、保存私有数 据、加解密等功能，同时每条授权都有64个子模块可以设置； \n• 第三，云锁和硬件锁无缝兼容，它们作为授权容器是差别的，使用时无需开发商任何额外的开发工作，开发商只关心授权本身和软件功能的对应 即可，所有细节都由 SS 加密中间件处理。\n • 最后，云锁和硬件锁的授权不仅完全兼容，而且可以安全地相互转移，对软件用户而言，获得了最大的软件使用灵活性：既可以在线使用云授权，又可以将授权转移到加密锁中离线使用。 \n通过对授权使用的了解，无需开发商和用户的任何隐私信息，深思云授权平台即可对用户的软件使用行为了如指掌，可以向开发商提供专业的统计分析结果，以帮助开发商开发出对用户更有实际价值的产品。深思云授权管理平台，以强大的授权功能“让软件回归价值”，又以强大的反盗版能力“让价值获得回报”。 \n云授权平台配套的最新一代加密锁精锐 5，基于 PKI 的安全体系，不再依赖 PIN 管理，开发商只关心授权的发放（通过深思的工具或 SDK）即可，加密锁硬件本身可以由用户或代理商按需直接向深思采购，并由深思提供后续服务，对于软件开发商本身而言真正做到了“去加密锁化”，加密锁的采购、设置、管理、服务都无需再操心了。','• 直接和锁绑定的全盘加密技术，简单易用但加密效果提升明显。 \n• 独立的 SS 加密中间件，软件只要简单的API调用即可获得驱动级的反破解保护，和硬件的通信信道层层保护，无需开发商任何开发操作。 \n• 配合 SS 加密中间件，提供了在硬件内对破解行为进行反制的手段。 \n• 使用 Virbox 加密引擎编译软件的重要部分，由于在编译阶段就介入，所以在安全性和性能的综合指标方面，世界级的虚拟机外壳也是无法比拟的 \n• 硬件性能大幅提升，可编程能力大大加强，一方面可以将实际的软件关键算法移植到硬件中做到万无一失；另一方面也支持更为复杂的定制方案，开发商可以方便添加自己的特定加密方案，进一步提高私密性。',5,'2016-07-16 09:38:01','2016-07-16 09:38:01');

/*!40000 ALTER TABLE `solutions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table super_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `super_admin`;

CREATE TABLE `super_admin` (
  `Fid` varchar(255) NOT NULL DEFAULT '' COMMENT '公司id 或后台用户id',
  `Ftype` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0公司 1 个人',
  UNIQUE KEY `Fid` (`Fid`,`Ftype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
