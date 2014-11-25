#Jun_21_2014
SELECT * FROM m_employee_dept_link WHERE employee_id=? AND is_active=?;
DESC king_invoice
DESC shipment_batch_process_invoice_link
# deal_member_price_changelog -- imp
# deal_price_changelog
#=============================================================
#Jun_23_2014
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_is_offer` TINYINT(11) DEFAULT 0 NULL AFTER `old_mp_max_order_qty`, ADD COLUMN `mp_offer_from` DATETIME NULL AFTER `mp_is_offer`, ADD COLUMN `mp_offer_to` DATETIME NULL AFTER `mp_offer_from`; 
ALTER TABLE `king_dealitems` ADD COLUMN `mp_is_offer` TINYINT(11) NULL AFTER `mp_max_order_qty`, ADD COLUMN `mp_offer_from` DATETIME NULL AFTER `mp_is_offer`, ADD COLUMN `mp_offer_to` DATETIME NULL AFTER `mp_offer_from`; 
#=============================================================
# Jun_23_2014
#===============================================
ALTER TABLE `m_vendor_api_templates` ADD COLUMN `attr3` VARCHAR (255)  NULL  AFTER `attr2`, ADD COLUMN `attr4` VARCHAR (255)  NULL  AFTER `attr3`, ADD COLUMN `attr5` VARCHAR (255)  NULL  AFTER `attr4`, ADD COLUMN `attr6` VARCHAR (255)  NULL  AFTER `attr5`, ADD COLUMN `attr7` VARCHAR (255)  NULL  AFTER `attr6`, ADD COLUMN `attr8` VARCHAR (255)  NULL  AFTER `attr7`,CHANGE `purchase_cost` `purchase_cost` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci; 
ALTER TABLE `m_vendor_api_datalog` ADD COLUMN `attr3` VARCHAR (255)  NULL  AFTER `attr2`, ADD COLUMN `attr4` VARCHAR (255)  NULL  AFTER `attr3`, ADD COLUMN `attr5` VARCHAR (255)  NULL  AFTER `attr4`, ADD COLUMN `attr6` VARCHAR (255)  NULL  AFTER `attr5`, ADD COLUMN `attr7` VARCHAR (255)  NULL  AFTER `attr6`, ADD COLUMN `attr8` VARCHAR (255)  NULL  AFTER `attr7`,CHANGE `purchase_cost` `purchase_cost` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci;
CREATE TABLE `m_vendor_attributes_link` (                                                   
    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,                                                  
    `vendor_id` INT(11) DEFAULT NULL,                                                         
    `attr` VARCHAR(255) DEFAULT NULL COMMENT 'vendor template link table attributes names ',  
    `attr_id` INT(11) DEFAULT NULL COMMENT 'master attribute table attrbute ids',             
    `created_on` DATETIME DEFAULT NULL,                                                       
    `created_by` INT(11) DEFAULT NULL,                                                        
    PRIMARY KEY (`id`)                                                                        
  );
#=====================================
SELECT * FROM king_dealitems WHERE id='2234525859';
SELECT * FROM deal_member_price_changelog WHERE itemid='2234525859';
`m_vendor_info`
`m_vendor_api_resources`
`m_vendor_api_access`
`m_vendor_api_templates`
`m_vendor_attributes_link`
#===========================================
# Jun_23_2014
INSERT INTO `king_admin` (`user_id`, `name`, `username`, `password`, `usertype`, `access`, `brandid`, `fullname`, `email`, `createdon`, `modifiedon`) VALUES ('0ec7bd10ef5d2c99e7121241f8f12d1f', 'Franchise/API', 'franchise', 'dc21afd02d0c2edb2bee3ead8da23e1f', '1', '0', '0', '', 'franchise@storeking.in', '2014-06-23 18:19:21', '2014-06-23 18:19:23'); 
#===========================================
SELECT a.id,a.itemid,a.old_mrp,a.new_mrp,a.old_price,a.new_price,a.reference_grn,a.created_by,a.created_on,b.username AS logged_by,mp.old_member_price,mp.new_member_price FROM deal_price_changelog a 
JOIN deal_member_price_changelog mp ON mp.itemid = a.itemid 
LEFT JOIN king_admin b ON a.created_by = b.id 
WHERE a.itemid='8575333269' ORDER BY a.id DESC
#===
SELECT * FROM king_dealitems WHERE id='8575333269';
`m_employee_roles`
#===========================================
# Jun_24_2014
ALTER TABLE `m_employee_roles` CHANGE `emp_status` `emp_role_status` TINYINT(11) DEFAULT 1 NULL;
#===========================================
# Jun_25_2014
SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_max_qty,di.mp_max_order_qty,di.mp_is_offer,di.mp_offer_from,di.mp_offer_to
	,di.live,dl.publish,e.product_id,e.is_sourceable
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid
	LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = c.product_id      
	WHERE di.is_pnh = 1  
	ORDER BY di.name# LIMIT 0,100
DESC king_deals;
SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id = '59';
mp_offer_note
#===========================================
# Jun_25_2014
ALTER TABLE `king_dealitems` ADD COLUMN `mp_offer_note` TEXT NULL AFTER `mp_offer_to`;
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_offer_note` TEXT NULL AFTER `mp_offer_to`;
ALTER TABLE `deal_member_price_changelog` CHANGE `is_active` `is_active` TINYINT(1) DEFAULT 1 NOT NULL AFTER `mp_offer_note`, ADD COLUMN `modified_on` DATETIME NULL AFTER `created_on`, ADD COLUMN `modified_by` INT(10) NULL AFTER `modified_on`;
# Jun_26_2014
ALTER TABLE `deal_member_price_changelog` CHANGE `new_mp_max_qty` `new_mp_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_qty` `old_mp_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `new_mp_max_order_qty` `new_mp_max_order_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_order_qty` `old_mp_max_order_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL;
#===========================================
UPDATE deal_member_price_changelog SET mp_offer_note = 'Test1',modified_on='2014-06-25 20:44:31',modified_by='37' WHERE is_active=1 AND itemid = '9797188692'  LIMIT 1
SELECT * FROM deal_member_price_changelog WHERE itemid='9797188692'
UPDATE deal_member_price_changelog SET mp_offer_note = 'Test10001894',modified_on='2014-06-25 21:23:19',modified_by='37' WHERE is_active=1 AND itemid = '891544930349'  LIMIT 1
SELECT * FROM king_dealitems WHERE id='891544930349'
SELECT * FROM deal_member_price_changelog WHERE itemid='891544930349'
SELECT * FROM `deal_member_price_changelog`;
#Jun_26_2014
UPDATE king_dealitems di
SET offer_price=\
SELECT * FROM deal_member_price_changelog WHERE itemid='2624864542'
UPDATE `king_dealitems` SET `member_price` = '4543', `mp_max_qty` = '10', `mp_max_order_qty` = '1', `mp_is_offer` = '1', `mp_offer_from` = '20142014-06-26', `mp_offer_to` = '20142014-06-30'
			, `mp_offer_note` = 'Bake N store very good price' WHERE `id` = '2624864542';
SELECT mpl.* FROM deal_member_price_changelog mpl WHERE mpl.itemid='2234525859' ORDER BY mpl.id DESC
INSERT INTO `deal_member_price_changelog` (`itemid`, `mp_offer_note`, `created_on`, `created_by`) VALUES ('2234525859', 'Test1', '2014-06-26 12:44:57', '37')
UPDATE deal_member_price_changelog SET `mp_offer_note` = 'Test1',`modified_on`='2014-06-26 12:48:02',`modified_by`='37' WHERE `is_active`=1 AND itemid = '2234525859'  LIMIT 1
UPDATE `king_dealitems` SET `member_price` = '1400', `mp_max_qty` = '11', `mp_max_order_qty` = '1', `mp_is_offer` = '1', `mp_offer_from` = '2014-06-25', `mp_offer_to` = '2014-06-30', `mp_offer_note` = 'Test1' WHERE `id` = '2234525859'
#=============================================
#Jun_26_2014
ALTER TABLE `king_dealitems` CHANGE `mp_max_qty` `mp_frn_max_qty` INT(10) DEFAULT 0 NULL, CHANGE `mp_max_order_qty` `mp_mem_max_qty` INT(10) DEFAULT 0 NULL;
ALTER TABLE `deal_member_price_changelog` CHANGE `new_mp_max_qty` `new_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_qty` `old_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `new_mp_max_order_qty` `new_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_order_qty` `old_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE `king_dealitems` CHANGE `mp_frn_max_qty` `mp_frn_max_qty` INT(10) DEFAULT 0 NOT NULL;
#=============================================
SELECT * FROM deal_member_price_changelog;
SELECT i.max_allowed_qty,i.billon_orderprice,i.gender_attr,d.catid,d.brandid,d.tagline,i.nyp_price,i.pnh_id,i.id,i.dealid,b.name AS brand,c.name AS category,
			  i.name,i.pic,i.orgprice,i.price,i.store_price,d.description,d.publish,e.name AS created_by,f.name AS mod_name,i.created_on,i.modified_on,
			  d.menuid,m.name AS menu_name,i.has_insurance,i.member_price,i.mp_frn_max_qty,i.mp_mem_max_qty
			  		FROM king_dealitems i 
			  		JOIN king_deals d ON d.dealid=i.dealid 
			  		LEFT JOIN king_brands b ON b.id=d.brandid 
			  		JOIN king_categories c ON c.id=d.catid 
			  		LEFT JOIN pnh_menu m ON m.id=d.menuid
			  		LEFT JOIN king_admin e ON e.id=i.created_by
			  		LEFT JOIN king_admin f ON f.id=i.modified_by
			  		WHERE i.id='8426452442' OR i.pnh_id='';
			  		
SELECT mpl.* FROM deal_member_price_changelog mpl WHERE mpl.itemid='9215926271'
ORDER BY mpl.id DESC
DESC deal_price_changelog
#2
UPDATE deal_member_price_changelog SET is_active=0,modified_on='1145',modified_by='11' WHERE itemid = '1'  LIMIT 1
#3
INSERT INTO `deal_member_price_changelog` (`itemid`, `new_member_price`, `old_member_price`, `new_mp_frn_max_qty`, `old_mp_frn_max_qty`, `new_mp_mem_max_qty`, `old_mp_mem_max_qty`, `mp_is_offer`, `mp_offer_from`, `mp_offer_to`, `mp_offer_note`, `created_by`, `created_on`, `is_active`) 
VALUES ('9217252483', '1145', '0.00', '11', NULL, '1', '0', '1', '2014-06-26', '2014-06-28', '0', '37', '2014-06-26 18:41:16', '1')
SELECT * FROM deal_member_price_changelog WHERE itemid='2749173323';
SELECT * FROM king_dealitems WHERE id='2749173323'
DESC king_dealitems;
SELECT * FROM king_dealitems WHERE id ='9217252483';
UPDATE deal_member_price_changelog SET is_active=0,modified_on=?,modified_by=? WHERE itemid = '';
SELECT * FROM deal_member_price_changelog WHERE itemid='9217252483';
SELECT * FROM king_dealitems WHERE id='2749173323'
SELECT * FROM `m_dept_request_types` ORDER BY id;
ALTER TABLE `m_client_category_link` ADD COLUMN `main_category` VARCHAR (255)  NULL  AFTER `client_category`;
ALTER TABLE `m_vendor_api_templates` ADD COLUMN `spec26` VARCHAR (255)  NULL  AFTER `spec25`, ADD COLUMN `spec27` VARCHAR (255)  NULL  AFTER `spec26`, ADD COLUMN `spec28` VARCHAR (255)  NULL  AFTER `spec27`, ADD COLUMN `spec29` VARCHAR (255)  NULL  AFTER `spec28`, ADD COLUMN `spec30` VARCHAR (255)  NULL  AFTER `spec29`, ADD COLUMN `spec31` VARCHAR (255)  NULL  AFTER `spec30`, ADD COLUMN `spec32` VARCHAR (255)  NULL  AFTER `spec31`, ADD COLUMN `spec33` VARCHAR (255)  NULL  AFTER `spec32`, ADD COLUMN `spec34` VARCHAR (255)  NULL  AFTER `spec33`, ADD COLUMN `spec35` VARCHAR (255)  NULL  AFTER `spec34`, ADD COLUMN `spec36` VARCHAR (255)  NULL  AFTER `spec35`, ADD COLUMN `spec37` VARCHAR (255)  NULL  AFTER `spec36`, ADD COLUMN `spec38` VARCHAR (255)  NULL  AFTER `spec37`, ADD COLUMN `spec39` VARCHAR (255)  NULL  AFTER `spec38`, ADD COLUMN `spec40` VARCHAR (255)  NULL  AFTER `spec39`, ADD COLUMN `spec41` VARCHAR (255)  NULL  AFTER `spec40`, ADD COLUMN `spec42` VARCHAR (255)  NULL  AFTER `spec41`, ADD COLUMN `spec43` VARCHAR (255)  NULL  AFTER `spec42`, ADD COLUMN `spec44` VARCHAR (255)  NULL  AFTER `spec43`, ADD COLUMN `spec45` VARCHAR (255)  NULL  AFTER `spec44`, ADD COLUMN `spec46` VARCHAR (255)  NULL  AFTER `spec45`, ADD COLUMN `spec47` VARCHAR (255)  NULL  AFTER `spec46`, ADD COLUMN `spec48` VARCHAR (255)  NULL  AFTER `spec47`, ADD COLUMN `spec49` VARCHAR (255)  NULL  AFTER `spec48`, ADD COLUMN `spec50` VARCHAR (255)  NULL  AFTER `spec49`, ADD COLUMN `spec51` VARCHAR (255)  NULL  AFTER `spec50`, ADD COLUMN `spec52` VARCHAR (255)  NULL  AFTER `spec51`, ADD COLUMN `spec53` VARCHAR (255)  NULL  AFTER `spec52`, ADD COLUMN `spec54` VARCHAR (255)  NULL  AFTER `spec53`, ADD COLUMN `spec55` VARCHAR (255)  NULL  AFTER `spec54`, ADD COLUMN `spec56` VARCHAR (255)  NULL  AFTER `spec55`, ADD COLUMN `spec57` VARCHAR (255)  NULL  AFTER `spec56`, ADD COLUMN `spec58` VARCHAR (255)  NULL  AFTER `spec57`, ADD COLUMN `spec59` VARCHAR (255)  NULL  AFTER `spec58`, ADD COLUMN `spec60` VARCHAR (255)  NULL  AFTER `spec59`,CHANGE `spec25` `spec25` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci;
ALTER TABLE `m_vendor_api_datalog` ADD COLUMN `spec26` VARCHAR (255)  NULL  AFTER `spec25`, ADD COLUMN `spec27` VARCHAR (255)  NULL  AFTER `spec26`, ADD COLUMN `spec28` VARCHAR (255)  NULL  AFTER `spec27`, ADD COLUMN `spec29` VARCHAR (255)  NULL  AFTER `spec28`, ADD COLUMN `spec30` VARCHAR (255)  NULL  AFTER `spec29`, ADD COLUMN `spec31` VARCHAR (255)  NULL  AFTER `spec30`, ADD COLUMN `spec32` VARCHAR (255)  NULL  AFTER `spec31`, ADD COLUMN `spec33` VARCHAR (255)  NULL  AFTER `spec32`, ADD COLUMN `spec34` VARCHAR (255)  NULL  AFTER `spec33`, ADD COLUMN `spec35` VARCHAR (255)  NULL  AFTER `spec34`, ADD COLUMN `spec36` VARCHAR (255)  NULL  AFTER `spec35`, ADD COLUMN `spec37` VARCHAR (255)  NULL  AFTER `spec36`, ADD COLUMN `spec38` VARCHAR (255)  NULL  AFTER `spec37`, ADD COLUMN `spec39` VARCHAR (255)  NULL  AFTER `spec38`, ADD COLUMN `spec40` VARCHAR (255)  NULL  AFTER `spec39`, ADD COLUMN `spec41` VARCHAR (255)  NULL  AFTER `spec40`, ADD COLUMN `spec42` VARCHAR (255)  NULL  AFTER `spec41`, ADD COLUMN `spec43` VARCHAR (255)  NULL  AFTER `spec42`, ADD COLUMN `spec44` VARCHAR (255)  NULL  AFTER `spec43`, ADD COLUMN `spec45` VARCHAR (255)  NULL  AFTER `spec44`, ADD COLUMN `spec46` VARCHAR (255)  NULL  AFTER `spec45`, ADD COLUMN `spec47` VARCHAR (255)  NULL  AFTER `spec46`, ADD COLUMN `spec48` VARCHAR (255)  NULL  AFTER `spec47`, ADD COLUMN `spec49` VARCHAR (255)  NULL  AFTER `spec48`, ADD COLUMN `spec50` VARCHAR (255)  NULL  AFTER `spec49`, ADD COLUMN `spec51` VARCHAR (255)  NULL  AFTER `spec50`, ADD COLUMN `spec52` VARCHAR (255)  NULL  AFTER `spec51`, ADD COLUMN `spec53` VARCHAR (255)  NULL  AFTER `spec52`, ADD COLUMN `spec54` VARCHAR (255)  NULL  AFTER `spec53`, ADD COLUMN `spec55` VARCHAR (255)  NULL  AFTER `spec54`, ADD COLUMN `spec56` VARCHAR (255)  NULL  AFTER `spec55`, ADD COLUMN `spec57` VARCHAR (255)  NULL  AFTER `spec56`, ADD COLUMN `spec58` VARCHAR (255)  NULL  AFTER `spec57`, ADD COLUMN `spec59` VARCHAR (255)  NULL  AFTER `spec58`, ADD COLUMN `spec60` VARCHAR (255)  NULL  AFTER `spec59`,CHANGE `spec25` `spec25` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci;
ALTER TABLE `m_vendor_product_link` CHANGE `vendor_group_no` `vendor_group_no` VARBINARY (255)  NULL ;                           
#Jun_27_2014
SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id=? LIMIT 1

SELECT price_type 
FROM pnh_m_franchise_info f
JOIN pnh_member_info m ON m.franchise_id = f.franchise_id
WHERE pnh_member_id='22222222' LIMIT 1;
#=======< New >===============
SELECT i.id AS itemid,i.price,i.member_price,i.mp_frn_max_qty,i.mp_mem_max_qty,i.mp_is_offer,i.mp_offer_from,i.mp_offer_to,i.mp_offer_note
,mpl.id AS logref_id,a.username AS created_by
FROM king_dealitems i
JOIN deal_member_price_changelog mpl ON mpl.itemid=i.id
LEFT JOIN king_admin a ON a.id=mpl.created_by
WHERE i.id='3527475588'; #'1737458672' and mpl.is_active=1; #9217252483
#=======< New >===============
SELECT i.price FROM king_dealitems i WHERE i.id='1737458672' #//'9217252483';
#=======< New >===============
DESC king_dealitems;
DESC deal_member_price_changelog
SELECT `status` FROM pnh_t_receipt_info WHERE receipt_id='7664'
0:Pending
SELECT * FROM deal_member_price_changelog WHERE itemid='9217252483'
-- // get total orders for that price
SELECT o.i_price,o.is_memberprice,FROM_UNIXTIME(o.time) AS `time`,DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') AS `date`,CURDATE() AS d
FROM king_orders o 
JOIN king_transactions tr ON tr.transid=o.transid
WHERE o.itemid='3527475588' AND o.status !=3  AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = '2014-05-10' #and o.is_member_price=1 and o.member_id='' AND o.i_price='1869' # and tr.franchise_id=? AND UNIX_TIMESTAMP() BETWEEN unix_timestamp() AND 
 ORDER BY o.time DESC
`pnh_franchise_account_summary`
DESC king_orders
DESC king_dealitems
SELECT o.i_price,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_orders o 
					JOIN king_transactions tr ON tr.transid=o.transid
					WHERE o.status !=3 AND o.is_member_price=1  AND o.itemid='9217252483' AND tr.franchise_id='59' AND o.i_price='1145.00' AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = CURDATE()
					ORDER BY o.time DESC

`time`
actiontime

ALTER TABLE `king_orders` ADD COLUMN `mp_change_logid` BIGINT(11) DEFAULT 0 NULL AFTER `is_memberprice`; 
#=========================
ALTER TABLE `king_orders` ADD COLUMN `mp_logid` BIGINT(11) DEFAULT 0 NULL AFTER `is_memberprice`;
#=========================

SELECT * FROM king_dealitems WHERE member_price='0.00'

#=========================
#Jun_27_2014
ALTER TABLE `support_tickets` ADD COLUMN `req_mem_name` VARCHAR(80) NULL AFTER `related_to`, ADD COLUMN `req_mem_mobile` VARCHAR(50) NULL AFTER `req_mem_name`;
#=========================

SELECT * FROM support_tickets WHERE ticket_id='1611'
1609

#Jun_30_2014

Franchise CHANGE( pnh_users )
52=>498

DESC king_dealitems
#=========================
#Jun_30_2014
ALTER TABLE `king_dealitems` CHANGE `mp_frn_max_qty` `mp_frn_max_qty` INT(10) DEFAULT 1 NOT NULL, CHANGE `mp_mem_max_qty` `mp_mem_max_qty` INT(10) DEFAULT 1 NULL, ADD COLUMN `mp_max_allow_qty` INT(11) DEFAULT 1 NULL AFTER `mp_mem_max_qty`;
ALTER TABLE `deal_member_price_changelog` CHANGE `new_mp_frn_max_qty` `new_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 1 NOT NULL, CHANGE `old_mp_frn_max_qty` `old_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 1 NOT NULL, CHANGE `new_mp_mem_max_qty` `new_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 1 NOT NULL, CHANGE `old_mp_mem_max_qty` `old_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 1 NOT NULL, ADD COLUMN `mp_max_allow_qty` INT(10) DEFAULT 1 NULL AFTER `old_mp_mem_max_qty`, ADD COLUMN `mp_offer_shipsin` VARCHAR(50) NULL AFTER `mp_offer_note`;
#ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_offer_shipsin` VARCHAR(50) NULL AFTER `mp_offer_note`;
ALTER TABLE `m_product_info` ADD COLUMN `sync_product_src` TINYINT(1) DEFAULT 0 NULL AFTER `corr_updated_on`;
#=========================

SELECT * FROM DATE_FORMAT

SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%b-%d-%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%b-%d-%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
							,di.live,dl.publish,e.product_id,e.is_sourceable,IF(di.mp_offer_to > NOW(),1,0) AS validity
						FROM king_deals dl 
						JOIN king_dealitems di ON dl.dealid = di.dealid
						LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
						LEFT JOIN m_product_info e ON e.product_id = c.product_id      
						WHERE di.is_pnh = 1  AND dl.publish = 1 AND di.live=1
						ORDER BY di.name LIMIT 0,100;
						
# Jul_01_2014
SELECT * FROM king_dealitems WHERE pnh_id=10025775;

#=>itemid=6623348295

SELECT * FROM deal_member_price_changelog ORDER BY id DESC; WHERE itemid='6623348295'

SELECT 5000 - ROUND((5000/100)*5,2);

SELECT STR_TO_DATE(varchar_column, '%d-%m-%Y')

`m_dept_request_type_link`
`m_dept_request_types`
`pnh_franchise_requests`



SELECT i.id AS itemid,i.member_price,i.mp_max_allow_qty,i.mp_frn_max_qty,i.mp_mem_max_qty,i.mp_is_offer,DATE_FORMAT(i.mp_offer_from,'%d/%M/%Y %H:%i:%s') AS mp_offer_from,DATE_FORMAT(i.mp_offer_to,'%d/%M/%Y %H:%i:%s') AS mp_offer_to,i.mp_offer_note
	,mpl.id AS logref_id,a.username AS created_by,mpl.created_on
	FROM king_dealitems i
	LEFT JOIN deal_member_price_changelog mpl ON mpl.itemid=i.id
	LEFT JOIN king_admin a ON a.id=mpl.created_by
	WHERE i.id='3119968432' AND mpl.is_active=1;
	#3119968432  6623348295 9217252483
	#2749173323 9264594264 891544930349 3527475588 2234525859
	
	
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity,di.mp_is_offer,di.mp_offer_from,di.mp_offer_to
FROM king_orders o
JOIN king_transactions tr ON tr.transid=o.transid
JOIN king_dealitems di ON di.id=o.itemid
WHERE di.mp_is_offer = 1 AND di.id='3527475588' AND o.i_price='1854' AND FROM_UNIXTIME(tr.init) BETWEEN di.mp_offer_from AND di.mp_offer_to
#group by di.id
ORDER BY tr.init DESC
LIMIT 5

SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity,di.mp_is_offer,di.mp_offer_from,di.mp_offer_to
	,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE di.mp_is_offer = 1 AND di.id='3119968432' #AND o.i_price='1854'  AND tr.init BETWEEN UNIX_TIMESTAMP(null) AND UNIX_TIMESTAMP(null)
	#GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC;
	
	
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity,di.mp_is_offer,di.mp_offer_from,di.mp_offer_to
	,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE di.mp_is_offer = 1 #AND di.id='3527475588' AND o.i_price='1854' AND FROM_UNIXTIME(tr.init) BETWEEN di.mp_offer_from AND di.mp_offer_to
	#group by di.id
	ORDER BY tr.init DESC
	LIMIT 5;

ALTER TABLE `pnh_menu_margin_track` ADD COLUMN `default_mp_margin` DOUBLE DEFAULT 0 NULL AFTER `default_margin`; 

SELECT d.menuid,m.default_margin  AS margin,m.default_mp_margin FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid WHERE i.is_pnh=1 AND i.pnh_id='10035757'

default_margin DOUBLE NULL
default_mp_margin
#=========================
#Jul_02_2014
ALTER TABLE `pnh_menu` ADD COLUMN `default_mp_margin` DOUBLE DEFAULT 0 NULL AFTER `default_margin`;
#=========================
pnh_member_insurance_menu

#Jul_03_2014

UPDATE king_dealitems SET member_price=price,mp_frn_max_qty=1,mp_mem_max_qty=1,mp_max_allow_qty=10 WHERE member_price = '0.00';
#insert into (itemid,old_member_price,new_member_price) VALUES() on duplicate 

SELECT * FROM `deal_member_price_changelog` WHERE itemid='1268616163';
DELETE FROM deal_member_price_changelog WHERE itemid='1268616163';
#=============================================================
# set empty member price to offer price
UPDATE king_dealitems SET member_price=price,mp_frn_max_qty=1,mp_mem_max_qty=1,mp_max_allow_qty=10 WHERE member_price = '0.00';
#UPDATE king_dealitems SET member_price=price,mp_frn_max_qty=1,mp_mem_max_qty=1,mp_max_allow_qty=10 WHERE mp_max_allow_qty=1;
# Insert default log entry in log table
INSERT INTO deal_member_price_changelog (itemid,old_member_price,new_member_price,old_mp_frn_max_qty,new_mp_frn_max_qty,old_mp_mem_max_qty,new_mp_mem_max_qty,mp_max_allow_qty,is_active,created_by,created_on)
	( 
		SELECT mi.id,0,mi.member_price,0,mi.mp_frn_max_qty,0,mi.mp_mem_max_qty,mi.mp_max_allow_qty,1 AS is_active,37 AS created_by,NOW()
		FROM king_dealitems AS mi
		LEFT JOIN deal_member_price_changelog mlog ON mlog.itemid = mi.id
		WHERE mlog.itemid IS NULL # and mi.id='1268616163'
	);
#=============================================================

ON DUPLICATE KEY IGNORE 

SELECT mi.id,mi.member_price,mi.mp_frn_max_qty,mi.mp_mem_max_qty,mi.mp_max_allow_qty,1 AS is_active
		FROM king_dealitems AS mi
		JOIN deal_member_price_changelog AS mlog ON mlog.itemid = mi.id
		WHERE #mi.id!= mlog.itemid AND 
		mi.id='6623348295'
#=========================
#Jul_03_2014
ALTER TABLE `king_deals` ADD INDEX (`publish`);
ALTER TABLE `deal_member_price_changelog` ADD INDEX (`mp_max_allow_qty`), ADD INDEX (`mp_is_offer`), ADD INDEX (`mp_offer_from`), ADD INDEX (`mp_offer_to`), ADD INDEX (`mp_offer_shipsin`), ADD INDEX (`is_active`);
ALTER TABLE `king_dealitems` ADD INDEX (`member_price`), ADD INDEX (`mp_frn_max_qty`), ADD INDEX (`mp_mem_max_qty`), ADD INDEX (`mp_max_allow_qty`), ADD INDEX (`mp_is_offer`), ADD INDEX (`mp_offer_from`), ADD INDEX (`mp_offer_to`), ADD INDEX (`price`);
# reset old entries
UPDATE king_dealitems SET member_price=price,mp_frn_max_qty=1,mp_mem_max_qty=1,mp_max_allow_qty=1 WHERE member_price = '0.00';
#=========================

`snapitto_erpsndx``snapitto_erpsndx_jul_04_2014`

ALTER DATABASE snpt_db_jun_28_2014 UPGRADE DATA DIRECTORY NAME
`snapitto_erpsndx_jul_04_2014`

SELECT MD5('shivaraj') =>b343ef440fded01538c145094a034cea

SELECT VALUE FROM m_config_params WHERE NAME = 'ALLOWED_IP_ADDR'

192.168.1.70

SELECT * FROM m_config_params


SELECT member_price,mp_max_allow_qty,mp_frn_max_qty,mp_mem_max_qty,mp_is_offer FROM king_dealitems WHERE id='6623348295';

SELECT new_member_price,mp_max_allow_qty,new_mp_frn_max_qty,new_mp_mem_max_qty,mp_is_offer FROM deal_member_price_changelog WHERE itemid='6623348295' AND is_active=1;


SELECT o.itemid,o.id,o.quantity AS order_id FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
                                                WHERE o.transid='PNH88486' AND  di.pnh_id='10025775';
                                                
ALTER TABLE king_dealitems ADD COLUMN size_chart TEXT DEFAULT "" AFTER price;
#===========================
SELECT * FROM pnh_member_offers ORDER BY sno DESC LIMIT 10;

SELECT * FROM pnh_member_insurance ORDER BY sno DESC LIMIT 10;
#===========================
SELECT o.itemid,o.id AS order_id,o.quantity FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
WHERE o.transid='PNH68668' AND  di.pnh_id='10005632';

#-===Version update log
CREATE TABLE `m_apk_version_update_log` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` INT(11) DEFAULT NULL,
  `menu_id` BIGINT(11) DEFAULT NULL,
  `cat_id` BIGINT(11) DEFAULT NULL,
  `brand_id` BIGINT(11) DEFAULT NULL,
  `updated_version` VARCHAR(255) DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE king_dealitems ADD COLUMN size_chart TEXT DEFAULT "" AFTER price;

-- ======================================< TO RESET IMEI NO >=======================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 AND created_on=UNIX_TIMESTAMP(NOW()) WHERE imei_no = '911375050552334';
-- =============================================================================

#=================
SELECT  COUNT(*) AS ttl  
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid 
	LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = c.product_id
	WHERE di.is_pnh = 1 AND dl.publish = 1 AND di.live=1
#=================
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = c.product_id
		WHERE di.is_pnh = 1  AND dl.publish = 1 AND di.live=1
		 ORDER BY di.name ASC  LIMIT 0,100
) AS g WHERE 1;

SELECT  COUNT(*) AS ttl  
FROM king_deals dl 
JOIN king_dealitems di ON dl.dealid = di.dealid 
LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
LEFT JOIN m_product_info e ON e.product_id = c.product_id
WHERE di.is_pnh = 1 AND dl.publish = 1 AND di.live=1;
#=================
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = c.product_id
		
		WHERE di.is_pnh = 1  AND dl.publish = 1 AND di.live=1
		
		 ORDER BY di.name ASC  ORDER BY di.created_on DESC  LIMIT 0,100
) AS g WHERE 1;

SELECT  COUNT(*) AS ttl  
FROM king_deals dl 
JOIN king_dealitems di ON dl.dealid = di.dealid 
LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
LEFT JOIN m_product_info e ON e.product_id = c.product_id

WHERE di.is_pnh = 1 AND dl.publish = 1 AND di.live=1
AND di.id = '20003910' AND di.pnh_id='20003910' 
#=================
DESC king_orders;
DESC king_dealitems;

SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN deal_member_price_changelog mplog ON mplog.itemid = di.id
	WHERE di.mp_is_offer = 1 #AND di.id=? AND o.i_price=? #AND tr.init BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?)
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC;
#========================================
#Jul_07_2014
ALTER TABLE `king_dealitems` CHANGE mp_offer_note mp_offer_note VARCHAR(2000) DEFAULT '';
ALTER TABLE `deal_member_price_changelog` CHANGE mp_offer_note mp_offer_note VARCHAR(2000) DEFAULT '';

UPDATE deal_member_price_changelog SET mp_offer_note='' WHERE mp_offer_note IS NULL;
UPDATE king_dealitems SET mp_offer_note='' WHERE mp_offer_note IS NULL;
#=>94656 row(s)
#========================================

ALTER TABLE `deal_member_price_changelog` CHANGE `mp_offer_note` `mp_offer_note` VARCHAR(2000) CHARSET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE deal_member_price_changelog ALTER COLUMN mp_offer_note SET DEFAULT ' ';

`snapitto_erpsndx_jul_04_2014`

SELECT * FROM pnh_franchise_account_summary LIMIT 2;

#Jul_08_2014

UPDATE m_product_info SET is_sourceable = !is_sourceable WHERE product_id = '278148' LIMIT 1;

SELECT * FROM m_product_info WHERE product_id='206957';


SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = c.product_id
		
		WHERE di.is_pnh = 1  AND dl.publish = 1 #AND di.live=1
		 AND dl.menuid = '112'  AND dl.brandid = '82298176' AND di.pnh_id='1914929'
		GROUP BY di.id
		 ORDER BY di.name ASC  LIMIT 0,35
) AS g WHERE 1;
-- new --
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = c.product_id
		
		WHERE di.is_pnh = 1  AND dl.publish = 1 #AND di.live=1
		 AND dl.menuid = '112'  AND dl.brandid = '82298176' AND di.pnh_id='1914929'
		GROUP BY di.id
		 ORDER BY di.name ASC  LIMIT 0,35
) AS g WHERE 1;
-- new --

SELECT  COUNT(*) AS ttl  
FROM king_deals dl 
JOIN king_dealitems di ON dl.dealid = di.dealid 
LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
LEFT JOIN m_product_info e ON e.product_id = c.product_id
JOIN king_orders o ON o.itemid = di.id 
WHERE di.is_pnh = 1 AND dl.publish = 1 AND di.live=1
AND dl.menuid = '112'  AND dl.brandid = '82298176' 
GROUP BY di.id;

SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,SUM(o.quantity) AS ttl_qty,IFNULL(SUM(pdl.qty*o.quantity),0) AS sold_in_60,IFNULL(CEIL(SUM(pdl.qty*o.quantity)/8),0) AS exp_stk
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
		 JOIN king_orders o ON o.itemid = di.id 
		WHERE di.is_pnh = 1  AND dl.publish = 1 #AND di.live=1
		 AND dl.menuid = '112'  AND dl.brandid = '82298176'  
		GROUP BY di.id
		ORDER BY o.time DESC  LIMIT 0,53
	) AS g WHERE 1 ORDER BY g.sold_in_60 DESC;
	
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,SUM(o.quantity) AS ttl_qty,IFNULL(SUM(pdl.qty*o.quantity),0) AS sold_in_60,IFNULL(CEIL(SUM(pdl.qty*o.quantity)/8.5714285714286),0) AS exp_stk
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
		 JOIN king_orders o ON o.itemid = di.id 
		WHERE di.is_pnh = 1  AND dl.publish = 1 #AND di.live=1
		 AND dl.menuid = '112'  AND dl.brandid = '82298176'  
		GROUP BY di.id
		 ORDER BY di.name ASC  LIMIT 0,53
) AS g WHERE 1   ORDER BY g.sold_in_60 DESC;

SELECT SUM(g.ttl) AS ttl FROM (SELECT  COUNT(*) AS ttl
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid 
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
	WHERE di.is_pnh = 1 AND dl.publish = 1 AND di.live=1
	GROUP BY di.id
	) AS g;

#========================< Jul_09_2014 >==============================
SELECT COUNT(g.ttl) AS ttl FROM ( ) AS g;

	SELECT  COUNT(DISTINCT di.id) AS ttl
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid 
		LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
				#LEFT JOIN `m_product_group_deal_link` g ON g.itemid=di.id and pdl.is_active = 1 
				#LEFT JOIN `products_group_pids` q ON q.product_id=pdl.product_id
		WHERE di.is_pnh = 1 AND dl.publish = 1;
		#AND ( di.id = '1354176193' OR di.pnh_id='1914929' )
		#GROUP BY di.id;
#=>35005 rowss, 38396
DESC king_dealitems;

SELECT * FROM pnh_users;

SELECT franchise_id,login_mobile1 AS username FROM pnh_m_franchise_info f WHERE 1 AND login_mobile1='9590932088';

#Jul_09_2014
SELECT * FROM pnh_users;

SELECT * FROM pnh_m_franchise_info LIMIT 5;
#PC Provision Store

SELECT * FROM king_admin;

SELECT * FROM products_group_pids;

SELECT COUNT(*) FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,di.is_group
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
				#LEFT JOIN `m_product_group_deal_link` g ON g.itemid=di.id AND pdl.is_active = 1 
				#LEFT JOIN `products_group_pids` q ON q.product_id=pdl.product_id
		WHERE di.is_pnh = 1  AND dl.publish = 1 
		GROUP BY di.id
		 #ORDER BY di.name ASC  LIMIT 0,100
) AS g WHERE 1;
#=>43289,63712,
#=>35005

SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN deal_member_price_changelog mplog ON mplog.itemid = di.id
	WHERE di.id='7419478266' AND o.i_price='524' AND o.mp_logid = mplog.id # AND di.mp_is_offer = 1 AND tr.init BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?)
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC;
#1 7419478266,59,524.00
SELECT o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_orders o
		JOIN king_transactions tr ON tr.transid=o.transid
		WHERE o.status !=3 AND o.is_memberprice=1  AND o.itemid='7419478266' AND tr.franchise_id=59 AND o.i_price='524.00' #AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = $date
			HAVING quantity IS NOT NULL 
		ORDER BY o.time DESC;
		
		#update mp log to dealitems table
		#$this->db->update("king_dealitems",array('mp_logid'=>$mp_logid), array("id"=>$itemid)) or output_error( 'DBError:'.mysql_error() );
#================================================================
#Jul_09_2014 - mp_logid added to dealitems table, 0:mp not set,num:mp log id
ALTER TABLE `king_dealitems` ADD COLUMN `mp_logid` BIGINT(20) DEFAULT 0 NOT NULL AFTER `mp_offer_note`;
#================================================================

#==================< RESET MP TO OFFER PRICE START >==============================================
#Jul_10_2014

# update deal_member_price_changelog set is_active = 0;

# SET EMPTY member price to offer price
UPDATE king_dealitems SET member_price=price,mp_frn_max_qty=2,mp_mem_max_qty=2,mp_max_allow_qty=10 WHERE is_pnh = 1;


# INSERT default log entry in log table
INSERT INTO deal_member_price_changelog (itemid,old_member_price,new_member_price,old_mp_frn_max_qty,new_mp_frn_max_qty,old_mp_mem_max_qty,new_mp_mem_max_qty,mp_max_allow_qty,is_active,created_by,created_on)
	( 
		SELECT mi.id,0,mi.member_price,0,mi.mp_frn_max_qty,0,mi.mp_mem_max_qty,mi.mp_max_allow_qty,1 AS is_active,37 AS created_by,NOW()
		FROM king_dealitems AS mi
		LEFT JOIN deal_member_price_changelog mlog ON mlog.itemid = mi.id
		WHERE is_pnh = 1  #AND mi.id='1268616163'
	);
#==================< RESET MP TO OFFER PRICE ENDS >==============================================


SELECT transid,is_ordqty_splitd FROM king_orders WHERE transid='PNH41767' ORDER BY id DESC LIMIT 4;

SELECT m.id AS menuid,m.name AS menuname,m.default_margin,m.default_mp_margin
FROM king_dealitems i
JOIN king_deals dl ON dl.dealid = i.dealid
JOIN pnh_menu m ON m.id = dl.menuid
ORDER BY dl.sno DESC
LIMIT 10;
 
 DESC king_deals
 DESC pnh_menu
 
 
SELECT m.id AS menuid,m.name AS menuname,m.default_margin,m.default_mp_margin
FROM king_dealitems i
JOIN king_deals dl ON dl.dealid = i.dealid
JOIN pnh_menu m ON m.id = dl.menuid
WHERE i.id='9763765471';

SELECT * FROM king_dealitems WHERE id='9763765471'



SELECT c.id,pnh_id AS pid,NAME,orgprice AS mrp,price,ROUND((price/orgprice)*100) AS disc,CONCAT('http://static.snapittoday.com/items/small/',b.pic,'.jpg') AS pimg_link,a.mp_offer_from,a.mp_offer_to
	FROM king_dealitems a 
	JOIN king_deals b ON a.dealid = b.dealid
	JOIN m_apk_store_menu_link c ON c.menu_id = b.menuid  
	WHERE is_pnh=1 AND store_id!='5' AND publish = 1 AND a.mp_is_offer=1 AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)# and a.pnh_id='11314779'
	GROUP BY a.id
	LIMIT 30;
	
DESC deal_member_price_changelog;

#Jul_11_2014

SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id='498' LIMIT 1;

SELECT * FROM king_dealitems WHERE created_on IS NULL;

DESC king_dealitems;

DESC support_tickets;

SELECT u.name AS USER,t.*,a.name AS assignedto 
	FROM support_tickets t 
	LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
	LEFT OUTER JOIN king_users u ON u.userid=t.user_id WHERE 1 ORDER BY t.updated_on DESC, t.created_on DESC #limit 30;
	
SELECT i.price,i.pnh_id,i.name FROM king_dealitems i LIMIT 2;

#======================================
#Jul_11_2014
INSERT INTO `m_dept_request_types` (`id`, `name`) VALUES ('7', 'Sourcing');
INSERT INTO `m_dept_request_type_link` (`dept_id`, `type_id`, `created_on`, `created_by`) VALUES ('7', '7', '2014-07-11 19:27:13', '1');
#======================================

SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name AS request_name FROM m_dept_request_type_link rlink
											LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
											WHERE rlink.is_active=1 AND rlink.type_id = '7'
											
#=> deptids: 7

SELECT emp.employee_id,emp.name,emp.email,emp.contact_no
			FROM m_departments dt
			JOIN m_employee_dept_link edl ON edl.dept_id = dt.id
			JOIN m_employee_info emp ON emp.employee_id = edl.employee_id
			WHERE dt.id IN ('3') 
			#AND job_title=9 
			AND emp.email !='';

SELECT rnk.dept_id,d.name,GROUP_CONCAT(rnk.type_id,':',req.name) AS type_ids FROM `m_departments` d
LEFT JOIN `m_dept_request_type_link` rnk ON rnk.dept_id=d.id
LEFT JOIN `m_dept_request_types` req ON req.id=rnk.type_id
GROUP BY d.id;

#================
#Jul_12_2014

SELECT * FROM m_dept_request_type_link WHERE dept_id=? AND type_id=?

DESC m_dept_request_type_link;

SELECT u.name AS USER,t.*,a.name AS assignedto 
	FROM support_tickets t 
	LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
	LEFT OUTER JOIN king_users u ON u.userid=t.user_id WHERE 1   LIMIT 30 ;
	
SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %H:%i:%s') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %H:%i:%s') AS updated_on
	,u.name AS `user`,a.name AS assignedto 
	FROM support_tickets t 
	LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
	LEFT OUTER JOIN king_users u ON u.userid=t.user_id WHERE 1;
	
#Jul_14_2014

SELECT t.franchise_id,f.franchise_name FROM support_tickets t
LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
WHERE t.franchise_id IS NOT NULL
ORDER BY f.franchise_name ASC;

SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %H:%i:%s') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %H:%i:%s') AS updated_on
				,u.name AS `user`,a.name AS assignedto 
				,tmsg.msg,tmsg.from_customer=1
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
				LEFT OUTER JOIN king_users u ON u.userid=t.user_id 
				JOIN support_tickets_msg tmsg ON tmsg.ticket_id = t.ticket_id
				WHERE 1 
				# AND t.franchise_id='498' 
				AND from_customer=1
				#AND t.status!=0   
				LIMIT 30;
				
SELECT * FROM support_tickets_msg WHERE ticket_id='6167148610';

#Jul_15_2014

SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on
				,u.name AS `user`,a.name AS assignedto 
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
				LEFT OUTER JOIN king_users u ON u.userid=t.user_id 
				WHERE 1  AND t.created_on BETWEEN '2014-07-01' AND '2014-07-16';
				
SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on
				,u.name AS `user`,a.name AS assignedto 
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
				LEFT OUTER JOIN king_users u ON u.userid=t.user_id 
				WHERE 1  AND t.transid LIKE '%SN%'
				 LIMIT 0,30;
				 
SELECT COUNT(1) AS l FROM support_tickets t
				WHERE 1  AND t.transid NOT LIKE '%SN%'  
				 LIMIT 0,30;
#=========
SELECT * FROM support_tickets t
WHERE 1  AND t.transid NOT LIKE '%SN%'  
#=========

#=====================================
#Jul_15_2014 - To filter the tickets by Web or APP
ALTER TABLE `support_tickets` ADD COLUMN `from_app` TINYINT(11) DEFAULT 0 NOT NULL COMMENT '0:from web,1:from api/app' AFTER `req_mem_mobile`;
ALTER TABLE `support_tickets` CHANGE `franchise_id` `franchise_id` BIGINT(20) DEFAULT 0 NULL;
UPDATE support_tickets SET franchise_id=0 WHERE franchise_id IS NULL;
UPDATE support_tickets SET from_app = 1 WHERE franchise_id != 0;
#=====================================

#==============================================Consolidated payment==============================================================#
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `price_type`;
ALTER TABLE `king_orders` ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `mp_logid`; 
ALTER TABLE `t_invoice_credit_notes` ADD COLUMN `payment_id` BIGINT(11) DEFAULT 0 NULL AFTER `ref_id`; 
UPDATE t_invoice_credit_notes SET payment_id=1
CREATE TABLE `pnh_payment_info` (
  `payment_id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `bank_id` BIGINT(11) DEFAULT NULL,
  `instrument_no` BIGINT(11) DEFAULT NULL,
  `instrument_date` DATE DEFAULT NULL,
  `drawn_inname` VARCHAR(255) DEFAULT NULL,
  `created_by` BIGINT(20) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `cleared_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
);
CREATE TABLE `creditnote_payment_linkinfo`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `credit_note_id` BIGINT(11), `payment_id` BIGINT(11), `is_active` TINYINT(1) DEFAULT 1, `created_on` DATETIME, `created_by` TINYINT(1), `modified_on` DATETIME, `modified_by` TINYINT(1), PRIMARY KEY (`id`) ); 
CREATE TABLE `pnh_payment_flag_changelog` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `is_consolidated_payment` TINYINT(1) DEFAULT NULL,
  `reason` VARCHAR(255) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE `pnh_payment_info` ADD COLUMN `amount` DOUBLE NULL AFTER `franchise_id`;

UPDATE support_tickets SET from_app = 1 WHERE franchise_id != 0;

SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on
				,u.name AS `user`,a.name AS assignedto,f.franchise_name
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
				LEFT OUTER JOIN king_users u ON u.userid=t.user_id
				LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id
				WHERE 1  
				 LIMIT 0,30;

#Jul_16_2014

SELECT * FROM pnh_member_offers ofr 
LEFT JOIN pnh_member_insurance ins ON ins.sno = ofr.insurance_id 
LEFT JOIN king_orders o ON o.id = ofr.order_id WHERE ofr.sno='2002';

ALTER TABLE `pnh_payment_info` ADD COLUMN `amount` DOUBLE NULL AFTER `franchise_id`;


SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name AS request_name FROM m_dept_request_type_link rlink
											LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
											WHERE rlink.is_active=1 AND 
											rlink.type_id = '1'
											HAVING dept_ids IS NOT NULL;
											

											
ALTER TABLE `king_user_orders` ADD COLUMN `is_memberprice` TINYINT(1) DEFAULT 0 NULL AFTER `loyality_point_value`, ADD COLUMN `other_price` DOUBLE DEFAULT 0 NULL AFTER `is_memberprice`;

SELECT * FROM king_user_orders;

#Jul_19_2014


SELECT COUNT(fee.id) AS l FROM pnh_member_fee fee
JOIN king_orders o ON o.transid = fee.transid AND o.status != 3
WHERE fee.member_id='22034342'; #fee.transid='PNH11667';

SELECT COUNT(fee.id) AS l FROM pnh_member_fee fee
JOIN king_orders o ON o.transid = fee.transid AND o.status != 3
JOIN pnh_member_info m ON m.pnh_member_id = 
WHERE fee.member_id='22034340';

SELECT COUNT(*) AS is_member_fee_paid FROM pnh_member_info mi
JOIN pnh_member_fee fee ON fee.member_id = mi.pnh_member_id AND fee.status=1
JOIN king_orders o ON o.transid = fee.transid AND o.status != 3
WHERE mi.created_on > DATE('2014-01-01') AND fee.member_id='21111112';


SELECT #c.id,
	pnh_id AS pid,NAME,orgprice AS mrp,price,ROUND((price/orgprice)*100) AS disc,CONCAT('http://static.snapittoday.com/items/small/',b.pic,'.jpg') AS pimg_link,a.mp_offer_to
	FROM king_dealitems a 
	JOIN king_deals b ON a.dealid = b.dealid
	#JOIN m_apk_store_menu_link c ON c.menu_id = b.menuid  
	WHERE is_pnh=1 #AND store_id=? 
#	AND publish = 1 
	AND a.mp_is_offer=1  AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)
	GROUP BY a.id;
	
SELECT pnh_member_fee FROM king_orders WHERE transid='PNH27836';
SELECT pnh_member_fee FROM king_transactions WHERE transid='PNH27836';
SELECT * FROM pnh_member_fee WHERE transid='PNH27836'

SELECT * FROM pnh_member_fee ORDER BY id DESC LIMIT 10;

#Jul_21_2014

SELECT * FROM  (
SELECT DISTINCT m.menuid,m.fid,c.id,c.name 
FROM `pnh_franchise_menu_link` m 

JOIN `king_deals` d ON d.menuid=m.menuid
JOIN king_dealitems i ON i.dealid=d.dealid 
JOIN king_categories c ON c.id=d.catid
# JOIN king_orders o ON o.itemid=i.id 
# JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1 

WHERE m.status=1 AND m.fid='498' # AND t.is_pnh=1 
#GROUP BY d.catid 
#ORDER BY COUNT(o.id) DESC 
LIMIT 20) AS g;
==================================
DESC king_transactions
	
SELECT m.menuid,m.fid,c.id,c.name
FROM pnh_franchise_menu_link m
JOIN king_categories c ON c.id=d.catid
WHERE m.fid='498';
===========================
DESC king_deals
=====================
SELECT DISTINCT m.menuid,m.fid,c.id,c.name
FROM king_deals d
JOIN `pnh_franchise_menu_link` m  ON d.menuid = d.menuid
JOIN king_categories c ON c.id=d.catid
LIMIT 3;

SELECT m.menuid,m.fid,c.id,c.name 
FROM `pnh_franchise_menu_link`m 
JOIN `king_deals`d ON d.menuid=m.menuid 
JOIN king_categories c ON c.id=d.catid  
WHERE m.status=1 AND fid='498' AND c.name LIKE 'F%' 
GROUP BY d.catid ORDER BY c.name ASC;

22034344

SELECT COUNT(o.transid) AS l
	FROM king_orders o
	JOIN pnh_member_info mi ON mi.user_id = o.userid
	WHERE o.userid='' OR mi.pnh_member_id='22034344'
	AND o.status NOT IN (3) AND mi.created_on > DATE('2014-01-01')
	HAVING SUM(o.i_price*o.quantity) >= '500';
	
	SELECT * FROM pnh_member_info
	
SELECT shipsin FROM king_dealitems WHERE shipsin IS NULL OR shipsin='';

SELECT mp_offer_shipsin FROM deal_member_price_changelog WHERE mp_offer_shipsin IS NULL OR mp_offer_shipsin='';
#=====================================================
# Set mp log table shipsin value '24-48 Hrs' for all null of empty
UPDATE deal_member_price_changelog 
SET mp_offer_shipsin='24-48 Hrs'
WHERE mp_offer_shipsin IS NULL OR mp_offer_shipsin='';

#Set dealitems shipsin value to '24-48 Hrs' for all null of empty
UPDATE king_dealitems
SET shipsin='24-48 Hrs'
WHERE mp_offer_shipsin IS NULL OR mp_offer_shipsin='';

# Change ships in 24 to 48 Hrs TO 24-48 Hrs
UPDATE king_dealitems
SET shipsin='24-48 Hrs'
WHERE shipsin='24 to 48 Hrs';
#=====================================================

SELECT COUNT(*) AS is_rechrge_gv 
FROM pnh_member_offers 
WHERE offer_type=1 AND member_id='22034344' 
HAVING is_rechrge_gv NOT NULL;


SELECT l.product_id,p.product_name,l.qty,p.is_sourceable AS src,0 AS deal_type,b.is_group 
FROM m_product_deal_link l 
JOIN m_product_info p ON p.product_id=l.product_id 
JOIN king_dealitems b ON b.id = l.itemid 
WHERE l.is_active = 1 AND l.itemid='3918737266'# and is_group = 0;

SELECT * FROM pnh_member_insurance WHERE order_id='9748473641'; 9748473641,7993547131,1373632869
SELECT * FROM pnh_member_insurance WHERE order_id='7993547131';
SELECT * FROM pnh_member_insurance WHERE order_id='1373632869';


SELECT * FROM (
                                    SELECT DISTINCT 
					
					trr.territory_name,tw.town_name,f.franchise_id,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.sno AS insurance_id,mo.`order_id`,i.`invoice_no`,DATE_FORMAT(FROM_UNIXTIME(i.`createdon`),'%d-%b-%Y') AS inv_date,mi.`mem_receipt_no`,DATE_FORMAT(mi.`mem_receipt_date`,'%d-%b-%Y') AS mem_receipt_date,IFNULL(mi.first_name,minfo.first_name) AS first_name,mi.`proof_address`,mi.`city`,mi.`pincode`,mi.`mob_no`,di.`name` AS product_name,b.name AS brand
                                        ,c.name AS catname,di.`name` AS product_make,'' AS other,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no,'' AS serial_no,mi.`mem_receipt_amount` AS retailer_invoice_value,mo.`transid_ref` AS transid,ptype.name AS proof_given,mi.`proof_id` AS proof_details
                                       ,IF(rcon.unreconciled = 0,0,1) AS pending_payment,mo.delivery_status,mo.feedback_status,mo.feedback_value,mo.process_status,mi.status_ship
                                       
                                        FROM pnh_member_offers mo
                                        JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                        JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                        JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id
                                        JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                        JOIN king_orders o ON o.id = mi.`order_id`
                                        JOIN king_dealitems di ON di.id = o.itemid
                                        LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                        LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                        LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
										LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                    JOIN king_deals d ON d.dealid=di.dealid
                                    JOIN king_brands b ON b.id = d.brandid
                                    JOIN king_categories c ON c.id = d.catid
                                    LEFT JOIN insurance_m_types AS ptype ON ptype.id= mi.`proof_type`
									JOIN pnh_towns tw ON tw.id = f.town_id  
									JOIN pnh_m_territory_info trr ON trr.id = tw.territory_id 
                                        WHERE mo.offer_type IN (0,2)$cond
                                        GROUP BY mo.sno ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        
                                        
SELECT  COUNT(DISTINCT di.id) AS ttl
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid 
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = pdl.product_id

	WHERE di.is_pnh = 1 AND dl.publish = 1
	AND di.mp_is_offer=1 AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(di.mp_offer_from) AND UNIX_TIMESTAMP(di.mp_offer_to);
	
#<!---Modification Done by Roopashree<roopashree@storeking.in> Modified Date 19/july/2014------>
#=============================Franchise Asset/Device Information====================================================#
CREATE TABLE `m_asset_info`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) );
CREATE TABLE `m_accessory_info`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) );
CREATE TABLE `m_franchise_asset_link`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `franchise_id` BIGINT(20), `asset_id` BIGINT(20), `accessory_id` BIGINT(20), `is_active` TINYINT(1) DEFAULT 1, `created_on` BIGINT(20), `created_by` INT(11), `modified_on` BIGINT(20), `modified_by` INT(11), PRIMARY KEY (`id`) );
CREATE TABLE `m_franchise_store_link`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `franchise_id` BIGINT(20), `store_id` INT(11), `is_active` TINYINT(1), `created_on` BIGINT(20), `created_by` INT(11), `modified_on` BIGINT(20), `modified_by` INT(11), PRIMARY KEY (`id`) );
#=============================Franchise Store type Info====================================================#

SELECT * FROM king_categories;

DESC `pnh_menu`
DESC king_deals;

#Jul_21_2014 - To allot towns for vendor-by Suresh
CREATE TABLE `m_vendor_town_link` (                       
                      `id` INT(11) NOT NULL AUTO_INCREMENT,                   
                      `brand_id` INT(11) DEFAULT NULL,                        
                      `vendor_id` INT(11) DEFAULT NULL,                       
                      `town_id` INT(11) DEFAULT NULL,                         
                      `is_active` TINYINT(1) DEFAULT '1',                     
                      `created_on` BIGINT(20) DEFAULT NULL,                   
                      `modified_on` BIGINT(20) DEFAULT NULL,                  
                      `modified_by` INT(11) DEFAULT '0',                      
                      PRIMARY KEY (`id`),                                     
                      UNIQUE KEY `id` (`id`)                                  
                    );
#==========================================                    
SELECT id,mp_is_offer,mp_offer_from,mp_offer_to,is_group,i.name FROM king_dealitems i WHERE mp_is_offer IS NOT NULL AND pnh_id='19996856';//20003689

SELECT mp_is_offer,mp_offer_from,mp_offer_to,is_active FROM deal_member_price_changelog WHERE itemid='1874667575';

SELECT id,mp_is_offer,mp_offer_from,mp_offer_to,is_group,i.name FROM king_dealitems i WHERE pnh_id='19996856';

SELECT id FROM king_dealitems WHERE is_group=0;
#==========================================      

SELECT l.product_id,p.product_name,l.qty,p.is_sourceable AS src,1 AS deal_type FROM m_product_deal_link l 
JOIN m_product_info p ON p.product_id=l.product_id 
JOIN king_dealitems b ON b.id = l.itemid 
WHERE l.is_active = 1 AND l.itemid='654346495938' AND is_group = 1;

#Jul_22_2014
SELECT DISTINCT c.id,c.name 
	FROM m_product_info a 
	JOIN king_categories c ON c.id = a.product_cat_id 
		#JOIN king_deals dl on 
		#JOIN king_brands b ON b.id = dl.brandid
	WHERE brand_id = '55715294' AND menuid='104'
	ORDER BY c.name ASC
	
DESC m_product_info
DESC king_categories

#===========================
SELECT DISTINCT c.id,c.name 
	FROM king_deals dl 
	JOIN king_categories c ON c.id = dl.catid 
	JOIN king_brands b ON b.id = dl.brandid
	WHERE dl.brandid = '55715294' AND dl.menuid='104'
	ORDER BY c.name ASC
	
DESC king_deals;

#=============================
SELECT COUNT(*) AS is_rechrge_gv FROM pnh_member_offers mo 
	WHERE mo.offer_type=1 AND mo.offer_type NOT IN (0,2,3) AND mo.member_id='22025272' 
	HAVING is_rechrge_gv IS NOT NULL;
#=============================
	SELECT * FROM pnh_member_offers mo WHERE mo.offer_type=1 AND mo.offer_type NOT IN (0,2,3)
	
#=============================
#JUl_23_2014
CREATE TABLE `m_master_franchise_info` (  `id` INT (11) NOT NULL AUTO_INCREMENT , `name` VARCHAR (255) , `address` VARCHAR (2024) , `city` VARCHAR (255) , `state` VARCHAR (255) , `town_id` INT (11) DEFAULT '0', `terr_id` INT (11) DEFAULT '0', `created_on` DATETIME , `modified_on` DATETIME , `created_by` INT (11) DEFAULT '0', `modified_by` INT (11) DEFAULT '0', PRIMARY KEY ( `id`)) ;
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `master_franchise_id` INT (11) DEFAULT '0' NULL  AFTER `pnh_franchise_id`;
ALTER TABLE `pnh_users` CHANGE `franchise_id` `reference_id` BIGINT(20) NULL COMMENT 'franchise_id,m_fid';
#=============================


SELECT  COUNT(di.id) AS ttl
	,IFNULL(SUM( IF(dl.publish=1,1,0) ),0) AS ttl_published
	,IFNULL(SUM( IF(dl.publish=0,1,0) ),0) AS ttl_unpublished
	,GROUP_CONCAT(DISTINCT dl.menuid,':',m.name) AS menu_info
	
FROM king_deals dl 
JOIN king_dealitems di ON dl.dealid = di.dealid 
JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
LEFT JOIN pnh_menu m ON m.id = dl.menuid

LEFT JOIN king_orders o ON o.itemid = di.id 
WHERE di.is_pnh = 1;

#Jul_24_2014
ALTER TABLE `t_imei_no` ADD COLUMN `reserved_batch_rowid` BIGINT(20) DEFAULT 0 NULL AFTER `modified_on`; 

SELECT live FROM king_dealitems WHERE id='2287852534'
DESC m_product_info

SELECT remarks FROM m_product_info WHERE remarks!='' AND remarks IS NOT NULL;

SELECT * FROM products_src_changelog;

#=================================================
#Jul_24_2014
ALTER TABLE `products_src_changelog` ADD COLUMN `remarks` VARCHAR(200) NULL AFTER `is_sourceable`;
#=================================================
SELECT * FROM products_src_changelog WHERE product_id='5341';

#Jul_26_2014

DROP TABLE m_asset_info;
DROP TABLE m_asset_accessory_info;
DROP TABLE m_franchise_asset_link;
DROP TABLE m_franchise_store_link;

/*[5:55:32 PM][63 ms]*/ CREATE TABLE `m_asset_info` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `asset_name` VARCHAR(255) DEFAULT NULL,
  `brand_id` BIGINT(20) DEFAULT NULL,
  `category_id` BIGINT(20) DEFAULT NULL,
  `created_date` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

/*[5:57:22 PM][28 ms]*/ CREATE TABLE `m_asset_accessory_info` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` BIGINT(11) DEFAULT NULL,
  `accessory_name` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT '1',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

/*[6:00:23 PM][35 ms]*/ CREATE TABLE `m_franchise_asset_link` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(20) DEFAULT NULL,
  `asset_id` BIGINT(20) DEFAULT NULL,
  `accessory_id` BIGINT(20) DEFAULT NULL,
  `asset_serialno` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT '1',
  `created_on` BIGINT(20) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` BIGINT(20) DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `m_franchise_store_link` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(20) DEFAULT NULL,
  `store_id` INT(11) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT NULL,
  `created_on` BIGINT(20) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` BIGINT(20) DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -60 DAY)),1,0)) AS soldin60 ,
SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -30 DAY)),1,0)) AS soldin30,
SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -15 DAY)),1,0)) AS soldin15,
SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)),1,0)) AS soldin7
=>
 AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL -60 DAY))
 
 
 SELECT  COUNT(di.id) AS ttl
	,IFNULL(SUM( IF(dl.publish=1,1,0) ),0) AS ttl_published
	,IFNULL(SUM( IF(dl.publish=0,1,0) ),0) AS ttl_unpublished
	,GROUP_CONCAT(DISTINCT dl.menuid,':',m.name) AS menu_info
	 ,GROUP_CONCAT(DISTINCT dl.brandid,':',bd.name) AS brand_info 
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid 
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
	JOIN pnh_menu m ON m.id = dl.menuid
	LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY))
	JOIN king_brands bd ON bd.id = dl.brandid 

	WHERE di.is_pnh = 1  AND dl.menuid = '104';
	
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,di.is_group
		,dl.catid,dl.brandid,ct.name AS catname,bd.name AS brandname
		,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -60 DAY)),1,0)) AS soldin60
		,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -30 DAY)),1,0)) AS soldin30
		,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -15 DAY)),1,0)) AS soldin15
		,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)),1,0)) AS soldin7
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
		JOIN king_brands bd ON bd.id = dl.brandid
		JOIN king_categories ct ON ct.id = dl.catid
		JOIN pnh_menu m ON m.id = dl.menuid
		LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY))
		
		WHERE di.is_pnh = 1
		 AND dl.menuid = ?  AND dl.publish = 1
		GROUP BY di.id
		 ORDER BY di.name ASC  LIMIT 0,100
) AS g WHERE 1   ORDER BY g.soldin60 DESC,g.soldin30 DESC,g.soldin15 DESC,g.soldin7 DESC;


SELECT DISTINCT c.id,c.name 
	FROM king_deals dl 
	JOIN king_categories c ON c.id = dl.catid 
	JOIN king_brands b ON b.id = dl.brandid
	WHERE 1 #$cond
	ORDER BY c.name ASC;
	
	=>774, 
	
	
SELECT DISTINCT dl.brandid,b.name
	FROM king_deals dl
	JOIN king_brands b ON b.id = dl.brandid
	WHERE dl.menuid='' ORDER BY b.name ASC;

#==========	
SELECT  COUNT( di.id) AS ttl
	,IFNULL(SUM( IF(dl.publish=1,1,0) ),0) AS ttl_published
	,IFNULL(SUM( IF(dl.publish=0,1,0) ),0) AS ttl_unpublished
	#,group_concat(distinct dl.menuid,':',m.name) as menu_info
	#,group_concat(DISTINCT dl.brandid,':',bd.name) as brand_info 
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid 
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
	JOIN pnh_menu m ON m.id = dl.menuid
	LEFT JOIN king_orders o ON o.itemid = di.id #AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY))
	JOIN king_brands bd ON bd.id = dl.brandid 

	WHERE di.is_pnh = 1  AND dl.menuid = '104'  AND ( di.id = '1759118' OR di.pnh_id='1759118' )  
	GROUP BY di.id

,IFNULL(SUM( IF(dl.publish=1,1,0) ),0) AS ttl_published
,IFNULL(SUM( IF(dl.publish=0,1,0) ),0) AS ttl_unpublished
,GROUP_CONCAT(DISTINCT dl.menuid,':',m.name) AS menu_info;

SELECT DISTINCT c.id,c.name 
	FROM king_deals dl 
	JOIN king_categories c ON c.id = dl.catid 
	JOIN king_brands b ON b.id = dl.brandid
	WHERE 1  AND dl.menuid = '112'
	ORDER BY c.name ASC
	
SELECT DISTINCT c.id,c.name 
	FROM king_deals dl 
	JOIN king_categories c ON c.id = dl.catid 
	JOIN king_brands b ON b.id = dl.brandid
	WHERE 1  AND dl.brandid = '100'
	ORDER BY c.name ASC;
	
SELECT DISTINCT c.id,c.name 
FROM king_deals dl 
JOIN king_categories c ON c.id = dl.catid 
JOIN king_brands b ON b.id = dl.brandid
WHERE 1  AND dl.brandid = '40218678' AND dl.menuid = '105'
ORDER BY c.name ASC;


SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
	,di.live,dl.publish,e.product_id,e.is_sourceable
	,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,di.is_group
	,dl.catid,dl.brandid,ct.name AS catname,bd.name AS brandname,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -60 DAY)),1,0)) AS soldin60
	,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -30 DAY)),1,0)) AS soldin30
	,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -15 DAY)),1,0)) AS soldin15
	,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)),1,0)) AS soldin7
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
	LEFT JOIN m_product_info e ON e.product_id = pdl.product_id
	JOIN king_brands bd ON bd.id = dl.brandid
	JOIN king_categories ct ON ct.id = dl.catid
	JOIN pnh_menu m ON m.id = dl.menuid
	LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY))
	
	WHERE di.is_pnh = 1
	AND dl.menuid = '104'  AND dl.publish = 1 
	GROUP BY di.id
	ORDER BY di.name ASC  LIMIT 0,100
) AS g WHERE 1  ORDER BY g.soldin60 DESC,g.soldin30 DESC,g.soldin15 DESC,g.soldin7 DESC;


SELECT SUM(ttl) AS ttl,SUM(ttl_published) AS ttl_published,SUM(ttl_unpublished) AS ttl_unpublished
		,GROUP_CONCAT(DISTINCT menu_info ORDER BY menu_info) AS menu_info
		,GROUP_CONCAT(DISTINCT brand_info ORDER BY brand_info) AS brand_info ,GROUP_CONCAT(DISTINCT cat_info ORDER BY cat_info) AS cat_info 
	FROM (
	SELECT publish,COUNT(*) AS ttl,IF(publish,COUNT(*),0) AS ttl_published
					,IF(!publish,COUNT(*),0) AS ttl_unpublished
					,GROUP_CONCAT(DISTINCT menuid,':',menu) AS menu_info
					,GROUP_CONCAT(DISTINCT brandid,':',brand) AS brand_info
					,GROUP_CONCAT(DISTINCT catid,':',cat) AS cat_info  
				FROM (
					SELECT  di.id,COUNT(*) AS t,dl.menuid,m.name AS menu,dl.brandid,bd.name AS brand,dl.catid,ct.name AS cat,publish
					FROM king_deals dl 
					JOIN king_dealitems di ON dl.dealid = di.dealid 
					JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
					JOIN m_product_info e ON e.product_id = pdl.product_id
					JOIN pnh_menu m ON m.id = dl.menuid
					 JOIN king_brands bd ON bd.id = dl.brandid  JOIN king_categories ct ON ct.id = dl.catid 
					 LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY)) 
					WHERE di.is_pnh = 1   AND dl.menuid = '104' 
					GROUP BY di.id
					ORDER BY menu,brand,cat
				) AS g 
	GROUP BY publish  ) AS h;
#2
SELECT SUM(ttl) AS ttl,SUM(ttl_published) AS ttl_published,SUM(ttl_unpublished) AS ttl_unpublished,
	GROUP_CONCAT(DISTINCT menu_info ORDER BY menu_info) AS menu_info
	,GROUP_CONCAT(DISTINCT brand_info ORDER BY brand_info) AS brand_info ,GROUP_CONCAT(DISTINCT cat_info ORDER BY cat_info) AS cat_info 
FROM (
SELECT publish,COUNT(*) AS ttl,IF(publish,COUNT(*),0) AS ttl_published
				,IF(!publish,COUNT(*),0) AS ttl_unpublished
				,GROUP_CONCAT(DISTINCT menuid,':',menu) AS menu_info
				,GROUP_CONCAT(DISTINCT brandid,':',brand) AS brand_info
				,GROUP_CONCAT(DISTINCT catid,':',cat) AS cat_info  
			FROM (
				SELECT  di.id,COUNT(*) AS t,dl.menuid,m.name AS menu,dl.brandid,bd.name AS brand,dl.catid,ct.name AS cat,publish
				FROM king_deals dl 
				JOIN king_dealitems di ON dl.dealid = di.dealid 
				JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
				JOIN m_product_info e ON e.product_id = pdl.product_id
				JOIN pnh_menu m ON m.id = dl.menuid
				 JOIN king_brands bd ON bd.id = dl.brandid  JOIN king_categories ct ON ct.id = dl.catid 
				 LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY)) 
				WHERE di.is_pnh = 1   AND dl.menuid = '104' 
				GROUP BY di.id
				ORDER BY menu,brand,cat
			) AS g 
GROUP BY publish  ) AS h;
#3 
SELECT * FROM (
	SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
		,di.live,dl.publish,e.product_id,e.is_sourceable
		,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity,di.is_group
		,dl.catid,dl.brandid,ct.name AS catname,bd.name AS brandname ,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -60 DAY)),1,0)) AS soldin60
	   ,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -30 DAY)),1,0)) AS soldin30
 	  ,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -15 DAY)),1,0)) AS soldin15
 	  ,SUM(IF(o.time > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)),1,0)) AS soldin7
		FROM king_deals dl 
		JOIN king_dealitems di ON dl.dealid = di.dealid
		JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
		JOIN m_product_info e ON e.product_id = pdl.product_id
		JOIN king_brands bd ON bd.id = dl.brandid
		JOIN king_categories ct ON ct.id = dl.catid
		JOIN pnh_menu m ON m.id = dl.menuid
		LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY)) 
		WHERE di.is_pnh = 1 AND dl.publish = 1
		 AND dl.menuid = '104'
		GROUP BY di.id
		 ORDER BY di.name ASC  #LIMIT 0,100
			
) AS g WHERE 1  ORDER BY g.soldin60 DESC,g.soldin30 DESC,g.soldin15 DESC,g.soldin7 DESC;

#4
SELECT SUM(ttl) AS total,SUM(ttl_published) AS ttl_pub,SUM(ttl_unpublished) AS ttl_unpub,
	GROUP_CONCAT(DISTINCT menu_info ORDER BY menu_info) AS menu_info,
	GROUP_CONCAT(DISTINCT brand_info ORDER BY brand_info) AS brand_info,
	GROUP_CONCAT(DISTINCT cat_info ORDER BY cat_info) AS cat_info
FROM (
SELECT publish,COUNT(*) AS ttl,IF(publish,COUNT(*),0) AS ttl_published
				,IF(!publish,COUNT(*),0) AS ttl_unpublished
				,GROUP_CONCAT(DISTINCT menuid,':',menu) AS menu_info
				,GROUP_CONCAT(DISTINCT brandid,':',brand) AS brand_info
				,GROUP_CONCAT(DISTINCT catid,':',cat) AS cat_info  
			FROM (
				SELECT  di.id,COUNT(*) AS t,dl.menuid,m.name AS menu,dl.brandid,bd.name AS brand,dl.catid,ct.name AS cat,publish
				FROM king_deals dl 
				JOIN king_dealitems di ON dl.dealid = di.dealid 
				JOIN m_product_deal_link pdl ON pdl.itemid = di.id AND pdl.is_active = 1
				JOIN m_product_info e ON e.product_id = pdl.product_id
				JOIN pnh_menu m ON m.id = dl.menuid
				JOIN king_brands bd ON bd.id = dl.brandid  
				JOIN king_categories ct ON ct.id = dl.catid 
				LEFT JOIN king_orders o ON o.itemid = di.id AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY)) 
				WHERE di.is_pnh = 1  AND dl.menuid = '104'
				GROUP BY di.id
				ORDER BY menu,brand,cat
			) AS g 
GROUP BY publish  ) AS h


#select (1846-(1655+221) )

#===============================================

SELECT * FROM support_tickets WHERE ticket_id='1716';

#Jul_29_2014

SELECT * 
FROM support_tickets t
LEFT JOIN m_dept_request_types rt ON rt.id=t.related_to
WHERE ticket_id='1717';

SELECT * FROM support_tickets_msg;

REPAIR DATABASE `snapitto_erpsndx_jul_04_2014`;
#================================================================
ALTER TABLE king_brands ADD INDEX (`name`);
ALTER TABLE `t_imei_no` ADD COLUMN `reserved_on` BIGINT(20) DEFAULT 0 NULL AFTER `reserved_batch_rowid`;
ALTER TABLE `m_vendor_product_images` ADD INDEX (`vendor_id`), ADD INDEX (`item_id`);
#================================================================

#SEARCH QUERY

SELECT i.pnh_id AS pid,i.name,i.size_chart,i.id AS itemid,i.gender_attr,i.member_price,i.orgprice AS mrp,i.price AS price,i.is_combo,i.shipsin AS ships_in,i.sno AS d_sno,i.store_price,(IF(0,i.price,i.member_price))/i.orgprice AS p_discount 
	,m.name AS menu,m.id AS menu_id
	,c.name AS category,c.type AS main_category_id
	,d.catid AS category_id,d.brandid AS brand_id,d.keywords
	,mc.name AS main_category
	,b.name AS brand
	,d.description
	,COUNT(o.id) AS total_orders
	,(IF(FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('hd',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('HD Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('HD MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) 
	+ IF(FIND_IN_SET('hd mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0)
	)  AS rel_ttl
	,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url
	,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url
		FROM king_deals d
		JOIN king_dealitems i ON i.dealid=d.dealid
		JOIN king_brands b ON b.id=d.brandid
		JOIN king_categories c ON c.id=d.catid
		LEFT OUTER JOIN king_categories mc ON mc.id=c.type
		
		LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid
		LEFT JOIN m_vendor_product_images v ON v.item_id=i.id
		
		LEFT JOIN king_orders o ON o.itemid = i.id
		WHERE  i.is_pnh=1  AND d.publish = 1 
		 AND m.id IN ("112","100","104","122","120","136","102","117","133","111","127","115","101","134","128","107","106","108","116","126","118","123","114","119","121","129","130","131","137","105","103","113","132","135","109")  AND ( FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('hd',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('hd mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  ) 
		GROUP BY i.id
		ORDER BY rel_ttl DESC,p_discount ASC, `name` ASC 
		
		LIMIT 0,10;
		
#=>11sec => 8.134=>2sec =>2.32 sec

#if i include image => 11.2 sec


SELECT pnh_id AS pid,i.size_chart,i.id AS itemid,i.name,m.name AS menu,m.id AS menu_id,
						i.gender_attr,c.name AS category,d.catid AS category_id,mc.name AS main_category,i.member_price,
						c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,i.orgprice AS mrp,i.price AS price,i.store_price,
						i.is_combo,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url,d.description,i.shipsin AS ships_in,d.keywords,
						COUNT(o.id) AS total_orders,
							(IF(0,i.price,i.member_price))/orgprice AS p_discount,
						i.sno AS d_sno , IF(FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('hd',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('HD Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('HD MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0) + IF(FIND_IN_SET('hd mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords)),1,0)  AS rel_ttl
						FROM king_deals d
						JOIN king_dealitems i ON i.dealid=d.dealid
						JOIN king_brands b ON b.id=d.brandid
						JOIN king_categories c ON c.id=d.catid
						
						LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid
						LEFT JOIN m_vendor_product_images v ON v.item_id=i.id
						LEFT OUTER JOIN king_categories mc ON mc.id=c.type
						LEFT JOIN king_orders o ON o.itemid = i.id
						WHERE  is_pnh=1  AND publish = 1  AND m.id IN ("112","100","104","122","120","136","102","117","133","111","127","115","101","134","128","107","106","108","116","126","118","123","114","119","121","129","130","131","137","105","103","113","132","135","109")  AND ( FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('hd',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD Mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('HD MOBILE',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('hd mobile',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  ) 
						GROUP BY i.id
						ORDER BY p_discount ASC,rel_ttl DESC, NAME ASC 
						LIMIT 0,10;
						
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid
	WHERE di.id='8686599844' AND o.i_price='7999' # AND di.mp_is_offer = 1 AND tr.init BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?)
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC ;
	
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid
	WHERE di.id='8686599844' AND o.i_price='7999.00' 
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC;
#========== Table Indexes created =============================
ALTER TABLE `m_product_deal_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_product_group_deal_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_rack_bin_info` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_rack_bin_info` ADD INDEX `is_damaged` (`is_damaged`);
ALTER TABLE `m_storage_location_info` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_stream_post_assigned_users` ADD INDEX `assigned_userid` (`assigned_userid`);
ALTER TABLE `m_stream_post_assigned_users` ADD INDEX `post_id` (`post_id`);
ALTER TABLE `m_stream_post_assigned_users` ADD INDEX `streamid` (`streamid`);
ALTER TABLE `m_stream_post_assigned_users` ADD INDEX `userid` (`userid`);
ALTER TABLE `m_stream_post_reply` ADD INDEX `post_id` (`post_id`);
ALTER TABLE `m_stream_post_reply` ADD INDEX `replied_by` (`replied_by`);
ALTER TABLE `m_stream_post_reply` ADD INDEX `status` (`status`);
ALTER TABLE `m_stream_posts` ADD INDEX `modified_by` (`modified_by`);
ALTER TABLE `m_stream_posts` ADD INDEX `posted_by` (`posted_by`);
ALTER TABLE `m_stream_posts` ADD INDEX `status` (`status`);
ALTER TABLE `m_stream_posts` ADD INDEX `stream_id` (`stream_id`);
ALTER TABLE `m_stream_users` ADD INDEX `access` (`access`);
ALTER TABLE `m_stream_users` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_stream_users` ADD INDEX `stream_id` (`stream_id`);
ALTER TABLE `m_stream_users` ADD INDEX `user_id` (`user_id`);
ALTER TABLE `m_town_territory_link` ADD INDEX `employee_id` (`employee_id`);
ALTER TABLE `m_town_territory_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_town_territory_link` ADD INDEX `parent_emp_id` (`parent_emp_id`);
ALTER TABLE `m_town_territory_link` ADD INDEX `territory_id` (`territory_id`);
ALTER TABLE `m_town_territory_link` ADD INDEX `town_id` (`town_id`);
ALTER TABLE `m_tray_info` ADD INDEX `max_allowed` (`max_allowed`);
ALTER TABLE `m_vendor_api_access` ADD INDEX `access_type` (`access_type`);
ALTER TABLE `m_vendor_api_access` ADD INDEX `vendor_id` (`vendor_id`);
ALTER TABLE `m_vendor_api_resources` ADD INDEX `api_access_id` (`api_access_id`);
ALTER TABLE `m_vendor_api_resources` ADD INDEX `mapping_tmpl_id` (`mapping_tmpl_id`);
ALTER TABLE `m_vendor_api_resources` ADD INDEX `module_type` (`module_type`);
ALTER TABLE `m_vendor_api_resources` ADD INDEX `vendor_id` (`vendor_id`);
ALTER TABLE `m_vendor_brand_link` ADD INDEX `applicable_from` (`applicable_from`);
ALTER TABLE `m_vendor_brand_link` ADD INDEX `applicable_till` (`applicable_till`);
ALTER TABLE `m_vendor_brand_link` ADD INDEX `cat_id` (`cat_id`);
ALTER TABLE `m_vendor_brand_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_contacts_info` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_contacts_info` ADD INDEX `mobile_no_1` (`mobile_no_1`);
ALTER TABLE `m_vendor_contacts_info` ADD INDEX `mobile_no_2` (`mobile_no_2`);
ALTER TABLE `m_vendor_info` ADD INDEX `api_access_key` (`api_access_key`);
ALTER TABLE `m_vendor_info` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_info` ADD INDEX `vendor_code` (`vendor_code`);
ALTER TABLE `m_vendor_product_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_product_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_product_link` ADD INDEX `ven_stock_qty` (`ven_stock_qty`);
ALTER TABLE `m_vendor_town_link` ADD INDEX `brand_id` (`brand_id`);
ALTER TABLE `m_vendor_town_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `m_vendor_town_link` ADD INDEX `vendor_id` (`vendor_id`);
ALTER TABLE `menu_class_config` ADD INDEX `class_id` (`class_id`);
ALTER TABLE `menu_class_config` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `menu_class_config` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `non_sk_imei_insurance_orders` ADD INDEX `nonsk_imei_no` (`nonsk_imei_no`);
ALTER TABLE `nonskimei_mem_fee_insu_amt` ADD INDEX `invoice_no` (`invoice_no`);
ALTER TABLE `nonskimei_mem_fee_insu_amt` ADD INDEX `member_id` (`member_id`);
ALTER TABLE `nonskimei_mem_fee_insu_amt` ADD INDEX `status` (`status`);
ALTER TABLE `nonskimei_mem_fee_insu_amt` ADD INDEX `transid` (`transid`);
ALTER TABLE `partner_deal_prices` ADD INDEX `partner_id` (`partner_id`);
ALTER TABLE `partner_info` ADD INDEX `name` (`name`);
ALTER TABLE `partner_info` ADD INDEX `trans_prefix` (`trans_prefix`);
ALTER TABLE `partner_order_items` ADD INDEX `log_id` (`log_id`);
ALTER TABLE `partner_order_items` ADD INDEX `status` (`status`);
ALTER TABLE `partner_order_items` ADD INDEX `transid` (`transid`);
ALTER TABLE `partner_orders_log` ADD INDEX `is_payment_made` (`is_payment_made`);
ALTER TABLE `partner_orders_log` ADD INDEX `status` (`status`);
ALTER TABLE `partner_transaction_details` ADD INDEX `order_no` (`order_no`);
ALTER TABLE `picklist_log_reservation` ADD INDEX `batch_id` (`batch_id`);
ALTER TABLE `pnh_acknowledgement_print_log` ADD INDEX `be_emp_id` (`be_emp_id`);
ALTER TABLE `pnh_acknowledgement_print_log` ADD INDEX `log_id` (`log_id`);
ALTER TABLE `pnh_acknowledgement_print_log` ADD INDEX `p_inv_no` (`p_inv_no`);
ALTER TABLE `pnh_acknowledgement_print_log` ADD INDEX `tm_emp_id` (`tm_emp_id`);
ALTER TABLE `pnh_api_franchise_cart_info` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_api_franchise_cart_info` ADD INDEX `member_id` (`member_id`);
ALTER TABLE `pnh_api_franchise_cart_info` ADD INDEX `pid` (`pid`);
ALTER TABLE `pnh_api_franchise_cart_info` ADD INDEX `status` (`status`);
ALTER TABLE `pnh_api_franchise_cart_info` ADD INDEX `user_id` (`user_id`);
ALTER TABLE `pnh_api_franchise_compaints` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_app_versions` ADD INDEX `version_date` (`version_date`);
ALTER TABLE `pnh_bussiness_trip_info` ADD INDEX `f_id` (`f_id`);
ALTER TABLE `pnh_bussiness_trip_info` ADD INDEX `task_id` (`task_id`);
ALTER TABLE `pnh_call_log` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_cash_bill` ADD INDEX `bill_no` (`bill_no`);
ALTER TABLE `pnh_cash_bill` ADD INDEX `status` (`status`);
ALTER TABLE `pnh_cash_bill` ADD INDEX `transid` (`transid`);
ALTER TABLE `pnh_cash_bill` ADD INDEX `user_id` (`user_id`);
ALTER TABLE `pnh_deliveryhub_fc_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `pnh_deliveryhub_town_link` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `pnh_deliveryhub` ADD INDEX `is_active` (`is_active`);
ALTER TABLE `pnh_employee_grpsms_log` ADD INDEX `emp_id` (`emp_id`);
ALTER TABLE `pnh_employee_grpsms_log` ADD INDEX `territory_id` (`territory_id`);
ALTER TABLE `pnh_employee_grpsms_log` ADD INDEX `town_id` (`town_id`);
ALTER TABLE `pnh_employee_grpsms_log` ADD INDEX `type` (`type`);
ALTER TABLE `pnh_executive_accounts_log` ADD INDEX `emp_id` (`emp_id`);
ALTER TABLE `pnh_franchise_account_stat` ADD INDEX `ref_id` (`ref_id`);
ALTER TABLE `pnh_franchise_account_summary` ADD INDEX `action_type` (`action_type`);
ALTER TABLE `pnh_franchise_account_summary` ADD INDEX `cheque_no` (`cheque_no`);
ALTER TABLE `pnh_franchise_account_summary` ADD INDEX `credit_note_id` (`credit_note_id`);
ALTER TABLE `pnh_franchise_account_summary` ADD INDEX `receipt_id` (`receipt_id`);
ALTER TABLE `pnh_franchise_menu_link` ADD INDEX `sch_discount_end` (`sch_discount_end`);
ALTER TABLE `pnh_franchise_menu_link` ADD INDEX `sch_discount_start` (`sch_discount_start`);
ALTER TABLE `pnh_franchise_owners` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_franchise_pprice_enqrylog` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_franchise_pprice_enqrylog` ADD INDEX `pid` (`pid`);
ALTER TABLE `pnh_franchise_prepaid_log` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_franchise_price_quote` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_franchise_price_quote` ADD INDEX `pid` (`pid`);
ALTER TABLE `pnh_franchise_unorderd_log` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_invoice_return` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_invoice_return` ADD INDEX `invoice_no` (`invoice_no`);
ALTER TABLE `pnh_invoice_returns_flags` ADD INDEX `name` (`name`);
ALTER TABLE `pnh_invoice_returns_product_link` ADD INDEX `imei_no` (`imei_no`);
ALTER TABLE `pnh_invoice_returns_product_link` ADD INDEX `order_id` (`order_id`);
ALTER TABLE `pnh_invoice_returns_product_link` ADD INDEX `product_id` (`product_id`);
ALTER TABLE `pnh_invoice_returns_product_link` ADD INDEX `return_id` (`return_id`);
ALTER TABLE `pnh_invoice_returns_product_service` ADD INDEX `return_prod_id` (`return_prod_id`);
ALTER TABLE `pnh_invoice_returns_remarks` ADD INDEX `parent_id` (`parent_id`);
ALTER TABLE `pnh_invoice_returns_remarks` ADD INDEX `return_prod_id` (`return_prod_id`);
ALTER TABLE `pnh_invoice_returns` ADD INDEX `handled_by` (`handled_by`);
ALTER TABLE `pnh_invoice_returns` ADD INDEX `invoice_no` (`invoice_no`);
ALTER TABLE `pnh_invoice_returns` ADD INDEX `return_by` (`return_by`);
ALTER TABLE `pnh_invoice_transit_log` ADD INDEX `ref_id` (`ref_id`);
ALTER TABLE `pnh_invoice_transit_log` ADD INDEX `sent_log_id` (`sent_log_id`);
ALTER TABLE `pnh_less_margin_brands` ADD INDEX `brandid` (`brandid`);
ALTER TABLE `pnh_loyalty_points` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_m_allotted_mid` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_m_allotted_mid` ADD INDEX `mid_end` (`mid_end`);
ALTER TABLE `pnh_m_allotted_mid` ADD INDEX `mid_start` (`mid_start`);
ALTER TABLE `pnh_m_book_template_voucher_link` ADD INDEX `book_template_id` (`book_template_id`);
ALTER TABLE `pnh_m_book_template_voucher_link` ADD INDEX `voucher_id` (`voucher_id`);
ALTER TABLE `pnh_m_book_template` ADD INDEX `menu_ids` (`menu_ids`);
ALTER TABLE `pnh_m_book_template` ADD INDEX `product_id` (`product_id`);
ALTER TABLE `pnh_m_coupons` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_m_coupons` ADD INDEX `member_id` (`member_id`);
ALTER TABLE `pnh_m_creditlimit_onprepaid` ADD INDEX `book_id` (`book_id`);
ALTER TABLE `pnh_m_creditlimit_onprepaid` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_m_creditlimit_onprepaid` ADD INDEX `receipt_id` (`receipt_id`);
ALTER TABLE `pnh_m_deposited_receipts` ADD INDEX `bank_id` (`bank_id`);
ALTER TABLE `pnh_m_device_info` ADD INDEX `device_type_id` (`device_type_id`);
ALTER TABLE `pnh_m_employee_leaves` ADD INDEX `emp_id` (`emp_id`);
ALTER TABLE `pnh_m_fran_security_cheques` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_m_franchise_info` ADD INDEX `class_id` (`class_id`);
ALTER TABLE `pnh_m_franchise_info` ADD INDEX `pnh_franchise_id` (`pnh_franchise_id`);
ALTER TABLE `pnh_m_franchise_info` ADD INDEX `territory_id` (`territory_id`);
ALTER TABLE `pnh_m_franchise_info` ADD INDEX `town_id` (`town_id`);
ALTER TABLE `pnh_m_franchise_search_log` ADD INDEX `fid` (`fid`);
ALTER TABLE `pnh_m_franchise_search_log` CHANGE `total_results` `total_results` INT (5) DEFAULT '0' NULL ;
ALTER TABLE `pnh_m_insurance_menu` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_m_manifesto_sent_log` ADD INDEX `bus_id` (`bus_id`);
ALTER TABLE `pnh_m_manifesto_sent_log` ADD INDEX `sent_invoices` (`sent_invoices`);
ALTER TABLE `pnh_m_offers` ADD INDEX `brand_id` (`brand_id`);
ALTER TABLE `pnh_m_offers` ADD INDEX `cat_id` (`cat_id`);
ALTER TABLE `pnh_m_offers` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_m_offers` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_m_payment_collection` ADD INDEX `f_id` (`f_id`);
ALTER TABLE `pnh_m_payment_collection` ADD INDEX `task_id` (`task_id`);
ALTER TABLE `pnh_m_sales_target_info` ADD INDEX `f_id` (`f_id`);
ALTER TABLE `pnh_m_sales_target_info` ADD INDEX `task_id` (`task_id`);
ALTER TABLE `pnh_m_task_info` ADD INDEX `asgnd_town_id` (`asgnd_town_id`);
ALTER TABLE `pnh_m_task_info` ADD INDEX `ref_no` (`ref_no`);
ALTER TABLE `pnh_m_task_types` ADD INDEX `task_type` (`task_type`);
ALTER TABLE `pnh_m_territory_info` ADD INDEX `state_id` (`state_id`);
ALTER TABLE `pnh_m_uploaded_depositedslips` ADD INDEX `deposited_reference_no` (`deposited_reference_no`);
ALTER TABLE `pnh_m_uploaded_depositedslips` ADD INDEX `receipt_ids` (`receipt_ids`);
ALTER TABLE `pnh_manifesto_log` ADD FULLTEXT `invoice_nos` (`invoice_nos`);
ALTER TABLE `pnh_manifesto_log` ADD INDEX `invoice_nos` (`invoice_nos`);
ALTER TABLE `pnh_member_offers_log` ADD INDEX `offer_sno` (`offer_sno`);
ALTER TABLE `pnh_member_offers_log` ADD INDEX `offer_type` (`offer_type`);
ALTER TABLE `pnh_member_points_track` ADD INDEX `transid` (`transid`);
ALTER TABLE `pnh_member_points_track` ADD INDEX `user_id` (`user_id`);
ALTER TABLE `pnh_membersch_deals` ADD INDEX `itemid` (`itemid`);
ALTER TABLE `pnh_membersch_deals` ADD INDEX `menuid` (`menuid`);
ALTER TABLE `pnh_menu_group_link` ADD INDEX `group_id` (`group_id`);
ALTER TABLE `pnh_menu_group_link` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_menu_margin_track` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_order_margin_track` ADD INDEX `itemid` (`itemid`);
ALTER TABLE `pnh_prepaid_menu_config` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_quotes_deal_link` ADD INDEX `pnh_id` (`pnh_id`);
ALTER TABLE `pnh_sch_discount_track` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_ship_remarksupdate_log` ADD INDEX `ship_msg_id` (`ship_msg_id`);
ALTER TABLE `pnh_ship_remarksupdate_log` ADD INDEX `ticket_id` (`ticket_id`);
ALTER TABLE `pnh_sms_log_sent` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_sms_log_sent` ADD INDEX `pnh_empid` (`pnh_empid`);
ALTER TABLE `pnh_sms_log_sent` ADD INDEX `pnh_mid` (`pnh_mid`);
ALTER TABLE `pnh_sms_log_sent` ADD INDEX `ticket_id` (`ticket_id`);
ALTER TABLE `pnh_sms_log_sent` ADD INDEX `to` (`to`);
ALTER TABLE `pnh_special_margin_deals` ADD INDEX `from` (`from`);
ALTER TABLE `pnh_special_margin_deals` ADD INDEX `to` (`to`);
ALTER TABLE `pnh_super_scheme` ADD INDEX `brand_id` (`brand_id`);
ALTER TABLE `pnh_super_scheme` ADD INDEX `cat_id` (`cat_id`);
ALTER TABLE `pnh_super_scheme` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_super_scheme` ADD INDEX `menu_id` (`menu_id`);
ALTER TABLE `pnh_super_scheme` ADD INDEX `schme_discount_id` (`schme_discount_id`);
ALTER TABLE `pnh_superscheme_deals` ADD INDEX `itemid` (`itemid`);
ALTER TABLE `pnh_superscheme_deals` ADD INDEX `menuid` (`menuid`);
ALTER TABLE `pnh_t_book_allotment` ADD INDEX `allotment_id` (`allotment_id`);
ALTER TABLE `pnh_t_book_allotment` ADD INDEX `book_id` (`book_id`);
ALTER TABLE `pnh_t_book_allotment` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_t_book_details` ADD INDEX `book_slno` (`book_slno`);
ALTER TABLE `pnh_t_book_details` ADD INDEX `book_template_id` (`book_template_id`);
ALTER TABLE `pnh_t_book_receipt_link` ADD INDEX `book_id` (`book_id`);
ALTER TABLE `pnh_t_book_receipt_link` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_t_book_receipt_link` ADD INDEX `receipt_id` (`receipt_id`);
ALTER TABLE `pnh_t_book_voucher_link` ADD INDEX `book_id` (`book_id`);
ALTER TABLE `pnh_t_book_voucher_link` ADD INDEX `voucher_slno_id` (`voucher_slno_id`);
ALTER TABLE `pnh_t_credit_info` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_t_device_movement_info` ADD INDEX `device_id` (`device_id`);
ALTER TABLE `pnh_t_returns_transit_log` ADD INDEX `order_id` (`order_id`);
ALTER TABLE `pnh_t_returns_transit_log` ADD INDEX `return_id` (`return_id`);
ALTER TABLE `pnh_t_returns_transit_log` ADD INDEX `ticket_id` (`ticket_id`);
ALTER TABLE `pnh_t_tray_invoice_link` ADD INDEX `invoice_no` (`invoice_no`);
ALTER TABLE `pnh_t_tray_invoice_link` ADD INDEX `tray_terr_id` (`tray_terr_id`);
ALTER TABLE `pnh_t_tray_territory_link` ADD INDEX `territory_id` (`territory_id`);
ALTER TABLE `pnh_t_tray_territory_link` ADD INDEX `tray_id` (`tray_id`);
ALTER TABLE `pnh_t_voucher_details` ADD INDEX `franchise_id` (`franchise_id`);
ALTER TABLE `pnh_t_voucher_details` ADD INDEX `group_code` (`group_code`);
ALTER TABLE `pnh_t_voucher_details` ADD INDEX `member_id` (`member_id`);
ALTER TABLE `pnh_t_voucher_details` ADD INDEX `voucher_id` (`voucher_id`);
ALTER TABLE `pnh_t_voucher_details` ADD INDEX `voucher_serial_no` (`voucher_serial_no`);
ALTER TABLE `pnh_task_remarks` ADD INDEX `emp_id` (`emp_id`);
ALTER TABLE `pnh_task_remarks` ADD INDEX `task_id` (`task_id`);
ALTER TABLE `pnh_task_type_details` ADD INDEX `f_id` (`f_id`);
ALTER TABLE `pnh_task_type_details` ADD INDEX `task_id` (`task_id`);
ALTER TABLE `pnh_task_type_details` ADD INDEX `task_type_id` (`task_type_id`);
#========== Table Indexes created =============================
#============ search api log =========================
CREATE TABLE `pnh_m_franchise_search_log` (
	`id` BIGINT(11) NOT NULL AUTO_INCREMENT,
	`elapsed_time` DECIMAL(20,4) DEFAULT '0.0000',
	`memory_usage` DECIMAL(20,4) DEFAULT '0.0000',
	`loaded_queries` TEXT,
	`fid` INT(11) DEFAULT '0',
	`srch_kwd` VARCHAR(255) DEFAULT NULL,
	`req_data` TEXT,
	`total_results` INT(5) DEFAULT '0',
	`logged_on` DATETIME DEFAULT NULL,
	`responded_on` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `fid` (`fid`)
);
ALTER TABLE `pnh_m_franchise_search_log` ADD COLUMN `last_query` TEXT NULL AFTER `responded_on`;
ALTER TABLE `pnh_m_franchise_search_log` ADD COLUMN `type` VARCHAR(100) NULL AFTER `last_query`;
#============ search api log =========================

#=================Suresh=================
SELECT o.transid,COUNT(*) AS t 
FROM king_orders o
JOIN king_transactions t ON t.transid=o.transid
GROUP BY t.transid 
HAVING t > 1 
ORDER BY t DESC 
LIMIT 10;
#===================
NEW FIELDS added memory_usage,last_query,TYPE;

SELECT * FROM pnh_m_franchise_search_log;

=========================================================
(
	SELECT l.itemid,p.product_id,CONCAT(GROUP_CONCAT(CONCAT(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) AS a
		FROM m_product_group_deal_link l
		JOIN products_group_pids p ON p.group_id=l.group_id
		JOIN products_group_attributes a ON a.attribute_name_id=p.attribute_name_id
		JOIN products_group_attribute_values v ON v.attribute_value_id=p.attribute_value_id
		JOIN m_product_info p1 ON p1.product_id = p.product_id AND p1.is_sourceable = 1
	WHERE l.itemid IN('1493282173')
	GROUP BY p.product_id
)
UNION
(
	SELECT a.itemid,a.product_id,CONCAT(GROUP_CONCAT(CONCAT(attr_name,':',attr_value) ORDER BY f.id DESC ),',ProductID:',a.product_id) AS a
		FROM m_product_deal_link a
		JOIN king_dealitems b ON a.itemid = b.id
		JOIN m_product_info d ON d.product_id = a.product_id
		JOIN m_product_attributes e ON e.pid = d.product_id
		JOIN m_attributes f ON f.id = e.attr_id
	WHERE b.is_group = 1 AND a.itemid IN('1493282173') AND a.is_active = 1
	GROUP BY a.product_id
)
=============================================
SELECT * FROM king_dealitems WHERE is_group=1 AND id='1493282173' LIMIT 10;

(
	SELECT l.itemid,p.product_id,CONCAT(GROUP_CONCAT(CONCAT(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) AS a
		FROM m_product_group_deal_link l
		JOIN products_group_pids p ON p.group_id=l.group_id
		JOIN products_group_attributes a ON a.attribute_name_id=p.attribute_name_id
		JOIN products_group_attribute_values v ON v.attribute_value_id=p.attribute_value_id
		JOIN m_product_info p1 ON p1.product_id = p.product_id AND p1.is_sourceable = 1
	WHERE l.itemid IN(Array)
	GROUP BY p.product_id
)
UNION
(
	SELECT a.itemid,a.product_id,CONCAT(GROUP_CONCAT(CONCAT(attr_name,':',attr_value) ORDER BY f.id DESC ),',ProductID:',a.product_id) AS a
		FROM m_product_deal_link a
		JOIN king_dealitems b ON a.itemid = b.id
		JOIN m_product_info d ON d.product_id = a.product_id
		JOIN m_product_attributes e ON e.pid = d.product_id
		JOIN m_attributes f ON f.id = e.attr_id
	WHERE b.is_group = 1 AND a.itemid IN(Array) AND a.is_active = 1
)



color:MEDIUM Blue,size:FS,ProductID:234251===<br>{"status":"success","response":{"deal_list":"","total_deals":376,"brand_list":[{"id":"83888427","name":"Lino Perros"},{"id":"96978419","name":"La Briza"},{"id":"86376267","name":"Nell"},{"id":"17691579","name":"Alayna"},{"id":"97761571","name":"Lovely Lady"},{"id":"17663564","name":"Medhira"},{"id":"15523712","name":"Mother Earth"},{"id":"31542664","name":"W"},{"id":"92239827","name":"Design Go"},{"id":"55712722","name":"Schellene"},{"id":"56163832","name":"Shree"},{"id":"22812311","name":"CHHABRA555"},{"id":"67839625","name":"DIVA FASHION"},{"id":"36964292","name":"Fabdeal"},{"id":"82461869","name":"Floret"},{"id":"82353289","name":"Campus Sutra"}],"category_list":[],"cat_list":[{"id":"10","name":"Bags & Clutches"},{"id":"172","name":"Casual Belts"},{"id":"131","name":"Hand bags"},{"id":"1019","name":"Heels"},{"id":"1177","name":"Kurta"},{"id":"648","name":"Luggage straps & & Safety Locks"},{"id":"1183","name":"Salwar Kameez Dupatta"},{"id":"1185","name":"Salwar\/Patiala"},{"id":"928","name":"SAREE"},{"id":"1174","name":"SCARF"},{"id":"1192","name":"Sleepwear"},{"id":"174","name":"Sling Bags"},{"id":"142","name":"Tshirts"},{"id":"659","name":"Women Wallets"}],"price_list":{"min":"295","max":"4499"},"attr_list":["color:Medium Blue,size:FS,ProductID:234251"],"gender_list":{"0":"Ladies","281":"Men"},"elapsed_time":"1.7247","memory_usage":"{memory_usage}"}}


#Aug_04_2014

#set @itemids='7781131135','7255479461','6691445999','6551335315','8622555884','8928159799','5881697461','6878318263','4463169691','4292387798','6177858685','1918691539';

SET @itemids='7781131135,7255479461,6691445999,6551335315,8622555884,8928159799,5881697461,6878318263,4463169691,4292387798,6177858685,1918691539';
#==================
(
SELECT l.itemid,p.product_id,CONCAT(GROUP_CONCAT(CONCAT(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) AS a
	FROM m_product_group_deal_link l
	JOIN products_group_pids p ON p.group_id=l.group_id
	JOIN products_group_attributes a ON a.attribute_name_id=p.attribute_name_id
	JOIN products_group_attribute_values v ON v.attribute_value_id=p.attribute_value_id
	JOIN m_product_info p1 ON p1.product_id = p.product_id AND p1.is_sourceable = 1
WHERE l.itemid IN ('7781131135','7255479461','6691445999','6551335315','8622555884','8928159799','5881697461','6878318263','4463169691','4292387798','6177858685','1918691539')
GROUP BY p.product_id
)
UNION
(
SELECT a.itemid,a.product_id,CONCAT(GROUP_CONCAT(CONCAT(attr_name,':',attr_value) ORDER BY f.id DESC ),',ProductID:',a.product_id) AS a
	FROM m_product_deal_link a
	JOIN king_dealitems b ON a.itemid = b.id
	JOIN m_product_info d ON d.product_id = a.product_id
	JOIN m_product_attributes e ON e.pid = d.product_id
	JOIN m_attributes f ON f.id = e.attr_id
WHERE b.is_group = 1 AND a.itemid IN ('7781131135','7255479461','6691445999','6551335315','8622555884','8928159799','5881697461','6878318263','4463169691','4292387798','6177858685','1918691539') AND a.is_active = 1
GROUP BY a.product_id
)

======================
DESC m_employee_info
SELECT * FROM m_employee_info WHERE emp_type=1;
INSERT INTO (`name`,email,emp_type) VALUES('name','email',1);
userid=0
,NAME=''
,fathername=''
,dob=''
,email=''
,gender='Male'
,address='kdfsjagsdakfjkldsajfklsajdf'
,city='asd'
,postcode=''
,contact_no=''
,job_title='9'
,job_title2='9'
,send_sms=1
,created_on=NOW()
,created_by=1
,is_suspended=0
,remarks=''
,emp_type=1

SELECT * FROM m_employee_dept_link

#=========================
INSERT INTO (`name`,email,emp_type) VALUES('name','email',1);
SELECT * FROM m_employee_dept_link;
#=========================





SELECT i.id AS itemid,i.is_group,i.pnh_id AS pid,orgprice,price,gender_attr,c.name AS cat,b.name AS brand,d.catid,d.brandid 
							FROM king_deals d 
							JOIN king_dealitems i ON i.dealid=d.dealid 
							JOIN king_brands b ON b.id=d.brandid 
							JOIN king_categories c ON c.id=d.catid
							 
							LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid 
							LEFT OUTER JOIN king_categories mc ON mc.id=c.type 
							WHERE  is_pnh=1 
							 AND publish = 1  AND m.id IN ("112","100","104","122","120","136","102","117","133","111","127","115","101","134","128","107","106","108","116","126","118","123","114","119","121","129","130","131","137","105","103","113","132","135","109")  AND ( FIND_IN_SET('formal',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('shirts',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  OR  FIND_IN_SET('formal shirts',CONCAT(pnh_id,',',i.name,',',IFNULL(mc.name,''),',',b.name,',',c.name,',',d.keywords))  ) 
							GROUP BY i.id 
							ORDER BY c.name,b.name;
#==> 1.37sec
					<pre>'9169948819','6769512317','5279582212','8866511342','5473219869','5627365154','1437974838','2766927272','6394112669','8171928886','9795122745','6797679953','4299791221','4844321569','7853136115','9542938719','9275153734','1149681457','5565555227','5554152844','1566636917','3389812768','7423913344','9675653555','9214158878','8157537747','7164274475','3275522452','8412387248','1674643285','9542281741','4341219718','1497998483','3511975517','4757675556','8437727392','3668888164','6745183918','6134741823','4367958952','6596121276','6828181376','6596238463','5522244525','2786134423','8544882133','8847242338','2662287832','2966678651','3587946443','3818927618','3896713129','8645128657','4246817388','7289174166','1869322996','7456571254','9833751515','3173477784','5491143692','1837318273','8453427162','7416241418','3582426944','3175561684','1374434414','5976422685','9791979843','8823646358','9745316817','4332318493','1372792941','1948229716','2568671729','3482944465','5151289527','4958833442','5212959868','3743942492','9426161275','2819457494','9518622878','2534153392','9471818324','8134111573','4221942994','6453847618','9588226523','4457547476','1811239362','5969744436','6121396917','3516733324','7723957913','8317729363','8388432385','2321672952','4693312573','6612356543','5814534295','9651722571','4394229442','5987798525','8641769358','4893184629','9271432927','8693471725','1768311382','2371146115','5223638648','5962762271','5184425933','7267752794','1997245168','8424511214','2279677768','3578369831','9873116269','3736912992','4187579841','8823348332','1742529453','8425936799','8168987994','1662733652','3742242262','7644356354','2367358584','2885685331','8329793259','7711683917','4432353199','8597242155','1163676233','1547793497','9974654136','2469329428','6793136227','7355757236','4217733611','7985233595','7318827445','5725266738','2191961697','4861925827','4127193193','7538443723','9887393545','5359928382','7118163724','4814581881','6594715258','6538931898','1941944836','8685681835','1463287748','5929376918','1951294983','1819878474','7379834992','9925733128','9998238452','3713984919','3529915755','3199498899','6197126485','8677784413','9923657391','2545124842','5621412514','5991436927','4342784585','7892214811','5163799251','7843757179','3855856553','2455294648','9265621539','7491993535','5916747967','6253189931','3696953148','7383623665','2979629729','3656461847','3518545575','5922493118','7126549528','6964973725','4615716844','6971589148','2936557111','5449158253','1941334293','4287949985','3488845347','8419423518','9425338636','8742323254','1716332185','9818433314','6895179281','2229512635','3494721192','5943552946','5792623251','2712679415','7443754532','3143524744','6392666612','5344559685','4283355467','7649672426','8717548268','2216975621','3315113175','7183962787','4842837821','5389241261','8368573837','7796582868','3961973855','2368746819','1316845232','4454826896','6292454243','7565925769','1638992125','6428342131','9937548441','4594425792','9914242223','2748777668','1318531129','4764277489','8715197346','9836614198','7974197855','7986742813','4464346179','1241386699','2347371279','9345692126','1168246613','7291574554','1197463639','7714523578','6431574445','6653425577','8299883674','4391335225','7165719567','9483949631','5527849719','2255944895','3892387616','2285372322','9815174912','6751882585','1262579574','6323651276','8989265634','7672516626','7929728461','9619915274','6252863562','5487557146','5735664828','7415293846','1348176822','5161843844','8439615372','9157176696','2577885473','5365829423','8174125615','2295481224','7526562851','4211815572','3894787653','3556717635','2648524439','3484568248','8384437719','5297114369','5476616859','9412544259','4318257251','3463213257','8683536887','8368977521','8575444394','9167343176','6985965663','3312438211','4511111848','8544449771','9596635333','8445617652','9734764619','7825116578','4286485764','1834692122','1443571529','4255996231','6958612479','5624812184','9444756762','7892637513','5442211183','6156266627','9817812756','2718188454','1889655865','2579114419','3896835443','5313464258','3612436258','5336814843','6414918995','4929357548','5172614665','8425629859','7988998794','2484872574','5684573648','5488865123','6528392447','1163581549','5976461277','9229614323','8895441912','3883776759','8337318957','4852913459','9617358929','9232794352','5172313154','2318551523','1642597561','6522471213','6429845925','6831715353','2685263896','1951716111','2295931535','7356993959','7594225818','7875597445','1436835816','2169769677','2561712799','8924516753','9649548358','7118364984','1793688442','4882645996','9218587119','8992683256','3655557797','5713764814','5682575264','9497885479','3857474485','4852838895','4928533916','8539286635','6649885996','3738775585','5759553932','6313517723','7718925864','2184163297','4213872929','5293557644','8553243274','8218218977','9223589823','9776317396','5878537633','5452768958','8544448317','2449186657','7188963371','9752152912','8578536793','9575956638','4187843382','8999264761','7387821168','6574166825','2817336897','8361733751','2181413186','8332649132','1612588412','7469235471','6827158692','4248772928','6122125585','3511466998','4165337573','7698988715','7415234245','8426424394','5856866222','9614572385','4467532987','8912865595','2979561392','7796875153','1455492469','8814225974','4455327148','4631184852','2742257728','7177529738','8377498377','1758346642','2732299615','3878975561','6672141644','2218642199','5762233648','1426966739','2477157596','7675158575','4252869561','8632711424','6921996377','3763389766','6125898862','6249725423','1734212474','6989983651','2991349964','7848636221','3318568336','9945558738','7712481291','4276488479','1437828461','1494595182','5284658821','4743314221','9251855779','7979175761','9248871838','5262135687','9263822297','8828258168','5555397299','6572636972','2436173637','8473353343','7789812199','3567667553','2279638969','9589332491','9272439325','4343314966','5345537778','6371295863','6945856691','3459951198','9553296797','9883946719','5372555344','7628329839','8111922223','2147337366','4669443984','6577981632','8124129159','8299892785','9545792946','1429191744','9742798414','9619965791','6322828426','2957499494','4681178943','4768443672','1282876767','7492684655','8379981466','1178736658','1851796685','9487191577','7525282496','5161382436','1552365769','1316591643','3332623197','4437876768','3937886556','4311522666','7366482743','6838864966','7277364142','4568619665','8153428274','1568463218','6494397782','9384251195','7455512991','9427562826','5539211814','9242141887','5998122794','6489217799','6382685199','5658184148','7244963555','3376298734','5862854426','3958433945','6624856458','1178364965','3129996689','1682589572','1751457349','6973137268','7135685982','3289892125','1764152433','3383266586','7194726513','5746494764','3216329775','6352748542','5293285222','3463929946','1835839555','2529763973','3598931358','5283789351','2465334663','9688836983','5784748168','2557933354','3323458411','3928846542','5674342934','7289939729','4693384381','9279653282','2216928973','8823633197','7816818491','3486226765','2672164598','3796479799','3315444325','1523775986','7958215184','9595977171','7834357787','4297567828','3452337339','9965134355','5386844934','9643262226','5746384229','6831697887','6491218494','4445454691','3395689298','8782946382','4953243942','3218181233','4412518757','4513728236','9934738342','2211415363','2891226685','1548546771','8459838851','2911839132','8562162528','2363534371','3475577396','9638623167','6422592628','3894999162','3537974629','9999921267','4235419358','7958495916','3962885728','5484939581','1723966661','3166753247','4547676862','2269823456','1734325911','6464527796','3469816259','9499874191','6112879885','8443157652','7958633918','5859167735','2668823953','3878543149','5349182164','6958129256','3797771428','4538141727','2272118488','1152312365','6944812554','3769928397','7678427794','2311294294','5962744166','4893552246','2471857924','7574383721','7869934144','5513177483','3315434521','7821816648','3322453668','4434443951','3188343492','1987639541','4591485159','8154287924','7337752649','2552639398','5285135476','8668377395','5162761452','3677267712','4741952775','6981464987','5513645875','2721747668','6281431261','1557554527','3578736961','3693923432','2254711138','6244819989','9221433395','6545288457','3124726828','4773832386','6734284415','3866824499','3529621522','6999445455','9273535786','6598231162','6185555614','7552898524','9165889244','6196319293','2317374698','8772699628','2766329445','4128769181','2195259663','6121813737','3727896186','1341812427','6112214233','9597843957','3821294311','2866739851','9988131345','5565372192','6743657648','5169863658','9854215165','5154169635','8981132735','5578442792','5525449666','6291539687','8185832756','6717657697','5123646416','9192938299','1937254963','3265792649','5689574392','9687415419','7362292572','3812138826','8623332888','4798829358','9292833677','2488651919','2661142836','2321743767','1526441835','2994884548','7364816273','8521889635','8514112361','6656269543','7945247295','6996627392','2278683435','1687182765','2563224635','2663995253','7153786884','6269197616','7294944298','8477782738','7243297232','1148621639','2837347693','3715918573','8663615922','6568487194','3894349142','5414582929','5931574288','8853821577','4658337282','6394989677','5414399312','3665429617','7461288749','3598869925','3749364435','7271528944','9678425414','8838695186','7433379243','1472859686','8987813716','9866753363','6187979974','3545368686','7852674385','4649718429','9288875851','8569382887','6459924899','7437977582','1652924613','1392872437','5387234686','7351582291','1843229311','6564912572','4845863465','6889328165','3316819892','8684317739','5237864423','1714687655','9986287496','9238712367','8659121856','5248153673','2377623851','8789165591','1263656468','6296516771','7893282947','9889653882','6268777416','3364422851','7453636356','5285743966','9998664355','1639614695','2212927651','3744131584','9162712919','7558243965','8133395959','3481551346','6629795793','2464948818','7912895917','9922658416','3387353169','5636973964','9848999778','8237912671','9211996692','1232149366','1781186374','1236288239','6863745425','7655525524','8589832518','7964938544','9915791734','8945679425','6863395941','1734974679','7172933282','9265132249','2259829414','3789424822','4722517985','6551335315','9263897883','9855871818','8622555884','4463169691','5881697461','4292387798','6177858685','9116755236','1918691539','7922375714','2614468811','4987176446','7151684437','5857891847','5577558978','8194269172','9951749455','1463328183','6948696325','3436395663','7599848526','2587379834','3276264988','6355124489','6421417491','7976296618','4828967941','8628487456','7662151556','9163771673','5552247116','4227896916','7452951298','4214669895','3267128745','7255479461','8713794861','7781131135','6597938873','2541126583','8359935229','3898479713','1789954676','1143166122','8928159799','6878318263','1598682777','6691445999','1196653796','5833216581','7224535566','2749681951','1824323334','6867472717','7662357667','9944549232','2944815518','6776591161','5285954759','3442432471','5951531664','6254257352','1978441335','5341126262','7625887928','8216877749','3747651898','3587953833','6989312781','3558182829','6475497936'
					
#+===============================
(
SELECT l.itemid,p.product_id,CONCAT(GROUP_CONCAT(CONCAT(a.attribute_name,':',v.attribute_value)),',ProductID:',p.product_id) AS a
	FROM m_product_group_deal_link l
	JOIN products_group_pids p ON p.group_id=l.group_id
	JOIN products_group_attributes a ON a.attribute_name_id=p.attribute_name_id
	JOIN products_group_attribute_values v ON v.attribute_value_id=p.attribute_value_id
	JOIN m_product_info p1 ON p1.product_id = p.product_id AND p1.is_sourceable = 1
WHERE l.itemid IN("9517198287" ,"1149681457" ,"4479759239" ,"9275153734" ,"3447513919" ,"9542938719" ,"6452638757" ,"1234989323" ,"6546822944" ,"5963361472" ,"5565555227" ,"2928492186" ,"5554164628" ,"2211532426" ,"5554152844" ,"4455189334" ,"8676928614" ,"9578754519" ,"7153734342" ,"5341126262" ,"8654738234" ,"2224495994" ,"8188384285" )
GROUP BY p.product_id
)
UNION
(
SELECT a.itemid,a.product_id,CONCAT(GROUP_CONCAT(CONCAT(attr_name,':',attr_value) ORDER BY f.id DESC ),',ProductID:',a.product_id) AS a
	FROM m_product_deal_link a
	JOIN king_dealitems b ON a.itemid = b.id
	JOIN m_product_info d ON d.product_id = a.product_id
	JOIN m_product_attributes e ON e.pid = d.product_id
	JOIN m_attributes f ON f.id = e.attr_id
WHERE b.is_group = 1 AND a.itemid IN("9517198287" ,"1149681457" ,"4479759239" ,"9275153734" ,"3447513919" ,"9542938719" ,"6452638757" ,"1234989323" ,"6546822944" ,"5963361472" ,"5565555227" ,"2928492186" ,"5554164628" ,"2211532426" ,"5554152844" ,"4455189334" ,"8676928614" ,"9578754519" ,"7153734342" ,"5341126262" ,"8654738234" ,"2224495994" ,"8188384285" ) AND a.is_active = 1
GROUP BY a.product_id
)
#=>0.011sec


SELECT * FROM t_imei_no WHERE reserved_batch_rowid !=0;

/*[1:47:50 PM][805 ms]*/ ALTER TABLE `t_reserved_batch_stock` ADD COLUMN `grn_id` BIGINT(11) DEFAULT 0 NULL AFTER `tmp_prev_stk_id`; 

#Aug_05_2014
====================
$GROUP=$this->db->QUERY("select group_id,group_name from products_group where group_id=?",$g['group_id'])->row_array();
$attr.="";
$anames=$this->db->QUERY("select attribute_name_id,attribute_name from products_group_attributes where group_id=?",$g['group_id'])->result_array();
foreach($anames AS $a)
{
	$attr.="<b>{$a['attribute_name']} :</b><span><select class='attr' name='{$pid}_{$a['attribute_name_id']}'>";
$avalues=$this->db->QUERY("select a.*,sum(available_qty) as p_stk,is_sourceable from products_group_attribute_values a join products_group_pids c on c.group_id = a.group_id join m_product_info b on c.product_id = b.product_id join t_stock_info ts on ts.product_id = b.product_id where a.attribute_name_id=? 	group by a.attribute_value_id having (is_sourceable or p_stk) ",$a['attribute_name_id'])->result_array();
	foreach($avalues AS $v)
	$attr.="<option stk='{$v['p_stk']}' value='{$v['attribute_value_id']}'>{$v['attribute_value']}</option>";
	$attr.='</select></span>';
}
=====================
#Aug_06_2014

SELECT COUNT(*) AS is_rechrge_gv FROM pnh_member_offers mo
	LEFT JOIN king_orders o ON o.id=mo.order_id AND o.status!=3
	WHERE mo.offer_type=1 AND mo.offer_type NOT IN (0,2,3) AND mo.member_id='21111152' AND mo.process_status!=1
	HAVING is_rechrge_gv IS NOT NULL;
	
mp_loyaltypoint_brand_cat_config
id
brandid
catid
offernote_tmpl
is_active
created_on
created_by
modified_on
modified_by

#===============================
#Aug_07_2014-Shivaraj
CREATE TABLE `mp_loyaltypoint_brand_cat_config`( `id` BIGINT NOT NULL AUTO_INCREMENT, `brandid` BIGINT, `catid` BIGINT, `offernote_tmpl` TEXT, `is_active` TINYINT(1) DEFAULT 1, `created_on` DATETIME, `created_by` INT(11), `modified_on` DATETIME, `modified_by` INT(11), PRIMARY KEY (`id`) );
#Indexes
ALTER TABLE `mp_loyaltypoint_brand_cat_config` ADD INDEX (`brandid`), ADD INDEX (`catid`), ADD INDEX (`is_active`);
#INSERT INTO `mp_loyaltypoint_brand_cat_config` (`brandid`, `catid`, `offernote_tmpl`, `created_on`, `created_by`) VALUES ('23645763', '131', 'You will get %diff% loyalty points', '2014-08-06 15:58:28', '37');
ALTER TABLE `mp_loyaltypoint_brand_cat_config` ADD COLUMN `menuid` BIGINT(20) NULL AFTER `id`;
ALTER TABLE `mp_loyaltypoint_brand_cat_config` DROP `mmenuid`; 
#===============================

Menu: 105, Brand;23645763, Cat:131
==============
SELECT * FROM mp_loyaltypoint_brand_cat_config;

#====================
SELECT i.id,i.price,i.pnh_id,i.name,dl.menuid,dl.brandid,dl.catid,lp.id,lp.offernote_tmpl FROM 
	king_deals dl 
	JOIN king_dealitems i ON i.dealid=dl.dealid
	LEFT JOIN mp_loyaltypoint_brand_cat_config lp ON lp.brandid=dl.brandid AND lp.catid=dl.catid AND lp.is_active=1
	WHERE i.id='4875693632' OR i.pnh_id='4875693632';
	#pnhid:10035801
#============================

#SELECT i.id,i.price,i.pnh_id,i.name FROM king_dealitems i WHERE i.id='4875693632' OR i.pnh_id='4875693632'
#====================
SELECT * FROM king_deals LIMIT 10;

SELECT i.id AS itemid,i.member_price,i.mp_max_allow_qty,i.mp_frn_max_qty,i.mp_mem_max_qty,i.mp_is_offer,i.mp_offer_from,i.mp_offer_to,i.mp_offer_note
	,mpl.id AS logref_id,a.username AS created_by,mpl.created_on
	,IF(i.mp_offer_to IS NULL,i.mp_offer_to,IF(i.mp_offer_to > NOW(),1,0) ) AS validity
	FROM king_dealitems i
	LEFT JOIN deal_member_price_changelog mpl ON mpl.itemid=i.id AND mpl.is_active=1
	LEFT JOIN king_admin a ON a.id=mpl.created_by
	WHERE i.id='4875693632';
#=========================
SELECT i.id,i.price,i.pnh_id,i.name,dl.menuid,dl.brandid,dl.catid,IFNULL(lp.id,'') AS lpid,lp.offernote_tmpl FROM 
	king_deals dl 
	JOIN king_dealitems i ON i.dealid=dl.dealid
	LEFT JOIN mp_loyaltypoint_brand_cat_config lp ON lp.brandid=dl.brandid AND lp.catid=dl.catid AND lp.is_active=1
	WHERE i.id='4875693632' OR i.pnh_id='4875693632';
	
#=========================
#ttl results
SELECT COUNT(*) AS ttl
	FROM mp_loyaltypoint_brand_cat_config lp 
	JOIN king_brands b ON b.id=lp.brandid
	JOIN king_categories c ON c.id=lp.catid
	WHERE lp.brandid='23645763' AND lp.catid='131';
#details res
SELECT lp.id,lp.brandid,lp.catid,lp.is_active,DATE_FORMAT(lp.created_on,'%b/%d/%Y %H:%i:%s') AS created_on,lp.created_by,DATE_FORMAT(lp.modified_on,'%b/%d/%Y %H:%i:%s') AS modified_on,lp.modified_by,lp.offernote_tmpl
	,b.name AS brand,c.name AS category,lp.offernote_tmpl
	FROM mp_loyaltypoint_brand_cat_config lp 
	JOIN king_brands b ON b.id=lp.brandid
	JOIN king_categories c ON c.id=lp.catid
	WHERE lp.brandid='23645763' AND lp.catid='131'
	ORDER BY lp.brandid ASC,lp.catid ASC;
#=========================

#Aug_07_2014
SELECT * FROM king_categories;
SELECT * FROM mp_loyaltypoint_brand_cat_config;

#============ Towns allotted for vendor by brand =========================
#Aug_06_2014-Suresh
CREATE TABLE `m_vendor_town_link` (                       
                      `id` INT(11) NOT NULL AUTO_INCREMENT,                   
                      `brand_id` INT(11) DEFAULT NULL,                        
                      `vendor_id` INT(11) DEFAULT NULL,                       
                      `town_id` INT(11) DEFAULT NULL,                         
                      `is_active` TINYINT(1) DEFAULT '1',                     
                      `created_on` BIGINT(20) DEFAULT NULL,                   
                      `modified_on` BIGINT(20) DEFAULT NULL,                  
                      `modified_by` INT(11) DEFAULT '0',                      
                      PRIMARY KEY (`id`),                                     
                      UNIQUE KEY `id` (`id`)                                  
                    );
#============ Towns allotted for vendor by brand =========================
SELECT * FROM mp_loyaltypoint_brand_cat_config WHERE id='1';

SELECT COUNT(*) AS ttl
	FROM mp_loyaltypoint_brand_cat_config lp 
	JOIN pnh_menu m ON m.id=lp.menuid
	JOIN king_brands b ON b.id=lp.brandid
	JOIN king_categories c ON c.id=lp.catid
	LEFT JOIN king_admin a ON a.id=lp.created_by
	WHERE 1

SELECT * FROM mp_loyaltypoint_brand_cat_config WHERE id='0'  OR (brandid=Array AND catid=


#Aug_08_2014

SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid
	
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE di.id='5319594831'
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY o.time DESC;
	
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE mplog.itemid='5319594831'
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY o.time DESC;
	

#============ Partner Deal Link table =========================
#Aug_07_2014-Suresh
CREATE TABLE `m_partner_deal_link` (    
                       `id` INT(11) NOT NULL,                
                       `itemid` BIGINT(20) DEFAULT NULL,     
                       `asin` VARCHAR(255) DEFAULT NULL,     
                       `fsin` VARCHAR(255) DEFAULT NULL,     
                       `created_by` INT(11) DEFAULT NULL,    
                       `created_on` DATE DEFAULT NULL,       
                       PRIMARY KEY (`id`)                    
                     );
#============ Partner Deal Link table =========================	

SELECT a.id AS item_id,pnh_id AS pid,orgprice,price,publish,live FROM king_dealitems a JOIN king_deals b ON a.dealid = b.dealid 
WHERE pnh_id = '11314779' AND a.id = '9763765471'# and publish = 1;
{"status":"error","error_code":2046,"error_msg":"Sold Out","response":{"error_code":2046,"error_msg":"Sold Out"}}


#==============
SELECT free_frame,has_power,b.shipsin,mp_offer_note,b.id AS item_id,b.pnh_id AS pid,menuid AS pmenu_id,e.name AS pmenu,b.name AS pname,catid AS pcat_id,c.name AS pcat,
	cp.id AS parent_cat_id,cp.name AS parent_cat,brandid AS pbrand_id,d.name AS pbrand,
	b.orgprice AS pmrp,b.price AS pprice,publish AS is_sourceable,
	b.gender_attr,b.url,b.is_combo,a.description AS pdesc,a.keywords AS kwds,
	a.pic AS pimg,menuid AS pimg_path,b.shipsin AS shipin,
	b.member_price,b.mp_frn_max_qty,b.mp_mem_max_qty
FROM king_deals a
JOIN king_dealitems b ON a.dealid = b.dealid
JOIN king_categories c ON c.id = a.catid
LEFT JOIN king_categories cp ON cp.id = c.type
JOIN king_brands d ON d.id = a.brandid
JOIN pnh_menu e ON e.id = a.menuid
WHERE b.id = '9763765471' AND b.pnh_id != 0 AND publish = 1 AND live = 1 
#===============================================
#Aug_08_2014
ALTER TABLE king_dealitems ADD COLUMN free_frame TINYINT(1) DEFAULT 0;
ALTER TABLE king_dealitems ADD COLUMN has_power TINYINT(1) DEFAULT 0;
ALTER TABLE `king_orders` ADD COLUMN `mp_loyalty_points` DOUBLE(10,2) DEFAULT 0 NULL AFTER `mp_logid`;
#===============================================

#Aug_09_2014
SELECT * FROM king_transactions WHERE transid='PNH64296';
SELECT * FROM king_orders WHERE transid='PNH39176';
SELECT * FROM king_orders WHERE transid='PNH28358';
SELECT * FROM king_orders WHERE transid='PNH88722';
#============<< NEW-Insurance SMS >>===================
#Aug_09_2014
SELECT mo.member_id,mo.franchise_id,mo.offer_type,mo.order_id,mo.transid_ref,mo.insurance_id
	,mi.first_name,ins.first_name AS ins_first_name,ins.mob_no,ins.city
	,f.franchise_name,f.login_mobile1
FROM pnh_member_offers mo
JOIN pnh_member_insurance ins ON ins.sno = mo.insurance_id
JOIN pnh_m_franchise_info f ON f.franchise_id=mo.franchise_id
JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id
WHERE ins.status_ship = 0 AND mo.insurance_id='815' #and  mo.sno ='162';
#============<< NEW-Insurance SMS >>===================
SELECT * FROM pnh_member_offers mo
JOIN pnh_member_insurance ins ON ins.sno = mo.insurance_id
WHERE mo.insurance_id='815';

SELECT mo.member_id,mo.franchise_id,mo.offer_type,mo.order_id,mo.transid_ref,mo.insurance_id
									,mi.first_name,ins.first_name AS ins_first_name,ins.mob_no,ins.city
									,f.franchise_name,f.login_mobile1
								FROM pnh_member_offers mo
								JOIN pnh_member_insurance ins ON ins.sno = mo.insurance_id
								JOIN pnh_m_franchise_info f ON f.franchise_id=mo.franchise_id
								JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id
								WHERE ins.status_ship = 0 AND mo.sno = '1004';
								
#Aug_11_2014

SELECT * FROM king_transactions ORDER BY id DESC LIMIT 5;


SELECT COUNT(*) AS t FROM pnh_member_info mi
	JOIN pnh_member_fee fee ON fee.member_id = mi.pnh_member_id AND fee.status=1
	JOIN king_orders o ON o.transid = fee.transid AND o.status != 3
	WHERE mi.created_on > DATE('2014-01-01') AND fee.member_id='21111152';
	
SELECT * FROM pnh_member_fee fee WHERE fee.member_id='21111111';
SELECT * FROM pnh_member_info ORDER BY id DESC; WHERE pnh_member_id='21911182';

SELECT * FROM pnh_member_info 
#WHERE pnh_member_id='21111111' 
ORDER BY id DESC;

SELECT UNIX_TIMESTAMP('2013-01-01'); =>1356978600
SELECT DATE(FROM_UNIXTIME(1356978600))

SELECT * FROM pnh_member_info WHERE created_on=0 IS NULL

SELECT COUNT(*) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.member_id='21111111';

SELECT COUNT(*) AS is_rechrge_gv FROM pnh_member_offers mo
		LEFT JOIN king_orders o ON o.id=mo.order_id AND o.status!=3
		WHERE mo.offer_type=1 AND mo.offer_type  IN (0,2,3) AND mo.member_id='21111111'
		HAVING is_rechrge_gv IS NOT NULL;
		 #AND mo.process_status!=1
SELECT * FROM pnh_member_offers mo WHERE mo.member_id='21111111';

SELECT * FROM king_orders WHERE id='9514723899';

#Aug_12_2014

SELECT * FROM m_partner_deal_link;
m_partner_deal_link
m_partner_deal_price;
#ALTER TABLE `m_partner_deal_link` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `asin` `partner_ref_no` VARCHAR(255) CHARSET utf16 COLLATE utf16_general_ci NULL, CHANGE `fsin` `partner_id` INT(11) NULL;
#ALTER TABLE `m_partner_deal_link` CHANGE `asin` `partner_ref_no` VARCHAR(255) CHARSET utf16 COLLATE utf16_general_ci NULL, CHANGE `fsin` `partner_id` INT(11) NULL;
#INSERT INTO `m_partner_deal_link` (`itemid`, `asin`, `fsin`, `created_by`, `created_on`) VALUES ('2616472368', 'az111', 'fb111', '37', '2014-08-12');

#==========================
#Aug_12_2014
DROP TABLE `m_partner_deal_link`;

CREATE TABLE `m_partner_deal_link` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `itemid` BIGINT(20) DEFAULT NULL,
  `partner_ref_no` VARCHAR(255) DEFAULT NULL,
  `partner_id` INT(11) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);
#id	itemid		partner_ref_no	partner_id	created_by	created_on
#1	2616472368	az111		7		37		2014-08-12 20:10:26
ALTER TABLE `partner_info` ADD COLUMN `partner_sku_text` VARCHAR(20) NULL AFTER `trans_mode`;
#==========================

SELECT * 
FROM m_partner_deal_link prl
JOIN king_dealitems di ON di.id=prl.itemid
WHERE 1 AND prl.itemid='' OR prl.partner_ref_no='ASN345546546' OR di.name LIKE '%Himalaya Baby Cream - 100ml%';

#===============================
#Aug_13_29014
SELECT prl.id,prl.partner_ref_no,prl.itemid,prl.partner_id,di.name,di.dealid,di.orgprice AS mrp,di.price,di.member_price,di.pic,di.is_group
	FROM m_partner_deal_link prl
	JOIN king_dealitems di ON di.id=prl.itemid
	WHERE 1  AND prl.itemid='AWS743894' OR prl.partner_ref_no='AWS743894' OR di.name LIKE 'AWS743894';
	
SELECT * FROM m_product_deal_link;


SELECT prl.id,prl.partner_ref_no,prl.itemid,prl.partner_id,di.name,di.dealid,di.orgprice AS mrp,di.price,di.member_price,di.pic,di.is_group
	,GROUP_CONCAT(pdl.product_id) AS product_ids,COUNT(DISTINCT pdl.id) AS num_prdt
	FROM m_partner_deal_link prl
	JOIN king_dealitems di ON di.id=prl.itemid
	JOIN m_product_deal_link pdl ON pdl.itemid=di.id AND pdl.is_active=1
	#WHERE 1  AND prl.itemid='AWS743894' OR prl.partner_ref_no='AWS743894' OR di.name LIKE 'AWS743894'
	GROUP BY prl.itemid;
	
SELECT * FROM king_dealitems WHERE is_combo=1 LIMIT 10;

#==========================
#Aug_13_2014
ALTER TABLE `partner_info` ADD COLUMN `partner_rackbinid` VARCHAR(20) NULL AFTER `partner_sku_text`;
#Aug_14_2014
ALTER TABLE king_orders ADD COLUMN lens_item_orderdet VARCHAR(255) DEFAULT '';
ALTER TABLE king_dealitems ADD COLUMN lens_type VARCHAR(255) DEFAULT '' AFTER has_power;
#==========================

SELECT DISTINCT transid,COUNT(*) AS t,partner_reference_no FROM king_transactions 
WHERE 1
#and partner_reference_no = ?
AND partner_id = 7
GROUP BY transid;

SELECT * FROM king_orders WHERE transid='PNHIYR69167'

SELECT * 
FROM king_transaction tr
JOIN ON 

DESC 
king_orders

SELECT di.pnh_id,f.franchise_id,o.member_id#,f.price_type,di.name,o.quantity,o.i_orgprice,o.i_price,di.member_price
FROM king_transactions tr
JOIN  king_orders o ON o.transid=tr.transid
JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
JOIN king_dealitems di ON di.id=o.itemid
WHERE f.price_type=1 AND tr.transid='PNH88722';


#Aug_16_2014


#==========================Confirm orders on credit Limit approval==============================================================#
#author roopa #11_august_2014
/*[4:07:43 PM][84 ms]*/ ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `max_credit_limit` DOUBLE DEFAULT 0 NULL AFTER `is_consolidated_payment`;
/*[5:18:44 PM][385 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `redeem_value` DOUBLE DEFAULT 0 NULL AFTER `partner_reference_no`; 
 /*[5:21:56 PM][310 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `is_memberprice` TINYINT DEFAULT 0 NULL AFTER `redeem_value`, ADD COLUMN `other_price` DOUBLE DEFAULT 0 NULL AFTER `is_memberprice`, ADD COLUMN `mp_logid` BIGINT(11) NULL AFTER `other_price`, ADD COLUMN `mp_loyalty_points` BIGINT(11) NULL AFTER `mp_logid`, ADD COLUMN `s_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `mp_loyalty_points`, ADD COLUMN `is_ordqty_splitd` TINYINT(1) DEFAULT 0 NULL AFTER `s_consolidated_payment`, ADD COLUMN `pnh_member_fee` DOUBLE DEFAULT 0 NULL AFTER `is_ordqty_splitd`, ADD COLUMN `insurance_amount` DOUBLE DEFAULT 0 NULL AFTER `pnh_member_fee`; 
 /*[5:23:13 PM][417 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `billon_orderprice` TINYINT DEFAULT 0 NULL AFTER `insurance_amount`; 
 /*[5:23:40 PM][341 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `member_id` BIGINT(11) NULL AFTER `billon_orderprice`; 
 /*[5:24:31 PM][58 ms]*/ ALTER TABLE `king_tmp_orders` CHANGE `s_consolidated_payment` `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL; 
 /*[12:38:42 PM][44 ms]*/ 

CREATE TABLE `king_tmp_transactions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transid` CHAR(18) NOT NULL,
  `orderid` BIGINT(20) UNSIGNED NOT NULL,
  `amount` DOUBLE UNSIGNED NOT NULL,
  `paid` DOUBLE UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL,
  `voucher_payment` TINYINT(3) DEFAULT '0',
  `cod` DOUBLE UNSIGNED NOT NULL,
  `ship` DOUBLE UNSIGNED NOT NULL,
  `giftwrap_charge` DOUBLE DEFAULT '0',
  `response_code` INT(10) UNSIGNED NOT NULL,
  `msg` TEXT NOT NULL,
  `payment_id` VARCHAR(50) NOT NULL,
  `pg_transaction_id` VARCHAR(50) NOT NULL,
  `is_flagged` VARCHAR(10) NOT NULL,
  `init` BIGINT(20) UNSIGNED NOT NULL,
  `actiontime` INT(10) UNSIGNED NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL,
  `is_pnh` TINYINT(1) NOT NULL,
  `franchise_id` INT(10) UNSIGNED NOT NULL,
  `credit_days` INT(11) DEFAULT '0',
  `order_for` INT(11) NOT NULL DEFAULT '0',
  `pnh_member_fee` DOUBLE DEFAULT '0',
  `credit_remarks` VARCHAR(255) DEFAULT NULL,
  `batch_enabled` TINYINT(1) NOT NULL DEFAULT '1',
  `admin_trans_status` TINYINT(3) DEFAULT '0',
  `priority` TINYINT(1) NOT NULL,
  `priority_note` VARCHAR(200) NOT NULL,
  `note` TEXT NOT NULL,
  `offline` TINYINT(1) NOT NULL,
  `status_backup` TINYINT(1) DEFAULT NULL,
  `partner_reference_no` VARCHAR(100) NOT NULL,
  `partner_id` INT(10) UNSIGNED NOT NULL,
  `trans_created_by` INT(11) DEFAULT '0',
  `trans_grp_ref_no` BIGINT(11) DEFAULT '0',
  `is_memberprice` TINYINT(1) DEFAULT '0',
  `approval_status` TINYINT(1) DEFAULT '0' COMMENT '0: Waiting FOR approval,1: Approved,2: Rejected',
  `approved_by` INT(11) DEFAULT '0',
  `approved_on` BIGINT(20) DEFAULT '0',
  `rejected_by` INT(11) DEFAULT '0',
  `rejected_on` BIGINT(20) DEFAULT '0',
  `remarks` TEXT,
  PRIMARY KEY (`id`),
  KEY `transid` (`transid`),
  KEY `franchise_id` (`franchise_id`),
  KEY `trans_created_by` (`trans_created_by`)
); 
 /*[7:15:31 PM][680 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `has_insurance` TINYINT(1) DEFAULT 0 NULL AFTER `pnh_member_fee`; 
 ========================
 #============ PO IS_DOA,Return_id column structure  =========================
#Aug_08_2014-Suresh
ALTER TABLE `t_po_info` ADD COLUMN `is_doa_po` TINYINT (1) DEFAULT '0' NULL  AFTER `paf_id`;
ALTER TABLE `t_po_info` ADD COLUMN `return_id` BIGINT (11)  NULL  AFTER `is_doa_po`;
#============ PO IS_DOA,Return_id column structure  =========================

ALTER TABLE `pnh_invoice_returns_product_service` ADD COLUMN `job_sheet_no` BIGINT (11)  NULL  AFTER `sent_to`;
ALTER TABLE `pnh_invoice_returns_product_service` ADD COLUMN `sent_with` VARCHAR (255)  NULL  AFTER `sent_on`;
ALTER TABLE `pnh_invoice_returns_product_service` ADD COLUMN `doa_copy_no` BIGINT (11)  NULL  AFTER `service_return_on`;
ALTER TABLE `pnh_invoice_returns_product_service` ADD COLUMN doa_copy_filepath VARCHAR (255)  NULL  AFTER doa_copy_no;


ALTER TABLE `t_imei_no` ADD COLUMN `is_doa` TINYINT (1) DEFAULT '0' NULL  AFTER `is_imei_activated`;
ALTER TABLE `pnh_invoice_returns_product_link` ADD COLUMN `doa_po_id` BIGINT (11)  NULL  AFTER `condition_type`

SELECT * FROM partner_info 

ALTER TABLE partner_info CHANGE partner_rackbinid partner_rackbin_id INT(11) DEFAULT 0   

UPDATE partner_info  SET partner_rackbin_id = 0


SELECT * FROM t_stock_info WHERE product_id = 4299

SELECT * FROM king_transactions WHERE transid='AMZ25889';

SELECT * FROM king_orders WHERE transid='AMZ25889';

SELECT price FROM king_dealitems WHERE id='9142873414'


SELECT in.invoice_no,in.mrp,item.nlc,item.phc,ordert.*,
							item.service_tax_cod,item.name,IF(LENGTH(item.print_name),item.print_name,item.name) AS print_name,in.invoice_no,
							brand.name AS brandname,
							in.tax AS tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
							item.pnh_id,f.offer_text,f.immediate_payment,
							in.invoice_qty AS quantity,
							ordert.member_id  AS alloted_mem_id,ordert.has_insurance,ordert.insurance_amount
						FROM king_orders AS ordert
						JOIN king_dealitems AS item ON item.id=ordert.itemid 
						JOIN king_deals AS deal ON deal.dealid=item.dealid 
						LEFT JOIN king_brands AS brand ON brand.id=deal.brandid 
						LEFT JOIN pnh_m_offers f ON f.id= ordert.offer_refid
						#left join pnh_member_offers mo on mo.transid_ref = ordert.transid 
						JOIN king_invoice `in` ON in.transid=ordert.transid AND in.order_id=ordert.id  
						WHERE in.invoice_no='20142054538' #or split_inv_grpno = ''

SELECT * FROM king_invoice WHERE invoice_no='20142054538';


SELECT d.catid,d.menuid,d.brandid,t.is_pnh,i_discount,i_coup_discount,i.tax,t.is_pnh,o.transid,o.id,o.itemid,i_orgprice,i_price,t.cod,t.ship,o.quantity,SUM(i.orgprice*o.quantity) AS mrp_amt,SUM(i.price*o.quantity) AS price_amt,o.other_price,o.is_memberprice 
										FROM king_orders o 
										JOIN king_dealitems i ON i.id=o.itemid 
										JOIN king_transactions t ON t.transid = o.transid
										JOIN king_deals d ON d.dealid = i.dealid 
										WHERE o.id IN ('4631839997')
										GROUP BY o.id ORDER BY o.sno ASC;
										
SELECT * FROM `t_reserved_batch_stock` WHERE batch_id='8579';

SELECT * FROM shipment_batch_process_invoice_link WHERE invoice_no='20142054538';

SELECT * FROM t_stock_info WHERE stock_id='297206'

SELECT order_id,mrp,inv_qty,ROUND(mrp*a_disc_perc,2) AS disc FROM (
	SELECT a.id,order_id,a.product_id,mrp,((qty+extra_qty)-release_qty) AS inv_qty,(i_discount+i_coup_discount) AS a_disc,((i_discount+i_coup_discount)/i_orgprice) AS a_disc_perc 
		FROM t_reserved_batch_stock a
		JOIN t_stock_info b ON a.stock_info_id = b.stock_id
		JOIN king_orders c ON c.id = a.order_id 
		WHERE p_invoice_no = '' AND a.status = 1 AND a.order_id = ''
	) AS g;
	

 #Aug_18_2014
 
SELECT * FROM king_transactions WHERE partner_id='7';

SELECT * FROM m_partner_deal_link

SELECT * FROM t_partner_transfer_request;

#===================================================
ALTER TABLE `king_tmp_orders` ADD COLUMN `has_insurance` TINYINT(1) DEFAULT 0 NULL AFTER `pnh_member_fee`;
 ALTER TABLE `king_tmp_orders` ADD COLUMN `approval_status` TINYINT(1) DEFAULT 0 NULL AFTER `member_id`; 

 /*[12:00:26 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,NULL,NULL,NULL,'4cecb21b44628b17c436739bf6301af2','','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','1',NULL,NULL); 
/*[12:01:17 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,NULL,'SMS','SMS','4cecb21b44628b17c436739bf6301af2','','0','0','0','','franchise@storeking.in',NULL,NULL,NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:02:52 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,NULL,NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:03:03 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,'',NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:03:11 PM][90 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,'',NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52','2014-08-18 12:03:08'); 
 
// Partner FBA ORDER IMPORT CHANGES
ALTER TABLE `t_trans_invoice_marker` ADD COLUMN `partner_id` INT (5) DEFAULT '0' NULL  AFTER `id`;
INSERT INTO `t_trans_invoice_marker`(`id`,`partner_id`,`transid`,`invoice_no`,`is_pnh`,`created_on`)VALUES(NULL,'7','','10700000000','0',NOW());
#================================================

SELECT * FROM `partner_info`;
SELECT * FROM `m_rack_bin_info`;


SELECT * FROM partner_deal_prices WHERE itemid = ? AND partner_id = ? AND partner_price != 0 ;

# Aug_19_2014

/*[12:00:26 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,NULL,NULL,NULL,'4cecb21b44628b17c436739bf6301af2','','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','1',NULL,NULL); 
/*[12:01:17 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,NULL,'SMS','SMS','4cecb21b44628b17c436739bf6301af2','','0','0','0','','franchise@storeking.in',NULL,NULL,NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:02:52 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,NULL,NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:03:03 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,'',NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52',NULL); 
/*[12:03:11 PM][90 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,'4cecb21b44628b17c436739bf6301af2','SMS','SMS','4cecb21b44628b17c436739bf6301af2','1','0','0','0','','franchise@storeking.in',NULL,'',NULL,NULL,NULL,NULL,'0','1','2014-08-18 12:00:52','2014-08-18 12:03:08'); 
 CREATE TABLE `pnh_t_max_credit_info` (
  `id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `franchise_id` INT(11) DEFAULT NULL,
  `credit_added` DOUBLE DEFAULT NULL,
  `new_credit_limit` DOUBLE DEFAULT NULL,
  `credit_given_by` INT(11) DEFAULT NULL COMMENT 'executive id',
  `reason` VARCHAR(200) NOT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  `created_on` BIGINT(20) DEFAULT NULL,
  `modified_on` BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
 
 SELECT * FROM m_product_deal_link;
 
 
 SELECT * FROM king_transactions WHERE transid='AMZ43772';
 
 SELECT * FROM m_rack_bin_info;
 
 SELECT * FROM partner_info
 
 #Aug_20_2014
 /*[4:23:27 PM][463 ms]*/ ALTER TABLE `pnh_member_info` ADD COLUMN `mem_image_url` VARCHAR(255) NULL AFTER `voucher_bal_validity`; 
//Franchise OTP TABLE
CREATE TABLE `t_franchise_otp` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `fid` BIGINT(20) DEFAULT NULL,
  `otp_no` BIGINT(20) DEFAULT NULL,
  `logged_on` BIGINT(20) DEFAULT NULL,
  `valid_till` BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

#Aug_21_2014

SELECT IFNULL(SUM(available_qty),0) AS s FROM t_stock_info t JOIN m_rack_bin_info rb ON rb.id = t.rack_bin_id WHERE product_id='3035'  AND t.rack_bin_id = 120;

SELECT * FROM pnh_member_insurance

SELECT offer_type,insurance_id,created_on FROM pnh_member_offers WHERE insurance_id!='' ORDER BY offer_type DESC LIMIT 5000; #where offer_type=;

SELECT * FROM support_tickets;

SELECT * FROM user_access_roles;
#====================================
#Aug_22_2014
UPDATE `user_access_roles` SET `user_role` = 'UPDATE_PRODUCT_DEAL_ROLE' , `const_name` = 'UPDATE_PRODUCT_DEAL_ROLE' WHERE `id` = '18';
#ALTER TABLE `pnh_api_franchise_cart_info` ADD COLUMN `cartid` BIGINT(20) NULL AFTER `updated_on`;
ALTER TABLE `king_transactions` ADD COLUMN `api_cartid` VARCHAR(100) DEFAULT '0' NULL AFTER `trans_grp_ref_no`;
ALTER TABLE king_orders ADD COLUMN lens_package_id INT(11) DEFAULT 0 AFTER is_consolidated_payment;
ALTER TABLE king_orders ADD COLUMN lens_package_price DOUBLE DEFAULT 0 AFTER lens_package_id;
#====================================

SELECT pid,qty,attributes FROM pnh_api_franchise_cart_info WHERE franchise_id='498' AND STATUS=1;

SELECT * FROM pnh_api_franchise_cart_info ORDER BY id DESC;

SELECT * FROM pnh_member_info WHERE (pnh_member_id = '22222222' OR mobile = '22222222')

/*[6:51:45 PM][648 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `insurance_id` BIGINT(20) DEFAULT 0 NULL AFTER `approval_status`; 
/*[6:52:17 PM][585 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `imei_scheme_id` BIGINT(20) DEFAULT 0 NULL AFTER `insurance_id`; 
/*[6:52:47 PM][600 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `imei_reimbursement_value_perunit` DOUBLE NULL AFTER `imei_scheme_id`; 
/*[6:53:18 PM][575 ms]*/ ALTER TABLE `king_tmp_orders` ADD COLUMN `loyality_point_value` DOUBLE NULL AFTER `imei_reimbursement_value_perunit`; 

SELECT * FROM king_transactions WHERE transid='PNH28181'

SELECT * FROM king_transactions WHERE transid='PNH69156'

SELECT * FROM king_transactions WHERE transid='PNH27948'; # 752409140877

172155140878
SELECT * FROM king_transactions WHERE transid='PNH12928';#'PNH68953'; # 752409140877

SELECT * FROM king_transactions ORDER BY id DESC LIMIT 10;

SELECT COUNT(*) AS t FROM king_transactions WHERE api_cartid='172155140878';

PNH12928;

#Aug_25_2014

SELECT prl.id,prl.partner_ref_no,prl.itemid,prl.partner_id,di.name,di.dealid,di.orgprice AS mrp,di.price,di.member_price,CONCAT('".IMAGES_URL."items/small/',di.pic,'.jpg') AS image_url,di.is_group,di.pnh_id
	,GROUP_CONCAT(pdl.product_id) AS product_ids,COUNT(DISTINCT pdl.id) AS num_prdt
	,p.name AS partner_name,trans_prefix,p.partner_rackbinid,p.trans_mode
	FROM m_partner_deal_link prl
	JOIN partner_info p ON p.id = prl.partner_id
	JOIN king_dealitems di ON di.id=prl.itemid
	JOIN m_product_deal_link pdl ON pdl.itemid=di.id AND pdl.is_active=1
	WHERE 1  AND prl.itemid='AWS743894' OR prl.partner_ref_no='AWS743894' OR di.name LIKE '%AWS743894%' 
	GROUP BY prl.itemid;
	
SELECT * FROM partner_info
SELECT * FROM m_partner_deal_link;

AWS743894, ASN345546546, ASN3455465434, AZ89856567899, AMZQDZ49537

SELECT SUM(available_qty)  AS t
	FROM t_stock_info  a
	JOIN m_storage_location_info b ON b.location_id = a.location_id
	JOIN m_rack_bin_info c ON c.id = a.rack_bin_id
	WHERE product_id='5274' AND c.is_damaged=0
	HAVING t >=0;
	
SELECT SUM(a.available_qty) AS s,a.mrp,
									a.location_id,a.rack_bin_id,
									CONCAT(c.rack_name,c.bin_name) AS rbname,c.is_damaged,
									IFNULL(product_barcode,'') AS pbarcode,
									a.stock_id,DATE_FORMAT(a.expiry_on, '%d-%m-%Y') AS expiry_on,offer_note     
									FROM t_stock_info  a 
									JOIN m_storage_location_info b ON b.location_id = a.location_id 
									JOIN m_rack_bin_info c ON c.id = a.rack_bin_id  
									WHERE product_id='5274' AND c.is_damaged=0
									GROUP BY a.mrp,pbarcode,a.location_id,a.rack_bin_id 
									HAVING s >=0 
									ORDER BY a.mrp ASC;
									
#Aug_26_2014
									
#=================================================================
#Aug_26_2014
ALTER TABLE `king_invoice` CHANGE `discount` `discount` DECIMAL(10,2) UNSIGNED NULL;
#=================================================================


SELECT * FROM king_dealitems WHERE is_combo=1;
normal deal:
AWS743894, ASN345546546, ASN3455465434, AZ89856567899, AMZQDZ49537
Combo deals:
5365274322
4996556468
8317261816

SELECT * FROM m_partner_deal_link;

SELECT SUM(available_qty)  AS t,product_id
	FROM t_stock_info  a
	JOIN m_storage_location_info b ON b.location_id = a.location_id
	JOIN m_rack_bin_info c ON c.id = a.rack_bin_id
	WHERE product_id IN ('5313','5323','5333','5351','5319') AND c.is_damaged=0
	HAVING t >0


SELECT * FROM m_partner_deal_link;


#Aug_27_2014

normal deal:
AWS743894, ASN345546546, ASN3455465434, AZ89856567899, AMZQDZ49537
Combo deals:
5365274322
4996556468
8317261816



SELECT * FROM m_partner_deal_link;

SELECT * FROM t_partner_transfer_request;

#===============================
#Aug_27_2014
ALTER TABLE king_invoice ADD COLUMN partner_invno VARCHAR(50) DEFAULT '';
ALTER TABLE king_invoice ADD COLUMN partner_invdate VARCHAR(50) DEFAULT '';
# Coupans table
CREATE TABLE `king_coupons` (                                
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,          
	`code` CHAR(12) NOT NULL,                                  
	`type` TINYINT(3) UNSIGNED NOT NULL,                       
	`value` INT(10) UNSIGNED NOT NULL,                         
	`brandid` VARCHAR(200) NOT NULL,                           
	`catid` VARCHAR(200) NOT NULL,                             
	`mode` TINYINT(3) UNSIGNED NOT NULL,                       
	`userid` BIGINT(20) UNSIGNED NOT NULL,                     
	`itemid` BIGINT(20) UNSIGNED NOT NULL,                     
	`status` TINYINT(3) UNSIGNED NOT NULL,                     
	`min` INT(10) UNSIGNED NOT NULL,                           
	`used` INT(10) UNSIGNED NOT NULL,                          
	`unlimited` TINYINT(1) NOT NULL,                           
	`referral` BIGINT(20) UNSIGNED NOT NULL,                   
	`created` BIGINT(20) UNSIGNED NOT NULL,                    
	`expires` BIGINT(20) UNSIGNED NOT NULL,                    
	`lastused` BIGINT(20) UNSIGNED NOT NULL,                   
	`gift_voucher` TINYINT(1) NOT NULL,                        
	`remarks` VARCHAR(255) DEFAULT NULL,                       
	PRIMARY KEY (`id`),                                        
	UNIQUE KEY `code` (`code`),                                
	KEY `brandid` (`brandid`),                                 
	KEY `catid` (`catid`),                                     
	KEY `itemid` (`itemid`)                                    
      );
#Partner stk transfer log table-Shivaraj
CREATE TABLE `t_partner_transfer_request`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `transfer_id` VARCHAR(50), `reference_no` VARCHAR(50), `itemid` VARCHAR(50), `product_id` INT(50), `stock_id` INT(50), `transfer_qty` INT(11), `from_loc` VARCHAR(20), `to_loc` VARCHAR(20), `from_rackbin` VARCHAR(20), `to_rackbin` VARCHAR(20), `barcode` VARCHAR(20), `mrp` DOUBLE(10,2), `transfer_remarks` TEXT, `transfer_exp_date` DATETIME, `type` INT(5), `created_by` INT(11), `created_on` DATETIME, PRIMARY KEY (`id`) );
#===============================




SELECT * FROM king_transactions 
#where transid='AMZ47174'
ORDER BY id DESC LIMIT 35;

SELECT * FROM partner_deal_prices WHERE itemid = '6228731442' AND partner_id = '7' AND partner_price != 0;

shipment_batch_process_invoice_link;
#suresh Aug 25 2014
ALTER TABLE `king_dealitems` ADD COLUMN `powered_by` VARCHAR (100)  NULL  AFTER `has_insurance`;
#author Suresh #21_august_2014
CREATE TABLE `pnh_invoice_returns_images` (  `id` INT (11) NOT NULL AUTO_INCREMENT , `return_id` BIGINT (20) , `invoice_no` BIGINT (20) , `pic` CHAR (50) , `created_on` DATETIME , PRIMARY KEY ( `id`))  
#==========================Return Shipment Track table structure==============================================================#
CREATE TABLE `t_shipment_tracking_info` (  `id` INT (11) NOT NULL AUTO_INCREMENT , `invoice_no` BIGINT (20) , `product_id` INT (11) , `return_id` BIGINT (20) ,`return_prod_id` BIGINT (20) , `status` TINYINT (1) DEFAULT '0', `transit_type` TINYINT (1) DEFAULT '1', `courier_name` VARCHAR (255) , `courier_awb` VARCHAR (255) , `emp_name` VARCHAR (255) , `emp_phno` VARCHAR (255) , `remarks` VARCHAR (255) , PRIMARY KEY ( `id`));  
ALTER TABLE `t_shipment_tracking_info` ADD COLUMN `order_id` BIGINT (20)  NULL  AFTER `invoice_no`;
#==========================Return Shipment Track table structure==============================================================#

#==========================PO table structure==============================================================#
ALTER TABLE `t_po_info` ADD COLUMN `credit_days` INT (11) DEFAULT '0' NULL  AFTER `payment_status`;
#==========================PO table structure==============================================================#
/*[12:00:26 PM][0 ms]*/ INSERT INTO `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`role_id`,`access`,`brandid`,`fullname`,`email`,`mobile`,`phone`,`gender`,`address`,`city`,`img_url`,`account_blocked`,`block_ip_addr`,`createdon`,`modifiedon`) VALUES ( NULL,NULL,NULL,NULL,'4cecb21b44628b17c436739bf6301af2','','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0','1',NULL,NULL); 

partner_transfer_request


DESC partner_info 

#author Suresh #21_august_2014
CREATE TABLE `pnh_invoice_returns_images` (  `id` INT (11) NOT NULL AUTO_INCREMENT , `return_id` BIGINT (20) , `invoice_no` BIGINT (20) , `pic` CHAR (50) , `created_on` DATETIME , PRIMARY KEY ( `id`))  

#Aug_30_2014

SELECT * FROM king_dealitems WHERE is_combo=1;
SELECT * FROM m_partner_deal_link;

2762354616
5521651316
3471838221
#-==============================
#Aug_30_2014
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `tab_simno` VARCHAR(50) NULL AFTER `max_credit_limit`;
#-==============================

SELECT * FROM m_product_deal_link WHERE itemid = 5365274322;

DESC t_partner_transfer_request

=>t_partner_stock_transfer=>transfer_id,partner_id,partner_transfer_no,transfer_remarks,scheduled_transfer_date,transfer_status,transfer_date,transfer_by
=>t_partner_stock_transfer_product_link=>id,transfer_id,itemid,product_id,from_stock_info_id,to_stock_info_id,product_transfer_qty,item_transfer_qty,transfer_status = 0

SELECT * FROM m_product_deal_link
SELECT * FROM t_imei_no
SELECT * FROM m_partner_deal_link

SELECT prl.id,prl.partner_ref_no,prl.itemid,prl.partner_id,di.name,di.dealid,di.orgprice AS mrp,di.price,di.member_price,CONCAT('http://static.snapittoday.com/items/small/',di.pic,'.jpg') AS image_url,di.is_group,di.pnh_id
	,GROUP_CONCAT(pdl.product_id) AS product_ids
	,p.name AS partner_name,trans_prefix,p.partner_rackbinid,p.trans_mode
	FROM m_partner_deal_link prl
	JOIN partner_info p ON p.id = prl.partner_id
	JOIN king_dealitems di ON di.id=prl.itemid
	LEFT JOIN m_product_deal_link pdl ON pdl.itemid=di.id #AND pdlmax_credit_limit.is_active=1
	LEFT JOIN t_imei_no imei ON imei.product_id=pdl.product_id #and imei.status=1
	WHERE 1  AND prl.itemid='8904132422957' OR prl.partner_ref_no='8904132422957' OR di.name LIKE '%8904132422957%' OR imei.imei_no='8904132422957' 
	GROUP BY prl.itemid;
	

normal deal:
AWS743894, ASN345546546, ASN3455465434, AZ89856567899, AMZQDZ49537
Combo deals:
5365274322
4996556468
8317261816

#Aug_
SELECT credit_limit,max_credit_limit,opt_maxcreditlimit FROM pnh_m_franchise_info WHERE franchise_id ='59';

ALTER TABLE king_tmp_orders ADD COLUMN `order_product_id` BIGINT(11) DEFAULT '0' AFTER `itemid`;
ALTER TABLE king_tmp_orders ADD COLUMN `order_from_vendor` INT(11) DEFAULT NULL AFTER order_product_id;
ALTER TABLE king_tmp_orders ADD COLUMN `vendor_order_ref_id` INT(11) DEFAULT NULL AFTER order_from_vendor;
ALTER TABLE king_tmp_orders ADD COLUMN `lens_item_orderdet` TEXT DEFAULT NULL AFTER vendor_order_ref_id;
ALTER TABLE king_tmp_orders ADD COLUMN `lens_package_id` INT(11) DEFAULT '0' AFTER lens_item_orderdet;
ALTER TABLE king_tmp_orders ADD COLUMN `customer_prescrption` TEXT DEFAULT NULL AFTER lens_package_id;
ALTER TABLE king_tmp_orders ADD COLUMN `lens_package_price` DOUBLE DEFAULT '0' AFTER customer_prescrption; 

#-==============================
#Sep_01_2014
ALTER TABLE `pnh_api_franchise_cart_info` ADD COLUMN `partner_id` INT(11) NULL AFTER `cartid`, `ref_itemid` BIGINT(20) NULL AFTER `pid`;
#-==============================

SELECT COUNT(*) AS ttl FROM pnh_api_franchise_cart_info WHERE STATUS=1 AND partner_id=0;

SELECT * FROM pnh_api_franchise_cart_info WHERE user_id='37',partner_id='7' AND ref_itemid='1669462818' AND AND STATUS=1
SELECT d.menuid FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid LEFT JOIN king_brands b ON b.id = d.brandid JOIN king_categories c ON c.id = d.catid WHERE i.id='1669462818'
SELECT has_insurance FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid LEFT JOIN king_brands b ON b.id = d.brandid JOIN king_categories c ON c.id = d.catid WHERE i.id='1669462818';

SELECT * FROM king_tmp_orders;
DESC king_tmp_orders;

SELECT i.id AS itemid,i.pnh_id,i.name,i.price,i.orgprice,i.store_price,i.pic,i.member_price FROM `pnh_api_franchise_cart_info` c
		JOIN king_dealitems i ON i.id=c.ref_itemid
		JOIN king_deals d ON d.dealid=i.dealid
		WHERE c.partner_id='7' AND c.status=1 AND c.user_id='37'
		

SELECT * FROM pnh_api_franchise_cart_info WHERE partner_id=7;

#Sep_02-2014
SELECT i.id AS itemid,i.pnh_id,i.name,i.price,i.orgprice,i.store_price,i.pic,i.member_price FROM `pnh_api_franchise_cart_info` c
		JOIN king_dealitems i ON i.id=c.ref_itemid
		JOIN king_deals d ON d.dealid=i.dealid
		WHERE c.partner_id='7' AND c.status=1 AND c.user_id='37'
		
SELECT i.id AS itemid,i.is_group,d.menuid,b.name AS brand,c.name AS cat,i.id,i.is_combo,i.pnh_id AS pid,i.live,i.orgprice AS mrp,i.price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,i.member_price,i.mp_mem_max_qty,i.shipsin
	FROM king_dealitems i
	JOIN king_deals d ON d.dealid=i.dealid AND 1
	JOIN king_brands b ON b.id = d.brandid
	JOIN king_categories c ON c.id = d.catid
	LEFT JOIN `m_product_deal_link` l ON l.itemid=i.id AND l.is_active = 1
	LEFT JOIN m_product_info p ON p.product_id=l.product_id
	LEFT JOIN `m_product_group_deal_link` g ON g.itemid=i.id AND l.is_active = 1
	LEFT JOIN `products_group_pids` q ON q.product_id=l.product_id
	WHERE i.id='1997493726';
	
	
	
SELECT * FROM king_dealitems WHERE id = 1997493726;

SELECT  COUNT(*) AS t FROM king_transactions WHERE partner_reference_no = '171-0078995-0129955' AND partner_id = '7'

SELECT  * FROM king_transactions WHERE partner_reference_no = '171-0078995-0129955' AND partner_id = '7';
SELECT  * FROM king_transactions WHERE partner_reference_no = '402-6522048-8174718' AND partner_id = '7';

#-==============================
#Sep_02_2014
-- -- ALTER TABLE `king_orders` ADD COLUMN `partner_price` DOUBLE DEFAULT 0 NULL AFTER `partner_order_id`;
-- -- ALTER TABLE `king_orders` DROP COLUMN `partner_price`;
#-==============================

SELECT * FROM partner_info;

#Sep_03_2014

UPDATE pnh_api_franchise_cart_info SET STATUS=0 WHERE user_id='37',partner_id='7' AND ref_itemid='1997493726';

SELECT IFNULL(GROUP_CONCAT(smd.special_margin),0) AS sm,0 AS stock,i.is_combo,i.is_group,d.publish,d.brandid,d.catid,i.orgprice,
					i.price,i.name,i.pic,i.pnh_id,i.id AS itemid,i.member_price,i.live AS allow_order,
					b.name AS brand,c.name AS category 
				FROM king_deals d 
				JOIN king_dealitems i ON i.dealid=d.dealid 
				JOIN king_brands b ON b.id=d.brandid 
				JOIN king_categories c ON c.id=d.catid 
				LEFT JOIN pnh_special_margin_deals smd ON i.id = smd.itemid  AND smd.from <= UNIX_TIMESTAMP() AND smd.to >=UNIX_TIMESTAMP()
				
				 JOIN m_product_deal_link pdl ON pdl.itemid=i.id AND pdl.is_active=1  
				 WHERE i.is_pnh=0 AND d.catid='1023'
				 GROUP BY i.id ORDER BY d.publish DESC
				 
SELECT * FROM m_product_deal_link;

SELECT * FROM t_partner_stock_transfer;
SELECT * FROM t_partner_stock_transfer_product_link;
DESC t_partner_stock_transfer
DESC t_partner_stock_transfer_product_link
DESC pnh_api_franchise_cart_info

SELECT tf.transfer_id,tf.partner_id,tf.partner_transfer_no,tf.transfer_remarks,DATE_FORMAT(tf.scheduled_transfer_date,'%b/%d/%Y %H:%i:%s %p') AS scheduled_transfer_date,tf.transfer_status,DATE_FORMAT(tf.transfer_date,'%b/%d/%Y %H:%i:%s %p') AS transfer_date,tf.transfer_by,a.username 
FROM t_partner_stock_transfer tf 
JOIN king_admin a ON a.id=tf.transfer_by										
ORDER BY transfer_id DESC;
										
UPDATE pnh_api_franchise_cart_info SET STATUS=0,updated_on=NOW() WHERE partner_id=? AND user_id=?
#-==============================

 (
	SELECT o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	WHERE o.status !=3 AND o.is_memberprice=1  AND o.itemid='76827952824' AND tr.franchise_id='108' AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = 'CURDATE()'
	HAVING quantity IS NOT NULL 
	ORDER BY o.time DESC
)
UNION
(
	SELECT o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_tmp_orders o
	JOIN king_tmp_transactions tr ON tr.transid=o.transid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	WHERE o.status !=3 AND o.is_memberprice=1  AND o.itemid='76827952824' AND tr.franchise_id='108' #AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = 'CURDATE()'
		HAVING quantity IS NOT NULL 
	ORDER BY o.time DESC
)

SELECT * FROM king_orders ORDER BY id DESC
76827952824
PNHVDU64433
SELECT * FROM king_transactions WHERE transid='PNHVDU64433'
fid: 108

#Sep_04_2014

DESC t_partner_stock_transfer
DESC t_partner_stock_transfer_product_link


SELECT GROUP_CONCAT(itemid) AS itemids FROM t_partner_stock_transfer_product_link WHERE transfer_status=0 AND is_active=1 AND transfer_id=1;

SELECT tpl.* FROM t_partner_stock_transfer tp JOIN t_partner_stock_transfer_product_link tpl ON tpl.transfer_id=tp.transfer_id AND tpl.is_active=1 WHERE tp.is_active=1 AND tp.transfer_id=1

SELECT MAX(p_invoice_no)+1 AS p_invoice_no FROM t_trans_proforma_invoice_marker WHERE is_pnh =0;

SELECT COUNT(DISTINCT b.id) AS total 
		FROM king_transactions a 
		JOIN king_orders b ON a.transid = b.transid
		JOIN m_product_deal_link l ON b.itemid=l.itemid AND l.is_active = 1 
		WHERE #b.status = 0  and #l.product_id='5392' or 
		b.itemid='1218529724';

SELECT itemid FROM king_orders

SELECT COUNT(DISTINCT b.id) AS total 
	FROM king_transactions a 
	JOIN king_orders b ON a.transid = b.transid
	JOIN m_product_deal_link l ON b.itemid=l.itemid AND l.is_active = 1 
	WHERE b.status = 0  AND b.itemid='1218529724'
	
SELECT pnh_id FROM king_dealitems WHERE id=1218529724;
=>10005509

SELECT * FROM t_partner_stock_transfer_product_link
t_partner_stock_transfer

#Sep_05_2014

DESC king_orders
DESC `proforma_invoices`;
SELECT * FROM proforma_invoices

#========================
SELECT tpl.item_transfer_qty,i.tax,i.orgprice,i.price,i.is_pnh
	FROM t_partner_stock_transfer_product_link tpl 
	JOIN king_dealitems i ON i.id=tpl.itemid 
	
	WHERE tpl.id IN ('1')
	GROUP BY tpl.id ORDER BY tpl.id ASC;
	
SELECT SUM(i.orgprice*o.item_transfer_qty) AS ttl_mrp,SUM(i.price*o.item_transfer_qty) AS ttl_price
	FROM t_partner_stock_transfer_product_link o 
	JOIN king_dealitems i ON i.id=o.itemid 
	JOIN t_partner_stock_transfer t ON t.transfer_id=o.transfer_id
	WHERE o.transfer_id=4;
	
UPDATE t_partner_stock_transfer_product_link SET transfer_status=1,modified_on=NOW() WHERE id=1 AND transfer_id = 1 LIMIT 1

SELECT * FROM t_partner_stock_transfer_product_link

#ALTER TABLE `t_partner_transfer_request` ADD COLUMN `transfer_item_qty` INT(50) NULL AFTER `itemid`, CHANGE `product_id` `product_id` BIGINT(11) NULL, CHANGE `stock_id` `stock_id` BIGINT(11) NULL, CHANGE `from_loc` `from_loc` INT(11) NULL, CHANGE `to_loc` `to_loc` INT(11) NULL, CHANGE `from_rackbin` `from_rackbin` INT(11) NULL, CHANGE `to_rackbin` `to_rackbin` INT(11) NULL;
#ALTER TABLE `t_partner_stock_transfer` CHANGE `transfer_status` `transfer_status` INT(10) DEFAULT 0 NULL;
#ALTER TABLE `t_partner_stock_transfer_product_link` CHANGE `transfer_status` `transfer_status` TINYINT(1) DEFAULT 0 NULL COMMENT 'is transfer success';
#ALTER TABLE `t_partner_stock_transfer` CHANGE `transfer_id` `transfer_id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'unique transferid', ADD PRIMARY KEY (`transfer_id`);
#ALTER TABLE `t_partner_stock_transfer` ADD COLUMN `is_active` INT(10) DEFAULT 1 NULL AFTER `transfer_status`;
#ALTER TABLE `snapitto_erpsndx_jul_04_2014`.`t_partner_stock_transfer_product_link` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 NULL AFTER `transfer_status`;
#ALTER TABLE `t_partner_stock_transfer_product_link` ADD COLUMN `modified_by` INT(10) NULL AFTER `is_active`, ADD COLUMN `modified_on` DATETIME NULL COMMENT 'status updated on' AFTER `modified_by`;
#ALTER TABLE `t_partner_stock_transfer_product_link` CHANGE `transfer_status` `transfer_status` TINYINT(1) DEFAULT 0 NULL COMMENT '0:pending,1:reserved,2:packed,3:cancelled,4:shipped';


SELECT COUNT(*) AS ttl_cart_itm FROM pnh_api_franchise_cart_info WHERE user_id='37' AND `status`=1 AND partner_id=7;
SELECT * FROM pnh_api_franchise_cart_info WHERE user_id='37' AND `status`=1 AND partner_id=7 AND ref_itemid='7796336415';

#Sep_06_2014


DESC king_dealitems
DESC king_deals
DESC t_partner_stock_transfer
DESC t_partner_stock_transfer_product_link;

SELECT * FROM t_partner_stock_transfer;
SELECT * FROM t_partner_stock_transfer_product_link;
SELECT * FROM t_shipment_tracking_info;

SELECT c.transid,e.franchise_id,e.franchise_name,a.return_id,a.invoice_no,return_by,a.returned_on,IF(r.return_id,1,0) AS in_transit,r.courier,r.awb, FROM_UNIXTIME(r.logged_on) AS logged_on,b.name AS handled_by_name,a.status,r.status AS transit_status,r.ticket_id,IFNULL(s.ticket_no,'n/a') AS ticket_no,r.emp_phno,r.emp_name,r.transit_mode
FROM pnh_invoice_returns a
LEFT JOIN king_admin b ON a.handled_by = b.id
JOIN king_invoice c ON c.invoice_no = a.invoice_no
JOIN king_transactions d ON d.transid = c.transid
JOIN pnh_m_franchise_info e ON e.franchise_id = d.franchise_id
LEFT JOIN pnh_t_returns_transit_log r ON r.return_id=a.return_id 
#LEFT JOIN m_employee_info em ON em.employee_id=r.empid 
#LEFT JOIN m_courier_info cr ON cr.courier_id=r.courier_id 
LEFT JOIN support_tickets s ON s.ticket_id=r.ticket_id 
WHERE a.return_id= '593';

SELECT * FROM pnh_t_returns_transit_log

DESC `t_partner_stock_transfer_product_link`
DESC `m_product_deal_link`
SELECT * FROM m_product_deal_link
SELECT * FROM `t_partner_stock_transfer_product_link`

SELECT mn.name AS menu,bd.name AS brand,ct.name AS category
 FROM king_dealitems di
JOIN king_deals dl ON dl.dealid=di.dealid
JOIN king_dealitems i ON i.dealid=dl.dealid 
LEFT JOIN king_menu mn ON mn.id=dl.menuid
JOIN king_categories ct ON ct.id=dl.catid
JOIN king_brands bd ON bd.id=dl.brandid
WHERE di.id=2616472368

DESC t_reserved_batch_stock


SELECT stock_id,product_id,available_qty 
	FROM t_stock_info a 
	JOIN m_rack_bin_info b ON a.rack_bin_id = b.id AND is_damaged = 0 
	WHERE product_id = 98 


SELECT stl.id AS tp_id,stl.transfer_id,stl.itemid,stl.item_transfer_qty,orgprice AS deal_mrp ,stl.product_id,stl.product_transfer_qty FROM t_partner_stock_transfer_product_link stl JOIN m_product_deal_link pdl ON pdl.itemid=stl.itemid JOIN king_dealitems di ON di.id = pdl.itemid WHERE stl.transfer_id = 1 AND stl.itemid = '9998389588' AND stl.transfer_status = 0 GROUP BY stl.product_id 1	

SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id = 1 
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id = 1 

DESC t_stock_info 

#ALTER TABLE t_partner_stock_transfer_product_link ADD COLUMN batched_on DATETIME DEFAULT '0000-00-00 00:00:00';
#ALTER TABLE t_partner_stock_transfer_product_link ADD COLUMN batched_by INT(11) DEFAULT 0 ;

SELECT u.name AS username,l.*,pi.p_invoice_no,ci.invoice_no AS c_invoice_no,i.invoice_no,ptrbs.transfer_id FROM t_stock_update_log l LEFT OUTER JOIN king_invoice i ON i.id=l.invoice_id LEFT OUTER JOIN t_client_invoice_info ci ON ci.invoice_id=l.corp_invoice_id LEFT OUTER JOIN proforma_invoices PI ON pi.id=l.p_invoice_id LEFT OUTER JOIN t_partner_reserved_batch_stock ptrbs ON ptrbs.id=l.transfer_id LEFT OUTER JOIN king_admin u ON u.id=l.created_by WHERE l.product_id=? 


#Sep_08_2014

SELECT * FROM m_partner_deal_link


SELECT * FROM m_partner_deal_link;
#12243
SELECT IFNULL(GROUP_CONCAT(smd.special_margin),0) AS sm,0 AS stock,i.is_combo,i.is_group,d.publish,d.brandid,d.catid,i.orgprice,
					i.price,i.name,i.pic,i.pnh_id,i.id AS itemid,i.member_price,i.live AS allow_order,
					b.name AS brand,c.name AS category,p.name AS partner_name,is_pnh
				FROM king_deals d 
				JOIN king_dealitems i ON i.dealid=d.dealid 
					JOIN m_partner_deal_link pdl ON pdl.itemid=i.id 
					LEFT JOIN partner_info p ON p.id=pdl.partner_id
				JOIN king_brands b ON b.id=d.brandid 
				JOIN king_categories c ON c.id=d.catid 
				LEFT JOIN pnh_special_margin_deals smd ON i.id = smd.itemid  AND smd.from <= UNIX_TIMESTAMP() AND smd.to >=UNIX_TIMESTAMP()
				 WHERE d.catid='170'
				 GROUP BY i.id ORDER BY d.publish DESC;
#1224353452345
SELECT IFNULL(GROUP_CONCAT(smd.special_margin),0) AS sm,0 AS stock,i.is_combo,i.is_group,d.publish,d.brandid,d.catid,i.orgprice,
					i.price,i.name,i.pic,i.pnh_id,i.id AS itemid,i.member_price,i.live AS allow_order,
					b.name AS brand,c.name AS category,p.name AS partner_name
				FROM king_deals d 
				JOIN king_dealitems i ON i.dealid=d.dealid 
				 JOIN m_partner_deal_link pdl ON pdl.itemid=i.id 
							JOIN partner_info p ON p.id=pdl.partner_id
				JOIN king_brands b ON b.id=d.brandid 
				JOIN king_categories c ON c.id=d.catid 
				LEFT JOIN pnh_special_margin_deals smd ON i.id = smd.itemid  AND smd.from <= UNIX_TIMESTAMP() AND smd.to >=UNIX_TIMESTAMP()
				 
				 WHERE AND d.catid='438' AND d.brandid='97944987' 
				 GROUP BY i.id ORDER BY d.publish DESC
#jfghjkdsakjfghjkdsafg
SELECT tfl.id,tfl.itemid,tfl.item_transfer_qty,tfl.transfer_status 
	,di.name AS deal_name,dl.catid,dl.brandid,dl.menuid,mn.name AS menu,ct.name AS category,bd.name AS brand
	,IFNULL(a.username,'--na--') AS username,tfl.modified_on
	FROM t_partner_stock_transfer_product_link tfl
	JOIN king_dealitems di ON di.id=tfl.itemid
	JOIN king_deals dl ON dl.dealid=di.dealid
		LEFT JOIN king_menu mn ON mn.id=dl.menuid
		LEFT JOIN king_categories ct ON ct.id=dl.catid
		LEFT JOIN king_brands bd ON bd.id=dl.brandid
	LEFT JOIN king_admin a ON a.id=tfl.modified_by
	WHERE tfl.transfer_id=2 ORDER BY tfl.id ASC;
	
SELECT * FROM m_partner_deal_link;
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=2;

#ALTER TABLE `snapitto_erpsndx_jul_04_2014`.`t_partner_stock_transfer` ADD COLUMN `modified_by` INT(10) NULL AFTER `transfer_by`, ADD COLUMN `modified_on` DATETIME NULL AFTER `modified_by`; 

SELECT tfl.id,tfl.itemid,tfl.item_transfer_qty,tfl.transfer_status 
	,di.name AS deal_name
	FROM t_partner_stock_transfer_product_link tfl
	JOIN king_dealitems di ON di.id=tfl.itemid
	WHERE tfl.transfer_id=1 ORDER BY tfl.item_transfer_qty ASC;
	
SELECT * FROM `t_partner_stock_transfer`
SELECT * FROM `t_partner_stock_transfer_product_link`;
SELECT * FROM `t_partner_reserved_batch_stock`;
3642-pid
201195-stkid
#324234
SELECT stock_id,product_id,location_id,rack_bin_id,CONCAT(location_id,'-',rack_bin_id) AS rbid,product_barcode,SUM(available_qty) AS s,mrp FROM t_stock_info 
WHERE product_id=3642 
GROUP BY rbid,mrp,product_barcode,stock_id 
HAVING SUM(available_qty)>=0 ORDER BY mrp ASC
#===
SELECT rstk.transfer_id,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
	,stk.location_id,stk.rack_bin_id,stk.mrp,stk.available_qty,stk.product_barcode
FROM `t_partner_reserved_batch_stock` rstk
JOIN t_stock_info stk ON stk.product_id=rstk.product_id AND stk.stock_id=rstk.stock_info_id
WHERE rstk.transfer_id=4

SELECT * FROM t_stock_info
DESC t_stock_info
SELECT * FROM t_imei_no
SELECT * FROM t_reserved_batch_stock

SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=3
SELECT * FROM t_imei_no WHERE product_id=3642;

SELECT a.imei_no,b.mrp,b.product_barcode,b.location_id,b.rack_bin_id,b.stock_id
	FROM t_imei_no a 
	LEFT JOIN t_stock_info b ON a.stock_id = b.stock_id AND `status`=0 
	WHERE a.product_id=3642;
	
#prd pending
SELECT SUM(pstk.qty) AS partner_pending_stk,SUM(rs.qty) AS pnh_pending_stk
	FROM t_partner_reserved_batch_stock pstk
	LEFT JOIN t_reserved_batch_stock rs ON rs.product_id=pstk.product_id AND rs.status=0
	WHERE pstk.status=0 AND pstk.itemid='5381136649'
	
SELECT tfl.id,tfl.itemid,tfl.item_transfer_qty,tfl.transfer_status 
	,di.name AS deal_name
	#,concat('".IMAGES_URL."items/small/',di.pic,'.jpg') as image_url
	,di.orgprice AS mrp,di.price
	FROM t_partner_stock_transfer_product_link tfl
	JOIN king_dealitems di ON di.id=tfl.itemid
	JOIN t_partner_reserved_batch_stock rstk ON rstk.transfer_id=tfl.transfer_id
	WHERE tfl.transfer_id='4'
	GROUP BY tfl.transfer_id
	ORDER BY tfl.id ASC
	
SELECT * FROM m_product_info;
#Sep_09_2014
#============new==========
SELECT si.stock_id,SUM(pstk.qty) AS s,si.mrp,si.location_id,si.rack_bin_id,CONCAT(ri.rack_name,ri.bin_name) AS rbname,ri.is_damaged,IFNULL(si.product_barcode,'') AS pbarcode
FROM t_partner_reserved_batch_stock pstk
JOIN t_stock_info si ON si.stock_id=pstk.stock_info_id
JOIN m_rack_bin_info ri ON ri.id = si.rack_bin_id
WHERE pstk.`status`=0 AND pstk.product_id=98
HAVING SUM(pstk.qty)>=0;
#===========
SELECT * FROM m_partner_deal_link;

#ifnull(group_concat(smd.special_margin),0) as sm,
#left join pnh_special_margin_deals smd on i.id = smd.itemid  and smd.from <= unix_timestamp() and smd.to >=unix_timestamp()
SELECT 0 AS stock,i.is_combo,i.is_group,d.publish,d.brandid,d.catid,i.orgprice,
					i.price,i.name,i.pic,i.pnh_id,i.id AS itemid,i.member_price,i.live AS allow_order,
					b.name AS brand,c.name AS category,p.name AS partner_name
				FROM king_deals d 
				JOIN king_dealitems i ON i.dealid=d.dealid 
				  JOIN m_partner_deal_link pdl ON pdl.itemid=i.id 
				LEFT JOIN partner_info p ON p.id=pdl.partner_id
						
				JOIN king_brands b ON b.id=d.brandid 
				JOIN king_categories c ON c.id=d.catid 

				 WHERE d.catid='170'
				 GROUP BY i.id ORDER BY d.publish DESC
				 

SELECT SUM(a.available_qty) AS s,a.mrp
	,a.location_id,a.rack_bin_id, CONCAT(c.rack_name,bin_name) AS rbname,c.is_damaged
	,IFNULL(a.product_barcode,'') AS pbarcode
	,a.stock_id,DATE_FORMAT(a.expiry_on, '%d-%m-%Y') AS expiry_on,a.offer_note 
FROM t_stock_info a 
JOIN m_storage_location_info b ON b.location_id = a.location_id 
JOIN m_rack_bin_info c ON c.id = a.rack_bin_id 
LEFT JOIN t_partner_reserved_batch_stock pstk ON pstk.product_id=a.product_id AND pstk.stock_info_id=a.stock_id AND pstk.status=0 
WHERE a.product_id='98' GROUP BY a.mrp,pbarcode,a.location_id,a.rack_bin_id 
HAVING SUM(a.available_qty)>=0 ORDER BY a.mrp ASC


SELECT SUM(available_qty) AS s,mrp, a.location_id,a.rack_bin_id, CONCAT(c.rack_name,bin_name) AS rbname,c.is_damaged, IFNULL(product_barcode,'') AS pbarcode, a.stock_id,DATE_FORMAT(a.expiry_on, '%d-%m-%Y') AS expiry_on,offer_note FROM t_stock_info a 
JOIN m_storage_location_info b ON b.location_id = a.location_id 
JOIN m_rack_bin_info c ON c.id = a.rack_bin_id 
WHERE product_id='98' GROUP BY mrp,pbarcode,a.location_id,a.rack_bin_id HAVING SUM(available_qty)>=0 ORDER BY mrp ASC


SELECT di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
	,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
	b.name AS brand,c.name AS category,p.name AS partner_name
FROM m_partner_deal_link pdl
JOIN king_dealitems di ON di.id=pdl.itemid
JOIN king_deals dl ON dl.dealid=di.dealid
JOIN king_brands b ON b.id=dl.brandid 
JOIN king_categories c ON c.id=dl.catid 
JOIN partner_info p ON p.id=pdl.partner_id

8901526206773

12312313

SELECT live FROM king_dealitems WHERE id=5146282584

SELECT product_id,sync_product_src FROM m_product_info WHERE sync_product_src = 1 ORDER BY product_id LIMIT 50000

SELECT di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name
							FROM m_partner_deal_link pdl
							JOIN king_dealitems di ON di.id=pdl.itemid
							JOIN king_deals dl ON dl.dealid=di.dealid
							JOIN king_brands b ON b.id=dl.brandid 
							JOIN king_categories c ON c.id=dl.catid 
							JOIN partner_info p ON p.id=pdl.partner_id
							WHERE 1  AND dl.catid='170' AND dl.brandid='41487638'  AND dl.publish=1
							 GROUP BY di.id ORDER BY dl.publish DESC

SELECT * FROM t_imei_no
#Search by barcode							 
SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid  
FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid
LEFT JOIN m_product_deal_link pdl ON pdl.itemid=i.id
LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id
LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id
WHERE ((i.name LIKE '%".$q."%' OR i.pnh_id = '911239254888409' OR i.id='911239254888409' OR ime.imei_no='911239254888409' OR stk.product_barcode='8901138511487') AND i.pnh_id > 0) GROUP BY i.pnh_id;

SELECT * FROM m_partner_deal_link pdl
JOIN m_product_deal_link prod ON prod.itemid=pdl.itemid
#join t_imei_no ime on ime.product_id=prod.product_id
LEFT JOIN t_stock_info stk ON stk.product_id=prod.product_id

bcode:
8901138511494
8901138511487

SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid
LEFT JOIN m_product_deal_link pdl ON pdl.itemid=i.id
LEFT JOIN t_stock_info stk ON stk.product_id=prod.product_id 
WHERE ((i.name LIKE '%8901138511494 %'  OR ime.imei_no='8901138511494 '  OR stk.product_barcode='8901138511494 '  OR i.pnh_id = '8901138511494 '  OR i.id = '8901138511494 ' ) AND i.pnh_id > 0 ) GROUP BY i.pnh_id LIMIT 10;

SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid
LEFT JOIN m_product_deal_link pdl ON pdl.itemid=i.id
LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id  LEFT JOIN t_stock_info stk ON stk.product_id=prod.product_id 
WHERE ((i.name LIKE '%8901138511487 %'  OR ime.imei_no='8901138511487 '  OR stk.product_barcode='8901138511487 '  OR i.pnh_id = '8901138511487 '  OR i.id = '8901138511487 ' ) AND i.pnh_id > 0 ) GROUP BY i.pnh_id LIMIT 10

SELECT * FROM t_partner_stock_transfer
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id='7';

SELECT * FROM king_dealitems WHERE live=1;


(
	SELECT i.itemid,live,di.dealid,d.publish,IF(SUM(stat),1,0) AS new_deal_stat FROM (
				SELECT itemid,product_id,psrc,stk,SUM(psrc+IF(stk,1,0)) AS stat FROM (
				SELECT a.itemid,b.product_id,a.qty,a.is_active,c.is_sourceable AS psrc,IFNULL(SUM(available_qty),0) AS stk
			FROM m_product_group_deal_link a
			JOIN products_group_pids b ON a.group_id = b.group_id
			JOIN (SELECT itemid
			FROM m_product_group_deal_link a
			JOIN products_group_pids b ON a.group_id = b.group_id
			WHERE product_id = '6741' AND is_active = 1) AS b ON b.itemid = a.itemid
			JOIN m_product_info c ON c.product_id = b.product_id
			LEFT JOIN t_stock_info d ON d.product_id = b.product_id
				LEFT JOIN m_rack_bin_info rb ON rb.id = d.rack_bin_id 
						
			GROUP BY a.itemid,b.product_id,rb.is_damaged 
				HAVING rb.is_damaged = 0
			)AS h
			GROUP BY itemid,product_id ) AS i
			JOIN king_dealitems di ON di.id = i.itemid
			JOIN king_deals d ON d.dealid = di.dealid
			GROUP BY itemid
	HAVING live != new_deal_stat
	)
	UNION(
	SELECT itemid,dealid,live,publish,IF(IF(is_combo,(ttl_p=ttl_in),IF(is_group,ttl_in,ttl_in) ),1,0) AS new_deal_stat 
			FROM (
		SELECT 	i.itemid,live,di.dealid,d.publish,is_combo,is_group,COUNT(*) AS ttl_p,SUM(stat) AS ttl_in
		FROM (
				SELECT itemid,product_id,psrc,stk,IF(SUM(psrc+IF(stk,1,0)),1,0) AS stat 
				FROM (
					SELECT a.itemid,a.product_id,a.qty,a.is_active,c.is_sourceable AS psrc,(SUM(available_qty)+IFNULL(ven_stock_qty,0)) AS stk
			FROM m_product_deal_link a
			JOIN (SELECT itemid FROM m_product_deal_link WHERE product_id = '6741' AND is_active = 1) AS b ON b.itemid = a.itemid
			JOIN m_product_info c ON c.product_id = a.product_id
			LEFT JOIN t_stock_info d ON d.product_id = a.product_id
				LEFT JOIN m_vendor_product_link e ON e.product_id = c.product_id  
					LEFT JOIN m_rack_bin_info rb ON rb.id = d.rack_bin_id 
						
			WHERE a.is_active = 1 
			GROUP BY a.itemid,a.product_id,rb.is_damaged
				HAVING rb.is_damaged = 0
				
			)AS h
				JOIN king_dealitems di ON di.id = h.itemid
				GROUP BY itemid,product_id 
		) AS i
			JOIN king_dealitems di ON di.id = i.itemid
			JOIN king_deals d ON d.dealid = di.dealid
			GROUP BY itemid
	) AS k 
	HAVING live != new_deal_stat
	)
	
#Sep_10_2014

SELECT * FROM t_stock_info WHERE product_id=3642;

SELECT * FROM m_rack_bin_info

#new procurement list
SELECT rstk.product_id,p.product_name,SUM(rstk.qty) AS qty,CONCAT(rb.rack_name,rb.bin_name) AS rbname,stk.mrp,GROUP_CONCAT(DISTINCT stk.product_barcode) AS barcodes
FROM t_partner_reserved_batch_stock rstk
JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
JOIN m_product_info p ON p.product_id=rstk.product_id
JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id  
WHERE rstk.transfer_id IN ('3','4')
GROUP BY stk.mrp,stk.rack_bin_id,stk.location_id

SELECT * FROM t_partner_reserved_batch_stock rstk
WHERE transfer_id IN ('8');
SELECT * FROM partner_info
SELECT * FROM t_partner_reserved_batch_stock
Reserved `status`:::
0-pending
1-alloted
2-reverted/cancelled

SELECT * FROM t_partner_stock_transfer WHERE transfer_id=4
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=4
#
SELECT rstk.id AS rlog_id,rstk.transfer_id,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
	,tdl.partner_id,tdl.transfer_remarks,p.partner_rackbinid,stk.location_id,stk.rack_bin_id,stk.mrp,stk.product_barcode
FROM t_partner_reserved_batch_stock rstk
JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
JOIN t_partner_stock_transfer tdl ON tdl.transfer_id=rstk.transfer_id
LEFT JOIN partner_info p ON p.id=tdl.partner_id
WHERE rstk.status=0 AND rstk.transfer_id=4;


	tfl.id,tfl.itemid,tfl.item_transfer_qty,tfl.transfer_status 
	,di.name AS deal_name
	,CONCAT('".IMAGES_URL."items/small/',di.pic,'.jpg') AS image_url
	,di.orgprice AS mrp,di.price,rstk.product_id,p.product_name
#new updated
SELECT rstk.product_id,rstk.qty,stk.mrp,rstk.itemid
	,di.name AS deal_name,p.product_name
FROM t_partner_reserved_batch_stock rstk
JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
JOIN king_dealitems di ON di.id=rstk.itemid
JOIN m_product_info p ON p.product_id=rstk.product_id
WHERE rstk.transfer_id=8

SELECT * FROM t_stock_info
DESC t_stock_info

SELECT rstk.transfer_id,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
		,stk.location_id,stk.rack_bin_id,stk.mrp,stk.available_qty,stk.product_barcode
	FROM `t_partner_reserved_batch_stock` rstk
	JOIN t_stock_info stk ON stk.product_id=rstk.product_id AND stk.stock_id=rstk.stock_info_id
	WHERE rstk.transfer_id=4
	GROUP BY rstk.product_id
	
SELECT rstk.itemid,rstk.stock_info_id
	,stk.product_barcode,stk.mrp,rstk.qty
 FROM t_partner_reserved_batch_stock rstk
 JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
WHERE rstk.product_id=5275 AND rstk.transfer_id=8 AND rstk.stock_info_id=159447;

SELECT * FROM t_partner_reserved_batch_stock;
SELECT * FROM t_partner_stock_transfer;
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=8;

#ALTER TABLE `t_partner_stock_transfer_product_link` ADD COLUMN `packed_on` DATETIME NULL AFTER `batched_by`, ADD COLUMN `packed_by` INT(11) NULL AFTER `packed_on`; 


#Sep_11_2014
SELECT * FROM t_stock_info WHERE stock_id = 0

SELECT transfer_status,	batched_on ,	batched_by ,	modified_on,	modified_by
	FROM t_partner_stock_transfer_product_link

SELECT * FROM t_partner_stock_transfer;
SELECT * FROM t_partner_stock_transfer_product_link;
SELECT * FROM t_partner_reserved_batch_stock;
stkid:297234
SELECT * FROM t_stock_info WHERE stock_id=297234;

SELECT * 
FROM m_partner_deal_link pdl
JOIN king_dealitems di ON di.id=pdl.itemidk;



SELECT * FROM king_dealitems WHERE dealid='1638665855';
=>6484744279

SELECT di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
		,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
		b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id
	FROM m_partner_deal_link pdl
	JOIN king_dealitems di ON di.id=pdl.itemid
	JOIN king_deals dl ON dl.dealid=di.dealid
	JOIN king_brands b ON b.id=dl.brandid 
	JOIN king_categories c ON c.id=dl.catid 
	JOIN partner_info p ON p.id=pdl.partner_id
	WHERE 1;
	
#truncate m_partner_deal_link;1638665855

SELECT id FROM king_dealitems WHERE is_pnh=0;

=>10307
SELECT * FROM m_partner_deal_link;
#===================================================

#10307 row(s) affected
#===================================================
#INSERT INTO `m_departments` (`name`, `keyword`, `created_on`, `created_by`, `status`) VALUES ('Business Development', 'BD', '2014-09-11 14:05:09', '37', '1'); 
#INSERT INTO `m_employee_roles` (`role_name`, `short_frm`, `created_on`, `created_by`) VALUES ('Regional Manager', 'RM', '2014-09-11 14:17:18', '37');

#`m_dept_request_types`
SELECT * FROM m_employee_roles;

SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id
							FROM m_partner_deal_link pdl
							JOIN king_dealitems di ON di.id=pdl.itemid
							JOIN king_deals dl ON dl.dealid=di.dealid
							JOIN king_brands b ON b.id=dl.brandid 
							JOIN king_categories c ON c.id=dl.catid 
							JOIN partner_info p ON p.id=pdl.partner_id
							WHERE 1  AND pdl.partner_id='7' AND di.is_pnh=0
							 GROUP BY di.id ORDER BY dl.publish DESC  LIMIT 100
							 
SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=9;

SELECT * FROM t_stock_info WHERE stock_id IN (162448,149847,232619)

rbid=75,114,84


SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=11;

SELECT * FROM t_stock_info WHERE stock_id IN (149847,232619)

SELECT * FROM t_partner_stock_transfer WHERE transfer_id=11
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=11;


SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=17;

SELECT * FROM t_stock_info WHERE stock_id IN (244513,295684);

SELECT * FROM t_stock_info WHERE product_id IN (1021,318);

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id='15';


SELECT * FROM t_stock_info WHERE product_id IN (1021);

SELECT * FROM m_rack_bin_info;

#Sep_12_2014

SELECT stock_id,product_id,location_id,rack_bin_id,CONCAT(location_id,'-',rack_bin_id) AS rbid,product_barcode,SUM(available_qty) AS s,mrp 
FROM t_stock_info 
WHERE product_id='1021' 
GROUP BY rbid,mrp,product_barcode,stock_id 
HAVING SUM(available_qty)>=0 
ORDER BY mrp ASC;


SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=18;
SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=18;
SELECT * FROM t_stock_info WHERE stock_id IN (244513,944,1076,295684,135592,134933)
SELECT * FROM t_partner_stock_transfer WHERE transfer_id=18;

SELECT * FROM t_partner_stock_transfer st
JOIN t_partner_stock_transfer_product_link stp ON stp.transfer_id=st.transfer_id
#join t_partner_reserved_batch_stock prb on prb.transfer_id=st.transfer_id
WHERE st.transfer_id=18;

SELECT * FROM m_partner_deal_link

#Sep_13_2014

#==========================Franchise allow sms==============================================================#
#suresh Aug 25 2014
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `allow_sms` TINYINT (1) DEFAULT '1' NULL  AFTER `opt_maxcreditlimit`
#==========================Franchise allow sms=============================================================#
/*[4:08:58 PM][121 ms]*/ ALTER TABLE `t_invoice_credit_notes` CHANGE `is_credit_processed` `is_credit_processed` TINYINT(1) DEFAULT 0 NULL COMMENT '1:processed 0:processed';

#-==============================
#Sep_08_2014
DROP TABLE t_partner_transfer_request;
DROP TABLE t_partner_stock_transfer;
# Transfer table
CREATE TABLE `t_partner_stock_transfer`( `transfer_id` BIGINT COMMENT 'unique transfer_id', `partner_id` INT(11) COMMENT 'related partner id', `partner_transfer_no` VARCHAR(50) COMMENT 'partner unique ref no'
, `transfer_remarks` TEXT, `is_active` INT(10) DEFAULT 1 NULL, `scheduled_transfer_date` DATETIME COMMENT 'for more info', `transfer_status` INT(10) DEFAULT 0, `transfer_date` DATETIME
, `transfer_by` INT(10) COMMENT 'created by userid'
, `modified_by` INT(10) NULL
, `modified_on` DATETIME NULL 
, PRIMARY KEY (`transfer_id`) );

#Transfer deal link table
CREATE TABLE `t_partner_stock_transfer_product_link`( `id` BIGINT NOT NULL AUTO_INCREMENT, `transfer_id` BIGINT(10) COMMENT 'reference transfer_id', `itemid` BIGINT(10), `product_id` INT(100)
, `from_stock_info_id` INT(100) COMMENT 'old stock id', `to_stock_info_id` INT(100) COMMENT 'new stock id', `product_transfer_qty` INT(10) COMMENT 'each product qty'
, `item_transfer_qty` INT(10) COMMENT 'overall transfer qty'
, `transfer_status` TINYINT(1) DEFAULT 0 COMMENT '0:pending,1:reserved,2:packed,3:cancelled,4:shipped'
,`is_active` TINYINT(1) DEFAULT 1 NULL
,`modified_by` INT(10) NULL
,`modified_on` DATETIME NULL COMMENT 'status updated on'
, batched_on DATETIME DEFAULT '0000-00-00 00:00:00'
, batched_by INT(11) DEFAULT 0
, packed_on DATETIME NULL
, packed_by INT(11) NULL
, batch_qty INT(10) DEFAULT 0 NULL COMMENT 'batch qty'
, scanned_qty INT(10) DEFAULT 0 NULL COMMENT 'how many quantity scanned'
, PRIMARY KEY (`id`) );

# Reserve stock transfer log
CREATE TABLE `t_partner_reserved_batch_stock` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` BIGINT(11) DEFAULT '0',
  `product_id` BIGINT(11) DEFAULT '0',
  `stock_info_id` BIGINT(11) DEFAULT '0',
  `itemid` BIGINT(11) DEFAULT '0',
  `qty` DOUBLE DEFAULT '0',
  `extra_qty` DOUBLE DEFAULT '0',
  `release_qty` DOUBLE DEFAULT '0',
  `reserved_on` DATETIME DEFAULT '0000-00-00 00:00:00',
  `released_on` DATETIME DEFAULT '0000-00-00 00:00:00',
  `status` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id`)
);

#Stock log table updated with transfer id field
ALTER TABLE t_stock_update_log ADD COLUMN transfer_id BIGINT(11) DEFAULT 0;

#return page fix
ALTER TABLE `pnh_t_returns_transit_log` ADD COLUMN `emp_name` VARCHAR(255) NULL AFTER `awb`;
#-==============================
#Sep_14_2014-Shivaraj - bug fix(_discount cannot be null)
ALTER TABLE `king_invoice` CHANGE `discount` `discount` DECIMAL(10,2) UNSIGNED DEFAULT 0.00 NULL;
#
RENAME TABLE `partner_t_reserved_batch_stock` TO `t_partner_reserved_batch_stock`;
ALTER TABLE `t_partner_stock_transfer_product_link` CHANGE `batched_on` `batched_on` DATETIME NULL;
ALTER TABLE `t_partner_reserved_batch_stock` CHANGE `reserved_on` `reserved_on` DATETIME NULL, CHANGE `released_on` `released_on` DATETIME NULL;
#Add Indexes
ALTER TABLE `t_partner_reserved_batch_stock` ADD INDEX (`transfer_id`), ADD INDEX (`product_id`), ADD INDEX (`stock_info_id`), ADD INDEX (`itemid`), ADD INDEX (`status`);
ALTER TABLE `t_partner_stock_transfer` ADD INDEX (`partner_id`), ADD INDEX (`transfer_status`), ADD INDEX (`partner_transfer_no`), ADD INDEX (`is_active`);
ALTER TABLE `t_partner_stock_transfer_product_link` ADD INDEX (`transfer_id`), ADD INDEX (`itemid`), ADD INDEX (`product_id`), ADD INDEX (`transfer_status`), ADD INDEX (`is_active`);
ALTER TABLE `m_partner_deal_link` ADD INDEX (`itemid`), ADD INDEX (`partner_ref_no`), ADD INDEX (`partner_id`);

ALTER TABLE `t_invoice_credit_notes` ADD COLUMN `is_credit_processed` TINYINT(1) DEFAULT 0 NULL COMMENT '1:processed 0:processed'; 
# Franchise_type feature updates
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `franchise_type` INT(1) DEFAULT 0 NULL COMMENT '0:Normal,1:RF(rural),2:RMF(rural_master)' AFTER `pnh_franchise_id`;
#-==============================
/*[4:08:58 PM][121 ms]*/ ALTER TABLE `t_invoice_credit_notes` ADD COLUMN `is_credit_processed` TINYINT(1) DEFAULT 0 NULL COMMENT '1:processed 0:processed'; 

#==============================
SELECT * FROM t_partner_reserved_batch_stock 
WHERE 
#`status`=0 AND 
transfer_id='21';

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=21

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=? AND itemid=? AND product_id=?;


SELECT * FROM t_partner_reserved_batch_stock 
WHERE 
#`status`=0 AND 
transfer_id='17';
SELECT STATUS FROM t_partner_reserved_batch_stock WHERE transfer_id=17 AND itemid='168595309925' AND product_id='1021'
HAVING STATUS=2

SELECT MAX(STATUS) AS STATUS FROM t_partner_reserved_batch_stock WHERE transfer_id=18;

SELECT st.transfer_id,st.partner_id,st.partner_transfer_no,st.transfer_remarks,st.transfer_status,st.transfer_by,st.scheduled_transfer_date,st.transfer_date
										,a.username,p.name AS partner_name
										FROM t_partner_stock_transfer st
										JOIN king_admin a ON a.id=st.transfer_by
										JOIN partner_info p ON p.id=st.partner_id
										WHERE 1  AND st.transfer_date BETWEEN "2014-09-15 00:00:00  00:00:00" AND "2014-09-15 00:00:00 23:00:00"
										ORDER BY transfer_id DESC;
										
SELECT * FROM 
m_partner_deal_link pd
JOIN king_dealitems di ON pd.itemid=di.id
WHERE di.is_combo=1

SELECT * FROM 
king_dealitems WHERE is_combo=1;

6717438462
5192344973
7185322699
5265164358
5521651316
2212733874
3471838221
8731752592
2382562473

5418217383
2549746695
8627278218
8989884625

# Sep_22_2014

SELECT tr.amount,tr.order_for,di.pnh_id,o.* FROM king_orders o
JOIN king_transactions tr ON tr.transid=o.transid
JOIN king_dealitems di ON di.id=o.itemid
WHERE (o.insurance_id IS NULL OR o.insurance_id='')

#======================================
SELECT tr.amount,tr.order_for,di.pnh_id,f.is_lc_store,o.*
FROM king_orders o
JOIN king_transactions tr ON tr.transid=o.transid
JOIN king_dealitems di ON di.id=o.itemid
JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
WHERE tr.is_pnh=1 AND o.has_insurance=1
 AND (o.insurance_id IS NOT NULL OR o.insurance_id !='')
 AND tr.transid='PNHVZP78775'

PNHPYD24824
PNHADY82182
mul:
PNHVZP78775
PNHQZG27464
PNHLLJ38838

SELECT i.*,d.publish,c.loyality_pntvalue,d.menuid FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid WHERE i.is_pnh=1 AND  i.pnh_id='10029039' AND i.pnh_id!=0;


SELECT * FROM king_dealitems WHERE 1
#and id='3828912355' 
AND has_insurance=1
#8423579341
#7488193851

SELECT * FROM king_deals WHERE dealid=2765499517
6449568775
2765499517

# SEP_23_2014

SET @req_mrp=17999;
SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id
	,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-@req_mrp),1,0) AS mrp_diff
						FROM t_stock_info a
						JOIN m_rack_bin_info b ON a.rack_bin_id = b.id AND b.is_damaged=0
						JOIN m_product_info p ON p.product_id = a.product_id
						LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
						LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
						LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
						LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id 
						WHERE a.mrp > 0 AND a.product_id = '190925' AND available_qty > 0  
						GROUP BY stock_id,town_diff
						ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp
						
						
SELECT stl.id AS tp_id,stl.transfer_id,stl.itemid,stl.item_transfer_qty,orgprice AS deal_mrp 
								,stl.product_id,stl.product_transfer_qty
								FROM t_partner_stock_transfer_product_link stl
								JOIN m_product_deal_link pdl ON pdl.itemid=stl.itemid
								JOIN king_dealitems di ON di.id = pdl.itemid 
								WHERE stl.transfer_id = '23' AND stl.itemid = '8423579341' AND stl.transfer_status = 0 GROUP BY stl.product_id;
								
SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-17999),1,0) AS mrp_diff
						FROM t_stock_info a
						JOIN m_rack_bin_info b ON a.rack_bin_id = b.id AND b.is_damaged=0
						JOIN m_product_info p ON p.product_id = a.product_id
						LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
						LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
						LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
						LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id 
						WHERE a.mrp > 0 AND a.product_id = '190925' AND available_qty > 0  
						GROUP BY stock_id,town_diff
						ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp;
						
						
						
#Sep_23_2014
ALTER TABLE m_vendor_product_link ADD COLUMN vendor_site_link VARCHAR(1024) DEFAULT '';

`deal_member_price_changelog`;
`deal_price_changelog`;

l_pnh_deal_member_price_change

SELECT COUNT(*) AS t FROM t_imei_no WHERE imei_no = '84375824375' AND order_id != 0 AND STATUS = 1

#UPDATE t_imei_no SET is_returned=0,STATUS=1 WHERE imei_no='84375824375' AND STATUS = 0 LIMIT 1;

SELECT GROUP_CONCAT(id) AS reserved_batch_rowids FROM t_reserved_batch_stock WHERE product_id=190925
#98769,101359,103837,105355
SELECT * FROM t_reserved_batch_stock WHERE product_id=190925 AND STATUS=0;

#update t_imei_no set reserved_batch_rowid=0 where product_id=190925 and reserved_batch_rowid in(98769,101359,103837,105355)

SELECT * FROM t_imei_no WHERE product_id=190925

SELECT * FROM t_imei_update_log;

SELECT * FROM king_deals WHERE dealid='3515129668'

SELECT is_pnh FROM king_dealitems WHERE dealid='3515129668'
#itemid=376219611919

SELECT is_pnh FROM king_dealitems WHERE id IN (1535213983,2215134531)
SELECT is_pnh FROM king_dealitems WHERE is_pnh=0;

SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id
							FROM m_partner_deal_link pdl
							JOIN king_dealitems di ON di.id=pdl.itemid
							JOIN king_deals dl ON dl.dealid=di.dealid
							JOIN king_brands b ON b.id=dl.brandid 
							JOIN king_categories c ON c.id=dl.catid 
							JOIN partner_info p ON p.id=pdl.partner_id
							WHERE 1  AND dl.catid='1030' AND pdl.partner_id='7' AND di.is_pnh=0
							 GROUP BY di.id ORDER BY dl.publish DESC;
#=====================================================							
#update king_dealitems di
#join m_partner_deal_link pdl on di.id=pdl.itemid
#set di.is_pnh=0
#=>10314 row(s) affected

SELECT * FROM king_dealitems di WHERE dealid IN (6449568775,2392527971,9189975456)

SELECT a.imei_no,b.mrp,b.product_barcode,b.location_id,b.rack_bin_id,b.stock_id
		FROM t_imei_no a 
		LEFT JOIN t_stock_info b ON a.stock_id = b.stock_id AND STATUS=0 
		WHERE a.product_id='8679';
		


SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id
							FROM m_partner_deal_link pdl
							JOIN king_dealitems di ON di.id=pdl.itemid
							JOIN king_deals dl ON dl.dealid=di.dealid
							JOIN king_brands b ON b.id=dl.brandid 
							JOIN king_categories c ON c.id=dl.catid 
							JOIN partner_info p ON p.id=pdl.partner_id
							WHERE 1  AND pdl.partner_id='7' AND di.is_pnh=0
							 GROUP BY di.id ORDER BY dl.publish DESC
							 
SELECT * FROM king_dealitems WHERE id=8423579341;

#SEP_24_2014

SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id
							FROM m_partner_deal_link pdl
							 JOIN king_dealitems di ON di.id=pdl.itemid
							 JOIN king_deals dl ON dl.dealid=di.dealid
							 JOIN king_brands b ON b.id=dl.brandid 
							 JOIN king_categories c ON c.id=dl.catid 
							LEFT JOIN partner_info p ON p.id=pdl.partner_id
							WHERE 1  AND pdl.partner_id='7' AND di.is_pnh=0 AND pdl.itemid='376219611919'
							 GROUP BY di.id ORDER BY dl.publish DESC 
							 

SELECT * FROM king_dealitems WHERE id IN (376219611919)
SELECT * FROM king_deals WHERE dealid='3515129668'

SELECT * FROM king_brands

SELECT *,FROM_UNIXTIME(created_on) FROM t_imei_no WHERE imei_no=243571234854325
							 
SELECT * FROM king_dealitems
iid:379437927161, did:5193963534
SELECT * FROM king_deals WHERE dealid='5193963534';

#====================
#SEP_24_2014
ALTER TABLE `partner_info` ADD INDEX (`partner_rackbinid`);
#====================

SELECT tr.amount,tr.order_for,di.pnh_id,f.is_lc_store,o.*
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
	WHERE tr.is_pnh=1 AND di.has_insurance=1
	 AND tr.transid='PNH81913';
	 
#SEP_25_2014

SELECT stk.* FROM t_imei_no ime
JOIN t_stock_info stk ON stk.stock_id=ime.stock_id
WHERE ime.imei_no = '243571234854325';

#=== MP discount price feature update ============
ALTER TABLE king_dealitems DROP COLUMN mp_discount_price 
ALTER TABLE deal_member_price_changelog DROP COLUMN mp_discount_price 
#SEP_25_2014-Shivaraj
ALTER TABLE `king_dealitems` ADD COLUMN `mp_discount_price` DECIMAL(10,2) DEFAULT 0 NOT NULL AFTER `mp_offer_to`;
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_discount_price` DECIMAL(10,2) DEFAULT 0 NOT NULL AFTER `mp_offer_to`;
CREATE TABLE `m_apk_store_banners` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) DEFAULT '0',
  `banner_name` VARCHAR(255) DEFAULT NULL,
  `banner_link` VARCHAR(2024) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT '0',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT '0',
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM;
#==============================

SELECT i.id AS itemid,i.member_price,i.mp_max_allow_qty,i.mp_frn_max_qty,i.mp_mem_max_qty,i.mp_is_offer,i.mp_offer_from,i.mp_offer_to,i.mp_discount_price,i.mp_offer_note
									,mpl.id AS logref_id,a.username AS created_by,mpl.created_on
									,IF(i.mp_offer_to IS NULL,i.mp_offer_to,IF(i.mp_offer_to > NOW(),1,0) ) AS validity
									FROM king_dealitems i
									LEFT JOIN deal_member_price_changelog mpl ON mpl.itemid=i.id AND mpl.is_active=1
									LEFT JOIN king_admin a ON a.id=mpl.created_by
									WHERE i.id='9451668128';
#====new get valid imei
SELECT imei.* FROM t_imei_no imei
JOIN t_stock_info stk ON stk.stock_id=imei.stock_id
JOIN m_rack_bin_info rb ON rb.id=stk.rack_bin_id AND rb.is_damaged=0
WHERE imei.stock_id !=0 AND stk.rack_bin_id NOT IN (SELECT GROUP_CONCAT(partner_rackbinid)  FROM partner_info WHERE partner_rackbinid !=0) AND imei_no =91121190053619500;
#=>21784
#=>21768

SELECT * FROM m_rack_bin_info LIMIT 10
SELECT * FROM partner_info;

#==Alloted to partner rackbin
SELECT COUNT(*) FROM t_imei_no imei
JOIN t_stock_info stk ON stk.stock_id=imei.stock_id
JOIN m_rack_bin_info rb ON rb.id=stk.rack_bin_id AND rb.is_damaged=0
 JOIN partner_info p ON p.partner_rackbinid=stk.rack_bin_id
WHERE stk.rack_bin_id=120

SELECT GROUP_CONCAT(IF(partner_rackbinid!=0,partner_rackbinid,'') ) FROM partner_info
SELECT GROUP_CONCAT(DISTINCT partner_rackbinid) FROM partner_info;

SELECT * FROM t_imei_no WHERE stock_id!=0 LIMIT 10;

SELECT rstk.id AS rlog_id,rstk.transfer_id,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
		,tdl.partner_id,tdl.transfer_remarks,tdpl.id AS plogid
	FROM t_partner_reserved_batch_stock rstk
	JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	JOIN t_partner_stock_transfer tdl ON tdl.transfer_id=rstk.transfer_id
	JOIN t_partner_stock_transfer_product_link tdpl ON tdpl.product_id=rstk.product_id AND tdpl.transfer_id=rstk.transfer_id
	LEFT JOIN partner_info p ON p.id=tdl.partner_id
	WHERE rstk.status=0 AND rstk.transfer_id='30' GROUP BY rstk.stock_info_id;
	
#===========================================
#SEP_26_2014-Shivaraj-IMEI table product link id update
#ALTER TABLE `t_imei_no` ADD COLUMN `transfer_prod_link_id` BIGINT(11) DEFAULT 0 NULL AFTER `ref_credit_note_id`; 
ALTER TABLE `t_imei_update_log` ADD COLUMN `transfer_prod_link_id` BIGINT(11) DEFAULT 0 NULL AFTER `cancelled_on`; 
ALTER TABLE `t_partner_reserved_batch_stock` ADD COLUMN `tp_id` BIGINT(11) DEFAULT 0 NULL AFTER `itemid`;
#ALTER TABLE `t_imei_no` DROP COLUMN `transfer_prod_link_id`; 
#===========================================

SELECT rstk.itemid,rstk.stock_info_id,stk.rack_bin_id,stk.location_id
	,stk.product_barcode,stk.mrp,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name AS deal,p.product_name
	,rstk.tp_id,imei.imei_no
	FROM t_partner_reserved_batch_stock rstk
	JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
	JOIN m_product_info p ON p.product_id=rstk.product_id
	JOIN king_dealitems di ON di.id=rstk.itemid

	LEFT JOIN t_imei_update_log imei ON  imei.transfer_prod_link_id=rstk.tp_id AND imei.transfer_prod_link_id!=0
	
	WHERE rstk.transfer_id='31' AND rstk.itemid='376219611919' AND rstk.product_id='8679' GROUP BY rstk.id;
	
SELECT * FROM t_imei_update_log WHERE transfer_prod_link_id IN (58,59)

SELECT imei.imei_no,imei.logged_on FROM t_imei_update_log imei WHERE imei.transfer_prod_link_id=58

SELECT * FROM pnh_member_info WHERE pnh_member_id='22034351';

INSERT INTO pnh_menu_margin_track(menu_id,default_margin,default_mp_margin,balance_discount,balance_amount,loyality_pntvalue,rf_mp_margin,rmf_commission,rf_commission,created_by,created_on)
VALUES('143534534543534534512','55555','55555','5550','5550','0.5','0','','','37',1411726307)

#================
ALTER TABLE `pnh_menu_margin_track` CHANGE `rmf_commssion` `rmf_commission` DOUBLE DEFAULT 0 NULL;
#===========

SELECT * FROM t_invoice_credit_notes WHERE invoice_no='20141027242'
id	TYPE	grp_no	franchise_id	invoice_no	amount	is_active	ref_id	payment_id	created_on	modified_on	created_by	modified_by	is_credit_processed
14965	1	0	500	20141027242	498.8005	0	0	0	2014-09-26 16:40:47	\N	37	0	1

SELECT * FROM t_invoice_credit_notes WHERE invoice_no='20141030909';

INSERT INTO `m_partner_deal_link` (`itemid`, `partner_ref_no`, `partner_id`, `created_by`, `created_on`) VALUES ('8322469195', 'AS8322469195', '7', '37', '2014-09-26 18:42:04');

#==================================================
#SEP_27_2014
ALTER TABLE `deal_member_price_changelog` CHANGE `mp_discount_price` `mp_offer_price` DECIMAL(10,2) DEFAULT 0.00 NOT NULL;
ALTER TABLE `king_dealitems` CHANGE `mp_discount_price` `mp_offer_price` DECIMAL(10,2) DEFAULT 0.00 NOT NULL; 

ALTER TABLE `king_dealitems` ADD COLUMN `mp_is_big_offer` TINYINT(1) DEFAULT 0 NOT NULL AFTER `mp_discount_price`;
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_is_big_offer` TINYINT(1) DEFAULT 0 NOT NULL AFTER `mp_discount_price`;
#==================================================

SELECT * FROM deal_member_price_changelog WHERE itemid='6776968436'; //'2323347653'
SELECT * FROM king_dealitems WHERE id='6776968436';

SELECT c.id,pnh_id AS pid,a.name,orgprice AS mrp,member_price AS price,ROUND(((orgprice-member_price)/orgprice)*100) AS disc,CONCAT('http://static.snapittoday.com/items/small/',b.pic,'.jpg') AS pimg_link,a.mp_offer_to
		FROM king_dealitems a
		JOIN king_deals b ON a.dealid = b.dealid
		JOIN pnh_menu_group_link c ON c.menu_id = b.menuid
		WHERE is_pnh=1 AND group_id = ? AND publish = 1 AND live = 1 AND a.mp_is_offer=1 AND a.mp_is_big_offer = 1 AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)
		GROUP BY a.id
	LIMIT 30;
	
#=============================
#SEP_29_2014
SELECT * FROM m_product_deal_link WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868)

#Convert to SIT deals
SELECT * FROM king_dealitems WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868)

sno	id	dealid	nlc	phc	shc	rsp	shipsin	shipsto	itemcode	model	price	size_chart	member_price	mp_frn_max_qty	mp_mem_max_qty	mp_max_allow_qty	mp_is_offer	mp_offer_from	mp_offer_to	mp_offer_price	mp_is_big_offer	mp_offer_note	mp_logid	store_price	nyp_price	billon_orderprice	gender_attr	ratings	reviews	snapits	buys	loves	fcp	orgprice	viaprice	agentcom	NAME	print_name	quantity	available	pic	tagline	description1	description2	slots	url	live	private	tellurprice	b2b	tax	service_tax	service_tax_cod	bp_expires	cod	groupbuy	sizing	gender_men	gender_women	gender_unisex	gender_kids	favs	min_cart_value	max_allowed_qty	is_featured	cashback	bodyparts	is_pnh	pnh_id	is_combo	temp_loc	move_as_product	tmp_dealid	tmp_itemid	created_on	created_by	modified_on	modified_by	description	created	modified	hs18_itemid	hs18_sku_code	tmp_pnh_itemid	tmp_pnh_dealid	is_group	has_insurance	powered_by	free_frame	has_power	lens_type
126599	2547651425	3392644375	0	0	0	0	48-72 hrs				7649	\N	7649.00	1	1	10	\N	\N	\N	0.00	0		0	9000	0	1		0	0	0	0	0	0	9000	0	0	Nokia X (Green)		0	0	30e5oh3d4o6jkn0						1	0	0	0	550	0	0	0	0	1	0	0	0	0	0	0	0	5	0	0	0	1	10044952	0	\N	0	\N	\N	2014-03-14 13:56:02	21	2014-03-14 13:56:45	21		1394785562	1394785605	\N	\N	\N	\N	0	1	\N	0	0	

SELECT GROUP_CONCAT(id) FROM king_dealitems WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868)
2471189723,1222449289,2558145287,2688412527,2547651425,2673935284

UPDATE king_dealitems SET is_pnh=0,pnh_id=0 WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868);

SELECT * FROM king_dealitems WHERE is_pnh=0


SELECT * FROM `m_partner_deal_link`;

#=============== CONVERT TO SIT DEAL ==========================
#1 get the itemids
SELECT GROUP_CONCAT(id) FROM king_dealitems WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868)
2471189723,1222449289,2558145287,2688412527,2547651425,2673935284

#2 into partner deal link table
INSERT INTO `m_partner_deal_link` (`itemid`, `partner_ref_no`, `partner_id`, `created_by`, `created_on`) VALUES 
('1222449289', 'AS1222449289', '7', '37', '2014-09-29 16:07:22')
,('2558145287', 'AS2558145287', '7', '37', '2014-09-29 16:07:22')
,('2688412527', 'AS2688412527', '7', '37', '2014-09-29 16:07:22')
,('2547651425', 'AS2547651425', '7', '37', '2014-09-29 16:07:22')
,('2673935284', 'AS2673935284', '7', '37', '2014-09-29 16:07:22');

#3 Reset as sit deal-menuid=6
UPDATE king_deals d 
JOIN king_dealitems di ON di.dealid=d.dealid
SET di.is_pnh=0,di.pnh_id=0,d.menuid=6
WHERE di.id IN(2471189723,1222449289,2558145287,2688412527,2547651425,2673935284);

#=============== CONVERT TO SIT DEAL ==========================

SELECT * FROM king_dealitems di
JOIN king_deals d ON d.dealid=di.dealid
WHERE di.id IN(2471189723,1222449289,2558145287,2688412527,2547651425,2673935284);

UPDATE king_deals d 
JOIN king_dealitems di ON di.dealid=d.dealid
SET di.is_pnh=0,di.pnh_id=0,d.menuid=6,d.menuid2=0
WHERE di.id IN(2471189723,1222449289,2558145287,2688412527,2547651425,2673935284);

#SEP_30_2014

SELECT rstk.itemid,rstk.stock_info_id,stk.rack_bin_id,stk.location_id
		,stk.product_barcode,stk.mrp,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name AS deal,p.product_name,rstk.tp_id
		,GROUP_CONCAT(imlog.imei_no) AS imei_no
	 FROM t_partner_reserved_batch_stock rstk
	 JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	 JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
	 JOIN m_product_info p ON p.product_id=rstk.product_id
	 JOIN king_dealitems di ON di.id=rstk.itemid
	 JOIN t_imei_update_log imlog ON imlog.product_id=rstk.product_id AND imlog.stock_id=rstk.stock_info_id
	WHERE rstk.transfer_id='33' AND rstk.itemid='2471189723' AND rstk.product_id='155705' GROUP BY rstk.id 	
#imlog.transfer_prod_link_id=rstk.tp_id# and 
itmid:2471189723, prdid:155705

SELECT * FROM t_imei_update_log WHERE transfer_prod_link_id=62

SELECT GROUP_CONCAT(imei.imei_no) AS imei_nos
FROM t_imei_update_log imei 
WHERE imei.transfer_prod_link_id='59' AND imei.transfer_prod_link_id!=0;

SELECT stp.transfer_id,stp.itemid,stp.item_transfer_qty 
FROM t_partner_stock_transfer_product_link stp
JOIN t_partner_stock_transfer st ON st.transfer_id=stp.transfer_id
WHERE st.transfer_id = '34' AND stp.transfer_status = 0 AND st.transfer_status=0 AND st.is_active=1 
GROUP BY stp.itemid;

#UPDATE t_partner_stock_transfer SET is_active=0,transfer_status=3,modified_by='35',modified_on='37' WHERE transfer_id='2014-09-30 16:53:37'

SELECT * FROM (
	SELECT tr.amount,tr.order_for,di.pnh_id,f.is_lc_store,di.has_insurance
	,o.id,o.itemid,o.brandid,o.bill_person,o.quantity,o.status,o.shipped,o.time,o.i_orgprice AS mrp,o.i_price AS price,o.is_memberprice,o.mp_logid
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
	WHERE tr.is_pnh=1 
	 AND (o.insurance_id IS NULL OR o.insurance_id ='')
	 AND tr.transid='PNH59314'
 )
 AS g
 WHERE g.has_insurance=1;
 #=============================
 SELECT * FROM king_orders WHERE transid='PNH59314';
  SELECT * FROM king_orders WHERE transid='PNH93356';
 #======================
 SELECT i.*,d.publish,c.loyality_pntvalue,d.menuid 
	 FROM king_dealitems i 
	 JOIN king_deals d ON d.dealid=i.dealid 
	 JOIN pnh_menu c ON c.id = d.menuid 
	 WHERE i.is_pnh=1 AND  i.pnh_id=? AND i.pnh_id!=0;
	 
SELECT c.id,pnh_id AS pid,a.name,orgprice AS mrp,a.mp_offer_price AS price,ROUND(((orgprice-member_price)/orgprice)*100) AS disc,IFNULL(v.main_image,CONCAT('".IMAGES_URL."/items/small/',b.pic,'.jpg')) AS pimg_link,IFNULL(v.main_image,CONCAT('".IMAGES_URL."/items/',b.pic,'.jpg')) AS main_pimg_link,a.mp_offer_to
		FROM king_dealitems a
		JOIN king_deals b ON a.dealid = b.dealid
		JOIN pnh_menu_group_link c ON c.menu_id = b.menuid
		LEFT JOIN m_vendor_product_images v ON v.item_id=a.id
		WHERE is_pnh=1 AND group_id = '1' AND publish = 1 AND live = 1 AND a.mp_is_offer=1 AND a.mp_is_big_offer = 1 AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)
		GROUP BY a.id
	LIMIT 30;
	
SELECT DISTINCT o.userid,o.*, p.name AS deal,p.dealid,p.url,u.name AS username,r.offer_text,r.immediate_payment,b.name AS brand,c.name AS cat_name,r.is_active,kv.invoice_no
				FROM king_orders o 
				JOIN king_dealitems p ON p.id=o.itemid
				LEFT OUTER JOIN king_users u ON u.userid=o.userid
				LEFT OUTER JOIN pnh_member_info i ON i.pnh_member_id=o.member_id
				LEFT OUTER JOIN pnh_m_offers r ON r.id=o.offer_refid
				LEFT OUTER JOIN king_brands b ON b.id=r.brand_id
				LEFT OUTER JOIN king_categories c ON c.id=r.cat_id
				LEFT OUTER JOIN king_invoice kv ON kv.order_id=o.id AND kv.invoice_status = 1 
				WHERE o.transid='PNH59314' GROUP BY o.id
				
SELECT * FROM king_transactions WHERE transid='PNH59314';

SELECT di.*,d.menuid FROM king_dealitems di
JOIN king_deals d ON d.dealid = di.dealid
 WHERE id=2243849623;
 
 SELECT * FROM pnh_member_offers WHERE member_id= '21111152' AND transid_ref='PNH33862' AND order_id='1542925176'; # and insurance_id =
 
 
 SELECT * FROM pnh_member_offers WHERE transid_ref='PNH59314';
 
 SELECT * FROM pnh_member_info WHERE pnh_member_id=21111152;
#=======================================
SELECT h.itemid,publish,live,product_id,lqty,aqty,dqty,is_sourceable,IF(SUM(ttl_l)=SUM(is_sourceable),0,1)*!is_group AS is_limited_stk,IF(SUM(ttl_l)=SUM(pstk),1,0) AS STATUS,is_combo,is_group,(MIN(dqty)) AS d_stk,IFNULL(SUM(o.quantity),0) AS oqty	
	FROM (
	SELECT g.itemid,product_id,lqty,aqty,is_sourceable,(FLOOR(aqty/lqty)) AS dqty,ttl_l,pstk,is_combo,is_group,publish,live
	FROM (
	SELECT a.itemid,a.product_id,b.is_sourceable,a.qty AS lqty,is_damaged,SUM(c.available_qty) AS aqty,
	COUNT(DISTINCT a.product_id) AS ttl_l,
	IF(a.qty<=SUM(c.available_qty),1,0) AS pstk
	FROM m_product_deal_link a 
	JOIN m_product_info b ON a.product_id = b.product_id  
	LEFT JOIN t_stock_info c ON c.product_id = b.product_id 
	LEFT JOIN m_rack_bin_info rb ON rb.id = c.rack_bin_id 
	WHERE itemid IN ('9971227223') AND a.is_active = 1  
	GROUP BY a.product_id,a.itemid,is_damaged
	HAVING (is_damaged = 0 OR is_damaged IS NULL)
	) AS g 
	JOIN king_dealitems di ON di.id = g.itemid 
	JOIN king_deals d ON d.dealid = di.dealid
	GROUP BY itemid,product_id 
	) AS h
	LEFT JOIN king_orders o ON o.itemid = h.itemid AND o.status = 0 
	GROUP BY h.itemid,IF(is_group,product_id,1) 
 #=======================================
 
 SELECT free_frame,has_power,i.id AS item_id,i.size_chart,pnh_id AS pid,i.is_group,i.gender_attr,i.id AS itemid,i.name,d.tagline,c.name AS category,m.name AS menu,d.menuid AS menu_id,
					d.catid AS category_id,mc.name AS main_category,c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,i.member_price,
					i.orgprice AS mrp,i.store_price,i.is_combo,i.powered_by,
					IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url,
					d.description,i.shipsin AS ships_in,d.keywords,i.live AS is_stock,d.publish AS is_enabled,
					IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url, 
					i.has_insurance,d.publish,i.price,i.mp_offer_from
					
					,i.mp_offer_to
					
					,IF(UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(i.mp_offer_from) AND UNIX_TIMESTAMP(i.mp_offer_to),1,0) AS mp_offer_validity,i.mp_offer_price
					
					FROM king_deals d 
					JOIN king_dealitems i ON i.dealid=d.dealid 
					JOIN king_brands b ON b.id=d.brandid 
					JOIN king_categories c ON c.id=d.catid 
					LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid 
					LEFT OUTER JOIN king_categories mc ON mc.id=c.type 
					LEFT JOIN m_vendor_product_images v ON v.item_id=i.id  
					WHERE is_pnh=1 AND i.pnh_id ='1645585' AND i.pnh_id!=0
					ORDER BY d.sno ASC;
					

SELECT COUNT(*) AS offers FROM pnh_member_offers mo
	JOIN pnh_member_info mif ON mif.pnh_member_id=mo.member_id
	WHERE mif.user_id='98877' AND mo.transid_ref='PNH69863'
	GROUP BY mif.user_id
	

	
SELECT orgprice,price,is_group,is_pnh,member_price FROM king_dealitems WHERE id='1619369786'
DESC king_dealitems
	
SELECT * FROM deal_member_price_changelog WHERE itemid='8392632691';

mp_max_allow_qty,mp_frn_max_qty,mp_mem_max_qty

SELECT orgprice,price,is_group,is_pnh,member_price,mp_max_allow_qty,mp_frn_max_qty,mp_mem_max_qty
	FROM king_dealitems WHERE id=8392632691
	
	SELECT orgprice,price,is_group,is_pnh,member_price,mp_max_allow_qty,mp_frn_max_qty,mp_mem_max_qty,mp_logid
													FROM king_dealitems WHERE id=8392632691
													
/*
//						$mp_log_set=0;
//						$mp_log_res=$this->db->query("SELECT * FROM deal_member_price_changelog WHERE itemid=?",$itemid);
//						if($mp_log_res->num_rows())
//							$mp_log_set=1;
*/


9877387993



#=============== CONVERT TO SIT DEAL ==========================
#1 get the itemids
SELECT GROUP_CONCAT(id) FROM king_dealitems WHERE pnh_id IN (10005486,10005393,10044952,10005988,10029039,15734868)
2471189723,1222449289,2558145287,2688412527,2547651425,2673935284

#2 into partner deal link table
INSERT INTO `m_partner_deal_link` (`itemid`, `partner_ref_no`, `partner_id`, `created_by`, `created_on`) VALUES 
('1222449289', 'AS1222449289', '7', '37', '2014-09-29 16:07:22')
,('2558145287', 'AS2558145287', '7', '37', '2014-09-29 16:07:22')
,('2688412527', 'AS2688412527', '7', '37', '2014-09-29 16:07:22')
,('2547651425', 'AS2547651425', '7', '37', '2014-09-29 16:07:22')
,('2673935284', 'AS2673935284', '7', '37', '2014-09-29 16:07:22');

#3 Reset as sit deal-menuid=6
UPDATE king_deals d 
JOIN king_dealitems di ON di.dealid=d.dealid
SET di.is_pnh=0,di.pnh_id=0,d.menuid=6
WHERE di.id IN(2471189723,1222449289,2558145287,2688412527,2547651425,2673935284);

#=============== CONVERT TO SIT DEAL ==========================

SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
		,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
		b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id,GROUP_CONCAT(plnk.product_id) AS product_ids
	FROM m_partner_deal_link pdl
	JOIN king_dealitems di ON di.id=pdl.itemid
	JOIN king_deals dl ON dl.dealid=di.dealid
	JOIN king_brands b ON b.id=dl.brandid 
	JOIN king_categories c ON c.id=dl.catid 
	JOIN partner_info p ON p.id=pdl.partner_id
	JOIN m_product_deal_link plnk ON plnk.itemid=di.id AND plnk.is_active=1
	WHERE 1  AND di.is_pnh=0;
	

	
SELECT * FROM pnh_member_offers WHERE transid_ref IN('PNHJXN39517','PNHUPT29312')
-- Update as 1 for delivery_status


SELECT * FROM `m_partner_deal_link` WHERE itemid='952320082997';

SELECT * FROM king_dealitems di
LEFT JOIN m_partner_deal_link pdl ON pdl.itemid!=di.id
WHERE di.is_pnh=0 AND di.pnh_id=0 AND pdl.itemid!=di.id;

SELECT * FROM `m_partner_deal_link` WHERE itemid='327746349439';

SELECT * FROM king_dealitems di
WHERE di.is_pnh=0 AND di.pnh_id=0 AND di.id NOT IN (SELECT itemid FROM m_partner_deal_link pdl);

SELECT * FROM `m_partner_deal_link` WHERE itemid='1113412718';

SELECT * FROM m_partner_deal_link WHERE itemid='1113412718' AND partner_id='7'

SELECT 1 FROM m_partner_deal_link WHERE itemid='1113412718' AND partner_id=7

SELECT GROUP_CONCAT(p.name) AS partner_names FROM `m_partner_deal_link` pdl
JOIN partner_info p ON p.id=pdl.partner_id
	WHERE pdl.itemid='1113412718' AND pdl.partner_ref_no!=''
	#GROUP BY pdl.partner_id
	ORDER BY p.name ASC ;
	
SELECT *,CONCAT(rb.rack_name,'',rb.bin_name) AS rack_name FROM t_imei_no i
JOIN t_stock_info stk ON stk.stock_id=i.stock_id
JOIN m_rack_bin_info rb ON rb.id=stk.rack_bin_id AND rb.is_active=1
WHERE i.product_id='206954' AND i.imei_no='353070061934390';

SELECT i.*,CONCAT(rb.rack_name,'',rb.bin_name) AS rack_name  FROM t_imei_no i
JOIN t_stock_info stk ON stk.stock_id=i.stock_id
JOIN m_rack_bin_info rb ON rb.id=stk.rack_bin_id AND rb.is_active=1
WHERE i.product_id='206954'  AND STATUS=0 AND i.reserved_batch_rowid=0 AND DATE(FROM_UNIXTIME(i.created_on)) BETWEEN "2012-01-01" AND "2014-10-10"  GROUP BY i.imei_no ORDER BY i.created_on DESC LIMIT 0,25

SELECT *,FROM_UNIXTIME(created_on) FROM t_imei_no WHERE STATUS=1 ORDER BY created_on DESC;


SELECT COUNT(*) AS t 
FROM t_imei_no i 
WHERE i.product_id='263142' AND STATUS=1 
#and i.reserved_batch_rowid!=0 
AND DATE(FROM_UNIXTIME(i.created_on)) BETWEEN "2012-08-01" AND "2014-10-11" 
ORDER BY i.created_on DESC

SELECT i.*,CONCAT(rb.rack_name,'',rb.bin_name) AS rack_name,stk.mrp  FROM t_imei_no i
	LEFT JOIN t_stock_info stk ON stk.stock_id=i.stock_id
	LEFT JOIN m_rack_bin_info rb ON rb.id=stk.rack_bin_id AND rb.is_active=1
	WHERE i.product_id='263142'  AND STATUS=1 AND i.reserved_batch_rowid!=0 AND DATE(FROM_UNIXTIME(i.created_on)) BETWEEN "2012-08-01" AND "2014-10-11"  GROUP BY i.imei_no ORDER BY stk.stock_id ASC,rb.is_damaged ASC,i.created_on DESC LIMIT 0,25
	

#===NEW SOLD BY AMAZON
SELECT IFNULL(SUM(o.quantity*l.qty),0) AS s 
FROM m_product_deal_link l
JOIN king_orders o ON o.itemid=l.itemid 
JOIN m_partner_deal_link pl ON pl.itemid=l.itemid
WHERE 
o.time > (UNIX_TIMESTAMP()-(24*60*60*60) ) AND o.time < UNIX_TIMESTAMP() AND l.product_id='4993' AND partner_id!=0 AND partner_id!=''
			


SELECT * FROM  m_product_deal_link l

SELECT SUM(available_qty) AS s,mrp,
	a.location_id,a.rack_bin_id,
	CONCAT(c.rack_name,bin_name) AS rbname,c.is_damaged,
	IFNULL(product_barcode,'') AS pbarcode,
	a.stock_id,a.expiry_on,offer_note     
	FROM t_stock_info  a 
	JOIN m_storage_location_info b ON b.location_id = a.location_id 
	JOIN m_rack_bin_info c ON c.id = a.rack_bin_id 
	JOIN 
	WHERE product_id='4993' 
	GROUP BY mrp,pbarcode,a.location_id,a.rack_bin_id 
	HAVING SUM(available_qty)>=0 
	ORDER BY mrp ASC;
	
SELECT IFNULL(SUM(t.available_qty),0) AS current_stock  
FROM t_stock_info t JOIN m_rack_bin_info rb ON t.rack_bin_id = rb.id  
WHERE t.product_id = '8966' AND rb.is_damaged=0;


SELECT IFNULL(SUM(o.quantity*l.qty),0) AS s FROM m_product_deal_link l 
JOIN king_orders o ON o.itemid=l.itemid 
JOIN m_partner_deal_link pl ON pl.itemid!=o.itemid
WHERE l.product_id='8966' AND o.time < UNIX_TIMESTAMP()


SELECT * FROM pnh_m_franchise_info WHERE franchise_id='460'

SELECT * FROM pnh_franchise_account_summary WHERE invoice_no=20141027252
unreconcile:2538 
cr unreconcile: 2537.88
SELECT * FROM king_invoice WHERE invoice_no=20141027252

# --- action_type=1 invoice debit amount
# --- action_type=7 invoice credit note amount

SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no=20141027254


SELECT * FROM pnh_franchise_account_summary WHERE invoice_no=20141027254
unreconcile:31144.42 
cr unreconcile: 1888.78

#==============available partner stock================
SELECT SUM(stk.available_qty) AS available_qty FROM t_stock_info stk
JOIN m_product_info p ON p.product_id=stk.product_id
JOIN partner_info pt ON pt.partner_rackbinid=stk.rack_bin_id
WHERE stk.available_qty >0 AND stk.product_id='4426'
#==============available partner stock================
SELECT * FROM pnh_m_franchise_info WHERE franchise_id='460'
"franchise_id"	"pnh_franchise_id"	"franchise_type"	"franchise_name"	"	address						"	"locality"	"taluk"	"hobli"	"city"		"postcode"	"state"		"territory_id"	"town_id"	"class_id"	"is_lc_store"	"is_sch_enabled"	"sch_discount"	"sch_discount_start"	"sch_discount_end"	"security_deposit"	"current_balance"	"credit_limit"	"last_credit"	"login_mobile1"	"login_mobile2"	"app_version"	"email_id"		"assigned_to"	"no_of_employees"	"store_name"		"store_area"	"lat"			"long"			"store_open_time"	"store_close_time"	"store_tin_no"	"store_pan_no"	"store_service_tax_no"	"store_reg_no"	"own_rented"	"internet_available"	"website_name"	"business_type"	"security_question"	"security_answer"	"security_question2"	"security_answer2"	"security_custom_question"	"security_custom_question2"	"is_prepaid"	"created_by"	"modified_by"	"created_on"	"modified_on"	"is_suspended"	"suspended_on"	"suspended_by"	"reason"	"purchase_limit"	"new_credit_limit"	"price_type"	"is_consolidated_payment"	"max_credit_limit"	"billing_type"	"tab_simno"	"opt_maxcreditlimit"	"assigned_rmfid"	"allow_sms"
"460"		"34538671"		"0"			"Praveen Communication"	"Belgaum, No.6, Hanuman Mandir Complex,Near Kapileswar Talav,"	"Belgaum"	\N	\N	"Belgaum"	"590001"	"Karanataka"	"14"		 "212"		"2"		"0"		"0"				"0"	"0"			"0"			 "12500"		 "-123252.501"		 "274000"	"19300"		"9035255585"	""		"0"		"hello@storeking.in"	"0"		"2"			"Praveen Communication"	"400"		"12.9694992850562"	"77.59423840625"	"05:00:00"		"05:00:00"		"AApplied"	""		""			""		 "0"		""			""		"Retail"	"0"			"Praveen"		"1"			"Belgaum"		""				""				"0"		"16"		"23"		"1393481686"	"1393495578"	"0"		"0"		"0"		\N		"0"			"0"			"1"		"1"				"0"			"0"		\N		"0"			"0"			"1"


SELECT * FROM m_town_territory_link WHERE employee_id=88;
SELECT * FROM m_employee_info WHERE employee_id=88;

SELECT * FROM king_admin 


SELECT * FROM t_imei_update_log

SELECT * FROM t_imei_no WHERE product_id=132979 AND STATUS=0 AND imei_no=911304152875682
SELECT * FROM t_stock_info WHERE product_id=132979
136705
163232

#=======:::::Member fee issue revert::::===========
pnh_member_fee
king_orders
king_transactions
pnh_franchise_account_summary

DESC king_dealitems
DESC deal_member_price_changelog


SELECT SUM(pstk.qty) AS partner_pending_stk,SUM(rs.qty) AS pnh_pending_stk
FROM t_partner_reserved_batch_stock pstk
LEFT JOIN t_reserved_batch_stock rs ON rs.product_id=pstk.product_id AND rs.status=0
WHERE pstk.status=0  AND a.partner_id != 0 AND pstk.itemid=367511493147;

DESC t_partner_reserved_batch_stock;
DESC t_reserved_batch_stock;


SELECT rstk.itemid,rstk.stock_info_id,stk.rack_bin_id,stk.location_id
	,stk.product_barcode,stk.mrp,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name AS deal
	FROM t_partner_reserved_batch_stock rstk
	JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
	JOIN king_dealitems di ON di.id=rstk.itemid
	WHERE rstk.transfer_id='60' AND rstk.itemid='5398916139' AND rstk.product_id='145603' GROUP BY rstk.id
	
SELECT * FROM king_admin
b343ef440fded01538c145094a034cea
b343ef440fded01538c145094a034cea
SELECT MD5('shivaraj');

SELECT VALUE FROM m_config_params WHERE NAME = 'ALLOWED_IP_ADDR'

115.115.231.126,59.90.238.133,59.90.238.134,223.227.116.252,223.227.101.3,171.78.247.9,223.227.72.191

SELECT SUM(pstk.qty) AS partner_pending_stk
	,SUM(rs.qty) AS pnh_pending_stk
	FROM t_partner_reserved_batch_stock pstk
	LEFT JOIN t_reserved_batch_stock rs ON rs.product_id=pstk.product_id  AND rs.status=0
	WHERE pstk.status=0 AND pstk.itemid='367511493147'
	GROUP BY pstk.id,rs.id;
	
-- #	group by pstk.product_id;

SELECT 55

SELECT pstk.*,SUM(pstk.qty) AS partner_pending_stk FROM t_partner_reserved_batch_stock pstk	WHERE pstk.status=0 AND pstk.itemid='367511493147'

SELECT * FROM t_reserved_batch_stock
DESC t_partner_reserved_batch_stock

DESC t_reserved_batch_stock;

SELECT * FROM t_reserved_batch_stock WHERE product_id='4743' AND STATUS=0;

SELECT (pstk.qty) AS partner_pending_stk
	,(rs.qty) AS pnh_pending_stk
	FROM t_stock_info stk
	LEFT JOIN t_partner_reserved_batch_stock pstk ON pstk.stock_info_id=stk.stock_id AND pstk.status=0
	LEFT JOIN t_reserved_batch_stock rs ON rs.stock_info_id=stk.stock_id AND rs.status=0
	WHERE pstk.itemid='367511493147'
	GROUP BY pstk.product_id
	
#Global
SELECT * FROM m_product_deal_link pdl
LEFT JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id AND rs.status=0
LEFT JOIN  t_partner_reserved_batch_stock pstk ON pstk.product_id=pdl.product_id AND pstk.status=0
WHERE pdl.itemid='367511493147' AND pdl.is_active=1

#1
SELECT SUM(rs.qty) AS pnh_pending_stk FROM m_product_deal_link pdl
JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id AND rs.status=0
WHERE pdl.itemid='367511493147' AND pdl.is_active=1
GROUP BY pdl.product_id;

#2
SELECT (pstk.qty) AS partner_pending_stk FROM m_product_deal_link pdl
JOIN  t_partner_reserved_batch_stock pstk ON pstk.product_id=pdl.product_id AND pstk.status=0
WHERE pdl.itemid='367511493147' AND pdl.is_active=1
GROUP BY pdl.product_id

SELECT * FROM pnh_t_receipt_info WHERE receipt_id=9158;


SELECT IFNULL(SUM(g.pnh_pending_stk),0) AS ttl_sum FROM (
	(
		SELECT SUM(rs.qty) AS pnh_pending_stk FROM m_product_deal_link pdl
		JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id AND rs.status=0
		WHERE pdl.is_active=1 AND pdl.itemid='837658718365'
		GROUP BY pdl.product_id
	)
UNION
	(
		SELECT SUM(pstk.qty) AS partner_pending_stk FROM m_product_deal_link pdl
		JOIN  t_partner_reserved_batch_stock pstk ON pstk.product_id=pdl.product_id AND pstk.status=0
		WHERE  pdl.is_active=1 AND pdl.itemid='837658718365'
		GROUP BY pdl.product_id
	)
) AS g

SELECT 70+55;

SELECT IFNULL(SUM(g.pnh_pending_stk),0) AS ttl_sum FROM (
	(
	SELECT SUM(rs.qty) AS pnh_pending_stk FROM m_product_deal_link pdl
	JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id
	WHERE pdl.is_active=1 AND pdl.itemid='570419904665' AND rs.status=0
	GROUP BY pdl.itemid
	)
	UNION
	(
	SELECT SUM(pstk.qty) AS partner_pending_stk FROM m_product_deal_link pdl
	JOIN  t_partner_reserved_batch_stock pstk ON pstk.product_id=pdl.product_id AND pstk.status=0
	WHERE  pdl.is_active=1 AND pdl.itemid='570419904665'
	GROUP BY pdl.itemid
	)
) AS g;


SELECT * FROM t_reserved_batch_stock

SELECT IFNULL(SUM(o.quantity),0) AS s 
	FROM m_product_deal_link l
	JOIN king_orders o ON o.itemid=l.itemid 
	JOIN m_partner_deal_link pl ON pl.itemid=l.itemid
	WHERE o.status=2 
		AND o.time > (UNIX_TIMESTAMP()-(60*60*24*60) ) AND pl.partner_id IS NOT NULL AND pl.partner_id!=0
		AND o.time < UNIX_TIMESTAMP('2014-09-02 17:15:54')
	AND l.product_id='2602';
	
	#=>84=>
	
SELECT i.*,p.product_name FROM t_po_product_link i JOIN m_product_info p ON p.product_id=i.product_id 
WHERE p.product_id=2602 AND i.po_id=10123
#i.po_id=? and i.is_active=1 

SELECT DISTINCT * FROM king_orders
DESC m_partner_deal_link;
DESC m_product_deal_link;
SELECT DISTINCT partner_id,itemid FROM m_partner_deal_link GROUP BY partner_id
SELECT * FROM m_partner_deal_link

SELECT SUM(o.quantity*l.qty) AS ttl_p_qty,GROUP_CONCAT(pl.partner_id),pl.itemid 
FROM m_partner_deal_link pl
JOIN king_orders o ON o.itemid=pl.itemid
JOIN m_product_deal_link l ON l.itemid=o.itemid
WHERE o.status=2 
AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')
AND l.product_id='2602'


SELECT IFNULL(SUM(o.quantity*l.qty),0) AS ttl_p_qty
FROM m_partner_deal_link pl
JOIN king_orders o ON o.itemid=pl.itemid
JOIN m_product_deal_link l ON l.itemid=o.itemid
WHERE o.status=2 
AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')
AND l.product_id='2602';


SELECT IFNULL(SUM(o.quantity*l.qty),0) AS s FROM m_product_deal_link l JOIN king_orders o ON o.itemid=l.itemid WHERE l.product_id=? AND o.time>".(time()-(24*60*60*60)).' and o.time < ?"


SELECT IFNULL(SUM(o.quantity*l.qty),0) AS ttl_p_qty,GROUP_CONCAT(pl.partner_id) AS pids,GROUP_CONCAT(pl.itemid) AS itms,DATE_FORMAT('%Y-%m-%d',o.time) AS crdn
	FROM m_partner_deal_link pl
	JOIN king_orders o ON o.itemid=pl.itemid
	JOIN m_product_deal_link l ON l.itemid=o.itemid
	WHERE o.status=2 
	AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')
	#AND o.time > (UNIX_TIMESTAMP()-(60*60*24*60) ) AND o.time < UNIX_TIMESTAMP('2014-09-02 17:15:54')
	#AND DATE(FROM_UNIXTIME(o.time)) > (DATE_ADD(CURDATE(),INTERVAL - 60 DAY))
	AND l.product_id='2126'
	
	
SELECT IFNULL(SUM(o.quantity*l.qty),0) AS ttl_p_qty,GROUP_CONCAT(pl.partner_id) AS pids,GROUP_CONCAT(pl.itemid) AS itms,DATE_FORMAT('%Y-%m-%d',o.time) AS crdn
	FROM m_partner_deal_link pl
	JOIN king_orders o ON o.itemid=pl.itemid
	JOIN m_product_deal_link l ON l.itemid=o.itemid
	WHERE o.status=2 
	AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')
	AND l.product_id='2126'
#++++++++++++++++++++++++++++++++++++++++++++++++++++
SELECT dl.product_id, IFNULL(SUM(o.quantity*dl.qty),0) AS ttl_part_sold
	FROM m_product_deal_link dl
	JOIN king_orders o ON dl.itemid=o.itemid		
	JOIN king_transactions t ON t.transid = o.transid
	WHERE o.status=2 AND partner_id > 0
	AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')
	AND dl.product_id='2602'
	GROUP BY dl.product_id
#++++++++++++++++++++++++++++++++++++++++++++++++++++	
	
SELECT o.* FROM king_orders o
JOIN king_transactions t ON t.transid = o.transid
WHERE o.itemid IN (SELECT itemid FROM m_product_deal_link WHERE product_id = 2602) AND t.partner_id > 0 AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54') AND o.status = 2

SELECT * FROM m_product_deal_link WHERE itemid = 7384723488;

SELECT  * FROM king_orders o WHERE DATE(FROM_UNIXTIME(o.time)) >= DATE('2014-09-02 17:15:54' - INTERVAL 2 MONTH) LIMIT 4;

SELECT  * FROM king_orders o WHERE FROM_UNIXTIME(o.time) > (DATE_ADD('2014-09-02 17:15:54', INTERVAL -2 MONTH)) LIMIT 4;

SELECT '2014-09-02 17:15:54',SUBDATE('2014-09-02 17:15:54',INTERVAL 2 MONTH) FROM king_orders o WHERE FROM_UNIXTIME(o.time) BETWEEN SUBDATE('2014-09-02 17:15:54',INTERVAL 2 MONTH) AND '2014-09-02 17:15:54'  LIMIT 4;

SELECT '2014-09-02 17:15:54',FROM_UNIXTIME(UNIX_TIMESTAMP()-(60*60*24*60) ) FROM king_orders o WHERE o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP('2014-09-02 17:15:54')  LIMIT 4;


SELECT IFNULL(SUM(stk.available_qty),0) AS available_qty FROM t_stock_info stk
	JOIN m_product_info p ON p.product_id=stk.product_id
	JOIN partner_info pt ON pt.partner_rackbinid=stk.rack_bin_id
	WHERE stk.available_qty >0 AND stk.product_id=2602 AND stk.created_on < NOW()

DESC t_stock_info;

DESC m_product_group_deal_link;

SELECT pgo.product_id, IFNULL(SUM(o.quantity*l.qty),0) AS ttl_part_sold
	FROM m_product_group_deal_link l
	JOIN king_orders o ON l.itemid=o.itemid		
	JOIN products_group_orders pgo ON pgo.order_id = o.id 
	JOIN king_transactions t ON t.transid = o.transid
	WHERE o.status=2 AND t.partner_id > 0
	AND FROM_UNIXTIME(o.time) BETWEEN SUBDATE('2014-09-02 17:15:54',INTERVAL 2 MONTH) AND '2014-09-02 17:15:54'
	AND pgo.product_id=2602
	GROUP BY pgo.product_id
	
	
	
SELECT IFNULL(SUM(o.quantity*l.qty),0) AS s 
	FROM m_product_group_deal_link l 
	JOIN king_orders o ON o.itemid=l.itemid 
	JOIN products_group_orders pgo ON pgo.order_id = o.id 
	WHERE pgo.product_id=? AND FROM_UNIXTIME(o.time) BETWEEN SUBDATE(?,INTERVAL 2 MONTH) AND ?;

SELECT * FROM `pnh_users`;

#===========================
# Oct_20_2014
ALTER TABLE `pnh_users` CHANGE `franchise_id` `reference_id` BIGINT(20) NULL COMMENT 'franchise_id,m_fid';
#===========================

# Pending ord qty
SELECT IFNULL(SUM(o.quantity*l.qty),0) AS s 
FROM m_product_deal_link l 
JOIN king_orders o ON o.itemid=l.itemid 
WHERE l.product_id='2602' AND o.status = 0 AND FROM_UNIXTIME(o.time) < ('2014-09-02 17:15:54');

# Pending ord qty
SELECT IFNULL(SUM(g.pnh_pending_stk),0) AS ttl_sum FROM (
(
	SELECT SUM(rs.qty) AS pnh_pending_stk FROM m_product_deal_link pdl
	JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id AND rs.status=0
	WHERE pdl.is_active=1 AND pdl.itemid=?
	GROUP BY pdl.product_id
)
UNION
(
	SELECT SUM(pstk.qty) AS partner_pending_stk FROM m_product_deal_link pdl
	JOIN  t_partner_reserved_batch_stock pstk ON pstk.product_id=pdl.product_id AND pstk.status=0
	WHERE  pdl.is_active=1 AND pdl.itemid=?
	GROUP BY pdl.product_id
)
) AS g
#================
SELECT SUM(rs.qty) AS pnh_pending_stk 
	FROM m_product_deal_link pdl
	JOIN t_reserved_batch_stock rs ON rs.product_id=pdl.product_id AND rs.status=0
	WHERE pdl.is_active=1 AND pdl.product_id=2602
	#AND pdl.itemid=?
	GROUP BY pdl.product_id
	

SELECT * FROM pnh_member_insurance WHERE sno=2712
SELECT * FROM pnh_member_offers WHERE insurance_id=2712;

mem_receipt_amount=8820

#UPDATE `pnh_member_insurance` SET `mem_receipt_amount` = '8820' , `status_ship` = '1' , `status_deliver` = '1' WHERE `sno` = '2712';

#UPDATE `pnh_member_offers` SET `delivery_status` = '1' WHERE `sno` = '4059'; 
#UPDATE `pnh_member_offers` SET `delivery_status` = '1' WHERE insurance_id=2712;


SELECT mi.*,a.username,mo.transid_ref AS transid,mo.process_status,mo.delivery_status,o.id AS orderid,f.franchise_name,i.invoice_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
														FROM pnh_member_insurance mi
														JOIN pnh_member_offers mo ON mo.insurance_id = mi.sno
														LEFT JOIN king_admin a ON a.id = mi.created_by
														JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
														JOIN king_orders o ON o.id = mi.order_id AND o.status != 3
														JOIN king_invoice i ON i.order_id = mi.order_id 
														LEFT JOIN t_imei_no imei ON imei.order_id = o.id
														LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id=o.id
														WHERE mi.sno IN (2712) 
														GROUP BY mi.sno;
														
														
-- UPDATE `pnh_member_insurance` SET `created_by` = '37' WHERE `sno` = '2712'; 
-- UPDATE `pnh_member_offers` SET `created_by` = '37' WHERE `sno` = '4059';

UPDATE king_tmp_orders SET approval_status=0 WHERE transid='PNH41918';
UPDATE king_tmp_transactions SET approval_status=0 WHERE transid='PNH41918';

SELECT billing_type FROM king_tmp_orders WHERE transid='PNH41918';
SELECT billing_type FROM king_tmp_transactions WHERE transid='PNH41918';
#=====================================================
# Oct_21_2014 - Shivaraj temp order table billing type added
ALTER TABLE `king_tmp_orders` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NOT NULL AFTER `order_imei_nos`; 
#=====================================================
SELECT billing_type FROM king_orders WHERE transid='PNH41918';

SELECT * FROM king_transactions WHERE transid='PNH49218';
SELECT * FROM king_orders WHERE transid='PNH49218';
SELECT 501+220;


SELECT IFNULL(SUM(g.pqty),0) AS ttl_sum FROM (
	(
		SELECT SUM(o.quantity) AS pqty 
		FROM king_orders o
		LEFT JOIN king_invoice i ON i.id = o.id AND i.invoice_status = 1 
		WHERE o.itemid='570419904665' AND o.status=1 AND i.id IS NULL
		GROUP BY o.itemid
	)
	UNION
	(
		SELECT SUM(stp.item_transfer_qty) AS pqty 
		FROM t_partner_reserved_batch_stock rs
		JOIN t_partner_stock_transfer_product_link stp ON stp.id=rs.tp_id
		WHERE  rs.itemid='570419904665' AND rs.status=0
		GROUP BY rs.itemid
	)
) AS g;

#========================
	SELECT SUM(stp.item_transfer_qty) AS pqty 
		FROM t_partner_reserved_batch_stock rs
		JOIN t_partner_stock_transfer_product_link stp ON stp.id=rs.tp_id
		WHERE  rs.itemid='570419904665' AND rs.status=0
		GROUP BY rs.itemid
#========================
DESC t_reserved_batch_stock;
DESC t_partner_stock_transfer_product_link;

SELECT SUM(o.quantity) AS pnh_pending_stk 
	FROM king_orders o
	LEFT JOIN king_invoice i ON i.id = o.id AND i.invoice_status = 1 
	WHERE o.itemid='570419904665' AND o.status=1 AND i.id IS NULL
GROUP BY o.itemid;

SELECT IFNULL(SUM(g.pqty),0) AS ttl_sum FROM (
	(
		SELECT SUM(o.quantity) AS pqty 
		FROM king_orders o
		LEFT JOIN king_invoice i ON i.id = o.id AND i.invoice_status = 1 
		WHERE o.status=1 AND i.id IS NULL AND o.itemid='570419904665'
		GROUP BY o.itemid
	)
	UNION
	(
		SELECT SUM(stp.item_transfer_qty) AS pqty 
		FROM t_partner_reserved_batch_stock rs
		JOIN t_partner_stock_transfer_product_link stp ON stp.id=rs.tp_id
		WHERE  rs.status=0 AND rs.itemid='570419904665'
		GROUP BY rs.itemid
	)
) AS g

#=======================================
SELECT SUM(o.quantity) AS pqty 
	FROM king_orders o
	LEFT JOIN king_invoice i ON i.id = o.id AND i.invoice_status = 1 
	WHERE o.status=1 AND i.id IS NULL AND o.itemid='570419904665'
	GROUP BY o.itemid
	
	
SELECT SUM(o.quantity) AS pqty 
	FROM king_orders o
	#join m_product_deal_link pdl on pdl.itemid
	WHERE o.status=0 AND o.itemid='570419904665'
	GROUP BY o.itemid
	
SELECT SUM(o.quantity) AS pqty 
	FROM king_orders o
	WHERE o.status=0 AND o.itemid='570419904665'
	GROUP BY o.itemid;
	
SELECT SUM(stp.item_transfer_qty) AS pqty 
	FROM t_partner_reserved_batch_stock rs
	JOIN t_partner_stock_transfer_product_link stp ON stp.id=rs.tp_id
	WHERE  rs.status=0 AND rs.itemid='570419904665'
	GROUP BY rs.itemid;
	
SELECT IFNULL(SUM(stp.product_transfer_qty),0) AS pqty FROM `t_partner_stock_transfer_product_link` stp 
WHERE stp.is_active=1 AND stp.transfer_status=0 AND stp.itemid='570419904665';

SELECT IFNULL(SUM(stp.product_transfer_qty),0) AS pqty FROM `t_partner_stock_transfer_product_link` stp 
JOIN t_partner_stock_transfer st ON st.transfer_id=stp.transfer_id
WHERE stp.is_active=1 AND st.transfer_status=0 AND stp.itemid='8178248576' 
GROUP BY stp.itemid;

SELECT * FROM user_roles;


SELECT rstk.itemid,rstk.stock_info_id,stk.rack_bin_id,stk.location_id
		,stk.product_barcode,stk.mrp,rstk.qty,CONCAT(rb.rack_name,'-',rb.bin_name) AS rbname,di.name AS deal
	 FROM t_partner_reserved_batch_stock rstk
	 JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	 JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id
	 JOIN king_dealitems di ON di.id=rstk.itemid
	WHERE rstk.transfer_id=72 AND rstk.itemid=? AND rstk.product_id=? GROUP BY rstk.id;
	

SELECT * FROM m_product_deal_link WHERE is_active=1 AND itemid=1534367354




SELECT rstk.itemid,rstk.product_id
	#,sum(rstk.qty) as qty
	,stk.mrp,rstk.stock_info_id
#	,replace(group_concat(DISTINCT stk.product_barcode),',','_') as barcodes
	,tpl.item_transfer_qty,tpl.product_transfer_qty
		,di.name AS deal_name,p.product_name,rstk.itemid
		,CONCAT('".IMAGES_URL."items/small/',di.pic,'.jpg') AS image_url
	FROM t_partner_reserved_batch_stock rstk
	JOIN t_partner_stock_transfer_product_link tpl ON tpl.id=rstk.tp_id
	JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
	JOIN king_dealitems di ON di.id=rstk.itemid
	LEFT JOIN m_product_deal_link plnk ON plnk.product_id=rstk.product_id AND plnk.is_active=1 AND plnk.itemid=rstk.itemid 
	LEFT JOIN m_product_info p ON p.product_id=rstk.product_id

	WHERE rstk.transfer_id=44 AND di.is_combo=0
	GROUP BY rstk.itemid;
	
	# 1132698749,1138258732,1534367354(3),5398916139,8178248576
	
	
	
	
	

SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=46; 
#status=2 to 0

SELECT * FROM t_partner_stock_transfer WHERE transfer_id=46;
is_active=0 TO 1& transfer_status=4 TO 1

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=46;
is_active=0 TO 1& transfer_status=3 TO 1




UPDATE king_dealitems SET is_combo=1 WHERE id IN 
	(
	SELECT GROUP_CONCAT(DISTINCT di.id) AS ids
			FROM king_dealitems di
			JOIN m_product_deal_link pdl ON pdl.itemid=di.id AND is_active=1
			WHERE di.is_pnh=0 AND di.is_combo=0
			GROUP BY di.id
			HAVING COUNT(pdl.id)>1
	) LIMIT 1;
	
	109
	

	
	
	UPDATE king_dealitems SET is_combo=1 WHERE id IN ("ids","71285566","92582258","1117277256","1436985411","1534367354","1545464937","1689849416","1919368174","2233648463","2355413918","2389454177","2918855168","2923861289","3325123443","3458614718","3497889759","3548447123","3775776282","3964753145","4237219959","4255458551","4267622626","4433418749","4435316747","4473186336","4496985952","4538715487","4571437882","4642918232","4655437436","4661652713","4691273841","4864363851","4875181848","4894396924","4895128433","4912219181","4921282626","4938284156","5144691532","5286776628","5587678674","5775147649","5979985574","6226274251","6323744289","6357495271","6358391828","6367695596","6411155229","6627147564","6651327969","6756216317","7166683139","7371185156","7496977454","7511132574","7658799767","7682184646","7725246775","7843393485","8392598126","8679914893","8961583398","9354195826","9489376857","9631112761","9632248965","9667734985","9669839466","9759725223","9967258716","9997196734","144376456641","147558652386","158776324557","163914287641","175984391915","176277296278","176885419757","183887657973","194156217112","246689223143","263193342979","281927371834","334885286949","362154297753","365247235342","415724242474","475833627568","478898394577","494278777366","526578668728","535544455512","538579337458","548147443639","613272623146","643972438356","742967126383","777465364296","789426157159","838438424868","843817188145","846346952192","848648293822","876661682894","911617958266","936519245463","951198426659")
	
#Oct_29_2014

#drop table m_member_subscription_plan_schemes,m_member_subscription_plans,m_member_plan_link,m_franchise_combo_preference_link,m_franchise_combo_preference,m_combo_categories
#	,m_member_family_list,m_member_feedback_link,m_member_subscription_plan_orderlist;
	
#NEWWW
SELECT COUNT(DISTINCT o.transid) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid='163427'

SELECT COUNT(DISTINCT o.transid) AS t FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid='163427'

SELECT DISTINCT transid AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid='163427'

SELECT DISTINCT transid AS t FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid='163427'
SELECT * FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid='163427'
DESC king_tmp_orders

SELECT * FROM king_tmp_orders o WHERE transid='PNHULE27785';

SELECT IFNULL(partner_ref_no,'N/A') AS `asin`  
	FROM m_partner_deal_link a 
	JOIN king_dealitems b ON b.id = a.itemid 
	WHERE a.itemid = '9232662758' AND a.partner_id = 7;
	

SELECT user_id FROM  pnh_member_info WHERE mobile='1011111111'
	

SELECT COUNT(DISTINCT o.transid) AS t 
FROM king_orders o 
WHERE 1 
AND o.status != 3 
AND o.userid=163430

163430

SELECT COUNT(DISTINCT o.transid) AS t 
FROM king_tmp_orders o 
LEFT JOIN king_orders mo ON mo.userid=o.userid
WHERE 1 AND o.approval_status != 2 AND mo.status !=3 AND o.userid=163432

DESC king_orders
DESC king_tmp_orders
#

SELECT DISTINCT GROUP_CONCAT(mo.transid),GROUP_CONCAT(o.status),COUNT(o.transid) AS t 
FROM king_tmp_orders o 
LEFT JOIN king_orders mo ON mo.userid=o.userid AND o.approval_status=1 AND mo.status !=3
WHERE 1 AND o.approval_status != 2 AND o.userid=163432

SELECT id,STATUS,transid,userid FROM king_orders WHERE transid IN ('','PNH73454','PNH72194')

SELECT mo.status,o.transid AS t 
FROM king_tmp_orders o 
LEFT JOIN king_orders mo ON mo.userid=o.userid AND mo.status !=3 AND mo.transid=o.transid
WHERE 1 AND o.approval_status != 2 AND o.userid=163432

SELECT * FROM king_tmp_orders o WHERE 



SELECT (g.trans) AS t FROM (
(
	SELECT DISTINCT o.transid AS trans FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=163432
)
UNION
(
	SELECT DISTINCT o.transid AS trans FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=163432
) 
)AS g


SELECT COUNT(DISTINCT o.transid) AS t,GROUP_CONCAT(DISTINCT o.transid) AS trans FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=163432 
	AND transid NOT IN (SELECT DISTINCT o.transid AS trans FROM king_orders o WHERE 1 AND o.status = 3 AND o.userid=163432)
	
SELECT COUNT(DISTINCT o.transid) AS t,GROUP_CONCAT(DISTINCT o.transid) AS trans FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=163432 
	AND o.transid NOT IN (SELECT DISTINCT o.transid AS trans FROM king_orders o WHERE 1 AND o.status = 3 AND o.userid=163432)
	
SELECT * FROM pnh_franchise_account_summary WHERE invoice_no=20141031953

SELECT (18900+50+100)-2230.2

16669.8=>16819.8


SELECT COUNT(*) AS is_offer_gvn FROM pnh_member_offers mo
	LEFT JOIN king_tmp_orders o ON o.id=mo.order_id AND o.status!=3
	WHERE mo.offer_type=1 AND mo.offer_type NOT IN (0,2,3) AND mo.process_status!=1 AND mo.member_id=22039099
	HAVING is_offer_gvn IS NOT NULL

SELECT offer_type FROM pnh_member_offers WHERE member_id=22039097;

# Oct_31_2014

SELECT * FROM pnh_member_info m WHERE m.member_fee_is_added=1 AND pnh_member_id=22039099

member_fee_collected
member_fee

member_fee_invoice

SELECT * FROM king_users
SELECT * FROM pnh_member_info WHERE  pnh_member_id=22039099;

#===============================
#Oct_31_2014 - Shivaraj - Member fee flag in member table
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee_collected` TINYINT(1) DEFAULT 0 NULL AFTER `voucher_bal_validity`
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee` FLOAT NULL AFTER `member_fee_collected`, ADD COLUMN `member_fee_invoice` VARCHAR(50) NULL AFTER `member_fee`;
#===============================

SELECT * FROM  pnh_member_info WHERE user_id=163432
22039099
user_id,pnh_member_id,first_name,mobile,email,member_fee_collected

SELECT i.pnh_id,partner_id,d.catid,d.menuid,d.brandid,t.is_pnh,i_discount,i_coup_discount,i.tax,o.transid,o.id,o.itemid,i_orgprice,i_price,t.cod,t.ship,o.quantity,SUM(i.orgprice*o.quantity) AS mrp_amt,SUM(i.price*o.quantity) AS price_amt,o.other_price,o.is_memberprice,t.franchise_id,o.billing_type,o.userid,o.member_id
										FROM king_orders o 
										JOIN king_dealitems i ON i.id=o.itemid 
										JOIN king_transactions t ON t.transid = o.transid
										JOIN king_deals d ON d.dealid = i.dealid 
										WHERE o.id IN ('6224617261')
										GROUP BY o.id ORDER BY o.sno ASC
										
SELECT * FROM king_invoice LIMIT 20

UPDATE pnh_member_info SET member_fee_is_added=?,member_fee=?,member_fee_invoice=? WHERE member_id=?

UPDATE pnh_member_info SET member_fee_invoice='' WHERE member_fee_collected=1 AND pnh_member_id=0

SELECT user_id,pnh_member_id,first_name,mobile,email,member_fee_collected,member_fee FROM  pnh_member_info WHERE user_id=163432

SELECT id,transid,order_id,invoice_no FROM king_invoice WHERE invoice_no=1 AND invoice_status=1

SELECT * FROM pnh_member_info WHERE pnh_member_id=22039099; #21111111;

#1
UPDATE pnh_member_info SET member_fee_collected=1,member_fee='50',member_fee_invoice='' WHERE pnh_member_id=22039099;
#2
UPDATE pnh_member_info SET member_fee_invoice='0000' WHERE member_fee_collected=1 AND pnh_member_id=22039099;
#3
UPDATE pnh_member_info SET member_fee_collected=0,member_fee_invoice='',member_fee=0 WHERE member_fee_collected=1 AND member_fee_invoice='0000' AND pnh_member_id=22039099;


# Nov_03

SELECT h.itemid,publish,live,product_id,lqty,aqty,dqty,is_sourceable,IF(SUM(ttl_l)=SUM(is_sourceable),0,1)*!is_group AS is_limited_stk,IF(SUM(ttl_l)=SUM(pstk),1,0) AS STATUS,is_combo,is_group,(MIN(dqty)) AS d_stk,IFNULL(SUM(o.quantity),0) AS oqty	
										FROM (
											SELECT g.itemid,product_id,lqty,aqty,is_sourceable,(FLOOR(aqty/lqty)) AS dqty,ttl_l,pstk,is_combo,is_group,publish,live
											FROM (
												SELECT a.itemid,a.product_id,b.is_sourceable,a.qty AS lqty,is_damaged,SUM(c.available_qty) AS aqty,
												COUNT(DISTINCT a.product_id) AS ttl_l,
												IF(a.qty<=SUM(c.available_qty),1,0) AS pstk
												FROM m_product_deal_link a 
												JOIN m_product_info b ON a.product_id = b.product_id  
												LEFT JOIN t_stock_info c ON c.product_id = b.product_id 
												LEFT JOIN m_rack_bin_info rb ON rb.id = c.rack_bin_id 
												LEFT JOIN  partner_info pt ON pt.partner_rackbinid=c.rack_bin_id AND pt.partner_rackbinid!=0
												WHERE itemid IN ('1693695852') AND a.is_active = 1 AND pt.id=7
												GROUP BY a.product_id,a.itemid,is_damaged
												#having (is_damaged = 0 or is_damaged is null)
											) AS g 
											JOIN king_dealitems di ON di.id = g.itemid 
											JOIN king_deals d ON d.dealid = di.dealid
											GROUP BY itemid,product_id 
										) AS h
										LEFT JOIN king_orders o ON o.itemid = h.itemid AND o.status = 0 
										GROUP BY h.itemid,IF(is_group,product_id,1)

"itemid"	"publish"	"live"	"product_id"	"lqty"	"aqty"	"dqty"	"is_sourceable"	"is_limited_stk"	"status"	"is_combo"	"is_group"	"d_stk"	"oqty"
"1693695852"	"1"	"1"	"4520"	"2"	"1"	"0"	"1"	"0"	"0"	"0"	"0"	"0"	"0"
"1693695852"	"1"	"1"	"4520"	"2"	"1"	"0"	"1"	"0"	"0"	"0"	"0"	"0"	"0"

SELECT * FROM partner_info pt
LEFT JOIN t_stock_info st ON st.rack_bin_id=pt.partner_rackbinid
LEFT JOIN m_rack_bin_info rb ON rb.id = pt.partner_rackbinid
WHERE pt.partner_rackbinid!=0 AND  pt.id=7 AND st.product_id=4520

SELECT partner_id FROM  king_transactions;

SELECT IFNULL(SUM(g.pqty),0) AS ttl_sum FROM (
(
	SELECT SUM(o.quantity) AS pqty 
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	WHERE o.status=0 AND o.itemid='1113412718'  AND tr.partner_id != 0 
	GROUP BY o.itemid
)
UNION
(
	SELECT IFNULL(SUM(stp.item_transfer_qty),0) AS pqty FROM `t_partner_stock_transfer_product_link` stp 
	JOIN t_partner_stock_transfer st ON st.transfer_id=stp.transfer_id
	WHERE stp.is_active=1 AND st.transfer_status=0 AND stp.itemid='1113412718'
	GROUP BY stp.itemid
)
) AS g;


SELECT h.itemid,publish,live,product_id,lqty,aqty,dqty,is_sourceable,IF(SUM(ttl_l)=SUM(is_sourceable),0,1)*!is_group AS is_limited_stk,IF(SUM(ttl_l)=SUM(pstk),1,0) AS STATUS,is_combo,is_group,(MIN(dqty)) AS d_stk,IFNULL(SUM(o.quantity),0) AS oqty	
FROM (
	SELECT g.itemid,product_id,lqty,aqty,is_sourceable,(FLOOR(aqty/lqty)) AS dqty,ttl_l,pstk,is_combo,is_group,publish,live
	FROM (
		SELECT a.itemid,a.product_id,b.is_sourceable,a.qty AS lqty,is_damaged,SUM(c.available_qty) AS aqty,
		COUNT(DISTINCT a.product_id) AS ttl_l,
		IF(a.qty<=SUM(c.available_qty),1,0) AS pstk
		FROM m_product_deal_link a 
		JOIN m_product_info b ON a.product_id = b.product_id  
		LEFT JOIN t_stock_info c ON c.product_id = b.product_id 
		LEFT JOIN m_rack_bin_info rb ON rb.id = c.rack_bin_id 
		
		WHERE itemid IN ('1113412718') AND a.is_active = 1  
		GROUP BY a.product_id,a.itemid,is_damaged
		 HAVING (is_damaged = 0 OR is_damaged IS NULL) 
	) AS g 
	JOIN king_dealitems di ON di.id = g.itemid 
	JOIN king_deals d ON d.dealid = di.dealid
	GROUP BY itemid,product_id 
) AS h
LEFT JOIN king_orders o ON o.itemid = h.itemid AND o.status = 0 
GROUP BY h.itemid,IF(is_group,product_id,1);
#=======================================================
#Nov_03_2014
ALTER TABLE `t_partner_stock_transfer` ADD COLUMN `transfer_option` TINYINT(11) DEFAULT 1 NULL COMMENT '1:to partner,2:from partner' AFTER `partner_id`;
ALTER TABLE `t_partner_reserved_batch_stock` ADD COLUMN `transfer_option` TINYINT(11) DEFAULT 1 NULL COMMENT '1:to partner,2:from partner' AFTER `transfer_id`;
#ALTER TABLE `t_partner_stock_transfer_product_link` ADD COLUMN `transfer_option` TINYINT(11) DEFAULT 1 NULL COMMENT '1:to partner,2:from partner' AFTER `transfer_id`;
#=======================================================
SELECT * FROM t_partner_stock_transfer WHERE transfer_id=47;


SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id
,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-185),1,0) AS mrp_diff
									FROM t_stock_info a
									JOIN m_rack_bin_info b ON a.rack_bin_id = b.id AND b.is_damaged=0
									JOIN m_product_info p ON p.product_id = a.product_id
									LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
									LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
									LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
									LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id 
								WHERE a.mrp > 0 AND a.product_id = '4447' AND available_qty > 0  
								GROUP BY stock_id,town_diff
								ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp;
								
								
SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=117; 
#status=2 to 1

SELECT * FROM t_partner_stock_transfer WHERE transfer_id=117;
is_active=0 TO 1& transfer_status=4 TO 1

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=117;
is_active=0 TO 1& transfer_status=3 TO 1


SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=117; 
#status=1

SELECT * FROM t_partner_stock_transfer WHERE transfer_id=117;
#is_active=1& transfer_status=1

SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=117;
#is_active=1& transfer_status=1

