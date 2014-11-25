SELECT * FROM support_tickets t WHERE  ticket_id=1892 ORDER BY created_on DESC LIMIT 50;


SELECT * FROM support_tickets t ORDER BY created_on DESC LIMIT 50;

SELECT f.franchise_id,f.franchise_name,f.login_mobile1 FROM pnh_m_franchise_info f WHERE f.login_mobile1 = '' OR franchise_id = 533

SELECT f.franchise_id,f.franchise_name,f.login_mobile1 FROM pnh_m_franchise_info f WHERE franchise_id = 498 LIMIT 1;



#==============
INSERT INTO `t_imei_update_log` (`imei_no`,`alloted_order_id`) VALUES ('352157064118609', '2236863886'); 

UPDATE `t_imei_update_log` SET `product_id` = '155926',`stock_id` = '163396',`grn_id` = '5786',`alloted_on` = '2014-04-05 16:39:47' , `logged_on` = '2014-04-05 16:23:26' , `logged_by` = '37' WHERE `id` = '22915'; 

#==============

INSERT INTO `t_imei_update_log` (`imei_no`, `alloted_order_id`) VALUES ('359717059707055', '2995582869'); 

UPDATE `t_imei_update_log` SET `product_id` = '155786',`stock_id` = '163255',`grn_id` = '5786',`alloted_on` = '2014-11-14 13:30:34' , `logged_on` = '2014-11-14 13:30:40' , `logged_by` = '37' WHERE `id` = '22916'; 

#=======================

SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2236863886,2438214924) ORDER BY id DESC;

SELECT * FROM pnh_member_offers WHERE transid_ref='PNHDTR48635';
Res:
"sno"	"member_id"	"franchise_id"	"offer_type"	"mem_fee_applicable"	"pnh_member_fee"	"offer_value"	"offer_towards"	"pnh_pid"	"order_id"	"transid_ref"	"insurance_id"	"process_status"	"feedback_status"	"feedback_value"	"delivery_status"	"details_updated"	"referred_by"	"referred_status"	"remarks"	"created_by"	"created_on"	"modified_by"	"modified_on"
"66"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005541"	"2236863886"	"PNHDTR48635"	"39"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"
"67"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005472"	"3469375677"	"PNHDTR48635"	"40"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"

SELECT * FROM pnh_member_insurance WHERE order_id IN(2236863886,2438214924,2995582869,3469375677);
Res:
"sno"	"insurance_id"	"fid"	"mid"	"menu_log_id"	"offer_type"	"proof_id"	"proof_type"	"proof_address"	"opted_insurance"	"offer_status"	"insurance_value"	"insurance_margin"	"order_value"	"itemid"	"order_id"	"first_name"	"last_name"	"mob_no"	"city"	"pincode"	"mem_receipt_no"	"mem_receipt_date"	"mem_receipt_amount"	"status_ship"	"status_deliver"	"created_by"	"created_on"	"modified_by"	"modified_on"	"processed_by"	"processed_on"
"39"	"771396684007"	"316"	"22003363"	"19"	"0"	"SUH2965168"	"3"	"146/2, Gajminal , Bailhongal (Rural), Belgaum,Karanataka-591121
"	"1"	"0"	"59.9"	"1"	"5990"	"4681751198"	"2236863886"	"Shankar"	"Pending"	"9731455645"	"Bailhongala"	"591121"	"2518"	"2014-04-14"	"6400"	"0"	"0"	"42"	"2014-04-05 13:16:47"	"42"	"2014-04-16 16:22:35"	\N	\N
"40"	"841396684007"	"316"	"22003363"	"19"	"0"	"Pending"	"1"	"Pending"	"1"	"0"	"59.9"	"1"	"5990"	"5717134917"	"3469375677"	"Upendra"	"Pending"	"9980568024"	""	""	\N	\N	\N	"0"	"0"	"42"	"2014-04-05 13:16:47"	\N	\N	\N	\N


SELECT t.is_delivered FROM shipment_batch_process_invoice_link t 
		JOIN king_invoice i ON i.invoice_no=t.invoice_no AND invoice_status=1
		WHERE 1 AND t.invoice_no=20141024118 
		# and order_id IN (2995582869)
		LIMIT 1;
		

SELECT a.sent_log_id,a.invoice_no,a.logged_on,a.status,ref_id
	FROM pnh_invoice_transit_log a 
	WHERE invoice_no = 2995582869
	ORDER BY a.id DESC LIMIT 1
	

SELECT o.itemid,o.id AS order_id,o.quantity,o.status FROM king_orders o JOIN king_dealitems di ON di.id=o.itemid
                                                WHERE o.transid='PNHDNQ92958' AND  di.pnh_id='20020169';
                                 
SELECT * FROM king_transactions WHERE transid='PNHDNQ92958';


SELECT * FROM support_tickets t WHERE  ticket_id=1892 ORDER BY created_on DESC LIMIT 50;


SELECT * FROM support_tickets t ORDER BY created_on DESC LIMIT 50;

SELECT f.franchise_id,f.franchise_name,f.login_mobile1 FROM pnh_m_franchise_info f WHERE f.login_mobile1 = '' OR franchise_id = 533

SELECT f.franchise_id,f.franchise_name,f.login_mobile1 FROM pnh_m_franchise_info f WHERE franchise_id = 498 LIMIT 1;



#==============
INSERT INTO `t_imei_update_log` (`imei_no`,`alloted_order_id`) VALUES ('352157064118609', '2236863886'); 

UPDATE `t_imei_update_log` SET `product_id` = '155926',`stock_id` = '163396',`grn_id` = '5786',`alloted_on` = '2014-04-05 16:39:47' , `logged_on` = '2014-04-05 16:23:26' , `logged_by` = '37' WHERE `id` = '22915'; 

#==============

INSERT INTO `t_imei_update_log` (`imei_no`, `alloted_order_id`) VALUES ('359717059707055', '2995582869'); 

UPDATE `t_imei_update_log` SET `product_id` = '155786',`stock_id` = '163255',`grn_id` = '5786',`alloted_on` = '2014-11-14 13:30:34' , `logged_on` = '2014-11-14 13:30:40' , `logged_by` = '37' WHERE `id` = '22916'; 

#=======================

SELECT * FROM t_imei_update_log WHERE alloted_order_id IN(2236863886,2438214924) ORDER BY id DESC;

SELECT * FROM pnh_member_offers WHERE transid_ref='PNHDTR48635';
Res:
"sno"	"member_id"	"franchise_id"	"offer_type"	"mem_fee_applicable"	"pnh_member_fee"	"offer_value"	"offer_towards"	"pnh_pid"	"order_id"	"transid_ref"	"insurance_id"	"process_status"	"feedback_status"	"feedback_value"	"delivery_status"	"details_updated"	"referred_by"	"referred_status"	"remarks"	"created_by"	"created_on"	"modified_by"	"modified_on"
"66"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005541"	"2236863886"	"PNHDTR48635"	"39"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"
"67"	"22003363"	"316"	"0"	"0"	\N	"59.9"	"5990"	"10005472"	"3469375677"	"PNHDTR48635"	"40"	"1"	"1"	"5"	"1"	"0"	"0"	"0"	\N	"42"	"2014-04-05 13:16:47"	"4"	"2014-04-25 17:56:37"

SELECT * FROM pnh_member_insurance WHERE order_id IN(2236863886,2438214924,2995582869,3469375677);
Res:
"sno"	"insurance_id"	"fid"	"mid"	"menu_log_id"	"offer_type"	"proof_id"	"proof_type"	"proof_address"	"opted_insurance"	"offer_status"	"insurance_value"	"insurance_margin"	"order_value"	"itemid"	"order_id"	"first_name"	"last_name"	"mob_no"	"city"	"pincode"	"mem_receipt_no"	"mem_receipt_date"	"mem_receipt_amount"	"status_ship"	"status_deliver"	"created_by"	"created_on"	"modified_by"	"modified_on"	"processed_by"	"processed_on"
"39"	"771396684007"	"316"	"22003363"	"19"	"0"	"SUH2965168"	"3"	"146/2, Gajminal , Bailhongal (Rural), Belgaum,Karanataka-591121
"	"1"	"0"	"59.9"	"1"	"5990"	"4681751198"	"2236863886"	"Shankar"	"Pending"	"9731455645"	"Bailhongala"	"591121"	"2518"	"2014-04-14"	"6400"	"0"	"0"	"42"	"2014-04-05 13:16:47"	"42"	"2014-04-16 16:22:35"	\N	\N
"40"	"841396684007"	"316"	"22003363"	"19"	"0"	"Pending"	"1"	"Pending"	"1"	"0"	"59.9"	"1"	"5990"	"5717134917"	"3469375677"	"Upendra"	"Pending"	"9980568024"	""	""	\N	\N	\N	"0"	"0"	"42"	"2014-04-05 13:16:47"	\N	\N	\N	\N

