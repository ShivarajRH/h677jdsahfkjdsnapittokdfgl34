SELECT user_id FROM pnh_member_info WHERE pnh_member_id='21111152';

SELECT * FROM (
(SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=98877)
  UNION 
(SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=98877)) AS g;


PNHCLZ43782:0:98877,PNHBZJ94344:0:98877
PNHIXY99464:2:98877,PNHBSK23582:2:98877,PNHWZN23234:2:98877,PNHBZJ94344:0:98877

SELECT user_id FROM pnh_member_info WHERE pnh_member_id='21111111'

SELECT DISTINCT pnh_member_id,user_id FROM pnh_member_info m
JOIN king_orders o ON o.userid!=m.user_id

#User have orders,count(o.id) as ttl_sls
SELECT DISTINCT pnh_member_id,user_id FROM pnh_member_info m
JOIN king_orders o ON o.userid!=m.user_id #and o.status!=3
GROUP BY m.user_id
ORDER BY COUNT(o.id) DESC 


SELECT rstk.transfer_id,rstk.itemid,p.product_id,p.product_name
,GROUP_CONCAT(DISTINCT stk.product_barcode) AS barcodes
,SUM(rstk.qty) AS ttl_qty,SUM(rstk.release_qty)
,GROUP_CONCAT(DISTINCT imei.imei_no) AS imei,rstk.tp_id
FROM  t_partner_reserved_batch_stock rstk
JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
JOIN m_product_info p ON p.product_id=rstk.product_id AND p.is_serial_required=1
JOIN t_imei_update_log imei ON imei.transfer_prod_link_id=rstk.tp_id AND imei.product_id=rstk.product_id 
WHERE rstk.status IN (1,2) AND rstk.transfer_option=1 
GROUP BY rstk.product_id;

SELECT * FROM t_partner_stock_transfer_product_link
WHERE id=8

SELECT * FROM t_imei_update_log WHERE transfer_prod_link_id=8


SELECT COUNT(DISTINCT g.transid) AS t FROM (
(SELECT o.transid AS transid FROM king_tmp_orders o WHERE 1 AND o.approval_status != 2 AND o.userid=65435)
  UNION 
(SELECT o.transid AS transid FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=65435)
) AS g

#PNHCLZ43782:0:98877,PNHBZJ94344:0:98877
#PNHIXY99464:2:98877,PNHBSK23582:2:98877,PNHWZN23234:2:98877,PNHBZJ94344:0:98877

	
#SELECT COUNT(DISTINCT o.transid),GROUP_CONCAT(DISTINCT o.transid,':',o.status,':',o.userid) AS t FROM king_orders o WHERE 1 AND o.status != 3 AND o.userid=58932


SELECT t.ticket_id,t.ticket_no,t.user_id,a.name AS `created_by`,t.name,t.mobile,t.email,t.transid,t.type,t.status,t.priority,t.assigned_to,t.franchise_id,t.related_to,t.req_mem_name,t.req_mem_mobile,DATE_FORMAT(t.created_on,'%d/%b/%Y %h:%i:%s %p') AS created_on,DATE_FORMAT(t.updated_on,'%d/%b/%Y %h:%i:%s %p') AS updated_on,t.from_app
	,t.assigned_to,a.name AS `user`,u.name AS assignedto,f.franchise_name
	,rt.id AS related_to_id,rt.name AS related_to_name
	,GROUP_CONCAT(DISTINCT dlnk.dept_id,':',dept.name ORDER BY dept.name) AS dept_dets
	FROM support_tickets t 
	LEFT JOIN king_admin a ON a.id=t.user_id
	LEFT JOIN king_users u ON u.userid=t.assigned_to  
	LEFT OUTER JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id
	
	LEFT OUTER JOIN m_dept_request_types rt ON rt.id=t.related_to
	LEFT OUTER JOIN m_dept_request_type_link dlnk ON dlnk.type_id = t.related_to AND dlnk.is_active='1'
	LEFT OUTER JOIN m_departments dept ON dept.id = dlnk.dept_id
	
	WHERE 1 # AND t.franchise_id != 0   
	GROUP BY t.ticket_id
	 ORDER BY t.updated_on DESC  LIMIT 0,30;
	 
SELECT * FROM support_tickets WHERE ticket_id=2689;




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
				 ORDER BY t.updated_on DESC  LIMIT 0,30 
				 

SELECT GROUP_CONCAT(DISTINCT rlink.dept_id) AS dept_ids,rlink.type_id,rtype.name AS request_name FROM m_dept_request_type_link rlink
											LEFT JOIN `m_dept_request_types` rtype ON rtype.id = rlink.type_id
											WHERE rlink.is_active=1 AND rlink.type_id = 6
											HAVING dept_ids IS NOT NULL
											
SELECT * FROM m_dept_request_type_link


SELECT emp.employee_id,emp.name,emp.email,emp.contact_no
			FROM m_departments dt
			JOIN m_employee_dept_link edl ON edl.dept_id = dt.id
			JOIN m_employee_info emp ON emp.employee_id = edl.employee_id
			WHERE dt.id IN (5,13) AND job_title=9
			HAVING emp.employee_id IS NOT NULL;
			
`snapitto_erpsndx_jul_04_2014`

# Nov_10_2014
ALTER TABLE `support_tickets` ADD COLUMN `created_by` INT(20) NULL AFTER `from_app`;

SELECT * FROM t_reserved_batch_stock WHERE product_id=345805;

#userid:16,#pwd: 941f06be841d8967cab504dbc7012067


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
					

SELECT u.name AS USER,t.*,a.name AS assignedto
FROM support_tickets t 
LEFT OUTER JOIN king_admin a ON a.id=t.assigned_to
LEFT OUTER JOIN king_admin u ON u.id=t.user_id WHERE 1  AND franchise_id= 498
ORDER BY t.updated_on DESC, t.created_on DESC;

SELECT IFNULL(CONCAT('http://sndev13.snapittoday.com/resources/returns_images/',pic,'.jpg'),'n/a') AS image_url FROM pnh_invoice_returns_images WHERE ticket_id=?


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
WHERE 1  AND f.franchise_id= 533
GROUP BY t.ticket_id
ORDER BY t.updated_on DESC, t.created_on DESC
#================================

authkey 563a9ee799e7e748db21b52c4259e8bc
franchise_id	533
TYPE	10
user_id	16
authkey 563a9ee799e7e748db21b52c4259e8bc
franchise_id	533
TYPE	10
user_id	16

SELECT * FROM support_tickets t WHERE t.franchise_id= 533 AND ticket_id=1892 ORDER BY created_on DESC LIMIT 50 

SELECT * FROM support_tickets t WHERE  ticket_id=1892 ORDER BY created_on DESC LIMIT 50;


SELECT * FROM support_tickets t WHERE  ticket_id=1892 ORDER BY created_on DESC LIMIT 50 

SELECT * FROM support_tickets t ORDER BY created_on DESC LIMIT 50 

SELECT * FROM support_tickets_msg t ORDER BY created_on DESC LIMIT 50 

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

				WHERE 1  AND f.franchise_id= 533
				GROUP BY t.ticket_id
				 ORDER BY t.updated_on DESC, t.created_on DESC 
				 
				 
SELECT t.is_delivered FROM shipment_batch_process_invoice_link t 
		JOIN king_invoice i ON i.invoice_no=t.invoice_no AND invoice_status=1
		WHERE 1 AND t.invoice_no=20141024118 
		# and order_id IN (2995582869)
		LIMIT 1;
		
SELECT o.itemid,o.id AS order_id,o.quantity,o.status FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
                                                WHERE o.transid='PNHDNQ92958' AND  di.pnh_id='20020169';
                                                
SELECT * FROM king_transactions WHERE transid='PNHDNQ92958';

#===========================
# GET IMEI deatis of AMAZON-FBA ORDERS
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


SELECT pid,qty,attributes FROM pnh_api_franchise_cart_info WHERE franchise_id=637 AND STATUS=1;



(
	SELECT o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_orders o
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	WHERE o.status !=3 AND o.is_memberprice=1  AND o.itemid='497689655793' AND tr.franchise_id='498' #AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = CURDATE()
	HAVING quantity IS NOT NULL 
	ORDER BY o.time DESC
)
UNION
(
	SELECT o.i_price,SUM(o.quantity) AS quantity,o.is_memberprice,FROM_UNIXTIME(o.time) FROM king_tmp_orders o
	JOIN king_tmp_transactions tr ON tr.transid=o.transid
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	WHERE o.approval_status NOT IN (1,2) AND o.is_memberprice=1  AND o.itemid='497689655793' AND tr.franchise_id=498 #AND DATE_FORMAT(FROM_UNIXTIME(o.time),'%Y-%m-%d') = CURDATE()
	HAVING quantity IS NOT NULL 
	ORDER BY o.time DESC
);

SELECT o.itemid,o.transid,SUM(o.quantity) AS quantity FROM king_transactions tr
JOIN king_orders o ON o.transid=tr.transid
WHERE tr.franchise_id='1409' AND o.itemid='497689655793' AND o.status !=3 AND o.is_memberprice=1
GROUP BY o.transid;
			
			
			
(
SELECT tr.franchise_id,o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE mplog.itemid='497689655793' AND o.status !=3
	GROUP BY tr.transid
	HAVING quantity IS NOT NULL
	ORDER BY o.time DESC
	)
	UNION
	(
	SELECT tr.franchise_id,o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_tmp_orders o
	JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	JOIN king_tmp_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE mplog.itemid='497689655793' AND o.approval_status NOT IN (1,2)
	GROUP BY tr.franchise_id
	HAVING quantity IS NOT NULL
	ORDER BY o.time DESC
	)
	
SELECT tr.franchise_id,o.itemid,o.i_price,SUM(o.quantity) AS quantity
	FROM king_orders o
	#JOIN deal_member_price_changelog mplog ON mplog.id = o.mp_logid AND mplog.is_active=1
	JOIN king_transactions tr ON tr.transid=o.transid
	JOIN king_dealitems di ON di.id=o.itemid
	WHERE o.itemid='497689655793' AND o.status !=3	
	GROUP BY tr.transid;
	
SELECT userid,member_id FROM king_orders WHERE transid='PNHHMI12562';

SELECT FROM_UNIXTIME(tr.actiontime),tr.*
FROM king_transactions tr 
JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id OR f.login_mobile1 = '9008103536'
JOIN king_orders o ON o.transid = tr.transid
WHERE  tr.franchise_id = '17' AND tr.actiontime 
BETWEEN UNIX_TIMESTAMP("2014-11-05 00:00:00") AND UNIX_TIMESTAMP("2014-11-05 23:59:59");


SELECT * FROM pnh_m_franchise_info ORDER BY franchise_id DESC LIMIT 10;

UPDATE `pnh_m_franchise_info` SET `class_id` = '1' WHERE `franchise_id` = '1431';