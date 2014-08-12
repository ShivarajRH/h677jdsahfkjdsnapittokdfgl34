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


SELECT DISTINCT week_day,shipped,invoice_no,shipped_by FROM (
 SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,sd.shipped,sd.invoice_no,sd.shipped_by
 FROM shipment_batch_process_invoice_link sd
 JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
 JOIN king_transactions tr ON tr.transid = pi.transid
 JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 WHERE shipped=1 AND f.territory_id ='3' AND UNIX_TIMESTAMP(sd.shipped_on) !=0 AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800
 ORDER BY shipped_on DESC
 ) AS g WHERE g.week_day IS NOT NULL

# Dec_20_2013

SELECT week_day,shipped_on,shipped,invoice_no,shipped_by FROM (
 SELECT DATE_FORMAT(sd.shipped_on,'%w') AS week_day,sd.shipped_on,sd.shipped,sd.invoice_no,sd.shipped_by
 FROM shipment_batch_process_invoice_link sd
 JOIN proforma_invoices PI ON pi.p_invoice_no = sd.p_invoice_no AND pi.invoice_status = 1 
 JOIN king_transactions tr ON tr.transid = pi.transid
 JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
 WHERE shipped=1 AND sd.shipped_by>0 AND UNIX_TIMESTAMP(sd.shipped_on)!=0 AND f.territory_id ='3' AND UNIX_TIMESTAMP(sd.shipped_on) BETWEEN 1383244200 AND 1387477800
 ORDER BY shipped_on DESC
 ) AS g WHERE g.week_day IS NOT NULL


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
UPDATE pnh_franchise_account_summary SET unreconciled_value = receipt_amount;


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
UPDATE pnh_franchise_account_summary SET unreconciled_value = receipt_amount;

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

REPAIR TABLE pnh_sms_log;

SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status,f.franchise_name,i.invoice_no FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.insurance_id
                        JOIN king_admin a ON a.id = mi.created_by
                        JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
                        JOIN king_invoice i ON i.`transid` = mf.`transid_ref` 
                        WHERE mi.insurance_id = '4685349726';
                        
                       
SELECT *,FROM_UNIXTIME(actiontime) FROM king_transactions ORDER BY actiontime DESC;


SELECT const_name,VALUE FROM user_access_roles ORDER BY id ASC;

# Apr_04_2014
====================================
ALTER TABLE `pnh_member_insurance` ADD COLUMN `mem_receipt_no` BIGINT NULL AFTER `pincode`,
 ADD COLUMN `mem_receipt_date` DATE NULL AFTER `mem_receipt_no`,
  ADD COLUMN `mem_receipt_amount` DOUBLE NULL AFTER `mem_receipt_date`, CHANGE `created_by` `created_by` TINYINT(20) NULL,
   CHANGE `created_on` `created_on` DATETIME NULL, ADD COLUMN `modified_by` TINYINT(20) NULL AFTER `created_on`,
    ADD COLUMN `modified_on` DATETIME NULL AFTER `modified_by`;

ALTER TABLE `pnh_member_offers` CHANGE `created_on` `created_on` DATETIME NULL, ADD COLUMN `modified_by` TINYINT(20) NULL AFTER `created_on`, ADD COLUMN `modified_on` DATETIME NULL AFTER `modified_by`;
ALTER TABLE `pnh_member_offers_log` ADD COLUMN `created_on` DATETIME NULL AFTER `status`, ADD COLUMN `created_by` TINYINT(20) NULL AFTER `created_on`;
ALTER TABLE `pnh_member_insurance` ADD INDEX (`fid`, `mid`, `opted_insurance`, `offer_status`, `itemid`, `mob_no`);
ALTER TABLE `pnh_member_offers` ADD INDEX (`member_id`, `franchise_id`, `offer_type`, `pnh_pid`, `transid_ref`, `insurance_id`, `process_status`);
ALTER TABLE `pnh_member_insurance_menu` ADD INDEX (`menu_id`, `greater_than`, `less_than`, `is_active`);
#ALTER TABLE `pnh_member_insurance` CHANGE `fran_receipt_no` `mem_receipt_no` BIGINT(20) NULL, CHANGE `fran_receipt_date` `mem_receipt_date` DATE NULL, CHANGE `fran_receipt_amount` `mem_receipt_amount` DOUBLE NULL; 
====================================

SELECT COUNT(*) AS t FROM pnh_member_insurance WHERE ( proof_id= '' OR proof_type='' OR proof_address=''
                                                                            OR first_name='' OR city='' OR fran_receipt_no='' OR fran_receipt_date='' OR fran_receipt_amount='') AND sno='391395830970';

UPDATE pnh_member_offers SET insurance_id=18 WHERE insurance_id= (SELECT insurance_id FROM pnh_member_insurance);

SELECT mi.* FROM pnh_member_insurance mi WHERE mi.sno = 10;

# Apr_05_2014

SELECT mi.* FROM pnh_member_insurance mi 
                    LEFT JOIN insurance_m_types mit ON
                    WHERE mi.sno = '';

SELECT * FROM t_imei_no WHERE order_id=''


UPDATE pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.insurance_id = mo.insurance_id
SET mo.insurance_id=mi.sno;

#WHERE mo.insurance_id

# =========================================================================
UPDATE pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.insurance_id = mo.insurance_id
SET mo.insurance_id=mi.sno;
# =========================================================================

#Apr_07_2014

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,a.created_on AS DATE 
                FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id 
                JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 100;


# =================================< Order_id Alter statements START >========================================
#order_id field added
ALTER TABLE `pnh_member_insurance` ADD COLUMN `order_id` BIGINT NULL AFTER `itemid`;
ALTER TABLE `pnh_member_offers` ADD COLUMN `order_id` BIGINT NULL AFTER `pnh_pid`;

# To insert order id in member offers table
UPDATE pnh_member_offers mo 
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
SET mo.`order_id` = o.`id`;

# To insert order id in member insurance table
UPDATE pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.sno = mo.`insurance_id`
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
SET mi.`order_id` = o.`id`;
# =================================< Order_id Alter statements END >========================================

SELECT di.name AS dealname,di.pnh_id,d.menuid,mn.name AS menuname,d.brandid,b.name AS brandname,d.catid,c.name AS catname FROM king_dealitems di
                                                                                            JOIN king_deals d ON d.dealid=di.dealid
                                                                                            JOIN pnh_menu mn ON mn.id=d.menuid
                                                                                            JOIN king_brands b ON b.id = d.brandid
                                                                                            JOIN king_categories c ON c.id = d.catid
                                                                                            WHERE di.id='9632266674';
                                                                                            
SELECT o.id AS order_id,di.`name` AS dealname,o.transid,di.id AS itemid FROM pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
JOIN king_dealitems di ON di.pnh_id=mo.pnh_pid
JOIN king_orders o ON o.itemid = mi.itemid
 WHERE pnh_pid='19326978';
 
 
 SELECT * FROM pnh_member_offers mo WHERE mo.`pnh_pid` = '1371699';
  SELECT * FROM king_dealitems di WHERE `pnh_id` = '1371699'
  SELECT o.id AS order_id FROM king_orders o WHERE o.itemid = '3553368223' AND o.transid='PNH24587';
 
--  new 
SELECT o.id FROM pnh_member_offers mo 
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
WHERE mo.`pnh_pid` = '1371699';

-- new Update query -- 
# To insert order id in member offers table
UPDATE pnh_member_offers mo 
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
SET mo.`order_id` = o.`id`;

--  new 
SELECT o.id FROM pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.sno = mo.`insurance_id`
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
WHERE mo.`pnh_pid` = '1371699';


 # To insert order id in member insurance table
UPDATE pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.sno = mo.`insurance_id`
JOIN king_dealitems di ON `pnh_id` = mo.`pnh_pid`
JOIN king_orders o ON o.itemid = di.id AND o.transid=mo.`transid_ref`
SET mi.`order_id` = o.`id`;

-- old
SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                JOIN king_orders o ON o.id = mi.`order_id`
                JOIN king_dealitems di ON di.id = o.itemid
                WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 100;
                
SELECT di.* FROM king_orders o 
JOIN king_dealitems di ON di.id = o.itemid
WHERE o.id='6896123449';

# Roles
SELECT const_name,VALUE FROM user_access_roles ORDER BY id ASC;

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                            FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                            JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                            JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                            JOIN king_orders o ON o.id = mi.`order_id`
                            JOIN king_dealitems di ON di.id = o.itemid
                            WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 0,23;
                            
SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESCSELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC LIMIT 0,5;

# Apr_08_2014

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
#=> 18 rows
--     new insurance offers
SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
#=> 18 rows
-- new oder details
SELECT di.name AS dealname FROM king_orders o
JOIN king_dealitems di ON di.id = o.itemid
WHERE o.id ='7538721686';

SELECT di.pnh_id,di.name AS dealname FROM king_orders o JOIN king_dealitems di ON di.id = o.itemid WHERE o.id = '4782618815';

#============================================================
#get all insurance offers
SELECT * FROM pnh_member_offers WHERE offer_type=2;

# Get all recharge offers
SELECT * FROM pnh_member_offers WHERE offer_type=1;

# Get all opted insurance list
SELECT * FROM pnh_member_offers WHERE offer_type=0;

# Get all opted insurance list
SELECT * FROM pnh_member_insurance WHERE offer_type=0;
#============================================================
SELECT points FROM pnh_loyalty_points WHERE menu_id=? AND ?>=amount AND is_active=1  ORDER BY amount DESC LIMIT 1

SELECT points FROM pnh_loyalty_points WHERE menu_id='112' AND 16000 >= amount AND is_active=1 ORDER BY amount DESC LIMIT 1;

DROP TABLE IF EXISTS pnh_loyalty_points;
CREATE TABLE `pnh_loyalty_points` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` BIGINT(15) DEFAULT NULL,
  `amount` INT(10) UNSIGNED NOT NULL,
  `points` INT(10) UNSIGNED NOT NULL,
  `is_active` TINYINT(11) DEFAULT '1',
  `valid_days` BIGINT(11) DEFAULT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE `menu_id` BIGINT(15) DEFAULT NULL;


SELECT a.*,o.status,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
                                    
                                    SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE 
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
                                    
                                    
                                    SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE,o.status AS order_status FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id JOIN king_orders o ON o.id = mi.`order_id` JOIN king_dealitems di ON di.id = o.itemid WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC
                                    

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS DATE,o.status AS order_status,imei.imei_no,imei.is_imei_activated,o.imei_reimbursement_value_perunit
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
                                    
SELECT * FROM king_orders WHERE id='4338999511';

SELECT * FROM pnh_m_franchise_info

SELECT * FROM pnh_member_info WHERE pnh_member_id = '20001602'
SELECT * FROM pnh_member_info WHERE pnh_member_id = '22017143' 

SELECT * FROM pnh_member_offers WHERE member_id = '22017143';

SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status,f.franchise_name
#,o.id as orderid,i.invoice_no,imei.imei_no 
		FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.sno
                        JOIN king_admin a ON a.id = mi.created_by
                        LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
                        #JOIN king_orders o ON o.itemid = mi.itemid
                       # JOIN king_invoice i ON i.`transid` = mf.`transid_ref` 
                       # JOIN t_imei_no imei on imei.order_id = o.id
                        WHERE mi.sno =31
                        
SELECT * FROM pnh_member_insurance mi WHERE mi.sno=31;

#=====================================================================

ALTER TABLE `pnh_member_insurance` ADD COLUMN `status_ship` TINYINT(11) DEFAULT 0 NULL AFTER `mem_receipt_amount`, ADD COLUMN `status_deliver` TINYINT(11) DEFAULT 0 NULL AFTER `status_ship`;

#=====================================================================

SELECT insurance_id FROM pnh_member_insurance WHERE insurance_id = '33'

SELECT * FROM pnh_member_insurance WHERE `mid` = '22017143';

SELECT inv.invoice_no,a.id AS orderid,e.franchise_id,a.itemid,GROUP_CONCAT(b.name) AS itemname,CONCAT(b.print_name,'-',b.pnh_id) AS print_name,i_orgprice,login_mobile1,i_price,i_coup_discount,i_discount,GROUP_CONCAT(a.quantity) AS qty,c.menuid,a.transid,f.franchise_id,f.franchise_name,SUM( (inv.mrp - inv.`discount`)*inv.`invoice_qty`) AS invoice_value
							,mi.first_name,mi.pnh_member_id,mi.mobile
							FROM king_invoice inv
							JOIN king_orders a ON a.id=inv.order_id
							JOIN king_dealitems b ON a.itemid = b.id
							JOIN king_deals c ON b.dealid = c.dealid 
							JOIN pnh_menu d ON d.id = c.menuid 
							JOIN king_transactions e ON e.transid = a.transid
							JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
							LEFT JOIN pnh_member_info mi ON mi.user_id = a.userid
							WHERE inv.invoice_no IN (20141019685,20141019686)
							GROUP BY inv.invoice_no
							
							
SELECT a.invoice_no,b.transid FROM shipment_batch_process_invoice_link a
									JOIN king_invoice b ON b.invoice_no = a.invoice_no
									JOIN pnh_member_offers c ON c.transid_ref=b.transid
									WHERE DATE(a.delivered_on) BETWEEN '2014-04-01' AND '2014-04-09';
									
SELECT mo.transid_ref AS transid FROM pnh_member_offers mo WHERE DATE(mo.created_on) BETWEEN '2014-04-01' AND '2014-04-09'

# ====================< view roles table >======================
SELECT const_name,VALUE FROM user_access_roles ORDER BY id ASC;
=>CALLCENTER_ROLE FINANCE_ROLE ( accounts)
#===============================================================

SELECT * FROM king_invoice;

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS `date`,o.status AS order_status,imei.imei_no,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no
                                    FROM pnh_member_offers a JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = a.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    WHERE a.offer_type IN (0,2) ORDER BY a.created_on DESC;
                                    
-- modified            
			SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
			FROM pnh_member_offers mo
                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                            LEFT JOIN king_invoice i ON i.order_id = mo.`order_id`
                            WHERE mo.offer_type=1 ORDER BY mo.created_on DESC;
                            
-- modified

SELECT a.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,a.created_on AS `date`,i.invoice_no
FROM pnh_member_offers a 
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
JOIN pnh_m_franchise_info f ON f.franchise_id= a.franchise_id
LEFT JOIN king_invoice i ON i.order_id = a.`order_id`
WHERE a.offer_type=3 ORDER BY a.created_on DESC;

SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
			FROM pnh_member_offers mo
                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                            LEFT JOIN king_invoice i ON i.order_id = mo.`order_id`
                            WHERE mo.offer_type=3 ORDER BY mo.created_on DESC;
                            
SELECT * FROM `pnh_t_receipt_reconcilation_log`;
SELECT * FROM `pnh_t_receipt_reconcilation`;

-- new - get is unreconciled?
SELECT * FROM `pnh_t_receipt_reconcilation` rcon
JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
WHERE rcon.invoice_no='20141019683'  AND rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0

# Apr_10_2014
ALTER TABLE `king_orders` ADD COLUMN `pnh_member_fee` DOUBLE NULL AFTER `vendor_order_refe_id`; 

SELECT COUNT(*) AS t FROM pnh_member_info WHERE ( mobile='' ) AND pnh_member_id= '20001906'

SELECT * FROM pnh_member_info WHERE pnh_member_id='21111111' => 8147491272
SELECT * FROM pnh_member_insurance
SELECT * FROM pnh_member_offers


SELECT COUNT(*) AS t FROM pnh_member_insurance WHERE ( mob_no='' ) AND sno=

SELECT * FROM pnh_member_insurance_menu ORDER BY id ASC

SELECT inv.invoice_no,a.id AS orderid,e.franchise_id,a.itemid,GROUP_CONCAT(b.name) AS itemname,CONCAT(b.print_name,'-',b.pnh_id) AS print_name,i_orgprice,login_mobile1,i_price,i_coup_discount,i_discount,SUM(a.quantity) AS qty,c.menuid,a.transid,f.franchise_id,f.franchise_name,SUM( (inv.mrp - inv.`discount`)*inv.`invoice_qty`) AS invoice_value
							,mi.first_name,mi.pnh_member_id,mi.mobile
							FROM king_invoice inv
							JOIN king_orders a ON a.id=inv.order_id
							JOIN king_dealitems b ON a.itemid = b.id
							JOIN king_deals c ON b.dealid = c.dealid 
							JOIN pnh_menu d ON d.id = c.menuid 
							JOIN king_transactions e ON e.transid = a.transid
							JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
							LEFT JOIN pnh_member_info mi ON mi.user_id = a.userid
							WHERE inv.invoice_no IN ('20141000312')
							GROUP BY inv.invoice_no;
							
							
# Apr_11_2014
# recharge offers
SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            WHERE mo.offer_type=1 GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC

 =>37 => 11

# reconcilation status
SELECT COUNT(*) AS t FROM `pnh_t_receipt_reconcilation` rcon
    JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
    WHERE rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0 AND rcon.invoice_no='2014101966300' #20141019661 20141019663 20141019672 20141019678


SELECT inv.invoice_no,a.id AS orderid,e.franchise_id,a.itemid,GROUP_CONCAT(b.name) AS itemname,CONCAT(b.print_name,'-',b.pnh_id) AS print_name,i_orgprice,login_mobile1,i_price,i_coup_discount,i_discount,SUM(a.quantity) AS qty,c.menuid,a.transid,f.franchise_id,f.franchise_name,SUM( (inv.mrp - inv.`discount` - inv.`credit_note_amt`)*inv.`invoice_qty`) AS invoice_value,SUM(a.`pnh_member_fee`) AS pnh_member_fee
							,mi.first_name,mi.pnh_member_id,mi.mobile
							FROM king_invoice inv
							JOIN king_orders a ON a.id=inv.order_id
							JOIN king_dealitems b ON a.itemid = b.id
							JOIN king_deals c ON b.dealid = c.dealid 
							JOIN pnh_menu d ON d.id = c.menuid 
							JOIN king_transactions e ON e.transid = a.transid
							JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
							LEFT JOIN pnh_member_info mi ON mi.user_id = a.userid
							WHERE inv.invoice_no IN ('20141000318')
							GROUP BY inv.invoice_no;
							
# Apr_12_2014

SELECT * FROM pnh_member_info

SELECT b.transid
																	FROM shipment_batch_process_invoice_link a
																	JOIN king_invoice b ON a.invoice_no = b.invoice_no
																	JOIN king_orders o ON o.transid=b.transid AND o.status=2
																	WHERE a.inv_manifesto_id = '2724'
																	GROUP BY o.transid
																	

SELECT userid,member_id,transid,SUM(loyality_point_value) AS loyality_point_value FROM king_orders WHERE transid='PNHDCF76262' AND STATUS=1
PNHDCF76262
PNHFFA56337

                       

#=====================================================================
# Apr_12_2014
ALTER TABLE `king_orders` ADD COLUMN `loyality_point_value` DOUBLE NULL AFTER `pnh_member_fee`;
ALTER TABLE `pnh_member_offers` ADD COLUMN `pnh_member_fee` FLOAT(25) NULL AFTER `mem_fee_applicable`; 
#=====================================================================


SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status
,o.id AS orderid,f.franchise_name
,i.invoice_no ,imei.imei_no
			FROM pnh_member_insurance mi
                        JOIN pnh_member_offers mf ON mf.insurance_id = mi.sno
                        JOIN king_admin a ON a.id = mi.created_by
                        JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
                        JOIN king_orders o ON o.id = mi.order_id AND o.status != 3
                        JOIN king_invoice i ON i.order_id = mi.order_id 
                        JOIN t_imei_no imei ON imei.order_id = o.id
                        WHERE mi.sno = '37';
                        # => 855
                        
SELECT * FROM king_orders WHERE transid = 'PNH91273'
#===========================================
# set member fee in offers table
UPDATE pnh_member_offers mo SET mo.pnh_member_fee = 50 WHERE mem_fee_applicable = 1
#===========================================

SELECT * FROM pnh_member_offers WHERE 

SELECT COUNT(DISTINCT(a.transid)) AS l FROM king_orders a
								  JOIN pnh_member_info b ON b.user_id=a.userid 							 
								WHERE b.pnh_member_id='22017180'  AND a.status NOT IN (3);
								

SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            WHERE mo.offer_type=1  AND mo.delivery_status = 0
                                            GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;
                                            
-- recharge
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,a.created_on AS `date`,o.status AS order_status,imei.imei_no,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    WHERE mo.offer_type IN (0,2) $cond
                                    ORDER BY mo.created_on DESC;
                                    
-- insurance
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.imei_no,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    WHERE mo.offer_type IN (0,2)  AND mo.delivery_status = 0
                                    ORDER BY mo.created_on DESC
                                    
                                    
===============================================
# Insurance export list
SELECT mo.`order_id`,i.`invoice_no`,i.`createdon` AS inv_date,mi.`mem_receipt_no`,mi.`mem_receipt_date`,mi.`first_name`,mi.`proof_address`,mi.`city`,mi.`pincode`,mi.`mob_no`,di.`name` AS product_name,b.name AS brand
	,c.name AS catname,di.`name` AS product_make,'' AS other,imei.imei_no,imei.imei_no AS serial_no,mi.`mem_receipt_amount` AS retailer_invoice_value,mo.`transid_ref` AS transid,ptype.name AS proof_given,mi.`proof_id`,mi.`proof_address` AS proof_details
                                    FROM pnh_member_offers mo 
                                    JOIN pnh_member_info m ON m.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    
                                    LEFT JOIN king_deals d ON d.dealid=di.dealid
                                    LEFT JOIN king_brands b ON b.id = d.brandid
                                    LEFT JOIN king_categories c ON c.id = d.catid
                                    LEFT JOIN pnh_menu mn ON mn.id=d.menuid
                                    
                                    LEFT JOIN insurance_m_types AS ptype ON ptype.id= mi.`proof_type`
                                    WHERE mo.offer_type IN (0,2)
                                    ORDER BY mo.created_on DESC;
# => 24 rows

Sl. NO	SK ORDER NO	SK Inv NO	SK Inv DATE	Retailer Inv No.	Retailer Inv DATE	NAME of the Customer	Address	AREA	Pin Code.	Ph. No.	Product NAME	Brand	Category NAME	Product NAME	other	IMEI No.	SERIAL No.	SIM No. 	Retailer Invoice VALUE	Trans Id	Proof Given	Proof Details

# Apr_15_2014

#===============================================
# Recharge export list
SELECT '' AS order_id,i.`invoice_no`,i.`createdon` AS inv_date,"" AS mem_receipt_no,'' AS mem_receipt_date,mi.first_name,'' AS proof_address,'' AS city,'' AS pincode,mi.mobile AS mobile,'' AS product_name,'' AS brand
	,'' AS catname,'' AS product_make,'' AS other,'' AS imei_no,'' AS serial_no,'' AS retailer_invoice_value,mo.`transid_ref` AS transid,'' AS proof_given,'' AS proof_id,'' AS proof_details
	#mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
    FROM pnh_member_offers mo
    JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
    LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
    LEFT JOIN king_orders o ON o.transid = mo.`transid_ref`
    LEFT JOIN king_dealitems di ON di.id = o.itemid
    WHERE mo.offer_type=1
    GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;
#===============================================   
# => 13 rows => 

SELECT * FROM pnh_member_info;`snapittoday_db_jan_2014`

#===============================================
#Apr_15_2014
CREATE TABLE `pnh_member_fee`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `member_id` INT(10) DEFAULT 0, `transid` VARCHAR(25), `invoice_no` BIGINT(11) DEFAULT 0, `amount` DOUBLE, `status` TINYINT(1) DEFAULT 0, `created_on` DATETIME, `created_by` INT(5) DEFAULT 0, `modified_on` DATETIME, `modified_by` INT(5) DEFAULT 0, PRIMARY KEY (`id`) );
#===============================================

SELECT * FROM `pnh_member_info` WHERE first_name LIKE '%shivaraj%';

SELECT * FROM pnh_franchise_account_summary
SELECT * FROM pnh_franchise_account_stat
SELECT * FROM king_invoice;

# ==============< Function to return Un-Reconciled invoices as json response >=======================
SELECT * FROM (SELECT DISTINCT i.invoice_no
                                ,rcon.unreconciled AS unreconciled
                                ,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2)
                                , MIN(rcon.unreconciled) ) AS inv_amount
				FROM king_invoice i
				JOIN king_transactions tr ON tr.transid=i.transid
				LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
				WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '415' 
				GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;

# Un-Reconcilation list
SELECT * FROM (SELECT DISTINCT i.invoice_no
                                ,rcon.unreconciled AS unreconciled
                                ,IF(rcon.unreconciled IS NULL, ROUND( ( fas.`debit_amt` - fas.`credit_amt` )  * invoice_qty , 2), MIN(rcon.unreconciled) ) AS inv_amount
                                ,IF(rcon.unreconciled IS NULL, ROUND( SUM( i.mrp - discount - credit_note_amt )  * invoice_qty , 2), MIN(rcon.unreconciled) ) AS inv_amount_old
				FROM king_invoice i
				JOIN king_transactions tr ON tr.transid=i.transid
				LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
				LEFT JOIN pnh_franchise_account_summary fas ON  fas.`invoice_no` = i.`invoice_no`
				WHERE i.invoice_status=1 AND tr.is_pnh=1 AND tr.franchise_id= '415' #and i.`invoice_no`='20141000340'
				GROUP BY i.invoice_no,i.transid ORDER BY i.invoice_no ASC) AS g WHERE g.inv_amount > 0;

# pnh_member_fee
SELECT member_id,IFNULL(SUM(pnh_member_fee),0) AS pnh_member_fee,transid FROM 
												(
													SELECT b.invoice_no,userid,member_id,pnh_member_fee,a.transid 
														FROM king_orders a 
														JOIN king_invoice b ON a.id = b.order_id 
														WHERE b.invoice_no = '20141019710'
													GROUP BY member_id 
												) AS h;
												
# Apr_16_2014

SELECT * FROM t_imei_no WHERE imei_no = '59076594645'

SELECT * FROM king_orders ORDER BY sno DESC LIMIT 100; WHERE transid='PNHDSF75424';

#================================================================================================
ALTER TABLE `king_transactions` ADD COLUMN `order_for` INT(11) DEFAULT 0 NOT NULL AFTER `credit_days`;
#================================================================================================

# Insert to member fee table
INSERT INTO pnh_member_fee (member_id,invoice_no,transid,amount,STATUS,created_on,created_by) (
SELECT member_id,a.transid,b.invoice_no,50,1,FROM_UNIXTIME(b.createdon),6
FROM king_orders a 
JOIN king_invoice b ON a.id = b.order_id 
WHERE pnh_member_fee > 0 AND invoice_status = 1 );

SELECT COUNT(*) AS t FROM pnh_member_insurance WHERE ( 
                                            proof_id= '' OR proof_type='' OR proof_address='' OR first_name='' OR mob_no='' OR city='' OR mem_receipt_no='' OR mem_receipt_date='' OR mem_receipt_amount=''
                                            OR proof_id IS NULL OR proof_type IS NULL OR proof_address IS NULL OR first_name IS NULL OR mob_no IS NULL OR city IS NULL OR mem_receipt_no IS NULL OR mem_receipt_date IS NULL OR mem_receipt_amount IS NULL
                                            ) AND sno='24';
                                            

# Apr_18_2014

/*[11:14:59 AM][2161 ms]*/ ALTER TABLE `king_orders` CHANGE `insurance_amount` `insurance_amount` DOUBLE(10,2) DEFAULT 0.00 NOT NULL, CHANGE `pnh_member_fee` `pnh_member_fee` DOUBLE(10,2) DEFAULT 0.00 NOT NULL; 

CREATE TABLE `non_sk_imei_insurance_orders` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `order_id` BIGINT(15) DEFAULT NULL,
  `transid` VARCHAR(255) DEFAULT NULL,
  `userid` BIGINT(11) DEFAULT NULL,
  `member_id` BIGINT(11) DEFAULT NULL,
  `nonsk_imei_no` VARCHAR(255) DEFAULT NULL,
  `model_no` VARCHAR(255) DEFAULT NULL,
  `model_value` DOUBLE(10,2) DEFAULT '0.00',
  `insurance_id` BIGINT(11) DEFAULT NULL,
  `insurance_amount` DOUBLE(10,2) NOT NULL DEFAULT '0.00',
  `pnh_member_fee` DOUBLE(10,2) NOT NULL DEFAULT '0.00',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

SELECT mi.sno,mo.`order_id`,i.`invoice_no`,FROM_UNIXTIME(i.`createdon`) AS inv_date,mi.`mem_receipt_no`,mi.`mem_receipt_date`,mi.`first_name`,mi.`proof_address`,mi.`city`,mi.`pincode`,mi.`mob_no`,di.`name` AS product_name,b.name AS brand
	,c.name AS catname,di.`name` AS product_make,'' AS other,imei.imei_no,'' AS serial_no,mi.`mem_receipt_amount` AS retailer_invoice_value,mo.`transid_ref` AS transid,ptype.name AS proof_given,mi.`proof_id` AS proof_details
                                    FROM pnh_member_offers mo 
                                    JOIN pnh_member_info m ON m.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    JOIN t_imei_no imei ON imei.order_id = o.id
                                    JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    JOIN king_deals d ON d.dealid=di.dealid
                                    JOIN king_brands b ON b.id = d.brandid
                                    JOIN king_categories c ON c.id = d.catid
                                    LEFT JOIN insurance_m_types AS ptype ON ptype.id= mi.`proof_type`
				    WHERE mo.offer_type IN (0,2) AND i.invoice_status = 1 
				    ORDER BY mo.created_on DESC;
				    
SELECT * FROM (
                                                    SELECT g.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NOT NULL, g.amount, MIN(rcon.unreconciled) ) AS inv_amount
                                                    FROM (
                                                            SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount
                                                            FROM pnh_franchise_account_summary fas
                                                            WHERE fas.franchise_id= '59' AND fas.status=1 AND fas.invoice_no= '20141019703'#'20141019703' #'20141019706'
                                                            GROUP BY fas.invoice_no 
                                                    ) AS g 
                                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = g.invoice_no
                                                    GROUP BY g.invoice_no
                                            ) AS final
                                            WHERE final.inv_amount > 0;


SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no='20141019703'
SELECT * FROM pnh_t_receipt_reconcilation_log WHERE reconcile_id='77'

SELECT * FROM king_invoice WHERE invoice_no='20141019703';

SELECT * FROM pnh_member_offers WHERE transid_ref='PNH61785';

#====================================================
SELECT * 
FROM pnh_member_offers mo
JOIN king_transactions tr ON tr.transid = mo.transid_ref
JOIN king_orders o ON o.transid = tr.transid
WHERE o.has_insurance=1;

#WHERE o.transid='PNH67818';

#====================================================
# Apr_18_2014
CREATE TABLE `pnh_member_insurance_printlog`( `id` BIGINT(11) NOT NULL AUTO_INCREMENT, `insurance_id` BIGINT(11), `printcount` INT(150) DEFAULT 0, `printed_by` TINYINT(11) DEFAULT 0, `last_printed_on` DATETIME, PRIMARY KEY (`id`) );
#====================================================
#,lpoints_valid_days
SELECT userid,member_id,loyality_point_value,lpoint_valid_days,transid FROM king_orders WHERE member_id='21111111' AND STATUS=2;

DESC king_orders

#====================================================
# Apr_19_2014
ALTER TABLE king_orders ADD COLUMN lpoints_valid_days DOUBLE AFTER loyality_point_value;
#====================================================


SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.imei_no,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,nonsk_imei_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    
                                    WHERE mo.offer_type IN (0,2) 
                                    ORDER BY mo.created_on DESC;
# => 35 

SELECT * FROM pnh_sms_log ORDER BY created_on DESC LIMIT 10;

SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            
                                            WHERE mo.offer_type=1 
                                            GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;
                                            
# Apr_21_2014

#===============================================

# NON SK IMEI Process DB Changes
CREATE TABLE `non_sk_imei_insurance_orders` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` BIGINT(11) DEFAULT NULL,
  `order_id` BIGINT(15) DEFAULT NULL,
  `transid` VARCHAR(255) DEFAULT NULL,
  `userid` BIGINT(11) DEFAULT NULL,
  `member_id` BIGINT(11) DEFAULT NULL,
  `nonsk_imei_no` VARCHAR(255) DEFAULT NULL,
  `model_no` VARCHAR(255) DEFAULT NULL,
  `model_value` DOUBLE(10,2) DEFAULT '0.00',
  `insurance_id` BIGINT(11) DEFAULT NULL,
  `insurance_amount` DOUBLE(10,2) NOT NULL DEFAULT '0.00',
  `pnh_member_fee` DOUBLE(10,2) NOT NULL DEFAULT '0.00',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `king_orders` CHANGE `insurance_amount` `insurance_amount` DOUBLE(10,2) DEFAULT 0.00 NOT NULL, CHANGE `pnh_member_fee` `pnh_member_fee` DOUBLE(10,2) DEFAULT 0.00 NOT NULL; 


SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status,o.id AS orderid,f.franchise_name,i.invoice_no,imei.imei_no,nonsk_imei.nonsk_imei_no
                                                FROM pnh_member_insurance mi
                                                JOIN pnh_member_offers mf ON mf.insurance_id = mi.sno
                                                JOIN king_admin a ON a.id = mi.created_by
                                                JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
                                                JOIN king_orders o ON o.id = mi.order_id AND o.status != 3
                                                LEFT JOIN king_invoice i ON i.order_id = mi.order_id 
                                                LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                                LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id=o.id
                                                WHERE mi.sno = '4'
                                                
SELECT mi.*,a.username,mf.transid_ref AS transid,mf.process_status,mf.delivery_status,o.id AS orderid,f.franchise_name,i.invoice_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
		FROM pnh_member_insurance mi
		JOIN pnh_member_offers mf ON mf.insurance_id = mi.sno
		JOIN king_admin a ON a.id = mi.created_by
		JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
		JOIN king_orders o ON o.id = mi.order_id AND o.status != 3
		JOIN king_invoice i ON i.order_id = mi.order_id 
		LEFT JOIN t_imei_no imei ON imei.order_id = o.id 
		LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id=o.id
		WHERE mi.sno = '53';
		
SELECT u.name AS USER,t.*,a.name AS assignedto FROM support_tickets t LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to LEFT OUTER JOIN king_users u ON u.userid=t.user_id;

SELECT user_id,pnh_member_id,franchise_id,first_name,mobile FROM pnh_member_info WHERE user_id = '58984';


SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                    WHERE mo.offer_type IN (0,2)  AND rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0 
                                    ORDER BY mo.created_on DESC
                                    
SELECT mo.transid_ref AS transid FROM pnh_member_offers mo WHERE DATE(mo.created_on) BETWEEN '2014-03-01' AND '2014-04-21';

SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    
                                    WHERE mo.offer_type IN (0,2)  AND DATE(mo.created_on) BETWEEN 0 AND 2014-04-21 24:59:59 
                                    ORDER BY mo.created_on DESC;
                                    
=====================
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    
                                    WHERE mo.offer_type IN (0,2) AND (mo.created_on) BETWEEN "2014-04-18 00:00:10" AND "2014-04-18 23:59:59" 
                                    ORDER BY mo.created_on DESC;
                                    
SELECT mo.*,mi.user_id,mi.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.order_id = mo.`order_id`
                                            
                                            WHERE mo.offer_type=3  AND mo.delivery_status = 0 AND (mo.created_on) BETWEEN "2014-04-20" AND "2014-04-22 23:59:59" 
                                            ORDER BY mo.created_on DESC;
                                            
                                            
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
,rcon.invoice_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                    WHERE mo.offer_type IN (0,2) AND rlog.reconcile_id IS NULL  AND mo.delivery_status = 1 AND mo.feedback_status = 1 # AND rlog.is_reversed != 0 AND rcon.is_invoice_cancelled != 0 AND rcon.unreconciled = 0 
                                    ORDER BY mo.created_on DESC;
                                    
                                    
SELECT COUNT(*) AS t FROM `pnh_t_receipt_reconcilation` rcon
    JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
    WHERE rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0 AND rcon.invoice_no= '20141019701';
    
    
    SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    
                                    WHERE mo.offer_type IN (0,2) 
                                    ORDER BY mo.created_on DESC;
                                    
 # Apr_22_2014
                                     
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
,minfo.mobile,minfo.first_name
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    
                                    WHERE mo.offer_type IN (0,2)  AND mo.delivery_status = 1 AND mo.feedback_status = 0  AND ( minfo.mobile NOT IN (NULL,0,'') OR minfo.first_name NOT IN (NULL,0,'') )
                                    ORDER BY mo.created_on DESC
                                    
                                    
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
,minfo.mobile,minfo.first_name
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    
                                    WHERE mo.offer_type IN (0,2)  AND mo.delivery_status = 1 AND mo.feedback_status = 0 # AND ( minfo.mobile NOT IN (NULL,"") OR minfo.first_name NOT IN (NULL,"") ) 
                                    ORDER BY mo.created_on DESC
                                    
SELECT mo.*,b.user_id,b.first_name,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
,minfo.mobile,minfo.first_name
                                    FROM pnh_member_offers mo JOIN pnh_member_info b ON b.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    
                                    WHERE mo.offer_type IN (0,2)  AND mo.delivery_status = 1 AND mo.feedback_status = 0 AND ( minfo.mobile NOT IN ('NULL'," ",0) AND minfo.first_name NOT IN ('NULL'," ",0) ) 
                                    ORDER BY mo.created_on DESC;
                                    
#=====================================================
ALTER TABLE `pnh_member_offers` ADD COLUMN `remarks` VARCHAR(100) NULL AFTER `referred_status`;
#=====================================================

SELECT * FROM `pnh_member_offers` WHERE `member_id` = '22017197';

SELECT * FROM pnh_member_info WHERE pnh_member_id = '22017242'

#Apr_23_2014

SELECT pnh_member_id,mobile FROM pnh_member_info WHERE 
						#pnh_member_id = '20001415' AND not isnull(mobile)# is not null)
					#IF(mobile = '','',
					 IFNULL(mobile, 0) = 0 AND pnh_member_id = 20001415 #'21111111' #
					 
SELECT mi.*,minfo.franchise_id,mo.* FROM pnh_member_insurance mi 
                        LEFT JOIN insurance_m_types mit ON mit.id = mi.proof_type
                        JOIN pnh_member_offers mo ON mo.member_id = mi.mid
                        LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mi.mid
    			WHERE mi.sno = '53'
    			
SELECT * FROM pnh_member_insurance mi WHERE `mid`= '22017263' ORDER BY mi.sno DESC;

SELECT * FROM pnh_member_insurance mi WHERE `mid`= '21111111' ORDER BY mi.sno DESC;


/*[4:33:00 PM][2366 ms]*/ ALTER TABLE `king_orders` ADD COLUMN `has_nonsk_imei_insurance` TINYINT(11) DEFAULT 0 NOT NULL AFTER `pnh_member_fee`; 
#================================================================================================


# Apr_24_2014

CREATE TABLE `nonskimei_mem_fee_insu_amt` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `member_id` INT(10) DEFAULT '0',
  `transid` VARCHAR(25) DEFAULT NULL,
  `invoice_no` BIGINT(11) DEFAULT '0',
  `pnh_member_fee` DOUBLE DEFAULT NULL,
  `insurance_amt` DOUBLE DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(5) DEFAULT '0',
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(5) DEFAULT '0',
  PRIMARY KEY (`id`)
);

SELECT * FROM pnh_member_insurance mi WHERE `mid`= '21111111' ORDER BY mi.sno DESC

SELECT a.* FROM pnh_member_offers a 
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id WHERE member_id='21111111'  AND a.offer_type != 0

SELECT a.*,f.franchise_name FROM pnh_member_offers a 
JOIN pnh_member_info b ON b.pnh_member_id=a.member_id 
JOIN pnh_m_franchise_info f ON f.franchise_id=a.franchise_id
WHERE member_id='21111111'  AND a.offer_type != 0

-- old-- 
SELECT '' AS order_id,i.`invoice_no`,DATE(FROM_UNIXTIME(i.`createdon`)) AS inv_date,'' AS mem_receipt_no,'' AS mem_receipt_date,mi.first_name,'' AS proof_address,'' AS city,'' AS pincode,mi.mobile AS mobile,'' AS product_name,'' AS brand
                                    ,'' AS catname,'' AS product_make,'' AS other,'' AS imei_no,'' AS serial_no,'' AS retailer_invoice_value,mo.`transid_ref` AS transid,'' AS proof_given,'' AS proof_id,'' AS proof_details,
                                    delivery_status,feedback_status
                                FROM pnh_member_offers mo
                                JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                JOIN king_orders o ON o.transid = mo.`transid_ref`
                                JOIN king_dealitems di ON di.id = o.itemid
                                WHERE mo.offer_type=1 AND i.invoice_status = 1 
                                GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;
                                
--                                 new

SELECT '' AS order_id,i.`invoice_no`,DATE(FROM_UNIXTIME(i.`createdon`)) AS inv_date,'' AS mem_receipt_no,'' AS mem_receipt_date,mi.first_name,f.franchise_name,terr.`territory_name`,'' AS proof_address,'' AS city,'' AS pincode,mi.mobile AS mobile,'' AS product_name,'' AS brand
                                    ,'' AS catname,'' AS product_make,'' AS other,'' AS imei_no,'' AS serial_no,'' AS retailer_invoice_value,mo.`transid_ref` AS transid,'' AS proof_given,'' AS proof_id,'' AS proof_details,
                                    delivery_status,feedback_status
                                FROM pnh_member_offers mo
                                JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                JOIN king_orders o ON o.transid = mo.`transid_ref`
                                JOIN king_dealitems di ON di.id = o.itemid
                                JOIN pnh_m_territory_info terr ON terr.`id` = f.`territory_id`
                                
                                WHERE mo.offer_type=1 AND i.invoice_status = 1 
                                GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;
                                

SELECT territory_name,town_name,f.franchise_id,f.franchise_name,f.login_mobile1 AS franchise_mob,'' AS order_id,i.`invoice_no`,DATE(FROM_UNIXTIME(i.`createdon`)) AS inv_date,'' AS mem_receipt_no,'' AS mem_receipt_date,mi.first_name,'' AS proof_address,'' AS city,'' AS pincode,mi.mobile AS mobile,'' AS product_name,'' AS brand
                                    ,'' AS catname,'' AS product_make,'' AS other,'' AS imei_no,'' AS serial_no,'' AS retailer_invoice_value,mo.`transid_ref` AS transid,'' AS proof_given,'' AS proof_id,'' AS proof_details,
                                    delivery_status,feedback_status
                                FROM pnh_member_offers mo
                                JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id  
				JOIN pnh_towns tw ON tw.id = f.town_id  
				JOIN pnh_m_territory_info tr ON tr.id = tw.territory_id 
                                JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                JOIN king_orders o ON o.transid = mo.`transid_ref`
                                JOIN king_dealitems di ON di.id = o.itemid
                                WHERE mo.offer_type=1 AND i.invoice_status = 1;
                                
# ===
SELECT * FROM pnh_franchise_account_summary WHERE invoice_no='20141019672'

 
#======================== To GET TOTAL INVOICE AMOUNT ===============================================
 
    SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount
    FROM pnh_franchise_account_summary fas
    WHERE fas.status=1 AND fas.invoice_no= '20141019701'
    GROUP BY fas.invoice_no;
    
#======================== To GET TOTAL INVOICE AMOUNT ===============================================

INSERT INTO `m_config_params` (`name`, `value`) VALUES ('FRAN_TYPE_ELECTRONIC', '112,102');

#======================================================
# Apr_25_2014
INSERT INTO `m_config_params` (`name`, `value`) VALUES ('FRAN_TYPE_ELECTRONIC', '112,102');
#======================================================

SELECT mi.sno,mi.insurance_id,mi.fid,mi.mid,mi.offer_type,mi.proof_id,mi.proof_type,mi.proof_address,mi.opted_insurance,mi.offer_status,mi.insurance_value,mi.order_value,mi.itemid,mi.order_id
,IFNULL(mi.first_name,minfo.first_name) AS first_name
,IFNULL(mi.last_name,minfo.last_name) AS last_name
,IFNULL(mi.mob_no,minfo.mobile) AS mob_no
,IFNULL(mi.`city`,minfo.city) AS city
,mi.pincode,mi.mem_receipt_no,mi.mem_receipt_date,mi.mem_receipt_amount
,minfo.franchise_id,mo.* FROM pnh_member_insurance mi 
                                                    LEFT JOIN insurance_m_types mit ON mit.id = mi.proof_type
                                                    JOIN pnh_member_offers mo ON mo.member_id = mi.mid
                                                    LEFT JOIN pnh_member_info minfo ON minfo.pnh_member_id = mi.mid
                                                    WHERE mi.sno = '55';
                                                    
mi.sno,mi.insurance_id,mi.fid,mi.mid,mi.offer_type,mi.proof_id,mi.proof_type,mi.proof_address,mi.opted_insurance,mi.offer_status,mi.insurance_value,mi.order_value,mi.itemid,mi.order_id,mi.first_name
,mi.last_name,mi.mob_no,mi.`city`,mi.pincode,mi.mem_receipt_no,mi.mem_receipt_date,mi.mem_receipt_amount

#======================================================
# Apr_25_2014
ALTER TABLE `king_transactions` ADD COLUMN `pnh_member_fee` DOUBLE DEFAULT 0 NULL AFTER `order_for`;
#======================================================

SELECT * FROM king_transactions WHERE transid='PNH67818';
#================================================
##Apr_25_4_2014
#Update Member fee column in transacctions table
UPDATE king_transactions tr 
JOIN pnh_member_offers mo ON mo.transid_ref = tr.transid
SET tr.pnh_member_fee = 50
WHERE tr.order_for != 2
#================================================

SELECT tr.* FROM king_transactions tr 
JOIN pnh_member_offers mo ON mo.transid_ref = tr.transid
WHERE tr.order_for != 2
GROUP BY transid








#Apr_25_4_2014
ALTER TABLE `m_vendor_api_info` ADD COLUMN `file_format` ENUM ('CSV','XML','TXT') DEFAULT 'CSV' NULL  AFTER `type`, ADD COLUMN `mapping_tmpl_id` INT (11)  NULL  AFTER `file_format`;
CREATE TABLE `m_vendor_api_templates` (                  
                          `id` BIGINT(20) NOT NULL AUTO_INCREMENT,               
                          `product_name` VARCHAR(255) DEFAULT NULL,              
                          `sku_code` VARCHAR(255) DEFAULT NULL,                  
                          `product_code` VARCHAR(255) DEFAULT NULL,              
                          `model_code` VARCHAR(255) DEFAULT NULL,                
                          `qty` VARCHAR(255) DEFAULT NULL,                       
                          `short_description` VARCHAR(255) DEFAULT NULL,         
                          `category` VARCHAR(255) DEFAULT NULL,                  
                          `category1` VARCHAR(255) DEFAULT NULL,                 
                          `category2` VARCHAR(255) DEFAULT NULL,                 
                          `category3` VARCHAR(255) DEFAULT NULL,                 
                          `category4` VARCHAR(255) DEFAULT NULL,                 
                          `brand` VARCHAR(255) DEFAULT NULL,                     
                          `menu` VARCHAR(255) DEFAULT NULL,                      
                          `unit_of_measurement` VARCHAR(255) DEFAULT NULL,       
                          `mrp` VARCHAR(255) DEFAULT NULL,                       
                          `offer_price` VARCHAR(255) DEFAULT NULL,               
                          `vat` VARCHAR(255) DEFAULT NULL,                       
                          `purchase_cost` VARCHAR(255) DEFAULT NULL,             
                          `barcode` VARCHAR(255) DEFAULT NULL,                   
                          `group_id` VARCHAR(255) DEFAULT NULL,                  
                          `gender_attr` VARCHAR(255) DEFAULT NULL,               
                          `long_description` VARCHAR(255) DEFAULT NULL,          
                          `main_image` VARCHAR(255) DEFAULT NULL,                
                          `image1` VARCHAR(255) DEFAULT NULL,                    
                          `image2` VARCHAR(255) DEFAULT NULL,                    
                          `image3` VARCHAR(255) DEFAULT NULL,                    
                          `image4` VARCHAR(255) DEFAULT NULL,                    
                          `image5` VARCHAR(255) DEFAULT NULL,                    
                          `color` VARCHAR(255) DEFAULT NULL,                     
                          `size` VARCHAR(255) DEFAULT NULL,                      
                          `attr1` VARCHAR(255) DEFAULT NULL,                     
                          `attr2` VARCHAR(255) DEFAULT NULL,                     
                          `spec1` VARCHAR(255) DEFAULT NULL,                     
                          `spec2` VARCHAR(255) DEFAULT NULL,                     
                          `spec3` VARCHAR(255) DEFAULT NULL,                     
                          `spec4` VARCHAR(255) DEFAULT NULL,                     
                          `spec5` VARCHAR(255) DEFAULT NULL,                     
                          `spec6` VARCHAR(255) DEFAULT NULL,                     
                          `spec7` VARCHAR(255) DEFAULT NULL,                     
                          `spec8` VARCHAR(255) DEFAULT NULL,                     
                          `spec9` VARCHAR(255) DEFAULT NULL,                     
                          `spec10` VARCHAR(255) DEFAULT NULL,                    
                          `spec11` VARCHAR(255) DEFAULT NULL,                    
                          `spec12` VARCHAR(255) DEFAULT NULL,                    
                          `spec13` VARCHAR(255) DEFAULT NULL,                    
                          `spec14` VARCHAR(255) DEFAULT NULL,                    
                          `spec15` VARCHAR(255) DEFAULT NULL,                    
                          `spec16` VARCHAR(255) DEFAULT NULL,                    
                          `spec17` VARCHAR(255) DEFAULT NULL,                    
                          `spec18` VARCHAR(255) DEFAULT NULL,                    
                          `spec19` VARCHAR(255) DEFAULT NULL,                    
                          `spec20` VARCHAR(255) DEFAULT NULL,                    
                          `spec21` VARCHAR(255) DEFAULT NULL,                    
                          `spec22` VARCHAR(255) DEFAULT NULL,                    
                          `spec23` VARCHAR(255) DEFAULT NULL,                    
                          `spec24` VARCHAR(255) DEFAULT NULL,                    
                          `spec25` VARCHAR(255) DEFAULT NULL,                    
                          `url` VARCHAR(255) DEFAULT NULL,                       
                          PRIMARY KEY (`id`)                                     
                        ) ;
ALTER TABLE `t_vendor_product_import_log` CHANGE `group_id` `group_id` VARCHAR (255)  NULL 
ALTER TABLE `m_vendor_api_info` ADD COLUMN `default_brand` VARCHAR (255)  NULL  AFTER `vendor_id`, ADD COLUMN `default_menu` VARCHAR (255)  NULL  AFTER `default_brand`;                    
#==============================================================

#Apr_28_2014

SELECT franchise_name FROM pnh_m_franchise_info WHERE franchise_id='92';

SELECT * FROM pnh_member_fee

$keymember_id=20141019716
$normal_member_id=20141019691

SELECT franchise_id,a.transid,b.invoice_no,is_pnh,SUM((mrp-discount)*invoice_qty) AS inv_amt,a.order_for,a.pnh_member_fee
													FROM king_transactions a 
													JOIN king_invoice b ON a.transid = b.transid 
													JOIN king_orders c ON c.id = b.order_id 
													WHERE b.invoice_no = '20141019716'; #=> Keymember
#PNH67818 20141019716 28135.55
SELECT a.franchise_id,a.transid,b.invoice_no,a.is_pnh,SUM((b.mrp-b.discount)*b.invoice_qty) AS inv_amt,a.order_for,a.pnh_member_fee,IF(a.order_for!=2,mi.pnh_member_id,'') AS member_id
													FROM king_transactions a 
													JOIN king_invoice b ON a.transid = b.transid 
													JOIN king_orders c ON c.id = b.order_id 
													LEFT JOIN pnh_member_info mi ON mi.user_id = c.userid
													WHERE b.invoice_no = '20141019691'; #=> Keymember
#=>PNH67818 20141019716 28135.55 103554,103555,103556


SELECT member_id,IFNULL(SUM(pnh_member_fee),0) AS pnh_member_fee,transid FROM 
												(
													SELECT b.invoice_no,userid,member_id,pnh_member_fee,a.transid 
														FROM king_orders a 
														JOIN king_invoice b ON a.id = b.order_id 
														WHERE b.invoice_no = '20141019716'
													GROUP BY member_id 
												) AS h 
													GROUP BY member_id; 
#=>20141019691

===
SELECT * FROM pnh_member_fee WHERE STATUS=1 AND member_id= '22017222' AND transid= 'PNH11648';

SELECT i.invoice_status,
		DATE_FORMAT(FROM_UNIXTIME(o.time),'%d/%m/%Y') AS orderd_on
		,DATE_FORMAT(FROM_UNIXTIME(t.init),'%d/%m/%Y') AS init
		,t.amount
		,i.invoice_no,t.transid 
		FROM king_orders o  JOIN king_transactions t ON t.transid=o.transid JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  LEFT JOIN king_invoice i ON i.order_id=o.id  WHERE t.franchise_id='59'  		GROUP BY t.franchise_id  ORDER BY o.id DESC LIMIT 1;

SELECT * FROM user_access_roles;

#
SELECT insurance_value,insurance_margin FROM pnh_member_insurance_menu WHERE menu_id='112' AND '5000' BETWEEN greater_than AND less_than AND is_active=1  LIMIT 1

SELECT insurance_value,insurance_margin FROM pnh_member_insurance_menu
SELECT * FROM pnh_member_insurance_menu


SELECT batch_id FROM shipment_batch_process_invoice_link WHERE p_invoice_no = '116447' AND invoice_no = 0 
SELECT * FROM shipment_batch_process_invoice_link WHERE batch_id='5822'


SELECT IFNULL(SUM(s.available_qty),0) AS stock,p.*,s.expiry_on,b.name AS brand,c.name AS cat FROM m_product_info p LEFT OUTER JOIN t_stock_info s ON s.product_id=p.product_id JOIN king_brands b ON b.id=p.brand_id JOIN king_categories c ON c.id=p.product_cat_id WHERE p.product_id='4825';

SELECT * FROM t_stock_info WHERE product_id='4825';
SELECT * FROM m_product_info WHERE product_id='4825';
DESC king_brands;
DESC king_categories;

#============================================
ALTER TABLE t_stock_info ADD COLUMN expiry_on DATE AFTER mrp,ADD COLUMN offer_note DATE AFTER expiry_on;
ALTER TABLE m_product_info ADD COLUMN self_life INT DEFAULT NULL AFTER product_cat_id; 
#============================================



##Apr_29_2014

SELECT territory_name,town_name,f.franchise_id,f.franchise_name,f.login_mobile1 AS franchise_mob,'' AS order_id,i.`invoice_no`,DATE(FROM_UNIXTIME(i.`createdon`)) AS inv_date,'' AS mem_receipt_no,'' AS mem_receipt_date,mi.first_name,'' AS proof_address,'' AS city,'' AS pincode,mi.mobile AS mobile,'' AS product_name,'' AS brand
                                        ,'' AS catname,'' AS product_make,'' AS other,'' AS imei_no,'' AS serial_no,'' AS retailer_invoice_value,mo.`transid_ref` AS transid,'' AS proof_given,'' AS proof_id,'' AS proof_details,
                                        delivery_status,feedback_status
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id  
                                    JOIN pnh_towns tw ON tw.id = f.town_id  
                                    JOIN pnh_m_territory_info tr ON tr.id = tw.territory_id 
                                    JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                    JOIN king_orders o ON o.transid = mo.`transid_ref`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    WHERE mo.offer_type=1 AND i.invoice_status = 1 AND delivery_status=1 AND feedback_status=1 AND process_status=0
                                    GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC



DESC pnh_member_offers

SELECT pnh_member_id,mobile FROM pnh_member_info WHERE IFNULL(mobile, 0) = 0 AND pnh_member_id = '21111111'
SELECT * FROM pnh_member_info WHERE IFNULL(mobile, 0) = 0 AND pnh_member_id = '21111111';

SELECT * FROM (
                                                    SELECT g.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NOT NULL, g.amount, MIN(rcon.unreconciled) ) AS inv_amount
                                                    FROM (
                                                            SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount
                                                            FROM pnh_franchise_account_summary fas
                                                            WHERE fas.franchise_id= '59' AND fas.status=1 AND fas.invoice_no= '20141019703'#'20141019703' #'20141019706'
                                                            GROUP BY fas.invoice_no 
                                                    ) AS g 
                                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = g.invoice_no
                                                    GROUP BY g.invoice_no
                                            ) AS final
                                            WHERE final.inv_amount > 0;


SELECT DATE_FORMAT(FROM_UNIXTIME(init),'%d/%m/%Y') AS ordererd_on,o.status,o.transid,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount) + o.pnh_member_fee + o.insurance_amount)*o.quantity),2) AS order_total FROM king_transactions t JOIN king_orders o ON o.transid=t.transid AND o.status NOT IN(3) WHERE t.transid='PNH68547' #AND t.franchise_id='59' 
AND DATE(FROM_UNIXTIME(t.init))=CURDATE()


SELECT mo.*,mi.user_id
			,IFNULL(mi.first_name,minfo.first_name) AS first_name,IFNULL(mi.last_name,minfo.last_name) AS last_name
			,mi.mobile,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
			FROM pnh_member_offers mo
			JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
			JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
			LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
			
			WHERE mo.offer_type=1 
			GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;

SELECT * FROM (
                                                    SELECT g.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, g.amount, MIN(rcon.unreconciled) ) AS inv_amount
                                                    FROM (
                                                            SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount,fas.credit_amt
                                                            FROM pnh_franchise_account_summary fas
                                                            WHERE fas.franchise_id= ? AND fas.status=1 AND fas.type IN ()
                                                            GROUP BY fas.invoice_no 
                                                    ) AS g 
                                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = g.invoice_no
                                                    GROUP BY g.invoice_no
                                            ) AS final
                                            WHERE final.inv_amount > 0;

SELECT * FROM pnh_franchise_account_summary

SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount,fas.credit_amt
                                                            FROM pnh_franchise_account_summary fas
                                                            WHERE fas.franchise_id= '21' AND fas.status=1 AND fas.invoice_no='20141000411' #'20141000415'#AND fas.type in ()
                                                            GROUP BY fas.invoice_no 

SELECT 	`id`, `type`, `grp_no`, `franchise_id`, `invoice_no`, `amount`, `is_active`, `ref_id`, `created_on`, 
	`modified_on`, 
	`created_by`, 
	`modified_by`
	 
	FROM 
	`t_invoice_credit_notes` 
	LIMIT 0, 6;

SELECT SUM(debit_amt-credit_amt) AS amt FROM (
	SELECT credit_amt,debit_amt,b.type 
	FROM pnh_franchise_account_summary a  
	LEFT JOIN t_invoice_credit_notes b ON a.credit_note_id = b.id  
	WHERE a.invoice_no =  '20141000411';

SELECT * FROM pnh_franchise_account_summary fas 
LEFT JOIN t_invoice_credit_notes cn ON cn.id = fas.credit_note_id
WHERE fas.invoice_no='20141000411' AND fas.franchise_id= '21' AND cn.type IS NULL AND cn.type = 1;
	AND action_type IN (1,7) 
	HAVING (b.type IS NULL OR b.type = 1 )
	
) AS h;

SELECT * FROM pnh_franchise_account_summary fas 
LEFT JOIN t_invoice_credit_notes cn ON cn.id = fas.credit_note_id
WHERE fas.invoice_no='20141000411' AND fas.franchise_id= '21' #and cn.type is null and cn.type = 1;
	AND fas.action_type IN (1,7) 
	HAVING (cn.type IS NULL OR cn.type = 1 );


SELECT * FROM (
						SELECT g.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, g.amount, MIN(rcon.unreconciled) ) AS inv_amount
						FROM (
								SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount,fas.credit_amt
								FROM pnh_franchise_account_summary fas
								WHERE fas.franchise_id= ? AND fas.status=1 AND fas.type IN ()
								GROUP BY fas.invoice_no 
						) AS g 
						LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = g.invoice_no
						GROUP BY g.invoice_no
				) AS final
				WHERE final.inv_amount > 0;

SELECT SUM(debit_amt-credit_amt) AS amt FROM (
	SELECT credit_amt,debit_amt,b.type 
	FROM pnh_franchise_account_summary a  
	LEFT JOIN t_invoice_credit_notes b ON a.credit_note_id = b.id  
	WHERE a.invoice_no =  '20141000411' 
	AND action_type IN (1,7) 
	HAVING (b.type IS NULL OR b.type = 1 )
) AS h;

SELECT * FROM (
		SELECT g.invoice_no,rcon.unreconciled AS unreconciled,IF(rcon.unreconciled IS NULL, g.amount, MIN(rcon.unreconciled) ) AS inv_amount
		FROM (
				SELECT fas.invoice_no, SUM( fas.`debit_amt`-fas.credit_amt ) AS amount,fas.credit_amt
				FROM pnh_franchise_account_summary fas
				WHERE fas.franchise_id= '21' AND fas.status=1 #AND fas.type in ()
				GROUP BY fas.invoice_no 
		) AS g 
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = g.invoice_no
		GROUP BY g.invoice_no
) AS final
WHERE final.inv_amount > 0 AND final.invoice_no='20141000411';

SELECT * FROM pnh_member_fee WHERE transid='PNHWNX65364'

SELECT * FROM pnh_member_fee WHERE transid='PNH19673'
SELECT * FROM pnh_franchise_account_summary ORDER BY statement_id DESC LIMIT 10;


#May_01_2014

SELECT a.id,a.itemid,b.name AS itemname,CONCAT(b.print_name,'-',b.pnh_id) AS print_name,i_orgprice,login_mobile1,
							i_price,i_coup_discount,i_discount,a.quantity,c.menuid,a.transid,
							f.franchise_id,f.franchise_name,mi.user_id,mi.first_name,mi.mobile,mi.pnh_member_id   
							FROM king_orders a 
							JOIN king_dealitems b ON a.itemid = b.id 
							JOIN king_deals c ON b.dealid = c.dealid 
							JOIN pnh_menu d ON d.id = c.menuid 
							JOIN king_transactions e ON e.transid = a.transid
							JOIN pnh_member_info mi ON mi.user_id = a.userid
							JOIN pnh_m_franchise_info f ON f.franchise_id = e.franchise_id 
							WHERE a.transid = 'PNH19673';


SELECT o.*,ROUND(SUM((o.i_orgprice-(o.i_coup_discount+o.i_discount))*o.quantity),2) AS amt,GROUP_CONCAT(o.status) AS ostatus
#mo.*,mi.user_id,mi.first_name,mi.mobile,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no,group_concat(o.status) as ostatus
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN king_orders o ON o.transid = mo.transid_ref
                                            WHERE mo.offer_type=1 AND NOT FIND_IN_SET(o.status,2)
                                            GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC;

=>17

#============================================
#May_01_2014
ALTER TABLE `pnh_member_offers` ADD COLUMN `mob_serv_provider` VARCHAR (50)  NULL  AFTER `remarks`
#============================================

#May_02_2014

DESC pnh_member_info

SELECT mo.*,minfo.user_id
                                    ,IFNULL(mi.first_name,minfo.first_name) AS first_name
                                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated
                                    ,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,nonsk_imei.nonsk_imei_no,o.has_nonsk_imei_insurance
                                    ,IFNULL(rlog.reconcile_id,1) AS payment_status
                                    
,IF(mi.proof_id NOT IN ('NULL',' ',0) AND mi.proof_type NOT IN ('NULL',' ',0) AND mi.proof_address NOT IN ('NULL',' ',0) AND mi.first_name NOT IN ('NULL',' ',0) AND mi.mob_no NOT IN ('NULL',' ',0) 
AND mi.city NOT IN ('NULL',' ',0) AND mi.mem_receipt_no NOT IN ('NULL',' ',0) AND mi.mem_receipt_date NOT IN ('NULL',' ',0) AND mi.mem_receipt_amount NOT IN ('NULL',' ',0) , 0,1) AS details_status
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    LEFT JOIN king_orders o ON o.id = mi.`order_id`
                                    LEFT JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    
                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no LEFT JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id

                                    WHERE mo.offer_type IN (0,2) AND i.invoice_status = 1 
                                    ORDER BY mo.created_on DESC;


===========================================
SELECT * FROM 
(
	SELECT COUNT(DISTINCT mo.sno) AS total
	,IF(IFNULL(fas.unreconciled_value,0),fas.unreconciled_value > 0,1) AS pending_payment,fas.unreconciled_value
	,IF(mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
			OR mi.mem_receipt_amount IS NULL,0,1) AS details_status
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                    WHERE mo.offer_type IN (0,2)
) AS g

========================================================


SELECT * FROM (
					SELECT DISTINCT mo.*,minfo.user_id
                                    ,IFNULL(mi.first_name,minfo.first_name) AS first_name
                                    ,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated
                                    ,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,nonsk_imei.nonsk_imei_no,o.has_nonsk_imei_insurance
,IF(IFNULL(fas.unreconciled_value,0),fas.unreconciled_value > 0,1) AS pending_payment,fas.unreconciled_value
,IF(mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
		OR mi.mem_receipt_amount IS NULL,0,1) AS details_pending
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                    WHERE mo.offer_type IN (0,2) #and mi.mid='22017260'
									GROUP BY mo.sno
                                    ORDER BY mo.created_on DESC
) AS g

========================================================
DESC king_invoice
DESC pnh_t_receipt_reconcilation
DESC pnh_t_receipt_reconcilation_log
DESC pnh_franchise_account_summary

SELECT * FROM (
                                    SELECT COUNT(DISTINCT mo.sno) AS total
                                    ,IF(IFNULL(fas.unreconciled_value,0),fas.unreconciled_value > 0,1) AS pending_payment,fas.unreconciled_value
                                    ,IF(minfo.first_name OR minfo.mobile IS NULL OR minfo.mobile IS NULL OR mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
                                    OR mi.mem_receipt_amount IS NULL,0,1) AS details_status
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                    WHERE mo.offer_type IN (0,2) AND mo.process_status = 1
                                    ) AS g WHERE 1;


SELECT * FROM t_imei_no WHERE imei_no='357091053374276'


-- ======================================< TO RESET IMEI NO >=======================================
UPDATE t_imei_no SET STATUS=0 AND order_id=0 AND created_on=UNIX_TIMESTAMP(NOW()) WHERE imei_no = '357091053374276';
-- =============================================================================

SELECT msg,sender,franchise_id,FROM_UNIXTIME(created_on) AS created_on FROM pnh_sms_log;

====
SELECT pnh_member_id,mobile FROM pnh_member_info 
WHERE 
IFNULL(mobile, 0) AND 
pnh_member_id = '22017256';

SELECT pnh_member_id,mobile FROM pnh_member_info 
WHERE 
#length(mobile)=10 AND 
pnh_member_id = '22017256';

# APR_03_2014

SELECT * FROM pnh_member_insurance WHERE MID='22017260';


SELECT * FROM (
                                    SELECT COUNT(DISTINCT mo.sno) AS total
                                    ,IF(IFNULL(fas.unreconciled_value,0),fas.unreconciled_value > 0,1) AS pending_payment,fas.unreconciled_value
                                    ,IF(minfo.first_name OR minfo.mobile IS NULL OR minfo.mobile IS NULL OR mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
                                    OR mi.mem_receipt_amount IS NULL,0,1) AS details_pending
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                    WHERE mo.offer_type IN (0,2)  AND mo.delivery_status = 1 
                                    AND mo.feedback_status = 1 
                                    AND mo.process_status = 0 
                                    ) AS g WHERE 1  #AND g.pending_payment = 0 
#AND g.details_pending = 0 
==============================================
SELECT * FROM (
					SELECT DISTINCT mo.*,minfo.user_id
                                            ,IFNULL(mi.first_name,minfo.first_name) AS first_name
                                            ,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated
                                            ,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,nonsk_imei.nonsk_imei_no,o.has_nonsk_imei_insurance
                                            

,IF(fas.unreconciled_value = 0, 1,0) AS rr


                                            ,IF(mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL 
                                                OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL OR mi.mem_receipt_amount IS NULL,0,1) AS details_pending
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                            JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                            JOIN king_orders o ON o.id = mi.`order_id`
                                            JOIN king_dealitems di ON di.id = o.itemid
                                            LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                            LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                            LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                            LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                            WHERE mo.offer_type IN (0,2)
                                            GROUP BY mo.sno
                                            ORDER BY mo.created_on DESC
                                            ) AS g WHERE 1  #AND g.pending_payment = 0 AND g.details_pending = 0 
                                     LIMIT 0,100
==============================================
#fas.unreconciled_value <> null and fas.unreconciled_value <> 0
# Unreconcile invouice value
SELECT * FROM pnh_franchise_account_summary fas WHERE fas.invoice_no ='20141019720';

SELECT * FROM pnh_member_insurance WHERE MID='22017144';


==============================================
SELECT * FROM (
                                    SELECT COUNT(DISTINCT mo.sno) AS total
                                    ,IF(fas.unreconciled_value = 0, 0,1) AS pending_payment
                                    ,IF(minfo.first_name OR minfo.mobile IS NULL OR minfo.mobile IS NULL OR mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
                                    OR mi.mem_receipt_amount IS NULL,0,1) AS details_pending
                                    FROM pnh_member_offers mo
                                    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
                                    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
                                    JOIN king_orders o ON o.id = mi.`order_id`
                                    JOIN king_dealitems di ON di.id = o.itemid
                                    JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id
                                    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
                                    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
                                    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no
                                    WHERE mo.offer_type IN (0,2) 
                                    ) AS g WHERE 1 

SELECT pnh_member_id,mobile FROM pnh_member_info 
                                            WHERE LENGTH(mobile)=10 AND 
                                            pnh_member_id = '22017262';
#======================
SELECT * FROM ( SELECT mo.*,mi.user_id,mi.first_name,mi.mobile,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,i.invoice_no
                                            ,IF(mi.mobile IS NULL, 1, 0) AS details_pending
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            
                                            WHERE mo.offer_type=1 
                                            GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        
#==========================
SELECT * FROM pnh_member_info WHERE pnh_member_id='21111152'
SELECT * FROM pnh_member_offers WHERE member_id='21111152'

#==========================
#May_06_2014
ALTER TABLE `pnh_member_offers` DROP COLUMN `mob_serv_provider`;
ALTER TABLE `pnh_member_info` ADD COLUMN `mobile_network` VARCHAR(100) NULL AFTER `mobile`; 
#==========================

# insurance list
SELECT * FROM (
	SELECT DISTINCT 
	    trr.territory_name,tw.town_name,f.franchise_id,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.sno AS insurance_id,mo.`order_id`,i.`invoice_no`,i.`createdon` AS inv_date,mi.`mem_receipt_no`,mi.`mem_receipt_date`,IFNULL(mi.first_name,minfo.first_name) AS first_name,mi.`proof_address`,mi.`city`,mi.`pincode`,mi.`mob_no`,di.`name` AS product_name,b.name AS brand
	    ,c.name AS catname,di.`name` AS product_make,'' AS other,imei.imei_no,'' AS serial_no,mi.`mem_receipt_amount` AS retailer_invoice_value,mo.`transid_ref` AS transid,ptype.name AS proof_given,mi.`proof_id` AS proof_details

	    ,IF(fas.unreconciled_value = 0, 0,1) AS pending_payment
	    ,IF( minfo.mobile IS NULL OR mi.proof_id IS NULL OR mi.proof_type IS NULL OR mi.proof_address IS NULL OR mi.first_name IS NULL OR mi.mob_no IS NULL OR mi.city IS NULL OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_date IS NULL
		OR mi.mem_receipt_amount IS NULL,1,0) AS details_pending
	    FROM pnh_member_offers mo
	    JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
	    JOIN king_transactions tr ON tr.transid = mo.transid_ref
	    JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id
	    JOIN pnh_towns tw ON tw.id = f.town_id  
	    JOIN pnh_m_territory_info trr ON trr.id = tw.territory_id 
	    JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
	    JOIN king_orders o ON o.id = mi.`order_id`
	    JOIN king_dealitems di ON di.id = o.itemid
	    LEFT JOIN t_imei_no imei ON imei.order_id = o.id
	    LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id = o.id
	    LEFT JOIN king_invoice i ON i.order_id = mi.`order_id`
	    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no AND fas.franchise_id= tr.franchise_id
	JOIN king_deals d ON d.dealid=di.dealid
	JOIN king_brands b ON b.id = d.brandid
	JOIN king_categories c ON c.id = d.catid
	LEFT JOIN insurance_m_types AS ptype ON ptype.id= mi.`proof_type`
	    WHERE mo.offer_type IN (0,2)
	    GROUP BY mo.sno
	    ORDER BY mo.created_on DESC
	    ) AS g WHERE 1;
# rechange list

SELECT * FROM ( 
                                            SELECT trr.territory_name,tw.town_name,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.first_name,mi.address AS proof_address,mi.city,mi.pincode,mi.mobile AS mobile
                                                    ,mo.`transid_ref` AS transid,mi.mobile_network,mo.delivery_status,mo.feedback_status,mo.feedback_value
                                                    ,IF( SUM(fas.unreconciled_value)  = 0, 0,1) AS pending_payment
                                                    ,IF(mi.mobile IS NULL, 1, 0) AS details_pending,GROUP_CONCAT(DISTINCT fas.invoice_no) AS invoice_nos
                                                    FROM pnh_member_offers mo
                                                    JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                                    JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                                    JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id
                                                    JOIN pnh_towns tw ON tw.id = f.town_id  
                                                    JOIN pnh_m_territory_info trr ON trr.id = tw.territory_id 
                                                    LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                                    
                                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no AND tr.franchise_id = fas.franchise_id
                                                    WHERE mo.offer_type=1 
                                                    GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC
                                                ) AS g WHERE 1;
                                                
#recharge total
SELECT * FROM ( SELECT COUNT(DISTINCT mo.sno) AS total
                                            ,IF( SUM(fas.unreconciled_value)  = 0, 0,1) AS pending_payment
                                            ,IF(mi.mobile IS NULL, 1, 0) AS details_pending
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no AND tr.franchise_id = fas.franchise_id
                                            WHERE mo.offer_type=1 
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        
SELECT * FROM ( 
                                            SELECT trr.territory_name,tw.town_name,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.first_name,mi.address AS proof_address,mi.city,mi.pincode,mi.mobile AS mobile
                                                    ,mo.`transid_ref` AS transid,mi.mobile_network,mo.delivery_status,mo.feedback_status,mo.feedback_value
                                                    ,IF( SUM(fas.unreconciled_value)  = 0, 0,1) AS pending_payment
                                                    ,IF(mi.mobile IS NULL, 1, 0) AS details_pending,GROUP_CONCAT(DISTINCT fas.invoice_no) AS invoice_nos
                                                    FROM pnh_member_offers mo
                                                    JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                                    JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                                    JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id
                                                    JOIN pnh_towns tw ON tw.id = f.town_id  
                                                    JOIN pnh_m_territory_info trr ON trr.id = tw.territory_id 
                                                    LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                                    
                                                    LEFT JOIN pnh_franchise_account_summary fas ON fas.invoice_no = i.invoice_no AND tr.franchise_id = fas.franchise_id
                                                    WHERE mo.offer_type=1  AND mo.process_status = 1
                                                    GROUP BY mo.`transid_ref` ORDER BY mo.created_on DESC
                                                ) AS g WHERE 1  AND g.details_pending = 0
                                    AND g.pending_payment = 0;
                                    
                                    
#May_07_2014

SELECT mim.id,mim.menu_id,m.name AS menu_name,mim.greater_than,mim.less_than,mim.insurance_value,mim.insurance_margin,mim.is_active,mim.created_on FROM pnh_member_insurance_menu mim
JOIN pnh_menu m ON m.id = mim.menu_id
WHERE mim.is_active = 1 ORDER BY mim.id ASC

pnh_menu

?term=10005734
#==
SELECT mim.id,mim.menu_id,m.name AS menu_name,mim.greater_than,mim.less_than,mim.insurance_value,mim.insurance_margin,mim.is_active,mim.created_on FROM pnh_member_insurance_menu mim
                                                                                            JOIN pnh_menu m ON m.id = mim.menu_id
                                                                                            WHERE mim.is_active = 1 ORDER BY mim.id ASC
                                                                                            
#==
SELECT insurance_value,insurance_margin FROM pnh_member_insurance_menu WHERE menu_id=? AND ? BETWEEN greater_than AND less_than AND is_active=1  LIMIT 1

#==
SELECT IFNULL(SUM(s.available_qty),0) AS stock,p.*,s.expiry_on,b.name AS brand,c.name AS cat FROM m_product_info p LEFT OUTER JOIN t_stock_info s ON s.product_id=p.product_id 
JOIN king_brands b ON b.id=p.brand_id 
JOIN king_categories c ON c.id=p.product_cat_id WHERE p.product_id = '10005741';  #'';10005492 #'156134';#'155822'

#==

SELECT IFNULL(SUM(s.available_qty),0) AS stock,p.*,s.expiry_on,b.name AS brand,c.name AS cat FROM m_product_info p LEFT OUTER JOIN t_stock_info s ON s.product_id=p.product_id 
JOIN king_brands b ON b.id=p.brand_id 
JOIN king_categories c ON c.id=p.product_cat_id WHERE p.product_id IN ('10005492,10005741'); 
#==

SELECT i.*,d.publish,d.menuid,d.brandid,d.catid,i.orgprice FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid WHERE i.is_pnh=1 AND i.pnh_id='10005741';
#=> 112

SELECT i.*,d.publish,d.menuid,d.brandid,d.catid,i.orgprice FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid WHERE i.is_pnh=1 AND i.pnh_id='10005492';
#=>112 17K

SELECT * FROM pnh_franchise_account_summary fas ORDER BY statement_id DESC LIMIT 12; #WHERE fas.invoice_no = '20141000434';

UPDATE pnh_franchise_account_summary SET unreconciled_value= ( unreconciled_value - '100' ),unreconciled_status= 'pending'
                                                    WHERE invoice_no = '20141019722' AND franchise_id = '59' AND action_type=1
                                                    
                                                    SELECT  15830-460.97 =>15369.03
                                                    SELECT 15780-460.97 =>15319.03
15980-19

SELECT * FROM pnh_t_receipt_info WHERE receipt_id='5318'

SELECT f.franchise_name,f.login_mobile1,r.instrument_no,r.receipt_amount,r.receipt_type,r.payment_mode,r.bank_name,r.remarks,r.franchise_id,r.created_on 
	FROM pnh_t_receipt_info r 
	JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
	WHERE r.receipt_id='5318';


SELECT * FROM pnh_m_deposited_receipts WHERE receipt_id='5318'


SELECT * FROM pnh_franchise_account_summary fas WHERE fas.invoice_no = '20141019716';
#================
DELETE FROM `snapitto_sand`.`pnh_t_receipt_reconcilation_log` WHERE `logid` = '75'; 
DELETE FROM `snapitto_sand`.`pnh_t_receipt_reconcilation` WHERE `id` = '58'; 
#================

-- // RESET RECONCILE TABLE
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
TRUNCATE TABLE `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_value = receipt_amount WHERE receipt_amount !=0;
UPDATE `snapittoday_db_jan_2014`.pnh_t_receipt_info SET unreconciled_status = 'pending' WHERE receipt_amount !=0;

UPDATE pnh_franchise_account_summary SET unreconciled_value = receipt_amount;

SELECT * FROM pnh_t_receipt_reconcilation_log
SELECT * FROM pnh_t_receipt_reconcilation WHERE invoice_no = ''

SELECT * FROM pnh_member_insurance WHERE `mid` = '22017271' AND sno = '60';
SELECT * FROM king_transactions LIMIT 10;



SELECT * FROM ( SELECT COUNT(DISTINCT mo.sno) AS total
                                            ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                            ,IF( mi.mobile IS NULL OR mi.mobile_network IS NULL, 1,0) AS details_pending
                                            ,GROUP_CONCAT(o.status) AS `status`
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            
                                            WHERE mo.offer_type=1 
                                            GROUP BY mo.sno
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        

SELECT * FROM ( SELECT mo.*,mi.user_id,mi.first_name,mi.mobile,mi.mobile_network,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`,GROUP_CONCAT(DISTINCT i.invoice_no) AS invoice_nos
                                                    ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                                    ,IF( mi.mobile IS NULL OR mi.mobile_network IS NULL, 1,0) AS details_pending
                                                    ,IF(o.status = 3,1,0)  AS order_pending
                                                    ,GROUP_CONCAT(o.status) AS os
                                                    FROM pnh_member_offers mo
                                                    JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                                    JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                                    JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                                    LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                                    LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                                    LEFT JOIN king_orders o ON o.transid=tr.transid
                                                    WHERE mo.offer_type = 1 #and 
                                                    AND mo.transid_ref='PNH34745' #$cond
#                                                    group by mo.sno
                                                    GROUP BY mo.`transid_ref`
                                                     ORDER BY mo.created_on DESC
                                                ) AS g WHERE 1  AND g.order_pending = 1;
                                                

SELECT * FROM ( SELECT COUNT(DISTINCT mo.sno) AS total
                                            ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                            ,IF( mi.mobile IS NULL OR mi.mobile_network IS NULL, 1,0) AS details_pending
                                            ,IF(o.status = 3,1,0)  AS order_pending,GROUP_CONCAT(o.status) AS os
                                            ,mo.`transid_ref`
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN king_orders o ON o.id=i.order_id
                                            WHERE mo.offer_type=1 
                                            #group by mo.sno
                                            GROUP BY mo.`transid_ref`
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        
                                        
# May_12_2014

-- -- -- -- -- -- -- --  NEW -- -- -- -- -- --

			SELECT *,COUNT(*) AS total FROM (
                                    SELECT 
                                    IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                    ,IF( minfo.first_name IS NULL OR minfo.first_name = 0 OR minfo.first_name=''
					OR minfo.mobile IS NULL OR minfo.mobile IS NULL = 0 OR minfo.mobile=''
					OR mi.proof_id IS NULL OR mi.proof_id = 0 OR mi.proof_id=''
					OR mi.proof_type IS NULL OR mi.proof_type = 0 OR mi.proof_type=''
					OR mi.proof_address IS NULL OR mi.proof_address=0 OR mi.proof_address=''
					OR mi.first_name IS NULL OR mi.first_name=0 OR mi.first_name=''
					OR mi.mob_no IS NULL OR mi.mob_no=0 OR mi.mob_no=''
					OR mi.city IS NULL OR mi.city=0 OR mi.city=''
					OR mi.mem_receipt_no IS NULL OR mi.mem_receipt_no=0 OR mi.mem_receipt_no=''
					OR mi.mem_receipt_date IS NULL OR mi.mem_receipt_date=0 OR mi.mem_receipt_date=''
					OR mi.mem_receipt_amount IS NULL OR mi.mem_receipt_amount=0 OR mi.mem_receipt_amount='',1,0) AS details_pending
                                    ,o.status
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
                                    WHERE mo.offer_type IN (0,2) 
                                    ) AS g WHERE 1  AND g.pending_payment = 0;
                                    

# 49-44=5 req

SELECT *,COUNT(*) AS total FROM ( SELECT IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                            ,IF(mi.first_name IS NULL OR mi.first_name=0 OR mi.first_name=''
								OR mi.mobile IS NULL OR mi.mobile=0 OR mi.mobile=''
								OR mi.mobile_network IS NULL OR mi.mobile_network=0 OR mi.mobile_network='' , 1,0) AS details_pending
                                            ,IF(o.status = 3,1,0)  AS order_pending
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN king_orders o ON o.transid=tr.transid
                                            WHERE mo.offer_type=1   AND mo.delivery_status = 1
                                    AND mo.feedback_status = 0
                                    AND mo.process_status = 0 
                                            #group by mo.sno
                                            GROUP BY mo.`transid_ref`
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1

#========================================================================
#May_12_2014
ALTER TABLE `pnh_member_offers` ADD COLUMN `details_updated` TINYINT(11) DEFAULT 0 NULL AFTER `delivery_status`; 
#========================================================================

SELECT * FROM (
					SELECT DISTINCT mo.*,minfo.user_id
                                            ,IFNULL(mi.first_name,minfo.first_name) AS first_name
                                            ,f.franchise_name,f.territory_id,f.town_id,mi.itemid,di.name AS dealname,mo.created_on AS `date`,o.status AS order_status,imei.is_imei_activated
                                            ,o.imei_reimbursement_value_perunit,i.invoice_no,imei.imei_no,nonsk_imei.nonsk_imei_no,o.has_nonsk_imei_insurance
                                            ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
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
                                            WHERE mo.offer_type IN (0,2)
                                            GROUP BY mo.sno
                                            ORDER BY mo.created_on DESC
                                            ) AS g WHERE 1 
                                     LIMIT 0,100
                                     
#==========================================================================================================
UPDATE pnh_member_offers mo
JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
 SET details_updated = 1
WHERE mo.offer_type IN (0,2) AND minfo.mobile!='' AND mi.proof_id!='' AND mi.proof_type!='' AND mi.proof_address!='' AND mi.first_name!='' AND mi.mob_no!='' AND mi.mem_receipt_no!='' AND UNIX_TIMESTAMP(mi.mem_receipt_date)!='' AND mi.mem_receipt_amount!='';
#==========================================================================================================
UPDATE pnh_member_offers mo 
JOIN pnh_member_info minfo ON minfo.pnh_member_id = mo.member_id
SET details_updated = 1
WHERE mo.offer_type=1 AND minfo.mobile_network!='' AND minfo.first_name!='' AND minfo.mobile!='';
#==========================================================================================================

#May_13_2014

SELECT * FROM ( 
                                                    SELECT mo.*,mi.user_id,mi.first_name,mi.mobile,mi.mobile_network,f.franchise_name,f.territory_id,f.town_id,mo.created_on AS `date`
                                                        ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                                        ,IF(o.status = 3,1,0)  AS order_pending
                                                        FROM pnh_member_offers mo
                                                        JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                                        JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                                        JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                                        LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                                        LEFT JOIN king_orders o ON o.transid=tr.transid
                                                        WHERE mo.offer_type=1 
                                                        #group by mo.sno
                                                        GROUP BY mo.`transid_ref`
                                                        ORDER BY mo.created_on DESC
                                                ) AS g WHERE 1  LIMIT 0,100

SELECT *,COUNT(*) AS total FROM ( SELECT IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                            ,mo.details_updated
                                            ,IF(o.status = 3,1,0)  AS order_pending
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN king_orders o ON o.transid=tr.transid
                                            WHERE mo.offer_type=1  AND mo.process_status = 1 #AND mo.details_updated = 1 
                                            #group by mo.sno
                                            GROUP BY mo.`transid_ref`
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1;
                                        
SELECT * FROM (
					SELECT DISTINCT 
					   trr.territory_name,tw.town_name,f.franchise_id,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.sno AS insurance_id,mo.`order_id`,i.`invoice_no`,i.`createdon` AS inv_date,mi.`mem_receipt_no`,mi.`mem_receipt_date`,IFNULL(mi.first_name,minfo.first_name) AS first_name,mi.`proof_address`,mi.`city`,mi.`pincode`,mi.`mob_no`,di.`name` AS product_name,b.name AS brand
                                        ,c.name AS catname,di.`name` AS product_make,'' AS other,imei.imei_no,'' AS serial_no,mi.`mem_receipt_amount` AS retailer_invoice_value,mo.`transid_ref` AS transid,ptype.name AS proof_given,mi.`proof_id` AS proof_details
                                        ,IF(fas.unreconciled_value = 0, 0,1) AS pending_payment
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
                                            WHERE mo.offer_type IN (0,2)
                                            GROUP BY mo.sno
                                            ORDER BY mo.created_on DESC
                                            ) AS g WHERE 1
                                            
                                            
SELECT * FROM ( SELECT trr.territory_name,tw.town_name,f.franchise_name,f.login_mobile1 AS franchise_mob,mi.first_name,mi.address AS proof_address,mi.city,mi.pincode,mi.mobile AS mobile
                                                ,mo.`transid_ref` AS transid,mi.mobile_network,mo.delivery_status,mo.feedback_status,mo.feedback_value
                                                ,IF(rcon.unreconciled = 0,0,1) AS pending_payment
						,mo.details_updated
						,IF(o.status = 3,1,0)  AS order_pending 
                                                        FROM pnh_member_offers mo
                                                        JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                                        JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id
						JOIN pnh_towns tw ON tw.id = f.town_id  
                                                JOIN pnh_m_territory_info trr ON trr.id = tw.territory_id 
                                                        JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                                        LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                                        LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                                        LEFT JOIN king_orders o ON o.transid=tr.transid
                                                        WHERE mo.offer_type=1 
                                                        GROUP BY mo.`transid_ref`
                                                        ORDER BY mo.created_on DESC
                                                ) AS g WHERE 1;
                                                
                                                
UPDATE pnh_member_offers SET details_updated = 1 WHERE member_id = '34' AND offer_type=1

SELECT STATUS,IFNULL(amt1,amt2) AS amt,totals,invoice_no
	FROM ( SELECT b.status,SUM((mrp-(discount+credit_note_amt))*a.invoice_qty) AS amt1,SUM(i_orgprice-(i_coup_discount+i_discount)*b.quantity) AS amt2,
	COUNT(b.id) AS totals,a.invoice_no
	FROM king_orders b
	LEFT JOIN king_invoice a ON a.order_id = b.id
	WHERE b.transid = 'PNH29199' GROUP BY b.status ) AS g;
	
SELECT DATE_FORMAT(shipped_on,'%d/%m/%Y') AS shipped_on FROM shipment_batch_process_invoice_link WHERE invoice_no = '20141019720' AND shipped = 1;


SELECT b.status#,SUM((mrp-(discount+credit_note_amt))*a.invoice_qty) AS amt1,SUM(i_orgprice-(i_coup_discount+i_discount)*b.quantity) AS amt2
	,COUNT(b.id) AS totals,a.invoice_no
	FROM king_orders b
	LEFT JOIN king_invoice a ON a.order_id = b.id
	#WHERE b.transid = 'PNH29199' 
	GROUP BY b.status;
#=========================================================
# May_14_2014

SELECT DISTINCT 
	    o.itemid,COUNT(DISTINCT tr.transid) AS ttl_trans,GROUP_CONCAT(DISTINCT tr.transid) AS grp_trans
	    ,bc.id AS menuid,bc.batch_grp_name AS menuname,GROUP_CONCAT(DISTINCT d.menuid) AS actualmenus,f.territory_id
	    ,sd.id,sd.batch_id,sd.p_invoice_no
	    ,FROM_UNIXTIME(tr.init) AS init,bc.batch_size,bc.group_assigned_uid AS bc_group_uids,GROUP_CONCAT(DISTINCT CONCAT(o.id,":",o.status) ) AS order_status
		FROM king_transactions tr
		JOIN king_orders AS o ON o.transid=tr.transid
		JOIN proforma_invoices AS `pi` ON pi.order_id = o.id AND pi.invoice_status=1
		JOIN shipment_batch_process_invoice_link sd ON sd.p_invoice_no = pi.p_invoice_no AND sd.invoice_no=0
		JOIN king_dealitems dl ON dl.id = o.itemid
		JOIN king_deals d ON d.dealid = dl.dealid 
		JOIN pnh_menu mn ON mn.id=d.menuid
		JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id AND f.is_suspended = 0
		JOIN m_batch_config bc ON FIND_IN_SET(d.menuid,bc.assigned_menuid)
		WHERE sd.batch_id = 7000  AND f.territory_id = 11  AND tr.batch_enabled=1 #and o.status in (0,1)
		    GROUP BY  bc.id
		ORDER BY tr.init ASC;
                                
# PNHDRM22113 
# PNHJRD77854,PNHKCC42923,PNHQRP26864
#=========================================================

SELECT o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid AND o.status IN (0,1) AND tr.batch_enabled = 1
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1 
                    JOIN king_dealitems di ON di.id = o.itemid 
                    WHERE i.id IS NULL AND tr.transid = 'PNHJRD77854'
                    ORDER BY tr.init,di.name;
#=========================================================

SELECT `status` FROM king_orders WHERE transid='PNHJRD77854';

create_batch_by_group_config
SELECT *,id,transid,`status` FROM king_orders WHERE id IN('7233155522','5319477189','9112799815','2711259352');

SELECT id,transid,`status` FROM king_orders WHERE transid IN('PNHJRD77854','PNHKCC42923','PNHQRP26864');

$order_status_arr[0]='Pending';
$order_status_arr[1]='Unshipped';
$order_status_arr[2]='Shipped';
$order_status_arr[3]='Cancelled';
$order_status_arr[4]='Returned';

SELECT STATUS,IFNULL(amt1,amt2) AS amt,totals,invoice_no
		FROM ( SELECT b.status,SUM((mrp-(discount+credit_note_amt))*a.invoice_qty) AS amt1,SUM(i_orgprice-(i_coup_discount+i_discount)*b.quantity) AS amt2,
		COUNT(b.id) AS totals,a.invoice_no
		FROM king_orders b
		LEFT JOIN king_invoice a ON a.order_id = b.id
		WHERE b.transid = 'PNHJRD77854' GROUP BY b.status ) AS g;
===============
SELECT * FROM t_deal_status_log;

SELECT * FROM shipment_batch_process ORDER BY batch_id DESC;
SELECT * FROM shipment_batch_process_invoice_link ORDER BY batch_id DESC;

#=====================================================================================
# May_14_2014
ALTER TABLE `king_orders` ADD COLUMN `order_product_id` BIGINT NULL AFTER `lpoints_valid_days`;

#BIG BUCKET ROW
#INSERT INTO `shipment_batch_process` (`num_orders`, `status`, `batch_remarks`,`orders_by`, `created_by`,`created_on`) VALUES ('9999', '2', 'DEFAULT BIG BUCKET - RESERVATION','Developer', '0','2013-10-03 14:42:46');
#=====================================================================================
CREATE TABLE `m_vendor_api_info` (                       
                     `id` BIGINT(20) NOT NULL AUTO_INCREMENT,               
                     `vendor_id` BIGINT(20) DEFAULT NULL,                   
                     `api_link` VARCHAR(255) DEFAULT NULL,                  
                     `type` VARCHAR(255) DEFAULT NULL,                      
                     PRIMARY KEY (`id`)                                     
                   );

CREATE TABLE `m_client_brand_link` (                     
                       `id` BIGINT(20) NOT NULL AUTO_INCREMENT,               
                       `brand_id` BIGINT(20) DEFAULT NULL,                    
                       `client_brand` VARCHAR(255) DEFAULT NULL,              
                       `created_on` DATETIME DEFAULT NULL,                    
                       `created_by` INT(11) DEFAULT NULL,                     
                       PRIMARY KEY (`id`)                                     
                     );
CREATE TABLE `m_client_category_link` (                  
                          `id` BIGINT(20) NOT NULL AUTO_INCREMENT,               
                          `category_id` BIGINT(20) DEFAULT NULL,                 
                          `client_category` VARCHAR(255) DEFAULT NULL,           
                          `created_on` DATETIME DEFAULT NULL,                    
                          `created_by` INT(11) DEFAULT NULL,                     
                          PRIMARY KEY (`id`)                                     
                        );
CREATE TABLE `t_vendor_product_import_log` (                       
                               `id` BIGINT(20) NOT NULL AUTO_INCREMENT,                         
                               `vendor_id` BIGINT(20) DEFAULT NULL,                             
                               `type` VARCHAR(255) DEFAULT NULL,                                
                               `data` TEXT,                                                     
                               `status` INT(1) DEFAULT NULL COMMENT '1:updated,2:not updated',  
                               `msg` TEXT,                                                      
                               `created_on` DATETIME DEFAULT NULL,                              
                               `created_by` INT(11) DEFAULT NULL,                               
                               `modified_on` DATETIME DEFAULT NULL,                             
                               `modified_by` INT(11) DEFAULT NULL,                              
                               PRIMARY KEY (`id`)                                               
                             );
CREATE TABLE `m_client_menu_link` (                      
                      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,               
                      `menu_id` BIGINT(20) DEFAULT NULL,                     
                      `client_menu` VARCHAR(255) DEFAULT NULL,               
                      `created_on` DATETIME DEFAULT NULL,                    
                      `created_by` INT(11) DEFAULT NULL,                     
                      PRIMARY KEY (`id`)                                     
                    );
                    
ALTER TABLE `king_orders` ADD COLUMN `order_from_vendor` INT (11)  NULL  AFTER `insurance_amount`, ADD COLUMN `vendor_order_refe_id` VARCHAR (255)  NULL  AFTER `order_from_vendor`;
ALTER TABLE `king_orders` DROP COLUMN `product_id`,ADD COLUMN `order_product_id` BIGINT (20)  NULL  AFTER `itemid`;
ALTER TABLE `t_vendor_deal_import_log` ADD COLUMN `vendor_id` BIGINT (20)  NULL  AFTER `id`;

SELECT d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.p_invoice_no,p.product_name,
		o.i_orgprice AS order_mrp,o.quantity*pl.qty AS qty,
		d.name AS deal,d.dealid,o.itemid,o.id AS order_id,i.p_invoice_no 
	FROM proforma_invoices i 
	JOIN king_orders o ON o.id=i.order_id AND i.transid = o.transid 
	JOIN m_product_deal_link pl ON pl.itemid=o.itemid AND pl.is_active = 1 
	JOIN m_product_info p ON p.product_id=pl.product_id 
	JOIN king_dealitems d ON d.id=o.itemid 
	JOIN king_deals e ON e.dealid=d.dealid 
	WHERE i.p_invoice_no='116483' AND i.invoice_status=1 
	AND ((d.is_group = 1  
			AND order_product_id = pl.product_id	
	) OR d.is_group = 0)
	ORDER BY o.sno;
	

order_product_id;

ALTER TABLE `king_orders` ADD COLUMN `order_product_id` BIGINT NULL AFTER `lpoints_valid_days`; 

253486478641536


-- =============================================================================
# RESET IMEI NUMBER
UPDATE t_imei_no SET STATUS=0 AND order_id=0 WHERE imei_no = '911208355656643';
-- =============================================================================


May_16_2014

SELECT * FROM pnh_sms_log_sent WHERE `to`= '9742951690' #'9590932088'

#========================
SELECT o.reply_for,CONCAT(f.franchise_name,', ',f.city) AS franchise,l.franchise_id,l.sender AS `from`,l.msg AS input,o.msg AS reply,l.created_on,o.created_on AS reply_on 
	FROM pnh_sms_log l 
	JOIN pnh_m_franchise_info f ON f.franchise_id=l.franchise_id 
	LEFT OUTER JOIN pnh_sms_log o ON o.reply_for=l.id 
	WHERE l.franchise_id='59'
	
#========================
SELECT l.*,CONCAT(f.franchise_name,', ',f.city) AS franchise 
	FROM pnh_sms_log_sent l 
	JOIN pnh_m_franchise_info f 
	ON f.franchise_id=l.franchise_id 
	WHERE l.franchise_id='59'
	
#==========================
#sms log
SELECT l.*,mi.first_name 
FROM pnh_sms_log l
LEFT  JOIN pnh_member_info mi ON mi.mobile = l.sender
WHERE l.sender='09590932088'

SELECT * FROM pnh_member_info mi WHERE mi.pnh_member_id='21111152';

#Received
SELECT * FROM pnh_sms_log l WHERE sender='09590932088'

#reply
SELECT o.reply_for,mi.first_name AS member,l.sender AS `from`,l.msg AS input,o.msg AS reply,l.created_on,o.created_on AS reply_on,mi.user_id,mi.mobile
FROM pnh_sms_log l
LEFT OUTER JOIN pnh_sms_log o ON o.reply_for=l.id 
JOIN pnh_member_info mi ON CONCAT(0,mi.mobile) = l.sender
WHERE mi.pnh_member_id='21111152';#l.sender='09590932088';

#sent
SELECT * FROM pnh_sms_log_sent l WHERE `to`='9590932088'

SELECT l.*,CONCAT(mi.first_name ,', ',mi.city) AS member,mi.user_id
FROM pnh_sms_log_sent l 
JOIN pnh_member_info mi ON mi.mobile = l.to WHERE mi.pnh_member_id='21111152'


CREATE TABLE `pnh_api_users` (                                           
                 `user_id` BIGINT(20) NOT NULL AUTO_INCREMENT,                          
                 `type` INT(1) DEFAULT '0' COMMENT '0:franchise,1:employee',            
                 `franchise_id` BIGINT(20) DEFAULT NULL,                                
                 `username` VARCHAR(255) DEFAULT NULL,                                  
                 `password` VARCHAR(255) DEFAULT NULL,                                  
                 `is_logged_in` INT(1) DEFAULT '0' COMMENT '0:not logged,1:logged in',  
                 `modified_by` BIGINT(20) DEFAULT NULL,                                 
                 `modified_on` DATETIME DEFAULT NULL,                                   
                 `created_by` BIGINT(20) DEFAULT NULL,                                  
                 `created_on` DATETIME DEFAULT NULL,                                    
                 PRIMARY KEY (`user_id`)                                                
               );
               
CREATE TABLE `pnh_api_user_auth` (                        
     `id` BIGINT(20) NOT NULL AUTO_INCREMENT,                
     `user_id` BIGINT(20) DEFAULT NULL,                      
     `auth_key` VARCHAR(255) DEFAULT NULL,                   
     `expired_on` DATETIME DEFAULT NULL,                     
     `created_by` BIGINT(20) DEFAULT NULL,                   
     `created_on` DATETIME DEFAULT NULL,                     
     PRIMARY KEY (`id`)                                      
   );
   
   SELECT MD5('123456'); #=>e10adc3949ba59abbe56e057f20f883e
   SELECT NOW();
   
May_17_2014
   
SELECT MD5('12345');
SELECT MD5('3304l3'); #c37cb5d613186b90a159d70d322e24f5

#====   
#check is pwd already exists
SELECT * FROM pnh_users WHERE `password`=MD5('123456') AND username='Shivaraj'
   
#====
UPDATE `pnh_api_users` SET `password` = '827ccb0eea8a706c4c34a16891f84e7b' WHERE `username` = 'Shivaraj'; 
#=====
SELECT a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
				t.town_name,tt.territory_name,a.login_mobile1,is_prepaid
					FROM pnh_m_franchise_info a 
					JOIN pnh_api_users b ON b.franchise_id=a.franchise_id
					JOIN pnh_towns AS t ON t.id=a.town_id
					JOIN pnh_m_territory_info tt ON tt.id=a.territory_id
					WHERE a.franchise_id=
					
SELECT * FROM king_users;

SELECT user_id,username,TYPE FROM pnh_api_users au 
	LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = au.franchise_id
	WHERE 1  AND au.username = '9480205313' OR f.login_mobile1= '9480205313'
	
SELECT * FROM pnh_m_franchise_info;

DESC pnh_m_franchise_info;

pnh_api_users => pnh_users

`pnh_user_auth`

SELECT * FROM pnh_users;

#=============================================================
#May_17_2014
RENAME TABLE `pnh_api_user_auth` TO `pnh_user_auth`;
RENAME TABLE `pnh_api_users` TO `pnh_users`; 
#=============================================================

# May_19_2014

SELECT f.franchise_id,f.pnh_franchise_id,f.franchise_name,f.address,f.city,f.postcode,f.state,f.login_mobile1
				,u.user_id
				#,t.town_name,tt.territory_name,f.is_prepaid,f.credit_limit,f.locality
					FROM pnh_m_franchise_info f 
					JOIN pnh_users u ON u.franchise_id=f.franchise_id
					#join pnh_towns as t on t.id=f.town_id
					#join pnh_m_territory_info tt on tt.id=f.territory_id
					WHERE u.user_id='1';
					
# new
SELECT f.franchise_id,f.pnh_franchise_id,f.franchise_name,f.login_mobile1,f.address,f.city,f.postcode,f.state
				,u.user_id
					FROM pnh_m_franchise_info f 
					JOIN pnh_users u ON u.franchise_id=f.franchise_id
					WHERE u.user_id='1'

SELECT 
user_id,username,TYPE,login_mobile1
FROM pnh_users au 
LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = au.franchise_id
WHERE 1 
AND au.username = '9480205313' OR f.login_mobile1= '9480205313';

-- 
SELECT * FROM pnh_users u
LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = u.franchise_id
WHERE (u.username = ? OR f.login_mobile1=? ) AND u.password = MD5(?)

SELECT a.franchise_id,a.pnh_franchise_id,a.franchise_name,a.login_mobile1,a.login_mobile2,a.address,a.locality,a.city,a.postcode,a.state,a.credit_limit,
				t.town_name,tt.territory_name,is_prepaid
					FROM pnh_m_franchise_info a 
					JOIN pnh_users b ON b.franchise_id=a.franchise_id
					JOIN pnh_towns AS t ON t.id=a.town_id
					JOIN pnh_m_territory_info tt ON tt.id=a.territory_id
					WHERE a.franchise_id='' OR a.login_mobile1 = '9480205313'
					
DESC pnh_m_franchise_info;

SELECT * FROM pnh_m_franchise_info WHERE franchise_id='59';

SELECT * FROM pnh_m_franchise_contacts_info WHERE franchise_id='59';

SELECT franchise_id,GROUP_CONCAT(DISTINCT contact_name) AS contact_names, COUNT(*) AS t FROM pnh_m_franchise_contacts_info WHERE franchise_id='59' GROUP BY franchise_id
					
SELECT * FROM king_transactions tr
ORDER BY tr.id DESC
LIMIT 10

#May_20_2014

SELECT * FROM king_dealitems WHERE id='2348147284'

SELECT transid FROM king_transactions tr 
JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
WHERE f.login_mobile1 = '9480205313' AND actiontime BETWEEN UNIX_TIMESTAMP('2014-05-10') AND UNIX_TIMESTAMP('2014-05-20')
ORDER BY tr.id DESC LIMIT 10;

SELECT DATE(NOW());

DESC king_transactions;

SELECT tr.transid,tr.actiontime,f.franchise_name,tr.trans_created_by FROM king_transactions tr 
								JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
								WHERE f.login_mobile1 = '9480205313'
								ORDER BY tr.id DESC
								
SELECT username FROM king_admin WHERE id='37';

#===============< Get count of orders by frnchise >===========
SELECT * FROM (
	SELECT f.franchise_id,GROUP_CONCAT(tr.transid) AS transid,COUNT(*) AS t FROM pnh_m_franchise_info f
	 LEFT JOIN king_transactions tr ON tr.franchise_id = f.franchise_id
	GROUP BY f.franchise_id
) AS g WHERE g.t < 2;

SELECT * FROM pnh_users

# ===================< INSERT LOGIN USER QUERY >=====================
INSERT INTO `pnh_users` (`franchise_id`, `username`, `password`, `is_logged_in`,`created_by`,`created_on`)
		 VALUES ('37', 'Nadeem', 'bf860eba06d8a33cfb9cd91ecf7af888' , '1' , '37', '2014-05-20 16:02:06');
# ===================< INSERT LOGIN USER QUERY >=====================


# insurance offers
SELECT COUNT(*) AS total
	,pending_payment
	,details_updated
	,order_pending
	,GROUP_CONCAT(DISTINCT franchise_id) AS franchise_ids,GROUP_CONCAT(DISTINCT territory_id) AS territory_ids,GROUP_CONCAT(DISTINCT town_id) AS town_ids
	FROM ( 
	SELECT IF(rcon.unreconciled = 0,0,1) AS pending_payment
                                            ,mo.details_updated
                                            ,IF(o.status = 3,1,0)  AS order_pending
                                            ,f.franchise_id,f.territory_id,f.town_id
                                            FROM pnh_member_offers mo
                                            JOIN king_transactions tr ON tr.transid = mo.transid_ref
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
                                            LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
                                            LEFT JOIN king_orders o ON o.transid=tr.transid
                                            WHERE mo.offer_type=1 
                                            #group by mo.sno
                                            GROUP BY mo.`transid_ref`
                                            ORDER BY mo.created_on DESC
                                        ) AS g WHERE 1 

# list of mem fee

SELECT COUNT(DISTINCT mo.sno) AS total
			,GROUP_CONCAT(DISTINCT f.franchise_id) AS franchise_ids,GROUP_CONCAT(DISTINCT f.territory_id) AS territory_ids,GROUP_CONCAT(DISTINCT f.town_id) AS town_ids
                                            FROM pnh_member_offers mo
                                            JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
                                            JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
                                            LEFT JOIN king_invoice i ON i.order_id = mo.`order_id`
                                            WHERE mo.offer_type=3 
                                            ORDER BY mo.created_on DESC

#May_21_2014

SELECT * FROM pnh_franchise_requests;

$in_array = array('username'=>$username,"franchise_id"=>$franchise_id,"request_from" => $request_from,"request_desc"=>$request_desc,"status"=>$STATUS,"created_on"=>$TIME);
			$this->db->INSERT("pnh_franchise_requests",$in_array);
			
#===============< REQUEST & complaint TABLE >====================
#May_21_2014
CREATE TABLE `pnh_franchise_requests`( `id` BIGINT NOT NULL AUTO_INCREMENT, `franchise_id` VARCHAR(100), `request_from` VARCHAR(80), `request_desc` TEXT, `status` TINYINT(11), `created_on` DATETIME, PRIMARY KEY (`id`) );
CREATE TABLE `pnh_franchise_complaints`( `id` BIGINT NOT NULL AUTO_INCREMENT, `token_id` VARCHAR(180) COMMENT 'Complaint Reference id', `franchise_id` INT(50), `complaint_from` VARCHAR(80), `complaint_desc` TEXT, `priority` VARCHAR(50), `remarks` VARCHAR(100), `status` TINYINT(11) DEFAULT 1, `created_on` DATETIME, PRIMARY KEY (`id`) );
#===============< REQUEST & complaint TABLE >====================

SELECT * FROM pnh_franchise_requests;

May_22_2014

SELECT * FROM pnh_member_offers;

SELECT * FROM `user_access_roles`;

SELECT * FROM pnh_t_receipt_info reconcilation

SELECT * FROM `pnh_t_receipt_reconcilation` WHERE invoice_no ='20141019701'
SELECT * FROM `pnh_t_receipt_reconcilation_log` WHERE receipt_id='5463'; reconcile_id='76';

May_23_2014

SELECT  COUNT(*) AS total
	,pending_payment,order_pending
	,GROUP_CONCAT(DISTINCT franchise_id) AS franchise_ids,GROUP_CONCAT(DISTINCT territory_id) AS territory_ids,GROUP_CONCAT(DISTINCT town_id) AS town_ids FROM ( 
		SELECT IF(rcon.unreconciled = 0,0,1) AS pending_payment
		,IF(o.status = 3,1,0)  AS order_pending
		,f.franchise_id,f.territory_id,f.town_id
		FROM pnh_member_offers mo
		JOIN king_transactions tr ON tr.transid = mo.transid_ref
		JOIN pnh_m_franchise_info f ON f.franchise_id= tr.franchise_id 
		JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
		LEFT JOIN king_invoice i ON i.transid = mo.`transid_ref`
		LEFT JOIN pnh_t_receipt_reconcilation rcon ON rcon.invoice_no = i.invoice_no
		LEFT JOIN king_orders o ON o.transid=tr.transid
		WHERE mo.offer_type =1  AND mo.details_updated=0 AND mo.feedback_status = 0 AND mo.delivery_status = 1 
		GROUP BY mo.`transid_ref`
		ORDER BY mo.created_on DESC
	) AS g WHERE 1   AND g.order_pending = 0;
	

SELECT mo.* #count(DISTINCT mo.sno) as total
	#,GROUP_CONCAT(DISTINCT f.franchise_id) AS franchise_ids,GROUP_CONCAT(DISTINCT f.territory_id) AS territory_ids,GROUP_CONCAT(DISTINCT f.town_id) AS town_ids
	FROM pnh_member_offers mo
	JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id 
	JOIN pnh_m_franchise_info f ON f.franchise_id= mo.franchise_id 
	LEFT JOIN king_invoice i ON i.order_id = mo.`order_id`
	WHERE mo.offer_type=3  
	#AND mo.delivery_status = 1  
	#AND mo.details_updated = 1 
	ORDER BY mo.created_on DESC;
	
SELECT rlog.receipt_id,rcon.id AS reconcile_id,rcon.invoice_no,rcon.inv_amount,rlog.reconcile_amount FROM pnh_t_receipt_reconcilation rcon 
                                            JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                            WHERE rlog.is_reversed = 0 AND rlog.is_invoice_cancelled = 0 AND rcon.invoice_no ='fghjdfghj';
                                            

SELECT * FROM pnh_t_receipt_reconcilation_log WHERE is_invoice_cancelled=1;

May_24_2014

#===============< REVERSE RECONCILED INVOICE LOG START >=======================
SELECT rlog.logid,rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.is_invoice_cancelled,rlog.reconcile_amount,rlog.is_reversed,DATE_FORMAT(FROM_UNIXTIME(rlog.created_on),'%d-%b-%Y %h-%i-%s') AS created_on,rlog.remarks
	,a.username,rcon.invoice_no,rcon.debit_note_id
	FROM pnh_t_receipt_reconcilation_log rlog
	JOIN king_admin a ON a.id = rlog.created_by
	JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.logid
	WHERE rlog.is_invoice_cancelled=1;
#===============< REVERSE RECONCILED INVOICE LOG END >=======================	

	
SELECT * FROM king_admin;

# May_26_2014



SELECT * FROM pnh_t_receipt_reconcilation_log WHERE logid IN('1586','1587','2624','2648','2649');
SELECT * FROM pnh_t_receipt_reconcilation WHERE id IN('1465','1466','2360','2378','2378')

#===============< REVERSE RECONCILED INVOICE LOG START >=======================
SELECT rlog.logid,rlog.credit_note_id,rlog.receipt_id,rlog.reconcile_id,rlog.is_invoice_cancelled,rlog.reconcile_amount,rlog.is_reversed,DATE_FORMAT(FROM_UNIXTIME(rlog.created_on),'%d-%b-%Y %h-%i-%s') AS created_on,rlog.remarks
	,a.username
	,rcon.invoice_no,rcon.debit_note_id
	FROM pnh_t_receipt_reconcilation_log rlog
	JOIN king_admin a ON a.id = rlog.created_by
	JOIN pnh_t_receipt_reconcilation rcon ON rcon.id = rlog.reconcile_id
	WHERE rlog.is_invoice_cancelled=1
	ORDER BY rlog.created_on DESC;
#===============< REVERSE RECONCILED INVOICE LOG END >=======================

DESC pnh_t_receipt_reconcilation;

# ============== Insert new member into table ============================
INSERT INTO `pnh_users` (`user_id`, `franchise_id`, `username`, `password`,`created_by`, `created_on`) 
		VALUES ('2', '59', 'shivaraj','b343ef440fded01538c145094a034cea' , '37' , '2014-05-27 11:38:21' ); 
# ============== Insert new member into table ============================
`snapitto_erpsndx`

SELECT * FROM king_transactions WHERE transid='PNHRSR89632'

SELECT * FROM king_admin username='sowmyashree'
SELECT MD5('sowmyashree') 
 852378559666bb9dd154bf88ff0dc246 => o4lflj

SELECT MD5('o4lflj')  
 # =========================================
#May_27_2014
ALTER TABLE `pnh_member_insurance` ADD COLUMN `processed_by` TINYINT(20) NULL AFTER `modified_on`, ADD COLUMN `processed_on` DATETIME NULL AFTER `processed_by`;
ALTER TABLE `pnh_member_insurance_printlog` ADD COLUMN `status` TINYINT(11) DEFAULT 1 NULL AFTER `last_printed_on`; 
# ==========================================

# =========================================
#May_29_2014
ALTER TABLE `t_imei_no` ADD INDEX `imei_no` (`imei_no`), ADD INDEX `status` (`status`);
ALTER TABLE `pnh_member_fee` ADD INDEX `indexes` (`member_id`, `transid`, `invoice_no`); 
ALTER TABLE `pnh_member_insurance_printlog` ADD INDEX `indexes` (`insurance_id`, `printed_by`); 
ALTER TABLE `pnh_member_offers` ADD INDEX `Indexes` (`member_id`, `franchise_id`, `offer_type`, `pnh_pid`, `transid_ref`, `insurance_id`, `process_status`); 
ALTER TABLE `pnh_member_offers_log` ADD INDEX `Indexes` (`fid`, `mid`, `offer_type`, `status`); 

ALTER TABLE `pnh_member_offers` DROP INDEX `member_id`, DROP INDEX `franchise_id`, DROP INDEX `transid_ref`, DROP INDEX `order_id`, DROP INDEX `insurance_id`, DROP INDEX `member_id_2`;
ALTER TABLE `pnh_member_insurance` DROP INDEX `fid_2`; 
ALTER TABLE `pnh_member_insurance_menu` DROP INDEX `menu_id_2`; 

ALTER TABLE `pnh_t_receipt_reconcilation` ADD INDEX `Indexes` (`debit_note_id`, `invoice_no`, `dispatch_id`, `inv_amount`, `unreconciled`, `is_invoice_cancelled`);
ALTER TABLE `pnh_t_receipt_reconcilation_log` ADD INDEX `Indexes` (`credit_note_id`, `receipt_id`, `reconcile_id`, `is_invoice_cancelled`, `is_reversed`); 
ALTER TABLE `non_sk_imei_insurance_orders` ADD INDEX (`franchise_id`, `order_id`, `transid`, `userid`, `member_id`, `nonsk_imei_no`, `model_no`, `insurance_id`);

DROP TABLE `nonskimei_mem_fee_insu_amt`;

ALTER TABLE `pnh_member_fee` DROP INDEX `indexes`, ADD INDEX (`member_id`), ADD INDEX (`transid`), ADD INDEX (`invoice_no`), ADD INDEX (`status`);
ALTER TABLE `pnh_member_insurance` DROP INDEX `fid`, ADD INDEX (`fid`), ADD INDEX (`mid`), ADD INDEX (`menu_log_id`), ADD INDEX (`offer_type`), ADD INDEX (`proof_id`), ADD INDEX (`opted_insurance`), ADD INDEX (`offer_status`), ADD INDEX (`insurance_margin`), ADD INDEX (`itemid`), ADD INDEX (`order_id`), ADD INDEX (`mob_no`);
ALTER TABLE `pnh_member_insurance_menu` DROP INDEX `menu_id`, ADD INDEX (`menu_id`), ADD INDEX (`greater_than`), ADD INDEX (`less_than`), ADD INDEX (`insurance_margin`), ADD INDEX (`is_active`);
ALTER TABLE `pnh_member_insurance_printlog` DROP INDEX `indexes`, ADD INDEX (`insurance_id`), ADD INDEX (`printcount`), ADD INDEX (`printed_by`);
ALTER TABLE `pnh_member_offers` DROP INDEX `Indexes`, ADD INDEX (`member_id`), ADD INDEX (`franchise_id`), ADD INDEX (`offer_type`), ADD INDEX (`mem_fee_applicable`), ADD INDEX (`pnh_member_fee`), ADD INDEX (`pnh_pid`), ADD INDEX (`order_id`), ADD INDEX (`transid_ref`), ADD INDEX (`insurance_id`), ADD INDEX (`process_status`), ADD INDEX (`feedback_status`), ADD INDEX (`delivery_status`), ADD INDEX (`details_updated`); 
ALTER TABLE `pnh_member_offers_log` DROP INDEX `Indexes`, ADD INDEX (`fid`), ADD INDEX (`mid`), ADD INDEX (`offer_type`), ADD INDEX (`status`); 
ALTER TABLE `pnh_t_receipt_reconcilation` DROP INDEX `Indexes`, ADD INDEX (`debit_note_id`), ADD INDEX (`invoice_no`), ADD INDEX (`dispatch_id`), ADD INDEX (`inv_amount`), ADD INDEX (`unreconciled`), ADD INDEX (`is_invoice_cancelled`);
ALTER TABLE `pnh_t_receipt_reconcilation_log` DROP INDEX `Indexes`, ADD INDEX (`credit_note_id`), ADD INDEX (`receipt_id`), ADD INDEX (`reconcile_id`), ADD INDEX (`is_invoice_cancelled`), ADD INDEX (`is_reversed`);
ALTER TABLE `non_sk_imei_insurance_orders` DROP INDEX `franchise_id`, ADD INDEX (`franchise_id`), ADD INDEX (`order_id`), ADD INDEX (`transid`), ADD INDEX (`userid`), ADD INDEX (`member_id`), ADD INDEX (`nonsk_imei_no`), ADD INDEX (`model_no`), ADD INDEX (`insurance_id`);
# =========================================
# =========================================
 # May_30_2014
ALTER TABLE `pnh_member_offers_log` ADD COLUMN `offer_sno` BIGINT(20) NULL COMMENT 'manage offers sno' AFTER `sno`;

CREATE TABLE `disabled_batch_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `transid` VARCHAR(255) DEFAULT NULL,
  `remarks` TINYTEXT,
  `status` TINYINT(11) NOT NULL DEFAULT '1',
  `created_on` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `modified_on` DATETIME DEFAULT NULL,
  `modified_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
); 
# =========================================

SELECT * FROM pnh_member_offers mo 
JOIN pnh_member_insurance mi ON mi.sno = mo.insurance_id
WHERE mi.sno = '621';

SELECT mi.sno,mi.insurance_id,mi.fid,mi.mid,mi.offer_type,mi.proof_id,mi.proof_type,mi.proof_address,mi.opted_insurance,mi.offer_status,mi.insurance_value,mi.order_value,mi.itemid,mi.order_id
                                                    ,IFNULL(mi.first_name,minfo.first_name) AS first_name,IFNULL(mi.last_name,minfo.last_name) AS last_name
                                                    ,IFNULL(mi.mob_no,minfo.mobile) AS mob_no
                                                    ,IFNULL(mi.`city`,minfo.city) AS city
                                                    ,mi.pincode,mi.mem_receipt_no,mi.mem_receipt_date,mi.mem_receipt_amount
                                                    ,minfo.franchise_id,mo.* 
                                                    FROM pnh_member_insurance mi 
                                                    LEFT JOIN insurance_m_types mit ON mit.id = mi.proof_type
                                                    JOIN pnh_member_offers mo ON mo.insurance_id = mi.sno
                                                     JOIN pnh_member_info minfo ON minfo.pnh_member_id = mi.mid
                                                    WHERE mi.sno = '621';
                                                    
#================================================================================
#May_28_2014
ALTER TABLE `m_vendor_product_link` ADD COLUMN `vendor_group_no` INT (11)  NULL  AFTER `vendor_id`;

SELECT * FROM king_admin

SELECT * FROM m_employee_roles

#================================================================================
#Jun_05_2014
CREATE TABLE `m_employee_dept_link`( `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, `employee_id` INT(150), `dept_id` INT(150), `is_active` TINYINT(11), `created_on` DATETIME, `created_by` INT(50), `modified_on` DATETIME, `modified_by` INT(50), PRIMARY KEY (`id`) );
CREATE TABLE `m_departments`( `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(150), `keyword` VARCHAR(100), `created_on` DATETIME, `created_by` INT(50), `modified_on` DATETIME, `modified_by` INT(50), `status` TINYINT(11), PRIMARY KEY (`id`) );

INSERT INTO `m_departments` (`name`, `keyword`, `created_on`, `created_by`,`status`) VALUES ('Customer Support', 'CUSP', '2014-06-05 13:21:48', '1', '1'); 
INSERT INTO `m_departments` (`name`, `keyword`, `created_on`, `created_by`,`status`) VALUES ('Account and Finance', 'AF', '2014-06-05 13:22:16', '1', '1'); 
INSERT INTO `m_departments` (`name`, `keyword`, `created_on`, `created_by`,`status`) VALUES ('Sourcing', 'SRC', '2014-06-05 13:24:58', '1', '1'); 
INSERT INTO `m_departments` (`name`, `keyword`, `created_on`, `created_by`,`status`) VALUES ('Logistics', 'LOG', '2014-06-05 13:21:48', '1', '1');

ALTER TABLE `m_employee_roles` ADD COLUMN `status` TINYINT(11) DEFAULT 1 NULL AFTER `created_by`;
INSERT INTO `m_employee_roles` (`role_name`, `short_frm`, `created_on`, `created_by`,`status`) VALUES ('Office Emplolyee', 'OFFICE_EMP', '2014-06-03 18:47:03', '54', '0');
ALTER TABLE `m_employee_info` ADD COLUMN `emp_type` TINYINT(11) DEFAULT 2 NULL COMMENT '1:field emp,2:office emp' AFTER `remarks`;
#================================================================================
#Jun_06_2014
ALTER TABLE `pnh_franchise_requests` CHANGE `request_from` `from` VARCHAR(80) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT 'app' NULL, ADD COLUMN `type` VARCHAR(50) NULL AFTER `from`, CHANGE `request_desc` `title` VARCHAR(150) CHARSET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `status` `desc` TEXT NULL, CHANGE `created_on` `status` TINYINT(11) DEFAULT 1 NULL COMMENT '1:pending,2:done,3:cancelled', ADD COLUMN `created_on` DATETIME NULL AFTER `status`, ADD COLUMN `modified_on` DATETIME NULL AFTER `created_on`, ADD COLUMN `modified_by` INT(20) NULL AFTER `modified_on`;
#================================================================================

SELECT f.franchise_id,f.pnh_franchise_id,f.franchise_name,f.login_mobile1,f.address,f.city,f.postcode,f.state,u.user_id
						FROM pnh_m_franchise_info f 
						JOIN pnh_users u ON u.franchise_id=f.franchise_id
						WHERE u.user_id='2'

UPDATE `pnh_member_insurance` SET status_ship WHERE id IN ('sdasd');

SELECT * FROM pnh_member_insurance WHERE status_ship=1;

#====================================================================
#Jun_09_2014
INSERT INTO `m_departments` (`name`, `keyword`,`created_on`,`created_by`, `status` ) VALUES ('Software', 'TECH','2014-06-09 17:12:32' ,'1' ,'1');
UPDATE `m_departments` SET `name` = 'Techical' WHERE `id` = '5';
#====================================================================

#Jun_11_2014

UPDATE `snapitto_erpsndx`.`support_tickets_msg` SET `msg_type` = '10' WHERE `id` = '8693'; 
UPDATE `snapitto_erpsndx`.`support_tickets` SET `type` = '10' WHERE `ticket_id` = '1595'; 

SELECT * FROM support_tickets ORDER BY ticket_id DESC LIMIT 10;

SELECT * FROM support_tickets_msg ORDER BY id DESC LIMIT 10;
#====================================================================
#Jun_12_2014
ALTER TABLE `pnh_member_insurance` ADD INDEX (`status_ship`);
#====================================================================
#===============MEMBER PRICE DB CHANGES==========================================
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `price_type` TINYINT(1) DEFAULT 0 NULL AFTER `new_credit_limit`; 
ALTER TABLE `king_orders` ADD COLUMN `member_price` DOUBLE DEFAULT 0 NULL AFTER `redeem_value`, ADD COLUMN `is_memberprice` TINYINT(1) DEFAULT 0 NULL AFTER `partner_order_id`; 
ALTER TABLE `king_dealitems` ADD COLUMN `member_price` INT(10) UNSIGNED DEFAULT 0 NOT NULL AFTER `price`; 

CREATE TABLE `franchise_pricetype_log`( `id` INT(11) NOT NULL AUTO_INCREMENT, `franchise_id` BIGINT(11), `price_type` TINYINT(1), `modified_on` BIGINT(25), `modified_by` INT(11), PRIMARY KEY (`id`) ); 
ALTER TABLE `franchise_pricetype_log` ADD COLUMN `reason` VARCHAR(255) NULL AFTER `price_type`; 
ALTER TABLE `pnh_sch_discount_brands` ADD COLUMN `price_type` TINYINT(1) DEFAULT 0 NOT NULL AFTER `is_sch_enabled`; 
#===============MEMBER PRICE DB CHANGES==========================================
#Jun_14_2014
ALTER TABLE `king_orders` ADD COLUMN `other_price` DOUBLE DEFAULT 0 NULL AFTER `member_price`;
#===============MEMBER PRICE DB CHANGES==========================================
#Jun_14_2014
SELECT DATE_FORMAT(FROM_UNIXTIME(init),'%d/%m/%Y') AS ordererd_on,o.status,o.transid,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount) + o.pnh_member_fee + o.insurance_amount)*o.quantity),2) AS order_total 
FROM king_transactions t 
JOIN king_orders o ON o.transid=t.transid AND o.status NOT IN(3) 
WHERE t.transid='PNHHKL21931' AND t.franchise_id='17' AND DATE(FROM_UNIXTIME(t.init))=CURDATE();

SELECT * FROM king_transactions WHERE transid='PNHHKL21931';

SELECT mi.*,a.username,mo.transid_ref AS transid,mo.process_status,mo.delivery_status,o.id AS orderid,f.franchise_name,i.invoice_no,IFNULL(imei.imei_no,nonsk_imei.nonsk_imei_no) AS imei_no
	FROM pnh_member_insurance mi
	JOIN pnh_member_offers mo ON mo.insurance_id = mi.sno
	JOIN king_admin a ON a.id = mi.created_by
	JOIN pnh_m_franchise_info f ON f.franchise_id = mi.fid
	JOIN king_orders o ON o.id = mi.order_id AND o.status != 3
	JOIN king_invoice i ON i.order_id = mi.order_id 
	LEFT JOIN t_imei_no imei ON imei.order_id = o.id
	LEFT JOIN non_sk_imei_insurance_orders nonsk_imei ON nonsk_imei.order_id=o.id
	WHERE mi.sno IN ('627') 
	GROUP BY mi.sno;

# Jun_16_2014

SELECT GROUP_CONCAT(DISTINCT dept_id) AS dept_ids FROM m_dept_request_type_link WHERE type_id=1

SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name FROM m_dept_request_type_link rlink
LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
WHERE type_id=1 AND rlink.is_active=1;


=> Dept_id: 1

SELECT emp.employee_id,emp.name,emp.email,emp.contact_no
	FROM m_departments dt
	JOIN m_employee_dept_link edl ON edl.dept_id = dt.id
	JOIN m_employee_info emp ON emp.employee_id = edl.employee_id
	WHERE dt.id IN ('1') AND job_title=9 AND emp.email !=''
	
DESC king_dealitems

SELECT * FROM support_tickets WHERE ticket_id='1614'
SELECT * FROM support_tickets_msg WHERE ticket_id='1614'

SELECT msg FROM support_tickets_msg WHERE ticket_id='1614' AND MEDIUM=0 
#and msg_type=1 
AND from_customer=1 ORDER BY id DESC;

-- new ---
SELECT * FROM king_dealitems
#Jun_17_2014
ALTER TABLE `deal_price_changelog` ADD COLUMN `old_member_price` DECIMAL(10,2) NULL AFTER `old_price`, ADD COLUMN `new_member_price` DECIMAL(10,2) NULL AFTER `old_member_price`;
ALTER TABLE `king_dealitems` CHANGE `member_price` `member_price` DECIMAL(10,2) UNSIGNED DEFAULT 0 NOT NULL, ADD COLUMN `mp_max_qty` INT(10) NULL AFTER `member_price`, ADD COLUMN `mp_max_order_qty` INT(10) DEFAULT 0 NULL AFTER `mp_max_qty`;
CREATE TABLE `deal_member_price_changelog` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemid` BIGINT(20) UNSIGNED NOT NULL,
  `old_member_price` DECIMAL(10,2) DEFAULT NULL,
  `new_member_price` DECIMAL(10,2) DEFAULT NULL,
  `new_mp_max_qty`INT(10) UNSIGNED NOT NULL,
  `old_mp_max_qty`INT(10) UNSIGNED NOT NULL,
  `new_mp_max_order_qty` INT(10) UNSIGNED NOT NULL,
  `old_mp_max_order_qty`INT(10) UNSIGNED NOT NULL,
  `created_by` INT(10) UNSIGNED NOT NULL,
  `created_on` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`)
);
#=============================== 

SELECT * FROM pnh_users;

SELECT * FROM ( SELECT tr.transid,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name,di.pnh_id,CONCAT('http://static.snapittoday.com/items/',di.pic,'.jpg') AS image_url
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no 
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid 
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1 
					
                    JOIN king_dealitems di ON di.id = o.itemid 
                    WHERE o.status=3 AND f.franchise_id=316 # tr.transid = 'PNHBJU41216'   and 
                    ORDER BY tr.init,di.name) AS g GROUP BY g.transid;
                    
#=============================== 
CREATE TABLE `pnh_t_returns_transit_log` (
  `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT(15) DEFAULT NULL,
  `return_id` BIGINT(11) DEFAULT NULL,
  `ticket_id` BIGINT(11) DEFAULT NULL,
  `transit_mode` TINYINT(1) DEFAULT '0',
  `courier` VARCHAR(255) DEFAULT NULL,
  `awb` VARCHAR(255) DEFAULT NULL,
  `emp_phno` INT(11) DEFAULT '0',
  `emp` INT(255) DEFAULT NULL,
  `status` TINYINT(1) DEFAULT '0',
  `logged_on` BIGINT(25) DEFAULT NULL,
  `logged_by` TINYINT(1) DEFAULT NULL,
  `returned_on` BIGINT(25) DEFAULT NULL,
  `returned_by` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id`)
);

#Jun_23_2014

SELECT * FROM king_admin WHERE `name` LIKE '%franchise%'

#=============================================================
#Jun_23_2014
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_is_offer` TINYINT(11) DEFAULT 0 NULL AFTER `old_mp_max_order_qty`, ADD COLUMN `mp_offer_from` DATETIME NULL AFTER `mp_is_offer`, ADD COLUMN `mp_offer_to` DATETIME NULL AFTER `mp_offer_from`; 
ALTER TABLE `king_dealitems` ADD COLUMN `mp_is_offer` TINYINT(11) NULL AFTER `mp_max_order_qty`, ADD COLUMN `mp_offer_from` DATETIME NULL AFTER `mp_is_offer`, ADD COLUMN `mp_offer_to` DATETIME NULL AFTER `mp_offer_from`; 
# API changes
INSERT INTO `king_admin` (`user_id`, `name`, `username`, `password`, `usertype`, `access`, `brandid`, `fullname`, `email`, `createdon`, `modifiedon`) VALUES ('0ec7bd10ef5d2c99e7121241f8f12d1f', 'Franchise/API', 'franchise', 'dc21afd02d0c2edb2bee3ead8da23e1f', '1', '0', '0', '', 'franchise@storeking.in', '2014-06-23 18:19:21', '2014-06-23 18:19:23'); 
#===========================================


#jun_24 2014
#=============================== 
ALTER TABLE `shipment_batch_process_invoice_link` CHANGE `is_delivered` `is_delivered` INT (11)  NOT NULL;

-- Insurances list:

-- 150, 151, 235, 358, 
-- 584, 589, 590, 730, 770, 787, 877, 878, 891, 956, 971, 972, 990, 991, 992, 1089, 1093, 1138, 1157, 1176, 1218, 1239, 1240, 1242, 1284, 1322, 1362 

SELECT a.id,a.itemid,a.old_mrp,a.new_mrp,a.old_price,a.new_price,a.reference_grn,a.created_by,a.created_on,b.username AS logged_by#,mp.old_member_price,mp.new_member_price 
FROM deal_price_changelog a 
#left JOIN deal_member_price_changelog mp ON mp.itemid = a.itemid 
LEFT JOIN king_admin b ON a.created_by = b.id 
WHERE a.itemid='6623348295' ORDER BY a.id DESC


SELECT b.product_id,IFNULL(SUM(o.quantity*l.qty),0) AS qty  
		FROM m_product_deal_link l 
		LEFT JOIN king_orders o ON o.itemid=l.itemid 
		JOIN t_paf_productlist b ON b.product_id = l.product_id 
		WHERE b.paf_id = '0' AND 
		o.status = 0   
	GROUP BY b.product_id

SELECT * FROM deal_member_price_changelog WHERE itemid='6623348295';


SELECT b.product_id,SUM(available_qty) AS qty  
	FROM t_stock_info a 
	JOIN t_paf_productlist b ON a.product_id = b.product_id 
	WHERE b.paf_id = 0
	GROUP BY b.product_id;
	
	
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
	WHERE 1  AND STATUS = ?  ORDER BY a.sent_on DESC LIMIT 0 , 10;
	
#jun_24 2014
#=============================== 
SELECT COUNT(*) AS ttl  
	FROM king_deals dl 
	JOIN king_dealitems di ON dl.dealid = di.dealid
	 JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1
	JOIN m_product_info e ON e.product_id = c.product_id      
	WHERE di.is_pnh = 1  

	ORDER BY di.name# LIMIT 0,100

SELECT * FROM king_admin

#===========================================
# Jun_25_2014
ALTER TABLE `king_dealitems` ADD COLUMN `mp_offer_note` TEXT NULL AFTER `mp_offer_to`;
ALTER TABLE `deal_member_price_changelog` ADD COLUMN `mp_offer_note` TEXT NULL AFTER `mp_offer_to`;
ALTER TABLE `deal_member_price_changelog` CHANGE `is_active` `is_active` TINYINT(1) DEFAULT 1 NOT NULL AFTER `mp_offer_note`, ADD COLUMN `modified_on` DATETIME NULL AFTER `created_on`, ADD COLUMN `modified_by` INT(10) NULL AFTER `modified_on`;
# Jun_26_2014
ALTER TABLE `deal_member_price_changelog` CHANGE `new_mp_max_qty` `new_mp_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_qty` `old_mp_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `new_mp_max_order_qty` `new_mp_max_order_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_order_qty` `old_mp_max_order_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL;
#===========================================
#=============================================
#Jun_26_2014
ALTER TABLE `king_dealitems` CHANGE `mp_max_qty` `mp_frn_max_qty` INT(10) DEFAULT 0 NULL, CHANGE `mp_max_order_qty` `mp_mem_max_qty` INT(10) DEFAULT 0 NULL;
ALTER TABLE `deal_member_price_changelog` CHANGE `new_mp_max_qty` `new_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_qty` `old_mp_frn_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `new_mp_max_order_qty` `new_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL, CHANGE `old_mp_max_order_qty` `old_mp_mem_max_qty` INT(10) UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE `king_dealitems` CHANGE `mp_frn_max_qty` `mp_frn_max_qty` INT(10) DEFAULT 0 NOT NULL;
#=============================================

ALTER TABLE `shipment_batch_process_invoice_link` CHANGE `is_delivered` `is_delivered` INT (11)  NOT NULL;

ALTER TABLE `m_employee_roles` CHANGE `status` `emp_role_status` TINYINT(11) DEFAULT 1 NULL;


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
			  		WHERE i.id='8426452442' OR i.pnh_id=''
			  		
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
			  		WHERE i.id='8426452442' OR i.pnh_id='8426452442';
			  		
SELECT * FROM 1159524

SELECT * FROM deal_member_price_changelog WHERE itemid='2749173323';
SELECT * FROM king_dealitems WHERE id='2749173323'

SELECT * FROM deal_member_price_changelog WHERE itemid='7353895357';
SELECT * FROM king_dealitems WHERE id='7353895357'

#===========================================
#jun_25_2014

SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id = '59';

SELECT mpl.* FROM deal_member_price_changelog mpl WHERE mpl.itemid='7353895357' ORDER BY mpl.id DESC

DESC deal_member_price_changelog

SELECT * FROM deal_member_price_changelog WHERE itemid='5124364193';
SELECT * FROM king_dealitems WHERE id='7353895357';


ALTER TABLE `m_client_category_link` ADD COLUMN `main_category` VARCHAR (255)  NULL  AFTER `client_category`;
ALTER TABLE `m_vendor_api_templates` ADD COLUMN `spec26` VARCHAR (255)  NULL  AFTER `spec25`, ADD COLUMN `spec27` VARCHAR (255)  NULL  AFTER `spec26`, ADD COLUMN `spec28` VARCHAR (255)  NULL  AFTER `spec27`, ADD COLUMN `spec29` VARCHAR (255)  NULL  AFTER `spec28`, ADD COLUMN `spec30` VARCHAR (255)  NULL  AFTER `spec29`, ADD COLUMN `spec31` VARCHAR (255)  NULL  AFTER `spec30`, ADD COLUMN `spec32` VARCHAR (255)  NULL  AFTER `spec31`, ADD COLUMN `spec33` VARCHAR (255)  NULL  AFTER `spec32`, ADD COLUMN `spec34` VARCHAR (255)  NULL  AFTER `spec33`, ADD COLUMN `spec35` VARCHAR (255)  NULL  AFTER `spec34`, ADD COLUMN `spec36` VARCHAR (255)  NULL  AFTER `spec35`, ADD COLUMN `spec37` VARCHAR (255)  NULL  AFTER `spec36`, ADD COLUMN `spec38` VARCHAR (255)  NULL  AFTER `spec37`, ADD COLUMN `spec39` VARCHAR (255)  NULL  AFTER `spec38`, ADD COLUMN `spec40` VARCHAR (255)  NULL  AFTER `spec39`, ADD COLUMN `spec41` VARCHAR (255)  NULL  AFTER `spec40`, ADD COLUMN `spec42` VARCHAR (255)  NULL  AFTER `spec41`, ADD COLUMN `spec43` VARCHAR (255)  NULL  AFTER `spec42`, ADD COLUMN `spec44` VARCHAR (255)  NULL  AFTER `spec43`, ADD COLUMN `spec45` VARCHAR (255)  NULL  AFTER `spec44`, ADD COLUMN `spec46` VARCHAR (255)  NULL  AFTER `spec45`, ADD COLUMN `spec47` VARCHAR (255)  NULL  AFTER `spec46`, ADD COLUMN `spec48` VARCHAR (255)  NULL  AFTER `spec47`, ADD COLUMN `spec49` VARCHAR (255)  NULL  AFTER `spec48`, ADD COLUMN `spec50` VARCHAR (255)  NULL  AFTER `spec49`, ADD COLUMN `spec51` VARCHAR (255)  NULL  AFTER `spec50`, ADD COLUMN `spec52` VARCHAR (255)  NULL  AFTER `spec51`, ADD COLUMN `spec53` VARCHAR (255)  NULL  AFTER `spec52`, ADD COLUMN `spec54` VARCHAR (255)  NULL  AFTER `spec53`, ADD COLUMN `spec55` VARCHAR (255)  NULL  AFTER `spec54`, ADD COLUMN `spec56` VARCHAR (255)  NULL  AFTER `spec55`, ADD COLUMN `spec57` VARCHAR (255)  NULL  AFTER `spec56`, ADD COLUMN `spec58` VARCHAR (255)  NULL  AFTER `spec57`, ADD COLUMN `spec59` VARCHAR (255)  NULL  AFTER `spec58`, ADD COLUMN `spec60` VARCHAR (255)  NULL  AFTER `spec59`,CHANGE `spec25` `spec25` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci;
ALTER TABLE `m_vendor_api_datalog` ADD COLUMN `spec26` VARCHAR (255)  NULL  AFTER `spec25`, ADD COLUMN `spec27` VARCHAR (255)  NULL  AFTER `spec26`, ADD COLUMN `spec28` VARCHAR (255)  NULL  AFTER `spec27`, ADD COLUMN `spec29` VARCHAR (255)  NULL  AFTER `spec28`, ADD COLUMN `spec30` VARCHAR (255)  NULL  AFTER `spec29`, ADD COLUMN `spec31` VARCHAR (255)  NULL  AFTER `spec30`, ADD COLUMN `spec32` VARCHAR (255)  NULL  AFTER `spec31`, ADD COLUMN `spec33` VARCHAR (255)  NULL  AFTER `spec32`, ADD COLUMN `spec34` VARCHAR (255)  NULL  AFTER `spec33`, ADD COLUMN `spec35` VARCHAR (255)  NULL  AFTER `spec34`, ADD COLUMN `spec36` VARCHAR (255)  NULL  AFTER `spec35`, ADD COLUMN `spec37` VARCHAR (255)  NULL  AFTER `spec36`, ADD COLUMN `spec38` VARCHAR (255)  NULL  AFTER `spec37`, ADD COLUMN `spec39` VARCHAR (255)  NULL  AFTER `spec38`, ADD COLUMN `spec40` VARCHAR (255)  NULL  AFTER `spec39`, ADD COLUMN `spec41` VARCHAR (255)  NULL  AFTER `spec40`, ADD COLUMN `spec42` VARCHAR (255)  NULL  AFTER `spec41`, ADD COLUMN `spec43` VARCHAR (255)  NULL  AFTER `spec42`, ADD COLUMN `spec44` VARCHAR (255)  NULL  AFTER `spec43`, ADD COLUMN `spec45` VARCHAR (255)  NULL  AFTER `spec44`, ADD COLUMN `spec46` VARCHAR (255)  NULL  AFTER `spec45`, ADD COLUMN `spec47` VARCHAR (255)  NULL  AFTER `spec46`, ADD COLUMN `spec48` VARCHAR (255)  NULL  AFTER `spec47`, ADD COLUMN `spec49` VARCHAR (255)  NULL  AFTER `spec48`, ADD COLUMN `spec50` VARCHAR (255)  NULL  AFTER `spec49`, ADD COLUMN `spec51` VARCHAR (255)  NULL  AFTER `spec50`, ADD COLUMN `spec52` VARCHAR (255)  NULL  AFTER `spec51`, ADD COLUMN `spec53` VARCHAR (255)  NULL  AFTER `spec52`, ADD COLUMN `spec54` VARCHAR (255)  NULL  AFTER `spec53`, ADD COLUMN `spec55` VARCHAR (255)  NULL  AFTER `spec54`, ADD COLUMN `spec56` VARCHAR (255)  NULL  AFTER `spec55`, ADD COLUMN `spec57` VARCHAR (255)  NULL  AFTER `spec56`, ADD COLUMN `spec58` VARCHAR (255)  NULL  AFTER `spec57`, ADD COLUMN `spec59` VARCHAR (255)  NULL  AFTER `spec58`, ADD COLUMN `spec60` VARCHAR (255)  NULL  AFTER `spec59`,CHANGE `spec25` `spec25` VARCHAR (255)  NULL  COLLATE latin1_swedish_ci;
ALTER TABLE `m_vendor_product_link` CHANGE `vendor_group_no` `vendor_group_no` VARBINARY (255)  NULL ;                           

#Jun_26_2014

SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by,b.bank_name AS submit_bankname,c.submitted_on,s.name AS submitted_by,c.remarks AS submittedremarks 
	FROM pnh_t_receipt_info r 
	JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
	LEFT JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id 
	LEFT OUTER JOIN king_admin a ON a.id=r.created_by 
	LEFT OUTER JOIN king_admin d ON d.id=r.activated_by

	LEFT JOIN `pnh_m_bank_info` b ON b.id=c.bank_id 
	LEFT JOIN king_admin s ON s.id=c.submitted_by 
	WHERE r.status=1 AND r.is_active=1 AND (r.is_submitted=1 OR r.activated_on!=0) AND r.is_active=1 AND r.franchise_id='316'  AND r.receipt_id='7709'
	ORDER BY activated_on DESC
	
#Jun_27_2014
ALTER TABLE `king_orders` ADD COLUMN `mp_logid` BIGINT(11) DEFAULT 0 NULL AFTER `is_memberprice`;
#=========================
#Jun_28_2014
ALTER TABLE `support_tickets` ADD COLUMN `req_mem_name` VARCHAR(80) NULL AFTER `related_to`, ADD COLUMN `req_mem_mobile` VARCHAR(50) NULL AFTER `req_mem_name`;
#=========================

#Jun_30_2014

 52=>498
DESC king_dealitems

#Jul_01_2014

SELECT di.pnh_id,di.id,di.name,di.orgprice,di.price,di.member_price,di.mp_frn_max_qty,di.mp_mem_max_qty,di.mp_max_allow_qty,di.mp_is_offer,DATE_FORMAT(di.mp_offer_from,'%d/%b/%Y %H:%i') AS mp_offer_from,DATE_FORMAT(di.mp_offer_to,'%d/%b/%Y %H:%i') AS mp_offer_to,di.mp_offer_note,di.shipsin
,di.live,dl.publish,e.product_id,e.is_sourceable,IF(di.mp_offer_to > NOW(),1,0) AS validity 
FROM king_deals dl  
JOIN king_dealitems di ON dl.dealid = di.dealid 
LEFT JOIN m_product_deal_link c ON c.itemid = di.id AND c.is_active = 1 
LEFT JOIN m_product_info e ON e.product_id = c.product_id       
WHERE di.is_pnh = 1  AND dl.publish = 1 AND di.live=1 
 AND dl.menuid = ?  AND dl.brandid = ?  AND dl.catid = ?  
ORDER BY di.name LIMIT ALL,30

SELECT (5000/100)*100;


SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity,di.mp_is_offer,di.mp_offer_from,di.mp_offer_to
	,IF(di.mp_offer_to IS NULL,di.mp_offer_to,IF(di.mp_offer_to > NOW(),1,0) ) AS validity
#,IF(di.mp_offer_to > NOW(),1, IFNULL(di.mp_offer_to,di.mp_offer_to) ) AS validity
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE di.mp_is_offer = 1 #AND di.id='3527475588' AND o.i_price='1854' AND FROM_UNIXTIME(tr.init) BETWEEN di.mp_offer_from AND di.mp_offer_to
	#group by di.id
	ORDER BY tr.init DESC
	LIMIT 5;
	
SELECT member_price,mp_max_allow_qty,mp_frn_max_qty,mp_mem_max_qty,mp_is_offer FROM king_dealitems WHERE id='6623348295';

SELECT new_member_price,mp_max_allow_qty,new_mp_frn_max_qty,new_mp_mem_max_qty,mp_is_offer FROM deal_member_price_changelog WHERE itemid='6623348295' AND is_active=1;

SELECT * FROM pnh_member_offers ORDER BY sno DESC LIMIT 10;

SELECT * FROM pnh_member_insurance ORDER BY sno DESC LIMIT 10;


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

Jul_05_2014

SELECT * form pnh_manifesto_log WHERE invoice_no='20141025396';

SELECT o.itemid,di.member_price,SUM(o.quantity) AS quantity,GROUP_CONCAT(o.id) AS oids,mplog.id AS logid,mplog.is_active
	FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	LEFT JOIN king_dealitems di ON di.id=o.itemid
	LEFT JOIN deal_member_price_changelog mplog ON mplog.itemid = di.id
	WHERE  o.is_memberprice=1 AND mplog.id = o.mp_logid #AND di.id='6623348295'  AND o.i_price='16750.00' #and di.mp_is_offer = 1#AND tr.init BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?)
	GROUP BY di.id
	HAVING quantity IS NOT NULL
	ORDER BY tr.init DESC;
#rows 6 => 41
#=>5535932891(10),5342466337(17)
#================== get todays order for mem ==================
SELECT o.itemid,o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice FROM king_orders o
		JOIN king_transactions tr ON tr.transid=o.transid
		#LEFT JOIN king_dealitems di ON di.id=o.itemid
		LEFT JOIN deal_member_price_changelog mplog ON mplog.itemid = o.itemid
		WHERE o.status !=3 AND o.is_memberprice=1   AND o.itemid='5535932891'  AND mplog.id = o.mp_logid #AND o.member_id=? AND o.i_price=? AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = $date
		HAVING quantity IS NOT NULL 
		ORDER BY o.time DESC;
#========================================
#Jul_07_2014
ALTER TABLE `king_dealitems` CHANGE mp_offer_note mp_offer_note VARCHAR(2000) DEFAULT '';
ALTER TABLE `deal_member_price_changelog` CHANGE mp_offer_note mp_offer_note VARCHAR(2000) DEFAULT '';

UPDATE deal_member_price_changelog SET mp_offer_note='' WHERE mp_offer_note IS NULL;
UPDATE king_dealitems SET mp_offer_note='' WHERE mp_offer_note IS NULL;
#=>94656 row(s)=>71176 row(s)
#========================================

SELECT * FROM king_orders WHERE transid='PNHHDP67183';

SELECT transid,is_ordqty_splitd FROM king_orders WHERE transid='PNHXDD27488' ORDER BY id DESC LIMIT 4;

SELECT price_type FROM pnh_m_franchise_info WHERE franchise_id='498' LIMIT 1;

SELECT o.* FROM king_user_orders o
 LEFT JOIN user_order_transactions t ON t.transid=o.transid 
 WHERE t.transid LIKE  '%94944%'  AND t.franchise_id=498 LIMIT 1

SELECT * FROM king_user_orders WHERE transid=

#========================================

DROP TABLE user_order_transactions;

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

DROP TABLE king_user_orders;

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
  `loyality_point_value` DOUBLE DEFAULT '0',
  `is_memberprice` TINYINT(1) DEFAULT '0',
  `other_price` DOUBLE DEFAULT '0',
  `mp_logid` BIGINT(11) DEFAULT '0',
  PRIMARY KEY (`sno`),
  KEY `transid` (`transid`),
  KEY `itemid` (`itemid`),
  KEY `userid` (`userid`),
  KEY `id` (`id`),
  KEY `status` (`status`)
);

#=====================================
#Jul_15_2014 - To filter the tickets by Web or APP
ALTER TABLE `support_tickets` ADD COLUMN `from_app` TINYINT(11) DEFAULT 0 NOT NULL COMMENT '0:from web,1:from api/app' AFTER `req_mem_mobile`;
ALTER TABLE `support_tickets` CHANGE `franchise_id` `franchise_id` BIGINT(20) DEFAULT 0 NULL;
#=====================================

ALTER TABLE `pnh_payment_info` ADD COLUMN `amount` DOUBLE NULL AFTER `franchise_id`;

SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name AS request_name FROM m_dept_request_type_link rlink
											LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
											WHERE rlink.is_active=1 AND 
											rlink.type_id = '1'
											HAVING dept_ids IS NOT NULL;
											
SELECT ed.employee_id,ed.is_active,d.name AS dept_name,d.created_by
											FROM `m_employee_dept_link` ed
											JOIN m_departments d ON d.id = ed.dept_id
											WHERE ed.is_active = 1 AND ed.employee_id= '89';
											
											SELECT * FROM user_order_transactions ORDER BY id DESC LIMIT 20
											SELECT o.* FROM king_user_orders o JOIN user_order_transactions t ON t.transid=o.transid WHERE t.transid LIKE  '%94944%'  AND t.franchise_id=498 LIMIT 1
											HAVING dept_name IS NOT NULL;
											
SELECT * FROM king_admin WHERE username = 'shivaraj' AND PASSWORD = MD5('shivaraj')
SELECT MD5('shivaraj');

17732816

ALTER TABLE `king_user_orders` ADD COLUMN `is_memberprice` TINYINT(1) DEFAULT 0 NULL AFTER `loyality_point_value`, ADD COLUMN `other_price` DOUBLE DEFAULT 0 NULL AFTER `is_memberprice`;

DESC king_user_orders

SELECT * FROM king_user_orders ORDER BY id DESC LIMIT 20


#=====================< To get current running offers >=====================
SELECT #c.id,
	pnh_id AS pid,NAME,orgprice AS mrp,price,ROUND((price/orgprice)*100) AS disc,CONCAT('http://static.snapittoday.com/items/small/',b.pic,'.jpg') AS pimg_link,a.mp_offer_to
	FROM king_dealitems a 
	JOIN king_deals b ON a.dealid = b.dealid
	#JOIN m_apk_store_menu_link c ON c.menu_id = b.menuid  
	WHERE is_pnh=1 #AND store_id=? 
	AND publish = 1 AND a.mp_is_offer=1  AND UNIX_TIMESTAMP() BETWEEN UNIX_TIMESTAMP(a.mp_offer_from) AND UNIX_TIMESTAMP(a.mp_offer_to)
	GROUP BY a.id;
#====================================================================


#<!---Modification Done by Roopashree<roopashree@storeking.in> Modified Date 19/july/2014------>
#=============================Franchise Asset/Device Information====================================================#
CREATE TABLE `m_asset_info`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) );
CREATE TABLE `m_accessory_info`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `name` VARCHAR(255), PRIMARY KEY (`id`) );
CREATE TABLE `m_franchise_asset_link`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `franchise_id` BIGINT(20), `asset_id` BIGINT(20), `accessory_id` BIGINT(20), `is_active` TINYINT(1) DEFAULT 1, `created_on` BIGINT(20), `created_by` INT(11), `modified_on` BIGINT(20), `modified_by` INT(11), PRIMARY KEY (`id`) );
CREATE TABLE `m_franchise_store_link`( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `franchise_id` BIGINT(20), `store_id` INT(11), `is_active` TINYINT(1), `created_on` BIGINT(20), `created_by` INT(11), `modified_on` BIGINT(20), `modified_by` INT(11), PRIMARY KEY (`id`) );
#=============================Franchise Store type Info====================================================#

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
  `loyality_point_value` DOUBLE DEFAULT '0',
  `is_memberprice` TINYINT(1) DEFAULT '0',
  `other_price` DOUBLE DEFAULT '0',
  `mp_logid` BIGINT(11) DEFAULT '0',
  PRIMARY KEY (`sno`),
  KEY `transid` (`transid`),
  KEY `itemid` (`itemid`),
  KEY `userid` (`userid`),
  KEY `id` (`id`),
  KEY `status` (`status`)
);

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
                    
SELECT id,mp_is_offer,mp_offer_from,mp_offer_to,is_group,i.name FROM king_dealitems i WHERE mp_is_offer IS NOT NULL AND pnh_id='19996856';//20003689

SELECT mp_is_offer,mp_offer_from,mp_offer_to,is_active FROM deal_member_price_changelog WHERE itemid='1874667575';

SELECT id,mp_is_offer,mp_offer_from,mp_offer_to,is_group,i.name FROM king_dealitems i WHERE pnh_id='19996856';

SELECT id FROM king_dealitems WHERE is_group=0;
#=============================
#JUl_23_2014
CREATE TABLE `m_master_franchise_info` (  `id` INT (11) NOT NULL AUTO_INCREMENT , `name` VARCHAR (255) , `address` VARCHAR (2024) , `city` VARCHAR (255) , `state` VARCHAR (255) , `town_id` INT (11) DEFAULT '0', `terr_id` INT (11) DEFAULT '0', `created_on` DATETIME , `modified_on` DATETIME , `created_by` INT (11) DEFAULT '0', `modified_by` INT (11) DEFAULT '0', PRIMARY KEY ( `id`)) ;
ALTER TABLE `pnh_m_franchise_info` ADD COLUMN `master_franchise_id` INT (11) DEFAULT '0' NULL  AFTER `pnh_franchise_id`;
ALTER TABLE `pnh_users` CHANGE `franchise_id` `reference_id` BIGINT(20) NULL COMMENT 'franchise_id,m_fid';
#=============================
#Jul_24_2014
ALTER TABLE `t_imei_no` ADD COLUMN `reserved_batch_rowid` BIGINT(20) DEFAULT 0 NULL AFTER `modified_on`; 

SELECT remarks FROM m_product_info WHERE remarks!='' AND remarks IS NOT NULL;
#=================================================
#Jul_24_2014
ALTER TABLE `products_src_changelog` ADD COLUMN `remarks` VARCHAR(200) NULL AFTER `is_sourceable`;
#=================================================
#Jul_24_2014
ALTER TABLE `products_src_changelog` ADD COLUMN `remarks` VARCHAR(200) NULL AFTER `is_sourceable`;
#=================================================


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

SELECT shipsin FROM king_dealitems WHERE id='8845721562';
#=====================================================
#Jul_26_2014
# Set mp log table shipsin value '24-48 Hrs' for all null of empty
UPDATE deal_member_price_changelog 
SET mp_offer_shipsin='24-48 Hrs'
WHERE mp_offer_shipsin IS NULL OR mp_offer_shipsin='';

#Set dealitems shipsin value to '24-48 Hrs' for all null of empty
UPDATE king_dealitems
SET shipsin='24-48 Hrs'
WHERE shipsin IS NULL OR shipsin='';
#=====================================================

SELECT * FROM support_tickets WHERE ticket_id='1692';

SELECT * FROM m_dept_request_types rt ORDER BY rt.name ASC;
`m_dept_request_types`


SELECT * FROM king_admin ORDER BY id DESC;

bharath.mc@storeking.in
SELECT MD5('bharath'); =>7616b81196ee6fe328497da3f1d9912d

SELECT MD5('akshay'); => 2de1b2d6a6738df78c5f9733853bd170


SELECT t.ticket_id,t.ticket_no,t.user_id,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on,t.from_app
				,u.name AS `user`,a.name AS assignedto,f.franchise_name
				,rt.id AS related_to_id,rt.name AS related_to_name
				,GROUP_CONCAT(DISTINCT dlnk.dept_id,':',dept.name ORDER BY dept.name) AS dept_dets
				FROM support_tickets t 
				LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to 
				LEFT OUTER JOIN king_users u ON u.userid=t.user_id
				LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id

				LEFT OUTER JOIN m_dept_request_types rt ON rt.id=t.related_to
				LEFT OUTER JOIN m_dept_request_type_link dlnk ON dlnk.type_id = t.related_to AND dlnk.is_active='1'
				LEFT OUTER JOIN m_departments dept ON dept.id = dlnk.dept_id
				WHERE 1
				GROUP BY t.ticket_id;
				
#=>1692

#Jul_30_2014

SELECT * FROM king_deals ORDER BY id DESC LIMIT 10;

SELECT d.menuid,b.name AS brand,c.name AS cat,i.id,i.is_combo,i.pnh_id AS pid,i.live,i.orgprice AS mrp,i.price,i.name,i.pic,d.publish,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name 
FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid 
LEFT JOIN king_brands b ON b.id = d.brandid JOIN king_categories c ON c.id = d.catid 
WHERE is_pnh=1 AND pnh_id=?;

SELECT * FROM pnh_super_scheme WHERE menu_id=? AND is_active=1 AND franchise_id = '' LIMIT 1;

#==============================================

SELECT brand_id,brand_name,g.product_id,product_name,order_date,(ttl_req_qty) AS ttl_req_qty,SUM(IFNULL(s.available_qty,0)) AS avail_qty,g.mrp,purchase_cost,fran_order_det,dp_price,member_price,po_id,is_sourceable
	FROM 
	((SELECT c.brand_id AS brand_id,d.name AS brand_name,
			c.product_id,TRIM(c.product_name) AS product_name,
			SUM(a.quantity*b.qty) AS ttl_req_qty,
			DATE(FROM_UNIXTIME(a.time)) AS order_date,
			c.mrp,c.purchase_cost,
			GROUP_CONCAT(DISTINCT CONCAT(a.transid,':',UNIX_TIMESTAMP(DATE(FROM_UNIXTIME(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity,':',DATEDIFF(CURDATE(),DATE(FROM_UNIXTIME(e.created_on))),':',tw.town_name) ORDER BY t.init ASC ) AS fran_order_det,di.price AS dp_price,di.member_price AS member_price,0 AS po_id,c.is_sourceable
		FROM king_orders a
		JOIN king_transactions t ON t.transid = a.transid 
		JOIN products_group_orders o ON o.order_id = a.id
		JOIN m_product_group_deal_link b ON b.itemid = a.itemid
		JOIN m_product_info c ON c.product_id = o.product_id 
		JOIN king_brands d ON c.brand_id = d.id 
		JOIN king_dealitems di ON di.id = a.itemid
		JOIN king_deals d1 ON d1.dealid = di.dealid
		JOIN pnh_m_franchise_info e ON e.franchise_id = t.franchise_id 
		JOIN pnh_towns tw ON tw.id = e.town_id 
		WHERE a.status = 0 AND t.is_pnh = 1  AND t.init BETWEEN 1325356200 AND 1406744999    
		GROUP BY c.product_id 
		)
	UNION(SELECT c.brand_id AS brand_id,d.name AS brand_name,
			c.product_id,TRIM(c.product_name) AS product_name,
			SUM(a.quantity*b.qty) AS ttl_req_qty,DATE(FROM_UNIXTIME(a.time)) AS order_date,
			c.mrp,c.purchase_cost,
			GROUP_CONCAT(DISTINCT CONCAT(a.transid,':',UNIX_TIMESTAMP(DATE(FROM_UNIXTIME(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity,':',DATEDIFF(CURDATE(),DATE(FROM_UNIXTIME(e.created_on))),':',tw.town_name) ORDER BY t.init ASC ) AS fran_order_det,di.price AS dp_price,di.member_price AS member_price,IFNULL(GROUP_CONCAT(po_id),0) AS po_id,c.is_sourceable
		FROM king_orders a 
		JOIN king_transactions t ON t.transid = a.transid 
		JOIN m_product_deal_link b ON a.itemid = b.itemid AND b.is_active = 1 
		JOIN m_product_info c ON c.product_id = b.product_id 
		JOIN king_brands d ON c.brand_id = d.id 
		JOIN pnh_m_franchise_info e ON e.franchise_id = t.franchise_id
		JOIN king_dealitems di ON di.id = a.itemid
		JOIN king_deals d1 ON d1.dealid = di.dealid
		JOIN pnh_towns tw ON tw.id = e.town_id  
		LEFT JOIN t_vendor_po_order_link vol ON vol.order_id = a.id AND vol.order_status = 1 
		WHERE a.status = 0 AND t.is_pnh = 1   AND t.init BETWEEN 1325356200 AND 1406744999  
		AND IF(di.is_group,(a.order_product_id = c.product_id),1) 
		GROUP BY c.product_id  
		)) AS g 
	LEFT JOIN t_stock_info s ON s.product_id = g.product_id 
	GROUP BY g.product_id 
	HAVING ttl_req_qty > 0  AND avail_qty < ttl_req_qty 
	ORDER BY g.product_name,g.product_id;

==================
SELECT i.id AS item_id,i.size_chart,pnh_id AS pid,i.is_group,i.gender_attr,i.id AS itemid,i.name,d.tagline,c.name AS category,m.name AS menu,d.menuid AS menu_id,
					d.catid AS category_id,mc.name AS main_category,c.type AS main_category_id,b.name AS brand,d.brandid AS brand_id,i.member_price,
					i.orgprice AS mrp,i.price AS price,i.store_price,i.is_combo,CONCAT('http://static.snapittoday.com/items/',d.pic,'.jpg') AS image_url,
					d.description,i.shipsin AS ships_in,d.keywords,i.live AS is_stock,d.publish AS is_enabled,CONCAT('http://static.snapittoday.com/items/small/',d.pic,'.jpg') AS small_image_url, 
					i.has_insurance,d.publish
					FROM king_deals d 
					JOIN king_dealitems i ON i.dealid=d.dealid 
					JOIN king_brands b ON b.id=d.brandid 
					JOIN king_categories c ON c.id=d.catid 
					LEFT OUTER JOIN pnh_menu m ON m.id=d.menuid 
					LEFT OUTER JOIN king_categories mc ON mc.id=c.type 
					WHERE is_pnh=1 AND i.pnh_id ='10005907' AND i.pnh_id!=0
					ORDER BY d.sno ASC;
====================
	
SELECT IFNULL(CONCAT('http://static.snapittoday.com/items/',r.id,'.jpg'),v.main_image)AS url
,IFNULL(CONCAT('http://static.snapittoday.com/items/small/',r.id,'.jpg'),v.other_images) AS small_img_url 
FROM king_resources r  
LEFT JOIN m_vendor_product_images v ON v.item_id=r.itemid 
WHERE r.itemid='2616472368'  AND r.type=0;

#========================

SELECT IFNULL(v.other_images,CONCAT('http://static.snapittoday.com/items/',r.id,'.jpg'))AS url
,CONCAT('http://static.snapittoday.com/items/small/',r.id,'.jpg') AS small_img_url
FROM king_resources r 
LEFT JOIN m_vendor_product_images v ON v.item_id=r.itemid 
WHERE r.itemid='2616472368'  AND TYPE=0;
#========================

#================================================================
ALTER TABLE king_brands ADD INDEX (`name`);
ALTER TABLE `t_imei_no` ADD COLUMN `reserved_on` BIGINT(20) DEFAULT 0 NULL AFTER `reserved_batch_rowid`; 
ALTER TABLE `m_vendor_product_images` ADD INDEX (`vendor_id`), ADD INDEX (`item_id`);
#================================================================

#Jul_31_2014

SELECT * FROM support_tickets_msg WHERE ticket_id='1692'

SELECT * FROM t_imei_no WHERE reserved_batch_rowid !=  0;




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
		
SELECT * FROM m_employee_info WHERE emp_type=1;

SELECT id,user_id FROM king_admin WHERE username='shivaraj';

#Aug_07_2014


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
#===============================
#Aug_07_2014-Shivaraj
CREATE TABLE `mp_loyaltypoint_brand_cat_config`( `id` BIGINT NOT NULL AUTO_INCREMENT, `brandid` BIGINT, `catid` BIGINT, `offernote_tmpl` TEXT, `is_active` TINYINT(1) DEFAULT 1, `created_on` DATETIME, `created_by` INT(11), `modified_on` DATETIME, `modified_by` INT(11), PRIMARY KEY (`id`) );
#Indexes
ALTER TABLE `mp_loyaltypoint_brand_cat_config` ADD INDEX (`brandid`), ADD INDEX (`catid`), ADD INDEX (`is_active`);
#INSERT INTO `mp_loyaltypoint_brand_cat_config` (`brandid`, `catid`, `offernote_tmpl`, `created_on`, `created_by`) VALUES ('23645763', '131', 'You will get %diff% loyalty points', '2014-08-06 15:58:28', '37');
ALTER TABLE `mp_loyaltypoint_brand_cat_config` ADD COLUMN `menuid` BIGINT(20) NULL AFTER `id`;
ALTER TABLE `mp_loyaltypoint_brand_cat_config` DROP `mmenuid`; 
#===============================
#Aug_08_2014

SELECT * FROM t_imei_no 
WHERE 1 #stock_id=? and grn_id in (?) 
AND reserved_batch_rowid=0 AND STATUS=0 AND order_id=0;



SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-$req_mrp),1,0) AS mrp_diff
	FROM t_stock_info a
	JOIN m_rack_bin_info b ON a.rack_bin_id = b.id
	JOIN m_product_info p ON p.product_id = a.product_id
	LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
	LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
	LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
	LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id $cond
	WHERE a.mrp > 0 AND a.product_id = '1428' AND available_qty > 0 AND is_damaged = 0
	GROUP BY stock_id,town_diff
	ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp;
#===================================================
SELECT pl.product_id,sk.stock_id,imei.grn_id
FROM king_orders o
JOIN king_transactions tr ON tr.transid=o.transid
JOIN king_dealitems di ON di.id=o.itemid
JOIN m_product_deal_link pl ON pl.itemid=di.id
JOIN t_stock_info sk ON sk.product_id = pl.product_id
LEFT JOIN t_imei_no imei ON imei.stock_id = sk.stock_id
WHERE tr.transid='PNHBLY54827'
#===================================================
DESC king_orders;
SELECT order_product_id FROM king_orders;
SELECT * FROM `m_product_deal_link`
SELECT * FROM t_stock_info;
SELECT * FROM king_transactions
SELECT * FROM t_imei_no;

SELECT * FROM t_imei_no WHERE stock_id='193122' AND grn_id IN ('0') AND reserved_batch_rowid=0 AND STATUS=0 AND order_id=0 LIMIT 1

SELECT * FROM t_imei_no WHERE stock_id='193122' AND grn_id IN ('0') AND reserved_batch_rowid=0 AND STATUS=0 AND order_id=0 LIMIT 2;
SELECT * FROM t_imei_no WHERE stock_id='162097' AND grn_id IN ('0') AND reserved_batch_rowid=0 AND STATUS=0 AND order_id=0 LIMIT 1;
SELECT * FROM t_imei_no WHERE stock_id='245136' AND grn_id IN ('0') AND reserved_batch_rowid=0 AND STATUS=0 AND order_id=0 LIMIT 1;


SELECT is_serial_required FROM m_product_info WHERE product_id=4427;


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
                     )
#============ Partner Deal Link table =========================	
#===============================================
#Aug_08_2014
ALTER TABLE king_dealitems ADD COLUMN free_frame TINYINT(1) DEFAULT 0;
ALTER TABLE king_dealitems ADD COLUMN has_power TINYINT(1) DEFAULT 0;
ALTER TABLE `king_orders` ADD COLUMN `mp_loyalty_points` DOUBLE(10,2) DEFAULT 0 NULL AFTER `mp_logid`;
#===============================================

#============<< NEW-Insurance SMS >>===================
#Aug_09_2014
SELECT mo.member_id#,mo.franchise_id,mo.offer_type,mo.order_id,mo.transid_ref,mo.insurance_id
	,mi.first_name,ins.first_name AS ins_first_name,ins.mob_no,ins.city
	,f.franchise_name,f.login_mobile1
FROM pnh_member_offers mo
JOIN pnh_member_insurance ins ON ins.sno = mo.insurance_id
JOIN pnh_m_franchise_info f ON f.franchise_id=mo.franchise_id
JOIN pnh_member_info mi ON mi.pnh_member_id=mo.member_id
WHERE ins.status_ship = 0 AND mo.process_status = 1 
#AND mo.sno ='162'
HAVING first_name !=ins_first_name;
#============<< NEW-Insurance SMS >>===================
SELECT * FROM pnh_member_info 
WHERE pnh_member_id='21111111' 
ORDER BY id DESC;

SELECT UNIX_TIMESTAMP('2013-01-01'); =>1356978600