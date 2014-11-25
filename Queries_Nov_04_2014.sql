
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
