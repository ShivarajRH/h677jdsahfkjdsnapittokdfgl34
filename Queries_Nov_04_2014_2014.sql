
#Nov_02_2014

#select * from t_partner_reserved_batch_stock
/*select * from king_dealitems where id=1599239936;
select * from king_deals where dealid=4661379646;
select * from m_product_deal_link where itemid=1599239936;
select * from t_partner_reserved_batch_stock where itemid=1599239936;*/


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
								
SELECT * FROM t_partner_stock_transfer WHERE transfer_id=48
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=48
SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=4;

SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id
,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-185),1,0) AS mrp_diff
									FROM t_stock_info a
									JOIN m_rack_bin_info b ON a.rack_bin_id = b.id # AND b.is_damaged=0
									JOIN m_product_info p ON p.product_id = a.product_id
									LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
									LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
									LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
									LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id 
									
									LEFT JOIN partner_info pt ON pt.partner_rackbinid=a.rack_bin_id AND pt.partner_rackbinid!=0 
									 
								WHERE a.mrp > 0 AND a.product_id = '4447' AND available_qty > 0  AND pt.id=7
								GROUP BY stock_id,town_diff
								ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp;
								
SELECT * FROM t_stock_info WHERE product_id=4447

SELECT si.stock_id,SUM(pstk.qty) AS reserv_qty,si.mrp,si.location_id,si.rack_bin_id,CONCAT(ri.rack_name,ri.bin_name) AS rbname,ri.is_damaged,IFNULL(si.product_barcode,'') AS pbarcode
										FROM t_partner_reserved_batch_stock pstk
										JOIN t_stock_info si ON si.stock_id=pstk.stock_info_id
										JOIN m_rack_bin_info ri ON ri.id = si.rack_bin_id
										WHERE 
										#pstk.`status`=0 AND 
										pstk.transfer_option=2 AND
										pstk.product_id=4447
										
										
SELECT rstk.id AS rlog_id,rstk.transfer_id,rstk.transfer_option,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
												,tdl.partner_id,tdl.transfer_remarks,rstk.tp_id
											FROM t_partner_reserved_batch_stock rstk
											JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
											JOIN t_partner_stock_transfer tdl ON tdl.transfer_id=rstk.transfer_id
											LEFT JOIN partner_info p ON p.id=tdl.partner_id
											WHERE rstk.status=0 AND rstk.transfer_id=47
											
SELECT * FROM t_partner_reserved_batch_stock WHERE transfer_id=47;




#Nov_03_2014
SELECT st.stock_id,st.product_id,st.location_id,st.rack_bin_id,st.mrp,st.expiry_on,st.available_qty
	FROM  t_stock_info st
	JOIN m_rack_bin_info b ON b.id=st.rack_bin_id
	WHERE b.is_damaged!=1 AND st.product_id=4447 
	ORDER BY st.available_qty DESC LIMIT 1;
	
# Nov_04_2014
DESC SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	JOIN t_imei_no ime ON ime.product_id=pdl.product_id 
	JOIN t_stock_info stk ON stk.product_id=pdl.product_id 
	WHERE ((i.name LIKE '%1117277256 %'  OR ime.imei_no='1117277256 '  OR stk.product_barcode='1117277256 '  OR i.pnh_id = '1117277256 '  OR i.id = '1117277256 ' ) ) 
	GROUP BY i.id LIMIT 10
	
#2 min 40 sec
#1 min 12 sec
# 5.4 sec

REPAIR TABLE king_dealitems;
REPAIR TABLE king_deals;
REPAIR TABLE t_stock_info;
REPAIR TABLE t_imei_no;


SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id AND ime.imei_no='1117277256'
	LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id AND stk.product_barcode='1117277256 '
	WHERE (i.name LIKE '%1117277256%'  OR i.pnh_id = '1117277256 ' OR i.id = '1117277256 '   )
	GROUP BY i.id LIMIT 10
	
#1.37 sec

SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
												FROM king_dealitems i 
												JOIN king_deals d ON d.dealid=i.dealid
												JOIN m_product_deal_link pdl ON pdl.itemid=i.id
												 LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id AND ime.imei_no='1117277256'  LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id AND stk.product_barcode='1117277256' 
												WHERE ((i.name LIKE '%1117277256%'  OR i.pnh_id = '1117277256'  OR i.id = '1117277256' ) AND i.is_pnh=1 ) GROUP BY i.pnh_id LIMIT 10
												
												

SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	 LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id AND ime.imei_no='1132698749'  LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id AND stk.product_barcode='1132698749' 
	WHERE ((i.name LIKE '%1132698749%'  OR i.pnh_id = '1132698749'  OR i.id = '1132698749' ) AND i.is_pnh=0 ) GROUP BY i.pnh_id LIMIT 10;
	
	
	
SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	 LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id AND ime.imei_no='8906007670753' 
	 LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id AND stk.product_barcode='8906007670753' 
	WHERE ((i.name LIKE '%8906007670753%'  OR i.pnh_id = '8906007670753'  OR i.id = '8906007670753' ) AND i.is_pnh=0 OR stk.stock_id IS NOT NULL) GROUP BY i.id LIMIT 10;
	
	#barcode: 8906009451251
	
	DESC t_stock_info
	


SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	 LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id AND ime.imei_no='911367104210571' 
	 LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id AND stk.product_barcode='911367104210571' 
	WHERE ((i.name LIKE '%911367104210571%'  OR i.pnh_id = '911367104210571'  OR i.id = '911367104210571' ) AND i.is_pnh=0 OR stk.stock_id IS NOT NULL) GROUP BY i.id LIMIT 10;
	

	
SELECT * FROM (
	SELECT g.*  
	FROM(
		SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid,pdl.product_id
			FROM king_dealitems i 
			JOIN king_deals d ON d.dealid=i.dealid
			JOIN m_product_deal_link pdl ON pdl.itemid=i.id
			WHERE ((i.name LIKE '%911367104210571%'  OR i.pnh_id = '911367104210571'  OR i.id = '911367104210571' ) AND i.is_pnh=0) GROUP BY i.id
	) AS g
	LEFT JOIN  t_imei_no ime ON ime.product_id=g.product_id AND ime.imei_no='911367104210571' 
	LEFT JOIN t_stock_info stk ON stk.product_id=g.product_id AND stk.product_barcode='911367104210571' OR stk.stock_id IS NOT NULL
) AS h
 LIMIT 10;
 
 SELECT *,COUNT(*) AS c FROM t_imei_no WHERE imei_no='911367104210571';
 
 SELECT COUNT(*) FROM t_stock_info WHERE product_barcode='8906007670753';
 
 
 SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	LEFT JOIN t_imei_no ime ON ime.product_id=pdl.product_id
	WHERE ((i.name LIKE '%911367104210571%'  OR i.pnh_id = '911367104210571'  OR i.id = '911367104210571' OR ime.imei_no='911367104210571'  ) AND i.is_pnh=0 ) GROUP BY i.id LIMIT 10
	
	
 
 SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	
	 LEFT JOIN t_stock_info stk ON stk.product_id=pdl.product_id 
	WHERE ((i.name LIKE '%8906007670753%'  OR i.pnh_id = '8906007670753'  OR i.id = '8906007670753' OR stk.product_barcode='8906007670753'  ) AND i.is_pnh=0 ) GROUP BY i.id LIMIT 10
	
SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	 JOIN t_stock_info stk ON stk.product_id=pdl.product_id 
	WHERE ((i.name LIKE '%8906007670753%'  OR stk.product_barcode='8906007670753' AND stk.stock_id IS NOT NULL  OR i.pnh_id = '8906007670753'  OR i.id = '8906007670753' ) AND i.is_pnh=0 ) GROUP BY i.id LIMIT 10
	

SELECT IFNULL(SUM(available_qty),0) AS s FROM t_stock_info t JOIN m_rack_bin_info rb ON rb.id = t.rack_bin_id WHERE product_id='2616'  AND t.rack_bin_id = 2 AND rb.is_damaged = 1;

#Nov_06_2014

DESC king_transactions;
SELECT billing_type FROM king_transactions;

DESC king_orders;

#==================================
#Nov_06_2014 - Shivaraj - Missing billing type & consolidated payment fields
ALTER TABLE `king_tmp_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `pnh_member_fee`;
ALTER TABLE `king_tmp_transactions` ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`;

ALTER TABLE `king_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `pnh_member_fee`;
ALTER TABLE `king_transactions` ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`;
#==================================


SELECT pgo.product_id, IFNULL(SUM(o.quantity*l.qty),0) AS ttl_part_sold
											FROM m_product_group_deal_link l
											JOIN king_orders o ON l.itemid=o.itemid		
											JOIN products_group_orders pgo ON pgo.order_id = o.id 
											JOIN king_transactions t ON t.transid = o.transid
											WHERE o.status=2 AND t.partner_id > 0
											AND FROM_UNIXTIME(o.time) BETWEEN SUBDATE('2014-10-13 18:25:18',INTERVAL 1 MONTH) AND '2014-10-13 18:25:18'
											AND pgo.product_id='199834'
											GROUP BY pgo.product_id 
#ALTER TABLE `king_tmp_orders` add `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL;

SELECT `king_tmp_transactions`

#===============
SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=98877
	AND o.transid NOT IN (SELECT DISTINCT o.transid AS trans FROM king_orders o WHERE 1 AND o.status = 3 AND o.userid=98877)
											
SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=98877


SELECT * FROM king_orders
SELECT user_id FROM pnh_member_info WHERE pnh_member_id='21111152'

#=================
SELECT * FROM (
(SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=98877)
  UNION 
(SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=98877)) AS g
	 
PNH38456:0,PNH16173:0
PNHZNK36848:3,PNHXUY27516:3,PNHHUT37764:3,PNHLHP76637:3,PNHWZN23234:3

PNHIXY99464:2:98877,PNHBSK23582:2:98877,PNHWZN23234:2:98877,PNH73884:0:98877,PNH38456:0:98877,PNH16173:0:98877
PNHIXY99464:2:98877,PNHBSK23582:2:98877,PNHWZN23234:2:98877,PNH73884:0:98877,PNH38456:0:98877,PNH16173:0:98877


#=========================
SELECT COUNT(g.transid) AS t FROM (
(SELECT o.transid AS transid FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=98877)
  UNION 
(SELECT o.transid AS transid FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=98877)
) AS g;


#============
  
SELECT DISTINCT pnh_member_id,user_id FROM king_orders o 
JOIN pnh_member_info m ON o.userid!=m.user_id
WHERE user_id NOT IN (58600)

SELECT DISTINCT * FROM king_orders WHERE userid=58600 AND STATUS!=3 GROUP BY transid

SELECT COUNT(DISTINCT g.transid) AS t FROM 
( (SELECT o.transid AS transid FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid='58600') 
UNION (SELECT o.transid AS transid FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid='58600') ) AS g



SELECT DISTINCT pnh_member_id,user_id FROM pnh_member_info m
JOIN king_orders o ON o.userid=m.user_id AND o.status!=3
GROUP BY m.user_id
ORDER BY COUNT(o.id) DESC 

SELECT * FROM pnh_member_fee ORDER BY id DESC LIMIT 10;WHERE transid='PNH79474';

SELECT * FROM king_tmp_transactions WHERE transid='PNH79474';


SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid JOIN m_product_deal_link pdl ON pdl.itemid=i.id 
WHERE ((i.name LIKE '%1493648148%' OR i.pnh_id = '1493648148' OR i.id = '1493648148' ) AND i.is_pnh=0)


SELECT * FROM king_orders WHERE transid='PNH87432';

SELECT * FROM pnh_member_info WHERE user_id='58934';


SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	
	WHERE ((i.name LIKE '%1493648148%'  OR i.pnh_id = '1493648148'  OR i.id = '1493648148' ) AND i.is_pnh=0 ) GROUP BY i.id LIMIT 10;
	

#========================================
SELECT i.pnh_id,i.dealid,i.id,i.name,i.is_pnh,d.brandid,d.catid
	FROM king_dealitems i 
	JOIN king_deals d ON d.dealid=i.dealid
	JOIN m_product_deal_link pdl ON pdl.itemid=i.id
	WHERE ((i.name LIKE '%1113412718%'  OR i.pnh_id = '1113412718'  OR i.id = '1113412718' ) AND i.is_pnh=0 ) GROUP BY i.id LIMIT 10;
	
	
SELECT * FROM user_order_transactions;
#==========================================
#Nov_08_2014
ALTER TABLE `user_order_transactions` ADD COLUMN `member_fee` FLOAT NULL AFTER `trans_created_by`;
ALTER TABLE `user_order_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `member_fee`;
ALTER TABLE `user_order_transactions` ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`;
# Nov_10_2014
ALTER TABLE `support_tickets` ADD COLUMN `created_by` INT(20) NULL AFTER `from_app`;
#======================================
SELECT f.franchise_id,f.franchise_name,f.town_id,f.territory_id,tw.town_name,tr.territory_name,f.is_suspended,assigned_rmfid FROM pnh_m_franchise_info f JOIN pnh_towns tw ON tw.id=f.town_id JOIN pnh_m_territory_info tr ON tr.id=f.territory_id AND franchise_type=1 WHERE (f.assigned_rmfid='498' OR f.assigned_rmfid=0);

SELECT t.ticket_id,t.ticket_no,t.user_id,a.name AS `user`,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on,t.from_app
				,u.name AS assignedto,a2.name AS created_by,f.franchise_name
				,rt.id AS related_to_id,rt.name AS related_to_name
				,GROUP_CONCAT(DISTINCT dlnk.dept_id,':',dept.name ORDER BY dept.name) AS dept_dets
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.user_id 
				LEFT OUTER JOIN king_users u ON u.userid=t.assigned_to
				LEFT OUTER JOIN king_admin a2 ON a2.id=t.created_by
				LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id
				
				LEFT OUTER JOIN m_dept_request_types rt ON rt.id=t.related_to
				LEFT OUTER JOIN m_dept_request_type_link dlnk ON dlnk.type_id = t.related_to AND dlnk.is_active='1'
				LEFT OUTER JOIN m_departments dept ON dept.id = dlnk.dept_id
				
				WHERE 1  AND t.franchise_id != 0   
				GROUP BY t.ticket_id
				 ORDER BY t.updated_on DESC  LIMIT 0,30;
				 
#======================================
SELECT * FROM support_tickets t
JOIN king_admin a ON a.id=t.user_id


UPDATE support_tickets t
JOIN king_admin a ON a.id=t.user_id
SET t.created_by=t.user_id
WHERE a.id=t.user_id
LIMIT 10;

SELECT emp.employee_id,emp.name,emp.email,emp.contact_no
			FROM m_departments dt
			JOIN m_employee_dept_link edl ON edl.dept_id = dt.id
			JOIN m_employee_info emp ON emp.employee_id = edl.employee_id
			WHERE dt.id IN (1,10) AND job_title=9 AND emp.email !='' 
			HAVING emp.employee_id IS NOT NULL ;
			
SELECT * FROM support_tickets WHERE ticket_id=
SELECT * FROM m_departments;


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
	WHERE 1 $cond AND di.is_pnh=0
	ALTER TABLE `pnh_towns` ADD COLUMN `created_by` INT(20) DEFAULT '0'  AFTER `created_on`; 
	


/*[11:33:37 AM][357 ms]*/ INSERT INTO `m_config_params`(`name`,`value`)VALUES('LAST_B2CINVOICENO','50000000105'); 

ALTER TABLE `user_order_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `trans_created_by`, ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`; 

ALTER TABLE `king_tmp_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `is_memberprice`, ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`; 

ALTER TABLE `king_transactions` ADD COLUMN `billing_type` TINYINT(1) DEFAULT 0 NULL AFTER `is_memberprice`, ADD COLUMN `is_consolidated_payment` TINYINT(1) DEFAULT 0 NULL AFTER `billing_type`; 
  
/*[12:45:58 PM][61 ms]*/ ALTER TABLE `m_seller_info` ADD COLUMN `city` VARCHAR(255) NULL AFTER `address`; 

/*[1:38:41 PM][0 ms]*/ INSERT INTO `m_config_params`(`name`,`value`) VALUES ( 'MARKET_PLACE_SELLER_PROFIT_VAL','10'); 


/*[12:36:52 PM][334 ms]*/ ALTER TABLE `pnh_payment_info` ADD COLUMN `processed_on` DATETIME NULL AFTER `cleared_on`, ADD COLUMN `processed_by` INT(11) NULL AFTER `processed_on`, ADD COLUMN `status` TINYINT(0) NULL AFTER `processed_by`, ADD COLUMN `reference_txt` VARCHAR(2500) NULL AFTER `status`; 
/*[12:40:57 PM][311 ms]*/ ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `payment_mode` INT(11) DEFAULT 0 NULL AFTER `allow_sms`, ADD COLUMN `payment_cycle` INT(11) DEFAULT 0 NULL AFTER `payment_mode`; 

#drop table t_mps_po_product_link;



SELECT free_frame,has_power,pnh_id AS pid,i.size_chart,i.id AS itemid,i.name,m.name AS menu,m.id AS menu_id,
						i.gender_attr,c.name AS category,d.catid AS category_id,mc.name AS main_category,i.member_price,
						c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,ROUND(i.orgprice,0) AS mrp,i.price,i.store_price,
						i.is_combo,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url,d.description,i.shipsin AS ships_in,d.keywords,
						COUNT(o.id) AS total_orders,
							(IF(0,i.price,i.member_price))/orgprice AS p_discount,
						i.sno AS d_sno ,0 AS rel_ttl
						FROM king_deals d
						JOIN king_dealitems i ON i.dealid=d.dealid
						JOIN king_brands b ON b.id=d.brandid
						JOIN king_categories c ON c.id=d.catid
						
						LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid
						LEFT JOIN m_vendor_product_images v ON v.item_id=i.id
						LEFT OUTER JOIN king_categories mc ON mc.id=c.type
						LEFT JOIN king_orders o ON o.itemid = i.id
						WHERE  is_pnh=1  AND publish = 1  AND pnh_id IN (1615156) 
						GROUP BY i.id
						ORDER BY  NAME ASC ,p_discount ASC,rel_ttl DESC
					LIMIT 0,10;
					
ALTER TABLE pnh_invoice_returns_images ADD COLUMN  `ticket_id` BIGINT (20) NOT NULL AFTER `return_id`;

SELECT IFNULL(CONCAT('http://sndev13.snapittoday.com/resources/returns_images/',pic,'.jpg'),'n/a') AS image_url FROM pnh_invoice_returns_images WHERE ticket_id=100;

#================================
SELECT t.ticket_id,t.ticket_no,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on,t.from_app
				,t.user_id,a.name AS USER,u.name AS assignedto,a2.name AS created_by,f.franchise_name
				,rt.id AS related_to_id,rt.name AS related_to_name
				,GROUP_CONCAT(DISTINCT ' ',dept.name ORDER BY dept.name) AS dept_dets,IF(t.franchise_id=0,0,1) AS source
FROM support_tickets t 

LEFT OUTER JOIN king_admin a ON a.id=t.user_id 
LEFT OUTER JOIN king_admin u ON u.id=t.assigned_to
LEFT OUTER JOIN king_admin a2 ON a2.id=t.created_by
LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id

LEFT OUTER JOIN m_dept_request_types rt ON rt.id=t.related_to
LEFT OUTER JOIN m_dept_request_type_link dlnk ON dlnk.type_id = t.related_to AND dlnk.is_active='1'
LEFT OUTER JOIN m_departments dept ON dept.id = dlnk.dept_id
WHERE 1  AND f.franchise_id= 498
GROUP BY t.ticket_id
ORDER BY t.updated_on DESC, t.created_on DESC;
#================================

ALTER TABLE `pnh_towns` CHANGE `created_on` `created_on` DATETIME DEFAULT NULL;
ALTER TABLE `pnh_towns` ADD COLUMN `created_by` INT(20) DEFAULT '0'  AFTER `created_on`; 

/*[12:36:52 PM][334 ms]*/ ALTER TABLE `pnh_payment_info` ADD COLUMN `processed_on` DATETIME NULL AFTER `cleared_on`, ADD COLUMN `processed_by` INT(11) NULL AFTER `processed_on`, ADD COLUMN `status` TINYINT(0) NULL AFTER `processed_by`, ADD COLUMN `reference_txt` VARCHAR(2500) NULL AFTER `status`; 
/*[12:40:57 PM][311 ms]*/ ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `payment_mode` INT(11) DEFAULT 0 NULL AFTER `allow_sms`, ADD COLUMN `payment_cycle` INT(11) DEFAULT 0 NULL AFTER `payment_mode`; 

SELECT * FROM m_dept_request_types rt ORDER BY rt.name ASC;

SELECT * FROM t_imei_no WHERE order_id IN(2236863886,2438214924);

352157064118609
352157064119045

SELECT * FROM king_orders WHERE id IN(2236863886,2438214924)

SELECT * FROM t_imei_update_log WHERE alloted_order_id = 2236863886 ORDER BY id DESC LIMIT 1;
SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2236863886,2438214924) ORDER BY id DESC;

#=======================
#Nov_14_2014
SELECT * FROM pnh_member_offers WHERE transid_ref='PNHDTR48635';
"sno"	"member_id"	"franchise_id"	"offer_type"	"mem_fee_applicable"	"pnh_member_fee"	"offer_value"	"offer_towards"	"pnh_pid"	"order_id"	"transid_ref"	"insurance_id"	"process_status"	"feedback_status"	"feedback_value"	"delivery_status"	"details_updated"	"referred_by"	"referred_status"	"remarks"	"created_by"	"created_on"	"modified_by"	"modified_on"
"66"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005541"	"2236863886"	"PNHDTR48635"	"39"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"
"67"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005472"	"3469375677"	"PNHDTR48635"	"40"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"

SELECT * FROM pnh_member_insurance WHERE order_id IN(2236863886,2438214924,2995582869,3469375677);
Res:
"sno"	"insurance_id"	"fid"	"mid"	"menu_log_id"	"offer_type"	"proof_id"	"proof_type"	"proof_address"	"opted_insurance"	"offer_status"	"insurance_value"	"insurance_margin"	"order_value"	"itemid"	"order_id"	"first_name"	"last_name"	"mob_no"	"city"	"pincode"	"mem_receipt_no"	"mem_receipt_date"	"mem_receipt_amount"	"status_ship"	"status_deliver"	"created_by"	"created_on"	"modified_by"	"modified_on"	"processed_by"	"processed_on"
"39"	"771396684007"	"316"	"22003363"	"19"	"0"	"SUH2965168"	"3"	"146/2, Gajminal , Bailhongal (Rural), Belgaum,Karanataka-591121
"	"1"	"0"	"59.9"	"1"	"5990"	"4681751198"	"2236863886"	"Shankar"	"Pending"	"9731455645"	"Bailhongala"	"591121"	"2518"	"2014-04-14"	"6400"	"0"	"0"	"42"	"2014-04-05 13:16:47"	"42"	"2014-04-16 16:22:35"	\N	\N
"40"	"841396684007"	"316"	"22003363"	"19"	"0"	"Pending"	"1"	"Pending"	"1"	"0"	"59.9"	"1"	"5990"	"5717134917"	"3469375677"	"Upendra"	"Pending"	"9980568024"	""	""	\N	\N	\N	"0"	"0"	"42"	"2014-04-05 13:16:47"	\N	\N	\N	\N
#==============
SELECT * FROM t_imei_no WHERE order_id IN(2236863886,2438214924);
SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2236863886,2438214924) ORDER BY id DESC;

INSERT INTO `t_imei_update_log` (`imei_no`,`alloted_order_id`) VALUES ('352157064118609', '2236863886'); 
UPDATE `t_imei_update_log` SET `product_id` = '155926',`stock_id` = '163396',`grn_id` = '5786',`alloted_on` = '2014-04-05 16:39:47' , `logged_on` = '2014-04-05 16:23:26' , `logged_by` = '37' WHERE `id` = '22915'; 
#==============
SELECT * FROM t_imei_no WHERE order_id IN(2995582869,3469375677);
SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2995582869,3469375677) ORDER BY id DESC;
INSERT INTO `t_imei_update_log` (`imei_no`, `alloted_order_id`) VALUES ('359717059707055', '2995582869'); 
UPDATE `t_imei_update_log` SET `product_id` = '155786',`stock_id` = '163255',`grn_id` = '5786',`alloted_on` = '2014-11-14 13:30:34' , `logged_on` = '2014-11-14 13:30:40' , `logged_by` = '37' WHERE `id` = '22916'; 
#=======================

DELETE FROM `snapitto_erpsndx_jul_04_2014`.`pnh_member_insurance` WHERE `sno` = '2744'; 
DELETE FROM `snapitto_erpsndx_jul_04_2014`.`pnh_member_offers` WHERE `sno` = '4158'; 
#=====================
SELECT * FROM t_imei_update_log WHERE alloted_order_id = 3469375677 ORDER BY id DESC LIMIT 1;
SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2995582869,3469375677) ORDER BY id DESC;
2995582869

SELECT * FROM pnh_member_offers WHERE order_id=2236863886; #member_id=? and transid_ref=? and pnh_pid=? and order_id=? limit 5

SELECT * FROM pnh_member_offers WHERE transid_ref='PNHDTR48635'
SELECT * FROM pnh_member_insurance WHERE order_id IN(2236863886,3469375677,2438214924);

delivery_status=

insurance: status_ship=1

SELECT o.itemid,o.id AS order_id,o.quantity,o.status FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
                                                WHERE o.transid='PNHDTR48635' AND  di.pnh_id='10005472';
                                                 
SELECT i.order_id,i.invoice_no,t.is_delivered FROM shipment_batch_process_invoice_link t 
JOIN king_invoice i ON i.invoice_no=t.invoice_no AND invoice_status=1
WHERE order_id IN (7957752967)
LIMIT 1

2236863886,3469375677,2438214924

#==========< RESET INSURANCE DETAILS >===================================
#UPDATE king_orders SET insurance_amount=0,has_insurance=0,insurance_id='' WHERE id IN(2236863886,2438214924,2995582869,3469375677);
#DELETE FROM pnh_member_offers WHERE transid_ref='PNHDTR48635';
#DELETE FROM pnh_member_insurance WHERE order_id IN(2236863886,2438214924,2995582869,3469375677);
#==========< RESET INSURANCE DETAILS >===================================


SELECT a.id,a.itemid,b.name AS itemname,CONCAT(b.print_name,'-',b.pnh_id) AS print_name,i_orgprice,login_mobile1,
							i_price,i_coup_discount,i_discount,a.quantity,c.menuid,a.transid,
							f.franchise_id,f.franchise_name,mi.user_id,mi.first_name,mi.mobile,mi.pnh_member_id,e.pnh_member_fee,a.mp_loyalty_points,d.order_notify_to,d.name AS menuname
							,c.brandid,br.name AS brandname
							FROM king_orders a 
							JOIN king_dealitems b ON a.itemid = b.id 
							JOIN king_deals c ON b.dealid = c.dealid 
							JOIN pnh_menu d ON d.id = c.menuid
							JOIN king_brands br ON br.id=c.brandid
							JOIN king_transactions e ON e.transid = a.transid
							JOIN pnh_member_info mi ON mi.user_id = a.userid
							JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
							WHERE a.transid = 'PNHDTR48635';
							
SELECT group_id,m.name AS menu,menu_id 
FROM  pnh_menu_groups a 
JOIN  pnh_menu_group_link b ON a.id = b.group_id  
JOIN  DESC pnh_menu m ON m.id=menu_id 
WHERE group_id= 5;

SELECT group_id,m.name AS menu,menu_id FROM pnh_menu_groups a JOIN  pnh_menu_group_link b ON a.id = b.group_id  JOIN pnh_menu m ON m.id=menu_id WHERE group_id= '5';
SELECT a.id,group_id,m.name AS menu,menu_id,c.id AS catid,c.name AS category_name,COUNT(IFNULL(o.id,0)) AS ttl_orders  
						FROM pnh_menu_groups a 
						JOIN  pnh_menu_group_link b ON a.id = b.group_id  
						JOIN pnh_menu m ON m.id=menu_id 
						JOIN king_deals d ON d.menuid=m.id
						JOIN king_categories c ON c.id=d.catid
						JOIN king_dealitems di ON di.dealid = d.dealid 
						LEFT JOIN king_orders o ON o.itemid = di.id  
						WHERE menu_id= '112' AND a.id='5'
						GROUP BY c.id
						ORDER BY ttl_orders DESC;



# deal price, brand,menu, cat details
SELECT di.id AS itemid,d.dealid,d.menuid,d.brandid,d.catid,di.orgprice AS mrp,di.price AS offer_price,di.member_price,di.name
FROM king_dealitems di
JOIN  king_deals d ON d.dealid=di.dealid WHERE di.id='2677776574'#d.dealid='7388271863'
LIMIT 1000;

# fid:316,10005538,
SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id=316;
SELECT franchise_type FROM pnh_m_franchise_info WHERE franchise_id=316
#get margin det
SELECT d.menuid,m.default_margin  AS margin,m.default_mp_margin,rf_mp_margin,rf_commission,rmf_commission 
FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid 
JOIN pnh_menu m ON m.id=d.menuid WHERE i.is_pnh=1 AND i.pnh_id=10005538;


SELECT free_frame,has_power,pnh_id AS pid,i.size_chart,i.id AS itemid,i.name,m.name AS menu,m.id AS menu_id,
	i.gender_attr,c.name AS category,d.catid AS category_id,mc.name AS main_category,i.member_price,
	c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,ROUND(i.orgprice,0) AS mrp,i.price,i.store_price,
	i.is_combo,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url,d.description,i.shipsin AS ships_in,d.keywords,
	COUNT(o.id) AS total_orders,
		(IF(0,i.price,i.member_price))/orgprice AS p_discount,
	i.sno AS d_sno ,0 AS rel_ttl
	FROM king_deals d
	JOIN king_dealitems i ON i.dealid=d.dealid
	JOIN king_brands b ON b.id=d.brandid
	JOIN king_categories c ON c.id=d.catid
	
	LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid
	LEFT JOIN m_vendor_product_images v ON v.item_id=i.id
	LEFT OUTER JOIN king_categories mc ON mc.id=c.type
	LEFT JOIN king_orders o ON o.itemid = i.id
	WHERE  is_pnh=1  AND publish = 1 
	GROUP BY i.id
	ORDER BY  p_discount ASC,rel_ttl DESC
LIMIT 0,10

/=========================
SELECT free_frame,has_power,pnh_id AS pid,i.size_chart,i.id AS itemid,i.name,m.name AS menu,m.id AS menu_id,
						i.gender_attr,c.name AS category,d.catid AS category_id,mc.name AS main_category,i.member_price,
						c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,ROUND(i.orgprice,0) AS mrp,i.price,i.store_price,
						i.is_combo,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg')) AS image_url,IFNULL(v.main_image,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg')) AS small_image_url,d.description,i.shipsin AS ships_in,d.keywords,
						COUNT(o.id) AS total_orders,
							(IF(0,i.price,i.member_price))/orgprice AS p_discount,
						i.sno AS d_sno ,0 AS rel_ttl
						FROM king_deals d
						JOIN king_dealitems i ON i.dealid=d.dealid
						JOIN king_brands b ON b.id=d.brandid
						JOIN king_categories c ON c.id=d.catid
						
						LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid
						LEFT JOIN m_vendor_product_images v ON v.item_id=i.id
						LEFT OUTER JOIN king_categories mc ON mc.id=c.type
						LEFT JOIN king_orders o ON o.itemid = i.id
						WHERE  is_pnh=1   AND pnh_id IN (10000830,10005653,20023078,20020503,10005903,20020503) 
						GROUP BY i.id
						ORDER BY  p_discount ASC,rel_ttl DESC
					LIMIT 0,10;
					

#===========================
# GET AMAZON ORDERS
SELECT o.transid,tr.partner_reference_no,o.id AS orderid,o.itemid,di.name AS deal_name,o.quantity,o.paid,o.status,o.ship_person,o.ship_phone,o.ship_state,FROM_UNIXTIME(o.actiontime) AS actiontime,o.i_orgprice,o.i_price,p.is_serial_required
	,imei.imei_no
FROM king_transactions tr
JOIN king_orders o ON o.transid=tr.transid
JOIN m_product_deal_link pdl ON pdl.itemid=o.itemid AND pdl.is_active=1
JOIN king_dealitems di ON di.id=o.itemid
JOIN m_product_info p ON p.product_id=pdl.product_id
LEFT JOIN t_imei_no imei ON imei.order_id=o.id
WHERE o.status!=3 AND tr.partner_id=7 
ORDER BY o.actiontime DESC
LIMIT 100;
# and partner_reference_no= and p.is_serial_required=1#AND tr.transid='AMZWSZ13784'

#===============================
#Oct_31_2014 - Shivaraj - Member fee flag in member table
ALTER TABLE `pnh_member_info` ADD COLUMN `member_type` TINYINT(1) DEFAULT 0 NULL COMMENT '0:free,1:paid' AFTER `voucher_bal_validity`; 
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee_collected` TINYINT(1) DEFAULT 0 NULL AFTER `member_type`
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee` FLOAT NULL AFTER `member_fee_collected`, ADD COLUMN `member_fee_invoice` VARCHAR(50) NULL AFTER `member_fee`;
#===============================

SELECT * FROM pnh_users WHERE reference_id=533;
SELECT MD5('11')
7899570251
8050209696
SELECT * FROM 
get_member_ship_type();
RETURN TRUE;




#===========================
#Nov_19_2014 - GET IMEI deatis of AMAZON-FBA ORDERS
SELECT o.transid,tr.partner_reference_no,o.id AS orderid,o.itemid,di.name AS deal_name,o.quantity,o.paid,o.status,o.ship_person,o.ship_phone,o.ship_state,FROM_UNIXTIME(o.actiontime) AS actiontime,o.i_orgprice,o.i_price,p.is_serial_required
	,imei.imei_no
FROM king_transactions tr
JOIN king_orders o ON o.transid=tr.transid
JOIN m_product_deal_link pdl ON pdl.itemid=o.itemid AND pdl.is_active=1
JOIN king_dealitems di ON di.id=o.itemid
JOIN m_product_info p ON p.product_id=pdl.product_id
LEFT JOIN t_imei_no imei ON imei.order_id=o.id AND imei.status=1
WHERE o.status!=3 AND tr.partner_id=7  AND p.is_serial_required=1
ORDER BY o.actiontime DESC;
# and partner_reference_no=#AND tr.transid='AMZWSZ13784'LIMIT 100
#===============================

#===============================
#Nov_20_2014 - Shivaraj - Member type -free or paid, collect member fee flag in member table
ALTER TABLE `pnh_member_info` ADD COLUMN `member_type` TINYINT(1) DEFAULT 0 NULL COMMENT '0:free,1:paid' AFTER `voucher_bal_validity`; 
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee_collected` TINYINT(1) DEFAULT 0 NULL AFTER `member_type`;
ALTER TABLE `pnh_member_info` ADD COLUMN `member_fee` FLOAT NULL AFTER `member_fee_collected`, ADD COLUMN `member_fee_invoice` VARCHAR(50) NULL AFTER `member_fee`;
#===============================
#Nov_20_2014 - Shivaraj
ALTER TABLE `pnh_payment_info` ADD COLUMN `payment_mode` INT(11) DEFAULT 0 NULL AFTER `reference_txt`; 
#===============================

#INSERT TO CART
INSERT INTO `pnh_api_franchise_cart_info` (`franchise_id`, `pid`, `qty`, `member_id`, `added_on`, `updated_on`) 
VALUES ('498', '10043522', '1', '0', '2014-11-20 15:56:58', '2014-11-20 15:57:01');


SELECT * FROM king_dealitems LIMIT 5;
DESC king_dealitems;

SELECT * FROM pnh_api_franchise_cart_info WHERE franchise_id=637;
SELECT pid,qty,attributes FROM pnh_api_franchise_cart_info WHERE franchise_id= AND STATUS=1

#===========================
#Nov_21_2014 - GET ALL details of AMAZON-FBA ORDERS
SELECT o.transid,tr.partner_reference_no,o.id AS orderid,o.itemid,di.name AS deal_name,o.quantity,o.paid,o.status,o.ship_person,o.ship_phone,o.ship_state,FROM_UNIXTIME(o.actiontime) AS actiontime,o.i_orgprice,o.i_price,p.is_serial_required
	,imei.imei_no
FROM king_transactions tr
JOIN king_orders o ON o.transid=tr.transid
JOIN m_product_deal_link pdl ON pdl.itemid=o.itemid AND pdl.is_active=1
JOIN king_dealitems di ON di.id=o.itemid
JOIN m_product_info p ON p.product_id=pdl.product_id
LEFT JOIN t_imei_no imei ON imei.order_id=o.id AND imei.status=1
WHERE o.status!=3 AND tr.partner_id=7 
ORDER BY o.actiontime DESC;
# and partner_reference_no=#AND tr.transid='AMZWSZ13784'LIMIT 100
#===============================

1823926

DESC king_orders;

SELECT * FROM pnh_users WHERE reference_id=498;

DESC pnh_api_franchise_cart_info

SELECT * FROM pnh_api_franchise_cart_info WHERE franchise_id=498 AND STATUS=1;

SELECT * FROM pnh_m_franchise_info WHERE login_mobile1=? OR login_mobile2=

SELECT loyality_pntvalue,i.*,d.publish,d.menuid,p.is_sourceable FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN `m_product_deal_link` l ON l.itemid=i.id JOIN m_product_info p ON p.product_id=l.product_id WHERE i.is_pnh=1 AND i.pnh_id='19997859' AND i.pnh_id!=0
SELECT id FROM king_admin WHERE NAME='SMS' AND username='SMS'

DESC pnh_menu_margin_track;
ALTER TABLE `pnh_menu_margin_track` ADD COLUMN `rf_mp_margin` DOUBLE DEFAULT 0 NULL AFTER `loyality_pntvalue`, ADD COLUMN `rf_commission` DOUBLE DEFAULT 0 NULL AFTER `rf_mp_margin`, ADD COLUMN `rmf_commssion` DOUBLE DEFAULT 0 NULL AFTER `rf_commission`; 

SELECT *,FROM_UNIXTIME(actiontime) FROM king_tmp_transactions ORDER BY id DESC LIMIT 12;

SELECT * FROM pnh_member_insurance LIMIT 3

#update pnh_member_insurance set mem_receipt_amount='6335',mem_receipt_date='2014-11-20' where sno='2919';

SELECT * FROM pnh_member_info WHERE pnh_member_id='22001082'
21111111-user_id:60119, mob:9980004542
22001082-81390--9902505821

SELECT * FROM pnh_m_franchise_info ORDER BY franchise_id DESC LIMIT 10;

SELECT f.*,f.franchise_id,f.sch_discount,f.sch_discount_start,f.sch_discount_end,f.credit_limit,f.security_deposit,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name AS assigned_to,t.territory_name,f.is_prepaid,f.price_type 
FROM pnh_m_franchise_info f 
LEFT OUTER JOIN king_admin u ON u.id=f.assigned_to JOIN pnh_m_territory_info t ON t.id=f.territory_id 
LEFT JOIN pnh_m_class_info c ON c.id=f.class_id WHERE f.franchise_id='1079' ORDER BY f.franchise_name ASC

SELECT f.created_on,f.is_suspended,f.franchise_id,f.is_lc_store,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name AS assigned_to,t.territory_name FROM pnh_m_franchise_info f LEFT OUTER JOIN king_admin u ON u.id=f.assigned_to JOIN pnh_m_territory_info t ON t.id=f.territory_id JOIN pnh_m_class_info c ON c.id=f.class_id
LEFT OUTER JOIN pnh_franchise_owners ow ON ow.franchise_id=f.franchise_id LEFT OUTER JOIN king_admin a ON a.id=ow.admin WHERE town_id=4 GROUP BY f.franchise_id ORDER BY f.franchise_name ASC

SELECT * FROM pnh_m_franchise_info ORDER BY franchise_id DESC LIMIT 10;

#UPDATE `pnh_m_franchise_info` SET `class_id` = '1' WHERE `franchise_id` = '1082';

#UPDATE `pnh_m_franchise_info` SET `class_id` = '1' WHERE `franchise_id` = '1082';

SELECT * FROM pnh_m_franchise_info WHERE class_id IS NULL;ORDER BY franchise_id DESC LIMIT 10;

#====================< Update class id>=============================
#Nov_24_2014
SELECT * FROM pnh_m_franchise_info WHERE class_id IS NULL;
UPDATE pnh_m_franchise_info SET class_id=1 WHERE class_id IS NULL;
#====================< Update class id>=============================
#10700000043
SELECT id,release_qty,extra_qty,stock_info_id,qty 
	FROM t_reserved_batch_stock 
	WHERE #batch_id = '11628' and p_invoice_no = '83650' and status = 1 and 
	order_id = '1258471953' AND product_id = '197317';
#10700000048
SELECT id,release_qty,extra_qty,stock_info_id,qty,STATUS
	FROM t_reserved_batch_stock 
	WHERE #batch_id = '11628' and p_invoice_no = '83650' and status = 1 and 
	order_id = '380700560' AND product_id = '197320';
	
SELECT * FROM t_reserved_batch_stock  WHERE id =157860;
UPDATE t_reserved_batch_stock SET STATUS=1 WHERE id = 157860;

UPDATE king_orders SET STATUS=0 WHERE id IN ('380700560') AND transid='AMZPKC34382';

UPDATE king_invoice SET invoice_status=1 WHERE invoice_no=10700000048 LIMIT 1;

SELECT id,release_qty,extra_qty,stock_info_id,qty,STATUS
	FROM t_reserved_batch_stock 
	WHERE batch_id = '11628' AND p_invoice_no = '83655' #and status = 1 
	AND order_id = '380700560' AND product_id = '197320';
	
 
 SELECT * FROM 
 pnh_member_info m 
 JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id
 WHERE f.franchise_id IN('498','637','533','17') AND 
 m.pnh_member_id='21111111'