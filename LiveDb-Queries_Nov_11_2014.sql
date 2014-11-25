SELECT * FROM t_reserved_batch_stock WHERE p_invoice_no=3003749;//product_id=225897 AND STATUS=1;

SELECT * FROM t_stock_info WHERE stock_id IN(370280);

DESC t_imei_no WHERE 

SELECT * FROM t_imei_no WHERE stock_id=370280;

SELECT o.itemid,o.id AS order_id,o.quantity,o.status FROM king_orders o 
LEFT JOIN king_dealitems di ON di.id=o.itemid
WHERE o.transid='PNHDNQ92958' AND  di.pnh_id=20020169;

SELECT * FROM king_transactions WHERE transid='PNHDNQ92958';
SELECT * FROM king_orders WHERE transid='PNHDNQ92958';

SELECT * FROM t_imei_no WHERE imei_no='355702061926335';

SELECT * FROM t_stock_info WHERE product_id=399674
423549

SELECT * FROM t_partner_reserved_batch_stock ORDER BY id DESC LIMIT 10; #
SELECT * FROM t_partner_stock_transfer_product_link WHERE transfer_id=130 LIMIT 1
SELECT * FROM t_partner_stock_transfer WHERE transfer_id=130 LIMIT 1;

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

SELECT * FROM king_invoice  WHERE transid='PNHLNE81544';
