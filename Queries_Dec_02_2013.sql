SELECT * FROM m_batch_config

###########################################################################################
ALTER TABLE `m_batch_config` #add column `territory_id` int (11) DEFAULT '0' NULL  after `assigned_uid`, add column `townid` int (11) DEFAULT '0' NULL  after `territory_id`
,CHANGE `batch_grp_name` `batch_grp_name` VARCHAR (150)  NULL  COLLATE utf8_unicode_ci 
, CHANGE `assigned_menuid` `assigned_menuid` VARCHAR (100)  NULL  COLLATE utf8_unicode_ci 
, CHANGE `assigned_uid` `assigned_uid` VARCHAR (100)  NULL  COLLATE utf8_unicode_ci;

ALTER TABLE `snapittoday_db_nov`.`m_batch_config` DROP COLUMN `townid`, DROP COLUMN `territory_id`, DROP COLUMN `assigned_uid`;

###########################################################################################

SELECT  *#d.menuid,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) 

FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid  #and d.menuid in (?)
                                WHERE sd.batch_id=5000
                                ORDER BY tr.init ASC;

SELECT * FROM shipment_batch_process WHERE batch_id='5000'
SELECT * FROM shipment_batch_process_invoice_link WHERE batch_id='5000';

SELECT o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id,di.name
                                ,o.status,pi.p_invoice_no,o.quantity
                                ,f.franchise_id,pi.p_invoice_no
                                FROM king_orders o
                                JOIN king_transactions tr ON tr.transid = o.transid AND o.status IN (0,1) AND tr.batch_enabled = 1
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                                LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                                LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1 
                                JOIN king_dealitems di ON di.id = o.itemid 
                                WHERE i.id IS NULL # and tr.transid in ('PNHESC16249') # and f.franchise_id = ? $cond 
                                ORDER BY tr.init DESC; #,di.name

#### Nov_03_2013 ###
SELECT * FROM shipment_batch_process WHERE batch_id=5000

D:--> added assigned_userid, territory_id,batch_configid FIELDS TO shipment_batch_process 

D:--> picklist-invoice id shoulkd carry 

--> generate picklist FOR un-grouped batches

###########################################################################################
ALTER TABLE `shipment_batch_process` 
	ADD COLUMN `assigned_userid` INT (11) DEFAULT '0' NULL  AFTER `status`, 
	ADD COLUMN `territory_id` INT (11) DEFAULT '0' NULL  AFTER `assigned_userid`, 
	ADD COLUMN `batch_configid` INT (11) DEFAULT '0' NULL  AFTER `territory_id`;

ALTER TABLE `snapittoday_db_nov`.`m_batch_config` DROP COLUMN `assigned_uid`

ALTER TABLE `snapittoday_db_nov`.`m_batch_config` ADD COLUMN `group_assigned_uid` VARCHAR (120)  NULL  AFTER `batch_size`;

ALTER TABLE `snapittoday_db_nov`.`shipment_batch_process_invoice_link` DROP COLUMN `assigned_userid`;

###########################################################################################


SELECT * FROM m_batch_config;

SELECT o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid AND o.status IN (0,1) AND tr.batch_enabled = 1
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1
			#left join shipment_batch_process_invoice_link sd on sd.p_invoice_no=pi.p_invoice_no
                    JOIN king_dealitems di ON di.id = o.itemid 
                    WHERE i.id IS NULL #and tr.transid = ?
                    ORDER BY tr.init,di.name;

SELECT * FROM proforma_invoices
SELECT * FROM shipment_batch_process_invoice_link
SELECT * FROM king_invoice

SELECT DISTINCT o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id,di.name
                                ,o.status,pi.p_invoice_no,o.quantity
                                ,f.franchise_id,pi.p_invoice_no
                                ,sd.batch_id
                                FROM king_orders o
                                JOIN king_transactions tr ON tr.transid = o.transid AND o.status IN (0,1) AND tr.batch_enabled = 1
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                                LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                                LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1
                                LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no 
                                JOIN king_dealitems di ON di.id = o.itemid 
                                WHERE f.franchise_id = '83'  AND i.id IS NULL AND tr.transid IN ('PNHTQE79561') #('PNHEIP95585')
                                ORDER BY tr.init DESC;


#Dec_04_2013

SELECT * FROM shipment_batch_process_invoice_link

SELECT * FROM king_admin

SELECT * FROM ( 
                    SELECT transid,TRIM(BOTH ',' FROM GROUP_CONCAT(p_inv_nos)) AS p_inv_nos,STATUS,COUNT(*) AS t,IF(COUNT(*)>1,'partial',(IF(STATUS,'ready','pending'))) AS trans_status,franchise_id  
                    FROM (
                    SELECT o.transid,IFNULL(GROUP_CONCAT(DISTINCT pi.p_invoice_no),'') AS p_inv_nos,o.status,COUNT(*) AS ttl_o,tr.franchise_id,tr.actiontime
                            FROM king_orders o
                            JOIN king_transactions tr ON tr.transid=o.transid
                            LEFT JOIN king_invoice i ON i.order_id = o.id AND i.invoice_status = 1 
                            LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND o.transid  = pi.transid AND pi.invoice_status = 1 
                            LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no 
                            LEFT JOIN shipment_batch_process sbp ON sbp.batch_id = sd.batch_id
                            WHERE o.status IN (0,1)  AND i.id IS NULL AND tr.franchise_id != 0  AND sbp.assigned_userid = 37  AND ((sd.packed=0 AND sd.p_invoice_no > 0) OR (sd.p_invoice_no IS NULL AND sd.packed IS NULL ))
                            GROUP BY o.transid,o.status
                    ) AS g 
                    GROUP BY g.transid )AS g1 HAVING g1.trans_status = 'ready';

SELECT * FROM (
            SELECT DISTINCT FROM_UNIXTIME(tr.init,'%D %M %Y') AS str_date,FROM_UNIXTIME(tr.init,'%h:%i:%s %p') AS str_time, COUNT(tr.transid) AS total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on AS f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name AS menu_name,bs.name AS brand_name
                    ,sd.batch_id
            FROM king_transactions tr
                    JOIN king_orders o ON o.transid=tr.transid
                    JOIN king_dealitems di ON di.id=o.itemid
                    JOIN king_deals dl ON dl.dealid=di.dealid
                    JOIN pnh_menu m ON m.id = dl.menuid
                    JOIN king_brands bs ON bs.id = o.brandid
            JOIN pnh_m_franchise_info  f ON f.franchise_id=tr.franchise_id #and f.is_suspended = 0
            JOIN pnh_m_territory_info ter ON ter.id = f.territory_id 
            JOIN pnh_towns twn ON twn.id=f.town_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND pi.invoice_status = 1 
                    LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status IN (0,1) AND tr.batch_enabled=1 AND i.id IS NULL  AND tr.transid IN ('PNHEQC73122')
            GROUP BY o.transid) AS g  GROUP BY transid ORDER BY  g.actiontime DESC
#=================================================

SELECT DISTINCT o.itemid,d.menuid,mn.name AS menuname,f.territory_id,
	sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) 
FROM king_transactions tr
                                    JOIN king_orders AS o ON o.transid=tr.transid
                                    JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                    JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                    JOIN king_dealitems dl ON dl.id = o.itemid
                                    JOIN king_deals d ON d.dealid = dl.dealid
				
				JOIN pnh_menu mn ON mn.id=d.menuid
				JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                                    WHERE sd.batch_id='5000' AND f.territory_id='4'
                                    ORDER BY tr.init ASC

#                                   limit 0,4
=>115ROWS

SELECT * FROM pnh_m_territory_info

SELECT * FROM shipment_batch_process WHERE batch_id='5000';

####################################################################
DELETE FROM shipment_batch_process  WHERE batch_id='5040';
####################################################################

SELECT DISTINCT * FROM shipment_batch_process_invoice_link WHERE batch_id='5042' GROUP BY id

UPDATE `shipment_batch_process_invoice_link` SET `batch_id` = 5000 WHERE `batch_id` = '5040';

UPDATE `shipment_batch_process_invoice_link` SET `batch_id` = 5038 WHERE `id` = 372675
#=============================================================
CREATE TABLE `pnh_m_states` (                            
                `state_id` INT(11) NOT NULL AUTO_INCREMENT,            
                `state_name` VARCHAR(255) DEFAULT NULL,                
                `created_on` DATETIME DEFAULT NULL,                    
                `created_by` INT(11) DEFAULT '0',                      
                PRIMARY KEY (`state_id`)                               
              );

#Dec_05_2013
SELECT id,username FROM king_admin WHERE account_blocked=0

SELECT * FROM shipment_batch_process_invoice_link WHERE p_invoice_no='114299';

SELECT COUNT(*) AS ttl FROM proforma_invoices WHERE p_invoice_no='114299'


SELECT DISTINCT d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
									FROM proforma_invoices i 
									JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
									JOIN m_product_deal_link pl ON pl.itemid=o.itemid 
									JOIN m_product_info p ON p.product_id=pl.product_id 
									JOIN king_dealitems d ON d.id=o.itemid 
									JOIN king_deals e ON e.dealid=d.dealid 
									JOIN shipment_batch_process_invoice_link sb ON sb.p_invoice_no = i.p_invoice_no AND sb.invoice_no = 0  
									WHERE i.p_invoice_no='114299' AND i.invoice_status=1 ORDER BY o.sno

SELECT d.pic,d.is_pnh,e.menuid,i.discount,i.discount,p.product_id,p.mrp,i.transid,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
FROM proforma_invoices i 
JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
JOIN products_group_orders pgo ON pgo.order_id=o.id 
JOIN m_product_group_deal_link pl ON pl.itemid=o.itemid 
JOIN m_product_info p ON p.product_id=pgo.product_id 
JOIN king_dealitems d ON d.id=o.itemid JOIN king_deals e ON e.dealid=d.dealid 
JOIN shipment_batch_process_invoice_link sb ON sb.p_invoice_no = i.p_invoice_no AND sb.invoice_no = 0  
WHERE i.p_invoice_no='114299' AND i.invoice_status=1 ORDER BY o.sno 


### Dec_11_2013
#==============================================================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '356631059543977';
#==============================================================================

JOIN king_orders o ON o.id = rbs.order_id
                        JOIN king_dealitems dlt ON dlt.id = o.itemid
			JOIN king_deals dl ON dl.dealid = dlt.dealid
			JOIN pnh_menu AS mnu ON mnu.id = dl.menuid AND mnu.status=1




SET @inv_no='114077';
SELECT DISTINCT o.time,e.menuid,mnu.name AS menuname,d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
									FROM proforma_invoices i 
									JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
									JOIN m_product_deal_link pl ON pl.itemid=o.itemid 
									JOIN m_product_info p ON p.product_id=pl.product_id 
									JOIN king_dealitems d ON d.id=o.itemid 
									JOIN king_deals e ON e.dealid=d.dealid
									LEFT JOIN pnh_menu AS mnu ON mnu.id = e.menuid AND mnu.status=1
									JOIN shipment_batch_process_invoice_link sb ON sb.p_invoice_no = i.p_invoice_no AND sb.invoice_no = 0  
									WHERE i.invoice_status=1 
											AND i.p_invoice_no IN (@inv_no) 
									ORDER BY o.sno;
==> 187 ROWS
==> 152 ROWS



SET @inv_no='114077';
SELECT d.pic,d.is_pnh,e.menuid,i.discount,i.discount,p.product_id,p.mrp,i.transid,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
									FROM proforma_invoices i 
									JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
									JOIN products_group_orders pgo ON pgo.order_id=o.id 
									JOIN m_product_group_deal_link pl ON pl.itemid=o.itemid 
									JOIN m_product_info p ON p.product_id=pgo.product_id 
									JOIN king_dealitems d ON d.id=o.itemid 
									JOIN king_deals e ON e.dealid=d.dealid 
									LEFT JOIN pnh_menu AS mnu ON mnu.id = e.menuid AND mnu.status=1
									JOIN shipment_batch_process_invoice_link sb ON sb.p_invoice_no = i.p_invoice_no AND sb.invoice_no = 0  
									WHERE i.invoice_status=1 AND i.p_invoice_no IN (@inv_no) 
									ORDER BY o.sno;

SELECT * FROM king_orders
DESC king_orders;

#Dec_12_2013
SELECT consider_mrp_chng FROM pnh_menu

#Dec_13_2013
SELECT note FROM king_transaction_notes WHERE transid=? AND note_priority=1 ORDER BY id ASC LIMIT 1;
SELECT transid FROM proforma_invoices WHERE p_invoice_no IN ($p_invno_list);

SELECT note FROM king_transaction_notes tnote
JOIN proforma_invoices `pi` ON pi.transid=tnote.transid
WHERE tnote.note_priority=1 AND pi.p_invoice_no IN ('10004')
ORDER BY tnote.id ASC LIMIT 1;

SELECT * FROM proforma_invoices WHERE invoice_status=1


SELECT c.status,a.product_id,product_barcode,mrp,location_id,rack_bin_id,b.stock_id 
			FROM t_reserved_batch_stock a 
			JOIN t_stock_info b ON a.stock_info_id = b.stock_id 
			JOIN t_imei_no c ON c.product_id = b.product_id 
	#where a.p_invoice_no = ? and a.order_id = ? and imei_no = ?
#==> 7880888 rows / 374ms

SELECT a.status,a.product_id,b.product_barcode,b.mrp,b.location_id AS location_id,
										b.rack_bin_id AS rack_bin_id,
										b.stock_id FROM (
									SELECT a.status,a.product_id,b.product_barcode,IFNULL(b.mrp,c.mrp) AS mrp,IFNULL(b.location_id,c.location_id) AS location_id,
										IFNULL(b.rack_bin_id,c.rack_bin_id) AS rack_bin_id,
										b.stock_id
										FROM t_imei_no a 
										LEFT JOIN t_stock_info b ON a.stock_id = b.stock_id AND a.product_id = b.product_id
										JOIN t_grn_product_link c ON c.grn_id = a.grn_id AND a.product_id = c.product_id 
										WHERE imei_no = '356631059543977' 
									) AS a 
									JOIN t_stock_info b ON a.product_id = b.product_id 
									WHERE a.mrp = b.mrp AND a.location_id = b.location_id AND a.rack_bin_id = b.rack_bin_id

SELECT STATUS,product_id FROM t_imei_no WHERE imei_no = '358956056763247';


SELECT DISTINCT o.time,e.menuid,mnu.name AS menuname,d.pic,d.is_pnh,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
									FROM proforma_invoices i 
									JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
									JOIN m_product_deal_link pl ON pl.itemid=o.itemid 
									JOIN m_product_info p ON p.product_id=pl.product_id 
									JOIN king_dealitems d ON d.id=o.itemid 
									JOIN king_deals e ON e.dealid=d.dealid
                                                                        LEFT JOIN pnh_menu AS mnu ON mnu.id = e.menuid AND mnu.status=1
                                                                        JOIN shipment_batch_process_invoice_link sb ON sb.p_invoice_no = i.p_invoice_no AND sb.invoice_no = 0  
									WHERE i.invoice_status=1 ORDER BY e.menuid DESC # and i.p_invoice_no in ($inv_no)

# Dec_14_2013

SELECT territory_name FROM pnh_m_territory_info WHERE id='3';

SELECT DISTINCT o.itemid,d.menuid,mn.name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid # and d.menuid in ('')
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                WHERE sd.batch_id=5000  AND f.territory_id = 3 
                                ORDER BY tr.init ASC
                                LIMIT 0,1;

SELECT dispatch_id,GROUP_CONCAT(DISTINCT a.id) AS man_id,GROUP_CONCAT(DISTINCT b.invoice_no) AS invs 
                                                                                                    FROM pnh_m_manifesto_sent_log a
                                                                                                    JOIN shipment_batch_process_invoice_link b ON a.manifesto_id = b.inv_manifesto_id AND b.invoice_no != 0 
                                                                                                    JOIN proforma_invoices c ON c.p_invoice_no = b.p_invoice_no AND c.invoice_status = 1  
                                                                                                    JOIN king_transactions d ON d.transid = c.transid 
                                                                                                    WHERE DATE(sent_on) BETWEEN '2013-11-01' AND '2013-11-07' AND dispatch_id != 0  
                                                                                            GROUP BY franchise_id;

#100rows/93ms

SELECT * FROM pnh_m_manifesto_sent_log


#Dec_16_2013

SELECT dispatch_id,GROUP_CONCAT(DISTINCT a.id) AS man_id,GROUP_CONCAT(DISTINCT b.invoice_no) AS invs,f.territory_id
				    FROM pnh_m_manifesto_sent_log a
				    JOIN shipment_batch_process_invoice_link b ON a.manifesto_id = b.inv_manifesto_id AND b.invoice_no != 0 
				    JOIN proforma_invoices c ON c.p_invoice_no = b.p_invoice_no AND c.invoice_status = 1  
				    JOIN king_transactions d ON d.transid = c.transid 
				    JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id
				    WHERE DATE(sent_on) BETWEEN '2013-11-01' AND '2013-11-07' AND dispatch_id != 0 #and f.territory_id='3'
			    GROUP BY d.franchise_id
#100 rows/187ms

SELECT * FROM shipment_batch_process_invoice_link

DESC shipment_batch_process_invoice_link;

SELECT f.territory_id,d.franchise_id,dispatch_id,GROUP_CONCAT(DISTINCT a.id) AS man_id,GROUP_CONCAT(DISTINCT b.invoice_no) AS invs 
	FROM pnh_m_manifesto_sent_log a 
	JOIN shipment_batch_process_invoice_link b ON a.manifesto_id = b.inv_manifesto_id AND b.invoice_no != 0 
	JOIN proforma_invoices c ON c.p_invoice_no = b.p_invoice_no AND c.invoice_status = 1 JOIN king_transactions d ON d.transid = c.transid 
	JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id WHERE DATE(sent_on) BETWEEN '2013-11-01' AND '2013-12-16' AND dispatch_id != 0 AND f.territory_id='3' GROUP BY d.franchise_id;


SET @invs='20141014918,20141014287,20141014389';
SELECT a.transid,a.createdon AS invoiced_on,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,d.init,b.itemid,c.name,IF(c.print_name,c.print_name,c.name) AS print_name,c.pnh_id,GROUP_CONCAT(DISTINCT a.invoice_no) AS invs,
                                                        SUM((i_orgprice-(i_discount+i_coup_discount))*a.invoice_qty) AS amt,
                                                        SUM(a.invoice_qty) AS qty 
                                                FROM king_invoice a 
                                                JOIN king_orders b ON a.order_id = b.id 
                                                JOIN king_dealitems c ON c.id = b.itemid
                                                JOIN king_transactions d ON d.transid = a.transid
                                                WHERE a.invoice_no IN (@invs) 
                                GROUP BY itemid



####################################################################
ALTER TABLE `shipment_batch_process_invoice_link` ADD COLUMN `is_acknowlege_printed` INT (11) DEFAULT '0' NULL  AFTER `delivered_by`;
####################################################################

SELECT * FROM pnh_m_territory_info WHERE id=3

SELECT f.territory_id,dispatch_id,GROUP_CONCAT(DISTINCT a.id) AS man_id,GROUP_CONCAT(DISTINCT b.invoice_no) AS invs,COUNT(DISTINCT b.invoice_no) AS ttl_invs
                                                            FROM pnh_m_manifesto_sent_log a
                                                            JOIN shipment_batch_process_invoice_link b ON a.manifesto_id = b.inv_manifesto_id AND b.invoice_no != 0 AND b.is_acknowlege_printed = 0
                                                            JOIN proforma_invoices c ON c.p_invoice_no = b.p_invoice_no AND c.invoice_status = 1  
                                                            JOIN king_transactions d ON d.transid = c.transid 
                                                            JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id
                                                            WHERE DATE(sent_on) BETWEEN '2013-11-01' AND '2013-12-16' AND dispatch_id != 0  AND f.territory_id=16
                                                    GROUP BY d.franchise_id ORDER BY f.territory_id ASC;

### Dec_17_2013 ###
UPDATE `shipment_batch_process_invoice_link` SET `is_acknowlege_printed`='0' WHERE 

SELECT * FROM shipment_batch_process_invoice_link WHERE is_acknowlege_printed>0


    TABLE: "picklist_log_reservation"
id
printcount
p_inv_no
created_by
createdon
####################################################################
CREATE TABLE `picklist_log_reservation` (  `id` BIGINT NOT NULL AUTO_INCREMENT , `group_no` BIGINT (20) DEFAULT '0', `p_inv_no` INT (100) , `created_by` INT (11) DEFAULT '0', `createdon` DATETIME , `printcount` INT (100) , PRIMARY KEY ( `id`));
####################################################################

picklist_log_reservationpicklist_log_reservation

X INSERT INTO `picklist_log_reservation`(`id`,`group_no`,`p_inv_no`,`created_by`,`createdon`,`printcount`) VALUES ( NULL,'1','114344','1',NULL,NULL);
X TRUNCATE TABLE `snapittoday_db_nov`.`picklist_log_reservation`picklist_log_reservation;


INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114344', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114333', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114324', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114318', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114315', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114313', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114311', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114299', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114281', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114334', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114308', '1', '2013-12-17 07:29:26', 1)
INSERT INTO `picklist_log_reservation` (`group_no`, `p_inv_no`, `created_by`, `createdon`, `printcount`) VALUES (1387288766, '114319', '1', '2013-12-17 07:29:26', 1)
UPDATE picklist_log_reservation SET printcount = `printcount` + 1 WHERE id = 11 LIMIT 1

## Dec_18_2013 ##

SELECT DATE_FORMAT(shipped_on,"%w-%a") AS day_of_week,DATE(shipped_on) AS normaldate,shipped_on,shipped,invoice_no 
FROM shipment_batch_process_invoice_link 
WHERE shipped_by=1 AND day_of_week IS NOT NULL .
ORDER BY shipped_on ASC

#7627rows

DESC shipment_batch_process_invoice_link;

SELECT * FROM shipment_batch_process_invoice_link

SELECT * FROM shipment_batch_process

#73124rows

SELECT week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by FROM (
SELECT DATE_FORMAT(shipped_on,"%w") AS week_day,shipped_on,UNIX_TIMESTAMP(shipped_on) AS shipped_on_time,shipped,invoice_no,shipped_by
FROM shipment_batch_process_invoice_link
WHERE shipped=1
ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL AND shipped_on_time!=0 AND shipped_by>0 AND shipped_on_time BETWEEN '1383284282' AND '1385619678'

# =>5086rows/62ms

# Dec_19_2013

SELECT week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by FROM (
SELECT DATE_FORMAT(shipped_on,"%w") AS week_day,shipped_on,UNIX_TIMESTAMP(shipped_on) AS shipped_on_time,shipped,invoice_no,shipped_by
FROM shipment_batch_process_invoice_link
WHERE shipped=1
ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL AND shipped_on_time!=0 AND shipped_by>0

# =>65078rows

SELECT * FROM shipment_batch_process_invoice_link
SELECT * FROM batch
SELECT * FROM shipment_batch_process

SELECT week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by FROM (
SELECT DATE_FORMAT(shipped_on,"%w") AS week_day,shipped_on,UNIX_TIMESTAMP(shipped_on) AS shipped_on_time,shipped,invoice_no,shipped_by
FROM shipment_batch_process_invoice_link
WHERE shipped=1
ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL AND shipped_on_time!=0 AND shipped_by>0

SELECT * FROM pnh_m_territory_info;

SELECT employee_id,NAME,email,gender,city,contact_no,IF(job_title=4,'TM','BE') AS job_role 
FROM m_employee_info 
WHERE job_title IN (4,5) AND is_suspended=0 ORDER BY job_title ASC;
=>28ROWS

SELECT DISTINCT emp.employee_id,emp.name,emp.email,emp.gender,emp.city,emp.contact_no,IF(emp.job_title=4,'TM','BE') AS job_role,ttl.is_active
FROM m_employee_info emp
JOIN m_town_territory_link ttl ON ttl.employee_id = emp.employee_id AND ttl.is_active=1
JOIN pnh_m_territory_info t ON t.id = ttl.territory_id
WHERE job_title IN (4) AND is_suspended=0 #and t.id='1'
#group by emp.employee_id
ORDER BY job_title ASC;

SELECT * FROM m_town_territory_link

SELECT  * FROM m_employee_info w WHERE w.name LIKE '%Kantaraj Naik%' AND is_suspended=0;

SELECT * FROM proforma_invoices
SELECT * FROM shipment_batch_process_invoice_link


SELECT week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by FROM (
SELECT DATE_FORMAT(shipped_on,"%w") AS week_day,shipped_on,UNIX_TIMESTAMP(shipped_on) AS shipped_on_time,shipped,invoice_no,shipped_by
FROM shipment_batch_process_invoice_link
WHERE shipped=1
ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL AND shipped_on_time!=0 AND shipped_by>0 AND shipped_on_time BETWEEN '1383284282' AND '1385619678';


SELECT week_day,shipped_on_time,shipped,invoice_no,shipped_by FROM (
	SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,UNIX_TIMESTAMP(sd.shipped_on) AS shipped_on_time,sd.shipped,sd.invoice_no,sd.shipped_by
	FROM shipment_batch_process_invoice_link sd
	JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1  
	JOIN king_transactions tr ON tr.transid = pi.transid
	JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
    WHERE shipped=1 AND sd.shipped_by>0 AND f.territory_id ='4'
    ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL AND g.shipped_on_time!=0 AND g.shipped_on_time BETWEEN 1383244200 AND 1387391400 

#5346 rows => #18008


SELECT DISTINCT week_day,shipped,invoice_no,shipped_by FROM ( SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,sd.shipped,sd.invoice_no,sd.shipped_by FROM shipment_batch_process_invoice_link sd JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1  JOIN king_transactions tr ON tr.transid = pi.transid JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id WHERE shipped=1 AND f.territory_id ='3' AND UNIX_TIMESTAMP(sd.shipped_on) !=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 ORDER BY shipped_on DESC ) AS g WHERE g.week_day IS NOT NULL

# Dec_20_2013

SELECT week_day,shipped_on,shipped,invoice_no,shipped_by FROM ( SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,sd.shipped_on,sd.shipped,sd.invoice_no,sd.shipped_by FROM shipment_batch_process_invoice_link sd JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1  JOIN king_transactions tr ON tr.transid = pi.transid JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND f.territory_id ='3' AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 ORDER BY shipped_on DESC ) AS g WHERE g.week_day IS NOT NULL


SELECT DISTINCT week_day,shipped_on,shipped,invoice_no,shipped_by FROM (
SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,sd.shipped_on,sd.shipped,sd.invoice_no,sd.shipped_by
FROM shipment_batch_process_invoice_link sd
JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1
JOIN king_transactions tr ON tr.transid = pi.transid
JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND f.territory_id ='' AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800
ORDER BY shipped_on DESC
) AS g WHERE g.week_day IS NOT NULL;

-- select distinct week_day,shipped_on,shipped,invoice_no_str,shipped_by from (
		    SELECT sd.shipped_on,sd.shipped,GROUP_CONCAT(sd.invoice_no) AS invoice_no_str,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,sd.shipped_by
		    FROM shipment_batch_process_invoice_link sd
		    JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
		    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 AND f.territory_id ='3'
		   
			ORDER BY shipped_on DESC
--                                                 ) as g where g.week_day is not null;


SELECT f.territory_id,dispatch_id,GROUP_CONCAT(DISTINCT a.id) AS man_id,GROUP_CONCAT(DISTINCT b.invoice_no) AS invs,COUNT(DISTINCT b.invoice_no) AS ttl_invs
                                                               FROM pnh_m_manifesto_sent_log a
                                                               JOIN shipment_batch_process_invoice_link b ON a.manifesto_id = b.inv_manifesto_id AND b.invoice_no != 0 #$cond_join
                                                               JOIN proforma_invoices c ON c.p_invoice_no = b.p_invoice_no AND c.invoice_status = 1  
                                                               JOIN king_transactions d ON d.transid = c.transid 
                                                               JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id
                                                               WHERE DATE(sent_on) BETWEEN '2013-11-01 17:27:17' AND '2013-11-27 18:44:22' AND dispatch_id != 0  AND f.territory_id='3'
                                                       GROUP BY d.franchise_id ORDER BY f.territory_id ASC


## idea 1
SELECT f.territory_id,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped,GROUP_CONCAT(sd.invoice_no) AS invoice_no_str,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,sd.shipped_by
		    FROM pnh_m_manifesto_sent_log man
			JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
		    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
		    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 #and f.territory_id ='3'
			GROUP BY f.territory_id
			ORDER BY f.territory_id DESC


-- idea 2
SELECT f.territory_id,t.territory_name,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped,GROUP_CONCAT(sd.invoice_no) AS invoice_no_str,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,emp.employee_id		    
		FROM pnh_m_manifesto_sent_log man
			JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
		    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 			JOIN m_town_territory_link ttl ON ttl.territory_id = f.territory_id AND is_active=1
			JOIN m_employee_info emp ON emp.employee_id = ttl.employee_id


			JOIN pnh_m_territory_info t ON t.id = f.territory_id
                    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 #and f.territory_id ='3'
			GROUP BY f.territory_id
			ORDER BY f.territory_id DESC;
-- Outout: 19rows/312ms

SELECT * FROM m_town_territory_link

# Dec_21_2013

SELECT f.territory_id,t.territory_name,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped,GROUP_CONCAT(DISTINCT sd.invoice_no) AS invoice_no_str
			,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,COUNT(DISTINCT f.franchise_id) AS ttl_franchises
		FROM pnh_m_manifesto_sent_log man
			JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
		    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 			JOIN m_town_territory_link ttl ON ttl.territory_id = f.territory_id AND is_active=1
			JOIN m_employee_info emp ON emp.employee_id = ttl.employee_id


			JOIN pnh_m_territory_info t ON t.id = f.territory_id
                    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 #and f.territory_id ='3'
			GROUP BY f.territory_id
			ORDER BY f.territory_id DESC;


#================================================================================================
ALTER TABLE king_invoice ADD COLUMN `paid_status` TINYINT(11) DEFAULT '0' AFTER ref_dispatch_id;
ALTER TABLE king_dealitems ADD COLUMN `billon_orderprice` TINYINT(1) DEFAULT '0' AFTER nyp_price;
ALTER TABLE king_orders ADD COLUMN `billon_orderprice` TINYINT(1) DEFAULT '0' AFTER note;
ALTER TABLE king_orders ADD COLUMN `is_paid` TINYINT(1) DEFAULT '0' AFTER offer_refid;
ALTER TABLE king_orders ADD COLUMN `partner_order_id` VARCHAR(30) DEFAULT '0' AFTER offer_refid;

CREATE TABLE `king_partner_settelment_filedata` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `partner_id` INT(11) DEFAULT NULL,
  `payment_uploaded_id` BIGINT(20) DEFAULT '0',
  `file_name` VARCHAR(255) DEFAULT NULL,
  `orderid` BIGINT(20) DEFAULT '0',
  `logged_on` BIGINT(20) DEFAULT '0',
  `processed_by` TINYINT(2) DEFAULT '0',
  PRIMARY KEY (`id`)
);

ALTER TABLE king_tmp_orders CHANGE brandid `brandid` BIGINT(20) DEFAULT '0',CHANGE vendorid `vendorid` BIGINT(20) DEFAULT '0';

ALTER TABLE king_tmp_orders ADD COLUMN `partner_order_id` VARCHAR(30) DEFAULT '0' AFTER user_note;
ALTER TABLE king_tmp_orders ADD COLUMN `partner_reference_no` VARCHAR(100) DEFAULT '0' AFTER partner_order_id;

ALTER TABLE king_transactions CHANGE partner_reference_no `partner_reference_no` VARCHAR(100) NOT NULL;
ALTER TABLE king_transactions ADD COLUMN `credit_days` INT(11) DEFAULT '0' AFTER trans_grp_ref_no;
ALTER TABLE king_transactions ADD COLUMN `credit_remarks` VARCHAR(255) DEFAULT NULL AFTER credit_days;

ALTER TABLE m_courier_info ADD COLUMN `ref_partner_id` INT(11) DEFAULT '0' AFTER remarks;


CREATE TABLE `m_partner_settelment_details` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT(20) DEFAULT NULL,
  `order_value` DOUBLE DEFAULT NULL,
  `shipping_charges` DOUBLE DEFAULT NULL,
  `payment_id` VARCHAR(255) DEFAULT NULL,
  `payment_amount` DOUBLE DEFAULT NULL,
  `payment_date` DATE DEFAULT NULL,
  `updated_by` INT(11) DEFAULT NULL,
  `updated_on` BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE m_product_info ADD COLUMN `corr_status` TINYINT(1) DEFAULT '0' AFTER modified_by;
ALTER TABLE m_product_info ADD COLUMN `corr_updated_on` DATETIME DEFAULT NULL AFTER corr_status;

CREATE TABLE `m_product_update_log` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) DEFAULT '0',
  `type` VARCHAR(255) DEFAULT NULL,
  `message` TEXT,
  `logged_by` INT(11) DEFAULT '0',
  `logged_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE m_vendor_brand_link CHANGE applicable_from `applicable_from` BIGINT(20) DEFAULT NULL;
ALTER TABLE m_vendor_brand_link CHANGE applicable_till `applicable_till` BIGINT(20) DEFAULT NULL;


CREATE TABLE `pnh_m_creditlimit_onprepaid` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(12) DEFAULT NULL,
  `book_id` BIGINT(12) DEFAULT NULL,
  `book_value` BIGINT(12) DEFAULT NULL,
  `receipt_id` BIGINT(12) DEFAULT NULL,
  `credit_limit_on_prepaid` DOUBLE DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `valid_till` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `pnh_m_fran_security_cheques` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` INT(11) DEFAULT '0',
  `bank_name` VARCHAR(255) DEFAULT NULL,
  `cheque_no` VARCHAR(30) DEFAULT NULL,
  `cheque_date` DATE DEFAULT NULL,
  `collected_on` DATE DEFAULT NULL,
  `amount` DOUBLE DEFAULT NULL,
  `returned_on` DATE DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `modified_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE pnh_m_franchise_info CHANGE store_open_time `store_open_time` TIME DEFAULT NULL;
ALTER TABLE pnh_m_franchise_info CHANGE store_close_time `store_close_time` TIME DEFAULT NULL;
# X
ALTER TABLE pnh_m_franchise_info ADD COLUMN `purchase_limit` DOUBLE DEFAULT '0' AFTER reason;
ALTER TABLE pnh_m_franchise_info ADD COLUMN `new_credit_limit` DOUBLE DEFAULT '0' AFTER purchase_limit;

ALTER TABLE pnh_m_manifesto_sent_log ADD COLUMN `lrno_update_refid` BIGINT(11) DEFAULT '0' AFTER lrno;


CREATE TABLE `pnh_m_states` (
  `state_id` INT(11) NOT NULL AUTO_INCREMENT,
  `state_name` VARCHAR(255) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT '0',
  PRIMARY KEY (`state_id`)
);

ALTER TABLE pnh_m_territory_info ADD COLUMN `state_id` BIGINT(11) DEFAULT '0' AFTER id;

ALTER TABLE pnh_menu ADD COLUMN `voucher_credit_default_margin` DOUBLE DEFAULT '0' AFTER default_margin;


CREATE TABLE `pnh_menu_margin_track` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `menu_id` BIGINT(20) DEFAULT NULL,
  `default_margin` DOUBLE DEFAULT NULL,
  `balance_discount` DOUBLE DEFAULT NULL,
  `balance_amount` BIGINT(20) DEFAULT NULL,
  `loyality_pntvalue` DOUBLE DEFAULT NULL,
  `created_by` INT(12) DEFAULT NULL,
  `created_on` BIGINT(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE pnh_sms_log_sent ADD COLUMN `no_ofboxes` BIGINT(20) DEFAULT '0' AFTER ticket_id;

CREATE TABLE `pnh_town_courier_priority_link` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `town_id` INT(11) DEFAULT '0',
  `courier_priority_1` INT(5) DEFAULT '0',
  `courier_priority_2` INT(5) DEFAULT '0',
  `courier_priority_3` INT(5) DEFAULT '0',
  `delivery_hours_1` INT(3) DEFAULT '0',
  `delivery_hours_2` INT(3) DEFAULT '0',
  `delivery_hours_3` INT(3) DEFAULT '0',
  `delivery_type_priority1` INT(3) DEFAULT '0',
  `delivery_type_priority2` INT(3) DEFAULT '0',
  `delivery_type_priority3` INT(3) DEFAULT '0',
  `is_active` TINYINT(1) DEFAULT '0',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT '0',
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_billedmrp_change_log` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` BIGINT(11) DEFAULT '0',
  `p_invoice_no` INT(11) DEFAULT '0',
  `packed_mrp` DOUBLE DEFAULT NULL,
  `billed_mrp` DOUBLE DEFAULT NULL,
  `remarks` TEXT,
  `logged_on` DATETIME DEFAULT NULL,
  `logged_by` INT(5) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_imei_update_log` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `imei_no` VARCHAR(255) DEFAULT NULL,
  `product_id` BIGINT(11) DEFAULT '0',
  `stock_id` BIGINT(11) DEFAULT '0',
  `grn_id` BIGINT(11) DEFAULT '0',
  `alloted_order_id` BIGINT(11) DEFAULT '0',
  `alloted_on` DATETIME DEFAULT NULL,
  `invoice_no` BIGINT(11) DEFAULT '0',
  `return_id` BIGINT(11) DEFAULT '0',
  `is_cancelled` TINYINT(1) DEFAULT '0',
  `cancelled_on` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT '0',
  `logged_on` DATETIME DEFAULT NULL,
  `logged_by` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_pnh_creditlimit_track` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(20) DEFAULT NULL,
  `payment_modetype` TINYINT(1) DEFAULT NULL COMMENT '1:postpaid,2:prepaid using vouchers,3:prepaid by holding Acounts',
  `prepaid_credit_id` DOUBLE DEFAULT NULL,
  `reconsolation_rid` BIGINT(11) DEFAULT NULL,
  `order_id` BIGINT(12) DEFAULT NULL,
  `transid` VARCHAR(255) DEFAULT NULL,
  `amount` DOUBLE DEFAULT NULL,
  `prepaid_creditlimit` DOUBLE DEFAULT NULL,
  `purchase_limit` DOUBLE DEFAULT '0',
  `init` BIGINT(20) DEFAULT NULL,
  `actiontime` BIGINT(20) DEFAULT NULL,
  `paid` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_prepaid_credit_receipt_track` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` BIGINT(11) DEFAULT NULL,
  `receipt_amount` DOUBLE DEFAULT '0',
  `prepaid_credit` DOUBLE DEFAULT '0',
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `receipt_realizedon` BIGINT(20) DEFAULT '0',
  PRIMARY KEY (`id`)
);


CREATE TABLE `m_batch_config` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `batch_grp_name` VARCHAR(150) DEFAULT NULL,
  `assigned_menuid` INT(11) DEFAULT '0',
  `batch_size` INT(11) DEFAULT '0',
  `group_assigned_uid` VARCHAR(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE shipment_batch_process ADD COLUMN `assigned_userid` INT(11) DEFAULT '0' AFTER STATUS;
ALTER TABLE shipment_batch_process ADD COLUMN `territory_id` INT(11) DEFAULT '0' AFTER assigned_userid;
ALTER TABLE shipment_batch_process ADD COLUMN `batch_configid` INT(11) DEFAULT '0' AFTER territory_id;

CREATE TABLE `t_exotel_agent_status` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `callsid` VARCHAR(255) DEFAULT NULL,
  `from` VARCHAR(50) DEFAULT NULL,
  `dialwhomno` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(255) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);
#================================================================

SELECT f.territory_id,t.territory_name,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped,GROUP_CONCAT(DISTINCT sd.invoice_no) AS invoice_no_str
			,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,COUNT(DISTINCT f.franchise_id) AS ttl_franchises
		FROM pnh_m_manifesto_sent_log man
			JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
		    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 			JOIN m_town_territory_link ttl ON ttl.territory_id = f.territory_id AND is_active=1
			JOIN m_employee_info emp ON emp.employee_id = ttl.employee_id


			JOIN pnh_m_territory_info t ON t.id = f.territory_id
                    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800 #and f.territory_id ='3'
			GROUP BY f.territory_id
			ORDER BY f.territory_id DESC;

SELECT f.territory_id,t.territory_name,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped,GROUP_CONCAT(DISTINCT sd.invoice_no) AS invoice_no_str,COUNT(tr.franchise_id) AS ttl_franchises
			,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,COUNT(DISTINCT f.franchise_id) AS ttl_franchises
		FROM pnh_m_manifesto_sent_log man
			JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
		    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
		    JOIN king_transactions tr ON tr.transid = pi.transid
		    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 			JOIN m_town_territory_link ttl ON ttl.territory_id = f.territory_id AND is_active=1
			JOIN m_employee_info emp ON emp.employee_id = ttl.employee_id
			JOIN pnh_m_territory_info t ON t.id = f.territory_id
                    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND DATE(man.sent_on) BETWEEN FROM_UNIXTIME('1383244200') AND FROM_UNIXTIME('1387477800') #and f.territory_id ='3'
			GROUP BY f.territory_id
			ORDER BY f.territory_id DESC;

SELECT GROUP_CONCAT(man.sent_invoices) grp_invs FROM pnh_m_manifesto_sent_log man WHERE DATE(man.sent_on) BETWEEN FROM_UNIXTIME('1383244200') AND FROM_UNIXTIME('1387477800')

SELECT  FROM pnh_m_manifesto_sent_log man
JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
WHERE DATE(man.sent_on) BETWEEN FROM_UNIXTIME('1383244200') AND FROM_UNIXTIME('1387477800')


SELECT m_town_territory_link

SELECT f.territory_id,t.territory_name,pi.dispatch_id,GROUP_CONCAT(DISTINCT man.id) AS man_id,sd.shipped_on,sd.shipped
                    ,GROUP_CONCAT(DISTINCT sd.invoice_no) AS invoice_no_str,COUNT(DISTINCT sd.invoice_no) AS ttl_invs,COUNT(DISTINCT f.franchise_id) AS ttl_franchises		    
                                                FROM pnh_m_manifesto_sent_log man
                                                        JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
                                                    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
                                                    JOIN king_transactions tr ON tr.transid = pi.transid
                                                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                                                        JOIN pnh_m_territory_info t ON t.id = f.territory_id
                                                    WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND dispatch_id != 0 AND UNIX_TIMESTAMP(sent_on) BETWEEN 1387564200 AND 1387391400 
                                                GROUP BY f.territory_id
                                                ORDER BY t.territory_name ASC;
SELECT FROM_UNIXTIME('1383244200')

#=======================================================================================================
SELECT UNIX_TIMESTAMP('2013-10-20') AS utime;
SELECT FROM_UNIXTIME(1382207400) AS TIME;
#=======================================================================================================

SELECT GROUP_CONCAT(man.sent_invoices) grp_invs
FROM pnh_m_manifesto_sent_log man 
JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
JOIN king_transactions tr ON tr.transid = pi.transid
JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
JOIN pnh_m_territory_info t ON t.id = f.territory_id
WHERE DATE(man.sent_on) BETWEEN FROM_UNIXTIME('1383244200') AND FROM_UNIXTIME('1387477800') AND f.territory_id='3';

SELECT * FROM pnh_m_manifesto_sent_log

SELECT GROUP_CONCAT(man.sent_invoices) grp_invs
	    FROM pnh_m_manifesto_sent_log man 
	    JOIN shipment_batch_process_invoice_link sd ON sd.inv_manifesto_id = man.manifesto_id
	    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
	    JOIN king_transactions tr ON tr.transid = pi.transid
	    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
	    JOIN pnh_m_territory_info t ON t.id = f.territory_id
	    WHERE DATE(man.sent_on) BETWEEN FROM_UNIXTIME('3') AND FROM_UNIXTIME(1384281000) AND f.territory_id=1384453800



SET @dd = '2013-12-25';
SELECT WEEKDAY(@dd),DATE_ADD(@dd,INTERVAL WEEKDAY(@dd) DAY );

4,3 
3-4 -1DAY 

# =========================================================================================================

SELECT * FROM (
	SELECT a.transid,COUNT(a.id) AS num_order_ids,SUM(a.status) AS orders_status 
		FROM king_orders a 
		JOIN king_transactions tr ON tr.transid = a.transid AND tr.is_pnh=1 
		WHERE a.status IN (0,1) AND tr.batch_enabled=1 
		AND a.transid IN ("'PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485'"
	) 
GROUP BY a.transid) AS ddd 
WHERE ddd.orders_status=0

SELECT * FROM (SELECT a.transid,COUNT(a.id) AS num_order_ids,SUM(a.status) AS orders_status FROM king_orders a JOIN king_transactions tr ON tr.transid = a.transid AND tr.is_pnh=1 WHERE a.status IN (0,1) AND tr.batch_enabled=1 AND a.transid IN ('PNHFFB52222','PNHTCI15423') GROUP BY a.transid) AS ddd WHERE ddd.orders_status=0;

#Dec_27_2013

SELECT * FROM shipment_batch_process_invoice_link;

#Dec_28_2013


# =========================================================================================================
ALTER TABLE `shipment_batch_process_invoice_link` DROP COLUMN `is_acknowlege_printed`;

USE snapittoday_db_jan_2014;

CREATE TABLE `pnh_acknowledgement_print_log` (  `sno` BIGINT NOT NULL AUTO_INCREMENT ,`log_id` VARCHAR (150), `tm_emp_id` VARCHAR (100) NULL, `be_emp_id` VARCHAR (100) , `p_inv_no` VARCHAR (100) NOT NULL 
, `created_on` VARCHAR (50) , `created_by` INT (30) , `status` INT (10) DEFAULT '0', `count` INT (100) DEFAULT '0', PRIMARY KEY ( `sno`));


# =========================================================================================================

SELECT terr.territory_name,a.transid,a.createdon AS invoiced_on,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,bill_phone,d.init,b.itemid,c.name,IF(c.print_name,c.print_name,c.name) AS print_name,c.pnh_id,GROUP_CONCAT(DISTINCT a.invoice_no) AS invs,
						((a.mrp-(a.discount))) AS amt,
						SUM(a.invoice_qty) AS qty 
				FROM king_invoice a 
				JOIN king_orders b ON a.order_id = b.id 
				JOIN king_dealitems c ON c.id = b.itemid
				JOIN king_transactions d ON d.transid = a.transid
				JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id
				JOIN pnh_m_territory_info terr ON terr.id=f.territory_id
				WHERE a.invoice_no IN (20141016108,20141016107,20141015715,20141015838,20141015885,20141015995,20141015996,20141015599,20141015598,20141015721,20141015915,20141015916,20141015917,20141015918,20141015919,20141015886,20141015887,20141015888,20141015889,20141015890,20141015891,20141015892,20141015893,20141015894) 
				GROUP BY f.territory_id
				ORDER BY c.name;
#=>67rows 94ms

SELECT f.territory_id,terr.territory_name,GROUP_CONCAT(DISTINCT a.invoice_no) AS invoice_no_str,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,b.bill_phone
				FROM king_invoice a 
				JOIN king_orders b ON a.order_id = b.id 
				JOIN king_dealitems c ON c.id = b.itemid
				JOIN king_transactions d ON d.transid = a.transid
				JOIN pnh_m_franchise_info f ON f.franchise_id = d.franchise_id
				JOIN pnh_m_territory_info terr ON terr.id=f.territory_id
				WHERE a.invoice_no IN (20141016108,20141016107,20141015715,20141015838,20141015885,20141015995,20141015996,20141015599,20141015598,20141015721,20141015915,20141015916,20141015917,20141015918,20141015919,20141015886,20141015887,20141015888,20141015889,20141015890,20141015891,20141015892,20141015893,20141015894) 
				GROUP BY f.territory_id
				ORDER BY c.name;

SELECT * FROM acknowledgement_print_log;

SELECT *
FROM `acknowledgement_print_log`
WHERE `p_inv_no` = '2147483647' AND `created_by` = 1
LIMIT 1 

# Dec_30_2013
SELECT * FROM (`acknowledgement_print_log`) WHERE `p_inv_no` = '20141016108' AND `created_by` IS NULL LIMIT 1;

SELECT * FROM acknowledgement_print_log WHERE log_id='23876A5';

SELECT *FROM (`acknowledgement_print_log`) WHERE `p_inv_no`='20141016108' LIMIT 50;

SELECT * FROM t_imei_no

SELECT * FROM shipment_batch_process_invoice_link WHERE invoice_no='20141016103'

SELECT * FROM  m_town_territory_link

SELECT * FROM king_admin
SELECT * FROM m_employee_info


SELECT tlink.employee_id,e.name FROM shipment_batch_process_invoice_link sd
JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no
JOIN king_transactions tr ON tr.transid = pi.transid
JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
JOIN m_town_territory_link tlink ON tlink.territory_id = f.territory_id AND tlink.is_active=1
LEFT JOIN m_employee_info e ON e.employee_id = tlink.employee_id 
WHERE sd.invoice_no = '20141016107' AND e.job_title IN (4,5)
GROUP BY tlink.employee_id;

#

SELECT * FROM employee_info

SELECT f.franchise_id,f.franchise_name
																			FROM t_imei_no i
																			JOIN king_orders b ON i.order_id = b.id
																			JOIN king_dealitems c ON c.id = b.itemid
																			JOIN m_product_deal_link d ON d.itemid = c.id
																			JOIN king_transactions e ON e.transid = b.transid
																			JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
																			WHERE b.status IN (1,2) AND b.imei_scheme_id > 0  
																			GROUP BY f.franchise_id
																			ORDER BY franchise_name 


SELECT i.is_imei_activated,COUNT(DISTINCT i.id) AS ttl,SUM(imei_reimbursement_value_perunit) AS amt 
							FROM t_imei_no i 
									JOIN king_orders o ON o.id = i.order_id 
									JOIN king_transactions t ON t.transid=o.transid
									JOIN m_product_deal_link p ON p.itemid=o.itemid
									JOIN m_product_info l ON l.product_id=p.product_id
									JOIN king_invoice inv ON inv.order_id=o.id AND inv.invoice_status = 1 
									JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
									#left join pnh_member_info b on b.pnh_member_id=i.activated_member_id 
									#left join t_invoice_credit_notes tcr on tcr.invoice_no = inv.invoice_no 
									JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
							WHERE o.status IN (1,2) AND o.imei_scheme_id > 0 AND t.franchise_id= 17  #and (date(imei_activated_on) >= date("2013-11-30") and date(imei_activated_on) <= date("2013-12-07")) 
							GROUP BY i.is_imei_activated 
							ORDER BY l.product_name ASC


SELECT * FROM acknowledgement_print_log WHERE p_inv_no IN ('20141016108','20141016107','20141015715','20141015838,20141015885,20141015995,20141015996,20141015599,20141015598')
SELECT * FROM acknowledgement_print_log WHERE p_inv_no IN ('20141015599,20141015598')

SELECT * FROM acknowledgement_print_log WHERE p_inv_no IN ("20141015599","20141015598")

SELECT log_id,tm_emp_id,be_emp_id,GROUP_CONCAT(p_inv_no) AS grp_invs,created_on,created_by,STATUS,COUNT FROM acknowledgement_print_log 
WHERE p_inv_no IN ("20141015599","20141015598")
GROUP BY log_id;

C:\Users\USER\Downloads\StoreKing_Database_2014_01_01_08_00_AM

#Jan_01_2014
CREATE DATABASE snapittoday_db_jan_2014;

SELECT MD5('17c4520f6cfd1ab53d8745e84681eb49'); =>superadmin

SELECT MD5('9027da57d66aa309df4d13q0e6ab0d06')

uuudgs5h1d28-234234arabin445221

SELECT * FROM ( SELECT transid,TRIM(BOTH ',' FROM GROUP_CONCAT(p_inv_nos)) AS p_inv_nos,STATUS,COUNT(*) AS t,IF(COUNT(*)>1,'partial',(IF(STATUS,'ready','pending'))) AS trans_status,franchise_id 
	FROM ( 
		SELECT o.transid,IFNULL(GROUP_CONCAT(DISTINCT pi.p_invoice_no),'') AS p_inv_nos,o.status,COUNT(*) AS ttl_o,tr.franchise_id,tr.actiontime 
			FROM king_orders o JOIN king_transactions tr ON tr.transid=o.transid 
			LEFT JOIN king_invoice i ON i.order_id = o.id AND i.invoice_status = 1 
			LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND o.transid = pi.transid AND pi.invoice_status = 1 LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no 
			LEFT JOIN shipment_batch_process sbp ON sbp.batch_id = sd.batch_id 
			WHERE o.status IN (0,1) AND i.id IS NULL AND tr.franchise_id != 0 #and sbp.assigned_userid = 37
				AND ((sd.packed=0 AND sd.p_invoice_no > 0) OR (sd.p_invoice_no IS NULL AND sd.packed IS NULL )) 
				GROUP BY o.transid,o.status ) AS g GROUP BY g.transid )AS g1 HAVING g1.trans_status = 'ready';

sbp.assigned_userid

SELECT * FROM shipment_batch_process_invoice_link;

#=============================================================================

ALTER TABLE `shipment_batch_process` 
	ADD COLUMN `assigned_userid` INT (11) DEFAULT '0' NULL  AFTER `status`, 
	ADD COLUMN `territory_id` INT (11) DEFAULT '0' NULL  AFTER `assigned_userid`, 
	ADD COLUMN `batch_configid` INT (11) DEFAULT '0' NULL  AFTER `territory_id`;

#=============================================================================

UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '355842059098166';

SELECT * FROM proforma_invoices WHERE p_invoice_no='115786';

SELECT * FROM t_imei_no WHERE STATUS=0 AND product_id='8702';

SELECT * FROM shipment_batch_process_invoice_link;

Electronics 112,118 4 1,2,37,36

Beauty      100     2 1,2,37,36

SELECT * FROM shipment_batch_process;

#======== Jan_09_2014 ======================

SET @global_batch_id=5000;
SELECT DISTINCT o.itemid,d.menuid,mn.name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                WHERE sd.batch_id=@global_batch_id 
                                ORDER BY tr.init ASC;


SELECT * FROM shipment_batch_process;


CREATE TABLE `pnh_m_fran_security_cheques` (             
                               `id` INT(11) NOT NULL AUTO_INCREMENT,                  
                               `franchise_id` INT(11) DEFAULT '0',                    
                               `bank_name` VARCHAR(255) DEFAULT NULL,                 
                               `cheque_no` VARCHAR(30) DEFAULT NULL,                  
                               `cheque_date` DATE DEFAULT NULL,                       
                               `collected_on` DATE DEFAULT NULL,                      
                               `amount` DOUBLE DEFAULT NULL,                          
                               `returned_on` DATE DEFAULT NULL,                       
                               `created_on` DATETIME DEFAULT NULL,                    
                               `modified_on` DATETIME DEFAULT NULL,                   
                               `created_by` INT(11) DEFAULT NULL,                     
                               `modified_by` INT(11) DEFAULT NULL,                    
                               PRIMARY KEY (`id`)                                     
                             );

SET @global_batch_id=5000;
SET @batch_id=5548; #5544-48
UPDATE shipment_batch_process_invoice_link SET batch_id=@global_batch_id WHERE batch_id=@batch_id;
DELETE FROM shipment_batch_process WHERE batch_id = @batch_id;



SET @global_batch_id=5000;
SELECT DISTINCT o.itemid,d.menuid,mn.name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                WHERE sd.batch_id=@global_batch_id 
-- 								group by d.menuid
                                ORDER BY d.menuid,tr.init ASC;


SELECT DISTINCT 
                                o.itemid
--                                 bc.id as menuid,bc.batch_grp_name as menuname
								,sd.invoice_no
								,d.menuid
								,f.territory_id
                                ,sd.id,sd.batch_id,sd.p_invoice_no
                                ,FROM_UNIXTIME(tr.init) AS init 
                                    FROM king_transactions tr
                                    JOIN king_orders AS o ON o.transid=tr.transid
                                    JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                    JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no AND sd.invoice_no=0
                                    JOIN king_dealitems dl ON dl.id = o.itemid
                                    JOIN king_deals d ON d.dealid = dl.dealid 
                                    JOIN pnh_menu mn ON mn.id=d.menuid
                                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
--                                     join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                    WHERE sd.batch_id=5000  AND f.territory_id = 3 
--                                         group by  bc.id
                                    ORDER BY tr.init ASC;


SELECT DISTINCT 
                            o.itemid,COUNT(*) AS ttl_orders
                            ,bc.id AS menuid,bc.batch_grp_name AS menuname,f.territory_id
                            ,sd.id,sd.batch_id,sd.p_invoice_no
                            ,FROM_UNIXTIME(tr.init) AS init 
                                FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no AND sd.invoice_no=0
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
                                JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) 
                                WHERE sd.batch_id=5000  AND f.territory_id = 00 
                                    GROUP BY  bc.id
                                ORDER BY tr.init;

SELECT DISTINCT o.itemid,d.menuid,mn.name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no AND sd.invoice_no=0
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                 JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) AND  bc.id = 1 
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
                                
                                WHERE sd.batch_id=5000 
                                ORDER BY d.menuid,tr.init ASC


SET @global_batch_id=5000,@batch_id=5531; #,5531,5532,5533,5534,5535';
UPDATE shipment_batch_process_invoice_link SET batch_id=@global_batch_id WHERE batch_id IN (@batch_id);
DELETE FROM shipment_batch_process WHERE batch_id IN (@batch_id);

SELECT * FROM shipment_batch_process_invoice_link;

DESC m_batch_config

SELECT 
sb.batch_id,SUM(num_orders) AS ttl_trans,assigned_userid,created_on,sd.id,sd.p_invoice_no,sd.invoice_no,GROUP_CONCAT(sd.p_invoice_no) AS grp_p_invoice_no,GROUP_CONCAT(sd.p_invoice_no) AS grp_invoice_no
,bc.assigned_menuid,bc.batch_grp_name
 FROM shipment_batch_process sb
JOIN shipment_batch_process_invoice_link sd ON sd.batch_id = sb.batch_id AND sd.invoice_no=0
#join proforma_invoices `pi` on pi.id=sd.p_invoice_no and pi.invoice_status=1
#join king_orders o on o.id = pi.order_id
JOIN m_batch_config bc ON bc.id=sb.batch_configid
WHERE created_on BETWEEN '2013-12-13 15:20:50' AND '2014-01-10 15:20:50'
GROUP BY sb.batch_id
ORDER BY created_on DESC

#=>227rows

SELECT sb.batch_id,terr.territory_name,bc.batch_grp_name,sb.num_orders,sb.status,sb.created_on,a.username AS assigned_to
FROM shipment_batch_process sb
JOIN m_batch_config bc ON bc.id=sb.batch_configid
LEFT JOIN pnh_m_territory_info terr ON terr.id=sb.territory_id
JOIN king_admin a ON a.id=sb.assigned_userid
WHERE batch_id>'5000' AND assigned_userid=1
ORDER BY batch_id DESC;




DESC king_transactions
DESC king_orders;
DESC proforma_invoices



SELECT f.franchise_name,COUNT(sd.p_invoice_no) AS num_orders,t.territory_name,tw.town_name
	FROM shipment_batch_process_invoice_link sd
JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no
JOIN king_orders o ON o.id=pi.order_id
JOIN king_transactions tr ON tr.transid=o.transid
JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
JOIN pnh_m_territory_info t ON t.id=f.territory_id
JOIN pnh_towns tw ON tw.id=f.town_id
WHERE batch_id='5577' AND sd.invoice_no = 0
GROUP BY f.franchise_id;


SELECT f.franchise_id,f.franchise_name,COUNT(sd.p_invoice_no) AS num_orders,t.territory_name,tw.town_name,sd.batch_id
                    FROM shipment_batch_process_invoice_link sd
                    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no
                    JOIN king_orders o ON o.id=pi.order_id
                    JOIN king_transactions tr ON tr.transid=o.transid
                    JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
                    JOIN pnh_m_territory_info t ON t.id=f.territory_id
                    JOIN pnh_towns tw ON tw.id=f.town_id
                    WHERE batch_id='5577' AND sd.invoice_no = 0
                    GROUP BY f.franchise_id;

SELECT GROUP_CONCAT(DISTINCT sd.p_invoice_no) AS p_invoice_ids
		FROM shipment_batch_process_invoice_link sd
		JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no
		JOIN king_orders o ON o.id=pi.order_id
		JOIN king_transactions tr ON tr.transid=o.transid
WHERE sd.batch_id='5577' AND tr.franchise_id='254';


SELECT GROUP_CONCAT(DISTINCT sd.p_invoice_no) AS p_invoice_ids FROM shipment_batch_process_invoice_link sd 
JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no 
JOIN king_orders o ON o.id=pi.order_id JOIN king_transactions tr ON tr.transid=o.transid WHERE sd.batch_id='5578'



SELECT menuid,menuname,p_invoice_no,product_id,product,location,SUM(rqty) AS qty FROM ( 
                SELECT dl.menuid,mnu.name AS menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name AS product,CONCAT(CONCAT(rack_name,bin_name),'::',si.mrp) AS location,rbs.qty AS rqty 
                        FROM t_reserved_batch_stock rbs 
                        JOIN t_stock_info si ON rbs.stock_info_id = si.stock_id 
                        JOIN m_product_info PI ON pi.product_id = si.product_id 
                        JOIN m_rack_bin_info rak ON rak.id = si.rack_bin_id 
                        JOIN shipment_batch_process_invoice_link e ON e.p_invoice_no = rbs.p_invoice_no AND invoice_no = 0
                        
                        JOIN king_orders o ON o.id = rbs.order_id
                        JOIN king_dealitems dlt ON dlt.id = o.itemid
			JOIN king_deals dl ON dl.dealid = dlt.dealid
			JOIN pnh_menu AS mnu ON mnu.id = dl.menuid AND mnu.status=1
                        
                        WHERE e.p_invoice_no='115640' 
                GROUP BY rbs.id  ) AS g 
                GROUP BY product_id,location;


SELECT menuid,menuname,p_invoice_no,product_id,product,location,SUM(rqty) AS qty FROM ( 
                SELECT dl.menuid,mnu.name AS menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name AS product,CONCAT(CONCAT(rack_name,bin_name),'::',si.mrp) AS location,rbs.qty AS rqty 
                        FROM t_reserved_batch_stock rbs 
                        JOIN t_stock_info si ON rbs.stock_info_id = si.stock_id 
                        JOIN m_product_info PI ON pi.product_id = si.product_id 
                        JOIN m_rack_bin_info rak ON rak.id = si.rack_bin_id 
                        JOIN shipment_batch_process_invoice_link e ON e.p_invoice_no = rbs.p_invoice_no AND invoice_no = 0
                        
                        JOIN king_orders o ON o.id = rbs.order_id
                        JOIN king_dealitems dlt ON dlt.id = o.itemid
			JOIN king_deals dl ON dl.dealid = dlt.dealid
			JOIN pnh_menu AS mnu ON mnu.id = dl.menuid AND mnu.status=1
                        
                        WHERE  e.batch_id = '5575'
                GROUP BY rbs.id  ) AS g 
                GROUP BY product_id,location;

#### Jan_11_2014 ======================
SELECT * FROM (
            SELECT DISTINCT FROM_UNIXTIME(tr.init,'%D %M %Y') AS str_date,FROM_UNIXTIME(tr.init,'%h:%i:%s %p') AS str_time, COUNT(tr.transid) AS total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on AS f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name AS menu_name,bs.name AS brand_name
                    ,sd.batch_id
            FROM king_transactions tr
                    JOIN king_orders o ON o.transid=tr.transid
                    JOIN king_dealitems di ON di.id=o.itemid
                    JOIN king_deals dl ON dl.dealid=di.dealid
                    JOIN pnh_menu m ON m.id = dl.menuid
                    JOIN king_brands bs ON bs.id = o.brandid
            JOIN pnh_m_franchise_info  f ON f.franchise_id=tr.franchise_id  AND f.is_suspended = 0
            JOIN pnh_m_territory_info ter ON ter.id = f.territory_id 
            JOIN pnh_towns twn ON twn.id=f.town_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND pi.invoice_status = 1 
                    LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status IN (0,1) AND tr.batch_enabled=1 AND i.id IS NULL  AND tr.transid IN ('PNH11244','PNHAXG89881','PNHBQV35115','PNHFMK39418','PNHGJL64274','PNHGQR82517','PNHJGC19279','PNHJSM35174','PNHSCD99697','PNHWTN26375','PNHWTW35421','PNHWUI91457','PNHWUM95923','PNHXJC33559','PNHZQI66535') 
            GROUP BY o.transid) AS g  
WHERE  ( g.batch_id IS NULL AND g.batch_id >= 5000 ) 
GROUP BY transid ORDER BY  g.actiontime DESC ;

SELECT * FROM king_admin
SELECT MD5('basava')

SELECT * FROM t_reserved_batch_stock rbs
JOIN king_orders o ON o.id=rbs.order_id
WHERE batch_id='5575'


SELECT franchise_id,franchise_name,batch_id,menuid,menuname,p_invoice_no,product_id,product,location,SUM(rqty) AS qty FROM ( 
                SELECT tr.franchise_id,f.franchise_name,e.batch_id,dl.menuid,mnu.name AS menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name AS product,CONCAT(CONCAT(rack_name,bin_name),'::',si.mrp) AS location,rbs.qty AS rqty 
                        FROM t_reserved_batch_stock rbs 
                        JOIN t_stock_info si ON rbs.stock_info_id = si.stock_id 
                        JOIN m_product_info PI ON pi.product_id = si.product_id 
                        JOIN m_rack_bin_info rak ON rak.id = si.rack_bin_id 
                        JOIN shipment_batch_process_invoice_link e ON e.p_invoice_no = rbs.p_invoice_no AND invoice_no = 0
                        
                        JOIN king_orders o ON o.id = rbs.order_id
						JOIN king_transactions tr ON tr.transid = o.transid
						JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                        JOIN king_dealitems dlt ON dlt.id = o.itemid

			JOIN king_deals dl ON dl.dealid = dlt.dealid
			JOIN pnh_menu AS mnu ON mnu.id = dl.menuid AND mnu.status=1
                        
                        WHERE  e.batch_id = '5584'
                GROUP BY rbs.id  ) AS g 
                GROUP BY product_id,location;

#=>32 rows

SELECT * FROM shipment_batch_process sb
JOIN shipment_batch_process_invoice_link sd ON sd.batch_id = sb.batch_id
WHERE sb.batch_id='5584'
GROUP BY sd.batch_id;

SELECT f.franchise_id,f.franchise_name,COUNT(sd.p_invoice_no) AS num_orders,t.territory_name,tw.town_name,sd.batch_id
                    FROM shipment_batch_process_invoice_link sd
                    JOIN proforma_invoices `pi` ON pi.p_invoice_no = sd.p_invoice_no
                    JOIN king_orders o ON o.id=pi.order_id
                    JOIN king_transactions tr ON tr.transid=o.transid
                    JOIN pnh_m_franchise_info f ON f.franchise_id=tr.franchise_id
                    JOIN pnh_m_territory_info t ON t.id=f.territory_id
                    JOIN pnh_towns tw ON tw.id=f.town_id
                    WHERE batch_id='5584' AND sd.invoice_no = 0
                    GROUP BY f.franchise_id;

SELECT * FROM shipment_batch_process_invoice_link

SELECT * FROM king_transactions
DESC king_transactions;



            SELECT DISTINCT FROM_UNIXTIME(tr.init,'%D %M %Y') AS str_date,FROM_UNIXTIME(tr.init,'%h:%i:%s %p') AS str_time, COUNT(tr.transid) AS total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on AS f_created_on
                    ,ter.territory_name,twn.town_name
                    ,dl.menuid,m.name AS menu_name,bs.name AS brand_name
                    ,sd.batch_id
            FROM king_transactions tr
                    JOIN king_orders o ON o.transid=tr.transid
                    JOIN king_dealitems di ON di.id=o.itemid
                    JOIN king_deals dl ON dl.dealid=di.dealid
                    JOIN pnh_menu m ON m.id = dl.menuid
                    JOIN king_brands bs ON bs.id = o.brandid
            JOIN pnh_m_franchise_info  f ON f.franchise_id=tr.franchise_id  AND f.is_suspended = 0
            JOIN pnh_m_territory_info ter ON ter.id = f.territory_id 
            JOIN pnh_towns twn ON twn.id=f.town_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND pi.invoice_status = 1 
                    LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no
            WHERE tr.status IN (0,1) AND i.id IS NULL AND tr.batch_enabled=0 
            GROUP BY tr.transid ORDER BY  tr.actiontime DESC;


SELECT o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid 
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id 
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id
                    JOIN king_dealitems di ON di.id = o.itemid
                    WHERE i.id IS NULL AND tr.transid = 'PNHCPX81979' AND tr.status IN (1)
                    ORDER BY tr.init,di.name;

SELECT * FROM king_transactions WHERE transid='PNH97446';


# Jan_13_2014
SELECT * FROM proforma_invoices

SELECT * FROM shipment_batch_process;

SELECT * 
FROM king_orders o
JOIN proforma_invoices PI ON pi.transid=o.transid
WHERE o.transid='PNHKWQ67556';


SELECT UNIX_TIMESTAMP(NOW())

SELECT FROM_UNIXTIME('1389609882');

error: PNH22377

SELECT * FROM king_invoice;


# Jan_14_2014

-- =============================================================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '911208355656643';
-- =============================================================================

SELECT (100*40)/100;

SELECT CEIL(50/2);

#============================================================================================
#========== BATCH RESET =====================================================================
SET @global_batch_id=5000;
SET @batch_id=5714; #5544-48
UPDATE shipment_batch_process_invoice_link SET batch_id=@global_batch_id WHERE batch_id=@batch_id;
DELETE FROM shipment_batch_process WHERE batch_id = @batch_id;
#============================================================================================

SELECT CEIL(66/20) =>4
SELECT 66%20 6
SELECT CEIL(20/2) 10
6<=10
26-26 = 0
batches 3

SELECT * FROM king_deals d
JOIN king_dealitems di ON di.dealid=d.dealid WHERE id='9953912416';

SELECT * FROM king_dealitems;

deal edit:dealid=9953912416
<DIV class="extra_text"> <TABLE>  <tr>      <td>     <ul class="fk-ul-disc">      <li>14.2 Megapixels</li>      <li>Optical Zoom: 5X</li>     </ul>    </td>    <td>     <ul class="fk-ul-disc">            <li>CCD Image Sensor</li>            <li>WITH 

SELECT * FROM pnh_m_voucher;


SELECT DISTINCT o.itemid,bc.id AS menuid,bc.batch_grp_name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) 
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                WHERE sd.batch_id=5000  AND f.territory_id = 11 
                                GROUP BY o.transid
                                ORDER BY menuname,tr.init ASC

# Jan_16_2014 ===========

SELECT * FROM king_admin
m_batch_config

SELECT * FROM king_menu;
SELECT * FROM pnh_menu

-- -- -- -- -- -- //===============================================
ALTER TABLE `king_orders` ADD INDEX `status` (`status`);
ALTER TABLE `m_product_info` ADD INDEX `product_id` (`product_id`);
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

SELECT DISTINCT FROM_UNIXTIME(tr.init,'%D %M %Y') AS str_date,FROM_UNIXTIME(tr.init,'%h:%i:%s %p') AS str_time, COUNT(tr.transid) AS total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on AS f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name AS menu_name,bs.name AS brand_name
                    ,sd.batch_id
                    ,pi.cancelled_on
            FROM king_transactions tr
                    JOIN king_orders o ON o.transid=tr.transid
                    JOIN king_dealitems di ON di.id=o.itemid
                    JOIN king_deals dl ON dl.dealid=di.dealid
                    JOIN pnh_menu m ON m.id = dl.menuid
                    JOIN king_brands bs ON bs.id = o.brandid
            JOIN pnh_m_franchise_info  f ON f.franchise_id=tr.franchise_id  AND f.is_suspended = 0
            JOIN pnh_m_territory_info ter ON ter.id = f.territory_id 
            JOIN pnh_towns twn ON twn.id=f.town_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND pi.invoice_status = 1 
                    LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status IN (0,1) AND tr.batch_enabled=1 AND i.id IS NULL  AND tr.transid IN ('PNH22839','PNH33295','PNH35578','PNH35818','PNH71847','PNH93611','PNHAXG89881','PNHBQV35115','PNHCXE88342'
,'PNHDJX75781','PNHEHA75287','PNHESP26174','PNHFMK39418','PNHGJL64274','PNHGQR82517','PNHHCP63831','PNHIHX16957','PNHIYC47867','PNHJGC19279','PNHJSM35174','PNHNVQ34988','PNHQQK26697','PNHQSG33417','PNHRAL44539'
,'PNHSCD99697','PNHSZZ72836','PNHTMP33937','PNHTRW66732','PNHTWJ53918','PNHUEA37733','PNHURC82659','PNHVSE94433','PNHVVX36284','PNHWTN26375','PNHWTW35421','PNHWUI91457','PNHWUM95923','PNHWZM23398','PNHXJC33559'
,'PNHZEV97667','PNHZQI66535')
            GROUP BY o.transid ASC) AS g  WHERE  AND g.batch_id = 5000 AND  g.batch_id >= 5000 GROUP BY transid ORDER BY  g.actiontime DESC;

# Jan_17_2014

-- =============================================================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '354619056098758';
-- =============================================================================

SELECT * FROM t_imei_no
SELECT * FROM m_batch_config

DESC m_batch_config;

SELECT e.*,b.batch_id FROM proforma_invoices a 
			JOIN shipment_batch_process_invoice_link b ON a.p_invoice_no = b.p_invoice_no 
			JOIN king_transactions c ON c.transid = a.transid  
			JOIN pnh_m_franchise_info d ON d.franchise_id = c.franchise_id  AND d.is_suspended = 0
			JOIN pnh_m_territory_info e ON e.id = d.territory_id 
			WHERE  a.invoice_status = 1 AND batch_id = '5000'
			GROUP BY d.territory_id 
			ORDER BY territory_name;

SELECT * FROM pnh_m_territory_info;

SELECT DISTINCT o.itemid,bc.id AS menuid,bc.batch_grp_name AS menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,FROM_UNIXTIME(tr.init) FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no =pi.p_invoice_no
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) 
                                
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                WHERE sd.batch_id=5000  AND f.franchise_id = 31 
                                GROUP BY o.transid
                                ORDER BY menuname,tr.init ASC;


G1 -
112,118,122 - Mobiles & Tablets,Computers & Peripherals,Cameras & Accessories

SELECT * FROM pnh_member_info WHERE first_name = 'shivraj'

SELECT * FROM king_admin
SELECT * FROM m_batch_config

SELECT product_name FROM m_product_info WHERE product_id='8583';

### Jan_18_2014 ###
SELECT * FROM shipment_batch_process

SELECT * FROM king_transactions WHERE transid='PNH43375';

SELECT * FROM t_stock_info

-- #================================================================

#new fields added
ALTER TABLE `shipment_batch_process_invoice_link` ADD COLUMN `batched_on` BIGINT (20)  NULL  AFTER `delivered_by`, ADD COLUMN `batched_by` INT (11)  NULL  AFTER `batched_on`;
DROP TABLE IF EXISTS `picklist_log_reservation`;

CREATE TABLE `picklist_log_reservation` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `batch_id` BIGINT(20) DEFAULT NULL,
  `created_by` INT(11) DEFAULT '0',
  `createdon` DATETIME DEFAULT NULL,
  `printcount` INT(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB;

-- #================================================================
SELECT * FROM shipment_batch_process_invoice_link

B5595
PNHWZM23398

SELECT * FROM proforma_invoices WHERE transid='PNHWZM23398'

SELECT printcount FROM picklist_log_reservation WHERE p_inv_no='116095';


##### 116 - TV, washing machine


c.id IN(112,118,122)
Mobiles & Tablets
Computers & Peripherals
Cameras & Accessories

110,112,116,118,122,124,125,126

Mobiles & Tablets
TV, Audio, Video & Gaming
Computers & Peripherals
Cameras & Accessories
StoreKing Vouchers
Company Assets
Watches


Beauty
100,101,102,103,104,105,106,107,108,109,110,111,120

Electronics
112,113,115,116,117,118,122,126

Footwear & Clothing
114,119,121

Company Assets
124,125



SELECT t.* FROM proforma_invoices PI 
		JOIN shipment_batch_process_invoice_link sd ON pi.p_invoice_no = sd.p_invoice_no 
		JOIN king_transactions tr ON tr.transid = pi.transid  
		JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
		JOIN pnh_m_territory_info t ON t.id = f.territory_id 
		WHERE pi.invoice_status = 1 AND sd.batch_id = '5000'
		GROUP BY f.territory_id 
		ORDER BY t.territory_name

# Jan_21_2014

-- ALTER TABLE `m_vendor_brand_link` ADD COLUMN `cat_id` bigint(11) DEFAULT 0 NULL AFTER `brand_id`;

-- ALTER TABLE `t_po_info` ADD COLUMN `status_remarks` Varchar(2555) NULL AFTER `modified_on`; 

-- XXXXX alter table `shipment_batch_process_invoice_link` drop column `is_acknowlege_printed`;

-- XXXXX create table `acknowledgement_print_log` (  `sno` bigint NOT NULL AUTO_INCREMENT ,`log_id` varchar (150), `tm_emp_id` varchar (100) NULL, `be_emp_id` varchar (100) , `p_inv_no` varchar (100) NOT NULL 
, `created_on` VARCHAR (50) , `created_by` INT (30) , `status` INT (10) DEFAULT '0', `count` INT (100) DEFAULT '0', PRIMARY KEY ( `sno`));

-- ALTER TABLE `pnh_sch_discount_track` ADD COLUMN `dealid` BIGINT(0) NULL AFTER `catid`; 
-- ALTER TABLE `pnh_sch_discount_brands` ADD COLUMN `dealid` BIGINT(11) DEFAULT 0 NULL AFTER `discount`;  

Jan_21_2014 DROP TABLE `acknowledgement_print_log`





SELECT t.* FROM proforma_invoices PI 
		JOIN shipment_batch_process_invoice_link sd ON pi.p_invoice_no = sd.p_invoice_no 
		JOIN king_transactions tr ON tr.transid = pi.transid  
		JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
		JOIN pnh_m_territory_info t ON t.id = f.territory_id 
		JOIN king_orders o ON o.transid=tr.transid
		LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
		WHERE pi.invoice_status = 1 AND sd.batch_id = '5000' AND tr.batch_enabled=1 AND i.id IS NULL
		GROUP BY f.territory_id 
		ORDER BY t.territory_name


SELECT DISTINCT 
		o.itemid,COUNT(DISTINCT tr.transid) AS ttl_trans,GROUP_CONCAT(DISTINCT tr.transid) AS grp_trans
		,bc.id AS menuid,bc.batch_grp_name AS menuname,GROUP_CONCAT(DISTINCT d.menuid) AS actualmenus,f.territory_id
		,sd.id,sd.batch_id,sd.p_invoice_no
		,FROM_UNIXTIME(tr.init) AS init,bc.batch_size,bc.group_assigned_uid AS bc_group_uids
			FROM king_transactions tr
			JOIN king_orders AS o ON o.transid=tr.transid
			JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
			JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no AND sd.invoice_no=0 
			JOIN king_dealitems dl ON dl.id = o.itemid
			JOIN king_deals d ON d.dealid = dl.dealid 
			JOIN pnh_menu mn ON mn.id=d.menuid
			JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
			JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) 
			WHERE   f.territory_id = '2' AND sd.batch_id = 5000 AND tr.batch_enabled=1
				GROUP BY  bc.id
			ORDER BY tr.init ASC

#PNH55415,PNH24756

SELECT * FROM (
            SELECT DISTINCT FROM_UNIXTIME(tr.init,'%d/%m/%Y') AS str_date,FROM_UNIXTIME(tr.init,'%h:%i:%s %p') AS str_time, COUNT(tr.transid) AS total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on AS f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name AS menu_name,bs.name AS brand_name
                    ,sd.batch_id,sd.batched_on
                    ,pi.cancelled_on
            FROM king_transactions tr
                    JOIN king_orders o ON o.transid=tr.transid
                    JOIN king_dealitems di ON di.id=o.itemid
                    JOIN king_deals dl ON dl.dealid=di.dealid
                    JOIN pnh_menu m ON m.id = dl.menuid
                    JOIN king_brands bs ON bs.id = o.brandid
            JOIN pnh_m_franchise_info  f ON f.franchise_id=tr.franchise_id  AND f.is_suspended = 0
            JOIN pnh_m_territory_info ter ON ter.id = f.territory_id 
            JOIN pnh_towns twn ON twn.id=f.town_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices PI ON pi.order_id = o.id AND pi.invoice_status = 1 
                    LEFT JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status IN (0,1) AND tr.batch_enabled=1 AND i.id IS NULL  AND tr.transid  = 'PNH24756' #tr.transid in ('PNH24756','PNH11768','PNH11769','PNH14153','PNH16531','PNH16758','PNH16829','PNH18499','PNH18613','PNH18694','PNH19988','PNH21379','PNH22377','PNH24756','PNH24973','PNH25135','PNH25654','PNH25842','PNH26544','PNH26765','PNH26782','PNH26895','PNH27179','PNH28758','PNH28935','PNH29347','PNH29776','PNH32855','PNH33145','PNH33191','PNH34632','PNH35578','PNH36978','PNH37124','PNH38468','PNH38666','PNH39728','PNH39841','PNH43375','PNH45122','PNH45598','PNH45771','PNH46113','PNH46474','PNH46595','PNH49892','PNH51259','PNH52451','PNH52959','PNH54347','PNH54546','PNH55285','PNH55415','PNH55963','PNH57714','PNH59216','PNH61185','PNH61811','PNH62431','PNH62792','PNH62894','PNH63543','PNH64597','PNH64835','PNH65361','PNH65422','PNH65711','PNH66479','PNH66571','PNH66924','PNH68492','PNH68863','PNH68996','PNH69272','PNH71162','PNH71171','PNH73246','PNH73648','PNH74896','PNH75579','PNH76542','PNH76553','PNH76695','PNH77194','PNH78763','PNH78771','PNH79767','PNH81262','PNH81424','PNH82245','PNH82796','PNH83136','PNH83532','PNH84494','PNH84838','PNH84978','PNH85514','PNH85534','PNH86684','PNH86879','PNH86943','PNH87251','PNH87352','PNH87866','PNH87895','PNH88261','PNH88784','PNH89294','PNH91142','PNH91414','PNH92727','PNH93167','PNH93343','PNH93391','PNH93714','PNH94121','PNH95855','PNH97158','PNH97261','PNH97446','PNH98427','PNH99546','PNH99823','PNHADS69161','PNHAED74212','PNHAGH88692','PNHAGN36664','PNHAHR92662','PNHANH58567','PNHANP18692','PNHARM33812','PNHAUT82647','PNHAVY15723','PNHAWP81139','PNHAXZ95887','PNHBCQ31955','PNHBDQ46772','PNHBIC14589','PNHBJX88262','PNHBQY95588','PNHBTY26342','PNHBUG92628','PNHBVZ88616','PNHBXL77127','PNHCBX23293','PNHCIK77141','PNHCIQ31331','PNHCMG84238','PNHCNI94123','PNHCUC68876','PNHDAC33555','PNHDAZ29396','PNHDHN67744','PNHDKQ83788','PNHDLH38581','PNHDLU22337','PNHDRB84232','PNHDRN86616','PNHDXQ59712','PNHEBM77371','PNHEDE76659','PNHEEA25773','PNHEKI99239','PNHEKN99876','PNHEKQ79585','PNHEMC36338','PNHEUC28782','PNHEVL56344','PNHEWD63196','PNHEWS63546','PNHEXJ46975','PNHFDQ39861','PNHFEP19358','PNHFGH39552','PNHFJJ89521','PNHFJW85241','PNHFMC31231','PNHFNJ93618','PNHFPD52595','PNHFQW52357','PNHFRK85831','PNHGBA81545','PNHGBW42171','PNHGCN55994','PNHGDB83273','PNHGFX26856','PNHGGV79376','PNHGIA46937','PNHGPD98365','PNHGPK52349','PNHGQW98992','PNHGRK87445','PNHGSP85927','PNHGTD69361','PNHGZG96317','PNHGZK66297','PNHGZX93525','PNHHAM59938','PNHHDV12858','PNHHFN96199','PNHHJT55225','PNHHKV17658','PNHHLJ32837','PNHHLK63918','PNHHPL57445','PNHIBP85449','PNHIEK31187','PNHIKZ84631','PNHINL91791','PNHITM15567','PNHJHY22236','PNHJIG23987','PNHJJW49978','PNHJPK79588','PNHJUB83444','PNHJUR83585','PNHJWH19378','PNHJXM54977','PNHJYI53269','PNHJYR49625','PNHKBX34595','PNHKFE84894','PNHKHA69179','PNHKHF18118','PNHKIU86986','PNHKJX49398','PNHKKW32568','PNHKMH14852','PNHKMP39512','PNHKRI73163','PNHKRJ67714','PNHKSE17358','PNHKSH76263','PNHKVE56634','PNHKVK83266','PNHKWQ67556','PNHKWT97949','PNHKWV36154','PNHKXY29712','PNHKZZ52757','PNHLCH25589','PNHLFZ38473','PNHLGE26468','PNHLGR41262','PNHLHF23981','PNHLJU53973','PNHLPT92586','PNHLUD18722','PNHLVQ28751','PNHLXS47336','PNHMCZ13697','PNHMGI67624','PNHMIQ71617','PNHMIT85823','PNHMQG96137','PNHMTK76282','PNHMUN63624','PNHMXM72843','PNHNBH61653','PNHNEF55642','PNHNES62472','PNHNFQ43361','PNHNHM42857','PNHNJW86618','PNHNLX33758','PNHNMW22688','PNHNPZ32251','PNHNQS25748','PNHNRI89691','PNHNRN27821','PNHNTL29354','PNHPJJ83597','PNHPLJ53135','PNHPLQ52878','PNHPNE57734','PNHPNV48628','PNHPWK16236','PNHPWN91691','PNHQJM28368','PNHQKF47245','PNHQMA88926','PNHQQA12834','PNHQRF86241','PNHQSN62741','PNHQVP81358','PNHQXN83166','PNHRCX12337','PNHRCX79657','PNHRGH56152','PNHRLJ44264','PNHRML93953','PNHRRA11758','PNHRRS58716','PNHRSM64458','PNHRSR33587','PNHRUV64528','PNHRUZ38876','PNHRVS63522','PNHRYS25414','PNHRZV84351','PNHSDG26737','PNHSII24313','PNHSJS19152','PNHSML27435','PNHSNN13966','PNHSQW37875','PNHSSH26593','PNHSYN46875','PNHTID81523','PNHTJC71133','PNHTRE43287','PNHTRL83653','PNHTRN68473','PNHTVC97613','PNHUCU43371','PNHUQC77913','PNHURC82659','PNHVMJ76716','PNHVPZ83614','PNHVQB23439','PNHVRS64166','PNHVXV45964','PNHWAV76894','PNHWJA56139','PNHWJW38128','PNHWWP64836','PNHWZH17882','PNHWZM23398','PNHXEN58219','PNHXFM55127','PNHXGX78951','PNHXMN52299','PNHXMS97273','PNHXQH46883','PNHXQP58382','PNHXUQ31412','PNHYAQ29515','PNHYCC44768','PNHYLV31787','PNHYMX51367','PNHYSG22114','PNHYSG25161','PNHYVR76171','PNHZFL18126','PNHZHW58257','PNHZIP31262','PNHZMD85141','PNHZQE12455','PNHZRT64433','PNHZVC56319')
            GROUP BY o.transid) AS g  WHERE  g.batch_id >= 5000  GROUP BY transid ORDER BY  g.actiontime DESC 

SELECT * FROM shipment_batch_process_invoice_link WHERE batch_id='5714'
5719
#=================================================================================================
#============== BATCH RESET ======================================================================
SET @global_batch_id=5000;
SET @batch_id=5760; #5544-48
UPDATE shipment_batch_process_invoice_link SET batch_id=@global_batch_id WHERE batch_id=@batch_id;
DELETE FROM shipment_batch_process WHERE batch_id = @batch_id;
#=================================================================================================

Beauty
100,101,102,103,104,105,106,107,108,109,110,111,120

Electronics
112,113,115,116,117,118,122,126

Footwear & Clothing
114,119,121

Company Assets
124,125

SELECT printcount FROM picklist_log_reservation WHERE batch_id='5730'

SELECT STATUS,product_id FROM t_imei_no WHERE imei_no = '355681053892715'; #51084_4090_1_1_163256_4271887558_5649623681';

# Jan_22_2014

-- =============================================================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '356519052961611';
-- =============================================================================
SELECT * FROM m_product_info WHERE product_id='132961';
# Stock intake the product with product id
SET @product_id = '132961'; # frequent
SET @id='24573';
SET @imei_no = '364619056098766';
INSERT INTO t_imei_no(id,product_id,imei_no,STATUS,grn_id,stock_id,order_id,created_on,modified_on) VALUES(@id,@product_id,@imei_no,0,'2235',0,0,NOW(),0);

-- =============================================================================
SELECT * FROM t_stock_info WHERE product_id='8702'

SELECT * FROM t_reserved_batch_stock WHERE  product_id='132989'

SELECT * FROM t_imei_no WHERE imei_no='354619056098765' product_id='132989'



-- // Territory 
SELECT t.*,sd.batch_id,i.id FROM proforma_invoices PI 
                                                        JOIN shipment_batch_process_invoice_link sd ON pi.p_invoice_no = sd.p_invoice_no 
                                                        JOIN king_transactions tr ON tr.transid = pi.transid  
                                                        JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
                                                        JOIN pnh_m_territory_info t ON t.id = f.territory_id 
                                                        JOIN king_orders o ON o.transid=tr.transid
                                                        LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                                                        WHERE pi.invoice_status = 1 AND sd.batch_id = '5000' AND tr.batch_enabled=1 #and i.id is null
                                                        GROUP BY f.territory_id 
                                                        ORDER BY t.territory_name

SELECT * FROM shipment_batch_process_invoice_link sd WHERE batch_id='5000';

SELECT DISTINCT 
                            o.itemid,COUNT(DISTINCT tr.transid) AS ttl_trans,GROUP_CONCAT(DISTINCT tr.transid) AS grp_trans
                            ,bc.id AS menuid,bc.batch_grp_name AS menuname,GROUP_CONCAT(DISTINCT d.menuid) AS actualmenus,f.territory_id
                            ,sd.id,sd.batch_id,sd.p_invoice_no
                            ,FROM_UNIXTIME(tr.init) AS init,bc.batch_size,bc.group_assigned_uid AS bc_group_uids
                                FROM king_transactions tr
                                JOIN king_orders AS o ON o.transid=tr.transid
                                JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
                                JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no AND sd.invoice_no=0
                                JOIN king_dealitems dl ON dl.id = o.itemid
                                JOIN king_deals d ON d.dealid = dl.dealid 
                                JOIN pnh_menu mn ON mn.id=d.menuid
                                JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
                                JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid) 
                                WHERE sd.batch_id = '5000' AND tr.batch_enabled=1
                                    GROUP BY  bc.id
                                ORDER BY tr.init ASC

SELECT * FROM 

SELECT STATUS,product_id FROM t_imei_no WHERE imei_no = '1223334566777_7089_1_10_163421_4628351511_7719121613'

SELECT * FROM t_imei_no  WHERE product_id='8583';

SELECT * FROM shipment_batch_process_invoice_link

-- =====================================================================
# INSERT NEW IMEI FOR PRODUCT
INSERT INTO t_imei_no(id,product_id,imei_no,STATUS,grn_id,stock_id,order_id,created_on,modified_on)
VALUES(24565,29226,'354619056098767',0,'2235',0,0,NOW(),0);
-- =====================================================================

SELECT * FROM m_product_info WHERE product_id='29226';

ALTER TABLE `m_vendor_info` ADD COLUMN `payment_type` INT(1) DEFAULT 0 NULL AFTER `require_payment_advance`;

ALTER TABLE `pnh_t_receipt_info` ADD COLUMN `cheq_realized_on` VARCHAR(255) NULL AFTER `activated_on`;

-- 1
SELECT r.*,m.name AS modifiedby,a.name AS admin,act.name AS act_by,d.remarks AS submittedremarks,sub.name AS submittedby,d.submitted_on,can.cancelled_on,can.cancel_reason 
FROM pnh_t_receipt_info r 
LEFT OUTER JOIN `pnh_m_deposited_receipts`can ON can.receipt_id=r.receipt_id 
LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin act ON act.id=r.activated_by 
LEFT OUTER JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id 
LEFT OUTER JOIN king_admin sub ON sub.id=d.submitted_by 
LEFT OUTER JOIN king_admin m ON m.id=r.modified_by WHERE franchise_id='371' GROUP BY r.receipt_id;

-- 2
SELECT dm.created_on,di.id,di.device_sl_no,d.device_name 
FROM pnh_m_device_info di 
JOIN pnh_m_device_type d ON d.id=di.device_type_id 
JOIN pnh_t_device_movement_info dm ON dm.device_id=di.id WHERE di.issued_to='371'

-- 3
SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a 
JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid 
JOIN pnh_menu m ON m.id=a.menuid 
WHERE a.status=1 AND b.franchise_id='371';

-- 4
SELECT f.*,f.franchise_id,f.sch_discount,f.sch_discount_start,f.sch_discount_end,f.credit_limit,f.security_deposit,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name AS assigned_to,t.territory_name,f.is_prepaid 
FROM pnh_m_franchise_info f 
LEFT OUTER JOIN king_admin u ON u.id=f.assigned_to 
JOIN pnh_m_territory_info t ON t.id=f.territory_id 
JOIN pnh_m_class_info c ON c.id=f.class_id 
WHERE f.franchise_id='371' ORDER BY f.franchise_name ASC;

-- 5 new invalid
SELECT i.invoice_no,i.transid,i.mrp,tr.franchise_id,FROM_UNIXTIME(i.createdon)
	FROM king_invoice i
JOIN king_transactions tr ON tr.transid=i.transid
WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id='224'
 ORDER BY i.createdon ASC

#invoice_status


#Jan_24_2014

SELECT * FROM king_invoice
SELECT COUNT(invoice_no),GROUP_CONCAT(mrp) FROM king_invoice GROUP BY invoice_no,transid

-- =============================================================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '356519052961611';
-- =============================================================================
SELECT * FROM m_product_info WHERE product_id='5777';
# Stock intake the product with product id
SET @product_id = '5777'; # frequent
SET @id='24575';
SET @imei_no = '364619056098768';
INSERT INTO t_imei_no(id,product_id,imei_no,STATUS,grn_id,stock_id,order_id,created_on,modified_on) VALUES(@id,@product_id,@imei_no,0,'2235',0,0,NOW(),0);

-- =============================================================================


-- new 1 valid
SELECT i.invoice_no,i.transid,tr.franchise_id,FROM_UNIXTIME(i.createdon),SUM(i.mrp) AS inv_total,COUNT(i.invoice_no) AS num_invs,GROUP_CONCAT(i.mrp) AS grp_mrp,GROUP_CONCAT(DISTINCT ref_dispatch_id) AS grp_dispatch_id
	FROM king_invoice i
JOIN king_transactions tr ON tr.transid=i.transid
WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id='224'
 GROUP BY i.invoice_no,i.transid ORDER BY i.createdon ASC

#============
SELECT * FROM pnh_t_receipt_info WHERE franchise_id = '43';


-- ====================================================================================
# Jan_24_2014
CREATE TABLE `pnh_t_receipt_reconcilation` (  `id` BIGINT (20) NOT NULL AUTO_INCREMENT , `debit_note_id` BIGINT (20) DEFAULT '0', `invoice_no` BIGINT (20) , `dispatch_id` INT (100) , `inv_amount` FLOAT (50) DEFAULT '0', `unreconciled` FLOAT (50) DEFAULT '0', `created_on` INT (50) , `created_by` INT (20) , `modified_on` INT (50) , `modified_by` INT (20) , PRIMARY KEY ( `id`))  
CREATE TABLE `pnh_t_receipt_reconcilation_log` (  `logid` BIGINT (20) NOT NULL AUTO_INCREMENT , `credit_note_id` INT (50) , `receipt_id` INT (50) , `reconcile_id` INT (50) , `reconcile_amount` FLOAT (50) DEFAULT '0', `is_reversed` INT (11) DEFAULT '0', `created_on` INT (100) , `created_by` INT (20) , PRIMARY KEY ( `logid`))  
ALTER TABLE `pnh_t_receipt_info` ADD COLUMN `unreconciled_value` DOUBLE   NULL  AFTER `modified_on`, ADD COLUMN `unreconciled_status` VARCHAR (11)  NULL  AFTER `unreconciled_value`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount;

-- ====================================================================================

SELECT * FROM king_invoice
-- new 
SELECT ref_dispatch_id FROM king_invoice WHERE invoice_no='20141019614' GROUP BY invoice_no

-- new
INSERT INTO pnh_t_receipt_reconcilation

# Jan_25_2014

CREATE TABLE `king_admin_activitylog` (                  
                          `id` INT(11) NOT NULL AUTO_INCREMENT,                  
                          `user_id` INT(11) DEFAULT '0',                         
                          `visited_url` VARCHAR(4000) DEFAULT NULL,              
                          `reference_method` VARCHAR(50) DEFAULT NULL,           
                          `ipaddress` VARCHAR(255) DEFAULT NULL,                 
                          `logged_on` DATETIME DEFAULT NULL,                     
                          PRIMARY KEY (`id`)                                     
                        );

SELECT * FROM pnh_t_receipt_info WHERE franchise_id = '43';

UPDATE pnh_t_receipt_info SET unreconciled_value='' AND unreconciled_status='' WHERE 1=1;

TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;

-- new 1
SELECT SUM(unreconciled_value),COUNT(receipt_id) AS num_receipts FROM pnh_t_receipt_info WHERE franchise_id = '43' AND unreconciled_value != 0 AND STATUS IN (1);

-- new 2
SELECT receipt_id,receipt_amount,unreconciled_value FROM pnh_t_receipt_info WHERE franchise_id = '43' AND STATUS = 1;

# Jan_29_2014
SELECT * FROM  pnh_t_receipt_info WHERE franchise_id = '43' AND unreconciled_status !='p'

-- // RESET RECONCILE TABLE
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount;

jx_manage_reservation_create_batch_form.php

SELECT * FROM pnh_t_receipt_info WHERE franchise_id = '43' AND STATUS = 1 AND unreconciled_value!=0;

#Jan_30_2014

-- // RESET RECONCILE TABLE
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount WHERE 1=1;

-- new 
SELECT rlog.*
		,r.franchise_id,r.receipt_amount,r.receipt_type 
SELECT * 
FROM pnh_t_receipt_reconcilation_log rlog
JOIN pnh_t_receipt_info r ON r.receipt_id = rlog.receipt_id
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE r.franchise_id = '43' AND r.status = 1;

-- new final
SELECT * 
FROM pnh_t_receipt_info r 
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE r.franchise_id = '43';

SELECT * FROM pnh_t_receipt_info WHERE receipt_amount != 0 AND franchise_id = '43';

# and unreconciled_value!=0;
-- new

ALTER TABLE `pnh_t_receipt_info` CHANGE `unreconciled_status` `unreconciled_status` VARCHAR (50) DEFAULT 'pending' NULL COMMENT 'pending,partial,done';

ALTER TABLE `pnh_t_receipt_info` DROP COLUMN `unreconciled_status`
ALTER TABLE `pnh_t_receipt_info` ADD `unreconciled_status` VARCHAR (50) DEFAULT 'pending' NULL COMMENT 'pending,partial,done';


UPDATE `pnh_t_receipt_info` SET `unreconciled_value` = '254', `unreconciled_status` = 'partial' WHERE `receipt_id` = 5382 AND `franchise_id` = '43';

-- new 1 reconcile info
SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed,rcon.id AS reconcile_id,rcon.invoice_no,rcon.inv_amount,rcon.unreconciled,r.unreconciled_value,r.receipt_amount
FROM pnh_t_receipt_info r 
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE r.franchise_id = '43' AND r.receipt_id = '119';

SELECT *,FROM_UNIXTIME(created_on) AS created_date FROM pnh_t_receipt_info WHERE receipt_amount != 0 AND franchise_id = '43' AND receipt_id='119'

-- new receipt info
SELECT r.unreconciled_value,r.receipt_amount,FROM_UNIXTIME(r.created_on)  FROM pnh_t_receipt_info r
 WHERE r.receipt_amount != 0 AND r.franchise_id = '43' AND r.receipt_id='119';

SELECT * FROM king_admin

# Jan_31_2014
-- new get only unreconciled receipts
SELECT * FROM pnh_t_receipt_info WHERE receipt_amount != 0 AND unreconciled_value > 0 AND franchise_id = '43' ORDER BY created_on DESC;


SELECT i.invoice_no,ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2) AS inv_amount,GROUP_CONCAT(DISTINCT i.invoice_no) AS grp_invs
                                                            FROM king_invoice i
                                                            JOIN king_transactions tr ON tr.transid=i.transid
 				LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                                            WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id='43' AND rcon.invoice_no IS NULL
                                                            GROUP BY i.invoice_no,i.transid ORDER BY i.createdon ASC

# => 200555 200696 200879 202019 => 4 70 - 4 = 66

# Feb_01_2014
-- new 
SELECT * FROM (
SELECT i.invoice_no,( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty) AS invoice_val,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),rcon.unreconciled) AS inv_amount,GROUP_CONCAT(DISTINCT i.invoice_no) AS grp_invs
		FROM king_invoice i
		JOIN king_transactions tr ON tr.transid=i.transid
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
		WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id='43' #and rcon.invoice_no is null 
		#and rcon.unreconciled = 0 
		AND rcon.invoice_no = '20141019616' #804'
		GROUP BY i.invoice_no,i.transid ORDER BY i.createdon ASC
) AS g WHERE g.inv_amount > 0;
#=>70 61 63 

SELECT * FROM pnh_t_receipt_reconcilation;


-- // RESET RECONCILE TABLE
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_value = receipt_amount WHERE receipt_amount !=0;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_status = 'pending' WHERE receipt_amount !=0;

t_invoice_credit_notes
pnh_t_credit_info

final price => mrp - discont- creditnoteamount

SELECT * FROM pnh_t_receipt_info WHERE receipt_id=5386

SELECT * FROM pnh_m_deposited_receipts WHERE receipt_id=5386

# Feb_04_2014

-- All new 

# 1. list a reconcile ids of that receipt
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id='5384';
UPDATE pnh_t_receipt_reconcilation_log SET is_reversed = 1 WHERE receipt_id = '5385';

# 2. 
SELECT * FROM pnh_t_receipt_reconcilation WHERE id = '1';
UPDATE pnh_t_receipt_reconcilation SET unreconciled = unreconciled + '.$reconcile_amount.' AND modified_on = NOW() AND modified_by = '.$userid.' WHERE id = '2';


# 3. update receipt table with unreconciled_amount and unreconcile status
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET `unreconciled_value` = `receipt_amount`,`unreconciled_status` = 'pending' WHERE `receipt_id` = '5385';
-- select * from pnh_t_receipt_info where receipt_id='5385';


# => 5384 -> 9=>2168.53

SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id='5383';
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ('12','13','14');
SELECT * FROM pnh_t_receipt_info WHERE receipt_id='5383';

SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id='5382' AND is_reversed = 0;
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ('15','16','17');
SELECT * FROM pnh_t_receipt_info WHERE receipt_id='5382';

--  new to unreconcile the invoice not receipt 
#1. get reconcile_id ( id )
SELECT rlog.receipt_id,rcon.id AS reconcile_id,rcon.invoice_no,rcon.inv_amount,rlog.reconcile_amount FROM pnh_t_receipt_reconcilation rcon 
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
WHERE rlog.is_reversed = 0 AND rcon.invoice_no = '20141019616';
#=> 11 

#2. update reconcile table set unreconciled = unreconciled + $reconcile_amount and modified_by and modified_on  and is_invoice_cancelled = 1 where invoice_no = '' and id = reconcile_id;

#3.  update receipt_info set unreconciled_value = unreconciled_value + $reconcile_amount and unreconciled_status = if(unreconciled_value = receipt_amount,'pending', if(unreconciled_value = 0, 'done', 'partial') ) where receipt_id = '';

#4. update `pnh_t_receipt_reconcilation_log` set `is_invoice_cancelled` = 1 where `reconcile_id` = '11'
-- 22. 

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
-- Unreconcilation on Cancel invoice

#1. reconcile table get reconcile_id ( id )
SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '20141019616';
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE reconcile_id IN ('18');
SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '4939';
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

# ==================================================================================
ALTER TABLE `pnh_t_receipt_reconcilation` ADD COLUMN     `is_invoice_cancelled` INT (20) DEFAULT '0' NULL  AFTER `modified_by`,CHANGE `modified_on` `modified_on` VARCHAR (50)  NULL;
ALTER TABLE `pnh_t_receipt_reconcilation_log` ADD COLUMN `is_invoice_cancelled` INT (20) DEFAULT '0' NULL  AFTER `reconcile_id`;
# ==================================================================================

SELECT * FROM king_invoice WHERE invoice_no = '20141019616';

-- # new
SELECT * FROM (
SELECT i.invoice_no,( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty) AS invoice_val,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),rcon.unreconciled) AS inv_amount,GROUP_CONCAT(DISTINCT i.invoice_no) AS grp_invs
		FROM king_invoice i
		JOIN king_transactions tr ON tr.transid=i.transid
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
		WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id='17' #and rcon.invoice_no is null 
		#and rcon.unreconciled = 0 
		#and i.invoice_no = '20141019616' #804'
		GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC
) AS g WHERE g.inv_amount > 0;

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
-- Unreconcilation on Cancel invoice

#1. reconcile table get reconcile_id ( id )
SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '20141019617';
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE reconcile_id IN ('19');
SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '4938';
#####http://localhost/snapitto/admin/invoice/20141019617
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
SELECT * FROM shipment_batch_process_invoice_link WHERE invoice_no = '20141019616'

# Feb_05_2014


XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

SELECT * FROM (SELECT i.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),rcon.unreconciled) AS inv_amount
                                                        FROM king_invoice i
                                                        JOIN king_transactions tr ON tr.transid=i.transid
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
                                                        WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '43' #and i.invoice_no is null 
                                                        GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;

XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

# Feb_06_2014

SELECT * FROM pnh_franchise_account_summary

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
-- new get unreconciled debit notes details
SELECT * FROM (
SELECT fcs.id AS debit_note_id,fcs.amount,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS inv_amount
FROM pnh_franchise_account_stat fcs
LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.id
WHERE fcs.type=1 AND fcs.franchise_id = '43' 
ORDER BY fcs.created_on DESC) AS g WHERE g.inv_amount > 0;
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

SELECT * FROM pnh_t_receipt_reconcilation WHERE debit_note_id 
-- 
SELECT *,FROM_UNIXTIME(created_on) AS TIME FROM pnh_franchise_account_stat WHERE franchise_id = '43' AND TYPE='1' ORDER BY created_on DESC

-- 
SELECT * FROM pnh_t_receipt_info WHERE franchise_id = '43' AND STATUS IN (0,1); 

#receipt_id='119'


-- // RESET RECONCILE TABLE
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_value = receipt_amount WHERE receipt_amount !=0;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_status = 'pending' WHERE receipt_amount !=0;

-- new 
SELECT a.acc_correc_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,STATUS,a.created_on,
						FROM `pnh_franchise_account_summary` a
						LEFT JOIN pnh_franchise_account_stat fcs ON fcs.id = a.acc_correc_id
						WHERE a.franchise_id='43' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC
-- 
SELECT * FROM pnh_franchise_account_summary WHERE franchise_id = '43'
-- 
SELECT * FROM pnh_franchise_account_stat WHERE franchise_id = '43'

-- new 
SELECT a.acc_correc_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,STATUS,a.created_on,rcon.unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
						FROM `pnh_franchise_account_summary` a
						LEFT JOIN pnh_franchise_account_stat fcs ON fcs.id = a.acc_correc_id
				LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.id
						WHERE a.franchise_id='43' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC;

# =============================================================================================
ALTER TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation` DROP COLUMN `remarks`;
ALTER TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log` ADD COLUMN `remarks` VARCHAR (100)  NULL  AFTER `created_by`;
# =============================================================================================

# Feb_07_2014


-- new 
SELECT a.acc_correc_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,STATUS,a.created_on,rcon.unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
						FROM `pnh_franchise_account_summary` a
						LEFT JOIN pnh_franchise_account_stat fcs ON fcs.id = a.acc_correc_id
				LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
				LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id 
						WHERE a.franchise_id='43' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC;

SELECT * FROM pnh_t_receipt_reconcilation_log;
SELECT * FROM pnh_franchise_account_stat;

-- new get unreconciled receipts & credit notes
SELECT * FROM pnh_t_receipt_info 
WHERE receipt_amount != 0 AND unreconciled_value > 0 AND franchise_id = '43' AND STATUS IN (0,1) ORDER BY created_on DESC

SELECT * FROM (
SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,rcon.unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
FROM pnh_franchise_account_stat fcs
LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE fcs.type = '0' AND fcs.franchise_id = '43' ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0;


SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5387';
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id = '5387'
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ('1','2','3','4','5','6','7');

SELECT * FROM pnh_t_receipt_reconcilation_log;
SELECT * FROM pnh_t_receipt_reconcilation

#Feb_08_2014

 #==========================================================
-- // RESET RECONCILE TABLE
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_stat SET unreconciled_value = amount,unreconciled_status = 'pending' WHERE amount !=0;
#==========================================================
SELECT * FROM ( SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,rcon.unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
                                                                FROM pnh_franchise_account_stat fcs
                                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
                                                                LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                                                                WHERE fcs.type = '0' AND fcs.franchise_id = '43' ORDER BY fcs.created_on DESC ) AS g WHERE g.unreconciled_amount > 0 ;



SELECT SUM(receipt_amount)  AS ttl_receipts_val FROM pnh_t_receipt_info 
WHERE receipt_amount != 0 AND 
unreconciled_value > 0 AND franchise_id = '43' AND STATUS IN (0,1) ORDER BY created_on DESC



--  new to get total unreconciled credit value
SELECT SUM(amount) ttl_cr_amount,SUM(unreconciled_amount) AS ttl_un_cr_amount FROM ( 
SELECT fcs.amount,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
                                                                FROM pnh_franchise_account_stat fcs
                                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
                                                                LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                                                                WHERE fcs.type = '0' AND fcs.franchise_id = '43' ORDER BY fcs.created_on DESC ) AS g WHERE g.unreconciled_amount > 0 ;

#=> 1289066.8400000003 1288910.6400000001
#=> 147236.34 147236.34

SELECT * FROM ( 
SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,rcon.unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17' 
ORDER BY fcs.created_on DESC 
) AS g WHERE g.unreconciled_amount > 0 ;

SELECT * FROM pnh_t_receipt_reconcilation_log WHERE credit_note_id IN ('25513')
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ('1','2'); #reconcile_id
SELECT * FROM pnh_franchise_account_stat WHERE id = '25513';

# =============================================================================
ALTER TABLE `pnh_franchise_account_stat` ADD COLUMN `unreconciled_value` DOUBLE   NULL  AFTER `is_correction`, ADD COLUMN `unreconciled_status` VARCHAR (11) DEFAULT 'pending' NULL  AFTER `unreconciled_value`;
# =============================================================================


SELECT * FROM ( 
SELECT * #fcs.id as credit_note_id,fcs.type,fcs.amount,fcs.desc,from_unixtime(fcs.created_on) as created_on,rcon.unreconciled,if(rcon.unreconciled is null, round( fcs.amount, 2),rcon.unreconciled) as unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '43' #and fcs.id = '25513'
ORDER BY fcs.created_on DESC 
) AS g #where g.unreconciled_amount > 0 ;

# if unreconcile val = 0  unreconcile credit amount 0 (done)
# if unreconcile val = cr.amount pending take amount
# if unreconcile val < cr.amount partial take unrecocncile value

-- new fcs.amount
SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,IF(rlog.reconcile_amount IS NULL,fcs.amount,IF(rlog.reconcile_amount = fcs.amount,fcs.amount ,rlog.reconcile_amount)) AS unreconciled_amount
#rlog.reconcile_amount,fcs.amount,if(rlog.reconcile_amount is null, fcs.amount , if(rlog.reconcile_amount = fcs.amount ,fcs.amount ,rlog.reconcile_amount ) ) as unreconciled_amount

#if ( rlog.reconcile_amount = 0, 0, fcs.amount ) as unreconcile_amount , fcs.*,rlog.*
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		#left join pnh_t_receipt_reconcilation rcon on rcon.id = rlog.reconcile_id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17' #and fcs.id = '25513'
ORDER BY fcs.created_on DESC;

-- new final to get unreconciled records
SELECT * FROM (SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on
,rlog.reconcile_amount,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(rlog.reconcile_amount = fcs.amount,fcs.amount ,ROUND(rlog.reconcile_amount,2)  )) AS unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17'
ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;


-- new final to get unreconciled sum
SELECT ROUND(SUM(amount),2) ttl_cr_amount,ROUND(SUM(unreconciled_amount),2) AS ttl_un_cr_amount FROM (SELECT fcs.amount,IF(rlog.reconcile_amount IS NULL,fcs.amount,IF(rlog.reconcile_amount = fcs.amount,fcs.amount ,rlog.reconcile_amount)) AS unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17'
ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;

#=> 1289066.8400000003 1288910.6400000001
#=> 147236.34 147236.34

 #==========================================================
-- // RESET RECONCILE TABLE
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_summary SET unreconciled_value = credit_amt,unreconciled_status = 'pending' WHERE credit_amt !=0;

#==========================================================
SELECT * FROM pnh_t_receipt_reconcilation_log
SELECT * FROM pnh_franchise_account_stat WHERE franchise_id = '17' AND id = '25512';

--  get unreconciled credit notes
SELECT * FROM (SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on
,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17'
ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;

-- 
SELECT ROUND(SUM(amount),2) ttl_cr_amount,ROUND(SUM(unreconciled_amount),2) AS ttl_un_cr_amount FROM (SELECT DISTINCT fcs.amount,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17'
ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;

--  get unreconciled credit notes
SELECT * FROM (SELECT DISTINCT 
fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on
,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount,fcs.unreconciled_status
		FROM pnh_franchise_account_stat fcs
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0'
AND fcs.franchise_id = '17'
ORDER BY fcs.created_on DESC) AS g 
WHERE g.unreconciled_amount > 0;


SELECT * FROM (SELECT fcs.id AS debit_note_id,fcs.amount,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2),rcon.unreconciled) AS inv_amount
                                                        FROM pnh_franchise_account_stat fcs
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.id
                                                        WHERE fcs.type=1 AND fcs.franchise_id = '17'
                                                        ORDER BY fcs.created_on DESC) AS g WHERE g.inv_amount > 0;

SELECT * FROM pnh_t_receipt_reconcilation;

# Feb_10_2014

ALTER TABLE `king_orders` ADD COLUMN `credit_days` INT(11) DEFAULT 0 NULL AFTER `is_paid`; 

SELECT MD5('shivaraj');
dshariuuudgs5h1d28-fs234234arabin445221

SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed,rcon.invoice_no,rcon.debit_note_id
                    ,rcon.inv_amount,rcon.unreconciled
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date,a.username
                FROM pnh_t_receipt_info r 
                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id AND is_reversed = 0
                JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                JOIN king_admin a ON a.id=rcon.created_by
                WHERE r.franchise_id = '43' AND r.receipt_id = '5382';

SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5388'
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id = '5388'
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ("8","9");

SELECT 25000 - 56 - 399;

SELECT * FROM pnh_franchise_account_stat WHERE TYPE='0' AND franchise_id = '43';

SELECT r.*,m.name AS modifiedby,f.franchise_name,a.name AS admin
						FROM pnh_t_receipt_info r
						JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
						LEFT OUTER JOIN king_admin a ON a.id=r.created_by
						LEFT OUTER JOIN king_admin m ON m.id=r.modified_by
						WHERE r.status=0 AND r.is_active=1 AND is_submitted=0 AND r.status=0 AND r.franchise_id= '43'
						ORDER BY instrument_date ASC

SELECT a.acc_correc_id,fcs.franchise_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,STATUS,a.created_on,rlog.reconcile_amount
,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value , ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
						FROM `pnh_franchise_account_summary` a
						LEFT JOIN pnh_franchise_account_stat fcs ON fcs.id = a.acc_correc_id
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
                                                LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id 
						WHERE a.franchise_id='43' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC;



#// get credit note info
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE credit_note_id = '27060'
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ("15");
SELECT * FROM pnh_franchise_account_stat


SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5388'
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE receipt_id = '5388'
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ("8","9");


SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed,rcon.invoice_no,rcon.debit_note_id
                    ,rcon.inv_amount,rcon.unreconciled
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date,a.username
                FROM pnh_franchise_account_stat fcs
                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id AND rlog.is_reversed = 0
                JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                JOIN king_admin a ON a.id=rcon.created_by
                WHERE fcs.franchise_id = '43' AND fcs.id = '27062'

#pnh_franchise_account_summary
SELECT id AS credit_note_id,franchise_id,`type`,amount,`desc`,is_correction,unreconciled_value,unreconciled_status,rlog.created_on
FROM pnh_franchise_account_stat fcs
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
WHERE TYPE=0 AND franchise_id='43' AND id='27062' ORDER BY rlog.created_on DESC;

SELECT id AS credit_note_id,franchise_id,`type`,amount,`desc`,is_correction,unreconciled_value,unreconciled_status,rlog.created_on
FROM pnh_franchise_account_stat fcs
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
WHERE TYPE=0 AND franchise_id='43' AND id='27062' ORDER BY rlog.created_on DESC;

# Feb_11_2014

SELECT * FROM king_orders;

SELECT *,FROM_UNIXTIME(batched_on) FROM shipment_batch_process_invoice_link WHERE batch_id = '6000' AND batched_by = '37' ORDER BY id DESC;
#=================================

	ALTER TABLE `m_vendor_info` ADD COLUMN `payment_type` INT(1) DEFAULT 0 NULL AFTER `require_payment_advance`;
	ALTER TABLE `king_orders` ADD COLUMN `credit_days` INT(11) DEFAULT 0 NULL AFTER `is_paid`; 


ALTER TABLE `king_orders` ADD COLUMN `is_paid` TINYINT(11) DEFAULT 0 NULL AFTER `partner_order_id`; 
ALTER TABLE `king_transactions` ADD COLUMN `credit_days` INT(11) DEFAULT 0 NULL AFTER `trans_grp_ref_no`;

ALTER TABLE m_product_info ADD COLUMN product_cat_id INT(11) DEFAULT 0 AFTER brand_id;

UPDATE m_product_info a 
	JOIN (
		SELECT itemid,product_id,catid,COUNT(*) AS t
		FROM m_product_deal_link a 
		JOIN king_dealitems b ON b.id = a.itemid
		JOIN king_deals c ON c.dealid = b.dealid 
		WHERE a.itemid IS NOT NULL AND a.itemid > 0 
		GROUP BY a.itemid
		HAVING t = 1 ) AS  h ON a.product_id = h.product_id 
		SET a.product_cat_id = h.catid;
		
		
UPDATE m_product_info a 
	JOIN (
		SELECT itemid,d.product_id,c.catid
		FROM m_product_group_deal_link a 
		JOIN king_dealitems b ON b.id = a.itemid
		JOIN king_deals c ON c.dealid = b.dealid 
		JOIN products_group_pids d ON d.group_id = a.group_id 
		WHERE a.itemid IS NOT NULL AND a.itemid > 0 
	)  AS  h ON a.product_id = h.product_id AND a.product_cat_id = 0 
	SET a.product_cat_id = h.catid;
#=================================

SELECT *,FROM_UNIXTIME(batched_on) FROM shipment_batch_process_invoice_link WHERE batch_id = '5779' AND batched_by = '37' ORDER BY id DESC;

-- 
SELECT * FROM (SELECT DISTINCT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
,rlog.reconcile_amount                                                
,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
												,fcs.unreconciled_status
												
                                                FROM pnh_franchise_account_stat fcs
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
                                                WHERE fcs.type = '0'
                                                AND fcs.franchise_id = '43'
                                                ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;
-- 
SELECT * FROM pnh_t_receipt_reconcilation_log

-- get unreconciled invoices
SELECT * FROM (SELECT DISTINCT i.invoice_no,rcon.unreconciled,ROUND( SUM( i.mrp - i.discount - i.credit_note_amt )  * i.invoice_qty , 2) AS amount
,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),MIN(rcon.unreconciled) ) AS inv_amount
#,if(rlog.reconcile_amount is null,round( sum( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),if(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,round(fcs.unreconciled_value,2)  )) as unreconciled_amount
		FROM king_invoice i
		JOIN king_transactions tr ON tr.transid=i.transid
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id #and rlog.is_reversed = 0
		WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '43' #and i.invoice_no is null 
		GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;

-- view receipts
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE credit_note_id = '27060'
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN ("15");
SELECT * FROM pnh_franchise_account_stat


SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5388'
SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '200696'
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE reconcile_id IN ("8","10",'21','22');


-- new

-- new get unreconciled invoices
SELECT * FROM (SELECT DISTINCT i.invoice_no,rcon.unreconciled,ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2) AS amount
,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2),rcon.unreconciled) AS inv_amount
		FROM pnh_t_receipt_reconcilation rcon



 king_invoice i
		JOIN king_transactions tr ON tr.transid=i.transid
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id #and rlog.is_reversed = 0
		WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '43' #and i.invoice_no is null 
		GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;


# Feb_12_2014

SELECT * FROM king_invoice i 
LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
WHERE i.invoice_no = '200696';

SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no='200696' ORDER BY created_on DESC LIMIT 1;

SELECT DISTINCT * FROM (SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
                                                ,rlog.reconcile_amount
                                                ,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
                                                ,fcs.unreconciled_status
                                                FROM pnh_franchise_account_stat fcs
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id

                                                WHERE fcs.type = '0'
                                                AND fcs.franchise_id = '43'
                                                ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0

-- new & final to get unique unreconculed credit notes 
SELECT * FROM (
		SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
			,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.amount,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
			,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
			,fcs.unreconciled_status
			FROM pnh_franchise_account_stat fcs 
			LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
			WHERE fcs.type = '0' AND fcs.franchise_id = '43'
			GROUP BY fcs.id 
			ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0;

SELECT DISTINCT * FROM (

		SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
			,rlog.reconcile_amount
			,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount

			,fcs.unreconciled_status
			FROM pnh_franchise_account_stat fcs
			JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id

			WHERE fcs.type = '0'
			AND fcs.franchise_id = '43'
			ORDER BY fcs.created_on DESC

) AS g WHERE g.unreconciled_amount > 0;


SELECT * FROM (
		SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
			,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.amount,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
			,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
			,fcs.unreconciled_status
			FROM pnh_franchise_account_stat fcs 
			LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
			WHERE fcs.type = '0' AND fcs.franchise_id = '43'
			GROUP BY fcs.id 
			ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0;

#=> 57

SELECT a.id
	FROM pnh_sch_discount_brands a 
	JOIN pnh_m_franchise_info b ON a.franchise_id = b.franchise_id
	WHERE dealid != 0 AND brandid = 76916829 AND b.territory_id = 22 AND UNIX_TIMESTAMP() BETWEEN valid_from AND valid_to AND a.is_sch_enabled = 1;


UPDATE pnh_sch_discount_brands a 
	JOIN pnh_m_franchise_info b ON a.franchise_id = b.franchise_id
	SET a.is_sch_enabled = 0 
	WHERE dealid != 0 AND brandid = 76916829 AND b.territory_id = 22 AND UNIX_TIMESTAMP() BETWEEN valid_from AND valid_to AND a.is_sch_enabled = 1;

# Feb_13_2014

SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5392';
SELECT * FROM pnh_t_receipt_reconcilation rcon WHERE rcon.id='29';

################### Show reconcile fields ##########################
-- new
SELECT * FROM pnh_t_receipt_reconcilation_log rlog
LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id=rlog.reconcile_id
WHERE rlog.receipt_id = '5392';
###########################################################

-- 1 TOTAL UNRECONCILE AMOUNT 
SELECT ROUND(SUM(amount),2) ttl_cr_amount,ROUND(SUM(unreconciled_amount),2) AS ttl_un_cr_amount FROM (SELECT DISTINCT fcs.amount,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
			FROM pnh_franchise_account_stat fcs
			LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
			WHERE fcs.type = '0'
			AND fcs.franchise_id = '17'
			ORDER BY fcs.created_on DESC) AS g WHERE g.unreconciled_amount > 0;

-- 2 UNRECONCILE LIST 
SELECT * FROM (SELECT fcs.id AS credit_note_id,fcs.type,fcs.amount,fcs.desc,FROM_UNIXTIME(fcs.created_on) AS created_on,fcs.franchise_id
		,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.amount,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
		,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
		,fcs.unreconciled_status
		FROM pnh_franchise_account_stat fcs 
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0' AND fcs.franchise_id = '17'
		GROUP BY fcs.id 
		ORDER BY fcs.created_on DESC
		) AS g WHERE g.unreconciled_amount > 0;

-- 2 UNRECONCILE LIST TOTAL
SELECT ROUND(SUM(amount),2) ttl_cr_amount,ROUND(SUM(unreconciled_amount),2) AS ttl_un_cr_amount FROM (

		SELECT fcs.amount,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.amount,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
		,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
		
		FROM pnh_franchise_account_stat fcs 
		LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
		WHERE fcs.type = '0' AND fcs.franchise_id = '17'
		GROUP BY fcs.id 
		ORDER BY fcs.created_on DESC
		) AS g WHERE g.unreconciled_amount > 0;

SELECT * FROM king_invoice WHERE invoice_no = '20141019623';

SELECT * FROM pnh_t_receipt_reconcilation_log

SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '20141019623'

SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5394'

SELECT * FROM pnh_franchise_account_stat WHERE TYPE='0' AND franchise_id = '59';

#==============================================================
ALTER TABLE `pnh_franchise_account_summary` ADD COLUMN `unreconciled_value` DOUBLE  DEFAULT '0' NULL  AFTER `created_by`, ADD COLUMN `unreconciled_status` VARCHAR (50) DEFAULT 'pending' NULL  AFTER `unreconciled_value`;
#==============================================================

-- new get list of franchise summary
SELECT * FROM (
SELECT fcs.acc_correc_id AS credit_note_id,fcs.action_type,fcs.credit_amt,fcs.remarks,DATE_FORMAT(fcs.created_on,'%e/%m/%Y') AS created_on,fcs.franchise_id
                                                ,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.credit_amt,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
                                                ,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.credit_amt,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
                                                ,fcs.unreconciled_status
                                                FROM pnh_franchise_account_summary fcs 
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id                                                WHERE fcs.action_type = '5' AND fcs.franchise_id = '59' AND credit_amt !='0'
                                                GROUP BY fcs.acc_correc_id 
                                                ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0;


-- new get list of franchise summary SUM - TOTAL
SELECT ROUND(SUM(credit_amt),2) ttl_cr_amount,ROUND(SUM(unreconciled_amount),2) AS ttl_un_cr_amount FROM ( SELECT fcs.credit_amt
                                                ,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.credit_amt,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
                                                FROM pnh_franchise_account_summary fcs 
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id                                                WHERE fcs.action_type = '5' AND fcs.franchise_id = '59' AND credit_amt !='0'
                                                GROUP BY fcs.acc_correc_id 
                                                ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0;

 #==========================================================
-- // RESET RECONCILE TABLE
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_summary SET unreconciled_value = credit_amt,unreconciled_status = 'pending' WHERE credit_amt !=0;

#==========================================================

SELECT * FROM pnh_franchise_account_summary WHERE action_type = '5' AND franchise_id = '59' AND credit_amt !='0';


SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '20141019623'

SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5394'

SELECT * FROM pnh_franchise_account_stat WHERE TYPE='0' AND franchise_id = '59';

SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '20141019623' OR debit_note_id = '0';

SELECT * FROM (SELECT DISTINCT i.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2), rcon.unreconciled ) AS inv_amount
                                                        FROM king_invoice i
                                                        JOIN king_transactions tr ON tr.transid=i.transid
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no #and rcon.unreconciled = 0
                                                        WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '59' #and i.invoice_no is null 
                                                        GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;

SELECT * FROM (SELECT fcs.id AS debit_note_id,fcs.amount,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2), (rcon.unreconciled) ) AS inv_amount
                                                        FROM pnh_franchise_account_stat fcs
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.id
                                                        WHERE fcs.type=1 AND fcs.franchise_id = '59'
                                                        ORDER BY fcs.created_on DESC) AS g WHERE g.inv_amount > 0;

# Feb_14_2014

SELECT * FROM m_stream_posts WHERE id  = 
SELECT * FROM m_stream_posts 

SELECT * FROM m_stream_post_reply WHERE post_id='70'

SELECT * FROM king_admin
0487cc982f7db39c51695026e4bdc692
ef8d47b9747e620fb3d29d17269c4c33
SELECT MD5('suresh')

franchise_id = '59';

SELECT * FROM m_stream_post_reply WHERE post_id='740' ORDER BY replied_on DESC LIMIT 100 # and id != ?

SELECT * FROM  pnh_franchise_account_summary WHERE action_type='5' AND debit_amt != 0 AND franchise_id = '59';

-- old old
SELECT * FROM (SELECT fcs.id AS debit_note_id,fcs.amount,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.amount, 2), (rcon.unreconciled) ) AS inv_amount
                                                        FROM pnh_franchise_account_stat fcs
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.id
                                                        WHERE fcs.type=1 AND fcs.franchise_id = '59'
                                                        ORDER BY fcs.created_on DESC) AS g WHERE g.inv_amount > 0;

-- NEW NEW to get list of debit entries:
SELECT * FROM (SELECT fcs.acc_correc_id AS debit_note_id,fcs.debit_amt AS amount,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, ROUND( fcs.debit_amt, 2), (rcon.unreconciled) ) AS inv_amount
			FROM pnh_franchise_account_summary fcs
			LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.acc_correc_id
			WHERE fcs.action_type='5' AND fcs.franchise_id = '17' AND debit_amt != 0
			ORDER BY fcs.created_on DESC
) AS g WHERE g.inv_amount > 0

======================================
# Feb_17_2014

SELECT * FROM pnh_t_receipt_reconcilation_log

# =============================
-- Shariff sir
SELECT k.* FROM (
SELECT g.p_invoice_no,invoice_status,inv,COUNT(*) AS t FROM (
		SELECT a.p_invoice_no,b.invoice_status,SUM(DISTINCT a.invoice_no) AS inv
			FROM shipment_batch_process_invoice_link a 
			JOIN proforma_invoices b ON a.p_invoice_no = b.p_invoice_no 
		WHERE a.p_invoice_no != 0 
		GROUP BY p_invoice_no 
		ORDER BY a.id DESC ) AS g
JOIN shipment_batch_process_invoice_link h ON g.p_invoice_no = h.p_invoice_no 
GROUP BY g.p_invoice_no
HAVING t > 1 AND invoice_status = 1 AND inv != 0 
ORDER BY h.id DESC ) AS k
JOIN king_invoice i ON i.invoice_no = k.inv
GROUP BY p_invoice_no
ORDER BY i.invoice_no DESC;
# =============================
-- OLD
SELECT a.acc_correc_id,fcs.franchise_id,fcs.type,a.debit_amt,a.credit_amt,a.remarks,STATUS,a.created_on,rlog.reconcile_amount
                        ,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.amount,fcs.unreconciled_value , ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount,fcs.unreconciled_status
						FROM `pnh_franchise_account_summary` a
						LEFT JOIN pnh_franchise_account_stat fcs ON fcs.id = a.acc_correc_id
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id
                                                LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
						WHERE a.franchise_id='59' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC;

-- old
SELECT a.acc_correc_id,a.franchise_id,a.action_type,a.debit_amt,a.credit_amt,a.remarks,`status`,a.created_on,rlog.reconcile_amount
                        ,IF(rlog.reconcile_amount IS NULL,a.unreconciled_value,IF(a.unreconciled_value = a.credit_amt,a.unreconciled_value , ROUND(a.unreconciled_value,2)  )) AS unreconciled_amount,a.unreconciled_status
						FROM `pnh_franchise_account_summary` a
						#left join pnh_franchise_account_stat fcs on fcs.id = a.acc_correc_id
                                                LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = a.acc_correc_id
                                                LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
						WHERE a.franchise_id='59' AND (a.action_type = 5 OR a.action_type = 6)
						ORDER BY a.created_on DESC;


-- new
SELECT fcs.franchise_id,fcs.action_type,fcs.acc_correc_id,fcs.debit_amt,fcs.is_returned,fcs.credit_amt,fcs.remarks,fcs.status,fcs.created_on,fcs.created_by
		,fcs.unreconciled_value,fcs.unreconciled_status,  SUM(rlog.reconcile_amount) AS reconcile_amount
FROM pnh_franchise_account_summary fcs 
LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id AND rlog.is_reversed !=1
WHERE fcs.action_type IN (5,6) AND fcs.acc_correc_id != 0 AND fcs.franchise_id='59' GROUP BY fcs.acc_correc_id,fcs.franchise_id;

-- old
SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed,rcon.invoice_no,rcon.debit_note_id
                    ,rcon.inv_amount,rcon.unreconciled
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date,a.username
                FROM pnh_franchise_account_stat fcs
                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.id AND rlog.is_reversed = 0
                JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                JOIN king_admin a ON a.id=rcon.created_by
                WHERE fcs.franchise_id = '59' AND fcs.id = '59'
-- old
SELECT fcs.acc_correc_id AS credit_note_id,fcs.franchise_id,`action_type`,fcs.credit_amt,fcs.`remarks`,fcs.unreconciled_value,fcs.unreconciled_status,DATE_FORMAT(FROM_UNIXTIME(rlog.created_on),'%e/%m/%Y') AS created_date
		FROM pnh_franchise_account_summary fcs
		JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id
		WHERE franchise_id='59' AND acc_correc_id='15317' ORDER BY rlog.created_on DESC

SELECT * FROM pnh_t_receipt_reconcilation rcon;
SELECT * FROM pnh_t_receipt_reconcilation_log rlog ;
SELECT * FROM pnh_franchise_account_summary fac WHERE acc_correc_id = '27068'
SELECT * FROM pnh_franchise_account_stat WHERE id = '27068';
SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5394';
 #==========================================================
-- // RESET RECONCILE TABLE
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_summary SET unreconciled_value = credit_amt,unreconciled_status = 'pending' WHERE credit_amt !=0;

#==========================================================

-- old
SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed,rcon.invoice_no,rcon.debit_note_id
                    ,rcon.inv_amount,rcon.unreconciled
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date,a.username
                FROM pnh_t_receipt_info r 
                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id AND is_reversed = 0
                JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                JOIN king_admin a ON a.id=rcon.created_by
                WHERE r.franchise_id = '59' AND r.receipt_id = '5394';

SELECT * FROM pnh_t_receipt_info WHERE receipt_id = '5394'

-- new 1
SELECT rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.reconcile_amount,rlog.is_reversed #,rcon.invoice_no,rcon.debit_note_id
                    #,rcon.inv_amount,rcon.unreconciled
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date #,a.username
                FROM pnh_t_receipt_info r 
                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id # and is_reversed = 0
                #join pnh_t_receipt_reconcilation rcon on rcon.id = rlog.reconcile_id
                #join king_admin a on a.id=rcon.created_by
                WHERE r.franchise_id = '59' AND r.receipt_id = '5394'

SELECT r.receipt_id,r.franchise_id,r.receipt_amount,r.remarks,r.unreconciled_value,unreconciled_status
	,rlog.credit_note_id,rlog.is_invoice_cancelled,rlog.is_reversed
	,rcon.invoice_no,rcon.debit_note_id,rcon.inv_amount,SUM(rlog.reconcile_amount) AS reconcile_amount,rcon.unreconciled,rcon.modified_on,rcon.modified_by
	,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date
	,a.username
	FROM pnh_t_receipt_info r 
	JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
	JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
	JOIN king_admin a ON a.id=rcon.created_by
	WHERE r.franchise_id = '59' AND r.receipt_id = '5387'
	GROUP BY rcon.invoice_no,rcon.debit_note_id;


SELECT r.receipt_id,r.franchise_id,r.receipt_amount,r.remarks,r.unreconciled_value,unreconciled_status ,rlog.credit_note_id,rlog.is_invoice_cancelled,SUM(rlog.reconcile_amount) AS reconcile_amount,rlog.is_reversed ,rcon.invoice_no,rcon.inv_amount,rcon.unreconciled,rcon.modified_on,rcon.modified_by ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date ,a.username FROM pnh_t_receipt_info r JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id JOIN king_admin a ON a.id=rcon.created_by WHERE r.franchise_id = '59' AND r.receipt_id = '5394'

SELECT * FROM (
SELECT fcs.acc_correc_id AS credit_note_id,fcs.action_type,fcs.credit_amt,fcs.remarks,DATE_FORMAT(fcs.created_on,'%e/%m/%Y') AS created_on,fcs.franchise_id
	,IF(rlog.reconcile_amount IS NULL,0,IF(rlog.reconcile_amount = fcs.credit_amt,rlog.reconcile_amount ,ROUND( SUM(rlog.reconcile_amount),2)  )) AS ttl_reconcile_amount
	,IF(rlog.reconcile_amount IS NULL,fcs.unreconciled_value,IF(fcs.unreconciled_value = fcs.credit_amt,fcs.unreconciled_value ,ROUND(fcs.unreconciled_value,2)  )) AS unreconciled_amount
	,fcs.unreconciled_status
	FROM pnh_franchise_account_summary fcs 
	LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id 
	WHERE fcs.action_type = '5' AND fcs.franchise_id = '59' AND credit_amt !='0' AND acc_correc_id !=0
	GROUP BY fcs.acc_correc_id 
	ORDER BY fcs.created_on DESC
) AS g WHERE g.unreconciled_amount > 0

SELECT * FROM pnh_franchise_account_summary fcs WHERE fcs.franchise_id = '59' AND fcs.action_type = '5' AND credit_amt !='0' AND acc_correc_id !=0;

#====================================================================
-- BEST RECONCILE LOG VIEW
SELECT rlog.*,rcon.debit_note_id,rcon.invoice_no FROM pnh_t_receipt_reconcilation_log rlog
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE rlog.receipt_id='5387';
#====================================================================

SELECT r.receipt_id,r.franchise_id,r.receipt_amount,r.remarks,r.unreconciled_value,unreconciled_status
	,rlog.credit_note_id,rlog.is_invoice_cancelled,rlog.is_reversed
	,rcon.invoice_no,rcon.debit_note_id,rcon.inv_amount,(rlog.reconcile_amount) AS reconcile_amount,rcon.unreconciled,rcon.modified_on,rcon.modified_by
	,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date
	,a.username
	FROM pnh_t_receipt_info r 
	JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
	JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
	JOIN king_admin a ON a.id=rcon.created_by
	WHERE r.franchise_id = '43' AND r.receipt_id = '5387'
	GROUP BY rcon.invoice_no,rcon.debit_note_id;


-- #==================================
## LOG for RECEIPTS RECONCILATION
SELECT r.receipt_id,rlog.credit_note_id,r.franchise_id,rcon.debit_note_id,rcon.invoice_no,rcon.inv_amount,(rlog.reconcile_amount) AS reconcile_amount,rcon.unreconciled,r.receipt_amount,r.remarks,r.unreconciled_value,unreconciled_status
                                ,rlog.is_invoice_cancelled,rlog.is_reversed
                                ,rcon.modified_on,rcon.modified_by
                                ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date
                                ,a.username
                                FROM pnh_t_receipt_info r 
                                JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
                                JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                                JOIN king_admin a ON a.id=rcon.created_by
                                WHERE r.franchise_id = '43' AND r.receipt_id = '5387';


SELECT * FROM king_deals dl
#join m_product_deal_link pdl on
WHERE tmp_pnh_dealid='1739268';

-- #==================================
# Feb_19_2014

# Feb_20_2014

-- <!--============================================<< OTHERS QUERIES >>===================================
//Coupon 

CREATE TABLE `pnh_m_coupons` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `coupon_slno` BIGINT(12) NOT NULL,
  `coupon_code` BIGINT(14) NOT NULL,
  `value` DOUBLE NOT NULL,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `member_id` BIGINT(8) DEFAULT NULL,
  `status` TINYINT(11) DEFAULT '0' COMMENT '0:pending,1:assigned to franchse,2:alloted to member',
  `assigned_by` TINYINT(11) DEFAULT NULL,
  `assigned_on` BIGINT(20) DEFAULT NULL,
  `alloted_on` BIGINT(20) DEFAULT NULL,
  `alloted_by` TINYINT(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`coupon_slno`,`coupon_code`)
) ;

ALTER TABLE `pnh_member_info` ADD COLUMN `voucher_balance` DOUBLE DEFAULT 0 NULL AFTER `points`; 

ALTER TABLE `pnh_member_info` ADD COLUMN `voucher_bal_validity` DATETIME NULL AFTER `dummy`;
//voucher_details
ALTER TABLE `pnh_t_voucher_details` CHANGE `assigned_by` `alloted_on` TINYINT(11) NULL, CHANGE `assigned_on` `alloted_by` DATETIME NULL, CHANGE `alloted_on` `activated_on` DATETIME NULL, CHANGE `alloted_by` `activated_by` TINYINT(11) NULL; 
ALTER TABLE `pnh_t_voucher_details` CHANGE `activated_by` `redeemed_on` TINYINT(11) NULL; 
ALTER TABLE `pnh_t_voucher_details` CHANGE `status` `status` TINYINT(11) DEFAULT 0 NULL COMMENT '0:pending,1:alloted to franchse,3:activated to franchise,3:coupon redeemed by customer/member', CHANGE `alloted_on` `alloted_on` DATETIME NULL, CHANGE `alloted_by` `alloted_by` INT NULL, ADD COLUMN `activated_by` INT NULL AFTER `activated_on`, CHANGE `redeemed_on` `redeemed_on` DATETIME NULL;
ALTER TABLE `pnh_t_voucher_details` CHANGE `value` `customer_value` DOUBLE DEFAULT 0 NOT NULL, ADD COLUMN `franchise_value` DOUBLE DEFAULT 0 NOT NULL AFTER `customer_value`;


//25/july voucher

 ALTER TABLE `pnh_t_voucher_details` DROP COLUMN `mobile_no`, ADD COLUMN `redeemed_on` DATETIME NULL AFTER `activated_on`, CHANGE `activation_mobileno` `redeem_activation_mobileno` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL; 
 
 ALTER TABLE `pnh_t_voucher_details` CHANGE `status` `status` TINYINT(11) DEFAULT 0 NULL COMMENT '0:assigned to franchise,1:Allloted to franchise,2:Activated,3:partailly Redeemed,4:Fully Reddemed,5:Cancelled,';
 
 ALTER TABLE `pnh_order_margin_track` ADD COLUMN `voucher_margin` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `bal_discount`; 

ALTER TABLE `pnh_voucher_activity_log` CHANGE `order_ids` `order_ids` VARCHAR(255) DEFAULT '0' NULL;    


//member scheme


CREATE TABLE `imei_m_scheme` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `menuid` BIGINT(11) DEFAULT NULL,
  `categoryid` BIGINT(20) DEFAULT NULL,
  `brandid` BIGINT(20) DEFAULT NULL,
  `scheme_type` TINYINT(11) DEFAULT NULL,
  `credit_value` DOUBLE(10,2) DEFAULT NULL,
  `scheme_from` BIGINT(20) DEFAULT NULL,
  `scheme_to` BIGINT(20) DEFAULT NULL,
  `sch_apply_from` BIGINT(20) DEFAULT NULL,
  `created_on` BIGINT(20) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` BIGINT(20) DEFAULT NULL,
  `modified_by` TINYINT(11) DEFAULT NULL,
  `is_active` TINYINT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `king_orders` ADD COLUMN `imei_reimbursement_value_perunit` DOUBLE(10,2) DEFAULT 0 NULL AFTER `super_scheme_processed`, ADD COLUMN `imei_scheme_id` BIGINT(20) DEFAULT 0 NULL AFTER `imei_reimbursement_value_perunit`;

ALTER TABLE `pnh_franchise_account_stat` CHANGE `imei_refid` `imei_refid` BIGINT(20) DEFAULT 0 NULL; 
 
 //knock mmeber scheme 
#added index for transactions table 
ALTER TABLE `king_transactions` ADD INDEX `init` (`init`);

/**STOREKING CART DB CHANGES**/
//franchise price QUOTE 

CREATE TABLE `pnh_franchise_price_quote` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(20) DEFAULT NULL,
  `pid` BIGINT(20) DEFAULT NULL,
  `mrp` DOUBLE DEFAULT NULL,
  `offrprice` DOUBLE DEFAULT NULL,
  `lprice` DOUBLE DEFAULT NULL,
  `quote` DOUBLE DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

//franchise product price enquery LOG TABLE

CREATE TABLE `pnh_franchise_pprice_enqrylog` (
  `id` BIGINT(30) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(30) DEFAULT NULL,
  `pid` BIGINT(30) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- <!--============================================<< OTHERS QUERIES >>===================================

ALTER TABLE `pnh_franchise_account_stat` DROP COLUMN `unreconciled_status`, DROP COLUMN `unreconciled_value`;


-- new
SELECT DISTINCT i.invoice_no,rcon.unreconciled AS unreconciled
		FROM king_invoice i
		JOIN king_transactions tr ON tr.transid=i.transid
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
		WHERE i.invoice_status=1 AND tr.is_pnh=1 AND rcon.unreconciled IS NULL AND tr.franchise_id= '415'

-- new
SELECT fcs.acc_correc_id AS debit_note_id,fcs.debit_amt AS amount,rcon.unreconciled AS unreconciled
			FROM pnh_franchise_account_summary fcs
			LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.debit_note_id = fcs.acc_correc_id
			WHERE fcs.action_type='5' AND debit_amt != 0 AND fcs.franchise_id = '415'

#====================================================================

-- <!--============================================<< BEST RECONCILE LOG VIEW >>===================================
SELECT rlog.*,rcon.debit_note_id,rcon.invoice_no,rcon.is_invoice_cancelled FROM pnh_t_receipt_reconcilation_log rlog
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE rlog.receipt_id='5396';
#====================================================================

-- <!--============================================<< RESET RECONCILE TABLE >>===================================-->
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_summary SET unreconciled_value = credit_amt,unreconciled_status = 'pending' WHERE credit_amt !=0;

#==========================================================

# Feb_24_2014
SELECT * FROM m_stream_post_reply 
#where post_id=? and id != ? 
ORDER BY replied_on DESC LIMIT 50;

CREATE TABLE `pnh_api_franchise_cart_info` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) DEFAULT NULL,
  `franchise_id` BIGINT(20) DEFAULT NULL,
  `pid` BIGINT(20) DEFAULT NULL,
  `qty` VARCHAR(275) DEFAULT NULL,
  `attributes` TEXT,
  `member_id` BIGINT(20) DEFAULT NULL,
  `status` INT(1) DEFAULT '1' COMMENT '1:item in cart,0:item removed from cart',
  `added_on` DATETIME DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- new ================================================
SELECT * FROM king_dealitems WHERE id='9758875986';

#m_product_group_deal_link
SELECT * FROM m_product_group_deal_link WHERE itemid = '9758875986'

#products_group_orders
SELECT * FROM products_group_orders WHERE 

-- old
SELECT #l.product_id,p.product_name,l.qty,p.is_sourceable as src 
* FROM m_product_deal_link l 
JOIN m_product_info p ON p.product_id=l.product_id WHERE l.itemid='7658178949'

-- new GET GROUP DEAL LINK
SELECT p.product_id,p.product_name,gpl.qty,p.is_sourceable AS src FROM m_product_group_deal_link gpl
JOIN products_group_pids g ON g.group_id = gpl.group_id
JOIN m_product_info p ON p.product_id = g.product_id
WHERE itemid = '9758875986';

#tmp_itemid
# product_group_pids
SELECT * FROM m_product_info
SELECT * FROM products_group_pids
SELECT * FROM m_product_group_deal_link

# Feb_25_2014

# Feb_26_2014
SELECT id AS menuid,NAME AS menuname FROM pnh_menu ORDER BY `name`;

SELECT fid,fml.menuid,`name` AS menuname FROM pnh_franchise_menu_link fml
JOIN pnh_menu m ON m.id = fml.menuid
 WHERE fml.fid='414'
ORDER BY NAME ASC

SELECT * FROM pnh_m_franchise_info

SELECT * FROM pnh_franchise_menu_link;

-- update
SELECT * FROM (
SELECT IFNULL(GROUP_CONCAT(DISTINCT fl.menuid),0) AS grp_menuids,f.created_on,f.is_suspended,GROUP_CONCAT(a.name) AS owners,tw.town_name AS town,f.is_lc_store,f.franchise_id,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,
							f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,
							f.email_id,u.name AS assigned_to,t.territory_name 
						FROM pnh_m_franchise_info f 
						LEFT OUTER JOIN king_admin u ON u.id=f.assigned_to 
						JOIN pnh_m_territory_info t ON t.id=f.territory_id 
						JOIN pnh_towns tw ON tw.id=f.town_id 
						JOIN pnh_m_class_info c ON c.id=f.class_id
						LEFT OUTER JOIN pnh_franchise_owners ow ON ow.franchise_id=f.franchise_id 
						LEFT OUTER JOIN king_admin a ON a.id=ow.admin
                                                LEFT JOIN pnh_franchise_menu_link fl ON fl.fid = f.franchise_id
						WHERE 1 GROUP BY f.franchise_id
) AS g WHERE (106) IN (g.grp_menuids);

#=>360 / 1061 ms
SELECT * FROM pnh_menu WHERE id='112';

SELECT fcs.franchise_id,fcs.action_type,fcs.acc_correc_id,fcs.debit_amt,fcs.is_returned,fcs.credit_amt,fcs.remarks,fcs.status,fcs.created_on,fcs.created_by
		,fcs.unreconciled_value,fcs.unreconciled_status,  SUM(rlog.reconcile_amount) AS reconcile_amount
FROM pnh_franchise_account_summary fcs 
LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.credit_note_id = fcs.acc_correc_id AND rlog.is_reversed !=1
WHERE fcs.action_type IN (5,6) AND fcs.acc_correc_id != 0 AND fcs.franchise_id='59' GROUP BY fcs.acc_correc_id,fcs.franchise_id

-- update

SELECT * FROM ( SELECT f.created_on,f.is_suspended,GROUP_CONCAT(a.name) AS owners,tw.town_name AS town,f.is_lc_store,f.franchise_id,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,
							f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,
							f.email_id,u.name AS assigned_to,t.territory_name,IFNULL(GROUP_CONCAT(DISTINCT fl.menuid),0) AS grp_menuids
						FROM pnh_m_franchise_info f 
						LEFT OUTER JOIN king_admin u ON u.id=f.assigned_to 
						JOIN pnh_m_territory_info t ON t.id=f.territory_id 
						JOIN pnh_towns tw ON tw.id=f.town_id 
						JOIN pnh_m_class_info c ON c.id=f.class_id
						LEFT OUTER JOIN pnh_franchise_owners ow ON ow.franchise_id=f.franchise_id 
						LEFT OUTER JOIN king_admin a ON a.id=ow.admin
						LEFT JOIN pnh_franchise_menu_link fl ON fl.fid = f.franchise_id
						WHERE 1 
					   AND f.is_suspended != 0 GROUP BY f.franchise_id  ORDER BY f.franchise_name ASC LIMIT 0,50 ) AS g  WHERE FIND_IN_SET(g.grp_menuids);

# Feb_28_2014

SELECT DISTINCT f.franchise_id FROM pnh_m_franchise_info f
JOIN pnh_franchise_menu_link fl ON fl.fid = f.franchise_id
 WHERE 1=1 AND fl.menuid='106' GROUP BY f.franchise_id 


DESC king_dealitems

SELECT * FROM king_categories

SELECT id AS attr_id,attr_name FROM m_attributes ORDER BY attr_name ASC

SELECT c.*,m.name AS main FROM king_categories c LEFT OUTER JOIN king_categories m ON m.id=c.type WHERE c.id='1037'

-- new 
SELECT * FROM m_attributes WHERE FIND_IN_SET(id,'1,2');

SELECT * FROM king_categories WHERE attribute_ids !=''

SELECT id,UCASE(SUBSTRING(attr_name, 1, -1)) FROM m_attributes WHERE FIND_IN_SET(id,'1,2' );

INSERT INTO m_product_attributes(pid,attr_id,attr_value) VALUES( ("123495486958",1,"41") , ("123495486958",2,"maroon") )

=========================================< UCFIRST()  >=====================================
DROP FUNCTION IF EXISTS UC_DELIMETER;
DELIMITER //
CREATE FUNCTION UC_DELIMETER(oldName VARCHAR(255), delim VARCHAR(1), trimSpaces BOOL) RETURNS VARCHAR(255)
BEGIN
  SET @oldString := oldName;
  SET @newString := "";
 
  tokenLoop: LOOP
    IF trimSpaces THEN SET @oldString := TRIM(BOTH " " FROM @oldString); END IF;
 
    SET @splitPoint := LOCATE(delim, @oldString);
 
    IF @splitPoint = 0 THEN
      SET @newString := CONCAT(@newString, UC_FIRST(@oldString));
      LEAVE tokenLoop;
    END IF;
 
    SET @newString := CONCAT(@newString, UC_FIRST(SUBSTRING(@oldString, 1, @splitPoint)));
    SET @oldString := SUBSTRING(@oldString, @splitPoint+1);
  END LOOP tokenLoop;
 
  RETURN @newString;
END//
DELIMITER ;
=========================================<  >=====================================

# Mar_01_2014

#	Mar_01_2014: Dealstock plugin module updates - 5 and Attributes Module ( Group Deal, Product Attributes, Category Attributes
#===========================< Attributes Related Tables >===============================================
# Mar_01_2014
CREATE TABLE `m_attributes` (  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `attr_name` VARCHAR (150) , PRIMARY KEY ( `id`))
INSERT INTO `m_attributes`(`id`,`attr_name`) VALUES ( '1','Size');
INSERT INTO `m_attributes`(`id`,`attr_name`) VALUES ( '2','Color');
CREATE TABLE `m_product_attributes` (  `id` BIGINT NOT NULL AUTO_INCREMENT , `pid` BIGINT , `attr_id` INT (150) , `attr_value` VARCHAR (150) , PRIMARY KEY ( `id`));
ALTER TABLE `king_categories` ADD COLUMN `attribute_ids` VARCHAR (125)  NULL  AFTER `prior`;
ALTER TABLE `king_dealitems` ADD COLUMN `is_group` TINYINT (1)  NULL  AFTER `tmp_pnh_dealid`;
#==========================================================================

SELECT * FROM m_product_info

SELECT * 
FROM m_product_info 
WHERE product_id='156140';
-- new
SELECT * FROM m_product_attributes WHERE pid='156140';

SELECT d.*,i.*,d.description,d.keywords,d.tagline FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid WHERE i.id='637675139343';

SELECT * FROM king_dealitems;

SELECT * FROM pnh_menu WHERE id='125';

# Mar_04_2014
SELECT * FROM pnh_t_receipt_reconcilation;

#=========< 1. >==============================
SELECT rlog.receipt_id,rcon.id AS reconcile_id,rcon.invoice_no,rcon.inv_amount,rlog.reconcile_amount FROM pnh_t_receipt_reconcilation rcon 
                                            JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                            WHERE rlog.is_reversed = 0 AND rcon.invoice_no = '200149';

SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = '200149' AND id = '3';

SELECT * FROM `pnh_t_receipt_reconcilation_log` WHERE `reconcile_id` = '3'
#==========< ## 2. >=================================
SELECT * FROM `pnh_t_receipt_reconcilation_log` WHERE `is_reversed` = 0 AND is_invoice_cancelled=0 AND `receipt_id`='5399'

SELECT * FROM `pnh_t_receipt_reconcilation` WHERE id IN (3,4,5)


-- <!--============================================<< BEST RECONCILE LOG VIEW >>===================================
SELECT rlog.*,rcon.debit_note_id,rcon.invoice_no,rcon.is_invoice_cancelled FROM pnh_t_receipt_reconcilation_log rlog
JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
WHERE rlog.receipt_id='5399';
#====================================================================

-- <!--============================================<< RESET RECONCILE TABLE >>===================================-->
TRUNCATE TABLE `pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `pnh_t_receipt_reconcilation_log`;
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0;
UPDATE pnh_franchise_account_summary SET unreconciled_value = credit_amt,unreconciled_status = 'pending' WHERE credit_amt !=0;

#==========================================================
SELECT * FROM pnh_franchise_account_summary WHERE acc_correc_id='11690';

SELECT * FROM king_invoice

SELECT * FROM t_imei_no = im

===================================================================
ALTER TABLE `snapittoday_db_jan_2014`.`t_imei_no` AUTO_INCREMENT=24580 COMMENT='' ROW_FORMAT=DYNAMIC ;
ALTER TABLE `snapittoday_db_jan_2014`.`t_imei_no` CHANGE `id` `id` BIGINT   NOT NULL AUTO_INCREMENT;
===================================================================

INSERT INTO `t_imei_no` (`product_id`, `imei_no`, `stock_id`, `grn_id`, `created_on`) VALUES ('152974', '6464646464', '163451', 4924, 1393923183);


#Mar_05_2014

ALTER TABLE `pnh_t_receipt_info` ADD COLUMN `cheq_realized_on` VARCHAR(255) NULL AFTER `activated_on`;
ALTER TABLE `pnh_m_deposited_receipts` ADD COLUMN `cheq_cancelled_on` VARCHAR(255) NULL AFTER `submitted_by`;

-- old
SELECT order_id,mrp,inv_qty,ROUND(mrp*a_disc_perc,2) AS disc FROM (
										SELECT a.id,order_id,a.product_id,mrp,( (qty+extra_qty)-release_qty) AS inv_qty,(i_discount+i_coup_discount) AS a_disc,((i_discount+i_coup_discount)/i_orgprice) AS a_disc_perc 
											FROM t_reserved_batch_stock a
											JOIN t_stock_info b ON a.stock_info_id = b.stock_id
											JOIN king_orders c ON c.id = a.order_id 
											WHERE a.status = 1 # and p_invoice_no = ? #and a.order_id = ''
										) AS g 

#oid = 1272208453 Rs. 125 Qty: 2 disc: 4.20
SELECT 125*2
-- new
SELECT a.id,order_id,a.product_id,mrp,( (qty+extra_qty)-release_qty) AS inv_qty,(i_discount+i_coup_discount) AS a_disc,((i_discount+i_coup_discount)/i_orgprice) AS a_disc_perc 
											FROM t_reserved_batch_stock a
											JOIN t_stock_info b ON a.stock_info_id = b.stock_id
											JOIN king_orders c ON c.id = a.order_id 
											WHERE a.status = 1 AND a.order_id = '1272208453' # and p_invoice_no = ? #
-- 
SELECT * #a.id,order_id,a.product_id,mrp,( (qty+extra_qty)-release_qty) as inv_qty,(i_discount+i_coup_discount) as a_disc,((i_discount+i_coup_discount)/i_orgprice) as a_disc_perc 
											FROM t_reserved_batch_stock a
											#join t_stock_info b on a.stock_info_id = b.stock_id
											JOIN king_orders c ON c.id = a.order_id 
											WHERE a.status = 1 AND a.order_id = '1272208453'

SELECT * FROM shipment_batch_process_invoice_link WHERE batch_id='5000' AND p_invoice_no IN ('116350','116335')

SELECT * FROM proforma_invoices WHERE p_invoice_no IN ('116307','116304','116350','116335') AND invoice_status='1';

# Mar_06_2014

SELECT * FROM m_product_attributes WHERE pid='5954';

SELECT * FROM pnh
-- new 
SELECT DISTINCT pa.attr_id,a.attr_name,pa.attr_value,pa.pid,c.id AS pcat_id FROM king_categories c
JOIN m_attributes a ON FIND_IN_SET(a.id,c.attribute_ids) 
LEFT JOIN m_product_attributes pa ON pa.attr_id = a.id AND pa.pid='5954'
WHERE c.attribute_ids !='' AND pa.is_active=1 AND c.id= '2'; # '1037'

SELECT a.id,a.attr_name,pa.attr_value,pa.pid FROM king_categories c JOIN m_attributes a ON FIND_IN_SET(a.id,c.attribute_ids) LEFT JOIN m_product_attributes pa ON pa.attr_id = a.id AND pa.pid= '0' WHERE c.attribute_ids !='' AND c.id='2'

SELECT * FROM m_product_info WHERE product_id='5954'

-- ========================< DB changes >==============================
ALTER TABLE `m_product_attributes` ADD COLUMN `pcat_id` BIGINT (20)  NULL  AFTER `pid`, ADD COLUMN `created_on` VARCHAR (100)  NULL  AFTER `attr_value`, ADD COLUMN `created_by` TINYINT (11)  NULL  AFTER `created_on`, ADD COLUMN `modified_on` VARCHAR (100)  NULL  AFTER `created_by`, ADD COLUMN `modified_by` TINYINT (100)  NULL  AFTER `modified_on`;

ALTER TABLE `m_product_attributes` ADD COLUMN `is_active` TINYINT (11) DEFAULT '1' NULL  COMMENT '1:active,2:deactive' AFTER `attr_value`;
ALTER TABLE `snapittoday_db_jan_2014`.`m_product_attributes` CHANGE `created_on` `created_on` VARCHAR (100)  NULL , CHANGE `modified_on` `modified_on` VARCHAR (100)  NULL;
ALTER TABLE `snapittoday_db_jan_2014`.`king_dealitems` CHANGE `is_group` `is_group` TINYINT (1) DEFAULT '0' NULL;
-- ========================< DB changes >==============================

-- ========================< Reset attributes column >==============================
UPDATE king_categories SET attribute_ids = '';
TRUNCATE TABLE m_product_attributes;
-- ========================< Reset attributes column >==============================

SELECT * FROM m_product_attributes;
SELECT * FROM king_categories WHERE id='1037'; #'2';

SELECT a.id,a.attr_name,pa.attr_value,pa.pid,pa.id FROM king_categories c
                                                JOIN m_attributes a ON FIND_IN_SET(a.id,c.attribute_ids) 
                                                LEFT JOIN m_product_attributes pa ON pa.attr_id = a.id AND pa.is_active=1 AND pa.pid= '5954' AND pa.pcat_id = c.id
                                                WHERE c.attribute_ids !='' AND c.id='2';

# Mar_07_2014
SELECT * FROM king_dealitems WHERE is_group='1'
SELECT * FROM king_dealitems WHERE id='9517175959';#'3628923721'//'9487464373';

SELECT * FROM m_product_deal_link WHERE itemid='9487464373'
SELECT * FROM m_product_group_deal_link WHERE itemid='9487464373'; #group_id:219495 #'9517175959';
SELECT * FROM products_group WHERE group_id='219495';
SELECT * FROM products_group_pids WHERE group_id='219495';

SELECT gdl.group_id,gdl.itemid,i.is_group FROM m_product_group_deal_link gdl
JOIN king_dealitems i ON i.id=gdl.itemid;
#=> 19493

UPDATE m_product_info a 
	JOIN (
		SELECT itemid,product_id,catid,COUNT(*) AS t
		FROM m_product_deal_link a 
		JOIN king_dealitems b ON b.id = a.itemid
		JOIN king_deals c ON c.dealid = b.dealid 
		WHERE a.itemid IS NOT NULL AND a.itemid > 0 
		GROUP BY a.itemid
		HAVING t = 1 ) AS  h ON a.product_id = h.product_id 
		SET a.product_cat_id = h.catid;

-- ========================< Set is groupid status start >==============================
SELECT gdl.group_id,gdl.itemid,i.is_group,i.pnh_id FROM m_product_group_deal_link gdl
JOIN king_dealitems i ON i.id=gdl.itemid;

UPDATE king_dealitems di
	JOIN (
		SELECT gdl.itemid AS itemid FROM m_product_group_deal_link gdl
		JOIN king_dealitems i ON i.id=gdl.itemid
	) AS g ON di.id = g.itemid SET di.is_group = 1;

#=>19485
-- ========================< Set is groupid status end >==============================

SELECT * 
FROM king_dealitems i 
LEFT JOIN m_product_deal_link pd ON pd.itemid = i.id
JOIN m_product_group_deal_link gd ON gd.itemid = i.id
JOIN products_group_pids pg ON pg.group_id = 
WHERE i.pnh_id='10005780';

SELECT * FROM m_product_attributes;
SELECT * FROM m_product_info WHERE product_id='10005780'
SELECT * FROM m_product_deal_link WHERE itemid='10005780'

#=========================================
SELECT a.id AS attr_id,a.attr_name,pa.attr_value,pa.pid,GROUP_CONCAT(pa.attr_value) AS grp_attr_value,GROUP_CONCAT(pa.attr_id) AS grp_attr_id
FROM king_dealitems di
JOIN m_product_deal_link pd ON pd.itemid = di.id
JOIN m_product_attributes pa ON pa.pid = pd.product_id AND pa.is_active=1
JOIN m_attributes a ON a.id =  pa.attr_id
WHERE di.pnh_id='10005781' GROUP BY pa.pid,pa.attr_id ORDER BY pa.pid ASC;

#================================================
SELECT * FROM m_product_deal_link WHERE itemid='9487464373';
SELECT * FROM m_product_attributes WHERE pid='156143';
#=========================================

################
# MENU
pnh_order_margin_track;

CREATE TABLE `pnh_menu_margin_track` (       
                         `id` BIGINT(20) NOT NULL AUTO_INCREMENT,   
                         `menu_id` BIGINT(20) DEFAULT NULL,         
                         `default_margin` DOUBLE DEFAULT NULL,      
                         `balance_discount` DOUBLE DEFAULT NULL,    
                         `balance_amount` BIGINT(20) DEFAULT NULL,  
                         `loyality_pntvalue` DOUBLE DEFAULT NULL,   
                         `created_by` INT(12) DEFAULT NULL,         
                         `created_on` BIGINT(20) DEFAULT NULL,      
                         PRIMARY KEY (`id`)                         
                       )

SELECT * FROM pnh_menu WHERE STATUS=1 GROUP BY id ORDER BY NAME ASC;

SELECT a.id AS attr_id,a.attr_name,GROUP_CONCAT(CONCAT(pa.id,':',pa.attr_value)) AS attr_vals,GROUP_CONCAT(pa.pid) AS pids FROM king_dealitems di
                                                                            JOIN m_product_deal_link pd ON pd.itemid = di.id
                                                                            JOIN m_product_attributes pa ON pa.pid = pd.product_id AND pa.is_active=1
                                                                            JOIN m_attributes a ON a.id =  pa.attr_id
                                                                            WHERE di.pnh_id='10005781' GROUP BY pa.attr_id;

SELECT a.id AS attr_id,a.attr_name,GROUP_CONCAT(CONCAT(pa.id,':',pa.attr_value)) AS attr_vals,GROUP_CONCAT(pa.pid) AS pids FROM king_dealitems di
                                                                            JOIN m_product_deal_link pd ON pd.itemid = di.id
                                                                            JOIN m_product_attributes pa ON pa.pid = pd.product_id AND pa.is_active=1
                                                                            JOIN m_attributes a ON a.id =  pa.attr_id
                                                                            WHERE di.pnh_id='10005781' GROUP BY pa.attr_id;

#=========================< new count of num of products linked to a product by itemid >=====================================
SELECT * FROM (  SELECT itemid,COUNT(*) AS ttl_prdt FROM m_product_deal_link
WHERE itemid IS NOT NULL AND itemid='2711322288' 
GROUP BY itemid ) AS g WHERE g.ttl_prdt > 4;
#=========================================
-- new
#=========================< new count of num of products linked to a product by PNH_ID >=====================================
SELECT pd.itemid,COUNT(*),di.is_sourceable AS ttl_prdt 
FROM m_product_deal_link pd 
JOIN king_dealitems di ON di.id=pd.itemid
WHERE pd.itemid IS NOT NULL AND di.pnh_id='10005781' GROUP BY pd.itemid

#=========================================
SELECT a.id AS attr_id,a.attr_name,GROUP_CONCAT(CONCAT(pa.id,":",pa.attr_value)) AS attr_vals#,group_concat(pa.pid) as pids
 FROM king_dealitems di
						JOIN m_product_deal_link pd ON pd.itemid = di.id
						JOIN m_product_attributes pa ON pa.pid = pd.product_id AND pa.is_active=1
						JOIN m_attributes a ON a.id =  pa.attr_id
						WHERE di.pnh_id='10005781' GROUP BY pa.attr_id;
#=========================================

-- new
SELECT s.* FROM m_product_attributes s WHERE s.id='11' ;

-- new
SELECT a.id AS attr_id,a.attr_name,pa.pid,GROUP_CONCAT(CONCAT(pa.id,':',pa.attr_value)) AS attr_vals
 FROM m_product_attributes pa
	JOIN m_attributes a ON a.id =  pa.attr_id
	WHERE pa.pid='156145' AND pa.id!='11' GROUP BY pa.attr_id;
#=========================================

#Mar-10_2014

SELECT 1 FROM pnh_member_info WHERE pnh_member_id='21111111';

#====================================================================================================================================================================
CREATE TABLE `pnh_member_offers` (  `sno` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `member_id` VARCHAR (120) , `offer_type` TINYINT (11) DEFAULT '0', `offer_value` VARCHAR (120) DEFAULT '0', `offer_towards` VARCHAR (200) , `transid_ref` VARCHAR (120) , `insurance_id` VARCHAR (120) DEFAULT '0', `process_status` TINYINT (11),`feedback_status` VARCHAR (50) DEFAULT '0' NULL , `created_by` INT (120) ,`referred_by` VARCHAR (100) DEFAULT '0' NULL,`referred_status` TINYINT (11)  DEFAULT '0' NULL , `created_on` VARCHAR (120) , PRIMARY KEY ( `sno`));

#alter table `pnh_member_offers` add column `feedback_status` varchar (50) DEFAULT '0' NULL  after `status`;
#alter table `pnh_member_offers` add column `referred_by` varchar (100) DEFAULT '0' NULL  after `feedback_status`;
#alter table `snapittoday_db_jan_2014`.`pnh_member_offers` add column `referred_status` tinyint (11)  NULL  after `referred_by`;
#====================================================================================================================================================================



SELECT * FROM pnh_invoice_transit_log;

SELECT * FROM pnh_member_offers;

SELECT COUNT(*) AS c FROM pnh_member_offers WHERE member_id='22017123' AND offer_type=0;

SELECT * FROM pnh_member_offers WHERE process_status = 0 ORDER BY DESC created_on LIMIT 100;

SELECT * FROM (
	SELECT COUNT(*) AS  FROM pnh_member_offers WHERE referred_by !=0 AND referred_status = 0 ORDER BY created_on DESC LIMIT 100
) AS COUNT JOIN pnh_member_offers a
	JOIN pnh_member_info b ON b.id=a.referred_by;

-- new
SELECT b.* FROM pnh_member_offers a
	JOIN pnh_member_info b ON b.pnh_member_id=a.referred_by
	WHERE a.referred_by !=0 AND a.referred_status = 0 
	ORDER BY a.created_on DESC LIMIT 100;

-- new 
SELECT num_referred,referred_by,FLOOR(num_referred/3) AS times FROM (
	SELECT COUNT(referred_by) AS num_referred,referred_by FROM pnh_member_offers WHERE referred_by !=0 AND referred_status = 0 GROUP BY referred_by ORDER BY created_on DESC LIMIT 100 
) AS a WHERE a.num_referred >= 3;

SELECT * FROM pnh_invoice_transit_log;
#1:in-transit,2:pickup or hand-over,3:delivered,4:return

SELECT r.receipt_id,rlog.credit_note_id,r.franchise_id,rcon.debit_note_id,rcon.invoice_no,rcon.inv_amount,SUM(rlog.reconcile_amount) AS reconcile_amount,rcon.unreconciled,r.receipt_amount,r.remarks,			r.unreconciled_value,unreconciled_status
                    ,rlog.is_invoice_cancelled,rlog.is_reversed
                    ,rcon.modified_on,rcon.modified_by
                    ,DATE_FORMAT(FROM_UNIXTIME(rcon.created_on),'%e/%m/%Y') AS created_date
                    ,a.username
                    FROM pnh_t_receipt_info r 
                    JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.receipt_id = r.receipt_id
                    JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
                    JOIN king_admin a ON a.id=rcon.created_by
                    WHERE r.franchise_id = '408' AND r.receipt_id = '5415' GROUP BY rcon.invoice_no,rcon.debit_note_id;


#================================
SELECT * FROM process_insurance_details;

#===============================================================================
#Mar_11_2014
CREATE TABLE `pnh_member_insurance` (  `sno` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `fid` VARCHAR (120) , `mid` VARCHAR (120) , `insurance_id` VARCHAR (120) , `insurance_type` VARCHAR (100) , `mem_address` VARCHAR (150) , PRIMARY KEY ( `sno`));
#===============================================================================

# Mar_12_2014
# ====================< RESET MEMBER OFFERS TABLES START >=====================================
TRUNCATE TABLE `pnh_member_insurance`;
TRUNCATE TABLE `pnh_member_offers`;
#truncate table `pnh_member_offers_referral`;
# ====================< RESET MEMBER OFFERS TABLES END >=====================================
se

SELECT COUNT(*) AS m FROM pnh_member_info mi
JOIN pnh_member_offers mo ON mo.member_id != mi.id
 WHERE member_id=

SELECT * FROM pnh_member_offers WHERE process_status = 0  AND offer_type=1 ORDER BY created_on DESC LIMIT 100
SELECT * FROM pnh_member_offers WHERE process_status = 0  AND offer_type=2 ORDER BY created_on DESC LIMIT 100;

/*[11:36:52 AM][51 ms]*/ CREATE TABLE `insurance_m_types`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) ); 
 /*[11:37:05 AM][0 ms]*/INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( '1',NULL); 
/*[11:37:53 AM][0 ms]*/ UPDATE `insurance_m_types` SET `name`='Aadhar ' WHERE `id`='1'; 
/*[11:38:22 AM][0 ms]*/ INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( '2','Driving Licence'); 
/*[11:38:37 AM][0 ms]*/ INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( NULL,'Voter ID');  
CREATE TABLE `pnh_member_insurance` (                    
                        `sno` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,     
                        `fid` VARCHAR(120) DEFAULT NULL,                       
                        `mid` VARCHAR(120) DEFAULT NULL,                       
                        `offer_type` TINYINT(11) DEFAULT NULL,                 
                        `insurance_id` VARCHAR(120) DEFAULT NULL,              
                        `insurance_type` VARCHAR(100) DEFAULT NULL,            
                        `mem_address` VARCHAR(150) DEFAULT NULL,               
                        `offer_status` TINYINT(11) DEFAULT NULL,               
                        PRIMARY KEY (`sno`)                                    
                      );

#===============================================================================

ALTER TABLE `pnh_member_insurance` ADD COLUMN `opted_insurance` TINYINT (11) DEFAULT '0' NULL  AFTER `proof_address`,CHANGE `offer_type` `offer_type` TINYINT (11)  NULL , CHANGE `insurance_id` `proof_id` VARCHAR (120)  NULL  COLLATE latin1_swedish_ci , CHANGE `insurance_type` `proof_type` VARCHAR (100)  NULL  COLLATE latin1_swedish_ci , CHANGE `mem_address` `proof_address` VARCHAR (150)  NULL  COLLATE latin1_swedish_ci;

ALTER TABLE `pnh_member_offers` CHANGE `insurance_id` `proof_id` VARCHAR (120) DEFAULT '0' NULL  COLLATE latin1_swedish_ci;

ALTER TABLE `pnh_member_offers` ADD COLUMN `delivery_status` VARCHAR (11) DEFAULT '0' NULL  COMMENT '0:not delivered,1:delivered' AFTER `feedback_status`,CHANGE `process_status` `process_status` TINYINT (11)  NULL  COMMENT '0:Not Processed,1:Ready Process,2:Processed,', CHANGE `feedback_status` `feedback_status` VARCHAR (50) DEFAULT '0' NULL  COLLATE latin1_swedish_ci  COMMENT '0:not given,1:given';

#===============================================================================
-- 
SELECT * FROM pnh_member_offers WHERE transid_ref='PNH38292';
-- 
UPDATE pnh_member_offers SET delivery_status = 1 WHERE transid_ref='PNH22337';
-- 
SELECT u.*,COUNT(o.transid) AS orders,f.franchise_name AS fran FROM pnh_member_info u LEFT OUTER JOIN king_orders o ON o.userid=u.user_id LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=u.franchise_id WHERE u.user_id='103440';

-- 
SELECT a.*,b.user_id,b.first_name,f.franchise_name FROM pnh_member_offers a 
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id  
JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
WHERE a.offer_type=2 ORDER BY a.created_on DESC LIMIT 100;

# Mar_14_2014
has_insurance
insurance_logid
insurance_amount
#===============================================================================
ALTER TABLE `king_orders` ADD COLUMN `has_insurance` TINYINT (11) DEFAULT '0' NULL  AFTER `is_paid`, ADD COLUMN `insurance_logid` BIGINT   NULL  AFTER `has_insurance`, ADD COLUMN `insurance_amount` DOUBLE   NULL  AFTER `insurance_logid`;

#drop table `insurance_m_type`;
ALTER TABLE `pnh_member_offers` ADD COLUMN `pnh_pid` VARCHAR (120)  NULL  AFTER `offer_towards`;

#===============================================================================

SELECT * FROM king_dealitems WHERE pnh_id='12759871' #=>9884254185
SELECT * FROM king_deals WHERE dealid='5172296214';

-- -- -- -- -- -- -- -- -- new ----=------------------------
SELECT *,d.menuid FROM king_orders o
JOIN king_dealitems di ON di.id=o.itemid
JOIN king_deals d ON d.dealid=di.dealid
WHERE o.transid='PNH64833' AND di.pnh_id='12759871';
#==========================================================================================
ALTER TABLE `pnh_member_insurance` ADD COLUMN `insurance_id` VARCHAR (150)  NULL  AFTER `sno`, ADD COLUMN `insurance_value` DOUBLE   NULL  AFTER `offer_status`, ADD COLUMN `insurance_margin` VARCHAR (50) DEFAULT '0' NULL  AFTER `insurance_value`, ADD COLUMN `order_value` DOUBLE   NULL  AFTER `insurance_margin`, ADD COLUMN `first_name` VARCHAR (60)  NULL  AFTER `order_value`, ADD COLUMN `last_name` VARCHAR (60)  NULL  AFTER `first_name`, ADD COLUMN `mob_no` VARCHAR (50)  NULL  AFTER `last_name`, ADD COLUMN `address` TEXT   NULL  AFTER `mob_no`, ADD COLUMN `city` VARCHAR (50)  NULL  AFTER `address`, ADD COLUMN `pincode` VARCHAR (50)  NULL  AFTER `city`, ADD COLUMN `created_by` VARCHAR (20)  NULL  AFTER `pincode`, ADD COLUMN `created_on` VARCHAR (60)  NULL  
AFTER `created_by`,CHANGE `offer_status` `offer_status` TINYINT (11) DEFAULT '0' NULL;

ALTER TABLE `pnh_member_insurance` ADD COLUMN `itemid` VARCHAR (25)  NULL  AFTER `order_value`;
ALTER TABLE `pnh_member_offers` CHANGE `proof_id` `insurance_id` VARCHAR (120) DEFAULT '0' NULL  COLLATE latin1_swedish_ci;
ALTER TABLE `pnh_member_insurance` DROP COLUMN `address`;
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
#==========================================================================================
CREATE TABLE `pnh_member_insurance_menu` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `menu_id` BIGINT(11) NOT NULL,
  `greater_than` DOUBLE DEFAULT NULL,
  `less_than` DOUBLE DEFAULT NULL,
  `insurance_value` DOUBLE DEFAULT NULL,
  `insurance_margin` DOUBLE NOT NULL DEFAULT '0',
  `is_active` TINYINT(1) DEFAULT '1',
  `created_by` BIGINT(11) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `updated_by` BIGINT(11) DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

id
menu_id           
greater_than      
less_than         
insurance_value   
insurance_margin  
is_active         
created_by        
created_on        
updated_by        
updated_on      
===============
is_group           TINYINT(11)          (NULL)             YES             0                        SELECT,INSERT,UPDATE,REFERENCES         
has_insurance  

-- old
SELECT a.*,b.user_id,b.first_name,f.*, DATE(FROM_UNIXTIME(a.created_on)) AS DATE FROM pnh_member_offers a 
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id  
WHERE a.offer_type=2 ORDER BY a.created_on DESC LIMIT 100;

SELECT insurance_value,insurance_margin FROM pnh_member_insurance_menu im WHERE menu_id='112' AND is_active = 1

SELECT insurance_value,insurance_margin FROM pnh_member_insurance_menu im WHERE is_active = 1 AND menu_id='112'

SELECT d.menuid FROM king_orders o
                                    JOIN king_dealitems di ON di.id=o.itemid
                                    JOIN king_deals d ON d.dealid=di.dealid
                                    WHERE o.transid='PNH33729' AND di.pnh_id='12759871';

SELECT o.itemid,o.transid FROM king_orders o
JOIN king_dealitems di ON di.id=o.itemid
WHERE o.transid='PNH64833' AND di.pnh_id='12759871'; # => 5172296214

# Mar_15_2014
SELECT *,o.itemid,o.transid FROM king_orders o WHERE o.transid='PNH64833' AND o.itemid='5172296214'
-- old
SELECT o.itemid FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
                                                WHERE o.transid='PNH45641' AND di.pnh_id='12759871';

# ====================< RESET MEMBER OFFERS TABLES START >=====================================
TRUNCATE TABLE `pnh_member_insurance`;
TRUNCATE TABLE `pnh_member_offers`;
TRUNCATE TABLE `pnh_member_offers_log`;
UPDATE king_orders SET has_insurance='',insurance_logid='',insurance_amount='' WHERE 1=1;
#truncate table `pnh_member_offers_referral`;
# ====================< RESET MEMBER OFFERS TABLES END >=====================================

-- old
SELECT a.*,b.user_id,b.first_name,f.*, DATE(FROM_UNIXTIME(a.created_on)) AS DATE FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id  
WHERE a.offer_type IN (0,2)
ORDER BY a.created_on DESC LIMIT 100;

SELECT a.*,b.first_name,b.user_id FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id  WHERE a.transid_ref='PNH16326';

SELECT a.*,b.user_id,b.first_name,f.*,DATE(FROM_UNIXTIME(a.created_on)) AS DATE FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id WHERE a.offer_type=1 ORDER BY a.created_on DESC LIMIT 100;

SELECT * FROM pnh_member_info WHERE pnh_member_id='22017148';
# 7962767596  4922585172  1797426333
3854235621
7338377355
4685349726
4896893429
6612136114

#,mi.insurance_id,mi.fid,mi.mid
SELECT *,a.username,mf.member_id,mf.transid_ref AS transid FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.insurance_id
                        JOIN king_admin a ON a.id = mi.created_by
                        WHERE mi.insurance_id = '2971433743';

SELECT NAME AS dealname,pnh_id FROM king_dealitems WHERE id='5172296214';

SELECT mi.*,a.username,mf.transid_ref AS transid FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.insurance_id
                        JOIN king_admin a ON a.id = mi.created_by
                        WHERE mi.insurance_id = '2971433743'

SELECT di.name AS dealname,di.pnh_id,d.menuid,mn.name AS menuname,d.brandid FROM king_dealitems di
                                                    JOIN king_deals d ON d.dealid=di.dealid
                                                    JOIN pnh_menu mn ON mn.id=d.menuid
                                                    WHERE di.id='2971433743';
# Mar_17_2014

UPDATE pnh_member_offers SET feedback_status = 1 WHERE 1=0; sno IN ( SELECT sno FROM pnh_member_offers WHERE feedback_status=0 AND delivery_status=1 AND member_id='21111111'  );

SELECT sno FROM pnh_member_offers WHERE feedback_status=0 AND delivery_status=1 AND member_id= '21111111';

-- new 
SELECT COUNT(*) AS t FROM pnh_member_offers WHERE delivery_status = '0' AND process_status='0' AND feedback_status='0' AND transid_ref='PNH16326';

SELECT * FROM pnh_member_info WHERE pnh_member_id='22012596';
#=========================================================================
# Mar_18_2014

SELECT * FROM king_invoice WHERE transid='PNH55553'
SELECT * FROM king_orders WHERE transid='PNH55553'
5=>3854235621
6=>7338377355

#=========================================================================
ALTER TABLE `king_orders` CHANGE `insurance_logid` `insurance_id` VARCHAR (100)  NULL;
#=========================================================================

SELECT a.*,b.first_name,b.user_id,di.name product_name FROM pnh_member_offers a 
                                                        JOIN pnh_member_info b ON b.pnh_member_id=a.member_id
                                                        JOIN pnh_member_insurance mi ON mi.insurance_id=a.insurance_id
                                                        LEFT JOIN king_dealitems di ON di.id = mi.itemid
                                                        WHERE a.transid_ref='PNH55553' AND mi.itemid='7682578652';
#PNH98587 

SELECT a.*,di.name,di.id AS itemid,pdl.product_id,b.user_id,b.first_name,f.franchise_name,f.address,f.territory_id,f.town_id
FROM pnh_member_offers a
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id
JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
JOIN king_dealitems di ON di.pnh_id = a.pnh_pid
JOIN m_product_deal_link pdl ON pdl.itemid=di.id
WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 100;

SELECT * FROM king_dealitems WHERE id='7682578652'
SELECT * FROM m_product_info WHERE product_id='28089'
SELECT * FROM m_product_deal_link WHERE product_id='28089';

#Mar_19_2014

SELECT IFNULL(SUM(s.available_qty),0) AS stock,GROUP_CONCAT(s.available_qty) AS stocks,p.*,b.name AS brand,b.id AS bid,c.id AS cid,c.name AS category FROM m_product_info p 
LEFT OUTER JOIN t_stock_info s ON s.product_id=p.product_id 
JOIN king_brands b ON b.id=p.brand_id 
JOIN king_categories c ON c.id=p.product_cat_id 
WHERE p.product_id= '155921'; #'155926';

SELECT p.product_id,p.mrp,r.default_location_id AS loc,r.default_rack_bin_id AS rack FROM m_product_info p LEFT OUTER JOIN m_brand_location_link r ON r.brand_id=p.brand_id LEFT OUTER JOIN t_stock_info s ON s.product_id=p.product_id 
WHERE p.product_id='155921'
GROUP BY p.product_id HAVING COUNT(s.product_id)=0;

#Mar_20_2014

ALTER TABLE `pnh_member_insurance` ADD COLUMN `menu_log_id` VARCHAR (100)  NULL  AFTER `mid`;

SELECT * FROM t_reserved_batch_stock WHERE product_id='3115';
SELECT * FROM t_stock_info WHERE available_qty < 0;


SELECT product_barcode,stock_id,product_id,available_qty,location_id,rack_bin_id,mrp,IF((mrp-'0'),1,0) AS mrp_diff 
                                            FROM t_stock_info WHERE mrp > 0  AND product_id = '155921' AND available_qty > 0 
                                            ORDER BY product_id DESC,mrp_diff,mrp;


SELECT * FROM (
(
SELECT i.itemid,di.dealid,d.publish,!SUM(stat) AS new_deal_stat FROM (
SELECT itemid,product_id,psrc,stk,IF(SUM(psrc+IF(IFNULL(stk,0),1,0)),0,1) AS stat FROM (
SELECT a.itemid,b.product_id,a.qty,a.is_active,c.is_sourceable AS psrc,SUM(available_qty) AS stk 
FROM m_product_group_deal_link a
JOIN products_group_pids b ON a.group_id = b.group_id 
JOIN (SELECT itemid 
FROM m_product_group_deal_link a 
JOIN products_group_pids b ON a.group_id = b.group_id 
WHERE product_id = '28089' AND is_active = 1) AS b ON b.itemid = a.itemid 
JOIN m_product_info c ON c.product_id = b.product_id
LEFT JOIN t_stock_info d ON d.product_id = b.product_id 
GROUP BY a.itemid,b.product_id )AS h
GROUP BY itemid,product_id ) AS i 
JOIN king_dealitems di ON di.id = i.itemid
JOIN king_deals d ON d.dealid = di.dealid
GROUP BY itemid 
HAVING publish != new_deal_stat 
)
UNION(
SELECT i.itemid,di.dealid,d.publish,!SUM(stat) AS new_deal_stat 
FROM (
SELECT itemid,product_id,psrc,stk,IF(SUM(psrc+IF(stk,1,0)),0,1) AS stat FROM (
SELECT a.itemid,a.product_id,a.qty,a.is_active,c.is_sourceable AS psrc,SUM(available_qty) AS stk 
FROM m_product_deal_link a
JOIN (SELECT itemid FROM m_product_deal_link WHERE product_id = '28089' AND is_active = 1) AS b ON b.itemid = a.itemid 
JOIN m_product_info c ON c.product_id = a.product_id
LEFT JOIN t_stock_info d ON d.product_id = a.product_id 
GROUP BY a.itemid,a.product_id 
)AS h
GROUP BY itemid,product_id ) AS i 
JOIN king_dealitems di ON di.id = i.itemid
JOIN king_deals d ON d.dealid = di.dealid
GROUP BY itemid 
HAVING publish != new_deal_stat 
) ) AS g;

#================================================================================================
# Mar_21_2014

block_ip_addr;

-- new 
SELECT si.*,pi.product_name,pi.mrp,pi.is_sourceable FROM t_stock_info si
JOIN m_product_info PI ON pi.product_id =  si.product_id
WHERE si.available_qty < 0;

SELECT * FROM 

Mar_22_2014

//MANAGE LOYALITY POINTS

/*[1:58:19 PM][2077 ms]*/ ALTER TABLE `king_orders` ADD COLUMN `lpoint_valid_days` DOUBLE NULL AFTER `member_id`; 

/*[5:24:49 PM][98 ms]*/ ALTER TABLE `pnh_member_points_track` ADD COLUMN `valid_till` BIGINT(20) NULL AFTER `created_on`; 

/*[12:32:00 PM][30 ms]*/ ALTER TABLE `pnh_loyalty_points` ADD COLUMN `created_on` DATETIME NULL AFTER `valid_days`, ADD COLUMN `updated_on` DATETIME NULL AFTER `created_on`, ADD COLUMN `created_by` INT(11) NULL AFTER `updated_on`;

/*[6:02:58 PM][99 ms]*/ ALTER TABLE `pnh_member_points_track` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 NULL AFTER `valid_till`; 


/*[1:29:02 PM][3501 ms]*/ ALTER TABLE `snapitto_dbexisting`.`king_dealitems` ADD COLUMN `has_insurance` TINYINT(1) NULL AFTER `is_group`; 
/*[2:49:13 PM][63 ms]*/ ALTER TABLE `king_dealitems` CHANGE `has_insurance` `has_insurance` TINYINT(1) DEFAULT 0 NULL; 
/*[3:19:55 PM][3392 ms]*/ ALTER TABLE `king_dealitems` CHANGE `has_insurance` `has_insurance` TINYINT(1) DEFAULT 0 NOT NULL; 

 /*[11:36:52 AM][51 ms]*/ CREATE TABLE `insurance_m_types`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) ); 
 /*[11:37:05 AM][0 ms]*/INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( '1',NULL); 
/*[11:37:53 AM][0 ms]*/ UPDATE `insurance_m_types` SET `name`='Aadhar ' WHERE `id`='1'; 
/*[11:38:22 AM][0 ms]*/ INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( '2','Driving Licence'); 
/*[11:38:37 AM][0 ms]*/ INSERT INTO `insurance_m_types`(`id`,`name`) VALUES ( NULL,'Voter ID'); 

/*[1:10:01 PM][61 ms]*/ CREATE TABLE `pnh_m_insurance_menu`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `menu_id` BIGINT(11) NOT NULL, `greater_than` DOUBLE, `less_than` DOUBLE, `insurance_value` DOUBLE, `created_by` BIGINT(11), `created_on` DATETIME, `updated_by` BIGINT(11), `updated_on` DATETIME, PRIMARY KEY (`id`) ); 


=============================================
CREATE TABLE `pnh_member_insurance_menu` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `menu_id` BIGINT(11) NOT NULL,
  `greater_than` DOUBLE DEFAULT NULL,
  `less_than` DOUBLE DEFAULT NULL,
  `insurance_value` DOUBLE DEFAULT NULL,
  `insurance_margin` DOUBLE NOT NULL DEFAULT '0',
  `is_active` TINYINT(1) DEFAULT '1',
  `created_by` BIGINT(11) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `updated_by` BIGINT(11) DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE `user_order_transactions` (
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
  `batch_enabled` TINYINT(1) NOT NULL DEFAULT '1',
  `admin_trans_status` TINYINT(3) DEFAULT '0',
  `priority` TINYINT(1) NOT NULL,
  `priority_note` VARCHAR(200) NOT NULL,
  `note` TEXT NOT NULL,
  `offline` TINYINT(1) NOT NULL,
  `status_backup` TINYINT(1) DEFAULT NULL,
  `trans_created_by` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `transid` (`transid`),
  KEY `franchise_id` (`franchise_id`),
  KEY `trans_created_by` (`trans_created_by`)
);

CREATE TABLE `user_order_margin_track` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transid` VARCHAR(20) NOT NULL,
  `itemid` BIGINT(20) UNSIGNED NOT NULL,
  `mrp` DECIMAL(10,2) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `base_margin` DECIMAL(10,2) NOT NULL,
  `sch_margin` DECIMAL(10,2) NOT NULL,
  `bal_discount` DOUBLE DEFAULT NULL,
  `qty` INT(10) UNSIGNED NOT NULL,
  `final_price` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transid` (`transid`)
);

CREATE TABLE `king_user_orders` (
  `sno` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` BIGINT(20) UNSIGNED NOT NULL,
  `transid` CHAR(18) NOT NULL,
  `userid` INT(11) UNSIGNED NOT NULL,
  `itemid` BIGINT(20) UNSIGNED NOT NULL,
  `brandid` BIGINT(20) UNSIGNED NOT NULL,
  `vendorid` BIGINT(20) UNSIGNED NOT NULL,
  `bill_person` VARCHAR(100) NOT NULL,
  `bill_address` TEXT NOT NULL,
  `bill_city` TEXT NOT NULL,
  `bill_pincode` VARCHAR(20) NOT NULL,
  `ship_person` VARCHAR(100) NOT NULL,
  `ship_address` TEXT NOT NULL,
  `ship_city` TEXT NOT NULL,
  `ship_pincode` VARCHAR(20) NOT NULL,
  `bill_phone` VARCHAR(50) NOT NULL,
  `ship_phone` VARCHAR(50) NOT NULL,
  `bill_state` VARCHAR(100) NOT NULL,
  `ship_state` VARCHAR(100) NOT NULL,
  `ship_email` VARCHAR(150) NOT NULL,
  `bill_email` VARCHAR(150) NOT NULL,
  `quantity` INT(10) UNSIGNED NOT NULL,
  `paid` INT(10) UNSIGNED NOT NULL,
  `mode` TINYINT(3) UNSIGNED NOT NULL COMMENT '0 - PG (cc,netbanking), 1 - cod',
  `status` TINYINT(4) NOT NULL DEFAULT '0',
  `admin_order_status` TINYINT(3) DEFAULT '0',
  `shipped` TINYINT(1) NOT NULL,
  `buyer_options` TEXT NOT NULL,
  `time` BIGINT(20) NOT NULL,
  `actiontime` BIGINT(20) UNSIGNED NOT NULL,
  `shiptime` BIGINT(20) UNSIGNED NOT NULL,
  `shipid` VARCHAR(50) NOT NULL,
  `medium` VARCHAR(100) NOT NULL,
  `bpid` BIGINT(20) UNSIGNED NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `ship_landmark` TEXT NOT NULL,
  `bill_landmark` TEXT NOT NULL,
  `ship_telephone` VARCHAR(50) NOT NULL,
  `ship_country` VARCHAR(255) DEFAULT NULL,
  `bill_country` VARCHAR(255) DEFAULT NULL,
  `bill_telephone` VARCHAR(50) NOT NULL,
  `invoice_no` BIGINT(20) UNSIGNED NOT NULL,
  `priority` TINYINT(1) NOT NULL,
  `priority_note` VARCHAR(200) NOT NULL,
  `note` TEXT NOT NULL,
  `billon_orderprice` TINYINT(1) DEFAULT '0',
  `i_orgprice` DOUBLE DEFAULT '0',
  `i_price` DOUBLE DEFAULT '0',
  `i_nlc` DOUBLE DEFAULT '0',
  `i_phc` DOUBLE DEFAULT '0',
  `i_tax` DOUBLE DEFAULT '0',
  `i_discount` DOUBLE DEFAULT '0',
  `i_coup_discount` DOUBLE DEFAULT '0',
  `member_id` BIGINT(11) DEFAULT '0',
  PRIMARY KEY (`sno`),
  KEY `transid` (`transid`),
  KEY `itemid` (`itemid`),
  KEY `userid` (`userid`),
  KEY `id` (`id`),
  KEY `status` (`status`)
); ENGINE=MYISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

SELECT * FROM pnh_member_info WHERE mobile='9611748183';

ALTER TABLE `pnh_member_offers` ADD COLUMN `mem_fee_applicable` TINYINT(11) DEFAULT NULL AFTER `offer_type`;

SELECT * FROM pnh_member_offers;
#=====================================================
ALTER TABLE `pnh_member_offers` ADD COLUMN `feedback_value` INT(50) NULL COMMENT 'customer feedback value' AFTER `feedback_status`;
#=====================================================

SELECT COUNT(*) AS t FROM pnh_member_info WHERE CONCAT('0',mobile) ='5646456456';

SELECT COUNT(*) AS t FROM pnh_member_info WHERE CONCAT('0',mobile)= '5646456456';

-- new -- 
SELECT GROUP_CONCAT('\'',mo.sno,'\'') AS snos 
FROM pnh_member_offers mo
JOIN pnh_member_info mi ON mi.pnh_member_id = mo.member_id
WHERE mo.feedback_status=0 AND mo.delivery_status=1 AND mi.mobile='5646456456'; 

-- new-- 
UPDATE pnh_member_offers SET feedback_status = 1,feedback_value='4' WHERE sno IN ('25');

SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status,f.franchise_name,i.invoice_no FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.insurance_id
                        JOIN king_admin a ON a.id = mi.created_by
                        JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
                        JOIN king_invoice i ON i.`transid` = mf.`transid_ref` 
                        WHERE mi.insurance_id = '691395657133';
                        

#=================================================
# Mar_26_2014
SELECT * FROM pnh_member_offers;

#=================================================
UPDATE pnh_t_receipt_info SET unreconciled_value = receipt_amount,unreconciled_status = 'pending' WHERE receipt_amount !=0 AND unreconciled_value IS NULL;
UPDATE pnh_franchise_account_summary SET unreconciled_value = IF(credit_amt=0,debit_amt,credit_amt) WHERE unreconciled_status <> 'done' AND unreconciled_value = 0;
#=================================================
#

SELECT g.short_name AS dest_shortname,IFNULL(e.role_name,'Other') AS role_type,b.name,a.id,a.is_printed,a.sent_invoices,a.manifesto_id,a.remark,
						c.name AS driver_name,a.hndleby_name,a.sent_on,d.name AS sent_by,c.contact_no,a.hndlby_roleid,a.hndleby_contactno,
						a.hndleby_empid,f.name AS pick_up_by,f.contact_no AS pick_up_by_contact,a.status,a.bus_id,a.bus_destination,
						a.hndleby_vehicle_num,a.start_meter_rate,a.amount,a.office_pickup_empid,a.pickup_empid,c.job_title2,a.lrno,a.hndlby_type,h.courier_name,a.hndleby_courier_id,a.modified_on,a.modified_by
				FROM pnh_m_manifesto_sent_log a
				JOIN pnh_manifesto_log b ON a.manifesto_id=b.id
				LEFT JOIN m_employee_info c ON a.hndleby_empid=c.employee_id
				LEFT JOIN king_admin d ON a.created_by=d.id
				LEFT JOIN m_employee_roles e ON a.hndlby_roleid = e.role_id
				LEFT JOIN m_employee_info f ON f.employee_id=a.pickup_empid
				LEFT JOIN pnh_transporter_dest_address g ON g.id = a.bus_destination
				LEFT JOIN m_courier_info h ON h.courier_id = a.hndleby_courier_id
				WHERE 1;
# Mar_27_2014		

SELECT * FROM  pnh_member_offers 
WHERE transid_ref='PNH53444'
member_id = '22017216';

SELECT r.*,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by 
WHERE r.status=0 AND r.is_active=1 AND DATE(FROM_UNIXTIME(instrument_date)) <= CURDATE()  AND is_submitted=0 AND f.franchise_id=? ORDER BY instrument_date ASC;

SELECT * FROM `snapittoday_db_jan_2014`.`pnh_t_receipt_info` WHERE franchise_id='59';

SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by,b.bank_name AS submit_bankname,c.submitted_on,s.name AS submitted_by,c.remarks AS submittedremarks
					FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
					LEFT JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id
					LEFT OUTER JOIN king_admin a ON a.id=r.created_by 
					LEFT OUTER JOIN king_admin d ON d.id=r.activated_by 
					LEFT JOIN `pnh_m_bank_info` b ON b.id=c.bank_id
					LEFT JOIN king_admin s ON s.id=c.submitted_by
					WHERE r.status=1 AND r.is_active=1 AND f.is_suspended=0 AND (r.is_submitted=1 OR r.activated_on!=0) AND r.is_active=1 
					ORDER BY activated_on DESC;
					
#============================================================================
ALTER TABLE `pnh_m_deposited_receipts` ADD INDEX (`receipt_id`);
#============================================================================

SELECT * FROM `pnh_m_deposited_receipts` WHERE receipt_id='5454';

-- OLD-- old

SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by ,c.cancel_reason,c.cancelled_on,c.cheq_cancelled_on,c.cancel_status,
b.bank_name AS submit_bankname,c.submitted_on,s.name AS submitted_by,c.remarks AS submittedremarks,m.name AS reversed_by 
FROM pnh_t_receipt_info r
 JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
 JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id 
 LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by 
 LEFT JOIN `pnh_m_bank_info` b ON b.id=c.bank_id LEFT JOIN king_admin s ON s.id=c.submitted_by 
 LEFT JOIN king_admin m ON m.id=r.modified_by WHERE r.status IN (2,3) AND r.is_active=1 
 GROUP BY r.receipt_id ORDER BY activated_on DESC;
 
 SELECT a.*,b.user_id,b.first_name,f.*,DATE(FROM_UNIXTIME(a.created_on)) AS DATE FROM pnh_member_offers a 
 JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
 JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id WHERE a.offer_type=3 ORDER BY a.created_on DESC LIMIT 100
 
 
 SELECT a.*,b.user_id,b.first_name,f.franchise_name, DATE(FROM_UNIXTIME(a.created_on)) AS `date` FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
 JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id  WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 100;
 
 #Mar_28_2014
 
--  new 
 SELECT SUM(r.receipt_amount) AS ttl_receipts_val FROM pnh_t_receipt_info r
                        JOIN pnh_m_franchise_info f ON f.franchise_id = r.franchise_id
                        WHERE r.receipt_amount != 0 AND r.unreconciled_value > 0 AND r.status IN (0,1) AND f.territory_id = '16' 
                        ORDER BY r.created_on DESC
                        
-- new
SELECT * FROM pnh_t_receipt_info 
WHERE receipt_amount != 0 AND unreconciled_value > 0 AND franchise_id = ? AND STATUS IN (0,1) AND receipt_type != 0 ORDER BY created_on DESC

SELECT r.*,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r
LEFT OUTER JOIN king_admin a ON a.id=r.created_by 
JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
WHERE r.receipt_amount != 0 AND r.unreconciled_value > 0 #and r.franchise_id = ? 
AND r.status IN (0,1) AND r.receipt_type != 0 ORDER BY r.created_on DESC;

SELECT FROM_UNIXTIME(1394562600);
SELECT FROM_UNIXTIME(1394616906);
SELECT UNIX_TIMESTAMP("2014-03-12 15:05:06");

-- new-- 
SELECT r.*,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r 
LEFT OUTER JOIN king_admin a ON a.id=r.created_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
WHERE r.receipt_amount != 0 AND r.unreconciled_value > 0 AND r.status IN (0,1) AND r.receipt_type != 0 AND FROM_UNIXTIME(r.instrument_date) BETWEEN '1395513000' AND '1396031399'
 ORDER BY r.created_on DESC;

Mar_29_2014

/*[11:18:32 AM][46 ms]*/ INSERT INTO `user_access_roles`(`id`,`user_role`,`const_name`,`value`)VALUES(NULL,'ORDER_CONFIRMATION',NULL,'0'); 
/*[11:18:49 AM][37 ms]*/ INSERT INTO `user_access_roles`(`id`,`user_role`,`const_name`,`value`)VALUES(NULL,'ORDER_CONFIRMATION','ORDER_CONFIRMATION','0'); 
/*[11:19:17 AM][29 ms]*/ UPDATE `user_access_roles` SET `value`='68719476736' WHERE `id`='37'; 

/*[2:49:13 PM][63 ms]*/ ALTER TABLE `king_dealitems` CHANGE `has_insurance` `has_insurance` TINYINT(1) DEFAULT 0 NULL; 
/*[3:19:55 PM][3392 ms]*/ ALTER TABLE `king_dealitems` CHANGE `has_insurance` `has_insurance` TINYINT(1) DEFAULT 0 NOT NULL;

SELECT * FROM pnh_member_offers;
SELECT * FROM king_orders;

SELECT * FROM pnh_member_info m WHERE m.mobile=''; pnh_member_id='21111111';

REPAIR TABLE pnh_sms_log