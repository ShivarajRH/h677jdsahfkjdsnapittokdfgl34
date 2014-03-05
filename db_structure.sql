-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 09, 2014 at 11:44 AM
-- Server version: 5.1.70-cll
-- PHP Version: 5.3.17

SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'snapitto_db'
--

DELIMITER $$
--
-- Procedures
--
$$

$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table 'auto_readmail_log'
--

CREATE TABLE IF NOT EXISTS auto_readmail_log (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  ticket_id bigint(20) unsigned NOT NULL,
  `subject` varchar(150) NOT NULL,
  msg text NOT NULL,
  `from` varchar(150) NOT NULL,
  created_on datetime NOT NULL,
  PRIMARY KEY (id),
  KEY ticket_id (ticket_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'auto_readmail_uid'
--

CREATE TABLE IF NOT EXISTS auto_readmail_uid (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  im_uid bigint(20) unsigned NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'backup_t_stock_info_mar_03'
--

CREATE TABLE IF NOT EXISTS backup_t_stock_info_mar_03 (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT NULL,
  location_id int(11) DEFAULT NULL,
  rack_bin_id int(11) DEFAULT NULL,
  mrp decimal(15,4) DEFAULT '0.0000',
  available_qty double DEFAULT '0',
  in_transit double DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  tmp_brandid double DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_imei_no'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_imei_no (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  product_id int(10) unsigned NOT NULL,
  imei_no varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  grn_id int(10) unsigned NOT NULL,
  stock_id bigint(11) DEFAULT '0',
  is_returned tinyint(1) DEFAULT '0',
  return_prod_id bigint(11) DEFAULT '0',
  order_id bigint(20) unsigned NOT NULL,
  is_imei_activated tinyint(1) DEFAULT '0',
  imei_activated_on datetime DEFAULT NULL,
  activated_by int(11) DEFAULT '0',
  activated_mob_no varchar(20) DEFAULT NULL,
  activated_member_id int(11) DEFAULT '0',
  ref_credit_note_id bigint(11) DEFAULT '0',
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_reserved_batch_stock'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_reserved_batch_stock (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  batch_id bigint(11) DEFAULT '0',
  p_invoice_no bigint(11) DEFAULT '0',
  product_id bigint(11) DEFAULT '0',
  stock_info_id bigint(11) DEFAULT '0',
  order_id bigint(11) DEFAULT '0',
  qty double DEFAULT '0',
  extra_qty double DEFAULT '0',
  release_qty double DEFAULT '0',
  reserved_on bigint(20) DEFAULT NULL,
  released_on bigint(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  tmp_prev_stk_id bigint(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY batch_id (batch_id),
  KEY p_invoice_no (p_invoice_no),
  KEY product_id (product_id),
  KEY stock_info_id (stock_info_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_stock_info'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_stock_info (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT '0',
  location_id int(11) DEFAULT '0',
  rack_bin_id int(11) DEFAULT '0',
  mrp decimal(15,4) DEFAULT '0.0000',
  available_qty double DEFAULT '0',
  product_barcode varchar(50) DEFAULT NULL,
  in_transit double DEFAULT '0',
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  tmp_brandid double DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_stock_update_log'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_stock_update_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT NULL,
  update_type tinyint(1) DEFAULT '0' COMMENT '0: Out, 1: In',
  p_invoice_id int(10) unsigned NOT NULL,
  corp_invoice_id bigint(11) DEFAULT NULL,
  invoice_id bigint(11) DEFAULT NULL,
  grn_id int(11) DEFAULT NULL,
  voucher_book_slno varchar(255) DEFAULT NULL,
  return_prod_id bigint(11) DEFAULT '0',
  qty double DEFAULT NULL,
  current_stock double DEFAULT NULL,
  msg varchar(255) NOT NULL,
  mrp_change_updated tinyint(1) DEFAULT '-1' COMMENT '0: no,1: yes,-1:not from stock intake',
  stock_info_id bigint(11) DEFAULT '0',
  stock_qty int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_up_king_invoice_2013apr1'
--

CREATE TABLE IF NOT EXISTS bck_up_king_invoice_2013apr1 (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  invoice_no bigint(20) unsigned NOT NULL,
  transid char(18) NOT NULL,
  order_id bigint(20) unsigned NOT NULL,
  mrp int(10) unsigned NOT NULL,
  discount decimal(10,2) unsigned NOT NULL,
  nlc decimal(10,2) unsigned NOT NULL,
  phc decimal(10,2) unsigned NOT NULL,
  tax double unsigned NOT NULL,
  service_tax double NOT NULL,
  cod double unsigned NOT NULL,
  ship double unsigned NOT NULL,
  giftwrap_charge double DEFAULT '0',
  invoice_status tinyint(1) DEFAULT '0',
  createdon bigint(20) DEFAULT NULL,
  cancelled_on bigint(20) DEFAULT NULL,
  delivery_medium varchar(255) DEFAULT '0',
  tracking_id varchar(50) DEFAULT '0',
  shipdatetime datetime DEFAULT NULL,
  notify_customer tinyint(1) DEFAULT '0',
  is_delivered tinyint(1) DEFAULT '0',
  is_partial_invoice tinyint(1) DEFAULT '0',
  total_prints int(5) DEFAULT '0',
  outscanned_on bigint(20) DEFAULT NULL,
  is_outscanned tinyint(1) DEFAULT '0',
  is_b2b tinyint(1) NOT NULL,
  old_pnh_inv_no bigint(20) DEFAULT '0',
  new_pnh_inv_no bigint(20) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY order_id (order_id),
  KEY invoice_no (invoice_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_up_t_stock_info_mar17'
--

CREATE TABLE IF NOT EXISTS bck_up_t_stock_info_mar17 (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT NULL,
  location_id int(11) DEFAULT NULL,
  rack_bin_id int(11) DEFAULT NULL,
  mrp decimal(15,4) DEFAULT '0.0000',
  available_qty double DEFAULT '0',
  product_barcode varchar(50) DEFAULT NULL,
  in_transit double DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  tmp_brandid double DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'ci_sessions'
--

CREATE TABLE IF NOT EXISTS ci_sessions (
  session_id varchar(40) NOT NULL DEFAULT '0',
  ip_address varchar(16) NOT NULL DEFAULT '0',
  user_agent varchar(50) NOT NULL,
  last_activity int(10) unsigned NOT NULL DEFAULT '0',
  user_data text NOT NULL,
  PRIMARY KEY (session_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cod_pincodes'
--

CREATE TABLE IF NOT EXISTS cod_pincodes (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  pincode varchar(8) DEFAULT NULL,
  old varchar(3) DEFAULT NULL,
  `name` varchar(24) DEFAULT NULL,
  state varchar(14) DEFAULT NULL,
  zone varchar(4) DEFAULT NULL,
  region varchar(6) DEFAULT NULL,
  control varchar(9) DEFAULT NULL,
  cod varchar(3) DEFAULT NULL,
  `type` varchar(40) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_admin'
--

CREATE TABLE IF NOT EXISTS cou_admin (
  userid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  usertype int(10) unsigned NOT NULL,
  username varchar(80) NOT NULL,
  `password` char(32) NOT NULL,
  mobile bigint(20) unsigned NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  created_date bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_admin_details'
--

CREATE TABLE IF NOT EXISTS cou_admin_details (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  email varchar(200) NOT NULL,
  address text NOT NULL,
  area varchar(150) NOT NULL,
  city varchar(150) NOT NULL,
  pincode int(10) unsigned NOT NULL,
  telephone varchar(60) NOT NULL,
  modified_time bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon'
--

CREATE TABLE IF NOT EXISTS cou_coupon (
  coupon char(16) NOT NULL,
  `status` tinyint(1) NOT NULL,
  denomination int(10) unsigned NOT NULL,
  valid_upto bigint(20) unsigned NOT NULL,
  used_on bigint(20) unsigned NOT NULL,
  UNIQUE KEY coupon (coupon)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon_details'
--

CREATE TABLE IF NOT EXISTS cou_coupon_details (
  coupon char(16) NOT NULL,
  sku1 char(3) NOT NULL,
  sku2 int(10) unsigned NOT NULL,
  distributor bigint(20) unsigned NOT NULL,
  retailer bigint(20) unsigned NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  created_date bigint(20) unsigned NOT NULL,
  PRIMARY KEY (coupon)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon_history'
--

CREATE TABLE IF NOT EXISTS cou_coupon_history (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  num int(10) unsigned NOT NULL,
  `start` char(15) NOT NULL,
  `end` char(15) NOT NULL,
  distributor bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_denominations'
--

CREATE TABLE IF NOT EXISTS cou_denominations (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_user'
--

CREATE TABLE IF NOT EXISTS cou_user (
  userid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  mobile bigint(20) unsigned NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cron_image_updater_lock'
--

CREATE TABLE IF NOT EXISTS cron_image_updater_lock (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  is_locked tinyint(1) NOT NULL,
  modified_by int(10) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  finish_status tinyint(4) NOT NULL,
  finished_on bigint(20) unsigned NOT NULL,
  images_updated int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cron_log'
--

CREATE TABLE IF NOT EXISTS cron_log (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  cron int(10) unsigned NOT NULL,
  count int(10) unsigned NOT NULL,
  `start` int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_auth'
--

CREATE TABLE IF NOT EXISTS data_api_auth (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lock` varchar(50) NOT NULL,
  `key` char(32) NOT NULL,
  last_login bigint(20) unsigned NOT NULL,
  is_enabled tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_tokens'
--

CREATE TABLE IF NOT EXISTS data_api_tokens (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  token char(32) NOT NULL,
  auth_id int(10) unsigned NOT NULL,
  expires_on bigint(20) NOT NULL,
  PRIMARY KEY (id),
  KEY token (token)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_tokens_bak'
--

CREATE TABLE IF NOT EXISTS data_api_tokens_bak (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  token char(32) NOT NULL,
  auth_id int(10) unsigned NOT NULL,
  expires_on bigint(20) NOT NULL,
  PRIMARY KEY (id),
  KEY token (token)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deals_bulk_upload'
--

CREATE TABLE IF NOT EXISTS deals_bulk_upload (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  items int(10) unsigned NOT NULL,
  is_all_image_updated tinyint(1) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deals_bulk_upload_items'
--

CREATE TABLE IF NOT EXISTS deals_bulk_upload_items (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  bulk_id int(10) unsigned NOT NULL,
  item_id bigint(20) unsigned NOT NULL,
  is_image_updated tinyint(1) NOT NULL,
  updated_on bigint(20) unsigned NOT NULL,
  updated_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY bulk_id (bulk_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deal_price_changelog'
--

CREATE TABLE IF NOT EXISTS deal_price_changelog (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  old_mrp decimal(10,2) NOT NULL,
  new_mrp decimal(10,2) NOT NULL,
  new_price decimal(10,2) unsigned NOT NULL,
  old_price decimal(10,2) NOT NULL,
  reference_grn int(10) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'discontinued'
--

CREATE TABLE IF NOT EXISTS discontinued (
  catid int(3) DEFAULT NULL,
  brandid int(8) DEFAULT NULL,
  cat varchar(20) DEFAULT NULL,
  brand varchar(18) DEFAULT NULL,
  `No of deals` int(3) DEFAULT NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'fb_miscusers'
--

CREATE TABLE IF NOT EXISTS fb_miscusers (
  id bigint(20) unsigned NOT NULL,
  fid varchar(100) NOT NULL,
  birthday char(15) NOT NULL,
  age int(10) unsigned NOT NULL,
  home varchar(100) NOT NULL,
  location varchar(100) NOT NULL,
  gender char(15) NOT NULL,
  lastupdate bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'franchise_suspension_log'
--

CREATE TABLE IF NOT EXISTS franchise_suspension_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT '0',
  suspension_type tinyint(3) DEFAULT '0',
  reason varchar(25555) DEFAULT NULL,
  suspended_on bigint(11) DEFAULT '0',
  suspended_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'group_log'
--

CREATE TABLE IF NOT EXISTS group_log (
  id int(11) NOT NULL AUTO_INCREMENT,
  emp_id int(11) DEFAULT NULL,
  `type` varchar(2555) DEFAULT NULL,
  grp_msg varchar(25555) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'imei_m_scheme'
--

CREATE TABLE IF NOT EXISTS imei_m_scheme (
  id int(11) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT NULL,
  menuid bigint(11) DEFAULT NULL,
  categoryid bigint(20) DEFAULT NULL,
  brandid bigint(20) DEFAULT NULL,
  scheme_type tinyint(11) DEFAULT NULL,
  credit_value double(10,2) DEFAULT NULL,
  scheme_from bigint(20) DEFAULT NULL,
  scheme_to bigint(20) DEFAULT NULL,
  sch_apply_from bigint(20) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  modified_by tinyint(11) DEFAULT NULL,
  is_active tinyint(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brandid (brandid),
  KEY categoryid (categoryid),
  KEY franchise_id (franchise_id),
  KEY menuid (menuid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_activity'
--

CREATE TABLE IF NOT EXISTS king_activity (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid char(32) NOT NULL,
  msg text NOT NULL,
  dealid bigint(20) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_address'
--

CREATE TABLE IF NOT EXISTS king_address (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  phone varchar(20) NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  address text NOT NULL,
  city text NOT NULL,
  pincode varchar(20) NOT NULL,
  shipbill tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin'
--

CREATE TABLE IF NOT EXISTS king_admin (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id char(32) NOT NULL,
  `name` varchar(120) NOT NULL,
  username varchar(150) NOT NULL,
  `password` char(32) NOT NULL,
  usertype enum('1','2','3') NOT NULL,
  role_id int(11) DEFAULT '0',
  access bigint(20) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  fullname varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  mobile varchar(255) DEFAULT NULL,
  phone varchar(255) NOT NULL,
  gender varchar(20) DEFAULT NULL,
  address text,
  city varchar(100) DEFAULT NULL,
  img_url varchar(255) DEFAULT NULL,
  account_blocked tinyint(1) DEFAULT '0',
  createdon datetime NOT NULL,
  modifiedon datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY user_id (user_id),
  KEY user_id_2 (user_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin_activity'
--

CREATE TABLE IF NOT EXISTS king_admin_activity (
  id int(11) NOT NULL AUTO_INCREMENT,
  activity text,
  created_by varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin_old'
--

CREATE TABLE IF NOT EXISTS king_admin_old (
  user_id char(32) NOT NULL,
  `name` varchar(120) NOT NULL,
  `password` char(32) NOT NULL,
  usertype enum('1','2','3') NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  fullname varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  phone varchar(255) NOT NULL,
  createdon datetime NOT NULL,
  modifiedon datetime NOT NULL,
  PRIMARY KEY (user_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_agents'
--

CREATE TABLE IF NOT EXISTS king_agents (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  via_uid varchar(60) NOT NULL,
  `name` varchar(100) NOT NULL,
  balance int(10) unsigned NOT NULL,
  created_date bigint(20) unsigned NOT NULL,
  last_login bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_agent_transactions'
--

CREATE TABLE IF NOT EXISTS king_agent_transactions (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  agentid varchar(60) NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  orderid bigint(20) unsigned NOT NULL,
  via_transid varchar(100) NOT NULL,
  price int(10) unsigned NOT NULL,
  com int(10) unsigned NOT NULL,
  qty int(10) unsigned NOT NULL,
  paid int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_announcements'
--

CREATE TABLE IF NOT EXISTS king_announcements (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(200) NOT NULL,
  url varchar(200) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_api_logins'
--

CREATE TABLE IF NOT EXISTS king_api_logins (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  auth char(32) NOT NULL,
  last_login bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY auth (auth)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_audit'
--

CREATE TABLE IF NOT EXISTS king_audit (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  description text NOT NULL,
  `user` varchar(100) NOT NULL,
  credit int(10) unsigned NOT NULL,
  debit int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_barcodes'
--

CREATE TABLE IF NOT EXISTS king_barcodes (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  barcode varchar(30) NOT NULL,
  PRIMARY KEY (id),
  KEY barcode (barcode)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boarders'
--

CREATE TABLE IF NOT EXISTS king_boarders (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  pic char(12) NOT NULL,
  username char(25) NOT NULL,
  boards int(10) unsigned NOT NULL,
  tags int(10) unsigned NOT NULL,
  loves int(10) unsigned NOT NULL,
  comments int(10) unsigned NOT NULL,
  followers int(10) unsigned NOT NULL,
  following int(10) unsigned NOT NULL,
  facebook varchar(200) NOT NULL,
  twitter varchar(200) NOT NULL,
  linkedin varchar(200) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY userid (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boarder_followers'
--

CREATE TABLE IF NOT EXISTS king_boarder_followers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  follower bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boards'
--

CREATE TABLE IF NOT EXISTS king_boards (
  bid int(10) unsigned NOT NULL AUTO_INCREMENT,
  catid int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  url varchar(50) NOT NULL,
  tags int(10) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  public tinyint(1) NOT NULL,
  followers int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (bid),
  KEY url (url)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_activity'
--

CREATE TABLE IF NOT EXISTS king_board_activity (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  userid2 bigint(20) unsigned NOT NULL,
  boardid bigint(20) unsigned NOT NULL,
  tagid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_cats'
--

CREATE TABLE IF NOT EXISTS king_board_cats (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_followers'
--

CREATE TABLE IF NOT EXISTS king_board_followers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  bid bigint(20) unsigned NOT NULL,
  follower bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_brands'
--

CREATE TABLE IF NOT EXISTS king_brands (
  sno bigint(20) NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  url varchar(150) NOT NULL,
  description text NOT NULL,
  logoid char(32) DEFAULT NULL,
  address text NOT NULL,
  website varchar(50) NOT NULL,
  email varchar(80) NOT NULL,
  admin varchar(255) NOT NULL,
  featured_start bigint(20) unsigned NOT NULL,
  featured_end bigint(20) unsigned NOT NULL,
  createdon datetime NOT NULL,
  modifiedon datetime NOT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY id (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_bulkorders_invoices'
--

CREATE TABLE IF NOT EXISTS king_bulkorders_invoices (
  id int(11) NOT NULL AUTO_INCREMENT,
  allotment_no int(11) DEFAULT '0',
  invoice_nos text,
  tot_printed int(3) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_buyprocess'
--

CREATE TABLE IF NOT EXISTS king_buyprocess (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  bpid bigint(20) unsigned NOT NULL,
  quantity int(10) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL,
  isrefund tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(3) unsigned NOT NULL,
  done_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_callcenter'
--

CREATE TABLE IF NOT EXISTS king_callcenter (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  enabled tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaigns'
--

CREATE TABLE IF NOT EXISTS king_campaigns (
  id int(11) NOT NULL AUTO_INCREMENT,
  campaign_no varchar(255) DEFAULT NULL,
  campaign_type varchar(255) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  description text,
  banner_image varchar(255) DEFAULT NULL,
  banner_link varchar(2024) DEFAULT NULL,
  campaign_cycle varchar(100) DEFAULT NULL,
  campaign_start datetime DEFAULT NULL,
  campagin_end datetime DEFAULT NULL,
  template_id varchar(255) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaigns_deals'
--

CREATE TABLE IF NOT EXISTS king_campaigns_deals (
  id int(11) NOT NULL AUTO_INCREMENT,
  campaign_no varchar(255) DEFAULT NULL,
  deal_id varchar(255) DEFAULT NULL,
  relative_link varchar(2024) DEFAULT NULL,
  `order` int(3) DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaign_templates'
--

CREATE TABLE IF NOT EXISTS king_campaign_templates (
  id int(11) NOT NULL AUTO_INCREMENT,
  template_filename varchar(255) DEFAULT NULL,
  template_name varchar(255) DEFAULT NULL,
  template_html text,
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_carts'
--

CREATE TABLE IF NOT EXISTS king_carts (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  cart text NOT NULL,
  updated bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_cashbacks (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  amount int(10) unsigned NOT NULL,
  userid int(11) NOT NULL,
  url char(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  claim_time bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks_config'
--

CREATE TABLE IF NOT EXISTS king_cashbacks_config (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` int(10) unsigned NOT NULL,
  min int(10) unsigned NOT NULL,
  validity int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks_track'
--

CREATE TABLE IF NOT EXISTS king_cashbacks_track (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  coupon char(15) NOT NULL,
  transid char(20) NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashback_campaigns'
--

CREATE TABLE IF NOT EXISTS king_cashback_campaigns (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  cashback double(5,2) NOT NULL,
  `starts` bigint(20) unsigned NOT NULL,
  expires bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  min_trans_amount int(10) unsigned NOT NULL,
  coupon_valid int(10) unsigned NOT NULL,
  coupons_num int(10) unsigned NOT NULL,
  coupon_min_order int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_catbrand'
--

CREATE TABLE IF NOT EXISTS king_catbrand (
  id int(11) NOT NULL AUTO_INCREMENT,
  catid int(11) NOT NULL,
  brandid bigint(20) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_categories'
--

CREATE TABLE IF NOT EXISTS king_categories (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  url varchar(150) NOT NULL,
  description text NOT NULL,
  catimage varchar(50) NOT NULL,
  prior smallint(5) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (id),
  KEY url (url)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_comments'
--

CREATE TABLE IF NOT EXISTS king_comments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  dealid bigint(20) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  `comment` text NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  flag tinyint(1) NOT NULL,
  `new` tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_contact'
--

CREATE TABLE IF NOT EXISTS king_contact (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  userid int(10) unsigned NOT NULL,
  `subject` text NOT NULL,
  message text NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `date` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_corporates'
--

CREATE TABLE IF NOT EXISTS king_corporates (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  alias int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_corp_buys'
--

CREATE TABLE IF NOT EXISTS king_corp_buys (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  corpid bigint(20) unsigned NOT NULL,
  buys int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_coupons'
--

CREATE TABLE IF NOT EXISTS king_coupons (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(12) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  brandid varchar(200) NOT NULL,
  catid varchar(200) NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  min int(10) unsigned NOT NULL,
  used int(10) unsigned NOT NULL,
  unlimited tinyint(1) NOT NULL,
  referral bigint(20) unsigned NOT NULL,
  created bigint(20) unsigned NOT NULL,
  expires bigint(20) unsigned NOT NULL,
  lastused bigint(20) unsigned NOT NULL,
  gift_voucher tinyint(1) NOT NULL,
  remarks varchar(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY `code` (`code`),
  KEY brandid (brandid),
  KEY catid (catid),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_coupon_activity'
--

CREATE TABLE IF NOT EXISTS king_coupon_activity (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(13) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL,
  min int(10) unsigned NOT NULL,
  expires bigint(20) unsigned NOT NULL,
  unlimited tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_dealitems'
--

CREATE TABLE IF NOT EXISTS king_dealitems (
  sno bigint(20) NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  dealid bigint(20) unsigned NOT NULL,
  nlc int(10) unsigned NOT NULL,
  phc int(10) unsigned NOT NULL,
  shc int(10) unsigned NOT NULL,
  rsp int(10) unsigned NOT NULL,
  shipsin varchar(50) NOT NULL,
  shipsto varchar(100) NOT NULL,
  itemcode varchar(100) NOT NULL,
  model varchar(100) NOT NULL,
  price int(10) unsigned NOT NULL,
  store_price int(10) unsigned NOT NULL,
  nyp_price int(10) unsigned NOT NULL,
  billon_orderprice tinyint(1) DEFAULT '0',
  gender_attr varchar(100) NOT NULL,
  ratings int(10) unsigned NOT NULL,
  reviews int(10) unsigned NOT NULL,
  snapits int(10) unsigned NOT NULL,
  buys int(10) unsigned NOT NULL,
  loves int(10) unsigned NOT NULL,
  fcp int(10) unsigned NOT NULL,
  orgprice int(10) unsigned NOT NULL,
  viaprice int(10) unsigned NOT NULL,
  agentcom int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  print_name varchar(150) DEFAULT NULL,
  quantity int(10) unsigned NOT NULL,
  available int(10) unsigned NOT NULL,
  pic char(50) NOT NULL,
  tagline varchar(250) NOT NULL,
  description1 text NOT NULL,
  description2 text NOT NULL,
  slots text NOT NULL,
  url varchar(150) NOT NULL,
  live tinyint(1) NOT NULL,
  private tinyint(1) NOT NULL,
  tellurprice tinyint(1) NOT NULL,
  b2b tinyint(1) NOT NULL,
  tax int(10) unsigned NOT NULL,
  service_tax int(10) unsigned NOT NULL,
  service_tax_cod int(10) unsigned NOT NULL,
  bp_expires bigint(20) unsigned NOT NULL,
  cod tinyint(1) NOT NULL,
  groupbuy tinyint(1) NOT NULL DEFAULT '1',
  sizing varchar(40) NOT NULL DEFAULT '0',
  gender_men tinyint(1) DEFAULT '0',
  gender_women tinyint(1) DEFAULT '0',
  gender_unisex tinyint(1) DEFAULT '0',
  gender_kids tinyint(1) DEFAULT '0',
  favs tinyint(1) NOT NULL,
  min_cart_value double DEFAULT '0',
  max_allowed_qty int(11) DEFAULT '5',
  is_featured tinyint(1) DEFAULT '0',
  cashback int(10) unsigned NOT NULL,
  bodyparts tinyint(1) NOT NULL,
  is_pnh tinyint(1) NOT NULL,
  pnh_id int(10) unsigned NOT NULL,
  is_combo tinyint(1) NOT NULL,
  temp_loc varchar(100) DEFAULT NULL,
  move_as_product tinyint(1) DEFAULT '0',
  tmp_dealid bigint(20) DEFAULT NULL,
  tmp_itemid bigint(20) DEFAULT NULL,
  created_on datetime NOT NULL,
  created_by bigint(11) DEFAULT NULL,
  modified_on datetime NOT NULL,
  modified_by bigint(11) DEFAULT NULL,
  description text NOT NULL,
  created bigint(20) unsigned NOT NULL,
  modified bigint(20) unsigned NOT NULL,
  hs18_itemid bigint(20) DEFAULT NULL,
  hs18_sku_code bigint(20) DEFAULT NULL,
  tmp_pnh_itemid bigint(20) DEFAULT NULL,
  tmp_pnh_dealid bigint(20) DEFAULT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY tmp_pnh_itemid (tmp_pnh_itemid),
  KEY url (url),
  KEY dealid (dealid),
  KEY id (id),
  KEY `name` (`name`),
  KEY pnh_id (pnh_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_dealpreviews'
--

CREATE TABLE IF NOT EXISTS king_dealpreviews (
  dealid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  id bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  UNIQUE KEY id (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_deals'
--

CREATE TABLE IF NOT EXISTS king_deals (
  sno bigint(20) NOT NULL AUTO_INCREMENT,
  dealid bigint(20) unsigned NOT NULL,
  catid int(10) unsigned NOT NULL,
  brandid bigint(15) unsigned NOT NULL,
  vendorid bigint(20) unsigned NOT NULL,
  menuid int(10) unsigned NOT NULL,
  menuid2 int(10) unsigned NOT NULL,
  startdate int(10) NOT NULL,
  enddate int(10) NOT NULL,
  pic char(50) NOT NULL,
  tagline varchar(250) NOT NULL,
  description text NOT NULL,
  description_bak text NOT NULL,
  keywords text NOT NULL,
  dealtype enum('0','1','2','3') NOT NULL,
  featured_start bigint(20) unsigned NOT NULL,
  featured_end bigint(20) unsigned NOT NULL,
  publish int(1) NOT NULL,
  discontinued tinyint(1) NOT NULL,
  website varchar(120) NOT NULL,
  email varchar(100) NOT NULL,
  mobile varchar(20) NOT NULL,
  phone varchar(80) NOT NULL,
  address text NOT NULL,
  city varchar(120) NOT NULL,
  state varchar(120) NOT NULL,
  pincode int(11) NOT NULL,
  created_on datetime NOT NULL,
  modified_on datetime NOT NULL,
  total_items int(3) NOT NULL,
  is_giftcard tinyint(1) DEFAULT '0',
  is_coupon_applicable tinyint(1) DEFAULT '1',
  catid_old int(11) DEFAULT '0',
  menuid_old int(11) DEFAULT '0',
  tmp_pnh_dealid bigint(20) DEFAULT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY tmp_pnh_dealid (tmp_pnh_dealid),
  KEY dealid (dealid),
  KEY brandid (brandid),
  KEY catid (catid),
  KEY tagline (tagline),
  KEY menuid (menuid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_deal_alerts'
--

CREATE TABLE IF NOT EXISTS king_deal_alerts (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  email varchar(100) NOT NULL,
  mobile varchar(20) NOT NULL,
  request tinyint(1) NOT NULL COMMENT '0-request 1-alert',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_facebookers'
--

CREATE TABLE IF NOT EXISTS king_facebookers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  fbid bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  username varchar(100) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY fbid (fbid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_failed_transactions_notify'
--

CREATE TABLE IF NOT EXISTS king_failed_transactions_notify (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(30) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_favs'
--

CREATE TABLE IF NOT EXISTS king_favs (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  catid bigint(20) unsigned NOT NULL,
  expires_on bigint(20) unsigned NOT NULL,
  added_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_fb_friends'
--

CREATE TABLE IF NOT EXISTS king_fb_friends (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  friends text NOT NULL,
  update_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_fb_mails'
--

CREATE TABLE IF NOT EXISTS king_fb_mails (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(100) NOT NULL,
  `to` bigint(20) unsigned NOT NULL,
  sub varchar(100) NOT NULL,
  msg text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  sent_time bigint(20) unsigned NOT NULL,
  expires_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_featured_mails'
--

CREATE TABLE IF NOT EXISTS king_featured_mails (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  url char(15) NOT NULL,
  items text NOT NULL,
  brands text NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_feedback'
--

CREATE TABLE IF NOT EXISTS king_feedback (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  email varchar(100) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franchisee'
--

CREATE TABLE IF NOT EXISTS king_franchisee (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  email varchar(150) NOT NULL,
  balance int(10) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  address text NOT NULL,
  city varchar(120) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franch_marks'
--

CREATE TABLE IF NOT EXISTS king_franch_marks (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL,
  franid bigint(20) unsigned NOT NULL,
  mark int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franch_transactions'
--

CREATE TABLE IF NOT EXISTS king_franch_transactions (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  franid bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  withdrawal int(10) unsigned NOT NULL,
  deposit int(10) unsigned NOT NULL,
  balance int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples'
--

CREATE TABLE IF NOT EXISTS king_freesamples (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  min int(10) unsigned NOT NULL,
  pic varchar(100) NOT NULL,
  available tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples_config'
--

CREATE TABLE IF NOT EXISTS king_freesamples_config (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  min int(10) unsigned NOT NULL,
  `limit` int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples_order'
--

CREATE TABLE IF NOT EXISTS king_freesamples_order (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(20) NOT NULL,
  fsid int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  invoice_no bigint(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_hoteldeals'
--

CREATE TABLE IF NOT EXISTS king_hoteldeals (
  dealid bigint(20) unsigned NOT NULL,
  address text NOT NULL,
  latlong varchar(25) NOT NULL,
  phone varchar(200) NOT NULL,
  email varchar(150) NOT NULL,
  city varchar(30) NOT NULL,
  heading varchar(150) NOT NULL,
  tagline varchar(200) NOT NULL,
  amenities char(19) NOT NULL,
  PRIMARY KEY (dealid)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_interested_products'
--

CREATE TABLE IF NOT EXISTS king_interested_products (
  id int(11) NOT NULL AUTO_INCREMENT,
  product varchar(150) NOT NULL,
  `name` varchar(100) NOT NULL,
  contact varchar(150) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_invoice'
--

CREATE TABLE IF NOT EXISTS king_invoice (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  invoice_no bigint(20) unsigned NOT NULL,
  transid char(18) NOT NULL,
  order_id bigint(20) unsigned NOT NULL,
  mrp int(10) unsigned NOT NULL,
  discount decimal(10,2) unsigned NOT NULL,
  invoice_qty int(5) DEFAULT '0',
  nlc decimal(10,2) unsigned NOT NULL,
  credit_note_id bigint(11) DEFAULT '0',
  credit_note_amt double DEFAULT '0',
  phc decimal(10,2) unsigned NOT NULL,
  tax double unsigned NOT NULL,
  service_tax double NOT NULL,
  cod double unsigned NOT NULL,
  ship double unsigned NOT NULL,
  giftwrap_charge double DEFAULT '0',
  invoice_status tinyint(1) DEFAULT '0',
  is_returned tinyint(1) DEFAULT '0',
  createdon bigint(20) DEFAULT NULL,
  cancelled_on bigint(20) DEFAULT NULL,
  delivery_medium varchar(255) DEFAULT '0',
  tracking_id varchar(50) DEFAULT '0',
  shipdatetime datetime DEFAULT NULL,
  notify_customer tinyint(1) DEFAULT '0',
  is_delivered tinyint(1) DEFAULT '0',
  is_partial_invoice tinyint(1) DEFAULT '0',
  is_printed tinyint(1) DEFAULT '0',
  total_prints int(5) DEFAULT '0',
  last_printedon datetime DEFAULT NULL,
  outscanned_on bigint(20) DEFAULT NULL,
  is_outscanned tinyint(1) DEFAULT '0',
  is_b2b tinyint(1) NOT NULL,
  old_pnh_inv_no bigint(20) DEFAULT '0',
  new_pnh_inv_no bigint(20) DEFAULT '0',
  split_inv_grpno bigint(20) DEFAULT '0',
  ref_dispatch_id bigint(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY order_id (order_id),
  KEY invoice_no (invoice_no),
  KEY ref_dispatch_id (ref_dispatch_id),
  KEY split_inv_grpno (split_inv_grpno)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_invoice_prints'
--

CREATE TABLE IF NOT EXISTS king_invoice_prints (
  id int(11) NOT NULL AUTO_INCREMENT,
  invoice_no varchar(50) DEFAULT NULL,
  printed_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_item_lovers'
--

CREATE TABLE IF NOT EXISTS king_item_lovers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_lookingto'
--

CREATE TABLE IF NOT EXISTS king_lookingto (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  product text NOT NULL,
  whenbuy varchar(100) NOT NULL,
  uids text NOT NULL,
  emails text NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_mail_providers'
--

CREATE TABLE IF NOT EXISTS king_mail_providers (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_menu'
--

CREATE TABLE IF NOT EXISTS king_menu (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  url varchar(150) NOT NULL,
  prepos varchar(25) NOT NULL DEFAULT 'for',
  tagline varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  priority smallint(5) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY url (url)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_miscusers'
--

CREATE TABLE IF NOT EXISTS king_miscusers (
  userid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  logins int(10) unsigned NOT NULL,
  lastlogin datetime NOT NULL,
  invites int(10) unsigned NOT NULL,
  viewdeals varchar(100) NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_m_buyprocess'
--

CREATE TABLE IF NOT EXISTS king_m_buyprocess (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  quantity int(10) unsigned NOT NULL,
  quantity_done int(10) unsigned NOT NULL,
  refund int(10) unsigned NOT NULL,
  refund_given int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  expires_on bigint(20) unsigned NOT NULL,
  started_by bigint(20) unsigned NOT NULL,
  started_on bigint(20) unsigned NOT NULL,
  refund_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_newsletters'
--

CREATE TABLE IF NOT EXISTS king_newsletters (
  id int(11) NOT NULL AUTO_INCREMENT,
  campaign_no varchar(255) DEFAULT NULL,
  item_id varchar(255) DEFAULT NULL,
  template_type varchar(255) DEFAULT NULL,
  banner_image varchar(255) DEFAULT 'site_banner.png',
  is_active int(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_orders'
--

CREATE TABLE IF NOT EXISTS king_orders (
  sno bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  transid char(18) NOT NULL,
  userid int(11) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  vendorid bigint(20) unsigned NOT NULL,
  bill_person varchar(100) NOT NULL,
  bill_address text NOT NULL,
  bill_city text NOT NULL,
  bill_pincode varchar(20) NOT NULL,
  ship_person varchar(100) NOT NULL,
  ship_address text NOT NULL,
  ship_city text NOT NULL,
  ship_pincode varchar(20) NOT NULL,
  bill_phone varchar(50) NOT NULL,
  ship_phone varchar(50) NOT NULL,
  bill_state varchar(100) NOT NULL,
  ship_state varchar(100) NOT NULL,
  ship_email varchar(150) NOT NULL,
  bill_email varchar(150) NOT NULL,
  quantity int(10) unsigned NOT NULL,
  paid int(10) unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL COMMENT '0 - PG (cc,netbanking), 1 - cod',
  `status` tinyint(4) NOT NULL,
  admin_order_status tinyint(3) DEFAULT '0',
  shipped tinyint(1) NOT NULL,
  buyer_options text NOT NULL,
  `time` bigint(20) NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  shiptime bigint(20) unsigned NOT NULL,
  shipid varchar(50) NOT NULL,
  `medium` varchar(100) NOT NULL,
  bpid bigint(20) unsigned NOT NULL,
  email varchar(100) NOT NULL,
  ship_landmark text NOT NULL,
  bill_landmark text NOT NULL,
  ship_telephone varchar(50) NOT NULL,
  ship_country varchar(255) DEFAULT NULL,
  bill_country varchar(255) DEFAULT NULL,
  bill_telephone varchar(50) NOT NULL,
  invoice_no bigint(20) unsigned NOT NULL,
  priority tinyint(1) NOT NULL,
  priority_note varchar(200) NOT NULL,
  note text NOT NULL,
  billon_orderprice tinyint(1) DEFAULT '0',
  i_orgprice double DEFAULT '0',
  i_price double DEFAULT '0',
  i_nlc double DEFAULT '0',
  i_phc double DEFAULT '0',
  i_tax double DEFAULT '0',
  i_discount double DEFAULT '0',
  i_coup_discount double DEFAULT '0',
  redeem_value float DEFAULT '1',
  i_discount_applied_on double DEFAULT '0',
  giftwrap_order tinyint(1) DEFAULT '0',
  is_giftcard tinyint(1) DEFAULT '0',
  gc_recp_name varchar(255) DEFAULT NULL,
  gc_recp_email varchar(255) DEFAULT NULL,
  gc_recp_mobile varchar(50) DEFAULT NULL,
  gc_recp_msg text,
  order_status_backup tinyint(1) DEFAULT NULL,
  has_super_scheme tinyint(1) DEFAULT '0',
  super_scheme_logid int(11) DEFAULT '0',
  super_scheme_target int(11) DEFAULT '0',
  super_scheme_cashback double DEFAULT '0',
  super_scheme_processed tinyint(1) DEFAULT '0',
  imei_reimbursement_value_perunit double(10,2) DEFAULT '0.00',
  imei_scheme_id bigint(20) DEFAULT '0',
  member_scheme_processed tinyint(11) DEFAULT '0',
  member_id bigint(11) DEFAULT '0',
  is_ordqty_splitd tinyint(1) DEFAULT '0',
  has_offer tinyint(1) DEFAULT '0',
  offer_refid bigint(11) DEFAULT '0',
  partner_order_id varchar(30) DEFAULT NULL,
  PRIMARY KEY (sno),
  KEY transid (transid),
  KEY itemid (itemid),
  KEY userid (userid),
  KEY id (id),
  KEY imei_scheme_id (imei_scheme_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_orders_bak'
--

CREATE TABLE IF NOT EXISTS king_orders_bak (
  sno bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  transid char(18) NOT NULL,
  userid int(11) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  vendorid bigint(20) unsigned NOT NULL,
  bill_person varchar(100) NOT NULL,
  bill_address text NOT NULL,
  bill_city text NOT NULL,
  bill_pincode varchar(20) NOT NULL,
  ship_person varchar(100) NOT NULL,
  ship_address text NOT NULL,
  ship_city text NOT NULL,
  ship_pincode varchar(20) NOT NULL,
  bill_phone varchar(20) NOT NULL,
  ship_phone varchar(20) NOT NULL,
  bill_state varchar(50) NOT NULL,
  ship_state varchar(50) NOT NULL,
  ship_email varchar(150) NOT NULL,
  bill_email varchar(150) NOT NULL,
  quantity int(10) unsigned NOT NULL,
  paid int(10) unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL COMMENT '0 - PG (cc,netbanking), 1 - cod',
  `status` tinyint(4) NOT NULL,
  shipped tinyint(1) NOT NULL,
  buyer_options text NOT NULL,
  `time` bigint(20) NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  shiptime bigint(20) unsigned NOT NULL,
  shipid varchar(50) NOT NULL,
  `medium` varchar(100) NOT NULL,
  bpid bigint(20) unsigned NOT NULL,
  email varchar(100) NOT NULL,
  ship_landmark text NOT NULL,
  bill_landmark text NOT NULL,
  ship_telephone varchar(30) NOT NULL,
  bill_telephone varchar(30) NOT NULL,
  invoice_no bigint(20) unsigned NOT NULL,
  priority tinyint(1) NOT NULL,
  priority_note varchar(200) NOT NULL,
  note text NOT NULL,
  i_orgprice double DEFAULT '0',
  i_price double DEFAULT '0',
  i_nlc double DEFAULT '0',
  i_phc double DEFAULT '0',
  i_tax double DEFAULT '0',
  i_discount double DEFAULT '0',
  i_coup_discount double DEFAULT '0',
  i_discount_applied_on double DEFAULT '0',
  PRIMARY KEY (sno),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_order_statuslog'
--

CREATE TABLE IF NOT EXISTS king_order_statuslog (
  id int(11) NOT NULL AUTO_INCREMENT,
  reference_trans_id varchar(30) DEFAULT NULL,
  transid varchar(30) DEFAULT NULL,
  order_id varchar(30) DEFAULT NULL,
  invoice_no varchar(30) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  logged_on bigint(20) DEFAULT NULL,
  message text,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_password_forgot'
--

CREATE TABLE IF NOT EXISTS king_password_forgot (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_pending_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_pending_cashbacks (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(12) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  min int(10) unsigned NOT NULL,
  expires bigint(20) unsigned NOT NULL,
  transid varchar(60) NOT NULL,
  orderid bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY `code` (`code`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points'
--

CREATE TABLE IF NOT EXISTS king_points (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  transid varchar(20) NOT NULL,
  points int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points_sys'
--

CREATE TABLE IF NOT EXISTS king_points_sys (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  amount int(10) unsigned NOT NULL,
  points int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points_track'
--

CREATE TABLE IF NOT EXISTS king_points_track (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  coupon char(15) NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_pricereqs'
--

CREATE TABLE IF NOT EXISTS king_pricereqs (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  price int(10) unsigned NOT NULL,
  aprice int(10) unsigned NOT NULL,
  quantity int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  url char(32) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_product_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_product_cashbacks (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  valid int(10) unsigned NOT NULL,
  min_order int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_profiles'
--

CREATE TABLE IF NOT EXISTS king_profiles (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  corpid bigint(20) unsigned NOT NULL,
  pic char(12) NOT NULL,
  designation varchar(100) NOT NULL DEFAULT 'not available',
  department varchar(100) NOT NULL,
  location varchar(150) NOT NULL,
  employee_no varchar(50) NOT NULL,
  desk_no varchar(50) NOT NULL,
  linkedin varchar(150) NOT NULL,
  facebook varchar(150) NOT NULL,
  twitter varchar(150) NOT NULL,
  products int(10) unsigned NOT NULL,
  reviews int(10) unsigned NOT NULL,
  lastbuy bigint(20) unsigned NOT NULL,
  lastbuy_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_referral_coupon_track'
--

CREATE TABLE IF NOT EXISTS king_referral_coupon_track (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  referral bigint(20) unsigned NOT NULL,
  coupon char(13) NOT NULL,
  transid varchar(20) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  ncoupon char(14) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_refunds'
--

CREATE TABLE IF NOT EXISTS king_refunds (
  id int(11) NOT NULL AUTO_INCREMENT,
  transid varchar(30) DEFAULT NULL,
  order_ids varchar(50) DEFAULT NULL,
  notify_customer tinyint(1) DEFAULT '0',
  notification_sent text,
  amount double DEFAULT NULL,
  tracking_id varchar(50) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_remindme'
--

CREATE TABLE IF NOT EXISTS king_remindme (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(100) NOT NULL,
  mobile varchar(12) NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_resources'
--

CREATE TABLE IF NOT EXISTS king_resources (
  dealid bigint(20) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  id varchar(200) NOT NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_reviews'
--

CREATE TABLE IF NOT EXISTS king_reviews (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  title varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  rating tinyint(3) unsigned NOT NULL,
  review text NOT NULL,
  `first` tinyint(1) NOT NULL,
  buyer tinyint(1) NOT NULL,
  thumbs_up int(10) unsigned NOT NULL,
  thumbs_down int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_review_thumbs'
--

CREATE TABLE IF NOT EXISTS king_review_thumbs (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  rid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  yes tinyint(1) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  points int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_roomdeals'
--

CREATE TABLE IF NOT EXISTS king_roomdeals (
  roomid bigint(20) unsigned NOT NULL,
  dealid bigint(20) unsigned NOT NULL,
  heading varchar(200) NOT NULL,
  tagline varchar(200) NOT NULL,
  availability text NOT NULL,
  PRIMARY KEY (roomid)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_savedcartitems'
--

CREATE TABLE IF NOT EXISTS king_savedcartitems (
  cartid bigint(20) unsigned NOT NULL,
  itemid bigint(20) NOT NULL,
  quantity int(11) NOT NULL,
  KEY cartid (cartid)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_savedcarts'
--

CREATE TABLE IF NOT EXISTS king_savedcarts (
  cartid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (cartid)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_search_index'
--

CREATE TABLE IF NOT EXISTS king_search_index (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  keywords varchar(150) NOT NULL,
  PRIMARY KEY (id),
  FULLTEXT KEY keywords (`name`,keywords)
) TYPE=MyISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'king_search_log'
--

CREATE TABLE IF NOT EXISTS king_search_log (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(150) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_shipment_update_filedata'
--

CREATE TABLE IF NOT EXISTS king_shipment_update_filedata (
  id int(11) NOT NULL AUTO_INCREMENT,
  uniq_id bigint(20) DEFAULT NULL,
  file_name varchar(255) DEFAULT NULL,
  invoice_no varchar(50) DEFAULT NULL,
  awb_no varchar(50) DEFAULT NULL,
  courier_name varchar(255) DEFAULT NULL,
  ship_date date DEFAULT NULL,
  notify_customer tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  logged_on datetime DEFAULT NULL,
  processed_on datetime DEFAULT NULL,
  message varchar(2024) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_specialusers'
--

CREATE TABLE IF NOT EXISTS king_specialusers (
  userid bigint(20) unsigned NOT NULL,
  suid varchar(100) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_stock'
--

CREATE TABLE IF NOT EXISTS king_stock (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  available int(10) unsigned NOT NULL,
  ins int(10) unsigned NOT NULL,
  outs int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_stock_activity'
--

CREATE TABLE IF NOT EXISTS king_stock_activity (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  stockids text NOT NULL,
  `type` tinyint(1) NOT NULL,
  remarks text NOT NULL,
  reference_no varchar(100) NOT NULL,
  purchase_date varchar(20) NOT NULL,
  vendor varchar(100) NOT NULL,
  amount int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_subscr_email'
--

CREATE TABLE IF NOT EXISTS king_subscr_email (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(100) NOT NULL,
  PRIMARY KEY (id),
  KEY email (email)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_subscr_mobile'
--

CREATE TABLE IF NOT EXISTS king_subscr_mobile (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  mobile char(12) NOT NULL,
  PRIMARY KEY (id),
  KEY mobile (mobile)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_sub_invoice'
--

CREATE TABLE IF NOT EXISTS king_sub_invoice (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  invoice_no bigint(20) unsigned NOT NULL,
  sub int(10) unsigned NOT NULL,
  orders text NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_supplier_contacts'
--

CREATE TABLE IF NOT EXISTS king_supplier_contacts (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  business varchar(150) NOT NULL,
  contact_number varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  location varchar(50) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tags'
--

CREATE TABLE IF NOT EXISTS king_tags (
  tid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  src_url varchar(200) NOT NULL,
  pic char(20) NOT NULL,
  retags int(10) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (tid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tags_in_boards'
--

CREATE TABLE IF NOT EXISTS king_tags_in_boards (
  tbid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  bid int(10) unsigned NOT NULL,
  tid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  url varchar(60) NOT NULL,
  `from` bigint(20) unsigned NOT NULL,
  comments int(10) unsigned NOT NULL,
  loves int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (tbid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tag_comments'
--

CREATE TABLE IF NOT EXISTS king_tag_comments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  tbid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `comment` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tag_lovers'
--

CREATE TABLE IF NOT EXISTS king_tag_lovers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  tbid bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tmp_orders'
--

CREATE TABLE IF NOT EXISTS king_tmp_orders (
  sno bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  transid char(18) NOT NULL,
  userid int(11) unsigned NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  vendorid bigint(20) unsigned NOT NULL,
  bill_person varchar(100) NOT NULL,
  bill_address text NOT NULL,
  bill_city text NOT NULL,
  bill_pincode varchar(20) NOT NULL,
  ship_person varchar(100) NOT NULL,
  ship_address text NOT NULL,
  ship_city text NOT NULL,
  ship_pincode varchar(20) NOT NULL,
  bill_phone varchar(20) NOT NULL,
  ship_phone varchar(20) NOT NULL,
  bill_state varchar(100) NOT NULL,
  ship_state varchar(100) NOT NULL,
  ship_email varchar(150) NOT NULL,
  bill_email varchar(150) NOT NULL,
  quantity int(10) unsigned NOT NULL,
  amount int(10) unsigned NOT NULL,
  bpid bigint(20) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  buyer_options text NOT NULL,
  `time` bigint(20) NOT NULL,
  actiontime bigint(20) unsigned NOT NULL,
  shiptime bigint(20) unsigned NOT NULL,
  shipid varchar(50) NOT NULL,
  `medium` varchar(100) NOT NULL,
  bill_landmark text NOT NULL,
  ship_landmark text NOT NULL,
  bill_telephone varchar(30) NOT NULL,
  ship_telephone varchar(30) NOT NULL,
  ship_country varchar(255) DEFAULT NULL,
  bill_country varchar(255) DEFAULT NULL,
  i_orgprice double DEFAULT '0',
  i_price double DEFAULT '0',
  i_nlc double DEFAULT '0',
  i_phc double DEFAULT '0',
  i_tax double DEFAULT '0',
  i_discount double DEFAULT '0',
  i_coup_discount double DEFAULT '0',
  i_discount_applied_on double DEFAULT '0',
  giftwrap_order tinyint(1) DEFAULT '0',
  is_giftcard tinyint(1) DEFAULT '0',
  gc_recp_name varchar(255) DEFAULT NULL,
  gc_recp_email varchar(255) DEFAULT NULL,
  gc_recp_mobile varchar(50) DEFAULT NULL,
  gc_recp_msg text,
  user_note text,
  partner_order_id varchar(30) DEFAULT NULL,
  partner_reference_no varchar(100) DEFAULT NULL,
  PRIMARY KEY (sno),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transactions'
--

CREATE TABLE IF NOT EXISTS king_transactions (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  transid char(18) NOT NULL,
  orderid bigint(20) unsigned NOT NULL,
  amount double unsigned NOT NULL,
  paid double unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL,
  voucher_payment tinyint(3) DEFAULT '0',
  cod double unsigned NOT NULL,
  ship double unsigned NOT NULL,
  giftwrap_charge double DEFAULT '0',
  response_code int(10) unsigned NOT NULL,
  msg text NOT NULL,
  payment_id varchar(50) NOT NULL,
  pg_transaction_id varchar(50) NOT NULL,
  is_flagged varchar(10) NOT NULL,
  init bigint(20) unsigned NOT NULL,
  actiontime int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  is_pnh tinyint(1) NOT NULL,
  franchise_id int(10) unsigned NOT NULL,
  batch_enabled tinyint(1) NOT NULL DEFAULT '1',
  admin_trans_status tinyint(3) DEFAULT '0',
  priority tinyint(1) NOT NULL,
  priority_note varchar(200) NOT NULL,
  note text NOT NULL,
  offline tinyint(1) NOT NULL,
  status_backup tinyint(1) DEFAULT NULL,
  partner_reference_no varchar(30) NOT NULL,
  partner_id int(10) unsigned NOT NULL,
  trans_created_by int(11) DEFAULT '0',
  trans_grp_ref_no bigint(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY franchise_id (franchise_id),
  KEY trans_created_by (trans_created_by)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transaction_activity'
--

CREATE TABLE IF NOT EXISTS king_transaction_activity (
  id int(11) NOT NULL AUTO_INCREMENT,
  reference_trans_id varchar(30) DEFAULT NULL,
  message text,
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transaction_notes'
--

CREATE TABLE IF NOT EXISTS king_transaction_notes (
  id int(11) NOT NULL AUTO_INCREMENT,
  transid varchar(30) DEFAULT NULL,
  order_id varchar(30) DEFAULT NULL,
  note text,
  `status` tinyint(1) DEFAULT '0',
  note_priority tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_trends'
--

CREATE TABLE IF NOT EXISTS king_trends (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  hits int(10) unsigned NOT NULL,
  deals text NOT NULL,
  listed_on bigint(20) unsigned NOT NULL,
  updated_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY `name` (`name`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_used_coupons'
--

CREATE TABLE IF NOT EXISTS king_used_coupons (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  coupon char(12) NOT NULL,
  transid char(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (id),
  KEY coupon (coupon),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_userlog'
--

CREATE TABLE IF NOT EXISTS king_userlog (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  userid bigint(20) unsigned NOT NULL,
  ip varchar(50) NOT NULL,
  last_login bigint(20) unsigned NOT NULL,
  ip_time_data text NOT NULL,
  useragent varchar(60) NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_users'
--

CREATE TABLE IF NOT EXISTS king_users (
  userid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  mobile bigint(11) unsigned NOT NULL,
  corpemail varchar(100) NOT NULL,
  corpid int(10) unsigned NOT NULL,
  balance int(10) unsigned NOT NULL,
  inviteid char(10) NOT NULL,
  friendof bigint(20) unsigned NOT NULL,
  special tinyint(1) NOT NULL,
  special_id varchar(30) NOT NULL,
  address text NOT NULL,
  landmark text NOT NULL,
  telephone varchar(30) NOT NULL,
  country varchar(255) DEFAULT NULL,
  city varchar(100) NOT NULL,
  state varchar(100) NOT NULL,
  pincode varchar(100) NOT NULL,
  `block` tinyint(1) NOT NULL,
  verified int(10) unsigned NOT NULL,
  verify_code char(10) NOT NULL,
  optin tinyint(1) NOT NULL DEFAULT '1',
  points int(10) unsigned NOT NULL,
  temperament tinyint(1) unsigned NOT NULL DEFAULT '2',
  is_pnh tinyint(1) NOT NULL,
  createdon bigint(20) unsigned NOT NULL DEFAULT '1275750318',
  PRIMARY KEY (userid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_vars'
--

CREATE TABLE IF NOT EXISTS king_vars (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_vendors'
--

CREATE TABLE IF NOT EXISTS king_vendors (
  sno int(10) unsigned NOT NULL AUTO_INCREMENT,
  id bigint(20) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  created_date bigint(20) unsigned NOT NULL,
  address text NOT NULL,
  email varchar(200) NOT NULL,
  telephone varchar(200) NOT NULL,
  mobile varchar(100) NOT NULL,
  description text NOT NULL,
  contact varchar(200) NOT NULL,
  PRIMARY KEY (sno,id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_widgets'
--

CREATE TABLE IF NOT EXISTS king_widgets (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  salt char(32) NOT NULL,
  `type` int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'menu_class_config'
--

CREATE TABLE IF NOT EXISTS menu_class_config (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  menu_id bigint(20) DEFAULT NULL,
  class_id int(11) DEFAULT NULL,
  percentage double DEFAULT NULL,
  is_active tinyint(4) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_brand_config_map_price'
--

CREATE TABLE IF NOT EXISTS m_brand_config_map_price (
  id int(11) NOT NULL AUTO_INCREMENT,
  menuid int(11) DEFAULT '0',
  brandid bigint(11) DEFAULT '0',
  catid bigint(11) DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM  COMMENT='Table to configure minimum applicable price to be considered';

-- --------------------------------------------------------

--
-- Table structure for table 'm_brand_location_link'
--

CREATE TABLE IF NOT EXISTS m_brand_location_link (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  brand_id bigint(11) DEFAULT NULL,
  default_location_id int(11) DEFAULT NULL,
  default_rack_bin_id int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brand_id (brand_id),
  KEY default_location_id (default_location_id),
  KEY default_rack_bin_id (default_rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_client_contacts_info'
--

CREATE TABLE IF NOT EXISTS m_client_contacts_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  client_id int(11) DEFAULT NULL,
  contact_name varchar(200) DEFAULT NULL,
  contact_designation varchar(200) DEFAULT NULL,
  mobile_no_1 varchar(10) DEFAULT NULL,
  mobile_no_2 varchar(10) DEFAULT NULL,
  telephone_no varchar(150) DEFAULT NULL,
  email_id_1 varchar(200) DEFAULT NULL,
  email_id_2 varchar(200) DEFAULT NULL,
  fax_no varchar(120) DEFAULT NULL,
  active_status int(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY client_id (client_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_client_info'
--

CREATE TABLE IF NOT EXISTS m_client_info (
  client_id int(11) NOT NULL AUTO_INCREMENT,
  client_code varchar(30) DEFAULT NULL,
  client_name varchar(255) DEFAULT NULL,
  address_line1 varchar(255) DEFAULT NULL,
  address_line2 varchar(255) DEFAULT NULL,
  locality varchar(200) DEFAULT NULL,
  landmark varchar(200) DEFAULT NULL,
  postcode varchar(10) DEFAULT NULL,
  city_name varchar(150) DEFAULT NULL,
  state_name varchar(150) DEFAULT NULL,
  country varchar(150) DEFAULT NULL,
  credit_limit_amount double DEFAULT '0',
  credit_days int(11) DEFAULT '0',
  cst_no varchar(100) DEFAULT NULL,
  pan_no varchar(100) DEFAULT NULL,
  vat_no varchar(100) DEFAULT NULL,
  service_tax_no varchar(100) DEFAULT NULL,
  remarks varchar(2000) DEFAULT NULL,
  logo varchar(255) DEFAULT NULL,
  active_status int(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (client_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_config_params'
--

CREATE TABLE IF NOT EXISTS m_config_params (
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_config_statusflags'
--

CREATE TABLE IF NOT EXISTS m_config_statusflags (
  id int(11) NOT NULL AUTO_INCREMENT,
  flag_for varchar(255) DEFAULT NULL,
  flag_no bigint(11) DEFAULT '0',
  flag_value varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_awb_series'
--

CREATE TABLE IF NOT EXISTS m_courier_awb_series (
  id int(11) NOT NULL AUTO_INCREMENT,
  courier_id int(11) DEFAULT NULL,
  awb_no_prefix varchar(10) DEFAULT NULL,
  awb_no_suffix varchar(10) DEFAULT NULL,
  awb_start_no double DEFAULT '0',
  awb_end_no double DEFAULT '0',
  awb_current_no double DEFAULT '0',
  mode_surface tinyint(1) DEFAULT '0',
  mode_air_cargo tinyint(1) DEFAULT '0',
  mode_air_courier tinyint(1) DEFAULT '0',
  mode_air_rail tinyint(1) DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY courier_id (courier_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_flags'
--

CREATE TABLE IF NOT EXISTS m_courier_flags (
  id int(11) NOT NULL AUTO_INCREMENT,
  courier_id int(11) DEFAULT '0',
  statuscode varchar(20) DEFAULT NULL,
  sys_statusflag int(2) DEFAULT '0',
  status_text varchar(1024) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY statuscode (statuscode),
  KEY sys_statusflag (sys_statusflag)
) TYPE=MyISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_info'
--

CREATE TABLE IF NOT EXISTS m_courier_info (
  courier_id int(11) NOT NULL AUTO_INCREMENT,
  courier_name varchar(255) DEFAULT NULL,
  address_line1 varchar(255) DEFAULT NULL,
  address_line2 varchar(255) DEFAULT NULL,
  locality varchar(200) DEFAULT NULL,
  landmark varchar(200) DEFAULT NULL,
  postcode varchar(10) DEFAULT NULL,
  city_id int(11) DEFAULT '0',
  city_name varchar(150) DEFAULT NULL,
  state_name varchar(150) DEFAULT NULL,
  country varchar(150) DEFAULT NULL,
  ledger_id int(11) DEFAULT '0',
  credit_limit_amount double DEFAULT '0',
  credit_days int(11) DEFAULT '0',
  credit_cycle int(11) DEFAULT '0',
  require_payment_advance tinyint(1) DEFAULT '0',
  cod_available tinyint(1) DEFAULT '0',
  mode_air_courier tinyint(1) DEFAULT '0',
  mode_air_cargo tinyint(1) DEFAULT '0',
  mode_surface tinyint(1) DEFAULT '0',
  mode_rail tinyint(1) DEFAULT '0',
  tin_no varchar(100) DEFAULT NULL,
  cst_no varchar(100) DEFAULT NULL,
  pan_no varchar(100) DEFAULT NULL,
  vat_no varchar(100) DEFAULT NULL,
  service_tax_no varchar(100) DEFAULT NULL,
  shipment_update_template_id int(11) DEFAULT '0',
  pincode_list_template_id int(11) DEFAULT '0',
  payment_terms_msg varchar(255) DEFAULT NULL,
  agreement_copy varchar(200) DEFAULT NULL,
  remarks varchar(2000) DEFAULT NULL,
  ref_partner_id int(11) DEFAULT '0',
  is_active int(1) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (courier_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_pincodes'
--

CREATE TABLE IF NOT EXISTS m_courier_pincodes (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  courier_id int(10) unsigned NOT NULL,
  pincode int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  added_on datetime NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_deals_bulk_update'
--

CREATE TABLE IF NOT EXISTS m_deals_bulk_update (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  items int(10) unsigned NOT NULL,
  updated_data text,
  created_on bigint(20) DEFAULT NULL,
  created_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_info'
--

CREATE TABLE IF NOT EXISTS m_employee_info (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  assigned_under int(11) DEFAULT '0',
  user_id int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  fathername varchar(255) DEFAULT NULL,
  mothername varchar(255) DEFAULT NULL,
  dob date DEFAULT NULL,
  qualification varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  gender varchar(255) DEFAULT NULL,
  address text,
  city varchar(255) DEFAULT NULL,
  postcode int(7) DEFAULT NULL,
  contact_no varchar(255) DEFAULT NULL,
  job_title int(11) DEFAULT NULL,
  job_title2 int(11) DEFAULT '0',
  photo_url varchar(255) DEFAULT NULL,
  cv_url varchar(255) DEFAULT NULL,
  send_sms int(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by varchar(255) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by varchar(255) DEFAULT NULL,
  is_suspended tinyint(11) NOT NULL DEFAULT '0',
  suspended_on datetime DEFAULT NULL,
  suspended_by bigint(255) DEFAULT NULL,
  remarks varchar(255) DEFAULT NULL,
  PRIMARY KEY (employee_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_list'
--

CREATE TABLE IF NOT EXISTS m_employee_list (
  id int(11) NOT NULL AUTO_INCREMENT,
  role varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  mobile varchar(50) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_rolelink'
--

CREATE TABLE IF NOT EXISTS m_employee_rolelink (
  id int(11) NOT NULL AUTO_INCREMENT,
  employee_id int(11) DEFAULT NULL,
  parent_emp_id int(11) DEFAULT NULL,
  is_active int(11) DEFAULT NULL,
  assigned_on date DEFAULT NULL,
  modified_on date DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_roles'
--

CREATE TABLE IF NOT EXISTS m_employee_roles (
  role_id int(11) NOT NULL AUTO_INCREMENT,
  role_name varchar(255) NOT NULL,
  short_frm varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  PRIMARY KEY (role_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_manifesto_driver_log'
--

CREATE TABLE IF NOT EXISTS m_manifesto_driver_log (
  id int(100) NOT NULL AUTO_INCREMENT,
  manifesto_id int(100) DEFAULT NULL,
  sent_invoices text,
  remark text,
  role_type varchar(255) DEFAULT NULL,
  other_driver varchar(255) DEFAULT NULL,
  contact_num varchar(255) DEFAULT NULL,
  is_printed int(100) DEFAULT '0',
  driver_id int(100) DEFAULT '0',
  sent_on datetime DEFAULT NULL,
  handle_by varchar(275) DEFAULT NULL,
  created_by int(100) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(100) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_partner_deal_price'
--

CREATE TABLE IF NOT EXISTS m_partner_deal_price (
  id int(11) NOT NULL AUTO_INCREMENT,
  partner_id int(11) DEFAULT NULL,
  itemid double DEFAULT NULL,
  offer_price double DEFAULT NULL,
  partner_price double DEFAULT NULL,
  modified_on bigint(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(11) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_deal_link'
--

CREATE TABLE IF NOT EXISTS m_product_deal_link (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned DEFAULT NULL,
  product_id int(11) unsigned DEFAULT NULL,
  product_mrp decimal(15,4) DEFAULT '0.0000',
  qty int(11) DEFAULT '1',
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  tmp_pnh_itemid bigint(20) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_groups'
--

CREATE TABLE IF NOT EXISTS m_product_groups (
  id int(11) NOT NULL AUTO_INCREMENT,
  group_type varchar(255) DEFAULT 'alternate',
  group_no int(11) DEFAULT '0',
  product_id int(11) DEFAULT '0',
  product_value decimal(15,4) DEFAULT '0.0000',
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_group_deal_link'
--

CREATE TABLE IF NOT EXISTS m_product_group_deal_link (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(11) unsigned DEFAULT NULL,
  group_id int(11) unsigned DEFAULT NULL,
  product_mrp decimal(15,4) DEFAULT '0.0000',
  qty int(11) DEFAULT '1',
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY group_id (group_id),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_info'
--

CREATE TABLE IF NOT EXISTS m_product_info (
  product_id int(11) NOT NULL AUTO_INCREMENT,
  product_code varchar(15) DEFAULT NULL,
  pid int(10) unsigned NOT NULL,
  product_name varchar(200) DEFAULT NULL,
  short_desc varchar(255) DEFAULT NULL,
  size decimal(15,4) unsigned DEFAULT '0.0000',
  uom varchar(10) DEFAULT NULL COMMENT 'ml, grams, unit',
  weight double DEFAULT '0' COMMENT 'in grams',
  mrp decimal(15,4) unsigned DEFAULT '0.0000',
  vat decimal(7,4) unsigned DEFAULT '0.0000',
  purchase_cost decimal(15,4) unsigned DEFAULT '0.0000' COMMENT 'including purchase tax',
  sku_code varchar(100) DEFAULT NULL,
  barcode varchar(50) DEFAULT NULL,
  is_offer tinyint(1) DEFAULT '0',
  is_serial_required tinyint(1) NOT NULL,
  brand_id bigint(11) DEFAULT '0',
  default_rackbin_id int(11) DEFAULT '0',
  moq int(11) DEFAULT '0',
  reorder_level int(11) DEFAULT '0',
  reorder_qty int(11) DEFAULT '0',
  is_sourceable tinyint(1) DEFAULT '1',
  remarks varchar(255) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  tmp_itemid double DEFAULT '0',
  tmp_dealid double DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  corr_status tinyint(1) DEFAULT '0',
  corr_updated_on datetime DEFAULT NULL,
  PRIMARY KEY (product_id),
  KEY pid (pid),
  KEY brand_id (brand_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_update_log'
--

CREATE TABLE IF NOT EXISTS m_product_update_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  message text,
  logged_by int(11) DEFAULT '0',
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_rack_bin_brand_link'
--

CREATE TABLE IF NOT EXISTS m_rack_bin_brand_link (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  rack_bin_id int(10) unsigned NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY rack_bin_id (rack_bin_id),
  KEY brandid (brandid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_rack_bin_info'
--

CREATE TABLE IF NOT EXISTS m_rack_bin_info (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  location_id int(11) DEFAULT NULL,
  rack_name varchar(100) DEFAULT NULL,
  bin_name varchar(100) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY location_id (location_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_storage_location_info'
--

CREATE TABLE IF NOT EXISTS m_storage_location_info (
  location_id int(11) NOT NULL AUTO_INCREMENT,
  location_name varchar(255) DEFAULT NULL,
  is_damaged tinyint(1) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (location_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_streams'
--

CREATE TABLE IF NOT EXISTS m_streams (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title varchar(200) NOT NULL,
  description text NOT NULL,
  file_url varchar(255) NOT NULL,
  created_by int(11) NOT NULL DEFAULT '0',
  created_time bigint(20) NOT NULL,
  modified_by varchar(100) DEFAULT '0',
  modified_time bigint(20) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_posts'
--

CREATE TABLE IF NOT EXISTS m_stream_posts (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title varchar(32) DEFAULT NULL,
  description text,
  stream_id bigint(20) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  posted_by int(11) DEFAULT '0',
  posted_on varchar(100) DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on timestamp NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_post_assigned_users'
--

CREATE TABLE IF NOT EXISTS m_stream_post_assigned_users (
  id int(25) NOT NULL AUTO_INCREMENT,
  userid bigint(20) DEFAULT '0',
  post_id bigint(20) DEFAULT '0',
  streamid bigint(20) DEFAULT '0',
  assigned_userid int(11) DEFAULT '0',
  assigned_on varchar(100) DEFAULT NULL,
  viewed tinyint(1) DEFAULT '0',
  active tinyint(1) DEFAULT '1',
  mail_sent tinyint(1) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_post_reply'
--

CREATE TABLE IF NOT EXISTS m_stream_post_reply (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  description text,
  post_id int(25) DEFAULT '0',
  replied_by int(25) DEFAULT '0',
  replied_on varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_users'
--

CREATE TABLE IF NOT EXISTS m_stream_users (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  stream_id int(11) DEFAULT '0',
  user_id int(25) DEFAULT '0',
  access int(1) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT '0',
  created_on varchar(100) DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on varchar(100) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_town_territory_link'
--

CREATE TABLE IF NOT EXISTS m_town_territory_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_emp_id int(11) DEFAULT NULL,
  employee_id int(11) DEFAULT NULL,
  territory_id int(11) DEFAULT NULL,
  town_id int(11) DEFAULT NULL,
  is_active int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_tray_info'
--

CREATE TABLE IF NOT EXISTS m_tray_info (
  tray_id int(11) NOT NULL AUTO_INCREMENT,
  tray_name varchar(255) NOT NULL,
  max_allowed int(5) NOT NULL DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  modified_by int(100) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(100) DEFAULT NULL,
  PRIMARY KEY (tray_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_brand_link'
--

CREATE TABLE IF NOT EXISTS m_vendor_brand_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  brand_id bigint(11) DEFAULT NULL,
  cat_id bigint(11) DEFAULT '0',
  vendor_id int(11) DEFAULT NULL,
  brand_margin decimal(10,4) DEFAULT '10.0000',
  applicable_from bigint(11) unsigned DEFAULT NULL,
  applicable_till bigint(11) unsigned DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  is_default tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brand_id (brand_id),
  KEY vendor_id (vendor_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_contacts_info'
--

CREATE TABLE IF NOT EXISTS m_vendor_contacts_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  vendor_id int(11) DEFAULT NULL,
  contact_name varchar(200) DEFAULT NULL,
  contact_designation varchar(200) DEFAULT NULL,
  mobile_no_1 varchar(10) DEFAULT NULL,
  mobile_no_2 varchar(10) DEFAULT NULL,
  telephone_no varchar(150) DEFAULT NULL,
  email_id_1 varchar(200) DEFAULT NULL,
  email_id_2 varchar(200) DEFAULT NULL,
  fax_no varchar(120) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY vendor_id (vendor_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_info'
--

CREATE TABLE IF NOT EXISTS m_vendor_info (
  vendor_id int(11) NOT NULL AUTO_INCREMENT,
  vendor_code varchar(30) DEFAULT NULL,
  vendor_name varchar(255) DEFAULT NULL,
  address_line1 varchar(255) DEFAULT NULL,
  address_line2 varchar(255) DEFAULT NULL,
  locality varchar(200) DEFAULT NULL,
  landmark varchar(200) DEFAULT NULL,
  postcode varchar(10) DEFAULT NULL,
  city_name varchar(150) DEFAULT NULL,
  state_name varchar(150) DEFAULT NULL,
  country varchar(150) DEFAULT NULL,
  ledger_id int(11) DEFAULT '0',
  credit_limit_amount double DEFAULT '0',
  credit_days int(11) DEFAULT '0',
  require_payment_advance int(11) DEFAULT '0' COMMENT 'store %age of adane for raising po',
  cst_no varchar(100) DEFAULT NULL,
  pan_no varchar(100) DEFAULT NULL,
  vat_no varchar(100) DEFAULT NULL,
  service_tax_no varchar(100) DEFAULT NULL,
  avg_tat int(11) DEFAULT '1',
  return_policy_msg varchar(255) DEFAULT NULL,
  payment_terms_msg varchar(255) DEFAULT NULL,
  agreement_copy_file_name varchar(150) DEFAULT NULL,
  remarks varchar(255) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (vendor_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_product_link'
--

CREATE TABLE IF NOT EXISTS m_vendor_product_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  vendor_id int(11) DEFAULT NULL,
  product_id int(11) DEFAULT NULL,
  vendor_product_code varchar(255) DEFAULT NULL,
  mrp decimal(15,4) DEFAULT NULL,
  purchase_price decimal(15,4) DEFAULT NULL,
  tax decimal(7,4) DEFAULT NULL,
  valid_from date DEFAULT NULL,
  min_order_qty int(11) DEFAULT '0',
  delivery_tat int(11) DEFAULT '1',
  is_default tinyint(1) DEFAULT '0',
  remarks varchar(255) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY vendor_id (vendor_id),
  KEY product_id (product_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_deal_prices'
--

CREATE TABLE IF NOT EXISTS partner_deal_prices (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  partner_id int(10) unsigned NOT NULL,
  customer_price int(10) unsigned NOT NULL,
  partner_price int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  modified_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_info'
--

CREATE TABLE IF NOT EXISTS partner_info (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  trans_prefix char(3) NOT NULL,
  trans_mode tinyint(3) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  modified_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_orders_log'
--

CREATE TABLE IF NOT EXISTS partner_orders_log (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  partner_id int(10) unsigned NOT NULL,
  amount int(10) unsigned NOT NULL,
  amount_paid int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  is_payment_made tinyint(1) NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  modified_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY partner_id (partner_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_order_items'
--

CREATE TABLE IF NOT EXISTS partner_order_items (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  log_id int(11) NOT NULL,
  transid varchar(20) NOT NULL,
  i_customer_price decimal(10,2) NOT NULL,
  i_partner_price decimal(10,2) NOT NULL,
  qty int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_transaction_details'
--

CREATE TABLE IF NOT EXISTS partner_transaction_details (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  partner_id bigint(11) DEFAULT '0',
  transid varchar(30) DEFAULT NULL,
  order_no varchar(255) DEFAULT NULL,
  order_date date DEFAULT NULL,
  net_amt double DEFAULT NULL,
  awb_no varchar(255) DEFAULT NULL,
  courier_name varbinary(255) DEFAULT NULL,
  ship_charges double DEFAULT '0',
  is_manifesto_created tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY partner_id (partner_id),
  KEY transid (transid),
  KEY is_manifesto_created (is_manifesto_created),
  KEY order_no (order_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_app_versions'
--

CREATE TABLE IF NOT EXISTS pnh_app_versions (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  version_no int(10) unsigned NOT NULL,
  version_date bigint(20) unsigned NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY version_no (version_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_bussiness_trip_info'
--

CREATE TABLE IF NOT EXISTS pnh_bussiness_trip_info (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  task_id int(11) DEFAULT NULL,
  f_id int(11) DEFAULT NULL,
  positive_msg varchar(5000) DEFAULT NULL,
  negative_msg varchar(5000) DEFAULT NULL,
  expenses int(11) DEFAULT NULL,
  final_report varchar(5000) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_call_log'
--

CREATE TABLE IF NOT EXISTS pnh_call_log (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  msg varchar(250) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_cash_bill'
--

CREATE TABLE IF NOT EXISTS pnh_cash_bill (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  bill_no int(10) unsigned NOT NULL,
  transid varchar(15) NOT NULL,
  `status` tinyint(1) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id,transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_comp_details'
--

CREATE TABLE IF NOT EXISTS pnh_comp_details (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  pan_no varchar(20) NOT NULL,
  vat_no varchar(20) NOT NULL,
  roc_no varchar(30) NOT NULL,
  toll_free_no varchar(40) NOT NULL,
  sms_no varchar(20) NOT NULL,
  bank_name varchar(100) NOT NULL,
  bank_ac_no varchar(40) NOT NULL,
  bank_ifsc_code varchar(40) NOT NULL,
  bank_ac_name varchar(60) NOT NULL,
  bank_branch_name varchar(70) NOT NULL,
  bank_name2 varchar(100) NOT NULL,
  bank_ac_no2 varchar(40) NOT NULL,
  bank_ifsc_code2 varchar(40) NOT NULL,
  bank_ac_name2 varchar(60) NOT NULL,
  bank_branch_name2 varchar(70) NOT NULL,
  bank_name3 varchar(255) DEFAULT NULL,
  bank_ac_no3 varchar(255) DEFAULT NULL,
  bank_ifsc_code3 varchar(255) DEFAULT NULL,
  bank_ac_name3 varchar(255) DEFAULT NULL,
  bank_branch_name3 varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub (
  id int(11) NOT NULL AUTO_INCREMENT,
  hub_name varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub_fc_link'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub_fc_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  hub_id int(11) DEFAULT '0',
  emp_id int(11) DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY hub_id (hub_id),
  KEY emp_id (emp_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub_town_link'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub_town_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  hub_id int(11) DEFAULT '0',
  town_id int(11) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY town_id (town_id),
  KEY hub_id (hub_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_employee_grpsms_log'
--

CREATE TABLE IF NOT EXISTS pnh_employee_grpsms_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  emp_id int(11) DEFAULT NULL,
  contact_no varchar(25) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT '4:shipments_notification,6:lr_number_updates,8:pickup_manifesto,9:hand_over_to_executive,10:delivery,11:invoice return,12:shipments akwm for tm',
  territory_id int(11) DEFAULT NULL,
  town_id int(11) DEFAULT NULL,
  grp_msg text,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_executive_accounts_log'
--

CREATE TABLE IF NOT EXISTS pnh_executive_accounts_log (
  id int(11) NOT NULL AUTO_INCREMENT,
  emp_id int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT 'PAID',
  msg varchar(512) DEFAULT NULL,
  reciept_status int(11) DEFAULT NULL,
  remarks varchar(512) DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  is_ticket_created tinyint(11) DEFAULT '0',
  updated_by int(11) DEFAULT NULL,
  updated_on datetime DEFAULT NULL,
  sender varchar(15) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_executive_sms_log'
--

CREATE TABLE IF NOT EXISTS pnh_executive_sms_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  emp_id bigint(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT 'PAID',
  msg varchar(512) DEFAULT NULL,
  receipt_status tinyint(1) DEFAULT '0',
  remarks text,
  logged_on datetime DEFAULT NULL,
  updated_by bigint(11) DEFAULT NULL,
  updated_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_stat'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_stat (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  amount double unsigned NOT NULL,
  balance_after double NOT NULL,
  `desc` varchar(250) NOT NULL,
  action_for varchar(255) DEFAULT NULL,
  ref_id varchar(80) DEFAULT NULL,
  created_on bigint(20) unsigned NOT NULL,
  is_correction tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_summary'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_summary (
  statement_id bigint(11) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT '0',
  action_type tinyint(1) DEFAULT '0' COMMENT '1:Sales Invoice 2:Deposti Receipt 3:Receipt 4:Membership 5:A/C Correction',
  acc_correc_id bigint(11) DEFAULT '0',
  member_id bigint(11) DEFAULT '0',
  invoice_no bigint(11) DEFAULT '0',
  credit_note_id bigint(11) DEFAULT '0',
  receipt_id bigint(11) DEFAULT '0',
  receipt_type tinyint(1) DEFAULT '0',
  cheque_no varchar(30) DEFAULT '0',
  debit_amt double DEFAULT '0',
  is_returned tinyint(1) DEFAULT '0',
  credit_amt double DEFAULT '0',
  remarks text,
  `status` int(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT '0',
  PRIMARY KEY (statement_id),
  KEY franchise_id (franchise_id),
  KEY invoice_no (invoice_no),
  KEY acc_correc_id (invoice_no)
) TYPE=MyISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_summary_copy'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_summary_copy (
  statement_id bigint(11) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT '0',
  action_type tinyint(1) DEFAULT '0' COMMENT '1:Sales Invoice 2:Deposti Receipt 3:Receipt 4:Membership 5:A/C Correction',
  acc_correc_id bigint(11) DEFAULT '0',
  member_id bigint(11) DEFAULT '0',
  invoice_no bigint(11) DEFAULT '0',
  receipt_id bigint(11) DEFAULT '0',
  receipt_type tinyint(1) DEFAULT '0',
  cheque_no varchar(30) DEFAULT '0',
  debit_amt double DEFAULT '0',
  credit_amt double DEFAULT '0',
  remarks text,
  `status` int(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT '0',
  PRIMARY KEY (statement_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_bank_details'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_bank_details (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  bank_name varchar(100) NOT NULL,
  ifsc_code varchar(100) NOT NULL,
  branch_name varchar(150) NOT NULL,
  account_no varchar(50) NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_menu_link'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_menu_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  fid bigint(200) DEFAULT NULL,
  menuid int(3) DEFAULT '0',
  `status` int(11) DEFAULT NULL,
  is_sch_enabled tinyint(1) DEFAULT '0',
  sch_discount_start bigint(20) DEFAULT '0',
  sch_discount_end bigint(20) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  sch_discount int(3) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_owners'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_owners (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  admin int(10) unsigned NOT NULL,
  franchise_id int(10) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_photos'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_photos (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id bigint(20) unsigned NOT NULL,
  pic char(20) NOT NULL,
  caption varchar(150) NOT NULL,
  is_deleted tinyint(1) NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_prepaid_log'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_prepaid_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT '0',
  is_prepaid tinyint(3) DEFAULT '0',
  reason text,
  created_on bigint(11) DEFAULT '0',
  created_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_unorderd_log'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_unorderd_log (
  id int(11) NOT NULL AUTO_INCREMENT,
  franchise_id int(11) DEFAULT NULL,
  last_orderd datetime DEFAULT NULL,
  is_notify int(11) DEFAULT NULL,
  msg varchar(255) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_return'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_return (
  id int(11) NOT NULL AUTO_INCREMENT,
  franchise_id int(25) DEFAULT NULL,
  invoice_no int(25) DEFAULT NULL,
  logged_by int(25) DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns (
  return_id bigint(11) NOT NULL AUTO_INCREMENT,
  invoice_no varchar(50) DEFAULT NULL,
  return_by varchar(255) DEFAULT NULL,
  handled_by bigint(11) DEFAULT NULL,
  total_items int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  order_from tinyint(1) DEFAULT '0' COMMENT '0:pnh,1:sit,2:partners',
  returned_on datetime DEFAULT NULL,
  PRIMARY KEY (return_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_flags'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_flags (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_product_link'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_product_link (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  return_id bigint(11) DEFAULT '0',
  order_id bigint(11) DEFAULT '0',
  product_id bigint(11) DEFAULT '0',
  qty double DEFAULT '0',
  barcode varchar(100) DEFAULT NULL,
  imei_no varchar(100) DEFAULT NULL,
  condition_type int(1) DEFAULT '0' COMMENT '1: ''Good Condition'' 2:''Duplicate product'' 3:''UnOrdered'' 4:''Late Shipment'' 5:''Address not found'' 6:''Faulty and needs service''',
  is_shipped tinyint(1) DEFAULT '0',
  is_packed tinyint(1) DEFAULT '0',
  readytoship tinyint(1) DEFAULT '0',
  is_stocked tinyint(1) DEFAULT '0',
  is_refunded tinyint(1) DEFAULT '0',
  shipped_on datetime DEFAULT NULL,
  stock_updated_on datetime DEFAULT NULL,
  refunded_on datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_product_service'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_product_service (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  return_prod_id bigint(11) DEFAULT NULL,
  sent_on datetime DEFAULT NULL,
  sent_to varchar(255) DEFAULT NULL,
  expected_dod date DEFAULT NULL,
  is_serviced tinyint(1) DEFAULT '0',
  service_return_on datetime DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_remarks (
  id int(11) NOT NULL AUTO_INCREMENT,
  return_prod_id bigint(11) DEFAULT '0',
  product_status int(11) DEFAULT '0',
  remarks text,
  parent_id bigint(11) DEFAULT '0',
  created_by bigint(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_transit_log'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_transit_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  sent_log_id bigint(11) DEFAULT NULL,
  invoice_no bigint(20) DEFAULT '0',
  ref_id int(11) DEFAULT '0',
  `status` int(2) DEFAULT '0' COMMENT '1:in-transit,2:pickup or hand-over,3:delivered,4:return',
  received_by varchar(255) DEFAULT NULL,
  received_on datetime DEFAULT NULL,
  contact_no varchar(255) DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  logged_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_less_margin_brands'
--

CREATE TABLE IF NOT EXISTS pnh_less_margin_brands (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  brandid bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_loyalty_points'
--

CREATE TABLE IF NOT EXISTS pnh_loyalty_points (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  amount int(10) unsigned NOT NULL,
  points int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_manifesto_log'
--

CREATE TABLE IF NOT EXISTS pnh_manifesto_log (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  st_date date DEFAULT NULL,
  en_date date DEFAULT NULL,
  invoice_nos text,
  total_prints int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by bigint(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_membersch_deals'
--

CREATE TABLE IF NOT EXISTS pnh_membersch_deals (
  id int(11) NOT NULL AUTO_INCREMENT,
  menuid int(11) DEFAULT NULL,
  itemid bigint(20) DEFAULT NULL,
  valid_from bigint(11) DEFAULT NULL,
  valid_to bigint(11) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT '0',
  created_on bigint(20) DEFAULT '20',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_member_info'
--

CREATE TABLE IF NOT EXISTS pnh_member_info (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL,
  pnh_member_id bigint(20) unsigned NOT NULL,
  franchise_id int(10) unsigned NOT NULL,
  points int(10) unsigned NOT NULL,
  gender tinyint(1) NOT NULL,
  salute tinyint(1) NOT NULL,
  first_name varchar(70) NOT NULL,
  last_name varchar(70) NOT NULL,
  dob date DEFAULT NULL,
  address text NOT NULL,
  city varchar(100) NOT NULL,
  pincode varchar(12) NOT NULL,
  mobile varchar(20) NOT NULL,
  email varchar(100) NOT NULL,
  marital_status tinyint(1) NOT NULL,
  spouse_name varchar(120) NOT NULL,
  child1_name varchar(100) NOT NULL,
  child2_name varchar(100) NOT NULL,
  anniversary date DEFAULT NULL,
  child1_dob date DEFAULT NULL,
  child2_dob date DEFAULT NULL,
  profession varchar(40) NOT NULL,
  expense tinyint(1) NOT NULL,
  is_card_printed tinyint(1) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) DEFAULT '0',
  created_by int(10) unsigned NOT NULL,
  modified_by bigint(11) DEFAULT '0',
  dummy bigint(20) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY user_id_2 (user_id),
  KEY pnh_member_id (pnh_member_id),
  KEY franchise_id (franchise_id),
  KEY address (mobile)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_member_points_track'
--

CREATE TABLE IF NOT EXISTS pnh_member_points_track (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL,
  transid varchar(20) NOT NULL,
  points int(10) NOT NULL,
  points_after int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY pnh_member_id (user_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_menu'
--

CREATE TABLE IF NOT EXISTS pnh_menu (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  min_balance_value double DEFAULT '0',
  bal_discount double DEFAULT '0',
  consider_mrp_chng tinyint(1) DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  loyality_pntvalue float DEFAULT '1',
  default_margin double DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_allotted_mid'
--

CREATE TABLE IF NOT EXISTS pnh_m_allotted_mid (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id bigint(20) unsigned NOT NULL,
  mid_start bigint(20) unsigned NOT NULL,
  mid_end bigint(20) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_bank_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_bank_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  bank_name varchar(2555) NOT NULL,
  branch_name varchar(2555) NOT NULL,
  account_number bigint(222) NOT NULL,
  ifsc_code varchar(222) NOT NULL,
  remarks text,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_book_template'
--

CREATE TABLE IF NOT EXISTS pnh_m_book_template (
  book_template_id bigint(20) NOT NULL AUTO_INCREMENT,
  book_type_name varchar(255) DEFAULT NULL,
  `value` int(10) DEFAULT NULL,
  product_id varchar(255) DEFAULT NULL,
  menu_ids varchar(255) DEFAULT NULL,
  is_active int(1) DEFAULT '1' COMMENT '1:active,0:inactive',
  created_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (book_template_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_book_template_voucher_link'
--

CREATE TABLE IF NOT EXISTS pnh_m_book_template_voucher_link (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  book_template_id bigint(20) DEFAULT NULL,
  voucher_id bigint(20) DEFAULT NULL,
  no_of_voucher int(10) DEFAULT NULL,
  is_active int(1) DEFAULT '1',
  created_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_class_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_class_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  class_name varchar(100) DEFAULT NULL,
  margin double DEFAULT NULL,
  combo_margin double NOT NULL,
  less_margin_brands decimal(10,2) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_deposited_receipts'
--

CREATE TABLE IF NOT EXISTS pnh_m_deposited_receipts (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  deposited_reference_no bigint(15) DEFAULT NULL,
  bank_id int(10) DEFAULT NULL,
  receipt_id bigint(255) DEFAULT NULL,
  is_submitted tinyint(1) DEFAULT NULL,
  is_deposited tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL,
  remarks varchar(2555) DEFAULT NULL,
  is_cancelled tinyint(1) DEFAULT '0',
  cancel_status tinyint(1) DEFAULT '0',
  cancel_reason varchar(2555) DEFAULT NULL,
  cancelled_on datetime DEFAULT NULL,
  dbt_amt double DEFAULT NULL,
  submitted_by bigint(12) DEFAULT NULL,
  submitted_on datetime DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_device_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_device_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  device_sl_no varchar(200) DEFAULT NULL,
  device_type_id int(11) DEFAULT NULL,
  issued_to int(11) DEFAULT NULL COMMENT '0: instock, else id of the franchise pnh id',
  is_damaged tinyint(1) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(11) DEFAULT NULL,
  modified_on bigint(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_device_type'
--

CREATE TABLE IF NOT EXISTS pnh_m_device_type (
  id int(11) NOT NULL AUTO_INCREMENT,
  device_name varchar(200) DEFAULT NULL,
  description varchar(200) NOT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_employee_leaves'
--

CREATE TABLE IF NOT EXISTS pnh_m_employee_leaves (
  id bigint(1) NOT NULL AUTO_INCREMENT,
  emp_id bigint(1) DEFAULT NULL,
  remarks text,
  holidy_stdt date DEFAULT NULL,
  holidy_endt date DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  created_by bigint(1) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by bigint(1) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_franchise_contacts_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_franchise_contacts_info (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  contact_name varchar(100) NOT NULL,
  contact_designation varchar(100) NOT NULL,
  contact_mobile1 varchar(20) NOT NULL,
  contact_mobile2 varchar(20) NOT NULL,
  contact_telephone varchar(20) NOT NULL,
  contact_fax varchar(20) NOT NULL,
  contact_email1 varchar(100) NOT NULL,
  contact_email2 varchar(100) NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_franchise_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_franchise_info (
  franchise_id int(11) NOT NULL AUTO_INCREMENT,
  pnh_franchise_id bigint(11) DEFAULT NULL COMMENT '6 digit no starting with No 3',
  franchise_name varchar(200) DEFAULT NULL,
  address varchar(250) DEFAULT NULL,
  locality varchar(200) DEFAULT NULL,
  city varchar(200) DEFAULT NULL,
  postcode varchar(10) DEFAULT NULL,
  state varchar(100) DEFAULT NULL,
  territory_id int(11) DEFAULT NULL,
  town_id int(10) unsigned NOT NULL,
  class_id int(11) DEFAULT NULL,
  is_lc_store tinyint(1) NOT NULL,
  is_sch_enabled tinyint(1) NOT NULL,
  sch_discount double NOT NULL,
  sch_discount_start bigint(20) unsigned NOT NULL,
  sch_discount_end bigint(20) unsigned NOT NULL,
  security_deposit double DEFAULT '0',
  current_balance double DEFAULT '0',
  credit_limit double DEFAULT '0',
  last_credit double DEFAULT '0',
  login_mobile1 varchar(20) DEFAULT NULL,
  login_mobile2 varchar(20) DEFAULT NULL,
  app_version int(10) unsigned NOT NULL,
  email_id varchar(200) DEFAULT NULL,
  assigned_to int(11) DEFAULT NULL,
  no_of_employees int(11) DEFAULT NULL,
  store_name varchar(100) NOT NULL,
  store_area int(11) DEFAULT NULL,
  lat double DEFAULT NULL,
  `long` double DEFAULT NULL,
  store_open_time time DEFAULT NULL,
  store_close_time time DEFAULT NULL,
  store_tin_no varchar(40) NOT NULL,
  store_pan_no varchar(40) NOT NULL,
  store_service_tax_no varchar(40) NOT NULL,
  store_reg_no varchar(40) NOT NULL,
  own_rented tinyint(1) DEFAULT '0',
  internet_available varchar(200) DEFAULT NULL COMMENT 'comma seperated names of the ISP',
  website_name varchar(200) DEFAULT NULL,
  business_type varchar(150) NOT NULL,
  security_question tinyint(3) NOT NULL,
  security_answer varchar(150) NOT NULL,
  security_question2 tinyint(3) NOT NULL,
  security_answer2 varchar(100) NOT NULL,
  security_custom_question varchar(120) NOT NULL,
  security_custom_question2 varchar(120) NOT NULL,
  is_prepaid int(1) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  is_suspended tinyint(1) NOT NULL,
  suspended_on bigint(20) unsigned NOT NULL,
  suspended_by int(10) unsigned NOT NULL,
  reason varchar(2555) DEFAULT NULL,
  PRIMARY KEY (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_fran_security_cheques'
--

CREATE TABLE IF NOT EXISTS pnh_m_fran_security_cheques (
  id int(11) NOT NULL AUTO_INCREMENT,
  franchise_id int(11) DEFAULT '0',
  bank_name varchar(255) DEFAULT NULL,
  cheque_no varchar(30) DEFAULT NULL,
  cheque_date date DEFAULT NULL,
  collected_on date DEFAULT NULL,
  amount double DEFAULT NULL,
  returned_on date DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_manifesto_sent_log'
--

CREATE TABLE IF NOT EXISTS pnh_m_manifesto_sent_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  manifesto_id bigint(11) DEFAULT NULL,
  sent_invoices text,
  remark text,
  remark2 text,
  hndlby_type int(11) DEFAULT '0' COMMENT '1:driver,2:fright-cordinator,3:bus transport,4:courier',
  hndlby_roleid int(11) DEFAULT '0',
  hndleby_empid int(11) DEFAULT '0',
  hndleby_courier_id int(11) DEFAULT '0',
  hndleby_name varchar(255) DEFAULT NULL,
  hndleby_contactno varchar(255) DEFAULT NULL,
  alternative_contactno varchar(255) DEFAULT NULL,
  bus_id int(100) DEFAULT NULL,
  bus_destination int(100) DEFAULT NULL,
  transport_type tinyint(1) DEFAULT '0',
  lrno varchar(255) DEFAULT NULL,
  hndleby_vehicle_num varchar(255) DEFAULT NULL,
  start_meter_rate varchar(255) DEFAULT NULL,
  amount varchar(255) DEFAULT NULL,
  weight double DEFAULT '0',
  pickup_empid int(11) DEFAULT '0',
  office_pickup_empid varchar(255) DEFAULT NULL,
  shipment_sent_date datetime DEFAULT NULL,
  lrn_updated_on datetime DEFAULT NULL,
  is_printed int(5) DEFAULT '0',
  `status` int(10) DEFAULT '1' COMMENT '1:pending,2:scaned,3:shipped',
  no_ofboxes bigint(10) DEFAULT '0',
  ref_box_no bigint(10) DEFAULT '0',
  sent_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_offers'
--

CREATE TABLE IF NOT EXISTS pnh_m_offers (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  franchise_id bigint(20) DEFAULT '0',
  menu_id bigint(20) DEFAULT '0',
  brand_id bigint(20) DEFAULT '0',
  cat_id bigint(20) DEFAULT '0',
  offer_text text,
  immediate_payment tinyint(1) DEFAULT '0',
  offer_start bigint(20) DEFAULT '0',
  offer_end bigint(20) DEFAULT '0',
  created_by bigint(20) DEFAULT '0',
  created_on bigint(20) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  modified_on bigint(20) DEFAULT '0',
  modified_by int(10) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_sales_target_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_sales_target_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  task_id int(11) DEFAULT NULL,
  f_id int(11) DEFAULT NULL,
  avg_amount double DEFAULT NULL,
  target_amount double DEFAULT NULL,
  actual_target double NOT NULL,
  `status` int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_states'
--

CREATE TABLE IF NOT EXISTS pnh_m_states (
  state_id int(11) NOT NULL AUTO_INCREMENT,
  state_name varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  PRIMARY KEY (state_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_task_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_task_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  ref_no bigint(11) DEFAULT '0',
  task_title varchar(255) DEFAULT NULL,
  task varchar(255) DEFAULT NULL,
  task_type varchar(50) DEFAULT NULL,
  asgnd_town_id int(11) DEFAULT NULL,
  on_date datetime DEFAULT NULL,
  due_date datetime DEFAULT NULL,
  assigned_by int(11) DEFAULT NULL,
  assigned_to int(11) DEFAULT NULL,
  task_status int(11) DEFAULT NULL,
  assigned_on datetime DEFAULT NULL,
  completed_on datetime DEFAULT NULL,
  completed_by int(11) DEFAULT NULL,
  cancelled_on datetime DEFAULT NULL,
  cancelled_by int(11) DEFAULT NULL,
  is_active int(11) DEFAULT NULL,
  comments text,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_task_types'
--

CREATE TABLE IF NOT EXISTS pnh_m_task_types (
  id int(11) NOT NULL AUTO_INCREMENT,
  task_type varchar(25555) DEFAULT NULL,
  task_for tinyint(1) DEFAULT '0',
  short_form varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_territory_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_territory_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  state_id int(11) DEFAULT '0',
  territory_name varchar(200) DEFAULT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_uploaded_depositedslips'
--

CREATE TABLE IF NOT EXISTS pnh_m_uploaded_depositedslips (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  deposited_reference_no bigint(11) DEFAULT NULL,
  receipt_ids varchar(20554) DEFAULT NULL,
  scanned_url varchar(255) DEFAULT NULL,
  is_deposited int(11) DEFAULT NULL,
  remarks varchar(2055) DEFAULT NULL,
  uploaded_by int(11) DEFAULT NULL,
  uploaded_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_voucher'
--

CREATE TABLE IF NOT EXISTS pnh_m_voucher (
  voucher_id bigint(20) NOT NULL AUTO_INCREMENT,
  voucher_name varchar(255) DEFAULT NULL,
  denomination int(11) DEFAULT NULL,
  created_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_order_margin_track'
--

CREATE TABLE IF NOT EXISTS pnh_order_margin_track (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(20) NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  mrp decimal(10,2) NOT NULL,
  price decimal(10,2) NOT NULL,
  base_margin decimal(10,2) NOT NULL,
  sch_margin decimal(10,2) NOT NULL,
  voucher_margin double DEFAULT NULL,
  bal_discount decimal(10,2) DEFAULT '0.00',
  qty int(10) unsigned NOT NULL,
  final_price int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_prepaid_menu_config'
--

CREATE TABLE IF NOT EXISTS pnh_prepaid_menu_config (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  menu_id int(10) DEFAULT NULL,
  menu_margin double DEFAULT '0',
  is_active int(1) DEFAULT '1',
  created_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(10) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quotes'
--

CREATE TABLE IF NOT EXISTS pnh_quotes (
  quote_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(11) NOT NULL,
  respond_in_min int(11) DEFAULT '0',
  quote_status tinyint(1) DEFAULT '0',
  created_on bigint(20) unsigned NOT NULL,
  created_by int(11) NOT NULL,
  updated_on bigint(20) unsigned NOT NULL,
  updated_by int(11) NOT NULL,
  PRIMARY KEY (quote_id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quotes_deal_link'
--

CREATE TABLE IF NOT EXISTS pnh_quotes_deal_link (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  quote_id int(10) unsigned NOT NULL,
  pnh_id bigint(20) unsigned NOT NULL,
  new_product varchar(2555) DEFAULT NULL,
  np_mrp int(255) DEFAULT '0',
  np_qty int(255) DEFAULT '1',
  np_quote int(255) DEFAULT NULL,
  qty int(10) DEFAULT '0',
  dp_price int(10) unsigned NOT NULL,
  final_price int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  order_status tinyint(1) NOT NULL,
  transid varchar(50) NOT NULL,
  price_updated_by int(10) unsigned NOT NULL,
  is_notified tinyint(1) DEFAULT '0',
  updated_by int(10) unsigned NOT NULL,
  updated_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY quote_id (quote_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quote_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_quote_remarks (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  quote_id int(10) unsigned NOT NULL,
  req_complete tinyint(1) DEFAULT '0',
  remarks varchar(200) NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY quote_id (quote_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_routes'
--

CREATE TABLE IF NOT EXISTS pnh_routes (
  id int(11) NOT NULL AUTO_INCREMENT,
  route_name varchar(255) DEFAULT NULL,
  is_active int(11) NOT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sch_discount_brands'
--

CREATE TABLE IF NOT EXISTS pnh_sch_discount_brands (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(10) unsigned NOT NULL,
  sch_type tinyint(1) DEFAULT '0',
  menuid bigint(20) DEFAULT NULL,
  brandid bigint(20) unsigned NOT NULL,
  catid int(10) unsigned NOT NULL,
  discount decimal(10,2) NOT NULL,
  valid_from bigint(20) unsigned NOT NULL,
  valid_to bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) DEFAULT NULL,
  modified_by int(10) DEFAULT NULL,
  is_sch_enabled tinyint(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sch_discount_track'
--

CREATE TABLE IF NOT EXISTS pnh_sch_discount_track (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id bigint(20) unsigned NOT NULL,
  sch_discount double NOT NULL,
  sch_type tinyint(11) DEFAULT NULL,
  sch_menu varchar(255) DEFAULT NULL,
  sch_discount_start bigint(20) unsigned NOT NULL,
  sch_discount_end bigint(20) unsigned NOT NULL,
  reason varchar(250) NOT NULL,
  brandid bigint(20) unsigned NOT NULL,
  catid int(10) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_ship_remarksupdate_log'
--

CREATE TABLE IF NOT EXISTS pnh_ship_remarksupdate_log (
  id bigint(25) NOT NULL AUTO_INCREMENT,
  ship_msg_id int(11) DEFAULT '0',
  ticket_id int(11) DEFAULT '0',
  updated_by time DEFAULT NULL,
  updated_on time DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sms_log'
--

CREATE TABLE IF NOT EXISTS pnh_sms_log (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  msg varchar(500) DEFAULT NULL,
  sender varchar(20) DEFAULT NULL,
  franchise_id int(10) unsigned NOT NULL,
  `type` varchar(255) DEFAULT '',
  reply_for int(10) unsigned NOT NULL,
  created_on bigint(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sms_log_sent'
--

CREATE TABLE IF NOT EXISTS pnh_sms_log_sent (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to` varchar(20) NOT NULL,
  msg text NOT NULL,
  franchise_id int(10) DEFAULT NULL,
  pnh_empid int(10) DEFAULT NULL,
  pnh_mid int(10) DEFAULT '0',
  `type` varchar(50) DEFAULT NULL COMMENT '11:invoice delivered info to franchise,12:invoice shiped notification to franchise,13:return inv info to frc',
  ticket_id bigint(50) DEFAULT '0',
  sent_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_special_margin_deals'
--

CREATE TABLE IF NOT EXISTS pnh_special_margin_deals (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  special_margin decimal(10,2) NOT NULL,
  itemid bigint(20) unsigned NOT NULL,
  i_mrp double DEFAULT NULL,
  i_price double DEFAULT NULL,
  `from` bigint(20) unsigned NOT NULL,
  `to` bigint(20) unsigned NOT NULL,
  is_active tinyint(1) DEFAULT '0',
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_superscheme_deals'
--

CREATE TABLE IF NOT EXISTS pnh_superscheme_deals (
  id int(11) NOT NULL AUTO_INCREMENT,
  menuid bigint(11) DEFAULT NULL,
  itemid bigint(11) DEFAULT NULL,
  valid_from bigint(11) DEFAULT '0',
  valid_to bigint(11) DEFAULT '0',
  is_active tinyint(11) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  reason varchar(255) DEFAULT NULL,
  created_on bigint(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_super_scheme'
--

CREATE TABLE IF NOT EXISTS pnh_super_scheme (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id bigint(11) DEFAULT NULL,
  schme_discount_id bigint(11) DEFAULT NULL,
  menu_id bigint(11) DEFAULT NULL,
  cat_id bigint(15) DEFAULT NULL,
  brand_id bigint(15) DEFAULT NULL,
  target_value double DEFAULT NULL,
  credit_prc double DEFAULT NULL,
  valid_from bigint(20) DEFAULT NULL,
  valid_to bigint(20) DEFAULT NULL,
  is_active int(11) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_task_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_task_remarks (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  emp_id bigint(11) DEFAULT NULL,
  task_id bigint(11) DEFAULT NULL,
  remarks text,
  posted_by bigint(11) DEFAULT NULL,
  posted_on datetime DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  logged_by bigint(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_task_type_details'
--

CREATE TABLE IF NOT EXISTS pnh_task_type_details (
  id int(11) NOT NULL AUTO_INCREMENT,
  custom_field_1 varchar(255) DEFAULT NULL,
  task_id int(11) DEFAULT NULL,
  task_type_id varchar(255) DEFAULT NULL,
  f_id int(11) DEFAULT NULL,
  request_msg varchar(255) DEFAULT NULL,
  response_msg varchar(255) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on date DEFAULT NULL,
  modified_on date DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_towns'
--

CREATE TABLE IF NOT EXISTS pnh_towns (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  route_id int(10) unsigned NOT NULL,
  territory_id int(10) unsigned NOT NULL,
  town_name varchar(100) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_town_courier_priority_link'
--

CREATE TABLE IF NOT EXISTS pnh_town_courier_priority_link (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  town_id int(11) DEFAULT '0',
  courier_priority_1 int(5) DEFAULT '0',
  courier_priority_2 int(5) DEFAULT '0',
  courier_priority_3 int(5) DEFAULT '0',
  delivery_hours_1 int(3) DEFAULT '0',
  delivery_hours_2 int(3) DEFAULT '0',
  delivery_hours_3 int(3) DEFAULT '0',
  delivery_type_priority1 int(3) DEFAULT '0',
  delivery_type_priority2 int(3) DEFAULT '0',
  delivery_type_priority3 int(3) DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_transporter_dest_address'
--

CREATE TABLE IF NOT EXISTS pnh_transporter_dest_address (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  transpoter_id bigint(11) DEFAULT NULL,
  short_name varchar(255) DEFAULT NULL,
  address text,
  city varchar(255) DEFAULT NULL,
  pincode int(10) DEFAULT NULL,
  contact_no varchar(255) DEFAULT NULL,
  active int(1) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  dest_addr_unqid int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(10) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_transporter_info'
--

CREATE TABLE IF NOT EXISTS pnh_transporter_info (
  id bigint(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  address text,
  city varchar(255) DEFAULT NULL,
  pincode varchar(255) DEFAULT NULL,
  contact_no varchar(255) DEFAULT NULL,
  allowed_transport varchar(255) DEFAULT NULL COMMENT '1:bus,2:Cargo,3:Gp',
  active int(10) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(10) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(10) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_allotment'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_allotment (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  allotment_id bigint(11) DEFAULT '0',
  book_id bigint(11) DEFAULT NULL,
  franchise_id bigint(20) DEFAULT NULL,
  `status` int(5) DEFAULT '0' COMMENT '1:assigned to franchise,2:payed and activated,3:returned',
  margin double DEFAULT '0',
  order_id varchar(255) DEFAULT NULL,
  activated_on datetime DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_details'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_details (
  book_id bigint(20) NOT NULL AUTO_INCREMENT,
  book_template_id int(11) DEFAULT NULL,
  book_slno varchar(255) DEFAULT NULL,
  book_value int(11) DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (book_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_receipt_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_receipt_link (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  book_id bigint(20) DEFAULT NULL,
  receipt_id bigint(20) DEFAULT NULL,
  franchise_id bigint(20) DEFAULT NULL,
  adjusted_value double DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_voucher_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_voucher_link (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  book_id bigint(20) DEFAULT NULL,
  voucher_slno_id bigint(20) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_credit_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_credit_info (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(11) DEFAULT NULL,
  credit_added double DEFAULT NULL,
  new_credit_limit double DEFAULT NULL,
  credit_given_by int(11) DEFAULT NULL COMMENT 'executive id',
  reason varchar(200) NOT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_device_movement_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_device_movement_info (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  device_id int(11) DEFAULT NULL,
  issued_to int(11) DEFAULT NULL COMMENT '0 means come back to stock, else PNH id',
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_receipt_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_receipt_info (
  receipt_id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  franchise_id int(11) DEFAULT NULL,
  receipt_amount double DEFAULT '0',
  unreconciliation_amount double DEFAULT '0',
  receipt_type tinyint(1) DEFAULT '2' COMMENT '1: deposit, 2: top-up',
  payment_mode tinyint(1) DEFAULT '0' COMMENT '0: cash, 1: cheque, 2: dd, 3: transfer',
  bank_name varchar(70) NOT NULL,
  instrument_no varchar(100) DEFAULT NULL COMMENT 'cheqye / dd / transfer no',
  instrument_date bigint(11) DEFAULT NULL,
  is_active tinyint(1) DEFAULT '1',
  is_submitted tinyint(1) DEFAULT '0',
  is_deposited tinyint(1) DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL,
  in_transit int(1) DEFAULT '1',
  remarks varchar(150) NOT NULL,
  created_by int(11) DEFAULT NULL,
  created_on bigint(20) DEFAULT NULL,
  activated_by bigint(20) unsigned NOT NULL,
  activated_on bigint(20) unsigned NOT NULL,
  reason varchar(150) NOT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  PRIMARY KEY (receipt_id),
  KEY pnh_franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_tray_invoice_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_tray_invoice_link (
  tray_inv_id bigint(11) NOT NULL AUTO_INCREMENT,
  tray_terr_id bigint(11) NOT NULL DEFAULT '0',
  invoice_no bigint(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:invoice in tray,2:invoice out of tray',
  is_active tinyint(1) NOT NULL DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  modified_by bigint(11) DEFAULT NULL,
  created_on datetime NOT NULL,
  created_by bigint(11) NOT NULL,
  PRIMARY KEY (tray_inv_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_tray_territory_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_tray_territory_link (
  tray_terr_id bigint(11) NOT NULL AUTO_INCREMENT,
  tray_id int(11) NOT NULL,
  territory_id int(11) NOT NULL,
  max_shipments int(5) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:Created Not used,1:In-use,2:Filled',
  is_active tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: Has Shipments,0: No Shipments',
  modified_on datetime DEFAULT NULL,
  modified_by bigint(11) DEFAULT NULL,
  created_by bigint(11) NOT NULL,
  created_on datetime NOT NULL,
  PRIMARY KEY (tray_terr_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_voucher_details'
--

CREATE TABLE IF NOT EXISTS pnh_t_voucher_details (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  voucher_id int(11) DEFAULT NULL,
  group_code int(11) DEFAULT NULL,
  voucher_serial_no bigint(12) NOT NULL,
  voucher_code bigint(14) NOT NULL,
  `value` double NOT NULL,
  voucher_margin double DEFAULT NULL,
  customer_value double(10,2) DEFAULT NULL,
  franchise_value double(10,2) DEFAULT NULL,
  last_redeemed_on datetime DEFAULT NULL,
  franchise_id bigint(11) DEFAULT NULL,
  member_id bigint(8) DEFAULT NULL,
  `status` tinyint(11) DEFAULT '0' COMMENT '0:pending,1:voucher_linked_to_book,2:Allloted to franchise,3:Activated,4:Fully Reddemed,5:partailly Redeemed,6:Cancelled',
  assigned_on datetime DEFAULT NULL,
  assigned_by int(11) DEFAULT NULL,
  is_alloted int(1) DEFAULT '0',
  alloted_on datetime DEFAULT NULL,
  is_activated int(1) DEFAULT '0',
  activated_on datetime DEFAULT NULL,
  redeemed_on datetime DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  PRIMARY KEY (id,voucher_serial_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_voucher_activity_log'
--

CREATE TABLE IF NOT EXISTS pnh_voucher_activity_log (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  voucher_slno bigint(11) DEFAULT '0',
  franchise_id bigint(20) DEFAULT '0',
  member_id bigint(20) DEFAULT '0',
  transid varchar(255) DEFAULT NULL,
  debit double DEFAULT '0',
  credit double DEFAULT '0',
  order_ids varchar(255) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group'
--

CREATE TABLE IF NOT EXISTS products_group (
  group_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  cat_id int(10) unsigned NOT NULL,
  group_name varchar(200) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (group_id),
  KEY cat_id (cat_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_attributes'
--

CREATE TABLE IF NOT EXISTS products_group_attributes (
  attribute_name_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  group_id int(10) unsigned NOT NULL,
  attribute_name varchar(100) NOT NULL,
  PRIMARY KEY (attribute_name_id),
  KEY group_id (group_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_attribute_values'
--

CREATE TABLE IF NOT EXISTS products_group_attribute_values (
  attribute_value_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  group_id int(11) NOT NULL,
  attribute_name_id int(11) NOT NULL,
  attribute_value varchar(100) NOT NULL,
  PRIMARY KEY (attribute_value_id),
  KEY group_id (group_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_category'
--

CREATE TABLE IF NOT EXISTS products_group_category (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_orders'
--

CREATE TABLE IF NOT EXISTS products_group_orders (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(20) NOT NULL,
  order_id bigint(20) unsigned NOT NULL,
  product_id int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_pids'
--

CREATE TABLE IF NOT EXISTS products_group_pids (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  group_id int(11) NOT NULL,
  product_id int(11) NOT NULL,
  attribute_name_id int(11) NOT NULL,
  attribute_value_id int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY group_id (group_id),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_src_changelog'
--

CREATE TABLE IF NOT EXISTS products_src_changelog (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  product_id int(10) unsigned NOT NULL,
  is_sourceable tinyint(3) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'product_price_changelog'
--

CREATE TABLE IF NOT EXISTS product_price_changelog (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  product_id bigint(20) unsigned NOT NULL,
  new_mrp decimal(10,2) unsigned NOT NULL,
  old_mrp decimal(10,2) NOT NULL,
  reference_grn int(10) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'proforma_invoices'
--

CREATE TABLE IF NOT EXISTS proforma_invoices (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  p_invoice_no int(10) unsigned NOT NULL,
  dispatch_id bigint(11) DEFAULT '0',
  transid char(18) NOT NULL,
  order_id bigint(20) unsigned NOT NULL,
  mrp int(10) unsigned NOT NULL,
  discount decimal(10,2) unsigned NOT NULL,
  nlc int(10) unsigned NOT NULL,
  phc int(10) unsigned NOT NULL,
  tax double unsigned NOT NULL,
  service_tax double NOT NULL,
  cod double unsigned NOT NULL,
  ship double unsigned NOT NULL,
  giftwrap_charge double DEFAULT '0',
  invoice_status tinyint(1) DEFAULT '0',
  createdon bigint(20) DEFAULT NULL,
  cancelled_on bigint(20) DEFAULT NULL,
  delivery_medium varchar(255) DEFAULT '0',
  tracking_id varchar(50) DEFAULT '0',
  shipdatetime datetime DEFAULT NULL,
  notify_customer tinyint(1) DEFAULT '0',
  is_delivered tinyint(1) DEFAULT '0',
  is_partial_invoice tinyint(1) DEFAULT '0',
  total_prints int(5) DEFAULT '0',
  is_b2b tinyint(1) NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY p_invoice_no (p_invoice_no),
  KEY order_id (order_id),
  KEY dispatch_id (dispatch_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'promo_email'
--

CREATE TABLE IF NOT EXISTS promo_email (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) DEFAULT NULL,
  lastsent bigint(20) unsigned DEFAULT NULL,
  count int(1) unsigned DEFAULT '0',
  un_subscribe tinyint(1) DEFAULT '0',
  company_name varchar(100) DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY email_id (email)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'promo_email_old'
--

CREATE TABLE IF NOT EXISTS promo_email_old (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) DEFAULT NULL,
  un_subscribe tinyint(1) DEFAULT '0',
  count int(10) unsigned NOT NULL,
  lastsent bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sample_king_users'
--

CREATE TABLE IF NOT EXISTS sample_king_users (
  userid bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  mobile bigint(11) unsigned NOT NULL,
  corpemail varchar(100) NOT NULL,
  corpid int(10) unsigned NOT NULL,
  balance int(10) unsigned NOT NULL,
  inviteid char(10) NOT NULL,
  friendof bigint(20) unsigned NOT NULL,
  special tinyint(1) NOT NULL,
  special_id varchar(30) NOT NULL,
  address text NOT NULL,
  landmark text NOT NULL,
  telephone varchar(30) NOT NULL,
  country varchar(255) DEFAULT NULL,
  city varchar(100) NOT NULL,
  state varchar(100) NOT NULL,
  pincode varchar(100) NOT NULL,
  `block` tinyint(1) NOT NULL,
  verified int(10) unsigned NOT NULL,
  verify_code char(10) NOT NULL,
  optin tinyint(1) NOT NULL DEFAULT '1',
  createdon bigint(20) unsigned NOT NULL DEFAULT '1275750318'
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'servicable_pincode'
--

CREATE TABLE IF NOT EXISTS servicable_pincode (
  id int(11) NOT NULL AUTO_INCREMENT,
  pincode varchar(10) DEFAULT NULL,
  locality varchar(200) DEFAULT NULL,
  city_name varchar(150) DEFAULT NULL,
  district_name varchar(150) DEFAULT NULL,
  state_name varchar(150) DEFAULT NULL,
  cod_applicable tinyint(1) DEFAULT '0',
  courier_code varchar(100) DEFAULT NULL,
  active_status tinyint(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY pincode (pincode)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'shipment_batch_process'
--

CREATE TABLE IF NOT EXISTS shipment_batch_process (
  batch_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  num_orders int(10) unsigned NOT NULL,
  process_type int(1) DEFAULT '0',
  orders_by varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  batch_remarks text,
  created_by bigint(20) unsigned NOT NULL,
  created_on datetime NOT NULL,
  PRIMARY KEY (batch_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'shipment_batch_process_invoice_link'
--

CREATE TABLE IF NOT EXISTS shipment_batch_process_invoice_link (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  batch_id bigint(20) unsigned NOT NULL,
  p_invoice_no int(10) unsigned NOT NULL,
  invoice_no bigint(20) unsigned NOT NULL,
  invoiced_on datetime DEFAULT NULL,
  invoiced_by bigint(11) DEFAULT '0',
  awb varchar(40) NOT NULL,
  courier_id int(10) unsigned NOT NULL,
  tray_id int(11) DEFAULT '0',
  packed tinyint(1) NOT NULL,
  shipped tinyint(1) NOT NULL,
  is_returned tinyint(1) DEFAULT '0',
  inv_manifesto_id bigint(11) DEFAULT '0',
  is_acknowleged tinyint(1) DEFAULT '0',
  packed_on datetime NOT NULL,
  packed_by bigint(20) unsigned NOT NULL,
  outscanned_on datetime DEFAULT NULL,
  outscanned_by bigint(20) DEFAULT NULL,
  outscanned tinyint(1) DEFAULT '0',
  shipped_by bigint(20) unsigned NOT NULL,
  shipped_on datetime NOT NULL,
  is_acknowleged_by int(11) DEFAULT NULL,
  is_acknowleged_on datetime DEFAULT NULL,
  delivered_on datetime DEFAULT NULL,
  tmp_courier_name varchar(100) DEFAULT NULL,
  is_delivered int(11) DEFAULT NULL,
  delivered_by int(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY invoice_no (invoice_no),
  KEY batch_id (batch_id),
  KEY courier_id (courier_id),
  KEY shipped (shipped),
  KEY p_invoice_no (p_invoice_no),
  KEY inv_manifesto_id (inv_manifesto_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_auth'
--

CREATE TABLE IF NOT EXISTS sms_auth (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lock` varchar(100) NOT NULL,
  `key` char(32) NOT NULL,
  hits int(10) unsigned NOT NULL,
  lasthit bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_done'
--

CREATE TABLE IF NOT EXISTS sms_done (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  msg text NOT NULL,
  number bigint(20) unsigned NOT NULL,
  sent_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_invoice_log'
--

CREATE TABLE IF NOT EXISTS sms_invoice_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  `type` bigint(11) DEFAULT NULL,
  fid bigint(11) DEFAULT NULL,
  invoice_no bigint(11) DEFAULT NULL,
  emp_id1 bigint(11) DEFAULT NULL,
  emp_id2 bigint(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  logged_by bigint(11) DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_pull'
--

CREATE TABLE IF NOT EXISTS sms_pull (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  mobile bigint(20) unsigned NOT NULL,
  msg text NOT NULL,
  rule int(10) unsigned NOT NULL,
  echo text NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_push'
--

CREATE TABLE IF NOT EXISTS sms_push (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to` bigint(20) unsigned NOT NULL,
  msg text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `code` char(5) NOT NULL,
  attempts int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  last_attempt bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_queue'
--

CREATE TABLE IF NOT EXISTS sms_queue (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  msg text NOT NULL,
  number bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'snp_product_views'
--

CREATE TABLE IF NOT EXISTS snp_product_views (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned NOT NULL,
  userid bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'support_tickets'
--

CREATE TABLE IF NOT EXISTS support_tickets (
  ticket_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  ticket_no bigint(20) unsigned NOT NULL,
  user_id bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  mobile varchar(20) NOT NULL,
  email varchar(150) NOT NULL,
  transid varchar(20) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  priority tinyint(1) unsigned NOT NULL,
  assigned_to int(10) unsigned NOT NULL,
  created_on datetime NOT NULL,
  updated_on datetime NOT NULL,
  PRIMARY KEY (ticket_id),
  UNIQUE KEY ticket_no (ticket_no),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'support_tickets_msg'
--

CREATE TABLE IF NOT EXISTS support_tickets_msg (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  ticket_id bigint(20) unsigned NOT NULL,
  msg text NOT NULL,
  msg_type tinyint(1) unsigned NOT NULL,
  `medium` tinyint(1) unsigned NOT NULL,
  from_customer tinyint(1) NOT NULL,
  support_user int(10) unsigned NOT NULL,
  created_on datetime NOT NULL,
  PRIMARY KEY (id),
  KEY ticket_id (ticket_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'transactions_changelog'
--

CREATE TABLE IF NOT EXISTS transactions_changelog (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(20) NOT NULL,
  msg text NOT NULL,
  admin bigint(20) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_billedmrp_change_log'
--

CREATE TABLE IF NOT EXISTS t_billedmrp_change_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  invoice_no bigint(11) DEFAULT '0',
  p_invoice_no int(11) DEFAULT '0',
  packed_mrp double DEFAULT NULL,
  billed_mrp double DEFAULT NULL,
  remarks text,
  logged_on datetime DEFAULT NULL,
  logged_by int(5) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_bulkordersinvoice_log'
--

CREATE TABLE IF NOT EXISTS t_bulkordersinvoice_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  grpno bigint(11) DEFAULT '0',
  batch_id bigint(11) DEFAULT '0',
  p_invno bigint(11) DEFAULT '0',
  invno bigint(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_info'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_info (
  invoice_id int(11) NOT NULL AUTO_INCREMENT,
  invoice_no varchar(50) DEFAULT NULL,
  invoice_date datetime DEFAULT NULL,
  client_id int(11) DEFAULT '0',
  order_id int(11) DEFAULT '0' COMMENT 'optional, invoice can be created without order',
  total_invoice_value double DEFAULT '0',
  total_paid_value double DEFAULT '0',
  invoice_status tinyint(1) DEFAULT '1' COMMENT 'default 1: active, 2: cancelled',
  payment_status tinyint(1) DEFAULT '0' COMMENT 'default 0: payment pending; 1: partially paid, 2: fully paid',
  created_date datetime DEFAULT NULL,
  modified_date datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (invoice_id)
) TYPE=MyISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_payment'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_payment (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  invoice_id int(11) DEFAULT '0',
  amount_paid double DEFAULT '0',
  payment_type tinyint(1) DEFAULT '1' COMMENT '1: Cash, 2: Cheque, 3: Transfer, 4: DD',
  instrument_no varchar(100) DEFAULT NULL,
  instrument_date datetime DEFAULT NULL,
  bank_name varchar(100) DEFAULT NULL,
  is_cleared tinyint(1) DEFAULT '0' COMMENT 'default 0: not cleared, 1: when cheque/dd/transfer is cleared & reflecting in a/c',
  bounced tinyint(1) DEFAULT NULL COMMENT '0: not bounced, 1: bounced',
  remarks varchar(250) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_product_info'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_product_info (
  id int(11) NOT NULL AUTO_INCREMENT,
  order_id int(11) DEFAULT '0' COMMENT 'optional, invoice can be created without order',
  invoice_id int(11) DEFAULT '0',
  product_id int(11) DEFAULT '0',
  mrp double DEFAULT '0',
  margin_offered double DEFAULT '0',
  offer_price double DEFAULT '0',
  tax_percent double DEFAULT '0',
  invoice_qty int(11) DEFAULT '0',
  active_status tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_order_info'
--

CREATE TABLE IF NOT EXISTS t_client_order_info (
  order_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  client_id int(11) DEFAULT '0',
  order_reference_no varchar(150) DEFAULT NULL,
  remarks varchar(255) DEFAULT NULL,
  order_status tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (order_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_order_product_info'
--

CREATE TABLE IF NOT EXISTS t_client_order_product_info (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  order_id int(11) unsigned DEFAULT '0',
  product_id bigint(11) DEFAULT '0' COMMENT 'optional, client might order product which is not there with us, we will purchase & then link it to order',
  product_name varchar(200) DEFAULT NULL,
  mrp double DEFAULT '0',
  order_qty int(11) DEFAULT '0',
  invoiced_qty int(11) DEFAULT '0',
  active_status tinyint(1) DEFAULT '1',
  created_on bigint(20) DEFAULT NULL,
  modified_on bigint(20) DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_exotel_agent_status'
--

CREATE TABLE IF NOT EXISTS t_exotel_agent_status (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  callsid varchar(255) DEFAULT NULL,
  `from` varchar(50) DEFAULT NULL,
  dialwhomno varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_info'
--

CREATE TABLE IF NOT EXISTS t_grn_info (
  grn_id int(11) NOT NULL AUTO_INCREMENT,
  po_id int(11) DEFAULT NULL,
  vendor_id int(11) DEFAULT NULL,
  purchase_invoice_no varchar(150) DEFAULT NULL COMMENT 'invoice or delivery challan no',
  purchase_invoice_value decimal(15,4) DEFAULT NULL,
  purchase_invoice_date date DEFAULT NULL,
  transporter_name varchar(255) DEFAULT NULL,
  driver_name varchar(255) DEFAULT NULL,
  transporter_contact_no varchar(255) DEFAULT NULL,
  vehicle_no varchar(255) DEFAULT NULL,
  payment_status tinyint(1) DEFAULT '0',
  grn_status tinyint(11) DEFAULT NULL,
  remarks varchar(2000) DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (grn_id),
  KEY po_id (po_id),
  KEY vendor_id (vendor_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_invoice_link'
--

CREATE TABLE IF NOT EXISTS t_grn_invoice_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  grn_id int(11) DEFAULT NULL,
  purchase_inv_no varchar(30) DEFAULT NULL,
  purchase_inv_date date DEFAULT NULL,
  purchase_inv_value decimal(15,4) DEFAULT '0.0000',
  is_active tinyint(1) DEFAULT '1',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY grn_id (grn_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_product_link'
--

CREATE TABLE IF NOT EXISTS t_grn_product_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  grn_id int(11) DEFAULT NULL,
  po_id int(11) DEFAULT NULL,
  product_id int(11) DEFAULT NULL,
  invoice_qty double DEFAULT NULL,
  received_qty double DEFAULT NULL,
  mrp decimal(15,4) DEFAULT NULL,
  dp_price decimal(15,4) DEFAULT '0.0000',
  purchase_price decimal(15,4) DEFAULT NULL,
  tax_percent double DEFAULT NULL,
  ref_stock_id bigint(11) DEFAULT '0',
  location_id int(11) DEFAULT '0',
  rack_bin_id int(11) DEFAULT '0',
  margin decimal(10,4) DEFAULT NULL,
  scheme_discount_value decimal(15,4) DEFAULT '0.0000',
  scheme_discunt_type tinyint(1) DEFAULT '1' COMMENT '1: Percent, 2: Value',
  is_foc tinyint(1) DEFAULT '0',
  has_offer tinyint(1) DEFAULT '0',
  grn_invoice_link_id bigint(11) DEFAULT '0',
  expiry_date date DEFAULT NULL,
  approval_status tinyint(1) DEFAULT '1',
  approved_by int(11) DEFAULT '0',
  approval_date datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  is_processed_upd tinyint(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY grn_id (grn_id),
  KEY po_id (po_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_imeino_allotment_track'
--

CREATE TABLE IF NOT EXISTS t_imeino_allotment_track (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  imeino_id bigint(11) DEFAULT '0',
  product_id bigint(11) DEFAULT '0',
  imei_no varchar(255) DEFAULT NULL,
  order_id bigint(20) DEFAULT '0',
  invoice_no bigint(20) DEFAULT '0',
  transid varchar(255) DEFAULT NULL,
  is_cancelled int(1) DEFAULT '0',
  alloted_on datetime DEFAULT NULL,
  cancelled_on datetime DEFAULT NULL,
  alloted_by bigint(11) DEFAULT '0',
  cancelled_by bigint(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_imei_no'
--

CREATE TABLE IF NOT EXISTS t_imei_no (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  product_id int(10) unsigned NOT NULL,
  imei_no varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL,
  grn_id int(10) unsigned NOT NULL,
  stock_id bigint(11) DEFAULT '0',
  is_returned tinyint(1) DEFAULT '0',
  return_prod_id bigint(11) DEFAULT '0',
  order_id bigint(20) unsigned NOT NULL,
  is_imei_activated tinyint(1) DEFAULT '0',
  imei_activated_on datetime DEFAULT NULL,
  activated_by int(11) DEFAULT '0',
  activated_mob_no varchar(20) DEFAULT NULL,
  activated_member_id int(11) DEFAULT '0',
  ref_credit_note_id bigint(11) DEFAULT '0',
  created_on bigint(20) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id),
  KEY activated_mob_no (activated_mob_no),
  KEY activated_member_id (activated_member_id),
  KEY order_id (order_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_imei_update_log'
--

CREATE TABLE IF NOT EXISTS t_imei_update_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  imei_no varchar(255) DEFAULT NULL,
  product_id bigint(11) DEFAULT '0',
  stock_id bigint(11) DEFAULT '0',
  grn_id bigint(11) DEFAULT '0',
  alloted_order_id bigint(11) DEFAULT '0',
  alloted_on datetime DEFAULT NULL,
  invoice_no bigint(11) DEFAULT '0',
  return_id bigint(11) DEFAULT '0',
  is_cancelled tinyint(1) DEFAULT '0',
  cancelled_on datetime DEFAULT NULL,
  is_active tinyint(1) DEFAULT '0',
  logged_on datetime DEFAULT NULL,
  logged_by int(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_invoice_credit_notes'
--

CREATE TABLE IF NOT EXISTS t_invoice_credit_notes (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT '1' COMMENT '1:Invoice,2:IMEI Scheme',
  grp_no bigint(11) DEFAULT '0',
  franchise_id bigint(11) DEFAULT '0',
  invoice_no bigint(11) DEFAULT '0',
  amount double DEFAULT '0',
  is_active tinyint(1) DEFAULT '0',
  ref_id bigint(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY invoice_no (invoice_no),
  KEY franchise_id (franchise_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_outscanentry_log'
--

CREATE TABLE IF NOT EXISTS t_outscanentry_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  outscan_no varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_list'
--

CREATE TABLE IF NOT EXISTS t_paf_list (
  id int(11) NOT NULL AUTO_INCREMENT,
  handled_by varchar(255) DEFAULT NULL,
  handled_by_mob varchar(255) DEFAULT NULL,
  paf_status tinyint(1) DEFAULT '0',
  cancelled_on datetime DEFAULT NULL,
  cancelled_by int(11) DEFAULT '0',
  remarks text,
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_productlist'
--

CREATE TABLE IF NOT EXISTS t_paf_productlist (
  id int(11) NOT NULL AUTO_INCREMENT,
  paf_id int(11) DEFAULT '0',
  product_id bigint(11) DEFAULT '0',
  vendor_id int(11) DEFAULT '0',
  qty double DEFAULT NULL,
  mrp double DEFAULT NULL,
  notify_handler tinyint(1) DEFAULT '0',
  po_id int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_smslog'
--

CREATE TABLE IF NOT EXISTS t_paf_smslog (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  paf_id int(11) DEFAULT NULL,
  handled_by int(11) DEFAULT NULL,
  message text,
  `status` tinyint(1) DEFAULT '0',
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_partner_manifesto_log'
--

CREATE TABLE IF NOT EXISTS t_partner_manifesto_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  serial_no bigint(11) DEFAULT '0',
  partner_id int(11) DEFAULT '0',
  invoice_no bigint(11) DEFAULT '0',
  partner_order_no varchar(40) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY partner_order_no (partner_order_no),
  KEY partner_id (serial_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_pending_voucher_document_link'
--

CREATE TABLE IF NOT EXISTS t_pending_voucher_document_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  voucher_id int(11) DEFAULT NULL,
  adjusted_amount double unsigned DEFAULT NULL,
  ref_doc_id int(11) DEFAULT NULL,
  ref_doc_type tinyint(1) DEFAULT '1' COMMENT '1: GRN, 2: PO',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_pending_voucher_info'
--

CREATE TABLE IF NOT EXISTS t_pending_voucher_info (
  voucher_id int(11) NOT NULL AUTO_INCREMENT,
  voucher_type_id int(11) DEFAULT '1' COMMENT '1: Payment Voucher, 2: Receipt Voucher',
  voucher_date datetime DEFAULT NULL,
  voucher_value double unsigned DEFAULT NULL,
  payment_mode tinyint(1) DEFAULT '0' COMMENT '1-Cash, 2-Cheque, 3-DD, 4-Bank Transfers',
  instrument_no varchar(20) DEFAULT NULL,
  instrument_date datetime DEFAULT NULL,
  instrument_issued_bank varchar(200) DEFAULT NULL,
  narration varchar(500) DEFAULT NULL,
  active_status tinyint(1) DEFAULT '1',
  is_reveresed tinyint(1) DEFAULT '0',
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_pnh_taskactivity'
--

CREATE TABLE IF NOT EXISTS t_pnh_taskactivity (
  id int(11) NOT NULL AUTO_INCREMENT,
  start_date varchar(255) NOT NULL,
  end_date varchar(255) NOT NULL,
  task_id int(11) DEFAULT NULL,
  msg varchar(255) NOT NULL,
  task_status int(11) DEFAULT NULL,
  logged_by int(11) DEFAULT NULL,
  logged_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_po_info'
--

CREATE TABLE IF NOT EXISTS t_po_info (
  po_id bigint(11) NOT NULL AUTO_INCREMENT,
  vendor_id int(11) DEFAULT NULL,
  remarks varchar(255) DEFAULT NULL,
  approval_status tinyint(1) DEFAULT '0',
  approved_by int(11) DEFAULT '0',
  approval_date datetime DEFAULT NULL,
  payment_status tinyint(1) DEFAULT '0' COMMENT 'this can be updated based on payment done to vendor',
  po_status tinyint(1) DEFAULT NULL,
  total_value decimal(10,2) unsigned NOT NULL,
  paf_id bigint(11) DEFAULT NULL,
  date_of_delivery datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  status_remarks varchar(2555) DEFAULT NULL,
  PRIMARY KEY (po_id),
  KEY vendor_id (vendor_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_po_product_link'
--

CREATE TABLE IF NOT EXISTS t_po_product_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  po_id int(11) DEFAULT NULL,
  product_id int(11) DEFAULT NULL,
  order_qty double DEFAULT NULL,
  received_qty double DEFAULT '0',
  mrp decimal(15,4) DEFAULT NULL,
  dp_price decimal(15,4) DEFAULT '0.0000',
  margin decimal(10,4) DEFAULT '0.0000',
  scheme_discount_value decimal(15,4) DEFAULT '0.0000',
  scheme_discount_type tinyint(1) DEFAULT '1' COMMENT '1: Percent, 2: Value',
  purchase_price decimal(15,4) DEFAULT '0.0000',
  is_foc tinyint(1) DEFAULT '0',
  has_offer tinyint(1) DEFAULT '0',
  special_note varchar(200) DEFAULT NULL,
  alert_qty_mismatch tinyint(1) DEFAULT '0',
  is_active tinyint(1) DEFAULT '1',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id),
  KEY po_id (po_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_process_partialqty_orders'
--

CREATE TABLE IF NOT EXISTS t_process_partialqty_orders (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  transid varchar(50) DEFAULT NULL,
  oid bigint(11) DEFAULT '0',
  new_oid bigint(11) DEFAULT '0',
  qty double DEFAULT NULL,
  new_qty double DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  created_by bigint(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_refund_info'
--

CREATE TABLE IF NOT EXISTS t_refund_info (
  refund_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  transid varchar(20) NOT NULL,
  invoice_no bigint(11) DEFAULT NULL,
  amount decimal(10,2) unsigned NOT NULL,
  refund_for varchar(20) DEFAULT 'cancel',
  `status` tinyint(3) unsigned NOT NULL,
  created_on bigint(20) unsigned NOT NULL,
  created_by int(10) unsigned NOT NULL,
  modified_on bigint(20) unsigned NOT NULL,
  modified_by bigint(20) unsigned NOT NULL,
  PRIMARY KEY (refund_id),
  KEY transid (transid)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_refund_order_item_link'
--

CREATE TABLE IF NOT EXISTS t_refund_order_item_link (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  refund_id bigint(20) unsigned NOT NULL,
  order_id bigint(20) unsigned NOT NULL,
  invoice_no int(11) DEFAULT NULL,
  qty int(10) unsigned NOT NULL,
  refund_amt double DEFAULT NULL,
  PRIMARY KEY (id),
  KEY refund_id (refund_id),
  KEY order_id (order_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_reserved_batch_stock'
--

CREATE TABLE IF NOT EXISTS t_reserved_batch_stock (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  batch_id bigint(11) DEFAULT '0',
  p_invoice_no bigint(11) DEFAULT '0',
  product_id bigint(11) DEFAULT '0',
  stock_info_id bigint(11) DEFAULT '0',
  order_id bigint(11) DEFAULT '0',
  qty double DEFAULT '0',
  extra_qty double DEFAULT '0',
  release_qty double DEFAULT '0',
  reserved_on bigint(20) DEFAULT NULL,
  released_on bigint(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  tmp_prev_stk_id bigint(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY batch_id (batch_id),
  KEY p_invoice_no (p_invoice_no),
  KEY product_id (product_id),
  KEY stock_info_id (stock_info_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_info'
--

CREATE TABLE IF NOT EXISTS t_stock_info (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT '0',
  location_id int(11) DEFAULT '0',
  rack_bin_id int(11) DEFAULT '0',
  mrp decimal(15,4) DEFAULT '0.0000',
  available_qty double DEFAULT '0',
  product_barcode varchar(50) DEFAULT NULL,
  in_transit double DEFAULT '0',
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  tmp_brandid double DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_info_copy'
--

CREATE TABLE IF NOT EXISTS t_stock_info_copy (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT NULL,
  location_id int(11) DEFAULT NULL,
  rack_bin_id int(11) DEFAULT NULL,
  mrp decimal(15,4) DEFAULT '0.0000',
  available_qty double DEFAULT '0',
  in_transit double DEFAULT '0',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  tmp_brandid double DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_update_log'
--

CREATE TABLE IF NOT EXISTS t_stock_update_log (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) DEFAULT NULL,
  update_type tinyint(1) DEFAULT '0' COMMENT '0: Out, 1: In',
  p_invoice_id int(10) unsigned NOT NULL,
  corp_invoice_id bigint(11) DEFAULT NULL,
  invoice_id bigint(11) DEFAULT NULL,
  grn_id int(11) DEFAULT NULL,
  voucher_book_slno varchar(255) DEFAULT NULL,
  return_prod_id bigint(11) DEFAULT '0',
  qty double DEFAULT NULL,
  current_stock double DEFAULT NULL,
  msg varchar(255) NOT NULL,
  mrp_change_updated tinyint(1) DEFAULT '-1' COMMENT '0: no,1: yes,-1:not from stock intake',
  stock_info_id bigint(11) DEFAULT '0',
  stock_qty int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  created_by int(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_trans_invoice_marker'
--

CREATE TABLE IF NOT EXISTS t_trans_invoice_marker (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  transid varchar(50) DEFAULT NULL,
  invoice_no bigint(11) DEFAULT '0',
  is_pnh tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY invoice_no (invoice_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_trans_proforma_invoice_marker'
--

CREATE TABLE IF NOT EXISTS t_trans_proforma_invoice_marker (
  id bigint(11) NOT NULL AUTO_INCREMENT,
  transid varchar(50) DEFAULT NULL,
  p_invoice_no bigint(11) DEFAULT '0',
  is_pnh tinyint(1) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY p_invoice_no (p_invoice_no)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_upd_product_deal_link_log'
--

CREATE TABLE IF NOT EXISTS t_upd_product_deal_link_log (
  id bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  itemid bigint(20) unsigned DEFAULT NULL,
  product_id int(11) unsigned DEFAULT NULL,
  product_mrp decimal(15,4) DEFAULT '0.0000',
  qty int(11) DEFAULT '1',
  is_updated int(11) DEFAULT '0',
  is_sit int(11) DEFAULT '0',
  perform_on datetime DEFAULT NULL,
  perform_by int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid),
  KEY product_id (product_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_document_link'
--

CREATE TABLE IF NOT EXISTS t_voucher_document_link (
  id int(11) NOT NULL AUTO_INCREMENT,
  voucher_id int(11) DEFAULT NULL,
  adjusted_amount double unsigned DEFAULT NULL,
  ref_doc_id int(11) DEFAULT NULL,
  ref_doc_type tinyint(1) DEFAULT '1' COMMENT '1: GRN, 2: PO',
  created_by int(11) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_expense_link'
--

CREATE TABLE IF NOT EXISTS t_voucher_expense_link (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  voucher_id bigint(20) unsigned NOT NULL,
  expense_type tinyint(1) unsigned NOT NULL,
  bill_no varchar(30) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_info'
--

CREATE TABLE IF NOT EXISTS t_voucher_info (
  voucher_id int(11) NOT NULL AUTO_INCREMENT,
  voucher_type_id int(11) DEFAULT '1' COMMENT '1: Payment Voucher, 2: Receipt Voucher',
  voucher_date datetime DEFAULT NULL,
  voucher_value double unsigned DEFAULT NULL,
  payment_mode tinyint(1) DEFAULT '0' COMMENT '1-Cash, 2-Cheque, 3-DD, 4-Bank Transfers',
  instrument_no varchar(20) DEFAULT NULL,
  instrument_date datetime DEFAULT NULL,
  instrument_issued_bank varchar(200) DEFAULT NULL,
  narration varchar(500) DEFAULT NULL,
  active_status tinyint(1) DEFAULT '1',
  is_reveresed tinyint(1) DEFAULT '0',
  created_by int(11) DEFAULT '0',
  created_on datetime DEFAULT NULL,
  modified_by int(11) DEFAULT '0',
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'user_access_roles'
--

CREATE TABLE IF NOT EXISTS user_access_roles (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_role varchar(100) NOT NULL,
  const_name varchar(100) NOT NULL,
  `value` bigint(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=InnoDB ;

-- --------------------------------------------------------

--
-- Table structure for table 'variant_deal_link'
--

CREATE TABLE IF NOT EXISTS variant_deal_link (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  variant_id bigint(20) unsigned NOT NULL,
  item_id bigint(20) unsigned NOT NULL,
  variant_value varchar(100) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'variant_info'
--

CREATE TABLE IF NOT EXISTS variant_info (
  variant_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  variant_name varchar(150) NOT NULL,
  variant_type tinyint(1) unsigned NOT NULL,
  created_by bigint(20) unsigned NOT NULL,
  created_on datetime NOT NULL,
  modified_on datetime NOT NULL,
  PRIMARY KEY (variant_id)
) TYPE=MyISAM;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
