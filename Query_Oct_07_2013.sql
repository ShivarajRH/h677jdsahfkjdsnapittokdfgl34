mklink /j "%APPDATA%\Microsoft\Sticky Notes" "C:\Users\User\Google Drive\Sticky Notes"
mklink /j "C:\Users\User\Downloads" "C:\Users\User\Google Drive\Downloads"

#List of Ajax requests in pnh_offline_order page:

admin/pnh_place_quote+$("#fr_req_quote_frm").serialize()

admin/pnh_jx_show_schemes+{id:fid}

admin/pnh_jx_loadmemids+{fid:fid}

admin/pnh_jx_loadfranchisebyid+{fid:fid}

admin/pnh_jx_loadfranchisebymobile+{mobile:fmobile}

/admin/jx_reg_newmem+$('#reg_mem_frm').serialize()

admin/pnh_jx_checkstock_order+{attr:attr,pids:ppids.join(","),qty:qty.join(","),fid:$('#i_fid').val(),mid:$("input[name='mid']",$(this)).val()}

admin/pnh_jx_searchdeals+{fid:$("#i_fid").val(),q:q}

admin/pnh_jx_loadpnhprodbybarcode+{fid:$("#i_fid").val(),barcode:barcode}

admin/pnh_jx_loadpnhprod+{pid:pid,fid:$("#i_fid").val()}

admin/jx_pnh_getmid+{mid:$(this).val(),more:1}

/admin/jx_to_load_productdata+{pids:ppids.join(","),fid:$("#i_fid").val()}

/admin/jx_pnh_ord_prod_unshipped+{fid:$("#i_fid").val()}

admin/jx_pnh_fran_cancelledorders+{pid:pid,fid:$("#i_fid").val()}

admin/pnh_jx_load_scheme_details+{fid:fid}

select * from t_stock_info order by available_qty desc; // where tmp_brandid=1532657
select * from m_product_info where product_id=1532657;

select * from king_dealitems where is_pnh=1 order by is_pnh desc;and sno = 1532657
select * from king_deals where dealid=1532657

select d.menuid,m.default_margin as margin,available as available_qty from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=1532657

select ifnull(sum(s.available_qty),0) as stock,p.*,b.name as brand from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id join king_brands b on b.id=p.brand_id where p.product_id=758

SELECT i.name,i.pnh_id,i.orgprice AS mrp,i.price,i.store_price,d.menuid
									 FROM king_dealitems i
									 JOIN king_deals d ON d.dealid=i.dealid
									 JOIN `pnh_franchise_menu_link` m ON m.menuid=d.menuid
									  WHERE m.status=1 and publish = 1 and live = 1 

select i.name,i.pnh_id,i.orgprice as mrp,i.price,i.store_price,i.available as stk_available from king_dealitems i where i.is_pnh=1

SELECT i.pnh_id AS pid,i.available as stk_available  
SELECT *  
						FROM m_product_info p  
						JOIN m_product_deal_link l ON l.product_id=p.product_id 
						JOIN king_dealitems i ON i.id=l.itemid AND i.is_pnh=1 
						JOIN king_deals d ON d.dealid=i.dealid  
						JOIN `pnh_franchise_menu_link` m ON m.menuid=d.menuid 
						WHERE p.barcode=?

select * from king_dealitems

select distinct * from t_stock_info si
left join m_product_info pi on pi.product_id=si.product_id
order by available_qty desc;

select count(*) as t from pnh_franchise_menu_link where status = 1 and menuid in (select menuid 
													from king_dealitems a
													join king_deals b on a.dealid = b.dealid  )

select * from king_dealitems
 where pnh_id != 0 and is_pnh=1 and available!=0
order by available desc

desc king_dealitems;

select * from king_deals

select distinct * from t_stock_info si
left join m_product_info pi on pi.product_id=si.product_id
order by available_qty desc;

select si.*,d.* from t_stock_info si
join m_product_deal_link pdl
join king_dealitems as d on d.dealid=pdl.itemid
where si.product_id=pdl.product_id


select di.max_allowed_qty,si.available_qty from king_dealitems di
join m_product_deal_link pdl on pdl.itemid=di.dealid
join t_stock_info si on si.product_id=pdl.product_id
where di.pnh_id = 151


#====== Oct-08-2013 =============
select * from king_transactions 
where transid='PNH19996'
order by init desc;

select * from pnh_menu;

select i.*,d.publish,c.loyality_pntvalue,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid where i.is_pnh=1 and  i.pnh_id='PNH51365' and i.pnh_id!=0

####################### OCT-09 ######################
select * from pnh_order_margin_track order by id desc;

####################### Oct-10 ######################
 select * from king_orders order by sno desc

select * from t_stock_info order by created_on desc;
select * from t_reserved_batch_stock order by reserved_on desc;
select * from grn_product_link  order by id desc;

cod_pincodes

####################### Oct-11 ######################
select * from king_deals
select * from m_product_info
select * from king_dealitems where id=9893764619
select * from m_product_group_deal_link where itemid=9893764619
select * from m_product_groups

=============================================================================================================================================================================================
# n
create database snapittoday_db_oct;
use snapittoday_db_oct


select md5("admin123"); #0192023a7bbd73250516f069df18b500
# superadmin1 9027da57d66aa309df4d13q0e6ab0d06
select md5("superadmin"); 17c4520f6cfd1ab53d8745e84681eb49


select distinct tr.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,tr.transid,sum(o.i_coup_discount) as com,tr.amount,o.transid,o.status,o.time,o.actiontime,mi.user_id as userid,mi.pnh_member_id from king_orders o
                                join king_transactions tr on tr.transid=o.transid
                                join pnh_member_info mi on mi.user_id=o.userid 
                                join pnh_m_franchise_info d on d.franchise_id = tr.franchise_id
                                join pnh_m_territory_info f on f.id = d.territory_id
                                join pnh_towns e on e.id = d.town_id 
                                join king_dealitems dl on dl.id=o.itemid
                                join king_deals deal on deal.dealid=dl.dealid
                                join king_brands br on br.id = deal.brandid 
                                join pnh_menu m on m.id = deal.menuid 
                                where tr.batch_enabled=0 and o.status=0 
                                group by tr.transid
                                order by tr.init desc

select * from king_dealitems print_name, max_allowed_qty
select print_name, max_allowed_qty from king_dealitems where print_name is not null and max_allowed_qty!=0

select d.*,i.*,d.description,d.keywords,d.tagline from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=9354839395

=============================================================================================================================================================================================

####################### Oct-17 ######################

select * from pnh_m_franchise_info fi where 1=1 is_suspended =1
where fi.territory_id=? and town_id= and franchise_id=?
335= 82 +248
select 82 +248
select count(*) from pnh_m_franchise_info fi where  fi.is_suspended=0

select count(*) as total from pnh_m_franchise_info

####################### Oct-18 ######################
select count(*) as total from pnh_m_franchise_info where 1=1  and fi.territory_id=14 and fi.town_id=67

select * from pnh_m_franchise_info fi where fi.franchise_id=5 limit 1

select * from pnh_m_franchise_info fi where 1=1 order by fi.created_on asc
select * from pnh_m_franchise_info fi where 1=1 and fi.territory_id=3 and fi.is_suspended=1 order by fi.created_on asc
select * from pnh_m_franchise_info fi where 1=1 and fi.territory_id=3 order by fi.created_on asc

select * from pnh_menu order by name

select mn.id,mn.name from pnh_menu mn

    where mn.status=1 
    group by mn.id
    order by mn.name

    join king_deals deal on deal.menuid=mn.id

select menuid from king_deals group by menuid

############ Oct-22-2013 ######################
select * from king_deals

select * from king_dealitems dl
join king_deals as d on d.dealid=dl.dealid
where 

select * from king_orders

join m_product_info as pi on pi.product_id=dl.product_id
where dl.dealid=5348265767


select * from king_dealitems

####### Oct-23-2013 ######################



#IMPSET1
set @transid='PNHYNI21343'; #//'PNHULV52253';
select 
if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,"not-ready","ready") ,"partial") as batch_status,from_unixtime(o.actiontime,'%D %M %h:%i:%s %Y') as str_time
,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
,o.id as orderid,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.status,o.shipped,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
,si.available_qty,si.product_id
from king_orders o
join m_product_deal_link pdl on pdl.itemid=o.itemid
join t_stock_info si on si.product_id=pdl.product_id
join king_transactions tr using(transid) 
WHERE tr.actiontime between 1379961000 and 1380047399 
group by o.transid order by tr.actiontime desc



select * from king_deals

select * from king_dealitems di
join m_product_deal_link pdl on pdl.itemid=di.id 
join t_stock_info si on si.product_id=pdl.product_id
where di.dealid=8626924263

select * from king_orders
select * from m_product_deal_link;
select * from t_stock_info where ;
select * from king_transactions

#IMPSET2
#1. GET TRANSACTIONS
set @transid='PNHYNI21343'; #'PNHULV52253';
set @s=1186857000; set @e=1382550000;
SELECT
if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,"not-ready","ready") ,"partial") as batch_status,
o.id as orderid,from_unixtime(o.actiontime,'%D %M %h:%i:%s %Y') as str_time,o.itemid,o.quantity,o.status,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount
,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
,di.id as itemid,di.name as dealname,di.price,di.available,di.pic
,pdl.itemid,pdl.product_id,pdl.product_mrp,pdl.qty
,si.stock_id,si.product_id,si.available_qty,si.location_id,si.rack_bin_id,si.product_barcode
from king_orders o
join king_transactions tr on tr.transid=o.transid
join king_dealitems di on di.id=o.itemid
join m_product_deal_link pdl on pdl.itemid=o.itemid
join t_stock_info si on si.product_id=pdl.product_id
join king_deals deal on deal.dealid=di.dealid
WHERE o.actiontime between @s and @e and tr.transid=@transid
group by o.id order by o.actiontime DESC;

#2.
SELECT if(o.quantity<=si.available_qty, 'ready' ,'partial') as batch_status,
                                                            o.id as orderid,o.itemid,o.quantity,o.status,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount
                                                            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                                                            ,di.id as itemid,di.name as dealname,di.price,di.available,di.pic
                                                            ,pdl.itemid,pdl.product_id,pdl.product_mrp,pdl.qty
                                                            ,si.stock_id,si.product_id,si.available_qty,si.location_id,si.rack_bin_id,si.product_barcode
                                                            from king_orders o
                                                            join king_transactions tr on tr.transid=o.transid
                                                            join king_dealitems di on di.id=o.itemid
                                                            join m_product_deal_link pdl on pdl.itemid=o.itemid
                                                            join t_stock_info si on si.product_id=pdl.product_id
                                                            join king_deals deal on deal.dealid=di.dealid
                                                            WHERE tr.transid='PNHQGI68526'
                                                            group by o.id order by o.actiontime DESC




set @transid='PNHYNI21343';  #'PNHULV52253';
set @s=1186857000; set @e=1382550000;
SELECT
o.id as orderid,from_unixtime(o.actiontime,'%D %M %h:%i:%s %Y') as str_time,o.itemid,o.quantity,o.status,o.bill_person
,tr.transid,tr.amount,tr.paid,tr.init,tr.actiontime,tr.status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.trans_created_by
,di.id as itemid,di.name as deal,di.price,di.available,di.pic
,pdl.*
#,si.*
from king_orders o
join king_transactions tr on tr.transid=o.transid
join king_dealitems di on di.id=o.itemid
join m_product_deal_link pdl on pdl.itemid=o.itemid
#join t_stock_info si on si.product_id=pdl.product_id
join king_deals deal on deal.dealid=di.dealid
WHERE o.actiontime between @s and @e and tr.transid=@transid
group by o.id order by o.actiontime DESC;
# pdl.product_id=7263 and di.name="Colgate Toothpaste Herbal 100Gm" and 



####### Oct-24 -2013 ######################
# Transaction
select 
                            if(sum(si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,'Not Ready','Batch Ready'),'Partial Ready')) as batch_status
                            ,from_unixtime(o.actiontime,'%D %M %h:%i:%s %Y') as str_time
                            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                            ,o.id as orderid,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.status,o.shipped,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
                            from king_orders o
                            join m_product_deal_link pdl on pdl.itemid=o.itemid
                            join t_stock_info si on si.product_id=pdl.product_id
                            join king_transactions tr using(transid) 
                            WHERE tr.actiontime between 1379961000 and 1380047399 
                            group by o.transid order by tr.actiontime desc

#Orders of transaction
SELECT if((si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, 'ready' ,'partial')) as batch_status,
                                                            o.id as orderid,o.itemid,o.quantity,o.status,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount
                                                            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                                                            ,di.id as itemid,di.name as dealname,di.price,di.available,di.pic
                                                            ,pdl.itemid,pdl.product_id,pdl.product_mrp,pdl.qty
                                                            ,si.stock_id,si.product_id,si.available_qty,si.location_id,si.rack_bin_id,si.product_barcode
                                                            ,if(p.is_sourceable=1,'yes','no') as sorceable
                                                            from king_orders o
                                                            join king_transactions tr on tr.transid=o.transid
                                                            join king_dealitems di on di.id=o.itemid
                                                            join m_product_deal_link pdl on pdl.itemid=o.itemid
                                                            join t_stock_info si on si.product_id=pdl.product_id
                                                            join m_product_info p on p.product_id=pdl.product_id
                                                            join king_deals deal on deal.dealid=di.dealid
                                                            WHERE tr.transid='PNHUMD82972'
                                                            group by o.id order by o.actiontime DESC


select * from t_stock_info where product_id=87

#################################
#Oct-25

select franchise_id  from pnh_m_franchise_info where is_suspended = 0 

select 
                           # if(sum(si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,'Not Ready','Batch Ready'),'Partial Ready')) as batch_status,
			from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
                            ,o.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                            ,o.id as orderid,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.status,o.shipped,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
                            from king_orders o
                            #join m_product_deal_link pdl on pdl.itemid=o.itemid
                            #join t_stock_info si on si.product_id=o.product_id
                            join king_transactions tr on tr.transid=o.transid
                            WHERE tr.init between 1382553000 and 1382725799 
                            group by tr.transid order by tr.init desc


select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
								from king_orders o 
								join king_transactions c on o.transid = c.transid 
								join pnh_member_info pu on pu.user_id=o.userid 
							where c.init between 1382553000 and 1382725799  
							group by c.transid  
							order by c.init desc 

	
SELECT #if((si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, 'ready' ,'partial')) as batch_status,
                                                            o.id as orderid,o.itemid,o.quantity,o.status,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount
                                                            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                                                            ,di.id as itemid,di.name as dealname,di.price,di.available,di.pic
                                                            #,pdl.itemid,pdl.product_id,pdl.product_mrp,pdl.qty
                                                            #,si.stock_id,si.product_id,si.available_qty,si.location_id,si.rack_bin_id,si.product_barcode
                                                            #,if(p.is_sourceable=1,'yes','no') as sorceable
                                                            from king_orders o
                                                            join king_transactions tr on tr.transid=o.transid
                                                            join king_dealitems di on di.id=o.itemid
                                                            #join m_product_deal_link pdl on pdl.itemid=o.itemid
                                                            #join t_stock_info si on si.product_id=pdl.product_id
                                                            #join m_product_info p on p.product_id=pdl.product_id
                                                            join king_deals deal on deal.dealid=di.dealid
                                                            WHERE tr.transid='PNH42934'
                                                            group by o.id order by o.actiontime DESC

select * from king_transactions where transid="PNH42934";
select * from king_deals order by sno desc
select * from king_dealitems order by sno desc
select * from m_product_info
select * from t_stock_info
select * from t_reserved_batch_stock

select * from (select count(pdl.product_id) as linked_prdt,pdl.* from m_product_deal_link pdl 
group by product_id order by created_on desc) as res  where res.linked_prdt>1

select is_pnh from king_transactions where transid ="PNH42934";
select o.* from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=1 and t.transid="PNH42934";


select stock_id,product_barcode,stock_id,product_id,available_qty,location_id,rack_bin_id,mrp,if((mrp),1,0) as mrp_diff 
                                            from t_stock_info where mrp > 0 and available_qty > 0 
                                            order by product_id desc,mrp_diff,mrp


select a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount from king_orders a
join king_dealitems b on a.itemid = b.id 
#where a.transid = ?
order by a.status 

select product_id,sum(available_qty) as stock from t_stock_info where available_qty > 0 and mrp > 0 group by product_id
# product_id in ('".implode("','",$productids)."') and

#Oct-26-2013
select * from m_product_info where product_id='152346'
select * from m_product_deal_link
select * from king_dealitems
select * from king_orders
select * from king_transactions where transid='PNH78836' order by init desc
select * from king_dealitems where id='8191888251'

SELECT 
                                                            o.id as orderid,o.itemid,o.quantity,o.status,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount
                                                            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                                                            ,di.id,di.name as dealname,di.price,di.available,di.pic
							,pdl.product_id
                                                            from king_orders o
                                                            join king_transactions tr on tr.transid=o.transid
                                                            join king_dealitems di on di.id=o.itemid
								join m_product_deal_link pdl on pdl.itemid=o.itemid
                                                            join king_deals deal on deal.dealid=di.dealid
                                                            WHERE tr.transid='PNH95477'
                                                            group by o.id order by o.actiontime DESC

select * from shipment_batch_process where created_on between 1382553000 and now()  order by batch_id desc

select * from proforma_invoices
select p_invoice_no as invoice_no,transid,count(order_id) as c from proforma_invoices where p_invoice_no in ('".implode("','",$invoices)."') group by transid

select * from shipment_batch_process_invoice_link order by packed_on desc;


#imp to GET BATCH ID 1
select * from proforma_invoices 
where transid='PNH78836'  
#and order_id='6755916991'
order by createdon desc
#2
select * from shipment_batch_process_invoice_link sbp
join shipment_batch_process sb on sb.batch_id=sbp.batch_id
where p_invoice_no='111588'
order by packed_on desc;

#final
select sb.status from shipment_batch_process_invoice_link sbp
join proforma_invoices pi on pi.p_invoice_no=sbp.p_invoice_no
join shipment_batch_process sb on sb.batch_id=sbp.batch_id
#where pi.transid='PNH78836'
where sb.status>2
order by sb.created_on desc;

#===========================
#Oct-28

select 
                    #if(sum(si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,'Not Ready','Batch Ready'),'Partial Ready')) as batch_status,
                    from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
                    ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                    ,o.id as orderid,o.itemid,o.status,o.quantity,o.shipped,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
                    from king_orders o
                    #join m_product_deal_link pdl on pdl.itemid=o.itemid
                    #join t_stock_info si on si.product_id=pdl.product_id
                    join king_transactions tr on tr.transid=o.transid
                    join king_dealitems di on di.id=o.itemid 
                    WHERE tr.actiontime between 1377973800 and 1382984999 
                    group by o.transid order by tr.actiontime desc

select 
                    #if(sum(si.available_qty)=0,'No stock',if(o.quantity<=si.available_qty, if(sum(o.quantity)<=0,'Not Ready','Batch Ready'),'Partial Ready')) as batch_status,
                    from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
                    ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled
                    ,o.id as orderid,o.itemid,o.status,o.quantity,o.shipped,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
                    from king_orders o
                    #join m_product_deal_link pdl on pdl.itemid=o.itemid
                    #join t_stock_info si on si.product_id=pdl.product_id
                    join king_transactions tr on tr.transid=o.transid
                    join king_dealitems di on di.id=o.itemid 
                    WHERE tr.actiontime between 1380565800 and 1382984999  and o.status in(1,2)
                    group by o.transid order by tr.actiontime desc; ==>479 rows 

select * from king_dealitems
select * from king_transactions
select * from king_orders
select * from king_deals
select * from pnh_m_franchise_info;
select * from pnh_m_territory_info
select * from pnh_towns

# menuid,brandid,franchise_id,territory_id,town_id
select 
                    from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
                    ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
                    ,o.id as orderid,o.itemid,o.status,o.quantity,o.shipped,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
		,d.brandid,d.menuid
		#,f.franchise_id	
                    from king_orders o
                    join king_transactions tr on tr.transid=o.transid
                    join king_dealitems di on di.id=o.itemid 
		join king_deals d on d.dealid=di.dealid
		join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
		join pnh_m_territory_info ter on ter.id = f.territory_id 
		join pnh_towns twn on twn.id=f.town_id
                    WHERE tr.actiontime between 1380565800 and 1382984999  and o.status in(1,2)
                    group by o.transid order by tr.actiontime desc; 
	==>334

# select 479-334 => 145 rows removed

#Oct-29_2013
select 
                    from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
                    ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
                    ,o.id as orderid,o.itemid,o.status,o.quantity,o.shipped,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
		,d.brandid,d.menuid
		#,f.franchise_id	
                    from king_orders o
                    join king_transactions tr on tr.transid=o.transid
                    join king_dealitems di on di.id=o.itemid 
		join king_deals d on d.dealid=di.dealid
		join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
		join pnh_m_territory_info ter on ter.id = f.territory_id 
		join pnh_towns twn on twn.id=f.town_id
                    WHERE tr.actiontime between 1380565800 and 1383071399 and tr.batch_enabled=1 and tr.transid='PNH76511'
#and o.status in(1,2)
                    group by o.transid order by tr.actiontime desc 

#Oct-30-2013

select * from king_transactions;
select * from proforma_invoices where transid='PNH58326'
select * from king_dealitems;
select * from king_deals;
select 
            from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
            ,o.id as orderid,o.itemid,o.status,o.quantity,o.shipped,o.ship_person,o.ship_address,o.ship_city,o.quantity,o.ship_pincode,o.ship_state,o.ship_email,o.ship_phone
        ,d.brandid,d.menuid
        #,f.franchise_id	
            from king_orders o
            join king_transactions tr on tr.transid=o.transid
            join king_dealitems di on di.id=o.itemid 
        join king_deals d on d.dealid=di.dealid
        join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        join pnh_m_territory_info ter on ter.id = f.territory_id 
        join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380652200 and 1383157799 #  and tr.batch_enabled=1 and o.status IN (1,2) 
            group by o.transid order by tr.actiontime desc 

select sb.status,sb.batch_id,sb.num_orders,sb.*
			from shipment_batch_process_invoice_link sbp
                        left join proforma_invoices pi on pi.p_invoice_no=sbp.p_invoice_no
                        left join shipment_batch_process sb on sb.batch_id=sbp.batch_id
                        #where pi.transid='PNH76511'
                        order by sb.created_on desc

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = '".$o['transid']."'
                                                                    $order_cond order by c.p_invoice_no desc


select 
            from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
            from king_transactions tr
        join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        join pnh_m_territory_info ter on ter.id = f.territory_id 
        join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1383157799  
            order by tr.actiontime desc
==>401

select 
            from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time
            ,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
            from king_transactions tr
         join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
         join pnh_m_territory_info ter on ter.id = f.territory_id 
         join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1383157799 and batch_enabled=1 
            order by tr.actiontime desc 
==> 1680

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                    from king_orders a
                                                                    join king_dealitems b on a.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = a.transid   
                                                                    left join proforma_invoices c on c.order_id = a.id 
                                                                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    left join king_invoice e on e.invoice_no = sd.invoice_no
                                                            where a.status in (0,1) and a.transid = 'PNH93579'
                                                            order by c.p_invoice_no desc


select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,dl.brandid,dl.menuid
                                                                    from king_orders a
                                                                    join king_dealitems b on a.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = a.transid   
                                                                    left join proforma_invoices c on c.order_id = a.id 
                                                                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    left join king_invoice e on e.invoice_no = sd.invoice_no
                                                            where a.status in (0,1) and a.transid = 'PNH15791' 
                                                            order by c.p_invoice_no desc   ==>155782

#1
select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,dl.brandid,dl.menuid
                                                                    from king_orders a
                                                                    join king_dealitems b on a.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = a.transid   
                                                                    left join proforma_invoices c on c.order_id = a.id 
                                                                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    left join king_invoice e on e.invoice_no = sd.invoice_no
                                                            where a.status in (0,1) and a.transid = 'PNH15791' 
                                                            order by c.p_invoice_no desc   ==>155782

#2
select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,dl.brandid,dl.menuid
                                                                    from king_orders a
                                                                    join king_dealitems b on a.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = a.transid   
                                                                    left join proforma_invoices c on c.order_id = a.id 
                                                                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    left join king_invoice e on e.invoice_no = sd.invoice_no
														
								left join pnh_m_franchise_info  f on f.franchise_id=t.franchise_id
								left join pnh_m_territory_info ter on ter.id = f.territory_id 
								left join pnh_towns twn on twn.id=f.town_id

                                                            where a.status in (0,1) and a.transid = 'PNH15791' 
                                                            order by c.p_invoice_no desc   ==>155782


### Oct-31-2013 ###

select distinct o.*,from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
		,o.*
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382034599 and o.status in (0,1) and batch_enabled=1
            group by tr.transid order by tr.actiontime desc 
==>538 ==> 189

select #e.invoice_no,e.invoice_status
#sd.packed,sd.shipped,sd.shipped_on,a.status,
c.*,
o.id,o.itemid,o.quantity,b.name
,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                    from king_orders o
                                                                    join king_dealitems b on o.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = o.transid   
                                                                    left join proforma_invoices c on c.order_id = o.id 
                                                                    #left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    #left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                        left join pnh_m_franchise_info  f on f.franchise_id=t.franchise_id
                                                                        left join pnh_m_territory_info ter on ter.id = f.territory_id 
                                                                        left join pnh_towns twn on twn.id=f.town_id
                                                            where t.actiontime between 1380565800 and 1383157799 and o.status in (0,1) and t.batch_enabled=1
                                                            order by c.p_invoice_no desc
==>679

select #e.invoice_no,e.invoice_status
                                                                #sd.packed,sd.shipped,sd.shipped_on,o.status,
                                                                c.*,
                                                                o.id,o.itemid,o.quantity,b.name
                                                                ,i_orgprice,i_price,i_discount,i_coup_discount
                                                                    from king_orders a
                                                                    join king_dealitems b on o.itemid = b.id
                                                                    join king_deals dl on dl.dealid = b.dealid
                                                                    join king_transactions t on t.transid = o.transid   
                                                                    left join proforma_invoices c on c.order_id = o.id 
                                                                    #left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                    #left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                        left join pnh_m_franchise_info  f on f.franchise_id=t.franchise_id
                                                                        left join pnh_m_territory_info ter on ter.id = f.territory_id 
                                                                        left join pnh_towns twn on twn.id=f.town_id
                                                            where t.actiontime between 1380565800 and 1382034599 and t.batch_enabled=1 and o.status in (0,1) #and o.transid = ? 
                                                            order by c.p_invoice_no desc


select distinct o.*,from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
		,o.*
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382034599 and o.status in (0,1) and batch_enabled=1
            group by tr.transid order by tr.actiontime desc 

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.batch_id,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
					,dl.menuid
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no and e.invoice_status=0
                                                                where a.transid = 'PNHJLD29923'
								#c.p_invoice_no = '45134'
                                                                order by c.p_invoice_no desc
select * from proforma_invoices;

#Nove_01_2013
#1
select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
		,o.*
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382120999 and o.status in (0,1) and batch_enabled=1 
            group by tr.transid order by tr.actiontime desc 
==>189 515s
#2
select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.franchise_id,tr.batch_enabled,tr.franchise_id
		,o.*
		,f.franchise_name
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382120999 and o.status in (0,1) and batch_enabled=1 
            group by tr.transid order by tr.actiontime desc
==>189 - 609s>624s =>1216

select * from king_orders;
desc king_orders;
desc king_transactions;
desc king_deals;
desc king_dealitems;
desc pnh_m_franchise_info;
desc pnh_m_territory_info;
desc pnh_towns;
desc pnh_menu;
desc king_brands;

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled,tr.franchise_id
		,o.*
		,f.franchise_name,f.territory_id,f.town_id
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
		left join king_dealitems di on di.id=o.itemid
		left join king_deals dl on dl.dealid=di.dealid
		left join pnh_menu m on m.id = dl.menuid
		left join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382120999 and o.status in (0,1) and batch_enabled=1 
            group by tr.transid order by tr.actiontime desc
==>1248s 189

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382120999 and o.status in (0,1) and batch_enabled=1  and dl.menuid=112 and dl.brandid=74323882 and f.territory_id=11
            group by tr.transid order by tr.actiontime desc