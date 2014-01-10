select * from products_group where group_id=219138

select gp.*,p.product_name from products_group_pids gp 
join m_product_info p on p.product_id=gp.product_id 
where gp.group_id=219138 group by gp.product_id order by p.product_name

select p.*,sum(s.available_qty) as stock from m_product_info p 
left outer join t_stock_info s on s.product_id=p.product_id 
where p.product_name like "%nike%" or (p.barcode="nike" and p.barcode!='') 
group by p.product_id order by p.product_name asc limit 0,100

## Sep_28_2013
select pgp.*,pga.*,pgav.*
from products_group_pids as pgp  
join products_group_attributes pga using pga.group_id=pgp.group_id
join products_group_attribute_values pgav using pgav.group_id=pgp.group_id
where pgp.group_id = (219138)
group by pgav.attribute_value_id

select pgp.id,pgp.group_id,pgp.product_id,pga.attribute_name_id as name_id,pga.attribute_name as name_val,pgav.attribute_value_id as att_val_id,pgav.attribute_value as att_value from products_group_pids as pgp  
join products_group_attributes pga on pga.group_id=pgp.group_id
join products_group_attribute_values pgav on pgav.group_id=pgp.group_id
where pgp.product_id in (153393)
group by att_val_id
(153270)29239

###pgp.id,pgp.group_id,pgp.product_id,pga.attribute_name_id as name_id,pga.attribute_name as name_val
###pgav.attribute_value_id as att_val_id,pgav.attribute_value as att_value 

select pgp.*,pga.*,pgav.*
from products_group_pids as pgp  
join products_group_attributes pga using pga.group_id=pgp.group_id
join products_group_attribute_values pgav using pgav.group_id=pgp.group_id
where pgp.group_id = (219138)
group by pgav.attribute_value_id

## Sep_30_2013
select * from king_invoice #where 
order by createdon desc

select * from t_stock_info
where ;

select (from_unixtime(1378473034));


select stock_id,product_id,mrp,product_barcode,bc_match,
	       length(product_barcode) as bc_len,length(?) as i_bc_len,
	       location_id,rack_bin_id,available_qty,sum(mrp_match+bc_match) as r from (
	   select a.stock_id,a.product_id,mrp,if(mrp=?,1,0) as mrp_match,
	       product_barcode,if(product_barcode=?,1,0) as bc_match,
	       location_id,rack_bin_id,available_qty
	       from t_stock_info a
	       where product_id = ? and available_qty >= 0
	       having mrp_match and if(length(?),(length(product_barcode) = 0 or bc_match = 1),1)
	       order by mrp_match desc,bc_match desc ) as g
	   group by stock_id
	   order by r desc

select count(*) as t from t_stock_info where product_id = ? and available_qty >= 0

select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=128785 and o.time > now()-24*60*60*90;

#Oct 1
select ifnull(sum(s.available_qty),0) as stock,p.*,b.name as brand from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id join king_brands b on b.id=p.brand_id where p.product_id=1751

select u.name as username,l.*,pi.p_invoice_no,ci.invoice_no as c_invoice_no,i.invoice_no 

select  u.name as username,l.update_type,l.qty,l.current_stock from t_stock_update_log l 
left outer join king_invoice i on i.id=l.invoice_id left outer join t_client_invoice_info ci on ci.invoice_id=l.corp_invoice_id 
left outer join proforma_invoices pi on pi.id=l.p_invoice_id 
left outer join king_admin u on u.id=l.created_by 
where l.product_id=153002 order by l.id desc

select * from t_stock_update_log l where product_id=153002 order by id desc;


select * from t_stock_info where product_id = 153002 and available_qty >= 0;
 #sum(available_qty) as t 

update t_stock_info set available_qty=available_qty".($stk_movtype==1?"+":"-")."?,modified_by=?,modified_on=now() where stock_id = ? and available_qty >= 0

select * from t_imei_no where status=0 and product_id=153002