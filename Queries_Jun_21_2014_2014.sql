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
