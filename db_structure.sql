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
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_id BIGINT(20) UNSIGNED NOT NULL,
  `subject` VARCHAR(150) NOT NULL,
  msg TEXT NOT NULL,
  `from` VARCHAR(150) NOT NULL,
  created_on DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY ticket_id (ticket_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'auto_readmail_uid'
--

CREATE TABLE IF NOT EXISTS auto_readmail_uid (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  im_uid BIGINT(20) UNSIGNED NOT NULL,
  `time` DATETIME NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'backup_t_stock_info_mar_03'
--

CREATE TABLE IF NOT EXISTS backup_t_stock_info_mar_03 (
  stock_id INT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT NULL,
  location_id INT(11) DEFAULT NULL,
  rack_bin_id INT(11) DEFAULT NULL,
  mrp DECIMAL(15,4) DEFAULT '0.0000',
  available_qty DOUBLE DEFAULT '0',
  in_transit DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  tmp_brandid DOUBLE DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_imei_no'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_imei_no (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT(10) UNSIGNED NOT NULL,
  imei_no VARCHAR(20) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  grn_id INT(10) UNSIGNED NOT NULL,
  stock_id BIGINT(11) DEFAULT '0',
  is_returned TINYINT(1) DEFAULT '0',
  return_prod_id BIGINT(11) DEFAULT '0',
  order_id BIGINT(20) UNSIGNED NOT NULL,
  is_imei_activated TINYINT(1) DEFAULT '0',
  imei_activated_on DATETIME DEFAULT NULL,
  activated_by INT(11) DEFAULT '0',
  activated_mob_no VARCHAR(20) DEFAULT NULL,
  activated_member_id INT(11) DEFAULT '0',
  ref_credit_note_id BIGINT(11) DEFAULT '0',
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_reserved_batch_stock'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_reserved_batch_stock (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  batch_id BIGINT(11) DEFAULT '0',
  p_invoice_no BIGINT(11) DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0',
  stock_info_id BIGINT(11) DEFAULT '0',
  order_id BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT '0',
  extra_qty DOUBLE DEFAULT '0',
  release_qty DOUBLE DEFAULT '0',
  reserved_on BIGINT(20) DEFAULT NULL,
  released_on BIGINT(20) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  tmp_prev_stk_id BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY batch_id (batch_id),
  KEY p_invoice_no (p_invoice_no),
  KEY product_id (product_id),
  KEY stock_info_id (stock_info_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_stock_info'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_stock_info (
  stock_id INT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT '0',
  location_id INT(11) DEFAULT '0',
  rack_bin_id INT(11) DEFAULT '0',
  mrp DECIMAL(15,4) DEFAULT '0.0000',
  available_qty DOUBLE DEFAULT '0',
  product_barcode VARCHAR(50) DEFAULT NULL,
  in_transit DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  tmp_brandid DOUBLE DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_15oct_t_stock_update_log'
--

CREATE TABLE IF NOT EXISTS bck_15oct_t_stock_update_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT NULL,
  update_type TINYINT(1) DEFAULT '0' COMMENT '0: Out, 1: In',
  p_invoice_id INT(10) UNSIGNED NOT NULL,
  corp_invoice_id BIGINT(11) DEFAULT NULL,
  invoice_id BIGINT(11) DEFAULT NULL,
  grn_id INT(11) DEFAULT NULL,
  voucher_book_slno VARCHAR(255) DEFAULT NULL,
  return_prod_id BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT NULL,
  current_stock DOUBLE DEFAULT NULL,
  msg VARCHAR(255) NOT NULL,
  mrp_change_updated TINYINT(1) DEFAULT '-1' COMMENT '0: no,1: yes,-1:not from stock intake',
  stock_info_id BIGINT(11) DEFAULT '0',
  stock_qty INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_up_king_invoice_2013apr1'
--

CREATE TABLE IF NOT EXISTS bck_up_king_invoice_2013apr1 (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  transid CHAR(18) NOT NULL,
  order_id BIGINT(20) UNSIGNED NOT NULL,
  mrp INT(10) UNSIGNED NOT NULL,
  discount DECIMAL(10,2) UNSIGNED NOT NULL,
  nlc DECIMAL(10,2) UNSIGNED NOT NULL,
  phc DECIMAL(10,2) UNSIGNED NOT NULL,
  tax DOUBLE UNSIGNED NOT NULL,
  service_tax DOUBLE NOT NULL,
  cod DOUBLE UNSIGNED NOT NULL,
  ship DOUBLE UNSIGNED NOT NULL,
  giftwrap_charge DOUBLE DEFAULT '0',
  invoice_status TINYINT(1) DEFAULT '0',
  createdon BIGINT(20) DEFAULT NULL,
  cancelled_on BIGINT(20) DEFAULT NULL,
  delivery_medium VARCHAR(255) DEFAULT '0',
  tracking_id VARCHAR(50) DEFAULT '0',
  shipdatetime DATETIME DEFAULT NULL,
  notify_customer TINYINT(1) DEFAULT '0',
  is_delivered TINYINT(1) DEFAULT '0',
  is_partial_invoice TINYINT(1) DEFAULT '0',
  total_prints INT(5) DEFAULT '0',
  outscanned_on BIGINT(20) DEFAULT NULL,
  is_outscanned TINYINT(1) DEFAULT '0',
  is_b2b TINYINT(1) NOT NULL,
  old_pnh_inv_no BIGINT(20) DEFAULT '0',
  new_pnh_inv_no BIGINT(20) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY order_id (order_id),
  KEY invoice_no (invoice_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'bck_up_t_stock_info_mar17'
--

CREATE TABLE IF NOT EXISTS bck_up_t_stock_info_mar17 (
  stock_id INT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT NULL,
  location_id INT(11) DEFAULT NULL,
  rack_bin_id INT(11) DEFAULT NULL,
  mrp DECIMAL(15,4) DEFAULT '0.0000',
  available_qty DOUBLE DEFAULT '0',
  product_barcode VARCHAR(50) DEFAULT NULL,
  in_transit DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  tmp_brandid DOUBLE DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'ci_sessions'
--

CREATE TABLE IF NOT EXISTS ci_sessions (
  session_id VARCHAR(40) NOT NULL DEFAULT '0',
  ip_address VARCHAR(16) NOT NULL DEFAULT '0',
  user_agent VARCHAR(50) NOT NULL,
  last_activity INT(10) UNSIGNED NOT NULL DEFAULT '0',
  user_data TEXT NOT NULL,
  PRIMARY KEY (session_id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cod_pincodes'
--

CREATE TABLE IF NOT EXISTS cod_pincodes (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  pincode VARCHAR(8) DEFAULT NULL,
  OLD VARCHAR(3) DEFAULT NULL,
  `name` VARCHAR(24) DEFAULT NULL,
  state VARCHAR(14) DEFAULT NULL,
  zone VARCHAR(4) DEFAULT NULL,
  region VARCHAR(6) DEFAULT NULL,
  control VARCHAR(9) DEFAULT NULL,
  cod VARCHAR(3) DEFAULT NULL,
  `type` VARCHAR(40) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_admin'
--

CREATE TABLE IF NOT EXISTS cou_admin (
  userid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  usertype INT(10) UNSIGNED NOT NULL,
  username VARCHAR(80) NOT NULL,
  `password` CHAR(32) NOT NULL,
  mobile BIGINT(20) UNSIGNED NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  created_date BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_admin_details'
--

CREATE TABLE IF NOT EXISTS cou_admin_details (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  address TEXT NOT NULL,
  AREA VARCHAR(150) NOT NULL,
  city VARCHAR(150) NOT NULL,
  pincode INT(10) UNSIGNED NOT NULL,
  telephone VARCHAR(60) NOT NULL,
  modified_time BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon'
--

CREATE TABLE IF NOT EXISTS cou_coupon (
  coupon CHAR(16) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  denomination INT(10) UNSIGNED NOT NULL,
  valid_upto BIGINT(20) UNSIGNED NOT NULL,
  used_on BIGINT(20) UNSIGNED NOT NULL,
  UNIQUE KEY coupon (coupon)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon_details'
--

CREATE TABLE IF NOT EXISTS cou_coupon_details (
  coupon CHAR(16) NOT NULL,
  sku1 CHAR(3) NOT NULL,
  sku2 INT(10) UNSIGNED NOT NULL,
  distributor BIGINT(20) UNSIGNED NOT NULL,
  retailer BIGINT(20) UNSIGNED NOT NULL,
  `user` BIGINT(20) UNSIGNED NOT NULL,
  created_date BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (coupon)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_coupon_history'
--

CREATE TABLE IF NOT EXISTS cou_coupon_history (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  num INT(10) UNSIGNED NOT NULL,
  `start` CHAR(15) NOT NULL,
  `end` CHAR(15) NOT NULL,
  distributor BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_denominations'
--

CREATE TABLE IF NOT EXISTS cou_denominations (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cou_user'
--

CREATE TABLE IF NOT EXISTS cou_user (
  userid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  mobile BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'cron_image_updater_lock'
--

CREATE TABLE IF NOT EXISTS cron_image_updater_lock (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  is_locked TINYINT(1) NOT NULL,
  modified_by INT(10) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  finish_status TINYINT(4) NOT NULL,
  finished_on BIGINT(20) UNSIGNED NOT NULL,
  images_updated INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'cron_log'
--

CREATE TABLE IF NOT EXISTS cron_log (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  cron INT(10) UNSIGNED NOT NULL,
  COUNT INT(10) UNSIGNED NOT NULL,
  `start` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_auth'
--

CREATE TABLE IF NOT EXISTS data_api_auth (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock` VARCHAR(50) NOT NULL,
  `key` CHAR(32) NOT NULL,
  last_login BIGINT(20) UNSIGNED NOT NULL,
  is_enabled TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_tokens'
--

CREATE TABLE IF NOT EXISTS data_api_tokens (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  token CHAR(32) NOT NULL,
  auth_id INT(10) UNSIGNED NOT NULL,
  expires_on BIGINT(20) NOT NULL,
  PRIMARY KEY (id),
  KEY token (token)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'data_api_tokens_bak'
--

CREATE TABLE IF NOT EXISTS data_api_tokens_bak (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  token CHAR(32) NOT NULL,
  auth_id INT(10) UNSIGNED NOT NULL,
  expires_on BIGINT(20) NOT NULL,
  PRIMARY KEY (id),
  KEY token (token)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deals_bulk_upload'
--

CREATE TABLE IF NOT EXISTS deals_bulk_upload (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  items INT(10) UNSIGNED NOT NULL,
  is_all_image_updated TINYINT(1) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deals_bulk_upload_items'
--

CREATE TABLE IF NOT EXISTS deals_bulk_upload_items (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  bulk_id INT(10) UNSIGNED NOT NULL,
  item_id BIGINT(20) UNSIGNED NOT NULL,
  is_image_updated TINYINT(1) NOT NULL,
  updated_on BIGINT(20) UNSIGNED NOT NULL,
  updated_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY bulk_id (bulk_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'deal_price_changelog'
--

CREATE TABLE IF NOT EXISTS deal_price_changelog (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  old_mrp DECIMAL(10,2) NOT NULL,
  new_mrp DECIMAL(10,2) NOT NULL,
  new_price DECIMAL(10,2) UNSIGNED NOT NULL,
  old_price DECIMAL(10,2) NOT NULL,
  reference_grn INT(10) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'discontinued'
--

CREATE TABLE IF NOT EXISTS discontinued (
  catid INT(3) DEFAULT NULL,
  brandid INT(8) DEFAULT NULL,
  cat VARCHAR(20) DEFAULT NULL,
  brand VARCHAR(18) DEFAULT NULL,
  `No of deals` INT(3) DEFAULT NULL
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'fb_miscusers'
--

CREATE TABLE IF NOT EXISTS fb_miscusers (
  id BIGINT(20) UNSIGNED NOT NULL,
  fid VARCHAR(100) NOT NULL,
  birthday CHAR(15) NOT NULL,
  age INT(10) UNSIGNED NOT NULL,
  home VARCHAR(100) NOT NULL,
  location VARCHAR(100) NOT NULL,
  gender CHAR(15) NOT NULL,
  lastupdate BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'franchise_suspension_log'
--

CREATE TABLE IF NOT EXISTS franchise_suspension_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT '0',
  suspension_type TINYINT(3) DEFAULT '0',
  reason VARCHAR(25555) DEFAULT NULL,
  suspended_on BIGINT(11) DEFAULT '0',
  suspended_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'group_log'
--

CREATE TABLE IF NOT EXISTS group_log (
  id INT(11) NOT NULL AUTO_INCREMENT,
  emp_id INT(11) DEFAULT NULL,
  `type` VARCHAR(2555) DEFAULT NULL,
  grp_msg VARCHAR(25555) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'imei_m_scheme'
--

CREATE TABLE IF NOT EXISTS imei_m_scheme (
  id INT(11) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT NULL,
  menuid BIGINT(11) DEFAULT NULL,
  categoryid BIGINT(20) DEFAULT NULL,
  brandid BIGINT(20) DEFAULT NULL,
  scheme_type TINYINT(11) DEFAULT NULL,
  credit_value DOUBLE(10,2) DEFAULT NULL,
  scheme_from BIGINT(20) DEFAULT NULL,
  scheme_to BIGINT(20) DEFAULT NULL,
  sch_apply_from BIGINT(20) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  modified_by TINYINT(11) DEFAULT NULL,
  is_active TINYINT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brandid (brandid),
  KEY categoryid (categoryid),
  KEY franchise_id (franchise_id),
  KEY menuid (menuid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_activity'
--

CREATE TABLE IF NOT EXISTS king_activity (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid CHAR(32) NOT NULL,
  msg TEXT NOT NULL,
  dealid BIGINT(20) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_address'
--

CREATE TABLE IF NOT EXISTS king_address (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  address TEXT NOT NULL,
  city TEXT NOT NULL,
  pincode VARCHAR(20) NOT NULL,
  shipbill TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin'
--

CREATE TABLE IF NOT EXISTS king_admin (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id CHAR(32) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  username VARCHAR(150) NOT NULL,
  `password` CHAR(32) NOT NULL,
  usertype ENUM('1','2','3') NOT NULL,
  role_id INT(11) DEFAULT '0',
  access BIGINT(20) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  mobile VARCHAR(255) DEFAULT NULL,
  phone VARCHAR(255) NOT NULL,
  gender VARCHAR(20) DEFAULT NULL,
  address TEXT,
  city VARCHAR(100) DEFAULT NULL,
  img_url VARCHAR(255) DEFAULT NULL,
  account_blocked TINYINT(1) DEFAULT '0',
  createdon DATETIME NOT NULL,
  modifiedon DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY user_id (user_id),
  KEY user_id_2 (user_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin_activity'
--

CREATE TABLE IF NOT EXISTS king_admin_activity (
  id INT(11) NOT NULL AUTO_INCREMENT,
  activity TEXT,
  created_by VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_admin_old'
--

CREATE TABLE IF NOT EXISTS king_admin_old (
  user_id CHAR(32) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `password` CHAR(32) NOT NULL,
  usertype ENUM('1','2','3') NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NOT NULL,
  createdon DATETIME NOT NULL,
  modifiedon DATETIME NOT NULL,
  PRIMARY KEY (user_id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_agents'
--

CREATE TABLE IF NOT EXISTS king_agents (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  via_uid VARCHAR(60) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  balance INT(10) UNSIGNED NOT NULL,
  created_date BIGINT(20) UNSIGNED NOT NULL,
  last_login BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_agent_transactions'
--

CREATE TABLE IF NOT EXISTS king_agent_transactions (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  agentid VARCHAR(60) NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  orderid BIGINT(20) UNSIGNED NOT NULL,
  via_transid VARCHAR(100) NOT NULL,
  price INT(10) UNSIGNED NOT NULL,
  com INT(10) UNSIGNED NOT NULL,
  qty INT(10) UNSIGNED NOT NULL,
  paid INT(10) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_announcements'
--

CREATE TABLE IF NOT EXISTS king_announcements (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(200) NOT NULL,
  url VARCHAR(200) NOT NULL,
  `enable` TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_api_logins'
--

CREATE TABLE IF NOT EXISTS king_api_logins (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  auth CHAR(32) NOT NULL,
  last_login BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY auth (auth)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_audit'
--

CREATE TABLE IF NOT EXISTS king_audit (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  description TEXT NOT NULL,
  `user` VARCHAR(100) NOT NULL,
  credit INT(10) UNSIGNED NOT NULL,
  debit INT(10) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_barcodes'
--

CREATE TABLE IF NOT EXISTS king_barcodes (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  barcode VARCHAR(30) NOT NULL,
  PRIMARY KEY (id),
  KEY barcode (barcode)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boarders'
--

CREATE TABLE IF NOT EXISTS king_boarders (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  pic CHAR(12) NOT NULL,
  username CHAR(25) NOT NULL,
  boards INT(10) UNSIGNED NOT NULL,
  tags INT(10) UNSIGNED NOT NULL,
  loves INT(10) UNSIGNED NOT NULL,
  comments INT(10) UNSIGNED NOT NULL,
  followers INT(10) UNSIGNED NOT NULL,
  following INT(10) UNSIGNED NOT NULL,
  facebook VARCHAR(200) NOT NULL,
  twitter VARCHAR(200) NOT NULL,
  linkedin VARCHAR(200) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY userid (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boarder_followers'
--

CREATE TABLE IF NOT EXISTS king_boarder_followers (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  follower BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_boards'
--

CREATE TABLE IF NOT EXISTS king_boards (
  bid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  catid INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  url VARCHAR(50) NOT NULL,
  tags INT(10) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  public TINYINT(1) NOT NULL,
  followers INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (bid),
  KEY url (url)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_activity'
--

CREATE TABLE IF NOT EXISTS king_board_activity (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  userid2 BIGINT(20) UNSIGNED NOT NULL,
  boardid BIGINT(20) UNSIGNED NOT NULL,
  tagid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_cats'
--

CREATE TABLE IF NOT EXISTS king_board_cats (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_board_followers'
--

CREATE TABLE IF NOT EXISTS king_board_followers (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  bid BIGINT(20) UNSIGNED NOT NULL,
  follower BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_brands'
--

CREATE TABLE IF NOT EXISTS king_brands (
  sno BIGINT(20) NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  url VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  logoid CHAR(32) DEFAULT NULL,
  address TEXT NOT NULL,
  website VARCHAR(50) NOT NULL,
  email VARCHAR(80) NOT NULL,
  admin VARCHAR(255) NOT NULL,
  featured_start BIGINT(20) UNSIGNED NOT NULL,
  featured_end BIGINT(20) UNSIGNED NOT NULL,
  createdon DATETIME NOT NULL,
  modifiedon DATETIME NOT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY id (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_bulkorders_invoices'
--

CREATE TABLE IF NOT EXISTS king_bulkorders_invoices (
  id INT(11) NOT NULL AUTO_INCREMENT,
  allotment_no INT(11) DEFAULT '0',
  invoice_nos TEXT,
  tot_printed INT(3) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_buyprocess'
--

CREATE TABLE IF NOT EXISTS king_buyprocess (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  bpid BIGINT(20) UNSIGNED NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `hash` CHAR(32) NOT NULL,
  isrefund TINYINT(1) NOT NULL DEFAULT '1',
  `status` TINYINT(3) UNSIGNED NOT NULL,
  done_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_callcenter'
--

CREATE TABLE IF NOT EXISTS king_callcenter (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL,
  `password` CHAR(32) NOT NULL,
  enabled TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaigns'
--

CREATE TABLE IF NOT EXISTS king_campaigns (
  id INT(11) NOT NULL AUTO_INCREMENT,
  campaign_no VARCHAR(255) DEFAULT NULL,
  campaign_type VARCHAR(255) DEFAULT NULL,
  title VARCHAR(255) DEFAULT NULL,
  description TEXT,
  banner_image VARCHAR(255) DEFAULT NULL,
  banner_link VARCHAR(2024) DEFAULT NULL,
  campaign_cycle VARCHAR(100) DEFAULT NULL,
  campaign_start DATETIME DEFAULT NULL,
  campagin_end DATETIME DEFAULT NULL,
  template_id VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaigns_deals'
--

CREATE TABLE IF NOT EXISTS king_campaigns_deals (
  id INT(11) NOT NULL AUTO_INCREMENT,
  campaign_no VARCHAR(255) DEFAULT NULL,
  deal_id VARCHAR(255) DEFAULT NULL,
  relative_link VARCHAR(2024) DEFAULT NULL,
  `order` INT(3) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_campaign_templates'
--

CREATE TABLE IF NOT EXISTS king_campaign_templates (
  id INT(11) NOT NULL AUTO_INCREMENT,
  template_filename VARCHAR(255) DEFAULT NULL,
  template_name VARCHAR(255) DEFAULT NULL,
  template_html TEXT,
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_carts'
--

CREATE TABLE IF NOT EXISTS king_carts (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  cart TEXT NOT NULL,
  updated BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_cashbacks (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  amount INT(10) UNSIGNED NOT NULL,
  userid INT(11) NOT NULL,
  url CHAR(40) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  claim_time BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks_config'
--

CREATE TABLE IF NOT EXISTS king_cashbacks_config (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` INT(10) UNSIGNED NOT NULL,
  MIN INT(10) UNSIGNED NOT NULL,
  validity INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashbacks_track'
--

CREATE TABLE IF NOT EXISTS king_cashbacks_track (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  coupon CHAR(15) NOT NULL,
  transid CHAR(20) NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_cashback_campaigns'
--

CREATE TABLE IF NOT EXISTS king_cashback_campaigns (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  cashback DOUBLE(5,2) NOT NULL,
  `starts` BIGINT(20) UNSIGNED NOT NULL,
  expires BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  min_trans_amount INT(10) UNSIGNED NOT NULL,
  coupon_valid INT(10) UNSIGNED NOT NULL,
  coupons_num INT(10) UNSIGNED NOT NULL,
  coupon_min_order INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_catbrand'
--

CREATE TABLE IF NOT EXISTS king_catbrand (
  id INT(11) NOT NULL AUTO_INCREMENT,
  catid INT(11) NOT NULL,
  brandid BIGINT(20) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_categories'
--

CREATE TABLE IF NOT EXISTS king_categories (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` INT(11) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  url VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  catimage VARCHAR(50) NOT NULL,
  prior SMALLINT(5) UNSIGNED NOT NULL DEFAULT '100',
  PRIMARY KEY (id),
  KEY url (url)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_comments'
--

CREATE TABLE IF NOT EXISTS king_comments (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  dealid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  flag TINYINT(1) NOT NULL,
  `new` TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_contact'
--

CREATE TABLE IF NOT EXISTS king_contact (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid INT(10) UNSIGNED NOT NULL,
  `subject` TEXT NOT NULL,
  message TEXT NOT NULL,
  `status` INT(10) UNSIGNED NOT NULL,
  `date` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_corporates'
--

CREATE TABLE IF NOT EXISTS king_corporates (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  alias INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_corp_buys'
--

CREATE TABLE IF NOT EXISTS king_corp_buys (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  corpid BIGINT(20) UNSIGNED NOT NULL,
  buys INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_coupons'
--

CREATE TABLE IF NOT EXISTS king_coupons (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` CHAR(12) NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  `value` INT(10) UNSIGNED NOT NULL,
  brandid VARCHAR(200) NOT NULL,
  catid VARCHAR(200) NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  MIN INT(10) UNSIGNED NOT NULL,
  used INT(10) UNSIGNED NOT NULL,
  unlimited TINYINT(1) NOT NULL,
  referral BIGINT(20) UNSIGNED NOT NULL,
  created BIGINT(20) UNSIGNED NOT NULL,
  expires BIGINT(20) UNSIGNED NOT NULL,
  lastused BIGINT(20) UNSIGNED NOT NULL,
  gift_voucher TINYINT(1) NOT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY `code` (`code`),
  KEY brandid (brandid),
  KEY catid (catid),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_coupon_activity'
--

CREATE TABLE IF NOT EXISTS king_coupon_activity (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` CHAR(13) NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  `value` INT(10) UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL,
  MIN INT(10) UNSIGNED NOT NULL,
  expires BIGINT(20) UNSIGNED NOT NULL,
  unlimited TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_dealitems'
--

CREATE TABLE IF NOT EXISTS king_dealitems (
  sno BIGINT(20) NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  dealid BIGINT(20) UNSIGNED NOT NULL,
  nlc INT(10) UNSIGNED NOT NULL,
  phc INT(10) UNSIGNED NOT NULL,
  shc INT(10) UNSIGNED NOT NULL,
  rsp INT(10) UNSIGNED NOT NULL,
  shipsin VARCHAR(50) NOT NULL,
  shipsto VARCHAR(100) NOT NULL,
  itemcode VARCHAR(100) NOT NULL,
  model VARCHAR(100) NOT NULL,
  price INT(10) UNSIGNED NOT NULL,
  store_price INT(10) UNSIGNED NOT NULL,
  nyp_price INT(10) UNSIGNED NOT NULL,
  billon_orderprice TINYINT(1) DEFAULT '0',
  gender_attr VARCHAR(100) NOT NULL,
  ratings INT(10) UNSIGNED NOT NULL,
  reviews INT(10) UNSIGNED NOT NULL,
  snapits INT(10) UNSIGNED NOT NULL,
  buys INT(10) UNSIGNED NOT NULL,
  loves INT(10) UNSIGNED NOT NULL,
  fcp INT(10) UNSIGNED NOT NULL,
  orgprice INT(10) UNSIGNED NOT NULL,
  viaprice INT(10) UNSIGNED NOT NULL,
  agentcom INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  print_name VARCHAR(150) DEFAULT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  available INT(10) UNSIGNED NOT NULL,
  pic CHAR(50) NOT NULL,
  tagline VARCHAR(250) NOT NULL,
  description1 TEXT NOT NULL,
  description2 TEXT NOT NULL,
  slots TEXT NOT NULL,
  url VARCHAR(150) NOT NULL,
  live TINYINT(1) NOT NULL,
  private TINYINT(1) NOT NULL,
  tellurprice TINYINT(1) NOT NULL,
  b2b TINYINT(1) NOT NULL,
  tax INT(10) UNSIGNED NOT NULL,
  service_tax INT(10) UNSIGNED NOT NULL,
  service_tax_cod INT(10) UNSIGNED NOT NULL,
  bp_expires BIGINT(20) UNSIGNED NOT NULL,
  cod TINYINT(1) NOT NULL,
  groupbuy TINYINT(1) NOT NULL DEFAULT '1',
  sizing VARCHAR(40) NOT NULL DEFAULT '0',
  gender_men TINYINT(1) DEFAULT '0',
  gender_women TINYINT(1) DEFAULT '0',
  gender_unisex TINYINT(1) DEFAULT '0',
  gender_kids TINYINT(1) DEFAULT '0',
  favs TINYINT(1) NOT NULL,
  min_cart_value DOUBLE DEFAULT '0',
  max_allowed_qty INT(11) DEFAULT '5',
  is_featured TINYINT(1) DEFAULT '0',
  cashback INT(10) UNSIGNED NOT NULL,
  bodyparts TINYINT(1) NOT NULL,
  is_pnh TINYINT(1) NOT NULL,
  pnh_id INT(10) UNSIGNED NOT NULL,
  is_combo TINYINT(1) NOT NULL,
  temp_loc VARCHAR(100) DEFAULT NULL,
  move_as_product TINYINT(1) DEFAULT '0',
  tmp_dealid BIGINT(20) DEFAULT NULL,
  tmp_itemid BIGINT(20) DEFAULT NULL,
  created_on DATETIME NOT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  modified_on DATETIME NOT NULL,
  modified_by BIGINT(11) DEFAULT NULL,
  description TEXT NOT NULL,
  created BIGINT(20) UNSIGNED NOT NULL,
  modified BIGINT(20) UNSIGNED NOT NULL,
  hs18_itemid BIGINT(20) DEFAULT NULL,
  hs18_sku_code BIGINT(20) DEFAULT NULL,
  tmp_pnh_itemid BIGINT(20) DEFAULT NULL,
  tmp_pnh_dealid BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY tmp_pnh_itemid (tmp_pnh_itemid),
  KEY url (url),
  KEY dealid (dealid),
  KEY id (id),
  KEY `name` (`name`),
  KEY pnh_id (pnh_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_dealpreviews'
--

CREATE TABLE IF NOT EXISTS king_dealpreviews (
  dealid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  id BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  UNIQUE KEY id (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_deals'
--

CREATE TABLE IF NOT EXISTS king_deals (
  sno BIGINT(20) NOT NULL AUTO_INCREMENT,
  dealid BIGINT(20) UNSIGNED NOT NULL,
  catid INT(10) UNSIGNED NOT NULL,
  brandid BIGINT(15) UNSIGNED NOT NULL,
  vendorid BIGINT(20) UNSIGNED NOT NULL,
  menuid INT(10) UNSIGNED NOT NULL,
  menuid2 INT(10) UNSIGNED NOT NULL,
  startdate INT(10) NOT NULL,
  enddate INT(10) NOT NULL,
  pic CHAR(50) NOT NULL,
  tagline VARCHAR(250) NOT NULL,
  description TEXT NOT NULL,
  description_bak TEXT NOT NULL,
  keywords TEXT NOT NULL,
  dealtype ENUM('0','1','2','3') NOT NULL,
  featured_start BIGINT(20) UNSIGNED NOT NULL,
  featured_end BIGINT(20) UNSIGNED NOT NULL,
  publish INT(1) NOT NULL,
  discontinued TINYINT(1) NOT NULL,
  website VARCHAR(120) NOT NULL,
  email VARCHAR(100) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  phone VARCHAR(80) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(120) NOT NULL,
  state VARCHAR(120) NOT NULL,
  pincode INT(11) NOT NULL,
  created_on DATETIME NOT NULL,
  modified_on DATETIME NOT NULL,
  total_items INT(3) NOT NULL,
  is_giftcard TINYINT(1) DEFAULT '0',
  is_coupon_applicable TINYINT(1) DEFAULT '1',
  catid_old INT(11) DEFAULT '0',
  menuid_old INT(11) DEFAULT '0',
  tmp_pnh_dealid BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (sno),
  UNIQUE KEY tmp_pnh_dealid (tmp_pnh_dealid),
  KEY dealid (dealid),
  KEY brandid (brandid),
  KEY catid (catid),
  KEY tagline (tagline),
  KEY menuid (menuid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_deal_alerts'
--

CREATE TABLE IF NOT EXISTS king_deal_alerts (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  email VARCHAR(100) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  request TINYINT(1) NOT NULL COMMENT '0-request 1-alert',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_facebookers'
--

CREATE TABLE IF NOT EXISTS king_facebookers (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  fbid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY fbid (fbid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_failed_transactions_notify'
--

CREATE TABLE IF NOT EXISTS king_failed_transactions_notify (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(30) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_favs'
--

CREATE TABLE IF NOT EXISTS king_favs (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  catid BIGINT(20) UNSIGNED NOT NULL,
  expires_on BIGINT(20) UNSIGNED NOT NULL,
  added_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_fb_friends'
--

CREATE TABLE IF NOT EXISTS king_fb_friends (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  friends TEXT NOT NULL,
  update_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_fb_mails'
--

CREATE TABLE IF NOT EXISTS king_fb_mails (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from` VARCHAR(100) NOT NULL,
  `to` BIGINT(20) UNSIGNED NOT NULL,
  sub VARCHAR(100) NOT NULL,
  msg TEXT NOT NULL,
  `status` TINYINT(4) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  sent_time BIGINT(20) UNSIGNED NOT NULL,
  expires_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_featured_mails'
--

CREATE TABLE IF NOT EXISTS king_featured_mails (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  url CHAR(15) NOT NULL,
  items TEXT NOT NULL,
  brands TEXT NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_feedback'
--

CREATE TABLE IF NOT EXISTS king_feedback (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` TEXT NOT NULL,
  email VARCHAR(100) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franchisee'
--

CREATE TABLE IF NOT EXISTS king_franchisee (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `password` CHAR(32) NOT NULL,
  email VARCHAR(150) NOT NULL,
  balance INT(10) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(120) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franch_marks'
--

CREATE TABLE IF NOT EXISTS king_franch_marks (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `type` INT(10) UNSIGNED NOT NULL,
  franid BIGINT(20) UNSIGNED NOT NULL,
  mark INT(11) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_franch_transactions'
--

CREATE TABLE IF NOT EXISTS king_franch_transactions (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  franid BIGINT(20) UNSIGNED NOT NULL,
  `name` TEXT NOT NULL,
  withdrawal INT(10) UNSIGNED NOT NULL,
  deposit INT(10) UNSIGNED NOT NULL,
  balance INT(10) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples'
--

CREATE TABLE IF NOT EXISTS king_freesamples (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  MIN INT(10) UNSIGNED NOT NULL,
  pic VARCHAR(100) NOT NULL,
  available TINYINT(1) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples_config'
--

CREATE TABLE IF NOT EXISTS king_freesamples_config (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  MIN INT(10) UNSIGNED NOT NULL,
  `limit` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_freesamples_order'
--

CREATE TABLE IF NOT EXISTS king_freesamples_order (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(20) NOT NULL,
  fsid INT(10) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  invoice_no BIGINT(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_hoteldeals'
--

CREATE TABLE IF NOT EXISTS king_hoteldeals (
  dealid BIGINT(20) UNSIGNED NOT NULL,
  address TEXT NOT NULL,
  latlong VARCHAR(25) NOT NULL,
  phone VARCHAR(200) NOT NULL,
  email VARCHAR(150) NOT NULL,
  city VARCHAR(30) NOT NULL,
  heading VARCHAR(150) NOT NULL,
  tagline VARCHAR(200) NOT NULL,
  amenities CHAR(19) NOT NULL,
  PRIMARY KEY (dealid)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_interested_products'
--

CREATE TABLE IF NOT EXISTS king_interested_products (
  id INT(11) NOT NULL AUTO_INCREMENT,
  product VARCHAR(150) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  contact VARCHAR(150) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_invoice'
--

CREATE TABLE IF NOT EXISTS king_invoice (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  transid CHAR(18) NOT NULL,
  order_id BIGINT(20) UNSIGNED NOT NULL,
  mrp INT(10) UNSIGNED NOT NULL,
  discount DECIMAL(10,2) UNSIGNED NOT NULL,
  invoice_qty INT(5) DEFAULT '0',
  nlc DECIMAL(10,2) UNSIGNED NOT NULL,
  credit_note_id BIGINT(11) DEFAULT '0',
  credit_note_amt DOUBLE DEFAULT '0',
  phc DECIMAL(10,2) UNSIGNED NOT NULL,
  tax DOUBLE UNSIGNED NOT NULL,
  service_tax DOUBLE NOT NULL,
  cod DOUBLE UNSIGNED NOT NULL,
  ship DOUBLE UNSIGNED NOT NULL,
  giftwrap_charge DOUBLE DEFAULT '0',
  invoice_status TINYINT(1) DEFAULT '0',
  is_returned TINYINT(1) DEFAULT '0',
  createdon BIGINT(20) DEFAULT NULL,
  cancelled_on BIGINT(20) DEFAULT NULL,
  delivery_medium VARCHAR(255) DEFAULT '0',
  tracking_id VARCHAR(50) DEFAULT '0',
  shipdatetime DATETIME DEFAULT NULL,
  notify_customer TINYINT(1) DEFAULT '0',
  is_delivered TINYINT(1) DEFAULT '0',
  is_partial_invoice TINYINT(1) DEFAULT '0',
  is_printed TINYINT(1) DEFAULT '0',
  total_prints INT(5) DEFAULT '0',
  last_printedon DATETIME DEFAULT NULL,
  outscanned_on BIGINT(20) DEFAULT NULL,
  is_outscanned TINYINT(1) DEFAULT '0',
  is_b2b TINYINT(1) NOT NULL,
  old_pnh_inv_no BIGINT(20) DEFAULT '0',
  new_pnh_inv_no BIGINT(20) DEFAULT '0',
  split_inv_grpno BIGINT(20) DEFAULT '0',
  ref_dispatch_id BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY order_id (order_id),
  KEY invoice_no (invoice_no),
  KEY ref_dispatch_id (ref_dispatch_id),
  KEY split_inv_grpno (split_inv_grpno)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_invoice_prints'
--

CREATE TABLE IF NOT EXISTS king_invoice_prints (
  id INT(11) NOT NULL AUTO_INCREMENT,
  invoice_no VARCHAR(50) DEFAULT NULL,
  printed_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_item_lovers'
--

CREATE TABLE IF NOT EXISTS king_item_lovers (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_lookingto'
--

CREATE TABLE IF NOT EXISTS king_lookingto (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  product TEXT NOT NULL,
  whenbuy VARCHAR(100) NOT NULL,
  uids TEXT NOT NULL,
  emails TEXT NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_mail_providers'
--

CREATE TABLE IF NOT EXISTS king_mail_providers (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_menu'
--

CREATE TABLE IF NOT EXISTS king_menu (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  url VARCHAR(150) NOT NULL,
  prepos VARCHAR(25) NOT NULL DEFAULT 'for',
  tagline VARCHAR(50) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1',
  priority SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY url (url)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_miscusers'
--

CREATE TABLE IF NOT EXISTS king_miscusers (
  userid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  logins INT(10) UNSIGNED NOT NULL,
  lastlogin DATETIME NOT NULL,
  invites INT(10) UNSIGNED NOT NULL,
  viewdeals VARCHAR(100) NOT NULL,
  PRIMARY KEY (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_m_buyprocess'
--

CREATE TABLE IF NOT EXISTS king_m_buyprocess (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  quantity_done INT(10) UNSIGNED NOT NULL,
  refund INT(10) UNSIGNED NOT NULL,
  refund_given INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  expires_on BIGINT(20) UNSIGNED NOT NULL,
  started_by BIGINT(20) UNSIGNED NOT NULL,
  started_on BIGINT(20) UNSIGNED NOT NULL,
  refund_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_newsletters'
--

CREATE TABLE IF NOT EXISTS king_newsletters (
  id INT(11) NOT NULL AUTO_INCREMENT,
  campaign_no VARCHAR(255) DEFAULT NULL,
  item_id VARCHAR(255) DEFAULT NULL,
  template_type VARCHAR(255) DEFAULT NULL,
  banner_image VARCHAR(255) DEFAULT 'site_banner.png',
  is_active INT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_orders'
--

CREATE TABLE IF NOT EXISTS king_orders (
  sno BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  transid CHAR(18) NOT NULL,
  userid INT(11) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  vendorid BIGINT(20) UNSIGNED NOT NULL,
  bill_person VARCHAR(100) NOT NULL,
  bill_address TEXT NOT NULL,
  bill_city TEXT NOT NULL,
  bill_pincode VARCHAR(20) NOT NULL,
  ship_person VARCHAR(100) NOT NULL,
  ship_address TEXT NOT NULL,
  ship_city TEXT NOT NULL,
  ship_pincode VARCHAR(20) NOT NULL,
  bill_phone VARCHAR(50) NOT NULL,
  ship_phone VARCHAR(50) NOT NULL,
  bill_state VARCHAR(100) NOT NULL,
  ship_state VARCHAR(100) NOT NULL,
  ship_email VARCHAR(150) NOT NULL,
  bill_email VARCHAR(150) NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  paid INT(10) UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL COMMENT '0 - PG (cc,netbanking), 1 - cod',
  `status` TINYINT(4) NOT NULL,
  admin_order_status TINYINT(3) DEFAULT '0',
  shipped TINYINT(1) NOT NULL,
  buyer_options TEXT NOT NULL,
  `time` BIGINT(20) NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  shiptime BIGINT(20) UNSIGNED NOT NULL,
  shipid VARCHAR(50) NOT NULL,
  `medium` VARCHAR(100) NOT NULL,
  bpid BIGINT(20) UNSIGNED NOT NULL,
  email VARCHAR(100) NOT NULL,
  ship_landmark TEXT NOT NULL,
  bill_landmark TEXT NOT NULL,
  ship_telephone VARCHAR(50) NOT NULL,
  ship_country VARCHAR(255) DEFAULT NULL,
  bill_country VARCHAR(255) DEFAULT NULL,
  bill_telephone VARCHAR(50) NOT NULL,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  priority TINYINT(1) NOT NULL,
  priority_note VARCHAR(200) NOT NULL,
  note TEXT NOT NULL,
  billon_orderprice TINYINT(1) DEFAULT '0',
  i_orgprice DOUBLE DEFAULT '0',
  i_price DOUBLE DEFAULT '0',
  i_nlc DOUBLE DEFAULT '0',
  i_phc DOUBLE DEFAULT '0',
  i_tax DOUBLE DEFAULT '0',
  i_discount DOUBLE DEFAULT '0',
  i_coup_discount DOUBLE DEFAULT '0',
  redeem_value FLOAT DEFAULT '1',
  i_discount_applied_on DOUBLE DEFAULT '0',
  giftwrap_order TINYINT(1) DEFAULT '0',
  is_giftcard TINYINT(1) DEFAULT '0',
  gc_recp_name VARCHAR(255) DEFAULT NULL,
  gc_recp_email VARCHAR(255) DEFAULT NULL,
  gc_recp_mobile VARCHAR(50) DEFAULT NULL,
  gc_recp_msg TEXT,
  order_status_backup TINYINT(1) DEFAULT NULL,
  has_super_scheme TINYINT(1) DEFAULT '0',
  super_scheme_logid INT(11) DEFAULT '0',
  super_scheme_target INT(11) DEFAULT '0',
  super_scheme_cashback DOUBLE DEFAULT '0',
  super_scheme_processed TINYINT(1) DEFAULT '0',
  imei_reimbursement_value_perunit DOUBLE(10,2) DEFAULT '0.00',
  imei_scheme_id BIGINT(20) DEFAULT '0',
  member_scheme_processed TINYINT(11) DEFAULT '0',
  member_id BIGINT(11) DEFAULT '0',
  is_ordqty_splitd TINYINT(1) DEFAULT '0',
  has_offer TINYINT(1) DEFAULT '0',
  offer_refid BIGINT(11) DEFAULT '0',
  partner_order_id VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (sno),
  KEY transid (transid),
  KEY itemid (itemid),
  KEY userid (userid),
  KEY id (id),
  KEY imei_scheme_id (imei_scheme_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_orders_bak'
--

CREATE TABLE IF NOT EXISTS king_orders_bak (
  sno BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  transid CHAR(18) NOT NULL,
  userid INT(11) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  vendorid BIGINT(20) UNSIGNED NOT NULL,
  bill_person VARCHAR(100) NOT NULL,
  bill_address TEXT NOT NULL,
  bill_city TEXT NOT NULL,
  bill_pincode VARCHAR(20) NOT NULL,
  ship_person VARCHAR(100) NOT NULL,
  ship_address TEXT NOT NULL,
  ship_city TEXT NOT NULL,
  ship_pincode VARCHAR(20) NOT NULL,
  bill_phone VARCHAR(20) NOT NULL,
  ship_phone VARCHAR(20) NOT NULL,
  bill_state VARCHAR(50) NOT NULL,
  ship_state VARCHAR(50) NOT NULL,
  ship_email VARCHAR(150) NOT NULL,
  bill_email VARCHAR(150) NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  paid INT(10) UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL COMMENT '0 - PG (cc,netbanking), 1 - cod',
  `status` TINYINT(4) NOT NULL,
  shipped TINYINT(1) NOT NULL,
  buyer_options TEXT NOT NULL,
  `time` BIGINT(20) NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  shiptime BIGINT(20) UNSIGNED NOT NULL,
  shipid VARCHAR(50) NOT NULL,
  `medium` VARCHAR(100) NOT NULL,
  bpid BIGINT(20) UNSIGNED NOT NULL,
  email VARCHAR(100) NOT NULL,
  ship_landmark TEXT NOT NULL,
  bill_landmark TEXT NOT NULL,
  ship_telephone VARCHAR(30) NOT NULL,
  bill_telephone VARCHAR(30) NOT NULL,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  priority TINYINT(1) NOT NULL,
  priority_note VARCHAR(200) NOT NULL,
  note TEXT NOT NULL,
  i_orgprice DOUBLE DEFAULT '0',
  i_price DOUBLE DEFAULT '0',
  i_nlc DOUBLE DEFAULT '0',
  i_phc DOUBLE DEFAULT '0',
  i_tax DOUBLE DEFAULT '0',
  i_discount DOUBLE DEFAULT '0',
  i_coup_discount DOUBLE DEFAULT '0',
  i_discount_applied_on DOUBLE DEFAULT '0',
  PRIMARY KEY (sno),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_order_statuslog'
--

CREATE TABLE IF NOT EXISTS king_order_statuslog (
  id INT(11) NOT NULL AUTO_INCREMENT,
  reference_trans_id VARCHAR(30) DEFAULT NULL,
  transid VARCHAR(30) DEFAULT NULL,
  order_id VARCHAR(30) DEFAULT NULL,
  invoice_no VARCHAR(30) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  logged_on BIGINT(20) DEFAULT NULL,
  message TEXT,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_password_forgot'
--

CREATE TABLE IF NOT EXISTS king_password_forgot (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash` CHAR(32) NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_pending_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_pending_cashbacks (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` CHAR(12) NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  `value` INT(10) UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  MIN INT(10) UNSIGNED NOT NULL,
  expires BIGINT(20) UNSIGNED NOT NULL,
  transid VARCHAR(60) NOT NULL,
  orderid BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY `code` (`code`)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points'
--

CREATE TABLE IF NOT EXISTS king_points (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  transid VARCHAR(20) NOT NULL,
  points INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points_sys'
--

CREATE TABLE IF NOT EXISTS king_points_sys (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  amount INT(10) UNSIGNED NOT NULL,
  points INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_points_track'
--

CREATE TABLE IF NOT EXISTS king_points_track (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  coupon CHAR(15) NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_pricereqs'
--

CREATE TABLE IF NOT EXISTS king_pricereqs (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  price INT(10) UNSIGNED NOT NULL,
  aprice INT(10) UNSIGNED NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  `time` INT(10) UNSIGNED NOT NULL,
  url CHAR(32) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_product_cashbacks'
--

CREATE TABLE IF NOT EXISTS king_product_cashbacks (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `value` INT(10) UNSIGNED NOT NULL,
  valid INT(10) UNSIGNED NOT NULL,
  min_order INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_profiles'
--

CREATE TABLE IF NOT EXISTS king_profiles (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  corpid BIGINT(20) UNSIGNED NOT NULL,
  pic CHAR(12) NOT NULL,
  designation VARCHAR(100) NOT NULL DEFAULT 'not available',
  department VARCHAR(100) NOT NULL,
  location VARCHAR(150) NOT NULL,
  employee_no VARCHAR(50) NOT NULL,
  desk_no VARCHAR(50) NOT NULL,
  linkedin VARCHAR(150) NOT NULL,
  facebook VARCHAR(150) NOT NULL,
  twitter VARCHAR(150) NOT NULL,
  products INT(10) UNSIGNED NOT NULL,
  reviews INT(10) UNSIGNED NOT NULL,
  lastbuy BIGINT(20) UNSIGNED NOT NULL,
  lastbuy_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_referral_coupon_track'
--

CREATE TABLE IF NOT EXISTS king_referral_coupon_track (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  referral BIGINT(20) UNSIGNED NOT NULL,
  coupon CHAR(13) NOT NULL,
  transid VARCHAR(20) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  ncoupon CHAR(14) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_refunds'
--

CREATE TABLE IF NOT EXISTS king_refunds (
  id INT(11) NOT NULL AUTO_INCREMENT,
  transid VARCHAR(30) DEFAULT NULL,
  order_ids VARCHAR(50) DEFAULT NULL,
  notify_customer TINYINT(1) DEFAULT '0',
  notification_sent TEXT,
  amount DOUBLE DEFAULT NULL,
  tracking_id VARCHAR(50) DEFAULT NULL,
  `datetime` DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_remindme'
--

CREATE TABLE IF NOT EXISTS king_remindme (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(100) NOT NULL,
  mobile VARCHAR(12) NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_resources'
--

CREATE TABLE IF NOT EXISTS king_resources (
  dealid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `type` TINYINT(1) NOT NULL,
  id VARCHAR(200) NOT NULL
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_reviews'
--

CREATE TABLE IF NOT EXISTS king_reviews (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  title VARCHAR(100) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  rating TINYINT(3) UNSIGNED NOT NULL,
  review TEXT NOT NULL,
  `first` TINYINT(1) NOT NULL,
  buyer TINYINT(1) NOT NULL,
  thumbs_up INT(10) UNSIGNED NOT NULL,
  thumbs_down INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_review_thumbs'
--

CREATE TABLE IF NOT EXISTS king_review_thumbs (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  rid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  yes TINYINT(1) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  points INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_roomdeals'
--

CREATE TABLE IF NOT EXISTS king_roomdeals (
  roomid BIGINT(20) UNSIGNED NOT NULL,
  dealid BIGINT(20) UNSIGNED NOT NULL,
  heading VARCHAR(200) NOT NULL,
  tagline VARCHAR(200) NOT NULL,
  availability TEXT NOT NULL,
  PRIMARY KEY (roomid)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_savedcartitems'
--

CREATE TABLE IF NOT EXISTS king_savedcartitems (
  cartid BIGINT(20) UNSIGNED NOT NULL,
  itemid BIGINT(20) NOT NULL,
  quantity INT(11) NOT NULL,
  KEY cartid (cartid)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_savedcarts'
--

CREATE TABLE IF NOT EXISTS king_savedcarts (
  cartid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (cartid)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_search_index'
--

CREATE TABLE IF NOT EXISTS king_search_index (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  keywords VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  FULLTEXT KEY keywords (`name`,keywords)
) TYPE=MYISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'king_search_log'
--

CREATE TABLE IF NOT EXISTS king_search_log (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `query` VARCHAR(150) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_shipment_update_filedata'
--

CREATE TABLE IF NOT EXISTS king_shipment_update_filedata (
  id INT(11) NOT NULL AUTO_INCREMENT,
  uniq_id BIGINT(20) DEFAULT NULL,
  file_name VARCHAR(255) DEFAULT NULL,
  invoice_no VARCHAR(50) DEFAULT NULL,
  awb_no VARCHAR(50) DEFAULT NULL,
  courier_name VARCHAR(255) DEFAULT NULL,
  ship_date DATE DEFAULT NULL,
  notify_customer TINYINT(1) DEFAULT '0',
  `status` TINYINT(1) DEFAULT '0',
  logged_on DATETIME DEFAULT NULL,
  processed_on DATETIME DEFAULT NULL,
  message VARCHAR(2024) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_specialusers'
--

CREATE TABLE IF NOT EXISTS king_specialusers (
  userid BIGINT(20) UNSIGNED NOT NULL,
  suid VARCHAR(100) NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'king_stock'
--

CREATE TABLE IF NOT EXISTS king_stock (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  available INT(10) UNSIGNED NOT NULL,
  ins INT(10) UNSIGNED NOT NULL,
  outs INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_stock_activity'
--

CREATE TABLE IF NOT EXISTS king_stock_activity (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  stockids TEXT NOT NULL,
  `type` TINYINT(1) NOT NULL,
  remarks TEXT NOT NULL,
  reference_no VARCHAR(100) NOT NULL,
  purchase_date VARCHAR(20) NOT NULL,
  vendor VARCHAR(100) NOT NULL,
  amount INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_subscr_email'
--

CREATE TABLE IF NOT EXISTS king_subscr_email (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  KEY email (email)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_subscr_mobile'
--

CREATE TABLE IF NOT EXISTS king_subscr_mobile (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  mobile CHAR(12) NOT NULL,
  PRIMARY KEY (id),
  KEY mobile (mobile)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_sub_invoice'
--

CREATE TABLE IF NOT EXISTS king_sub_invoice (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  sub INT(10) UNSIGNED NOT NULL,
  orders TEXT NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_supplier_contacts'
--

CREATE TABLE IF NOT EXISTS king_supplier_contacts (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  business VARCHAR(150) NOT NULL,
  contact_number VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  location VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tags'
--

CREATE TABLE IF NOT EXISTS king_tags (
  tid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  src_url VARCHAR(200) NOT NULL,
  pic CHAR(20) NOT NULL,
  retags INT(10) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (tid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tags_in_boards'
--

CREATE TABLE IF NOT EXISTS king_tags_in_boards (
  tbid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  bid INT(10) UNSIGNED NOT NULL,
  tid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  url VARCHAR(60) NOT NULL,
  `from` BIGINT(20) UNSIGNED NOT NULL,
  comments INT(10) UNSIGNED NOT NULL,
  loves INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (tbid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tag_comments'
--

CREATE TABLE IF NOT EXISTS king_tag_comments (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  tbid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `comment` VARCHAR(200) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tag_lovers'
--

CREATE TABLE IF NOT EXISTS king_tag_lovers (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  tbid BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_tmp_orders'
--

CREATE TABLE IF NOT EXISTS king_tmp_orders (
  sno BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  transid CHAR(18) NOT NULL,
  userid INT(11) UNSIGNED NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  vendorid BIGINT(20) UNSIGNED NOT NULL,
  bill_person VARCHAR(100) NOT NULL,
  bill_address TEXT NOT NULL,
  bill_city TEXT NOT NULL,
  bill_pincode VARCHAR(20) NOT NULL,
  ship_person VARCHAR(100) NOT NULL,
  ship_address TEXT NOT NULL,
  ship_city TEXT NOT NULL,
  ship_pincode VARCHAR(20) NOT NULL,
  bill_phone VARCHAR(20) NOT NULL,
  ship_phone VARCHAR(20) NOT NULL,
  bill_state VARCHAR(100) NOT NULL,
  ship_state VARCHAR(100) NOT NULL,
  ship_email VARCHAR(150) NOT NULL,
  bill_email VARCHAR(150) NOT NULL,
  quantity INT(10) UNSIGNED NOT NULL,
  amount INT(10) UNSIGNED NOT NULL,
  bpid BIGINT(20) UNSIGNED NOT NULL,
  `status` TINYINT(4) NOT NULL,
  buyer_options TEXT NOT NULL,
  `time` BIGINT(20) NOT NULL,
  actiontime BIGINT(20) UNSIGNED NOT NULL,
  shiptime BIGINT(20) UNSIGNED NOT NULL,
  shipid VARCHAR(50) NOT NULL,
  `medium` VARCHAR(100) NOT NULL,
  bill_landmark TEXT NOT NULL,
  ship_landmark TEXT NOT NULL,
  bill_telephone VARCHAR(30) NOT NULL,
  ship_telephone VARCHAR(30) NOT NULL,
  ship_country VARCHAR(255) DEFAULT NULL,
  bill_country VARCHAR(255) DEFAULT NULL,
  i_orgprice DOUBLE DEFAULT '0',
  i_price DOUBLE DEFAULT '0',
  i_nlc DOUBLE DEFAULT '0',
  i_phc DOUBLE DEFAULT '0',
  i_tax DOUBLE DEFAULT '0',
  i_discount DOUBLE DEFAULT '0',
  i_coup_discount DOUBLE DEFAULT '0',
  i_discount_applied_on DOUBLE DEFAULT '0',
  giftwrap_order TINYINT(1) DEFAULT '0',
  is_giftcard TINYINT(1) DEFAULT '0',
  gc_recp_name VARCHAR(255) DEFAULT NULL,
  gc_recp_email VARCHAR(255) DEFAULT NULL,
  gc_recp_mobile VARCHAR(50) DEFAULT NULL,
  gc_recp_msg TEXT,
  user_note TEXT,
  partner_order_id VARCHAR(30) DEFAULT NULL,
  partner_reference_no VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (sno),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transactions'
--

CREATE TABLE IF NOT EXISTS king_transactions (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid CHAR(18) NOT NULL,
  orderid BIGINT(20) UNSIGNED NOT NULL,
  amount DOUBLE UNSIGNED NOT NULL,
  paid DOUBLE UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL,
  voucher_payment TINYINT(3) DEFAULT '0',
  cod DOUBLE UNSIGNED NOT NULL,
  ship DOUBLE UNSIGNED NOT NULL,
  giftwrap_charge DOUBLE DEFAULT '0',
  response_code INT(10) UNSIGNED NOT NULL,
  msg TEXT NOT NULL,
  payment_id VARCHAR(50) NOT NULL,
  pg_transaction_id VARCHAR(50) NOT NULL,
  is_flagged VARCHAR(10) NOT NULL,
  init BIGINT(20) UNSIGNED NOT NULL,
  actiontime INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  is_pnh TINYINT(1) NOT NULL,
  franchise_id INT(10) UNSIGNED NOT NULL,
  batch_enabled TINYINT(1) NOT NULL DEFAULT '1',
  admin_trans_status TINYINT(3) DEFAULT '0',
  priority TINYINT(1) NOT NULL,
  priority_note VARCHAR(200) NOT NULL,
  note TEXT NOT NULL,
  offline TINYINT(1) NOT NULL,
  status_backup TINYINT(1) DEFAULT NULL,
  partner_reference_no VARCHAR(30) NOT NULL,
  partner_id INT(10) UNSIGNED NOT NULL,
  trans_created_by INT(11) DEFAULT '0',
  trans_grp_ref_no BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY franchise_id (franchise_id),
  KEY trans_created_by (trans_created_by)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transaction_activity'
--

CREATE TABLE IF NOT EXISTS king_transaction_activity (
  id INT(11) NOT NULL AUTO_INCREMENT,
  reference_trans_id VARCHAR(30) DEFAULT NULL,
  message TEXT,
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_transaction_notes'
--

CREATE TABLE IF NOT EXISTS king_transaction_notes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  transid VARCHAR(30) DEFAULT NULL,
  order_id VARCHAR(30) DEFAULT NULL,
  note TEXT,
  `status` TINYINT(1) DEFAULT '0',
  note_priority TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_trends'
--

CREATE TABLE IF NOT EXISTS king_trends (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` CHAR(20) NOT NULL,
  hits INT(10) UNSIGNED NOT NULL,
  deals TEXT NOT NULL,
  listed_on BIGINT(20) UNSIGNED NOT NULL,
  updated_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY `name` (`name`)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_used_coupons'
--

CREATE TABLE IF NOT EXISTS king_used_coupons (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  coupon CHAR(12) NOT NULL,
  transid CHAR(20) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  KEY coupon (coupon),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_userlog'
--

CREATE TABLE IF NOT EXISTS king_userlog (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) UNSIGNED NOT NULL,
  ip VARCHAR(50) NOT NULL,
  last_login BIGINT(20) UNSIGNED NOT NULL,
  ip_time_data TEXT NOT NULL,
  useragent VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  KEY userid (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_users'
--

CREATE TABLE IF NOT EXISTS king_users (
  userid BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  `password` CHAR(32) NOT NULL,
  mobile BIGINT(11) UNSIGNED NOT NULL,
  corpemail VARCHAR(100) NOT NULL,
  corpid INT(10) UNSIGNED NOT NULL,
  balance INT(10) UNSIGNED NOT NULL,
  inviteid CHAR(10) NOT NULL,
  friendof BIGINT(20) UNSIGNED NOT NULL,
  special TINYINT(1) NOT NULL,
  special_id VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  landmark TEXT NOT NULL,
  telephone VARCHAR(30) NOT NULL,
  country VARCHAR(255) DEFAULT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  pincode VARCHAR(100) NOT NULL,
  `block` TINYINT(1) NOT NULL,
  verified INT(10) UNSIGNED NOT NULL,
  verify_code CHAR(10) NOT NULL,
  optin TINYINT(1) NOT NULL DEFAULT '1',
  points INT(10) UNSIGNED NOT NULL,
  temperament TINYINT(1) UNSIGNED NOT NULL DEFAULT '2',
  is_pnh TINYINT(1) NOT NULL,
  createdon BIGINT(20) UNSIGNED NOT NULL DEFAULT '1275750318',
  PRIMARY KEY (userid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_vars'
--

CREATE TABLE IF NOT EXISTS king_vars (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` TEXT NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_vendors'
--

CREATE TABLE IF NOT EXISTS king_vendors (
  sno INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  id BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  created_date BIGINT(20) UNSIGNED NOT NULL,
  address TEXT NOT NULL,
  email VARCHAR(200) NOT NULL,
  telephone VARCHAR(200) NOT NULL,
  mobile VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  contact VARCHAR(200) NOT NULL,
  PRIMARY KEY (sno,id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'king_widgets'
--

CREATE TABLE IF NOT EXISTS king_widgets (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  salt CHAR(32) NOT NULL,
  `type` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'menu_class_config'
--

CREATE TABLE IF NOT EXISTS menu_class_config (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  menu_id BIGINT(20) DEFAULT NULL,
  class_id INT(11) DEFAULT NULL,
  percentage DOUBLE DEFAULT NULL,
  is_active TINYINT(4) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_brand_config_map_price'
--

CREATE TABLE IF NOT EXISTS m_brand_config_map_price (
  id INT(11) NOT NULL AUTO_INCREMENT,
  menuid INT(11) DEFAULT '0',
  brandid BIGINT(11) DEFAULT '0',
  catid BIGINT(11) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM  COMMENT='Table to configure minimum applicable price to be considered';

-- --------------------------------------------------------

--
-- Table structure for table 'm_brand_location_link'
--

CREATE TABLE IF NOT EXISTS m_brand_location_link (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  brand_id BIGINT(11) DEFAULT NULL,
  default_location_id INT(11) DEFAULT NULL,
  default_rack_bin_id INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brand_id (brand_id),
  KEY default_location_id (default_location_id),
  KEY default_rack_bin_id (default_rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_client_contacts_info'
--

CREATE TABLE IF NOT EXISTS m_client_contacts_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  client_id INT(11) DEFAULT NULL,
  contact_name VARCHAR(200) DEFAULT NULL,
  contact_designation VARCHAR(200) DEFAULT NULL,
  mobile_no_1 VARCHAR(10) DEFAULT NULL,
  mobile_no_2 VARCHAR(10) DEFAULT NULL,
  telephone_no VARCHAR(150) DEFAULT NULL,
  email_id_1 VARCHAR(200) DEFAULT NULL,
  email_id_2 VARCHAR(200) DEFAULT NULL,
  fax_no VARCHAR(120) DEFAULT NULL,
  active_status INT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY client_id (client_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_client_info'
--

CREATE TABLE IF NOT EXISTS m_client_info (
  client_id INT(11) NOT NULL AUTO_INCREMENT,
  client_code VARCHAR(30) DEFAULT NULL,
  client_name VARCHAR(255) DEFAULT NULL,
  address_line1 VARCHAR(255) DEFAULT NULL,
  address_line2 VARCHAR(255) DEFAULT NULL,
  locality VARCHAR(200) DEFAULT NULL,
  landmark VARCHAR(200) DEFAULT NULL,
  postcode VARCHAR(10) DEFAULT NULL,
  city_name VARCHAR(150) DEFAULT NULL,
  state_name VARCHAR(150) DEFAULT NULL,
  country VARCHAR(150) DEFAULT NULL,
  credit_limit_amount DOUBLE DEFAULT '0',
  credit_days INT(11) DEFAULT '0',
  cst_no VARCHAR(100) DEFAULT NULL,
  pan_no VARCHAR(100) DEFAULT NULL,
  vat_no VARCHAR(100) DEFAULT NULL,
  service_tax_no VARCHAR(100) DEFAULT NULL,
  remarks VARCHAR(2000) DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  active_status INT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (client_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_config_params'
--

CREATE TABLE IF NOT EXISTS m_config_params (
  `name` VARCHAR(255) DEFAULT NULL,
  `value` VARCHAR(255) DEFAULT NULL
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_config_statusflags'
--

CREATE TABLE IF NOT EXISTS m_config_statusflags (
  id INT(11) NOT NULL AUTO_INCREMENT,
  flag_for VARCHAR(255) DEFAULT NULL,
  flag_no BIGINT(11) DEFAULT '0',
  flag_value VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_awb_series'
--

CREATE TABLE IF NOT EXISTS m_courier_awb_series (
  id INT(11) NOT NULL AUTO_INCREMENT,
  courier_id INT(11) DEFAULT NULL,
  awb_no_prefix VARCHAR(10) DEFAULT NULL,
  awb_no_suffix VARCHAR(10) DEFAULT NULL,
  awb_start_no DOUBLE DEFAULT '0',
  awb_end_no DOUBLE DEFAULT '0',
  awb_current_no DOUBLE DEFAULT '0',
  mode_surface TINYINT(1) DEFAULT '0',
  mode_air_cargo TINYINT(1) DEFAULT '0',
  mode_air_courier TINYINT(1) DEFAULT '0',
  mode_air_rail TINYINT(1) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY courier_id (courier_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_flags'
--

CREATE TABLE IF NOT EXISTS m_courier_flags (
  id INT(11) NOT NULL AUTO_INCREMENT,
  courier_id INT(11) DEFAULT '0',
  statuscode VARCHAR(20) DEFAULT NULL,
  sys_statusflag INT(2) DEFAULT '0',
  status_text VARCHAR(1024) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY statuscode (statuscode),
  KEY sys_statusflag (sys_statusflag)
) TYPE=MYISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_info'
--

CREATE TABLE IF NOT EXISTS m_courier_info (
  courier_id INT(11) NOT NULL AUTO_INCREMENT,
  courier_name VARCHAR(255) DEFAULT NULL,
  address_line1 VARCHAR(255) DEFAULT NULL,
  address_line2 VARCHAR(255) DEFAULT NULL,
  locality VARCHAR(200) DEFAULT NULL,
  landmark VARCHAR(200) DEFAULT NULL,
  postcode VARCHAR(10) DEFAULT NULL,
  city_id INT(11) DEFAULT '0',
  city_name VARCHAR(150) DEFAULT NULL,
  state_name VARCHAR(150) DEFAULT NULL,
  country VARCHAR(150) DEFAULT NULL,
  ledger_id INT(11) DEFAULT '0',
  credit_limit_amount DOUBLE DEFAULT '0',
  credit_days INT(11) DEFAULT '0',
  credit_cycle INT(11) DEFAULT '0',
  require_payment_advance TINYINT(1) DEFAULT '0',
  cod_available TINYINT(1) DEFAULT '0',
  mode_air_courier TINYINT(1) DEFAULT '0',
  mode_air_cargo TINYINT(1) DEFAULT '0',
  mode_surface TINYINT(1) DEFAULT '0',
  mode_rail TINYINT(1) DEFAULT '0',
  tin_no VARCHAR(100) DEFAULT NULL,
  cst_no VARCHAR(100) DEFAULT NULL,
  pan_no VARCHAR(100) DEFAULT NULL,
  vat_no VARCHAR(100) DEFAULT NULL,
  service_tax_no VARCHAR(100) DEFAULT NULL,
  shipment_update_template_id INT(11) DEFAULT '0',
  pincode_list_template_id INT(11) DEFAULT '0',
  payment_terms_msg VARCHAR(255) DEFAULT NULL,
  agreement_copy VARCHAR(200) DEFAULT NULL,
  remarks VARCHAR(2000) DEFAULT NULL,
  ref_partner_id INT(11) DEFAULT '0',
  is_active INT(1) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (courier_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_courier_pincodes'
--

CREATE TABLE IF NOT EXISTS m_courier_pincodes (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  courier_id INT(10) UNSIGNED NOT NULL,
  pincode INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  added_on DATETIME NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_deals_bulk_update'
--

CREATE TABLE IF NOT EXISTS m_deals_bulk_update (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  items INT(10) UNSIGNED NOT NULL,
  updated_data TEXT,
  created_on BIGINT(20) DEFAULT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_info'
--

CREATE TABLE IF NOT EXISTS m_employee_info (
  employee_id INT(11) NOT NULL AUTO_INCREMENT,
  assigned_under INT(11) DEFAULT '0',
  user_id INT(11) DEFAULT '0',
  `name` VARCHAR(255) DEFAULT NULL,
  fathername VARCHAR(255) DEFAULT NULL,
  mothername VARCHAR(255) DEFAULT NULL,
  dob DATE DEFAULT NULL,
  qualification VARCHAR(255) DEFAULT NULL,
  email VARCHAR(255) DEFAULT NULL,
  gender VARCHAR(255) DEFAULT NULL,
  address TEXT,
  city VARCHAR(255) DEFAULT NULL,
  postcode INT(7) DEFAULT NULL,
  contact_no VARCHAR(255) DEFAULT NULL,
  job_title INT(11) DEFAULT NULL,
  job_title2 INT(11) DEFAULT '0',
  photo_url VARCHAR(255) DEFAULT NULL,
  cv_url VARCHAR(255) DEFAULT NULL,
  send_sms INT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by VARCHAR(255) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by VARCHAR(255) DEFAULT NULL,
  is_suspended TINYINT(11) NOT NULL DEFAULT '0',
  suspended_on DATETIME DEFAULT NULL,
  suspended_by BIGINT(255) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (employee_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_list'
--

CREATE TABLE IF NOT EXISTS m_employee_list (
  id INT(11) NOT NULL AUTO_INCREMENT,
  role VARCHAR(255) DEFAULT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  mobile VARCHAR(50) DEFAULT NULL,
  email VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_rolelink'
--

CREATE TABLE IF NOT EXISTS m_employee_rolelink (
  id INT(11) NOT NULL AUTO_INCREMENT,
  employee_id INT(11) DEFAULT NULL,
  parent_emp_id INT(11) DEFAULT NULL,
  is_active INT(11) DEFAULT NULL,
  assigned_on DATE DEFAULT NULL,
  modified_on DATE DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_employee_roles'
--

CREATE TABLE IF NOT EXISTS m_employee_roles (
  role_id INT(11) NOT NULL AUTO_INCREMENT,
  role_name VARCHAR(255) NOT NULL,
  short_frm VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  PRIMARY KEY (role_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_manifesto_driver_log'
--

CREATE TABLE IF NOT EXISTS m_manifesto_driver_log (
  id INT(100) NOT NULL AUTO_INCREMENT,
  manifesto_id INT(100) DEFAULT NULL,
  sent_invoices TEXT,
  remark TEXT,
  role_type VARCHAR(255) DEFAULT NULL,
  other_driver VARCHAR(255) DEFAULT NULL,
  contact_num VARCHAR(255) DEFAULT NULL,
  is_printed INT(100) DEFAULT '0',
  driver_id INT(100) DEFAULT '0',
  sent_on DATETIME DEFAULT NULL,
  handle_by VARCHAR(275) DEFAULT NULL,
  created_by INT(100) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(100) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_partner_deal_price'
--

CREATE TABLE IF NOT EXISTS m_partner_deal_price (
  id INT(11) NOT NULL AUTO_INCREMENT,
  partner_id INT(11) DEFAULT NULL,
  itemid DOUBLE DEFAULT NULL,
  offer_price DOUBLE DEFAULT NULL,
  partner_price DOUBLE DEFAULT NULL,
  modified_on BIGINT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(11) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_deal_link'
--

CREATE TABLE IF NOT EXISTS m_product_deal_link (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED DEFAULT NULL,
  product_id INT(11) UNSIGNED DEFAULT NULL,
  product_mrp DECIMAL(15,4) DEFAULT '0.0000',
  qty INT(11) DEFAULT '1',
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  tmp_pnh_itemid BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_groups'
--

CREATE TABLE IF NOT EXISTS m_product_groups (
  id INT(11) NOT NULL AUTO_INCREMENT,
  group_type VARCHAR(255) DEFAULT 'alternate',
  group_no INT(11) DEFAULT '0',
  product_id INT(11) DEFAULT '0',
  product_value DECIMAL(15,4) DEFAULT '0.0000',
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_group_deal_link'
--

CREATE TABLE IF NOT EXISTS m_product_group_deal_link (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(11) UNSIGNED DEFAULT NULL,
  group_id INT(11) UNSIGNED DEFAULT NULL,
  product_mrp DECIMAL(15,4) DEFAULT '0.0000',
  qty INT(11) DEFAULT '1',
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY group_id (group_id),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_info'
--

CREATE TABLE IF NOT EXISTS m_product_info (
  product_id INT(11) NOT NULL AUTO_INCREMENT,
  product_code VARCHAR(15) DEFAULT NULL,
  pid INT(10) UNSIGNED NOT NULL,
  product_name VARCHAR(200) DEFAULT NULL,
  short_desc VARCHAR(255) DEFAULT NULL,
  size DECIMAL(15,4) UNSIGNED DEFAULT '0.0000',
  uom VARCHAR(10) DEFAULT NULL COMMENT 'ml, grams, unit',
  weight DOUBLE DEFAULT '0' COMMENT 'in grams',
  mrp DECIMAL(15,4) UNSIGNED DEFAULT '0.0000',
  vat DECIMAL(7,4) UNSIGNED DEFAULT '0.0000',
  purchase_cost DECIMAL(15,4) UNSIGNED DEFAULT '0.0000' COMMENT 'including purchase tax',
  sku_code VARCHAR(100) DEFAULT NULL,
  barcode VARCHAR(50) DEFAULT NULL,
  is_offer TINYINT(1) DEFAULT '0',
  is_serial_required TINYINT(1) NOT NULL,
  brand_id BIGINT(11) DEFAULT '0',
  default_rackbin_id INT(11) DEFAULT '0',
  moq INT(11) DEFAULT '0',
  reorder_level INT(11) DEFAULT '0',
  reorder_qty INT(11) DEFAULT '0',
  is_sourceable TINYINT(1) DEFAULT '1',
  remarks VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  tmp_itemid DOUBLE DEFAULT '0',
  tmp_dealid DOUBLE DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  corr_status TINYINT(1) DEFAULT '0',
  corr_updated_on DATETIME DEFAULT NULL,
  PRIMARY KEY (product_id),
  KEY pid (pid),
  KEY brand_id (brand_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_product_update_log'
--

CREATE TABLE IF NOT EXISTS m_product_update_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT '0',
  `type` VARCHAR(255) DEFAULT NULL,
  message TEXT,
  logged_by INT(11) DEFAULT '0',
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_rack_bin_brand_link'
--

CREATE TABLE IF NOT EXISTS m_rack_bin_brand_link (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  rack_bin_id INT(10) UNSIGNED NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY rack_bin_id (rack_bin_id),
  KEY brandid (brandid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_rack_bin_info'
--

CREATE TABLE IF NOT EXISTS m_rack_bin_info (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  location_id INT(11) DEFAULT NULL,
  rack_name VARCHAR(100) DEFAULT NULL,
  bin_name VARCHAR(100) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY location_id (location_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_storage_location_info'
--

CREATE TABLE IF NOT EXISTS m_storage_location_info (
  location_id INT(11) NOT NULL AUTO_INCREMENT,
  location_name VARCHAR(255) DEFAULT NULL,
  is_damaged TINYINT(1) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (location_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_streams'
--

CREATE TABLE IF NOT EXISTS m_streams (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  file_url VARCHAR(255) NOT NULL,
  created_by INT(11) NOT NULL DEFAULT '0',
  created_time BIGINT(20) NOT NULL,
  modified_by VARCHAR(100) DEFAULT '0',
  modified_time BIGINT(20) NOT NULL DEFAULT '0',
  `status` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_posts'
--

CREATE TABLE IF NOT EXISTS m_stream_posts (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  title VARCHAR(32) DEFAULT NULL,
  description TEXT,
  stream_id BIGINT(20) NOT NULL,
  `status` TINYINT(1) DEFAULT '0',
  posted_by INT(11) DEFAULT '0',
  posted_on VARCHAR(100) DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on TIMESTAMP NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_post_assigned_users'
--

CREATE TABLE IF NOT EXISTS m_stream_post_assigned_users (
  id INT(25) NOT NULL AUTO_INCREMENT,
  userid BIGINT(20) DEFAULT '0',
  post_id BIGINT(20) DEFAULT '0',
  streamid BIGINT(20) DEFAULT '0',
  assigned_userid INT(11) DEFAULT '0',
  assigned_on VARCHAR(100) DEFAULT NULL,
  viewed TINYINT(1) DEFAULT '0',
  active TINYINT(1) DEFAULT '1',
  mail_sent TINYINT(1) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_post_reply'
--

CREATE TABLE IF NOT EXISTS m_stream_post_reply (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  description TEXT,
  post_id INT(25) DEFAULT '0',
  replied_by INT(25) DEFAULT '0',
  replied_on VARCHAR(100) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_stream_users'
--

CREATE TABLE IF NOT EXISTS m_stream_users (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  stream_id INT(11) DEFAULT '0',
  user_id INT(25) DEFAULT '0',
  access INT(1) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT '0',
  created_on VARCHAR(100) DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_town_territory_link'
--

CREATE TABLE IF NOT EXISTS m_town_territory_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  parent_emp_id INT(11) DEFAULT NULL,
  employee_id INT(11) DEFAULT NULL,
  territory_id INT(11) DEFAULT NULL,
  town_id INT(11) DEFAULT NULL,
  is_active INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_tray_info'
--

CREATE TABLE IF NOT EXISTS m_tray_info (
  tray_id INT(11) NOT NULL AUTO_INCREMENT,
  tray_name VARCHAR(255) NOT NULL,
  max_allowed INT(5) NOT NULL DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(100) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(100) DEFAULT NULL,
  PRIMARY KEY (tray_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_brand_link'
--

CREATE TABLE IF NOT EXISTS m_vendor_brand_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  brand_id BIGINT(11) DEFAULT NULL,
  cat_id BIGINT(11) DEFAULT '0',
  vendor_id INT(11) DEFAULT NULL,
  brand_margin DECIMAL(10,4) DEFAULT '10.0000',
  applicable_from BIGINT(11) UNSIGNED DEFAULT NULL,
  applicable_till BIGINT(11) UNSIGNED DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  is_default TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY brand_id (brand_id),
  KEY vendor_id (vendor_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_contacts_info'
--

CREATE TABLE IF NOT EXISTS m_vendor_contacts_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  vendor_id INT(11) DEFAULT NULL,
  contact_name VARCHAR(200) DEFAULT NULL,
  contact_designation VARCHAR(200) DEFAULT NULL,
  mobile_no_1 VARCHAR(10) DEFAULT NULL,
  mobile_no_2 VARCHAR(10) DEFAULT NULL,
  telephone_no VARCHAR(150) DEFAULT NULL,
  email_id_1 VARCHAR(200) DEFAULT NULL,
  email_id_2 VARCHAR(200) DEFAULT NULL,
  fax_no VARCHAR(120) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY vendor_id (vendor_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_info'
--

CREATE TABLE IF NOT EXISTS m_vendor_info (
  vendor_id INT(11) NOT NULL AUTO_INCREMENT,
  vendor_code VARCHAR(30) DEFAULT NULL,
  vendor_name VARCHAR(255) DEFAULT NULL,
  address_line1 VARCHAR(255) DEFAULT NULL,
  address_line2 VARCHAR(255) DEFAULT NULL,
  locality VARCHAR(200) DEFAULT NULL,
  landmark VARCHAR(200) DEFAULT NULL,
  postcode VARCHAR(10) DEFAULT NULL,
  city_name VARCHAR(150) DEFAULT NULL,
  state_name VARCHAR(150) DEFAULT NULL,
  country VARCHAR(150) DEFAULT NULL,
  ledger_id INT(11) DEFAULT '0',
  credit_limit_amount DOUBLE DEFAULT '0',
  credit_days INT(11) DEFAULT '0',
  require_payment_advance INT(11) DEFAULT '0' COMMENT 'store %age of adane for raising po',
  cst_no VARCHAR(100) DEFAULT NULL,
  pan_no VARCHAR(100) DEFAULT NULL,
  vat_no VARCHAR(100) DEFAULT NULL,
  service_tax_no VARCHAR(100) DEFAULT NULL,
  avg_tat INT(11) DEFAULT '1',
  return_policy_msg VARCHAR(255) DEFAULT NULL,
  payment_terms_msg VARCHAR(255) DEFAULT NULL,
  agreement_copy_file_name VARCHAR(150) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (vendor_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'm_vendor_product_link'
--

CREATE TABLE IF NOT EXISTS m_vendor_product_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  vendor_id INT(11) DEFAULT NULL,
  product_id INT(11) DEFAULT NULL,
  vendor_product_code VARCHAR(255) DEFAULT NULL,
  mrp DECIMAL(15,4) DEFAULT NULL,
  purchase_price DECIMAL(15,4) DEFAULT NULL,
  tax DECIMAL(7,4) DEFAULT NULL,
  valid_from DATE DEFAULT NULL,
  min_order_qty INT(11) DEFAULT '0',
  delivery_tat INT(11) DEFAULT '1',
  is_default TINYINT(1) DEFAULT '0',
  remarks VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY vendor_id (vendor_id),
  KEY product_id (product_id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_deal_prices'
--

CREATE TABLE IF NOT EXISTS partner_deal_prices (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  partner_id INT(10) UNSIGNED NOT NULL,
  customer_price INT(10) UNSIGNED NOT NULL,
  partner_price INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  modified_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_info'
--

CREATE TABLE IF NOT EXISTS partner_info (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  trans_prefix CHAR(3) NOT NULL,
  trans_mode TINYINT(3) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  modified_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_orders_log'
--

CREATE TABLE IF NOT EXISTS partner_orders_log (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  partner_id INT(10) UNSIGNED NOT NULL,
  amount INT(10) UNSIGNED NOT NULL,
  amount_paid INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  is_payment_made TINYINT(1) NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  modified_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY partner_id (partner_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_order_items'
--

CREATE TABLE IF NOT EXISTS partner_order_items (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  log_id INT(11) NOT NULL,
  transid VARCHAR(20) NOT NULL,
  i_customer_price DECIMAL(10,2) NOT NULL,
  i_partner_price DECIMAL(10,2) NOT NULL,
  qty INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'partner_transaction_details'
--

CREATE TABLE IF NOT EXISTS partner_transaction_details (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  partner_id BIGINT(11) DEFAULT '0',
  transid VARCHAR(30) DEFAULT NULL,
  order_no VARCHAR(255) DEFAULT NULL,
  order_date DATE DEFAULT NULL,
  net_amt DOUBLE DEFAULT NULL,
  awb_no VARCHAR(255) DEFAULT NULL,
  courier_name VARBINARY(255) DEFAULT NULL,
  ship_charges DOUBLE DEFAULT '0',
  is_manifesto_created TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY partner_id (partner_id),
  KEY transid (transid),
  KEY is_manifesto_created (is_manifesto_created),
  KEY order_no (order_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_app_versions'
--

CREATE TABLE IF NOT EXISTS pnh_app_versions (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  version_no INT(10) UNSIGNED NOT NULL,
  version_date BIGINT(20) UNSIGNED NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY version_no (version_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_bussiness_trip_info'
--

CREATE TABLE IF NOT EXISTS pnh_bussiness_trip_info (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  task_id INT(11) DEFAULT NULL,
  f_id INT(11) DEFAULT NULL,
  positive_msg VARCHAR(5000) DEFAULT NULL,
  negative_msg VARCHAR(5000) DEFAULT NULL,
  expenses INT(11) DEFAULT NULL,
  final_report VARCHAR(5000) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_call_log'
--

CREATE TABLE IF NOT EXISTS pnh_call_log (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  msg VARCHAR(250) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_cash_bill'
--

CREATE TABLE IF NOT EXISTS pnh_cash_bill (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  bill_no INT(10) UNSIGNED NOT NULL,
  transid VARCHAR(15) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id,transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_comp_details'
--

CREATE TABLE IF NOT EXISTS pnh_comp_details (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  pan_no VARCHAR(20) NOT NULL,
  vat_no VARCHAR(20) NOT NULL,
  roc_no VARCHAR(30) NOT NULL,
  toll_free_no VARCHAR(40) NOT NULL,
  sms_no VARCHAR(20) NOT NULL,
  bank_name VARCHAR(100) NOT NULL,
  bank_ac_no VARCHAR(40) NOT NULL,
  bank_ifsc_code VARCHAR(40) NOT NULL,
  bank_ac_name VARCHAR(60) NOT NULL,
  bank_branch_name VARCHAR(70) NOT NULL,
  bank_name2 VARCHAR(100) NOT NULL,
  bank_ac_no2 VARCHAR(40) NOT NULL,
  bank_ifsc_code2 VARCHAR(40) NOT NULL,
  bank_ac_name2 VARCHAR(60) NOT NULL,
  bank_branch_name2 VARCHAR(70) NOT NULL,
  bank_name3 VARCHAR(255) DEFAULT NULL,
  bank_ac_no3 VARCHAR(255) DEFAULT NULL,
  bank_ifsc_code3 VARCHAR(255) DEFAULT NULL,
  bank_ac_name3 VARCHAR(255) DEFAULT NULL,
  bank_branch_name3 VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub (
  id INT(11) NOT NULL AUTO_INCREMENT,
  hub_name VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub_fc_link'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub_fc_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  hub_id INT(11) DEFAULT '0',
  emp_id INT(11) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY hub_id (hub_id),
  KEY emp_id (emp_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_deliveryhub_town_link'
--

CREATE TABLE IF NOT EXISTS pnh_deliveryhub_town_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  hub_id INT(11) DEFAULT '0',
  town_id INT(11) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY town_id (town_id),
  KEY hub_id (hub_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_employee_grpsms_log'
--

CREATE TABLE IF NOT EXISTS pnh_employee_grpsms_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  emp_id INT(11) DEFAULT NULL,
  contact_no VARCHAR(25) DEFAULT NULL,
  `type` VARCHAR(255) DEFAULT NULL COMMENT '4:shipments_notification,6:lr_number_updates,8:pickup_manifesto,9:hand_over_to_executive,10:delivery,11:invoice return,12:shipments akwm for tm',
  territory_id INT(11) DEFAULT NULL,
  town_id INT(11) DEFAULT NULL,
  grp_msg TEXT,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_executive_accounts_log'
--

CREATE TABLE IF NOT EXISTS pnh_executive_accounts_log (
  id INT(11) NOT NULL AUTO_INCREMENT,
  emp_id INT(11) DEFAULT NULL,
  `type` VARCHAR(255) DEFAULT 'PAID',
  msg VARCHAR(512) DEFAULT NULL,
  reciept_status INT(11) DEFAULT NULL,
  remarks VARCHAR(512) DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  is_ticket_created TINYINT(11) DEFAULT '0',
  updated_by INT(11) DEFAULT NULL,
  updated_on DATETIME DEFAULT NULL,
  sender VARCHAR(15) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_executive_sms_log'
--

CREATE TABLE IF NOT EXISTS pnh_executive_sms_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  emp_id BIGINT(11) DEFAULT NULL,
  `type` VARCHAR(255) DEFAULT 'PAID',
  msg VARCHAR(512) DEFAULT NULL,
  receipt_status TINYINT(1) DEFAULT '0',
  remarks TEXT,
  logged_on DATETIME DEFAULT NULL,
  updated_by BIGINT(11) DEFAULT NULL,
  updated_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_stat'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_stat (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  `type` TINYINT(3) UNSIGNED NOT NULL,
  amount DOUBLE UNSIGNED NOT NULL,
  balance_after DOUBLE NOT NULL,
  `desc` VARCHAR(250) NOT NULL,
  action_for VARCHAR(255) DEFAULT NULL,
  ref_id VARCHAR(80) DEFAULT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  is_correction TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_summary'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_summary (
  statement_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT '0',
  action_type TINYINT(1) DEFAULT '0' COMMENT '1:Sales Invoice 2:Deposti Receipt 3:Receipt 4:Membership 5:A/C Correction',
  acc_correc_id BIGINT(11) DEFAULT '0',
  member_id BIGINT(11) DEFAULT '0',
  invoice_no BIGINT(11) DEFAULT '0',
  credit_note_id BIGINT(11) DEFAULT '0',
  receipt_id BIGINT(11) DEFAULT '0',
  receipt_type TINYINT(1) DEFAULT '0',
  cheque_no VARCHAR(30) DEFAULT '0',
  debit_amt DOUBLE DEFAULT '0',
  is_returned TINYINT(1) DEFAULT '0',
  credit_amt DOUBLE DEFAULT '0',
  remarks TEXT,
  `status` INT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT '0',
  PRIMARY KEY (statement_id),
  KEY franchise_id (franchise_id),
  KEY invoice_no (invoice_no),
  KEY acc_correc_id (invoice_no)
) TYPE=MYISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_account_summary_copy'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_account_summary_copy (
  statement_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT '0',
  action_type TINYINT(1) DEFAULT '0' COMMENT '1:Sales Invoice 2:Deposti Receipt 3:Receipt 4:Membership 5:A/C Correction',
  acc_correc_id BIGINT(11) DEFAULT '0',
  member_id BIGINT(11) DEFAULT '0',
  invoice_no BIGINT(11) DEFAULT '0',
  receipt_id BIGINT(11) DEFAULT '0',
  receipt_type TINYINT(1) DEFAULT '0',
  cheque_no VARCHAR(30) DEFAULT '0',
  debit_amt DOUBLE DEFAULT '0',
  credit_amt DOUBLE DEFAULT '0',
  remarks TEXT,
  `status` INT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT '0',
  PRIMARY KEY (statement_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_bank_details'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_bank_details (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  bank_name VARCHAR(100) NOT NULL,
  ifsc_code VARCHAR(100) NOT NULL,
  branch_name VARCHAR(150) NOT NULL,
  account_no VARCHAR(50) NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_menu_link'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_menu_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  fid BIGINT(200) DEFAULT NULL,
  menuid INT(3) DEFAULT '0',
  `status` INT(11) DEFAULT NULL,
  is_sch_enabled TINYINT(1) DEFAULT '0',
  sch_discount_start BIGINT(20) DEFAULT '0',
  sch_discount_end BIGINT(20) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  sch_discount INT(3) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_owners'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_owners (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  admin INT(10) UNSIGNED NOT NULL,
  franchise_id INT(10) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_photos'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_photos (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(20) UNSIGNED NOT NULL,
  pic CHAR(20) NOT NULL,
  caption VARCHAR(150) NOT NULL,
  is_deleted TINYINT(1) NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_prepaid_log'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_prepaid_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT '0',
  is_prepaid TINYINT(3) DEFAULT '0',
  reason TEXT,
  created_on BIGINT(11) DEFAULT '0',
  created_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_franchise_unorderd_log'
--

CREATE TABLE IF NOT EXISTS pnh_franchise_unorderd_log (
  id INT(11) NOT NULL AUTO_INCREMENT,
  franchise_id INT(11) DEFAULT NULL,
  last_orderd DATETIME DEFAULT NULL,
  is_notify INT(11) DEFAULT NULL,
  msg VARCHAR(255) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_return'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_return (
  id INT(11) NOT NULL AUTO_INCREMENT,
  franchise_id INT(25) DEFAULT NULL,
  invoice_no INT(25) DEFAULT NULL,
  logged_by INT(25) DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns (
  return_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  invoice_no VARCHAR(50) DEFAULT NULL,
  return_by VARCHAR(255) DEFAULT NULL,
  handled_by BIGINT(11) DEFAULT NULL,
  total_items INT(11) DEFAULT '0',
  `status` TINYINT(1) DEFAULT '0',
  order_from TINYINT(1) DEFAULT '0' COMMENT '0:pnh,1:sit,2:partners',
  returned_on DATETIME DEFAULT NULL,
  PRIMARY KEY (return_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_flags'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_flags (
  id INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_product_link'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_product_link (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  return_id BIGINT(11) DEFAULT '0',
  order_id BIGINT(11) DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT '0',
  barcode VARCHAR(100) DEFAULT NULL,
  imei_no VARCHAR(100) DEFAULT NULL,
  condition_type INT(1) DEFAULT '0' COMMENT '1: ''Good Condition'' 2:''Duplicate product'' 3:''UnOrdered'' 4:''Late Shipment'' 5:''Address not found'' 6:''Faulty and needs service''',
  is_shipped TINYINT(1) DEFAULT '0',
  is_packed TINYINT(1) DEFAULT '0',
  readytoship TINYINT(1) DEFAULT '0',
  is_stocked TINYINT(1) DEFAULT '0',
  is_refunded TINYINT(1) DEFAULT '0',
  shipped_on DATETIME DEFAULT NULL,
  stock_updated_on DATETIME DEFAULT NULL,
  refunded_on DATETIME DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_product_service'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_product_service (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  return_prod_id BIGINT(11) DEFAULT NULL,
  sent_on DATETIME DEFAULT NULL,
  sent_to VARCHAR(255) DEFAULT NULL,
  expected_dod DATE DEFAULT NULL,
  is_serviced TINYINT(1) DEFAULT '0',
  service_return_on DATETIME DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_returns_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_returns_remarks (
  id INT(11) NOT NULL AUTO_INCREMENT,
  return_prod_id BIGINT(11) DEFAULT '0',
  product_status INT(11) DEFAULT '0',
  remarks TEXT,
  parent_id BIGINT(11) DEFAULT '0',
  created_by BIGINT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_invoice_transit_log'
--

CREATE TABLE IF NOT EXISTS pnh_invoice_transit_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  sent_log_id BIGINT(11) DEFAULT NULL,
  invoice_no BIGINT(20) DEFAULT '0',
  ref_id INT(11) DEFAULT '0',
  `status` INT(2) DEFAULT '0' COMMENT '1:in-transit,2:pickup or hand-over,3:delivered,4:return',
  received_by VARCHAR(255) DEFAULT NULL,
  received_on DATETIME DEFAULT NULL,
  contact_no VARCHAR(255) DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  logged_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_less_margin_brands'
--

CREATE TABLE IF NOT EXISTS pnh_less_margin_brands (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_loyalty_points'
--

CREATE TABLE IF NOT EXISTS pnh_loyalty_points (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  amount INT(10) UNSIGNED NOT NULL,
  points INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_manifesto_log'
--

CREATE TABLE IF NOT EXISTS pnh_manifesto_log (
  id INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  st_date DATE DEFAULT NULL,
  en_date DATE DEFAULT NULL,
  invoice_nos TEXT,
  total_prints INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_membersch_deals'
--

CREATE TABLE IF NOT EXISTS pnh_membersch_deals (
  id INT(11) NOT NULL AUTO_INCREMENT,
  menuid INT(11) DEFAULT NULL,
  itemid BIGINT(20) DEFAULT NULL,
  valid_from BIGINT(11) DEFAULT NULL,
  valid_to BIGINT(11) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT '0',
  created_on BIGINT(20) DEFAULT '20',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_member_info'
--

CREATE TABLE IF NOT EXISTS pnh_member_info (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT(20) UNSIGNED NOT NULL,
  pnh_member_id BIGINT(20) UNSIGNED NOT NULL,
  franchise_id INT(10) UNSIGNED NOT NULL,
  points INT(10) UNSIGNED NOT NULL,
  gender TINYINT(1) NOT NULL,
  salute TINYINT(1) NOT NULL,
  first_name VARCHAR(70) NOT NULL,
  last_name VARCHAR(70) NOT NULL,
  dob DATE DEFAULT NULL,
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  pincode VARCHAR(12) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  marital_status TINYINT(1) NOT NULL,
  spouse_name VARCHAR(120) NOT NULL,
  child1_name VARCHAR(100) NOT NULL,
  child2_name VARCHAR(100) NOT NULL,
  anniversary DATE DEFAULT NULL,
  child1_dob DATE DEFAULT NULL,
  child2_dob DATE DEFAULT NULL,
  profession VARCHAR(40) NOT NULL,
  expense TINYINT(1) NOT NULL,
  is_card_printed TINYINT(1) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) DEFAULT '0',
  created_by INT(10) UNSIGNED NOT NULL,
  modified_by BIGINT(11) DEFAULT '0',
  dummy BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY user_id_2 (user_id),
  KEY pnh_member_id (pnh_member_id),
  KEY franchise_id (franchise_id),
  KEY address (mobile)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_member_points_track'
--

CREATE TABLE IF NOT EXISTS pnh_member_points_track (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT(20) UNSIGNED NOT NULL,
  transid VARCHAR(20) NOT NULL,
  points INT(10) NOT NULL,
  points_after INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY pnh_member_id (user_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_menu'
--

CREATE TABLE IF NOT EXISTS pnh_menu (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  min_balance_value DOUBLE DEFAULT '0',
  bal_discount DOUBLE DEFAULT '0',
  consider_mrp_chng TINYINT(1) DEFAULT '0',
  `status` TINYINT(1) NOT NULL,
  loyality_pntvalue FLOAT DEFAULT '1',
  default_margin DOUBLE DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_allotted_mid'
--

CREATE TABLE IF NOT EXISTS pnh_m_allotted_mid (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(20) UNSIGNED NOT NULL,
  mid_start BIGINT(20) UNSIGNED NOT NULL,
  mid_end BIGINT(20) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_bank_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_bank_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  bank_name VARCHAR(2555) NOT NULL,
  branch_name VARCHAR(2555) NOT NULL,
  account_number BIGINT(222) NOT NULL,
  ifsc_code VARCHAR(222) NOT NULL,
  remarks TEXT,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_book_template'
--

CREATE TABLE IF NOT EXISTS pnh_m_book_template (
  book_template_id BIGINT(20) NOT NULL AUTO_INCREMENT,
  book_type_name VARCHAR(255) DEFAULT NULL,
  `value` INT(10) DEFAULT NULL,
  product_id VARCHAR(255) DEFAULT NULL,
  menu_ids VARCHAR(255) DEFAULT NULL,
  is_active INT(1) DEFAULT '1' COMMENT '1:active,0:inactive',
  created_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (book_template_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_book_template_voucher_link'
--

CREATE TABLE IF NOT EXISTS pnh_m_book_template_voucher_link (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  book_template_id BIGINT(20) DEFAULT NULL,
  voucher_id BIGINT(20) DEFAULT NULL,
  no_of_voucher INT(10) DEFAULT NULL,
  is_active INT(1) DEFAULT '1',
  created_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_class_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_class_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  class_name VARCHAR(100) DEFAULT NULL,
  margin DOUBLE DEFAULT NULL,
  combo_margin DOUBLE NOT NULL,
  less_margin_brands DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_deposited_receipts'
--

CREATE TABLE IF NOT EXISTS pnh_m_deposited_receipts (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  deposited_reference_no BIGINT(15) DEFAULT NULL,
  bank_id INT(10) DEFAULT NULL,
  receipt_id BIGINT(255) DEFAULT NULL,
  is_submitted TINYINT(1) DEFAULT NULL,
  is_deposited TINYINT(1) DEFAULT '0',
  `status` TINYINT(1) DEFAULT NULL,
  remarks VARCHAR(2555) DEFAULT NULL,
  is_cancelled TINYINT(1) DEFAULT '0',
  cancel_status TINYINT(1) DEFAULT '0',
  cancel_reason VARCHAR(2555) DEFAULT NULL,
  cancelled_on DATETIME DEFAULT NULL,
  dbt_amt DOUBLE DEFAULT NULL,
  submitted_by BIGINT(12) DEFAULT NULL,
  submitted_on DATETIME DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_device_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_device_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  device_sl_no VARCHAR(200) DEFAULT NULL,
  device_type_id INT(11) DEFAULT NULL,
  issued_to INT(11) DEFAULT NULL COMMENT '0: instock, else id of the franchise pnh id',
  is_damaged TINYINT(1) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(11) DEFAULT NULL,
  modified_on BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_device_type'
--

CREATE TABLE IF NOT EXISTS pnh_m_device_type (
  id INT(11) NOT NULL AUTO_INCREMENT,
  device_name VARCHAR(200) DEFAULT NULL,
  description VARCHAR(200) NOT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_employee_leaves'
--

CREATE TABLE IF NOT EXISTS pnh_m_employee_leaves (
  id BIGINT(1) NOT NULL AUTO_INCREMENT,
  emp_id BIGINT(1) DEFAULT NULL,
  remarks TEXT,
  holidy_stdt DATE DEFAULT NULL,
  holidy_endt DATE DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  created_by BIGINT(1) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by BIGINT(1) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_franchise_contacts_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_franchise_contacts_info (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  contact_name VARCHAR(100) NOT NULL,
  contact_designation VARCHAR(100) NOT NULL,
  contact_mobile1 VARCHAR(20) NOT NULL,
  contact_mobile2 VARCHAR(20) NOT NULL,
  contact_telephone VARCHAR(20) NOT NULL,
  contact_fax VARCHAR(20) NOT NULL,
  contact_email1 VARCHAR(100) NOT NULL,
  contact_email2 VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_franchise_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_franchise_info (
  franchise_id INT(11) NOT NULL AUTO_INCREMENT,
  pnh_franchise_id BIGINT(11) DEFAULT NULL COMMENT '6 digit no starting with No 3',
  franchise_name VARCHAR(200) DEFAULT NULL,
  address VARCHAR(250) DEFAULT NULL,
  locality VARCHAR(200) DEFAULT NULL,
  city VARCHAR(200) DEFAULT NULL,
  postcode VARCHAR(10) DEFAULT NULL,
  state VARCHAR(100) DEFAULT NULL,
  territory_id INT(11) DEFAULT NULL,
  town_id INT(10) UNSIGNED NOT NULL,
  class_id INT(11) DEFAULT NULL,
  is_lc_store TINYINT(1) NOT NULL,
  is_sch_enabled TINYINT(1) NOT NULL,
  sch_discount DOUBLE NOT NULL,
  sch_discount_start BIGINT(20) UNSIGNED NOT NULL,
  sch_discount_end BIGINT(20) UNSIGNED NOT NULL,
  security_deposit DOUBLE DEFAULT '0',
  current_balance DOUBLE DEFAULT '0',
  credit_limit DOUBLE DEFAULT '0',
  last_credit DOUBLE DEFAULT '0',
  login_mobile1 VARCHAR(20) DEFAULT NULL,
  login_mobile2 VARCHAR(20) DEFAULT NULL,
  app_version INT(10) UNSIGNED NOT NULL,
  email_id VARCHAR(200) DEFAULT NULL,
  assigned_to INT(11) DEFAULT NULL,
  no_of_employees INT(11) DEFAULT NULL,
  store_name VARCHAR(100) NOT NULL,
  store_area INT(11) DEFAULT NULL,
  lat DOUBLE DEFAULT NULL,
  `long` DOUBLE DEFAULT NULL,
  store_open_time TIME DEFAULT NULL,
  store_close_time TIME DEFAULT NULL,
  store_tin_no VARCHAR(40) NOT NULL,
  store_pan_no VARCHAR(40) NOT NULL,
  store_service_tax_no VARCHAR(40) NOT NULL,
  store_reg_no VARCHAR(40) NOT NULL,
  own_rented TINYINT(1) DEFAULT '0',
  internet_available VARCHAR(200) DEFAULT NULL COMMENT 'comma seperated names of the ISP',
  website_name VARCHAR(200) DEFAULT NULL,
  business_type VARCHAR(150) NOT NULL,
  security_question TINYINT(3) NOT NULL,
  security_answer VARCHAR(150) NOT NULL,
  security_question2 TINYINT(3) NOT NULL,
  security_answer2 VARCHAR(100) NOT NULL,
  security_custom_question VARCHAR(120) NOT NULL,
  security_custom_question2 VARCHAR(120) NOT NULL,
  is_prepaid INT(1) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  is_suspended TINYINT(1) NOT NULL,
  suspended_on BIGINT(20) UNSIGNED NOT NULL,
  suspended_by INT(10) UNSIGNED NOT NULL,
  reason VARCHAR(2555) DEFAULT NULL,
  PRIMARY KEY (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_fran_security_cheques'
--

CREATE TABLE IF NOT EXISTS pnh_m_fran_security_cheques (
  id INT(11) NOT NULL AUTO_INCREMENT,
  franchise_id INT(11) DEFAULT '0',
  bank_name VARCHAR(255) DEFAULT NULL,
  cheque_no VARCHAR(30) DEFAULT NULL,
  cheque_date DATE DEFAULT NULL,
  collected_on DATE DEFAULT NULL,
  amount DOUBLE DEFAULT NULL,
  returned_on DATE DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_manifesto_sent_log'
--

CREATE TABLE IF NOT EXISTS pnh_m_manifesto_sent_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  manifesto_id BIGINT(11) DEFAULT NULL,
  sent_invoices TEXT,
  remark TEXT,
  remark2 TEXT,
  hndlby_type INT(11) DEFAULT '0' COMMENT '1:driver,2:fright-cordinator,3:bus transport,4:courier',
  hndlby_roleid INT(11) DEFAULT '0',
  hndleby_empid INT(11) DEFAULT '0',
  hndleby_courier_id INT(11) DEFAULT '0',
  hndleby_name VARCHAR(255) DEFAULT NULL,
  hndleby_contactno VARCHAR(255) DEFAULT NULL,
  alternative_contactno VARCHAR(255) DEFAULT NULL,
  bus_id INT(100) DEFAULT NULL,
  bus_destination INT(100) DEFAULT NULL,
  transport_type TINYINT(1) DEFAULT '0',
  lrno VARCHAR(255) DEFAULT NULL,
  hndleby_vehicle_num VARCHAR(255) DEFAULT NULL,
  start_meter_rate VARCHAR(255) DEFAULT NULL,
  amount VARCHAR(255) DEFAULT NULL,
  weight DOUBLE DEFAULT '0',
  pickup_empid INT(11) DEFAULT '0',
  office_pickup_empid VARCHAR(255) DEFAULT NULL,
  shipment_sent_date DATETIME DEFAULT NULL,
  lrn_updated_on DATETIME DEFAULT NULL,
  is_printed INT(5) DEFAULT '0',
  `status` INT(10) DEFAULT '1' COMMENT '1:pending,2:scaned,3:shipped',
  no_ofboxes BIGINT(10) DEFAULT '0',
  ref_box_no BIGINT(10) DEFAULT '0',
  sent_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_offers'
--

CREATE TABLE IF NOT EXISTS pnh_m_offers (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(20) DEFAULT '0',
  menu_id BIGINT(20) DEFAULT '0',
  brand_id BIGINT(20) DEFAULT '0',
  cat_id BIGINT(20) DEFAULT '0',
  offer_text TEXT,
  immediate_payment TINYINT(1) DEFAULT '0',
  offer_start BIGINT(20) DEFAULT '0',
  offer_end BIGINT(20) DEFAULT '0',
  created_by BIGINT(20) DEFAULT '0',
  created_on BIGINT(20) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  modified_on BIGINT(20) DEFAULT '0',
  modified_by INT(10) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_sales_target_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_sales_target_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  task_id INT(11) DEFAULT NULL,
  f_id INT(11) DEFAULT NULL,
  avg_amount DOUBLE DEFAULT NULL,
  target_amount DOUBLE DEFAULT NULL,
  actual_target DOUBLE NOT NULL,
  `status` INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_states'
--

CREATE TABLE IF NOT EXISTS pnh_m_states (
  state_id INT(11) NOT NULL AUTO_INCREMENT,
  state_name VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  PRIMARY KEY (state_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_task_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_task_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  ref_no BIGINT(11) DEFAULT '0',
  task_title VARCHAR(255) DEFAULT NULL,
  task VARCHAR(255) DEFAULT NULL,
  task_type VARCHAR(50) DEFAULT NULL,
  asgnd_town_id INT(11) DEFAULT NULL,
  on_date DATETIME DEFAULT NULL,
  due_date DATETIME DEFAULT NULL,
  assigned_by INT(11) DEFAULT NULL,
  assigned_to INT(11) DEFAULT NULL,
  task_status INT(11) DEFAULT NULL,
  assigned_on DATETIME DEFAULT NULL,
  completed_on DATETIME DEFAULT NULL,
  completed_by INT(11) DEFAULT NULL,
  cancelled_on DATETIME DEFAULT NULL,
  cancelled_by INT(11) DEFAULT NULL,
  is_active INT(11) DEFAULT NULL,
  comments TEXT,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_task_types'
--

CREATE TABLE IF NOT EXISTS pnh_m_task_types (
  id INT(11) NOT NULL AUTO_INCREMENT,
  task_type VARCHAR(25555) DEFAULT NULL,
  task_for TINYINT(1) DEFAULT '0',
  short_form VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_territory_info'
--

CREATE TABLE IF NOT EXISTS pnh_m_territory_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  state_id INT(11) DEFAULT '0',
  territory_name VARCHAR(200) DEFAULT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_uploaded_depositedslips'
--

CREATE TABLE IF NOT EXISTS pnh_m_uploaded_depositedslips (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  deposited_reference_no BIGINT(11) DEFAULT NULL,
  receipt_ids VARCHAR(20554) DEFAULT NULL,
  scanned_url VARCHAR(255) DEFAULT NULL,
  is_deposited INT(11) DEFAULT NULL,
  remarks VARCHAR(2055) DEFAULT NULL,
  uploaded_by INT(11) DEFAULT NULL,
  uploaded_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_m_voucher'
--

CREATE TABLE IF NOT EXISTS pnh_m_voucher (
  voucher_id BIGINT(20) NOT NULL AUTO_INCREMENT,
  voucher_name VARCHAR(255) DEFAULT NULL,
  denomination INT(11) DEFAULT NULL,
  created_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_order_margin_track'
--

CREATE TABLE IF NOT EXISTS pnh_order_margin_track (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(20) NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  mrp DECIMAL(10,2) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  base_margin DECIMAL(10,2) NOT NULL,
  sch_margin DECIMAL(10,2) NOT NULL,
  voucher_margin DOUBLE DEFAULT NULL,
  bal_discount DECIMAL(10,2) DEFAULT '0.00',
  qty INT(10) UNSIGNED NOT NULL,
  final_price INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_prepaid_menu_config'
--

CREATE TABLE IF NOT EXISTS pnh_prepaid_menu_config (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  menu_id INT(10) DEFAULT NULL,
  menu_margin DOUBLE DEFAULT '0',
  is_active INT(1) DEFAULT '1',
  created_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(10) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quotes'
--

CREATE TABLE IF NOT EXISTS pnh_quotes (
  quote_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(11) NOT NULL,
  respond_in_min INT(11) DEFAULT '0',
  quote_status TINYINT(1) DEFAULT '0',
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(11) NOT NULL,
  updated_on BIGINT(20) UNSIGNED NOT NULL,
  updated_by INT(11) NOT NULL,
  PRIMARY KEY (quote_id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quotes_deal_link'
--

CREATE TABLE IF NOT EXISTS pnh_quotes_deal_link (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  quote_id INT(10) UNSIGNED NOT NULL,
  pnh_id BIGINT(20) UNSIGNED NOT NULL,
  new_product VARCHAR(2555) DEFAULT NULL,
  np_mrp INT(255) DEFAULT '0',
  np_qty INT(255) DEFAULT '1',
  np_quote INT(255) DEFAULT NULL,
  qty INT(10) DEFAULT '0',
  dp_price INT(10) UNSIGNED NOT NULL,
  final_price INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL,
  order_status TINYINT(1) NOT NULL,
  transid VARCHAR(50) NOT NULL,
  price_updated_by INT(10) UNSIGNED NOT NULL,
  is_notified TINYINT(1) DEFAULT '0',
  updated_by INT(10) UNSIGNED NOT NULL,
  updated_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY quote_id (quote_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_quote_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_quote_remarks (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  quote_id INT(10) UNSIGNED NOT NULL,
  req_complete TINYINT(1) DEFAULT '0',
  remarks VARCHAR(200) NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY quote_id (quote_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_routes'
--

CREATE TABLE IF NOT EXISTS pnh_routes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  route_name VARCHAR(255) DEFAULT NULL,
  is_active INT(11) NOT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sch_discount_brands'
--

CREATE TABLE IF NOT EXISTS pnh_sch_discount_brands (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(10) UNSIGNED NOT NULL,
  sch_type TINYINT(1) DEFAULT '0',
  menuid BIGINT(20) DEFAULT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  catid INT(10) UNSIGNED NOT NULL,
  discount DECIMAL(10,2) NOT NULL,
  valid_from BIGINT(20) UNSIGNED NOT NULL,
  valid_to BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  modified_by INT(10) DEFAULT NULL,
  is_sch_enabled TINYINT(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sch_discount_track'
--

CREATE TABLE IF NOT EXISTS pnh_sch_discount_track (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(20) UNSIGNED NOT NULL,
  sch_discount DOUBLE NOT NULL,
  sch_type TINYINT(11) DEFAULT NULL,
  sch_menu VARCHAR(255) DEFAULT NULL,
  sch_discount_start BIGINT(20) UNSIGNED NOT NULL,
  sch_discount_end BIGINT(20) UNSIGNED NOT NULL,
  reason VARCHAR(250) NOT NULL,
  brandid BIGINT(20) UNSIGNED NOT NULL,
  catid INT(10) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_ship_remarksupdate_log'
--

CREATE TABLE IF NOT EXISTS pnh_ship_remarksupdate_log (
  id BIGINT(25) NOT NULL AUTO_INCREMENT,
  ship_msg_id INT(11) DEFAULT '0',
  ticket_id INT(11) DEFAULT '0',
  updated_by TIME DEFAULT NULL,
  updated_on TIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sms_log'
--

CREATE TABLE IF NOT EXISTS pnh_sms_log (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  msg VARCHAR(500) DEFAULT NULL,
  sender VARCHAR(20) DEFAULT NULL,
  franchise_id INT(10) UNSIGNED NOT NULL,
  `type` VARCHAR(255) DEFAULT '',
  reply_for INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_sms_log_sent'
--

CREATE TABLE IF NOT EXISTS pnh_sms_log_sent (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to` VARCHAR(20) NOT NULL,
  msg TEXT NOT NULL,
  franchise_id INT(10) DEFAULT NULL,
  pnh_empid INT(10) DEFAULT NULL,
  pnh_mid INT(10) DEFAULT '0',
  `type` VARCHAR(50) DEFAULT NULL COMMENT '11:invoice delivered info to franchise,12:invoice shiped notification to franchise,13:return inv info to frc',
  ticket_id BIGINT(50) DEFAULT '0',
  sent_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_special_margin_deals'
--

CREATE TABLE IF NOT EXISTS pnh_special_margin_deals (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  special_margin DECIMAL(10,2) NOT NULL,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  i_mrp DOUBLE DEFAULT NULL,
  i_price DOUBLE DEFAULT NULL,
  `from` BIGINT(20) UNSIGNED NOT NULL,
  `to` BIGINT(20) UNSIGNED NOT NULL,
  is_active TINYINT(1) DEFAULT '0',
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_superscheme_deals'
--

CREATE TABLE IF NOT EXISTS pnh_superscheme_deals (
  id INT(11) NOT NULL AUTO_INCREMENT,
  menuid BIGINT(11) DEFAULT NULL,
  itemid BIGINT(11) DEFAULT NULL,
  valid_from BIGINT(11) DEFAULT '0',
  valid_to BIGINT(11) DEFAULT '0',
  is_active TINYINT(11) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  created_on BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_super_scheme'
--

CREATE TABLE IF NOT EXISTS pnh_super_scheme (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id BIGINT(11) DEFAULT NULL,
  schme_discount_id BIGINT(11) DEFAULT NULL,
  menu_id BIGINT(11) DEFAULT NULL,
  cat_id BIGINT(15) DEFAULT NULL,
  brand_id BIGINT(15) DEFAULT NULL,
  target_value DOUBLE DEFAULT NULL,
  credit_prc DOUBLE DEFAULT NULL,
  valid_from BIGINT(20) DEFAULT NULL,
  valid_to BIGINT(20) DEFAULT NULL,
  is_active INT(11) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_task_remarks'
--

CREATE TABLE IF NOT EXISTS pnh_task_remarks (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  emp_id BIGINT(11) DEFAULT NULL,
  task_id BIGINT(11) DEFAULT NULL,
  remarks TEXT,
  posted_by BIGINT(11) DEFAULT NULL,
  posted_on DATETIME DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  logged_by BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_task_type_details'
--

CREATE TABLE IF NOT EXISTS pnh_task_type_details (
  id INT(11) NOT NULL AUTO_INCREMENT,
  custom_field_1 VARCHAR(255) DEFAULT NULL,
  task_id INT(11) DEFAULT NULL,
  task_type_id VARCHAR(255) DEFAULT NULL,
  f_id INT(11) DEFAULT NULL,
  request_msg VARCHAR(255) DEFAULT NULL,
  response_msg VARCHAR(255) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on DATE DEFAULT NULL,
  modified_on DATE DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_towns'
--

CREATE TABLE IF NOT EXISTS pnh_towns (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  route_id INT(10) UNSIGNED NOT NULL,
  territory_id INT(10) UNSIGNED NOT NULL,
  town_name VARCHAR(100) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_town_courier_priority_link'
--

CREATE TABLE IF NOT EXISTS pnh_town_courier_priority_link (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  town_id INT(11) DEFAULT '0',
  courier_priority_1 INT(5) DEFAULT '0',
  courier_priority_2 INT(5) DEFAULT '0',
  courier_priority_3 INT(5) DEFAULT '0',
  delivery_hours_1 INT(3) DEFAULT '0',
  delivery_hours_2 INT(3) DEFAULT '0',
  delivery_hours_3 INT(3) DEFAULT '0',
  delivery_type_priority1 INT(3) DEFAULT '0',
  delivery_type_priority2 INT(3) DEFAULT '0',
  delivery_type_priority3 INT(3) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_transporter_dest_address'
--

CREATE TABLE IF NOT EXISTS pnh_transporter_dest_address (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  transpoter_id BIGINT(11) DEFAULT NULL,
  short_name VARCHAR(255) DEFAULT NULL,
  address TEXT,
  city VARCHAR(255) DEFAULT NULL,
  pincode INT(10) DEFAULT NULL,
  contact_no VARCHAR(255) DEFAULT NULL,
  active INT(1) DEFAULT NULL,
  `type` VARCHAR(255) DEFAULT NULL,
  dest_addr_unqid INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(10) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_transporter_info'
--

CREATE TABLE IF NOT EXISTS pnh_transporter_info (
  id BIGINT(100) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  address TEXT,
  city VARCHAR(255) DEFAULT NULL,
  pincode VARCHAR(255) DEFAULT NULL,
  contact_no VARCHAR(255) DEFAULT NULL,
  allowed_transport VARCHAR(255) DEFAULT NULL COMMENT '1:bus,2:Cargo,3:Gp',
  active INT(10) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(10) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(10) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_allotment'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_allotment (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  allotment_id BIGINT(11) DEFAULT '0',
  book_id BIGINT(11) DEFAULT NULL,
  franchise_id BIGINT(20) DEFAULT NULL,
  `status` INT(5) DEFAULT '0' COMMENT '1:assigned to franchise,2:payed and activated,3:returned',
  margin DOUBLE DEFAULT '0',
  order_id VARCHAR(255) DEFAULT NULL,
  activated_on DATETIME DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_details'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_details (
  book_id BIGINT(20) NOT NULL AUTO_INCREMENT,
  book_template_id INT(11) DEFAULT NULL,
  book_slno VARCHAR(255) DEFAULT NULL,
  book_value INT(11) DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (book_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_receipt_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_receipt_link (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  book_id BIGINT(20) DEFAULT NULL,
  receipt_id BIGINT(20) DEFAULT NULL,
  franchise_id BIGINT(20) DEFAULT NULL,
  adjusted_value DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_book_voucher_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_book_voucher_link (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  book_id BIGINT(20) DEFAULT NULL,
  voucher_slno_id BIGINT(20) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_credit_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_credit_info (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(11) DEFAULT NULL,
  credit_added DOUBLE DEFAULT NULL,
  new_credit_limit DOUBLE DEFAULT NULL,
  credit_given_by INT(11) DEFAULT NULL COMMENT 'executive id',
  reason VARCHAR(200) NOT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_device_movement_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_device_movement_info (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  device_id INT(11) DEFAULT NULL,
  issued_to INT(11) DEFAULT NULL COMMENT '0 means come back to stock, else PNH id',
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_receipt_info'
--

CREATE TABLE IF NOT EXISTS pnh_t_receipt_info (
  receipt_id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  franchise_id INT(11) DEFAULT NULL,
  receipt_amount DOUBLE DEFAULT '0',
  unreconciliation_amount DOUBLE DEFAULT '0',
  receipt_type TINYINT(1) DEFAULT '2' COMMENT '1: deposit, 2: top-up',
  payment_mode TINYINT(1) DEFAULT '0' COMMENT '0: cash, 1: cheque, 2: dd, 3: transfer',
  bank_name VARCHAR(70) NOT NULL,
  instrument_no VARCHAR(100) DEFAULT NULL COMMENT 'cheqye / dd / transfer no',
  instrument_date BIGINT(11) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '1',
  is_submitted TINYINT(1) DEFAULT '0',
  is_deposited TINYINT(1) DEFAULT '0',
  `status` TINYINT(1) UNSIGNED NOT NULL,
  in_transit INT(1) DEFAULT '1',
  remarks VARCHAR(150) NOT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on BIGINT(20) DEFAULT NULL,
  activated_by BIGINT(20) UNSIGNED NOT NULL,
  activated_on BIGINT(20) UNSIGNED NOT NULL,
  reason VARCHAR(150) NOT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (receipt_id),
  KEY pnh_franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_tray_invoice_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_tray_invoice_link (
  tray_inv_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  tray_terr_id BIGINT(11) NOT NULL DEFAULT '0',
  invoice_no BIGINT(11) NOT NULL DEFAULT '0',
  `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1:invoice in tray,2:invoice out of tray',
  is_active TINYINT(1) NOT NULL DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  modified_by BIGINT(11) DEFAULT NULL,
  created_on DATETIME NOT NULL,
  created_by BIGINT(11) NOT NULL,
  PRIMARY KEY (tray_inv_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_tray_territory_link'
--

CREATE TABLE IF NOT EXISTS pnh_t_tray_territory_link (
  tray_terr_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  tray_id INT(11) NOT NULL,
  territory_id INT(11) NOT NULL,
  max_shipments INT(5) DEFAULT '0',
  `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:Created Not used,1:In-use,2:Filled',
  is_active TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1: Has Shipments,0: No Shipments',
  modified_on DATETIME DEFAULT NULL,
  modified_by BIGINT(11) DEFAULT NULL,
  created_by BIGINT(11) NOT NULL,
  created_on DATETIME NOT NULL,
  PRIMARY KEY (tray_terr_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_t_voucher_details'
--

CREATE TABLE IF NOT EXISTS pnh_t_voucher_details (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  voucher_id INT(11) DEFAULT NULL,
  group_code INT(11) DEFAULT NULL,
  voucher_serial_no BIGINT(12) NOT NULL,
  voucher_code BIGINT(14) NOT NULL,
  `value` DOUBLE NOT NULL,
  voucher_margin DOUBLE DEFAULT NULL,
  customer_value DOUBLE(10,2) DEFAULT NULL,
  franchise_value DOUBLE(10,2) DEFAULT NULL,
  last_redeemed_on DATETIME DEFAULT NULL,
  franchise_id BIGINT(11) DEFAULT NULL,
  member_id BIGINT(8) DEFAULT NULL,
  `status` TINYINT(11) DEFAULT '0' COMMENT '0:pending,1:voucher_linked_to_book,2:Allloted to franchise,3:Activated,4:Fully Reddemed,5:partailly Redeemed,6:Cancelled',
  assigned_on DATETIME DEFAULT NULL,
  assigned_by INT(11) DEFAULT NULL,
  is_alloted INT(1) DEFAULT '0',
  alloted_on DATETIME DEFAULT NULL,
  is_activated INT(1) DEFAULT '0',
  activated_on DATETIME DEFAULT NULL,
  redeemed_on DATETIME DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id,voucher_serial_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'pnh_voucher_activity_log'
--

CREATE TABLE IF NOT EXISTS pnh_voucher_activity_log (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  voucher_slno BIGINT(11) DEFAULT '0',
  franchise_id BIGINT(20) DEFAULT '0',
  member_id BIGINT(20) DEFAULT '0',
  transid VARCHAR(255) DEFAULT NULL,
  debit DOUBLE DEFAULT '0',
  credit DOUBLE DEFAULT '0',
  order_ids VARCHAR(255) DEFAULT '0',
  `status` TINYINT(4) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group'
--

CREATE TABLE IF NOT EXISTS products_group (
  group_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  cat_id INT(10) UNSIGNED NOT NULL,
  group_name VARCHAR(200) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (group_id),
  KEY cat_id (cat_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_attributes'
--

CREATE TABLE IF NOT EXISTS products_group_attributes (
  attribute_name_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INT(10) UNSIGNED NOT NULL,
  attribute_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (attribute_name_id),
  KEY group_id (group_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_attribute_values'
--

CREATE TABLE IF NOT EXISTS products_group_attribute_values (
  attribute_value_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INT(11) NOT NULL,
  attribute_name_id INT(11) NOT NULL,
  attribute_value VARCHAR(100) NOT NULL,
  PRIMARY KEY (attribute_value_id),
  KEY group_id (group_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_category'
--

CREATE TABLE IF NOT EXISTS products_group_category (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(70) NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_orders'
--

CREATE TABLE IF NOT EXISTS products_group_orders (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(20) NOT NULL,
  order_id BIGINT(20) UNSIGNED NOT NULL,
  product_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_group_pids'
--

CREATE TABLE IF NOT EXISTS products_group_pids (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INT(11) NOT NULL,
  product_id INT(11) NOT NULL,
  attribute_name_id INT(11) NOT NULL,
  attribute_value_id INT(11) NOT NULL,
  PRIMARY KEY (id),
  KEY group_id (group_id),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'products_src_changelog'
--

CREATE TABLE IF NOT EXISTS products_src_changelog (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT(10) UNSIGNED NOT NULL,
  is_sourceable TINYINT(3) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'product_price_changelog'
--

CREATE TABLE IF NOT EXISTS product_price_changelog (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id BIGINT(20) UNSIGNED NOT NULL,
  new_mrp DECIMAL(10,2) UNSIGNED NOT NULL,
  old_mrp DECIMAL(10,2) NOT NULL,
  reference_grn INT(10) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'proforma_invoices'
--

CREATE TABLE IF NOT EXISTS proforma_invoices (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  p_invoice_no INT(10) UNSIGNED NOT NULL,
  dispatch_id BIGINT(11) DEFAULT '0',
  transid CHAR(18) NOT NULL,
  order_id BIGINT(20) UNSIGNED NOT NULL,
  mrp INT(10) UNSIGNED NOT NULL,
  discount DECIMAL(10,2) UNSIGNED NOT NULL,
  nlc INT(10) UNSIGNED NOT NULL,
  phc INT(10) UNSIGNED NOT NULL,
  tax DOUBLE UNSIGNED NOT NULL,
  service_tax DOUBLE NOT NULL,
  cod DOUBLE UNSIGNED NOT NULL,
  ship DOUBLE UNSIGNED NOT NULL,
  giftwrap_charge DOUBLE DEFAULT '0',
  invoice_status TINYINT(1) DEFAULT '0',
  createdon BIGINT(20) DEFAULT NULL,
  cancelled_on BIGINT(20) DEFAULT NULL,
  delivery_medium VARCHAR(255) DEFAULT '0',
  tracking_id VARCHAR(50) DEFAULT '0',
  shipdatetime DATETIME DEFAULT NULL,
  notify_customer TINYINT(1) DEFAULT '0',
  is_delivered TINYINT(1) DEFAULT '0',
  is_partial_invoice TINYINT(1) DEFAULT '0',
  total_prints INT(5) DEFAULT '0',
  is_b2b TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid),
  KEY p_invoice_no (p_invoice_no),
  KEY order_id (order_id),
  KEY dispatch_id (dispatch_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'promo_email'
--

CREATE TABLE IF NOT EXISTS promo_email (
  id INT(11) NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) DEFAULT NULL,
  lastsent BIGINT(20) UNSIGNED DEFAULT NULL,
  COUNT INT(1) UNSIGNED DEFAULT '0',
  un_subscribe TINYINT(1) DEFAULT '0',
  company_name VARCHAR(100) DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY email_id (email)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'promo_email_old'
--

CREATE TABLE IF NOT EXISTS promo_email_old (
  id INT(11) NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) DEFAULT NULL,
  un_subscribe TINYINT(1) DEFAULT '0',
  COUNT INT(10) UNSIGNED NOT NULL,
  lastsent BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sample_king_users'
--

CREATE TABLE IF NOT EXISTS sample_king_users (
  userid BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `name` VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  `password` CHAR(32) NOT NULL,
  mobile BIGINT(11) UNSIGNED NOT NULL,
  corpemail VARCHAR(100) NOT NULL,
  corpid INT(10) UNSIGNED NOT NULL,
  balance INT(10) UNSIGNED NOT NULL,
  inviteid CHAR(10) NOT NULL,
  friendof BIGINT(20) UNSIGNED NOT NULL,
  special TINYINT(1) NOT NULL,
  special_id VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  landmark TEXT NOT NULL,
  telephone VARCHAR(30) NOT NULL,
  country VARCHAR(255) DEFAULT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  pincode VARCHAR(100) NOT NULL,
  `block` TINYINT(1) NOT NULL,
  verified INT(10) UNSIGNED NOT NULL,
  verify_code CHAR(10) NOT NULL,
  optin TINYINT(1) NOT NULL DEFAULT '1',
  createdon BIGINT(20) UNSIGNED NOT NULL DEFAULT '1275750318'
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'servicable_pincode'
--

CREATE TABLE IF NOT EXISTS servicable_pincode (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pincode VARCHAR(10) DEFAULT NULL,
  locality VARCHAR(200) DEFAULT NULL,
  city_name VARCHAR(150) DEFAULT NULL,
  district_name VARCHAR(150) DEFAULT NULL,
  state_name VARCHAR(150) DEFAULT NULL,
  cod_applicable TINYINT(1) DEFAULT '0',
  courier_code VARCHAR(100) DEFAULT NULL,
  active_status TINYINT(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY pincode (pincode)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'shipment_batch_process'
--

CREATE TABLE IF NOT EXISTS shipment_batch_process (
  batch_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  num_orders INT(10) UNSIGNED NOT NULL,
  process_type INT(1) DEFAULT '0',
  orders_by VARCHAR(10) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL,
  batch_remarks TEXT,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  created_on DATETIME NOT NULL,
  PRIMARY KEY (batch_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'shipment_batch_process_invoice_link'
--

CREATE TABLE IF NOT EXISTS shipment_batch_process_invoice_link (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  batch_id BIGINT(20) UNSIGNED NOT NULL,
  p_invoice_no INT(10) UNSIGNED NOT NULL,
  invoice_no BIGINT(20) UNSIGNED NOT NULL,
  invoiced_on DATETIME DEFAULT NULL,
  invoiced_by BIGINT(11) DEFAULT '0',
  awb VARCHAR(40) NOT NULL,
  courier_id INT(10) UNSIGNED NOT NULL,
  tray_id INT(11) DEFAULT '0',
  packed TINYINT(1) NOT NULL,
  shipped TINYINT(1) NOT NULL,
  is_returned TINYINT(1) DEFAULT '0',
  inv_manifesto_id BIGINT(11) DEFAULT '0',
  is_acknowleged TINYINT(1) DEFAULT '0',
  packed_on DATETIME NOT NULL,
  packed_by BIGINT(20) UNSIGNED NOT NULL,
  outscanned_on DATETIME DEFAULT NULL,
  outscanned_by BIGINT(20) DEFAULT NULL,
  outscanned TINYINT(1) DEFAULT '0',
  shipped_by BIGINT(20) UNSIGNED NOT NULL,
  shipped_on DATETIME NOT NULL,
  is_acknowleged_by INT(11) DEFAULT NULL,
  is_acknowleged_on DATETIME DEFAULT NULL,
  delivered_on DATETIME DEFAULT NULL,
  tmp_courier_name VARCHAR(100) DEFAULT NULL,
  is_delivered INT(11) DEFAULT NULL,
  delivered_by INT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY invoice_no (invoice_no),
  KEY batch_id (batch_id),
  KEY courier_id (courier_id),
  KEY shipped (shipped),
  KEY p_invoice_no (p_invoice_no),
  KEY inv_manifesto_id (inv_manifesto_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_auth'
--

CREATE TABLE IF NOT EXISTS sms_auth (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock` VARCHAR(100) NOT NULL,
  `key` CHAR(32) NOT NULL,
  hits INT(10) UNSIGNED NOT NULL,
  lasthit BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_done'
--

CREATE TABLE IF NOT EXISTS sms_done (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  msg TEXT NOT NULL,
  number BIGINT(20) UNSIGNED NOT NULL,
  sent_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_invoice_log'
--

CREATE TABLE IF NOT EXISTS sms_invoice_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  `type` BIGINT(11) DEFAULT NULL,
  fid BIGINT(11) DEFAULT NULL,
  invoice_no BIGINT(11) DEFAULT NULL,
  emp_id1 BIGINT(11) DEFAULT NULL,
  emp_id2 BIGINT(11) DEFAULT NULL,
  `status` INT(11) DEFAULT '0',
  logged_by BIGINT(11) DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_pull'
--

CREATE TABLE IF NOT EXISTS sms_pull (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  mobile BIGINT(20) UNSIGNED NOT NULL,
  msg TEXT NOT NULL,
  rule INT(10) UNSIGNED NOT NULL,
  echo TEXT NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_push'
--

CREATE TABLE IF NOT EXISTS sms_push (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to` BIGINT(20) UNSIGNED NOT NULL,
  msg TEXT NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  `code` CHAR(5) NOT NULL,
  attempts INT(10) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  last_attempt BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'sms_queue'
--

CREATE TABLE IF NOT EXISTS sms_queue (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  msg TEXT NOT NULL,
  number BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'snp_product_views'
--

CREATE TABLE IF NOT EXISTS snp_product_views (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED NOT NULL,
  userid BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'support_tickets'
--

CREATE TABLE IF NOT EXISTS support_tickets (
  ticket_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_no BIGINT(20) UNSIGNED NOT NULL,
  user_id BIGINT(20) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(150) NOT NULL,
  transid VARCHAR(20) NOT NULL,
  `type` TINYINT(1) UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  priority TINYINT(1) UNSIGNED NOT NULL,
  assigned_to INT(10) UNSIGNED NOT NULL,
  created_on DATETIME NOT NULL,
  updated_on DATETIME NOT NULL,
  PRIMARY KEY (ticket_id),
  UNIQUE KEY ticket_no (ticket_no),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'support_tickets_msg'
--

CREATE TABLE IF NOT EXISTS support_tickets_msg (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_id BIGINT(20) UNSIGNED NOT NULL,
  msg TEXT NOT NULL,
  msg_type TINYINT(1) UNSIGNED NOT NULL,
  `medium` TINYINT(1) UNSIGNED NOT NULL,
  from_customer TINYINT(1) NOT NULL,
  support_user INT(10) UNSIGNED NOT NULL,
  created_on DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY ticket_id (ticket_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'transactions_changelog'
--

CREATE TABLE IF NOT EXISTS transactions_changelog (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(20) NOT NULL,
  msg TEXT NOT NULL,
  admin BIGINT(20) UNSIGNED NOT NULL,
  `time` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_billedmrp_change_log'
--

CREATE TABLE IF NOT EXISTS t_billedmrp_change_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  invoice_no BIGINT(11) DEFAULT '0',
  p_invoice_no INT(11) DEFAULT '0',
  packed_mrp DOUBLE DEFAULT NULL,
  billed_mrp DOUBLE DEFAULT NULL,
  remarks TEXT,
  logged_on DATETIME DEFAULT NULL,
  logged_by INT(5) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_bulkordersinvoice_log'
--

CREATE TABLE IF NOT EXISTS t_bulkordersinvoice_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  grpno BIGINT(11) DEFAULT '0',
  batch_id BIGINT(11) DEFAULT '0',
  p_invno BIGINT(11) DEFAULT '0',
  invno BIGINT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_info'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_info (
  invoice_id INT(11) NOT NULL AUTO_INCREMENT,
  invoice_no VARCHAR(50) DEFAULT NULL,
  invoice_date DATETIME DEFAULT NULL,
  client_id INT(11) DEFAULT '0',
  order_id INT(11) DEFAULT '0' COMMENT 'optional, invoice can be created without order',
  total_invoice_value DOUBLE DEFAULT '0',
  total_paid_value DOUBLE DEFAULT '0',
  invoice_status TINYINT(1) DEFAULT '1' COMMENT 'default 1: active, 2: cancelled',
  payment_status TINYINT(1) DEFAULT '0' COMMENT 'default 0: payment pending; 1: partially paid, 2: fully paid',
  created_date DATETIME DEFAULT NULL,
  modified_date DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (invoice_id)
) TYPE=MYISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_payment'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_payment (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_id INT(11) DEFAULT '0',
  amount_paid DOUBLE DEFAULT '0',
  payment_type TINYINT(1) DEFAULT '1' COMMENT '1: Cash, 2: Cheque, 3: Transfer, 4: DD',
  instrument_no VARCHAR(100) DEFAULT NULL,
  instrument_date DATETIME DEFAULT NULL,
  bank_name VARCHAR(100) DEFAULT NULL,
  is_cleared TINYINT(1) DEFAULT '0' COMMENT 'default 0: not cleared, 1: when cheque/dd/transfer is cleared & reflecting in a/c',
  bounced TINYINT(1) DEFAULT NULL COMMENT '0: not bounced, 1: bounced',
  remarks VARCHAR(250) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_invoice_product_info'
--

CREATE TABLE IF NOT EXISTS t_client_invoice_product_info (
  id INT(11) NOT NULL AUTO_INCREMENT,
  order_id INT(11) DEFAULT '0' COMMENT 'optional, invoice can be created without order',
  invoice_id INT(11) DEFAULT '0',
  product_id INT(11) DEFAULT '0',
  mrp DOUBLE DEFAULT '0',
  margin_offered DOUBLE DEFAULT '0',
  offer_price DOUBLE DEFAULT '0',
  tax_percent DOUBLE DEFAULT '0',
  invoice_qty INT(11) DEFAULT '0',
  active_status TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM  ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_order_info'
--

CREATE TABLE IF NOT EXISTS t_client_order_info (
  order_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id INT(11) DEFAULT '0',
  order_reference_no VARCHAR(150) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  order_status TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (order_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_client_order_product_info'
--

CREATE TABLE IF NOT EXISTS t_client_order_product_info (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT(11) UNSIGNED DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0' COMMENT 'optional, client might order product which is not there with us, we will purchase & then link it to order',
  product_name VARCHAR(200) DEFAULT NULL,
  mrp DOUBLE DEFAULT '0',
  order_qty INT(11) DEFAULT '0',
  invoiced_qty INT(11) DEFAULT '0',
  active_status TINYINT(1) DEFAULT '1',
  created_on BIGINT(20) DEFAULT NULL,
  modified_on BIGINT(20) DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_exotel_agent_status'
--

CREATE TABLE IF NOT EXISTS t_exotel_agent_status (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  callsid VARCHAR(255) DEFAULT NULL,
  `from` VARCHAR(50) DEFAULT NULL,
  dialwhomno VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(255) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_info'
--

CREATE TABLE IF NOT EXISTS t_grn_info (
  grn_id INT(11) NOT NULL AUTO_INCREMENT,
  po_id INT(11) DEFAULT NULL,
  vendor_id INT(11) DEFAULT NULL,
  purchase_invoice_no VARCHAR(150) DEFAULT NULL COMMENT 'invoice or delivery challan no',
  purchase_invoice_value DECIMAL(15,4) DEFAULT NULL,
  purchase_invoice_date DATE DEFAULT NULL,
  transporter_name VARCHAR(255) DEFAULT NULL,
  driver_name VARCHAR(255) DEFAULT NULL,
  transporter_contact_no VARCHAR(255) DEFAULT NULL,
  vehicle_no VARCHAR(255) DEFAULT NULL,
  payment_status TINYINT(1) DEFAULT '0',
  grn_status TINYINT(11) DEFAULT NULL,
  remarks VARCHAR(2000) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (grn_id),
  KEY po_id (po_id),
  KEY vendor_id (vendor_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_invoice_link'
--

CREATE TABLE IF NOT EXISTS t_grn_invoice_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  grn_id INT(11) DEFAULT NULL,
  purchase_inv_no VARCHAR(30) DEFAULT NULL,
  purchase_inv_date DATE DEFAULT NULL,
  purchase_inv_value DECIMAL(15,4) DEFAULT '0.0000',
  is_active TINYINT(1) DEFAULT '1',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY grn_id (grn_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_grn_product_link'
--

CREATE TABLE IF NOT EXISTS t_grn_product_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  grn_id INT(11) DEFAULT NULL,
  po_id INT(11) DEFAULT NULL,
  product_id INT(11) DEFAULT NULL,
  invoice_qty DOUBLE DEFAULT NULL,
  received_qty DOUBLE DEFAULT NULL,
  mrp DECIMAL(15,4) DEFAULT NULL,
  dp_price DECIMAL(15,4) DEFAULT '0.0000',
  purchase_price DECIMAL(15,4) DEFAULT NULL,
  tax_percent DOUBLE DEFAULT NULL,
  ref_stock_id BIGINT(11) DEFAULT '0',
  location_id INT(11) DEFAULT '0',
  rack_bin_id INT(11) DEFAULT '0',
  margin DECIMAL(10,4) DEFAULT NULL,
  scheme_discount_value DECIMAL(15,4) DEFAULT '0.0000',
  scheme_discunt_type TINYINT(1) DEFAULT '1' COMMENT '1: Percent, 2: Value',
  is_foc TINYINT(1) DEFAULT '0',
  has_offer TINYINT(1) DEFAULT '0',
  grn_invoice_link_id BIGINT(11) DEFAULT '0',
  expiry_date DATE DEFAULT NULL,
  approval_status TINYINT(1) DEFAULT '1',
  approved_by INT(11) DEFAULT '0',
  approval_date DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  is_processed_upd TINYINT(1) DEFAULT '0',
  PRIMARY KEY (id),
  KEY grn_id (grn_id),
  KEY po_id (po_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_imeino_allotment_track'
--

CREATE TABLE IF NOT EXISTS t_imeino_allotment_track (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  imeino_id BIGINT(11) DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0',
  imei_no VARCHAR(255) DEFAULT NULL,
  order_id BIGINT(20) DEFAULT '0',
  invoice_no BIGINT(20) DEFAULT '0',
  transid VARCHAR(255) DEFAULT NULL,
  is_cancelled INT(1) DEFAULT '0',
  alloted_on DATETIME DEFAULT NULL,
  cancelled_on DATETIME DEFAULT NULL,
  alloted_by BIGINT(11) DEFAULT '0',
  cancelled_by BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_imei_no'
--

CREATE TABLE IF NOT EXISTS t_imei_no (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT(10) UNSIGNED NOT NULL,
  imei_no VARCHAR(20) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  grn_id INT(10) UNSIGNED NOT NULL,
  stock_id BIGINT(11) DEFAULT '0',
  is_returned TINYINT(1) DEFAULT '0',
  return_prod_id BIGINT(11) DEFAULT '0',
  order_id BIGINT(20) UNSIGNED NOT NULL,
  is_imei_activated TINYINT(1) DEFAULT '0',
  imei_activated_on DATETIME DEFAULT NULL,
  activated_by INT(11) DEFAULT '0',
  activated_mob_no VARCHAR(20) DEFAULT NULL,
  activated_member_id INT(11) DEFAULT '0',
  ref_credit_note_id BIGINT(11) DEFAULT '0',
  created_on BIGINT(20) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id),
  KEY activated_mob_no (activated_mob_no),
  KEY activated_member_id (activated_member_id),
  KEY order_id (order_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_imei_update_log'
--

CREATE TABLE IF NOT EXISTS t_imei_update_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  imei_no VARCHAR(255) DEFAULT NULL,
  product_id BIGINT(11) DEFAULT '0',
  stock_id BIGINT(11) DEFAULT '0',
  grn_id BIGINT(11) DEFAULT '0',
  alloted_order_id BIGINT(11) DEFAULT '0',
  alloted_on DATETIME DEFAULT NULL,
  invoice_no BIGINT(11) DEFAULT '0',
  return_id BIGINT(11) DEFAULT '0',
  is_cancelled TINYINT(1) DEFAULT '0',
  cancelled_on DATETIME DEFAULT NULL,
  is_active TINYINT(1) DEFAULT '0',
  logged_on DATETIME DEFAULT NULL,
  logged_by INT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_invoice_credit_notes'
--

CREATE TABLE IF NOT EXISTS t_invoice_credit_notes (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  `type` TINYINT(1) DEFAULT '1' COMMENT '1:Invoice,2:IMEI Scheme',
  grp_no BIGINT(11) DEFAULT '0',
  franchise_id BIGINT(11) DEFAULT '0',
  invoice_no BIGINT(11) DEFAULT '0',
  amount DOUBLE DEFAULT '0',
  is_active TINYINT(1) DEFAULT '0',
  ref_id BIGINT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY invoice_no (invoice_no),
  KEY franchise_id (franchise_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_outscanentry_log'
--

CREATE TABLE IF NOT EXISTS t_outscanentry_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  outscan_no VARCHAR(255) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_list'
--

CREATE TABLE IF NOT EXISTS t_paf_list (
  id INT(11) NOT NULL AUTO_INCREMENT,
  handled_by VARCHAR(255) DEFAULT NULL,
  handled_by_mob VARCHAR(255) DEFAULT NULL,
  paf_status TINYINT(1) DEFAULT '0',
  cancelled_on DATETIME DEFAULT NULL,
  cancelled_by INT(11) DEFAULT '0',
  remarks TEXT,
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_productlist'
--

CREATE TABLE IF NOT EXISTS t_paf_productlist (
  id INT(11) NOT NULL AUTO_INCREMENT,
  paf_id INT(11) DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0',
  vendor_id INT(11) DEFAULT '0',
  qty DOUBLE DEFAULT NULL,
  mrp DOUBLE DEFAULT NULL,
  notify_handler TINYINT(1) DEFAULT '0',
  po_id INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_paf_smslog'
--

CREATE TABLE IF NOT EXISTS t_paf_smslog (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  paf_id INT(11) DEFAULT NULL,
  handled_by INT(11) DEFAULT NULL,
  message TEXT,
  `status` TINYINT(1) DEFAULT '0',
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_partner_manifesto_log'
--

CREATE TABLE IF NOT EXISTS t_partner_manifesto_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  serial_no BIGINT(11) DEFAULT '0',
  partner_id INT(11) DEFAULT '0',
  invoice_no BIGINT(11) DEFAULT '0',
  partner_order_no VARCHAR(40) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY partner_order_no (partner_order_no),
  KEY partner_id (serial_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_pending_voucher_document_link'
--

CREATE TABLE IF NOT EXISTS t_pending_voucher_document_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  voucher_id INT(11) DEFAULT NULL,
  adjusted_amount DOUBLE UNSIGNED DEFAULT NULL,
  ref_doc_id INT(11) DEFAULT NULL,
  ref_doc_type TINYINT(1) DEFAULT '1' COMMENT '1: GRN, 2: PO',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_pending_voucher_info'
--

CREATE TABLE IF NOT EXISTS t_pending_voucher_info (
  voucher_id INT(11) NOT NULL AUTO_INCREMENT,
  voucher_type_id INT(11) DEFAULT '1' COMMENT '1: Payment Voucher, 2: Receipt Voucher',
  voucher_date DATETIME DEFAULT NULL,
  voucher_value DOUBLE UNSIGNED DEFAULT NULL,
  payment_mode TINYINT(1) DEFAULT '0' COMMENT '1-Cash, 2-Cheque, 3-DD, 4-Bank Transfers',
  instrument_no VARCHAR(20) DEFAULT NULL,
  instrument_date DATETIME DEFAULT NULL,
  instrument_issued_bank VARCHAR(200) DEFAULT NULL,
  narration VARCHAR(500) DEFAULT NULL,
  active_status TINYINT(1) DEFAULT '1',
  is_reveresed TINYINT(1) DEFAULT '0',
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_pnh_taskactivity'
--

CREATE TABLE IF NOT EXISTS t_pnh_taskactivity (
  id INT(11) NOT NULL AUTO_INCREMENT,
  start_date VARCHAR(255) NOT NULL,
  end_date VARCHAR(255) NOT NULL,
  task_id INT(11) DEFAULT NULL,
  msg VARCHAR(255) NOT NULL,
  task_status INT(11) DEFAULT NULL,
  logged_by INT(11) DEFAULT NULL,
  logged_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_po_info'
--

CREATE TABLE IF NOT EXISTS t_po_info (
  po_id BIGINT(11) NOT NULL AUTO_INCREMENT,
  vendor_id INT(11) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  approval_status TINYINT(1) DEFAULT '0',
  approved_by INT(11) DEFAULT '0',
  approval_date DATETIME DEFAULT NULL,
  payment_status TINYINT(1) DEFAULT '0' COMMENT 'this can be updated based on payment done to vendor',
  po_status TINYINT(1) DEFAULT NULL,
  total_value DECIMAL(10,2) UNSIGNED NOT NULL,
  paf_id BIGINT(11) DEFAULT NULL,
  date_of_delivery DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  status_remarks VARCHAR(2555) DEFAULT NULL,
  PRIMARY KEY (po_id),
  KEY vendor_id (vendor_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_po_product_link'
--

CREATE TABLE IF NOT EXISTS t_po_product_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  po_id INT(11) DEFAULT NULL,
  product_id INT(11) DEFAULT NULL,
  order_qty DOUBLE DEFAULT NULL,
  received_qty DOUBLE DEFAULT '0',
  mrp DECIMAL(15,4) DEFAULT NULL,
  dp_price DECIMAL(15,4) DEFAULT '0.0000',
  margin DECIMAL(10,4) DEFAULT '0.0000',
  scheme_discount_value DECIMAL(15,4) DEFAULT '0.0000',
  scheme_discount_type TINYINT(1) DEFAULT '1' COMMENT '1: Percent, 2: Value',
  purchase_price DECIMAL(15,4) DEFAULT '0.0000',
  is_foc TINYINT(1) DEFAULT '0',
  has_offer TINYINT(1) DEFAULT '0',
  special_note VARCHAR(200) DEFAULT NULL,
  alert_qty_mismatch TINYINT(1) DEFAULT '0',
  is_active TINYINT(1) DEFAULT '1',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY product_id (product_id),
  KEY po_id (po_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_process_partialqty_orders'
--

CREATE TABLE IF NOT EXISTS t_process_partialqty_orders (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  transid VARCHAR(50) DEFAULT NULL,
  oid BIGINT(11) DEFAULT '0',
  new_oid BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT NULL,
  new_qty DOUBLE DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  created_by BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_refund_info'
--

CREATE TABLE IF NOT EXISTS t_refund_info (
  refund_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  transid VARCHAR(20) NOT NULL,
  invoice_no BIGINT(11) DEFAULT NULL,
  amount DECIMAL(10,2) UNSIGNED NOT NULL,
  refund_for VARCHAR(20) DEFAULT 'cancel',
  `status` TINYINT(3) UNSIGNED NOT NULL,
  created_on BIGINT(20) UNSIGNED NOT NULL,
  created_by INT(10) UNSIGNED NOT NULL,
  modified_on BIGINT(20) UNSIGNED NOT NULL,
  modified_by BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (refund_id),
  KEY transid (transid)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_refund_order_item_link'
--

CREATE TABLE IF NOT EXISTS t_refund_order_item_link (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  refund_id BIGINT(20) UNSIGNED NOT NULL,
  order_id BIGINT(20) UNSIGNED NOT NULL,
  invoice_no INT(11) DEFAULT NULL,
  qty INT(10) UNSIGNED NOT NULL,
  refund_amt DOUBLE DEFAULT NULL,
  PRIMARY KEY (id),
  KEY refund_id (refund_id),
  KEY order_id (order_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_reserved_batch_stock'
--

CREATE TABLE IF NOT EXISTS t_reserved_batch_stock (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  batch_id BIGINT(11) DEFAULT '0',
  p_invoice_no BIGINT(11) DEFAULT '0',
  product_id BIGINT(11) DEFAULT '0',
  stock_info_id BIGINT(11) DEFAULT '0',
  order_id BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT '0',
  extra_qty DOUBLE DEFAULT '0',
  release_qty DOUBLE DEFAULT '0',
  reserved_on BIGINT(20) DEFAULT NULL,
  released_on BIGINT(20) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  tmp_prev_stk_id BIGINT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY batch_id (batch_id),
  KEY p_invoice_no (p_invoice_no),
  KEY product_id (product_id),
  KEY stock_info_id (stock_info_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_info'
--

CREATE TABLE IF NOT EXISTS t_stock_info (
  stock_id INT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT '0',
  location_id INT(11) DEFAULT '0',
  rack_bin_id INT(11) DEFAULT '0',
  mrp DECIMAL(15,4) DEFAULT '0.0000',
  available_qty DOUBLE DEFAULT '0',
  product_barcode VARCHAR(50) DEFAULT NULL,
  in_transit DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  tmp_brandid DOUBLE DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_info_copy'
--

CREATE TABLE IF NOT EXISTS t_stock_info_copy (
  stock_id INT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT NULL,
  location_id INT(11) DEFAULT NULL,
  rack_bin_id INT(11) DEFAULT NULL,
  mrp DECIMAL(15,4) DEFAULT '0.0000',
  available_qty DOUBLE DEFAULT '0',
  in_transit DOUBLE DEFAULT '0',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  tmp_brandid DOUBLE DEFAULT '0',
  PRIMARY KEY (stock_id),
  KEY product_id (product_id),
  KEY location_id (location_id),
  KEY rack_bin_id (rack_bin_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_stock_update_log'
--

CREATE TABLE IF NOT EXISTS t_stock_update_log (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  product_id INT(11) DEFAULT NULL,
  update_type TINYINT(1) DEFAULT '0' COMMENT '0: Out, 1: In',
  p_invoice_id INT(10) UNSIGNED NOT NULL,
  corp_invoice_id BIGINT(11) DEFAULT NULL,
  invoice_id BIGINT(11) DEFAULT NULL,
  grn_id INT(11) DEFAULT NULL,
  voucher_book_slno VARCHAR(255) DEFAULT NULL,
  return_prod_id BIGINT(11) DEFAULT '0',
  qty DOUBLE DEFAULT NULL,
  current_stock DOUBLE DEFAULT NULL,
  msg VARCHAR(255) NOT NULL,
  mrp_change_updated TINYINT(1) DEFAULT '-1' COMMENT '0: no,1: yes,-1:not from stock intake',
  stock_info_id BIGINT(11) DEFAULT '0',
  stock_qty INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  created_by INT(11) DEFAULT '0',
  PRIMARY KEY (id),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_trans_invoice_marker'
--

CREATE TABLE IF NOT EXISTS t_trans_invoice_marker (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  transid VARCHAR(50) DEFAULT NULL,
  invoice_no BIGINT(11) DEFAULT '0',
  is_pnh TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY invoice_no (invoice_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_trans_proforma_invoice_marker'
--

CREATE TABLE IF NOT EXISTS t_trans_proforma_invoice_marker (
  id BIGINT(11) NOT NULL AUTO_INCREMENT,
  transid VARCHAR(50) DEFAULT NULL,
  p_invoice_no BIGINT(11) DEFAULT '0',
  is_pnh TINYINT(1) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY p_invoice_no (p_invoice_no)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_upd_product_deal_link_log'
--

CREATE TABLE IF NOT EXISTS t_upd_product_deal_link_log (
  id BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  itemid BIGINT(20) UNSIGNED DEFAULT NULL,
  product_id INT(11) UNSIGNED DEFAULT NULL,
  product_mrp DECIMAL(15,4) DEFAULT '0.0000',
  qty INT(11) DEFAULT '1',
  is_updated INT(11) DEFAULT '0',
  is_sit INT(11) DEFAULT '0',
  perform_on DATETIME DEFAULT NULL,
  perform_by INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY itemid (itemid),
  KEY product_id (product_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_document_link'
--

CREATE TABLE IF NOT EXISTS t_voucher_document_link (
  id INT(11) NOT NULL AUTO_INCREMENT,
  voucher_id INT(11) DEFAULT NULL,
  adjusted_amount DOUBLE UNSIGNED DEFAULT NULL,
  ref_doc_id INT(11) DEFAULT NULL,
  ref_doc_type TINYINT(1) DEFAULT '1' COMMENT '1: GRN, 2: PO',
  created_by INT(11) DEFAULT NULL,
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT NULL,
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_expense_link'
--

CREATE TABLE IF NOT EXISTS t_voucher_expense_link (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  voucher_id BIGINT(20) UNSIGNED NOT NULL,
  expense_type TINYINT(1) UNSIGNED NOT NULL,
  bill_no VARCHAR(30) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 't_voucher_info'
--

CREATE TABLE IF NOT EXISTS t_voucher_info (
  voucher_id INT(11) NOT NULL AUTO_INCREMENT,
  voucher_type_id INT(11) DEFAULT '1' COMMENT '1: Payment Voucher, 2: Receipt Voucher',
  voucher_date DATETIME DEFAULT NULL,
  voucher_value DOUBLE UNSIGNED DEFAULT NULL,
  payment_mode TINYINT(1) DEFAULT '0' COMMENT '1-Cash, 2-Cheque, 3-DD, 4-Bank Transfers',
  instrument_no VARCHAR(20) DEFAULT NULL,
  instrument_date DATETIME DEFAULT NULL,
  instrument_issued_bank VARCHAR(200) DEFAULT NULL,
  narration VARCHAR(500) DEFAULT NULL,
  active_status TINYINT(1) DEFAULT '1',
  is_reveresed TINYINT(1) DEFAULT '0',
  created_by INT(11) DEFAULT '0',
  created_on DATETIME DEFAULT NULL,
  modified_by INT(11) DEFAULT '0',
  modified_on DATETIME DEFAULT NULL,
  PRIMARY KEY (voucher_id)
) TYPE=MYISAM ;

-- --------------------------------------------------------

--
-- Table structure for table 'user_access_roles'
--

CREATE TABLE IF NOT EXISTS user_access_roles (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_role VARCHAR(100) NOT NULL,
  const_name VARCHAR(100) NOT NULL,
  `value` BIGINT(20) DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=INNODB ;

-- --------------------------------------------------------

--
-- Table structure for table 'variant_deal_link'
--

CREATE TABLE IF NOT EXISTS variant_deal_link (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  variant_id BIGINT(20) UNSIGNED NOT NULL,
  item_id BIGINT(20) UNSIGNED NOT NULL,
  variant_value VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table 'variant_info'
--

CREATE TABLE IF NOT EXISTS variant_info (
  variant_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  variant_name VARCHAR(150) NOT NULL,
  variant_type TINYINT(1) UNSIGNED NOT NULL,
  created_by BIGINT(20) UNSIGNED NOT NULL,
  created_on DATETIME NOT NULL,
  modified_on DATETIME NOT NULL,
  PRIMARY KEY (variant_id)
) TYPE=MYISAM;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

#Mar_24_2014
ALTER TABLE `pnh_member_offers` ADD COLUMN `feedback_value` INT(50) NULL COMMENT 'customer feedback value' AFTER `feedback_status`;