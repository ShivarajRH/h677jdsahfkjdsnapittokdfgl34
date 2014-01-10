
use database snapittoday_db_oct;
## Nov_06_2013
select a.status from king_orders a where a.transid = 'PNH15791' and a.status = 0

select sum(a.status) as total from king_orders a where a.transid = 'PNHBZC28433' and a.status in (0,1)

set @tid = 'PNHBZC28433';
select (a.status) as total from king_orders a where a.status = 0 and a.transid = @tid;

select * from snapittoday_db_oct.support_tickets_msg;

select a.status,sd.batch_id,c.p_invoice_no as total from king_orders a left join proforma_invoices c on c.order_id = a.id left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no  where a.transid = @tid and a.status in (0,1)

set @tid = 'PNHBZC28433';
select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status
		from king_orders a
		#join king_transactions tr on tr.transid = a.transid
            #left join proforma_invoices c on c.order_id = a.id
            #left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no
            where a.status in (0,1) #and a.transid=@tid
		group by a.transid) as ddd
			where ddd.orders_status=0
# => 34298/608ms => 95/203ms
#,sd.batch_id,c.p_invoice_no

select * from king_transactions where transid='7';

set @tid = 'PNHBZC28433';
select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status
		from king_orders a
		join king_transactions tr on tr.transid = a.transid
            where a.status in (0,1) and tr.batch_enabled=1 #and a.transid=@tid
		group by a.transid) as ddd
			where ddd.orders_status=0
#==> 91/343ms => 85/593ms => 60/577ms

select o.* from king_transactions t 
join king_orders o on o.transid=t.transid and o.status=0 
where t.batch_enabled=1 #and t.transid=?
#==>

set @transid='PNH15791';
select c.invoice_status,a.status,sd.batch_id,c.p_invoice_no as total
from king_orders a 
 join proforma_invoices c on c.order_id = a.id 
left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no  
where a.status in (0,1) and c.invoice_status=1 and a.transid = @transid

#==>51877/62ms

select c.invoice_status,a.status,sd.batch_id,c.p_invoice_no as total from king_orders a join proforma_invoices c on c.order_id = a.id left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no where c.invoice_status=1 and a.status in (0,1)
 and a.transid = 'PNH15791';

select c.invoice_status,a.status,sd.batch_id,c.p_invoice_no as total 
from king_orders a 
left join proforma_invoices c on c.order_id = a.id 
left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
where a.status in (0,1) and a.transid = 'PNH14386'; #PNHZLA55363

# Nov_07_2013

select distinct c.invoice_status,a.status,sd.batch_id,c.p_invoice_no,
from king_orders a 
left join proforma_invoices c on c.order_id = a.id 
left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
where a.status in (0,1) and sd.status=0 and a.transid = 'PNHFDF78772'; #PNHQTX46549 #PNH73789#PNH14386#PNHZLA55363

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
            WHERE tr.actiontime between 1380565800 and 1382639399 and o.status in (0,1) and tr.batch_enabled=1 and o.shipped=0 
            group by tr.transid order by tr.actiontime desc

#========================================================
alter table `pnh_m_manifesto_sent_log` add column `ref_box_no` bigint (10) DEFAULT '0' NULL  after `no_ofboxes`;

alter table `t_grn_product_link` add column `dp_price` decimal (15,4) DEFAULT '0' NULL  after `mrp`;
alter table `t_po_product_link` add column `dp_price` decimal (15,4) DEFAULT '0' NULL  after `mrp`;

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.batch_id,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,c.p_invoice_no 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id and c.invoice_status = 1 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = ? and a.status in (0,1) and c.invoice_status = 1 
                                                                order by c.p_invoice_no desc



// check if any pending order for processing invoice 
select a.id,b.invoice_no 
	from king_orders a 
	left join king_invoice b on a.id = b.order_id and invoice_status = 1 
	where a.transid = 'PNHXKZ65744' and b.id is null 
group by a.id 

## Nov 08 ==========

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
	left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
	left join king_invoice i on o.id = i.order_id and i.invoice_status = 1 
            WHERE tr.actiontime between 1380565800 and 1382725799 and o.status in (0,1) and tr.batch_enabled=1 and i.id is not null
            group by tr.transid order by tr.actiontime desc

#==>105/1201ms ==>87/1185ms

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
            WHERE tr.actiontime between 1380565800 and 1382725799 and o.status in (0,1) and tr.batch_enabled=1 
            group by tr.transid order by tr.actiontime desc

select * from t_reserved_batch_stock;

alter table king_invoice add column ref_dispatch_id bigint(11) default 0;

#### Nov_09_2013 ####

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                ,i.invoice_status,sd.shipped
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
        left join king_invoice i on o.id = i.order_id and i.invoice_status = 1 
                  left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
            WHERE tr.actiontime between 1380565800 and 1384021799 and o.status in (0,1) and tr.batch_enabled=1   and i.invoice_status=1 and sd.shipped=1
            group by tr.transid order by tr.actiontime desc

#Nov_11_2013

<section class="progress window">
        <h1>Copying "Really Achieving Your Childhood Dreams"</h1>
        <details>
         <summary>Copying... <progress max="375505392" value="97543282"></progress> 25%</summary>
         <dl>
          <dt>Transfer rate:</dt> <dd>452KB/s</dd>
          <dt>Local filename:</dt> <dd>/home/rpausch/raycd.m4v</dd>
          <dt>Remote filename:</dt> <dd>/var/www/lectures/raycd.m4v</dd>
          <dt>Duration:</dt> <dd>01:16:27</dd>
	</dl>
        </details>
       </section>
### .HTACCESS FILE:
#############################################################
#	GZIP Compression
#<ifModule mod_gzip.c>
#mod_gzip_on Yes
#mod_gzip_dechunk Yes
#mod_gzip_item_include file .(html?|txt|css|js|php|pl)$
#mod_gzip_item_include handler ^cgi-script$
#mod_gzip_item_include mime ^text/.*
#mod_gzip_item_include mime ^application/x-javascript.*
#mod_gzip_item_exclude mime ^image/.*
#mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
#</ifModule>
#   End GZIP Compression code

#############################################################
### EXPIRES CACHING ##
#<IfModule mod_expires.c>
#ExpiresActive On
#ExpiresByType image/jpg "access plus 1 year"
#ExpiresByType image/jpeg "access plus 1 year"
#ExpiresByType image/gif "access plus 1 year"
#ExpiresByType image/png "access plus 1 year"
#ExpiresByType text/css "access plus 1 month"
#ExpiresByType application/pdf "access plus 1 month"
#ExpiresByType text/x-javascript "access plus 1 month"
#ExpiresByType application/x-shockwave-flash "access plus 1 month"
#ExpiresByType image/x-icon "access plus 1 year"
#ExpiresDefault "access plus 2 days"
#</IfModule>
## EXPIRES CACHING ##
=============================================================================
### Nov_12_2013

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
        
            WHERE tr.actiontime between 1380565800 and 1384280999 and o.status in (0,1) and tr.batch_enabled=1 
            group by tr.transid order by tr.actiontime desc
	#=> 192/1544 ms
##########
select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id

		left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                  left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 

            WHERE tr.actiontime between 1380565800 and 1384280999 and o.status in (0,1) and tr.batch_enabled=1  and i.invoice_status=1 and sd.shipped=1
            group by tr.transid order by tr.actiontime desc
#=>15 / 1576 ms =>DONE / CANCEL RECORDS

select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_ords
		,tr.transid,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,o.*
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                
		from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id

		left join king_invoice i on o.id = i.order_id and i.invoice_status=0
                  left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 

            WHERE tr.actiontime between 1380565800 and 1384280999 and o.status in (0,1) and tr.batch_enabled=1 and i.id is not null
            group by tr.transid order by tr.actiontime desc
####
select * from (
select distinct from_unixtime(tr.init,'%D %M %h:%i:%s %Y') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.id,o.itemid,o.brandid,o.quantity,o.time,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
        
		left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                  left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        
            WHERE tr.actiontime between 1380565800 and 1384280999 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
            group by o.transid order by tr.actiontime desc
) as g where is_pending = 0 group by transid;

# is_pending=0 and
# group by tr.transid 
# transid='PNHRTR62345'
is_pending=0 => ready
is_pending=total_trans =>pending
is_pending < total_trans => partial_pending
####
select status from king_orders where transid='PNHYVV59449';

###################
# Nov_13_2013


select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.id,o.itemid,o.brandid,o.quantity,o.time,o.i_orgprice,o.bill_person,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1384367399 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null 
        group by o.transid order by tr.actiontime desc) as g where  g.is_pending = g.total_trans  group by transid

select from_days(6767)

SELECT STR_TO_DATE('10.31.2013',GET_FORMAT(DATE,'INR'));
        -> '2003-10-31'

#New work ---------------------------------------
CREATE TABLE `pnh_town_courier_priority_link` (  
	  `id` bigint(11) NOT NULL AUTO_INCREMENT,       
	  `town_id` int(11) DEFAULT '0',                 
	  `courier_priority_1` int(5) DEFAULT '0',       
	  `courier_priority_2` int(5) DEFAULT '0',       
	  `courier_priority_3` int(5) DEFAULT '0',       
	  `delivery_hours_1` int(3) DEFAULT '0',         
	  `delivery_hours_2` int(3) DEFAULT '0',         
	  `delivery_hours_3` int(3) DEFAULT '0',         
	  `is_active` tinyint(1) DEFAULT '0',            
	  `created_on` datetime DEFAULT NULL,            
	  `created_by` int(11) DEFAULT '0',              
	  `modified_on` datetime DEFAULT NULL,           
	  `modified_by` int(11) DEFAULT '0',             
	  PRIMARY KEY (`id`)
	);
#---------------------------------------------------------
select * from m_courier_info where is_active =1;

select * from `pnh_town_courier_priority_link` tcp
join pnh_towns tw on tw.id = tcp.town_id
where tcp.is_active=1;

# Nov_14_2013

select distinct tw.id as townid,tw.town_name,tcp.* from pnh_towns tw
left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id
order by town_name;

select town_id from `pnh_town_courier_priority_link` where town_id='3'

select * from `pnh_town_courier_priority_link` tcp;

select * from pnh_towns
select * from pnh_m_territory_info;

select ter.id,ter.territory_name from pnh_m_territory_info ter
join pnh_towns tw on tw.territory_id = ter.id
group by ter.id order by territory_name

select distinct tw.id as townid,tw.town_name,tcp.* from pnh_towns tw
    left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1    
    order by town_name;

# Nov_15_2013

select distinct tw.id as townid,tw.town_name,tcp.* from pnh_towns tw
    left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1    
    order by town_name;

select * from `pnh_town_courier_priority_link`;

select ci.courier_name,ci.ref_partner_id,is_active,pt.name,pt.trans_prefix,trans_mode from m_courier_info ci
left join `partner_info` as pt on pt.id = ci.ref_partner_id
order by courier_name asc;
#============================================
alter table `m_courier_info` add column `ref_partner_id` int (11) DEFAULT '0' NULL  after `remarks`;
#============================================
select * from partner_info;
select * from m_courier_info;

select * from partner_info order by name;

select * from m_courier_awb_series where courier_id='';
select * from m_courier_pincodes where courier_id='';
select * from m_courier_info where courier_id='';

#######################
delete from m_courier_awb_series where courier_id='21';
delete from m_courier_pincodes where courier_id='21';
delete from m_courier_info where courier_id='21';
#######################
select * from m_courier_pincodes where courier_id=20
select * from m_courier_awb_series where courier_id=21

insert into m_courier_info(courier_name,ref_partner_id,is_active) values('SHIVARAJ COUR','6',0)

select pt.*,ci.ref_partner_id,ci.is_active from partner_info pt
order by name;

#Nov_16_2013

select * from `pnh_town_courier_priority_link`;

#========================================
alter table `pnh_town_courier_priority_link` add column `delivery_type_priority1` int (3) DEFAULT '0' NULL  after `delivery_hours_3`, add column `delivery_type_priority2` int (3) DEFAULT '0' NULL  after `delivery_type_priority1`, add column `delivery_type_priority3` int (3) DEFAULT '0' NULL  after `delivery_type_priority2`
#========================================

insert into `pnh_town_courier_priority_link` (`town_id`,`courier_priority_1`,`courier_priority_2`,`courier_priority_3`
,`delivery_hours_1`,`delivery_hours_2`,`delivery_hours_3`,`is_active`,`created_on`,`created_by`) 
values ('6','1','0','0','24','0','0',0,0,0,1,'2013-11-16 12:03:45','1')

select * from `pnh_town_courier_priority_link` where town_id=6;

select distinct tw.id as townid,tw.town_name,tcp.* from pnh_towns tw
                                                            left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1
                                                             where territory_id=1
                                                            order by town_name

select cp.* from pnh_town_courier_priority_link cp
left join pnh_towns tw on tw.id = cp.town_id
#left join pnh_m_franchise_info f on tw.id = f.town_id where f.franchise_id = '163';

select ci.courier_name,cp.* #courier_priority_1,cp.delivery_hours_1,cp.delivery_type_priority1 
from pnh_m_franchise_info f
left join pnh_towns tw on tw.id = f.town_id
left join pnh_town_courier_priority_link cp on cp.town_id=tw.id and cp.is_active=1
left join m_courier_info ci on ci.courier_id=cp.courier_priority_1
 where f.franchise_id = '335' and ci.courier_name!='';

select ci.courier_name,cp.courier_priority_2,cp.delivery_hours_2,cp.delivery_type_priority2 
from pnh_m_franchise_info f left join pnh_towns tw on tw.id = f.town_id 
left join pnh_town_courier_priority_link cp on cp.town_id=tw.id and cp.is_active=1 
left join m_courier_info ci on ci.courier_id=cp.courier_priority_2 
where f.franchise_id = '335' and ci.courier_name!='';

select * from pnh_town_courier_priority_link order by id desc

select * from pnh_towns;

# Nov_18_2013
select * from pnh_m_franchise_info;

select distinct tw.id as townid,tw.town_name,count(frn.franchise_id) as fran_count,tcp.* from pnh_towns tw
	    left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1
	    left join `pnh_m_franchise_info` frn on frn.town_id = tw.id and frn.is_suspended=0
	     where tw.territory_id=3
	    group by tw.id order by tw.town_name asc

select ci.*,pt.name,pt.trans_prefix,trans_mode from m_courier_info ci
                                        left join `partner_info` as pt on pt.id = ci.ref_partner_id
                                        order by courier_name asc


select  ci.*,if(ci.is_active=1,"PNH",if(ref_partner_id=0,"SIT",pt.name)) as used_for,pt.trans_prefix,trans_mode 
from m_courier_info ci
                                        left join `partner_info` as pt on pt.id = ci.ref_partner_id
                                        order by courier_name asc

select distinct ci.ref_partner_id,is_active
,if(ci.is_active=1,'PNH',if(ref_partner_id=0,'SIT',pt.name)) as used_for
,if(ci.is_active=1,'PNH',if(ref_partner_id=0,'SIT',pt.trans_prefix)) as trans_prefix,trans_mode 
from m_courier_info ci
left join `partner_info` as pt on pt.id = ci.ref_partner_id
order by courier_name asc

#Assigned users
select distinct tw.id as townid,tw.town_name,count(frn.franchise_id) as fran_count,tcp.* from pnh_towns tw
    left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1
    left join `pnh_m_franchise_info` frn on frn.town_id = tw.id and frn.is_suspended=0
     where  tw.territory_id=3
    group by tw.id order by tw.town_name;

#Unassigned users
select distinct tw.id as townid,tw.town_name,count(frn.franchise_id) as fran_count,tcp.* from pnh_towns tw
	left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1
	left join `pnh_m_franchise_info` frn on frn.town_id = tw.id and frn.is_suspended=0
	where  tw.territory_id=3 and tcp.courier_priority_1 is NOT null
	group by tw.id order by tw.town_name;

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.batch_id,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,c.p_invoice_no 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id and c.invoice_status = 1
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no and sd.invoice_no = 0
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where #a.transid = 'PNHFFV43589' and 
												a.status in (0,1) and sd.shipped=0
                                                                order by c.p_invoice_no desc;


# Nov_19_2013
select * from shipment_batch_process_invoice_link sd
select * from proforma_invoices;
select * from king_invoice;

select product_id,product,location,sum(rqty) as qty from ( 
                            select a.product_id,c.product_name as product,concat(concat(rack_name,bin_name),'::',b.mrp) as location,a.qty as rqty 
                                    from t_reserved_batch_stock a 
                                    join t_stock_info b on a.stock_info_id = b.stock_id 
                                    join m_product_info c on c.product_id = b.product_id 
                                    join m_rack_bin_info d on d.id = b.rack_bin_id 
                                    join shipment_batch_process_invoice_link e on e.p_invoice_no = a.p_invoice_no and invoice_no = 0 
                                    where e.batch_id in (?)
                            group by a.id  ) as g 
                            group by product_id,location

select product_id,product,location,sum(rqty) as qty from ( 
                            select a.product_id,c.product_name as product,concat(concat(rack_name,bin_name),'::',b.mrp) as location,a.qty as rqty 
                                    from t_reserved_batch_stock a 
                                    join t_stock_info b on a.stock_info_id = b.stock_id 
                                    join m_product_info c on c.product_id = b.product_id 
                                    join m_rack_bin_info d on d.id = b.rack_bin_id 
                                    join shipment_batch_process_invoice_link e on e.p_invoice_no = a.p_invoice_no and invoice_no = 0 
                                    where 
				e.p_invoice_no = '111602' 
				#e.batch_id ='4370'
                            group by a.id  ) as g 
                            group by product_id,location;

select distinct tw.id as townid,tw.town_name,count(frn.franchise_id) as fran_count,tcp.* 
from pnh_towns tw left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1 
left join `pnh_m_franchise_info` frn on frn.town_id = tw.id and frn.is_suspended=0 
where 1=1 and tcp.courier_priority_1 is NOT null group by tw.id order by tw.town_name;

# Nov_20_2013

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.batch_id,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,c.p_invoice_no 
	,b.dealid
			from king_orders a
			join king_dealitems b on a.itemid = b.id
			join king_deals dl on dl.dealid = b.dealid
			join king_transactions t on t.transid = a.transid   
			left join proforma_invoices c on c.order_id = a.id and c.invoice_status = 1
			left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no
			left join king_invoice e on e.invoice_no = sd.invoice_no
		where a.transid = 'PNH16711' and a.status in (0,1) 
		order by c.p_invoice_no desc

select * from king_orders;
select * from king_dealitems;
select * from king_deals;
select * from m_product_info where tmp_dealid='3737332157';

desc king_orders;
desc king_dealitems;
desc king_deals;
desc shipment_batch_process_invoice_link
desc m_product_info

select product_id,product,location,sum(rqty) as qty from ( 
                        select rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                                from t_reserved_batch_stock rbs 
                                join t_stock_info si on rbs.stock_info_id = si.stock_id 
                                join m_product_info pi on pi.product_id = si.product_id 
                                join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                                join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0 
                                where e.p_invoice_no=? 
                        group by rbs.id  ) as g 
                        group by product_id,location

select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.id,o.itemid,o.brandid,o.quantity,o.time,o.i_orgprice,o.bill_person,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1384972199 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null
        group by o.transid order by tr.actiontime desc) as g where  g.is_pending = g.total_trans group by transid #shipped

desc shipment_batch_process_invoice_link;

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.batch_id,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,c.p_invoice_no 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id and c.invoice_status = 1
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = 'PNH45687' and a.status in (0,1)
                                                                order by c.p_invoice_no desc;

select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
		,tkt.status as ticket_status
		from king_transactions tr
		left join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
	        left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
	left outer join support_tickets tkt on tkt.transid = tr.transid
        WHERE tr.actiontime between 1383244200 and 1384972199 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null 
        group by o.transid) as g where  g.`is_pending`=0  group by transid order by g.init desc;

#1981ms/14rows =>1965ms/14
# g.is_pending = g.total_trans = ready
# g.`is_pending` < g.`total_trans` and g.`is_pending` <> 0 => partial
# 
select * from king_orders ;

select u.name as user,t.*,a.name as assignedto from support_tickets t 
left outer join king_admin a on a.id=t.assigned_to 
left outer join king_users u on u.userid=t.user_id
 order by t.created_on desc

#Nov_21_2013
select in.invoice_no,in.invoice_no, brand.name as brandname, in.mrp,in.tax as tax, in.discount, in.phc,in.nlc, in.service_tax,ordert.quantity 
,item.nlc,item.phc,ordert.*, item.service_tax_cod,item.name,if(length(item.print_name),item.print_name,item.name) as print_name, item.pnh_id,f.offer_text,f.immediate_payment
from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid 
join king_deals as deal on deal.dealid=item.dealid 
left join king_brands as brand on brand.id=deal.brandid 
left join pnh_m_offers f on f.id= ordert.offer_refid 
join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id 
where in.invoice_no='20141010400' or split_inv_grpno = '20141010400';

select * from king_invoice;
desc king_invoice;
desc king_orders;
desc king_deals;

######
select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1385058599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null 
        group by o.transid) as g where  g.is_pending = g.total_trans  group by transid order by g.init desc
#=> 51rows/1778ms

select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
        from king_transactions tr
		left join king_orders o on o.transid=tr.transid
		
		join king_dealitems di on di.id=o.itemid
	
		join king_deals dl on dl.dealid=di.dealid

	#join m_product_info pi on pi.tmp_dealid=dl.dealid

		left join pnh_menu m on m.id = dl.menuid
		left join king_brands bs on bs.id = o.brandid
        left join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        left join pnh_m_territory_info ter on ter.id = f.territory_id 
        left join pnh_towns twn on twn.id=f.town_id
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no
	
        WHERE tr.actiontime between 1380565800 and 1385058599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null 
        group by o.transid) as g where  g.is_pending = g.total_trans  group by transid order by g.init desc

select * from king_orders
select * from m_product_info;
select * from king_dealitems

select * from t_stock_info;

select c.courier_name as courier,bi.shipped,bi.shipped_on,bi.awb,bi.courier_id,bi.batch_id,bi.packed,bi.shipped,i.createdon,i.invoice_status,i.invoice_no,bi.p_invoice_no 
from king_invoice i 
left outer join shipment_batch_process_invoice_link bi on bi.invoice_no=i.invoice_no and bi.shipped
left outer join m_courier_info c on c.courier_id=bi.courier_id
where i.transid='PNHDJL51773' 
group by i.invoice_no;

select i.p_invoice_no,c.courier_name as courier,bi.shipped,bi.shipped_on,bi.awb,bi.courier_id,bi.batch_id,bi.packed,bi.shipped,i.createdon,i.invoice_status,bi.p_invoice_no 
from proforma_invoices i 
left outer join shipment_batch_process_invoice_link bi on bi.p_invoice_no=i.p_invoice_no 
left outer join m_courier_info c on c.courier_id=bi.courier_id 
where i.transid='PNHDJL51773'  group by i.p_invoice_no;

select i.p_invoice_no,c.courier_name as courier,bi.shipped,bi.shipped_on,bi.awb,bi.courier_id,bi.batch_id,bi.packed,bi.shipped,i.createdon,i.invoice_status,bi.p_invoice_no 
from proforma_invoices i 
left outer join shipment_batch_process_invoice_link bi on bi.p_invoice_no=i.p_invoice_no 
left outer join m_courier_info c on c.courier_id=bi.courier_id 
where i.transid='PNHDJL51773'  group by i.p_invoice_no;

# NOV_22_2013

select * from shipment_batch_process_invoice_link

select * from king_deals d join m_product_info p on d.dealid=p.tmp_dealid;

select * from king_transactions;

#########################################
create table `m_batch_config` ( `id` bigint (20) NOT NULL AUTO_INCREMENT , `batch_grp_name` varchar (150) , `assigned_menuid` int (11) DEFAULT '0', `batch_size` int (11) DEFAULT '0', `assigned_uid` int (10) , PRIMARY KEY ( `id`));

alter table `shipment_batch_process_invoice_link` add column `assigned_userid` int (11) DEFAULT '1' NOT NULL  after `delivered_by`;

#########################################

select * from pnh_menu; #=>27r 100-126

electronics
	=> 112 - Mobiles & Tablets
	=> 118 - Computers & Peripherals

Beauty
	=> 100 - Beauty & Cosmetics



select * from m_batch_config;

select * from proforma_invoices;

#Nov_23_2013
select * from shipment_batch_process_invoice_link where batch_id=5000 limit 0,5; =>6

insert into shipment_batch_process(num_orders,batch_remarks,created_on) values(?,?,?),array($ttl_inbatch,$batch_remarks,date('Y-m-d H:i:s'));
insert_id();

update shipment_batch_process_invoice_link set batch_id = '' where id='';

select * from (
select *,count(p_invoice_no) as s from shipment_batch_process_invoice_link
group by p_invoice_no) as g
 where g.s>1;


select sd.*,from_unixtime(tr.init) from king_transactions tr
join king_orders as o on o.transid=tr.transid
join proforma_invoices as `pi` on pi.order_id = o.id
join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
join king_dealitems dl on dl.id = o.itemid
join king_deals d on d.dealid = dl.dealid  and menuid in (112,118)
where batch_id=5000 
order by tr.init asc
limit 0,5

select * from proforma_invoices
select * from king_orders;
desc king_orders;
desc king_transactions
desc proforma_invoices
desc shipment_batch_process_invoice_link
desc king_dealitems
desc king_deals;


select * from m_batch_config;

select username from king_admin where id=1;

# =============================
alter table `shipment_batch_process_invoice_link` add column `assigned_userid` int (11) DEFAULT '0' NOT NULL  after `delivered_by`;
# =============================
select *,from_unixtime(createdon) from proforma_invoices order by id desc;


select * from shipment_batch_process order by batch_id desc

select * from shipment_batch_process_invoice_link order by batch_id desc

update `shipment_batch_process_invoice_link` set batch_id = 5010 and assigned_userid = '38' where id='361710'
update `shipment_batch_process_invoice_link` set batch_id = 5010 and assigned_userid = '38' where id='361711'

select d.menuid,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr join king_orders as o on o.transid=tr.transid join proforma_invoices as `pi` on pi.order_id = o.id 
join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no join king_dealitems dl on dl.id = o.itemid join king_deals d on d.dealid = dl.dealid and d.menuid in ('112,118') 
where sd.batch_id=5000 order by tr.init asc limit 0,5;

# Nov_25_2013

select d.menuid,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
		join king_orders as o on o.transid=tr.transid
		join proforma_invoices as `pi` on pi.order_id = o.id
		join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
		join king_dealitems dl on dl.id = o.itemid
		join king_deals d on d.dealid = dl.dealid  and d.menuid in ('112,118')
		where sd.batch_id=5000
		order by tr.init asc
		limit 0,2;

select * from shipment_batch_process order by batch_id desc

select * from shipment_batch_process_invoice_link order by id desc;


select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1385404199 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
        group by o.transid) as g where  g.is_pending = g.total_trans  group by transid order by g.init desc

#==> 72/1919ms

/* select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1385404199 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
        group by f.territory_id) as g where  g.is_pending = g.total_trans  group by g.territory_id order by g.init desc*/

#group by o.transid) as g where  g.is_pending = g.total_trans  group by transid order by g.init desc

select distinct count(f.franchise_id) as num_trans,from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time,tr.transid
		,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
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
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1385404199 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
	group by f.franchise_id order by tr.init desc
	
select distinct * from king_transactions tr 
#WHERE tr.actiontime between 1380565800 and 1385404199 
group by tr.franchise_id

#### IMP 1 ########
set @transid='PNH89211'; #'PNH47533'; #PNH29334 

set @franchise_id = '17';
select count(f.franchise_id) as total_trans_by_fran
from king_orders o
join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
join king_dealitems di on o.itemid = di.id
where f.franchise_id = @franchise_id and tr.actiontime between 1380565800 and 1385404199  and i.id is null
group by f.franchise_id
order by tr.init;
#tr.transid = @transid;
#



#### IMP 2 ########
set @franchise_id = '17';
select * #sum(o.status) as is_pending
from king_orders o
join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
join king_dealitems di on o.itemid = di.id
where tr.transid = @transid  and i.id is null;


select * from pnh_m_franchise_info where franchise_id='17';

##### IMP 3 +++++++++++++++++
set @franchise_id = '17';
select o.*,tr.* #,di.name 
from king_orders o
join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
left join king_invoice i on o.id = i.order_id and i.invoice_status = 1

where f.franchise_id = @franchise_id and tr.actiontime between 1380565800 and 1385404199  and i.id is null
group by tr.transid
order by tr.init;

# Nov_26_2013


select distinct 
		f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
        from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        join pnh_m_territory_info ter on ter.id = f.territory_id 
        join pnh_towns twn on twn.id=f.town_id
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
        WHERE tr.actiontime between 1380565800 and 1385490599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
	group by f.franchise_id order by tr.init desc




select * from ( 
                    select transid,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status  
                    from (
                    select o.transid,o.status,count(*) as ttl_o,tr.init
                            from king_orders o
			join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            where o.status in (0,1)  and i.id is null  and o.transid like '%PNH%'
                            group by o.transid,o.status 
                    ) as g  where g.init between 1380565800 and 1385490599
                    group by g.transid )as g1 having g1.trans_status = 'ready'




set @transid='PNH89211';
select distinct 
	f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
	,ter.territory_name
	,twn.town_name
from king_transactions tr
	join king_orders o on o.transid=tr.transid
	join king_dealitems di on di.id=o.itemid
	join king_deals dl on dl.dealid=di.dealid
	join pnh_menu m on m.id = dl.menuid
	join king_brands bs on bs.id = o.brandid
join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
join pnh_m_territory_info ter on ter.id = f.territory_id 
join pnh_towns twn on twn.id=f.town_id
	left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
	left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
WHERE tr.actiontime between 1380565800 and 1385490599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null and tr.transid = @transid
group by f.franchise_id order by tr.init desc


select distinct o.transid,
                        f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                        ,ter.territory_name
                        ,twn.town_name
                from king_transactions tr
                        join king_orders o on o.transid=tr.transid
                        join king_dealitems di on di.id=o.itemid
                        join king_deals dl on dl.dealid=di.dealid
                        join pnh_menu m on m.id = dl.menuid
                        join king_brands bs on bs.id = o.brandid
                join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
                join pnh_m_territory_info ter on ter.id = f.territory_id 
                join pnh_towns twn on twn.id=f.town_id
                        left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                        left join shipment_batch_process_invoice_link sd on sd.invoice_no = i.invoice_no 
                WHERE tr.actiontime between 1380565800 and 1385490599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null and o.transid in ('PNH11173,PNH15738,PNH15823,PNH16711,PNH16782,PNH19782,PNH19853,PNH22848,PNH23461,PNH26592,PNH29431,PNH29531,PNH32787,PNH36986,PNH39715,PNH39735,PNH39793,PNH42951,PNH45687,PNH47533,PNH48698,PNH49463,PNH51721,PNH52341,PNH54344,PNH55354,PNH56848,PNH58326,PNH58537,PNH59297,PNH59839,PNH62146,PNH62736,PNH62836,PNH64325,PNH66972,PNH68579,PNH72673,PNH73789,PNH75881,PNH76274,PNH76511,PNH78836,PNH82149,PNH82455,PNH82916,PNH83842,PNH86795,PNH86988,PNH89211,PNH89454,PNH91222,PNH95477,PNH96882,PNH98878,PNHAJZ96314,PNHBZC28433,PNHCFL75676,PNHCPM28331,PNHDJL51773,PNHFFV43589,PNHGQT85898,PNHJAD84472,PNHKJF35238,PNHQJF86154,PNHQRZ64714,PNHQTZ72497,PNHSZL33118,PNHUKJ57331,PNHULV52253,PNHUYQ92238,PNHYQK72368,PNHZVF76225')
                group by f.franchise_id order by tr.init desc





select distinct o.transid,
                        f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                        ,ter.territory_name
                        ,twn.town_name
                from king_transactions tr
                        join king_orders o on o.transid=tr.transid
                        join king_dealitems di on di.id=o.itemid
                        join king_deals dl on dl.dealid=di.dealid
                        join pnh_menu m on m.id = dl.menuid
                        join king_brands bs on bs.id = o.brandid
                join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
                join pnh_m_territory_info ter on ter.id = f.territory_id 
                join pnh_towns twn on twn.id=f.town_id
                WHERE o.transid in ('PNH11173,PNH15738,PNH15823,PNH16711,PNH16782,PNH19782,PNH19853,PNH22848,PNH23461,PNH26592,PNH29431,PNH29531,PNH32787,PNH36986,PNH39715,PNH39735,PNH39793,PNH42951,PNH45687,PNH47533,PNH48698,PNH49463,PNH51721,PNH52341,PNH54344,PNH55354,PNH56848,PNH58326,PNH58537,PNH59297,PNH59839,PNH62146,PNH62736,PNH62836,PNH64325,PNH66972,PNH68579,PNH72673,PNH73789,PNH75881,PNH76274,PNH76511,PNH78836,PNH82149,PNH82455,PNH82916,PNH83842,PNH86795,PNH86988,PNH89211,PNH89454,PNH91222,PNH95477,PNH96882,PNH98878,PNHAJZ96314,PNHBZC28433,PNHCFL75676,PNHCPM28331,PNHDJL51773,PNHFFV43589,PNHGQT85898,PNHJAD84472,PNHKJF35238,PNHQJF86154,PNHQRZ64714,PNHQTZ72497,PNHSZL33118,PNHUKJ57331,PNHULV52253,PNHUYQ92238,PNHYQK72368,PNHZVF76225,s')
                group by f.franchise_id order by tr.init desc


###############
group_concat()

select * from ( 
                    select transid,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status  
                    from (
                    select o.transid,o.status,count(*) as ttl_o,tr.init
                            from king_orders o
                            join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            left join proforma_invoices `pi` on pi.order_id = o.id
                            where o.status in (0,1)  and i.id is null and tr.franchise_id != 0 
                            group by o.transid,o.status 
                    ) as g where g.init between ? and ?
                    group by g.transid )as g1 having g1.trans_status = 'ready'

select * from proforma_invoices

desc king_orders;


select pt.courier_name as p_courier_name,t.init as ordered_on,b.*,pi.transid as pi_transid,b.p_invoice_no, 

                pi.invoice_status as p_invoice_status,i.transid,i.invoice_status  

                from shipment_batch_process_invoice_link b  

                left outer join proforma_invoices pi on pi.p_invoice_no=b.p_invoice_no  

                left outer join king_invoice i on i.invoice_no=b.invoice_no  

                left outer join king_transactions t on t.transid=pi.transid  

                left outer join partner_transaction_details pt on pt.transid=t.transid and pt.order_no = t.partner_reference_no  

                where 1  and t.franchise_id='59' and pi.invoice_status = 1 and b.invoice_no = 0  and b.p_invoice_no in  (111653,111579,111679,111670,111680,111686,111674,111643,111645,111584,111688,111582,111580,111699,111576,111694,111687,111690,111651,111581,111685,111689,111675)

                group by b.p_invoice_no


select pt.courier_name as p_courier_name,t.init as ordered_on,b.*,pi.transid as pi_transid,b.p_invoice_no, 
                pi.invoice_status as p_invoice_status,i.transid,i.invoice_status  
                from shipment_batch_process_invoice_link b  
                left outer join proforma_invoices pi on pi.p_invoice_no=b.p_invoice_no  
                left outer join king_invoice i on i.invoice_no=b.invoice_no  
                left outer join king_transactions t on t.transid=pi.transid  
                left outer join partner_transaction_details pt on pt.transid=t.transid and pt.order_no = t.partner_reference_no  
                where 1  and t.franchise_id='59' and pi.invoice_status = 1 and b.invoice_no = 0  and b.invoice_no in (111653,111579,111679,111670,111680,111686,111674,111643,111645,111584,111688,111582,111580,111699,111576,111694,111687,111690,111651,111581,111685,111689,111675)
                group by b.p_invoice_no

select pt.courier_name as p_courier_name,t.init as ordered_on,b.*,pi.transid as pi_transid,b.p_invoice_no, 
                pi.invoice_status as p_invoice_status,i.transid,i.invoice_status  
                from shipment_batch_process_invoice_link b  
		
                left outer join proforma_invoices pi on pi.p_invoice_no=b.p_invoice_no  
                left outer join king_invoice i on i.invoice_no=b.invoice_no  
                left outer join king_transactions t on t.transid=pi.transid  
		
                left outer join partner_transaction_details pt on pt.transid=t.transid and pt.order_no = t.partner_reference_no  
                where 1  and t.franchise_id='17' and pi.invoice_status = 1 and b.invoice_no = 0  and b.p_invoice_no in (111671,111655,111657,111672,111336)
                group by b.p_invoice_no

select * from m_product_info
select * from king_invoice
select * from partner_transaction_details
select * from shipment_batch_process_invoice_link
select * from king_deals
select * from king_dealitems
select * from king_orders


select * from t_imei_no where 
##############TO RESET THE IMEI NUMBER FOR MOBILE ##############################
update t_imei_no set status=0 and order_id=0 where imei_no = '358956054359626';
#############################################################################
desc proforma_invoices;
desc king_invoice;

alter table king_invoice add column invoice_qty int(5) default 0 after discount;

desc shipment_batch_process_invoice_link;

update t_imei_no set status = 0,product_id = 8398,order_id=0 where status = 1 limit 10;



select * from (
	select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                ,sd.batch_id
        from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
		join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
		join pnh_m_territory_info ter on ter.id = f.territory_id 
		join pnh_towns twn on twn.id=f.town_id
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
		left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
        WHERE tr.actiontime between 1375295400 and 1385490599 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  
        group by o.transid
) as g where  g.is_pending = g.total_trans  group by transid order by g.init desc



select * from ( 
select transid,group_concat(p_inv_nos) as p_inv_nos,status,sum(ttl_o),count(*) as t,if(count(*)>1,'Partial',(if(status,'Ready','Pending'))) as trans_status  
from (
select o.transid,ifnull(group_concat(distinct p_invoice_no),'') as p_inv_nos,o.status,count(*) as ttl_o

from king_orders o 
join king_transactions t on t.transid = o.transid  
left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1  
left join king_invoice i on i.order_id = o.id and i.invoice_status = 1  
where o.status in (0,1) and o.transid like '%PNH%' and i.id is null  
group by o.transid,o.status 
) as g  
group by g.transid )as g1 
group by g1.transid
having trans_status = 'partial' 

#Nov_27_2013

select * from shipment_batch_process_invoice_link order by id desc




select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                ,sd.batch_id,sd.assigned_userid
        from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id
        join pnh_m_territory_info ter on ter.id = f.territory_id 
        join pnh_towns twn on twn.id=f.town_id
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
        WHERE tr.actiontime between 1375295400 and 1385576999 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null # and sd.assigned_userid = 1  
        group by o.transid) as g where  g.`is_pending` = 0  group by transid order by g.init desc


alter table `shipment_batch_process_invoice_link` add column `assigned_userid` int (11) DEFAULT '0' NOT NULL  after `delivered_by`;

select * from pnh_m_franchise_info;

select * from shipment_batch_process_invoice_link where p_invoice_no='111274'
select * from shipment_batch_process_invoice_link where p_invoice_no='111282'


select * from ( 
                    select transid,group_concat(p_inv_nos) as p_inv_nos,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id  
                    from (
                    select o.transid,ifnull(group_concat(distinct p_invoice_no),'') as p_inv_nos,o.status,count(*) as ttl_o,tr.franchise_id
                            from king_orders o
                            join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1 
                            where o.status in (0,1)  and i.id is null and tr.franchise_id != 0 
                            group by o.transid,o.status 
                    ) as g 
                    group by g.transid )as g1 having g1.trans_status='pending'


select * from ( 
                    select count(transid),group_concat(p_inv_nos) as p_inv_nos,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id  
                    from (
				    select o.transid,ifnull(group_concat(distinct p_invoice_no),'') as p_inv_nos,o.status,count(*) as ttl_o,tr.franchise_id
					    from king_orders o
					    join king_transactions tr on tr.transid=o.transid
					    left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
					    left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1 
					    where o.status in (0,1)  and i.id is null and tr.franchise_id != 0 
					    group by o.transid,o.status 

                    ) as g group by trans_status 

)as g1 group by g1.trans_status and g1.trans_status='ready'


select * from (
select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
		,sum(o.status) as is_pending,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
		,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
		,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
		,ter.territory_name
		,twn.town_name
		,dl.menuid,m.name as menu_name,bs.name as brand_name
                ,sd.batch_id,sd.assigned_userid
        from king_transactions tr
		join king_orders o on o.transid=tr.transid
		join king_dealitems di on di.id=o.itemid
		join king_deals dl on dl.dealid=di.dealid
		join pnh_menu m on m.id = dl.menuid
		join king_brands bs on bs.id = o.brandid
        join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id and f.is_suspended = 0
        join pnh_m_territory_info ter on ter.id = f.territory_id 
        join pnh_towns twn on twn.id=f.town_id
                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
        WHERE tr.actiontime between 1375295400 and 1385576999 and o.status in (0,1) and tr.batch_enabled=1 and i.id is null  and tr.transid='PNH57586'
        group by o.transid) as g group by transid order by g.init desc


##======================
select o.*,tr.*,di.name,o.status,pi.p_invoice_no,o.quantity
                                from king_orders o
                                join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1 
                                join king_dealitems di on di.id = o.itemid 
                                where i.id is null and tr.transid ='PNH57586'
                                order by tr.init,di.name 



## NOV_28_2013

select o.itemid from  king_orders o 
where o.id='8896996428'; ==>1415651433
#o.transid='PNH89454'

select qty,product_id from m_product_deal_link where itemid = '1415651433';

select sum(available_qty) as stock from t_stock_info where product_id = '8364' and available_qty > 0 and mrp > 0 group by product_id

select * from king_transactions

select o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id
,di.name,o.status,pi.p_invoice_no,o.quantity,f.franchise_id,pi.p_invoice_no
                                from king_orders o
                                join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1 
                                join king_dealitems di on di.id = o.itemid 
                                where f.franchise_id = '59' and tr.actiontime between '1375295400' and '1385663399'  and i.id is null and tr.transid in ('PNH15738','PNH15823','PNH16782','PNH19782','PNH19853','PNH26592','PNH29531','PNH36986','PNH39332','PNH39793','PNH48698','PNH49463','PNH51412','PNH51721','PNH52341','PNH53357','PNH55354','PNH58263','PNH58455','PNH58537','PNH59839','PNH61216','PNH62146','PNH62621','PNH63981','PNH66269','PNH68579','PNH73961','PNH76274','PNH76511','PNH78821','PNH82149','PNH82916','PNH83211','PNH83586','PNH83626','PNH83834','PNH89454')
                                order by tr.init,di.name;


### Nov_29_2013

select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status
                    from king_orders a
                    join king_transactions tr on tr.transid = a.transid and tr.is_pnh=1
                    where a.status in (0,1) and tr.batch_enabled=1 
                    group by a.transid) as ddd
                    where ddd.orders_status=0

select * from t_stock_info where stock_id='26411' or product_id='26411'

select * from proforma_invoices where transid='H18APE26411'

select * from shipment_batch_process where batch_id = 5000;
_______________________________________________________________________________________________
###############################################################################################
update shipment_batch_process set status = 2 where batch_id != 5000;

update proforma_invoices  a 
	join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
	set invoice_status = 0 
	where invoice_status = 1 and b.invoice_no = 0 
	
update king_orders set status = 0 where status = 1 ;
###############################################################################################
_______________________________________________________________________________________________

RENAME from snapittoday_db_oct snapittoday_db_nov;

STOCK ALLOTED - PNHCIH65185 with 1 product Alloted. 
STOCK ALLOTED - PNHCIH65185 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 2 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 2 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 2 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHDJV14356 with 1 product Alloted. 
STOCK ALLOTED - PNHEQC73122 with 1 product Alloted. 
STOCK ALLOTED - PNHEQC73122 with 1 product Alloted. 
STOCK ALLOTED - PNHEQC73122 with 1 product Alloted. 
STOCK ALLOTED - PNHEQC73122 with 1 product Alloted. 
STOCK ALLOTED - PNHEQC73122 with 1 product Alloted. 
STOCK ALLOTED - PNHESC16249 with 1 product Alloted. 
STOCK ALLOTED - PNHESC16249 with 1 product Alloted. 
STOCK ALLOTED - PNHESC16249 with 1 product Alloted. 
STOCK ALLOTED - PNHFSF34938 with 1 product Alloted. 
STOCK ALLOTED - PNHFTA26362 with 1 product Alloted. 
STOCK ALLOTED - PNHFTA26362 with 1 product Alloted. 
STOCK ALLOTED - PNHFTA26362 with 1 product Alloted. 
STOCK ALLOTED - PNHFTA26362 with 1 product Alloted. 
STOCK ALLOTED - PNHFTA26362 with 1 product Alloted. 
STOCK ALLOTED - PNHGGW34943 with 1 product Alloted. 
STOCK ALLOTED - PNHGGW34943 with 1 product Alloted. 
STOCK ALLOTED - PNHGGW34943 with 1 product Alloted. 
STOCK ALLOTED - PNHGGW34943 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHAL36241 with 1 product Alloted. 
STOCK ALLOTED - PNHHZW89963 with 1 product Alloted. 
STOCK ALLOTED - PNHHZW89963 with 1 product Alloted. 
STOCK ALLOTED - PNHHZW89963 with 1 product Alloted. 
STOCK ALLOTED - PNHHZW89963 with 1 product Alloted. 
STOCK ALLOTED - PNHHZW89963 with 1 product Alloted. 
STOCK ALLOTED - PNHJYW63221 with 1 product Alloted. 
STOCK ALLOTED - PNHJYW63221 with 1 product Alloted. 
STOCK ALLOTED - PNHJYW63221 with 1 product Alloted. 
STOCK ALLOTED - PNHJYW63221 with 2 product Alloted. 
STOCK ALLOTED - PNHMKA33359 with 1 product Alloted. 
STOCK ALLOTED - PNHMKA33359 with 1 product Alloted. 
STOCK ALLOTED - PNHQBY27499 with 1 product Alloted. 
STOCK ALLOTED - PNHQBY27499 with 1 product Alloted. 
STOCK ALLOTED - PNHQBY27499 with 1 product Alloted. 
STOCK ALLOTED - PNHQBY27499 with 1 product Alloted. 
STOCK ALLOTED - PNHTQE79561 with 1 product Alloted. 
STOCK ALLOTED - PNHTQE79561 with 1 product Alloted. 
STOCK ALLOTED - PNHTQE79561 with 1 product Alloted. 
STOCK ALLOTED - PNHWXR42666 with 1 product Alloted. 
STOCK ALLOTED - PNHWXR42666 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted. 
STOCK ALLOTED - PNHZVY46717 with 1 product Alloted.


create database snapittoday_db_nov;
drop database snapittoday_db_nov;

CREATE DATABASE snapittoday_db_nov / DROP DATABASE snapittoday_db_oct;


http://localhost/snapitto/admin/product/153072

#Nov_30_2013

select md5('superadmin1')//('17c4520f6cfd1ab53d8745e84681eb49')

#1
select product_id,product,location,sum(rqty) as qty from ( 
                select rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0 
                        where e.p_invoice_no='114324'
                group by rbs.id  ) as g 
                group by product_id,location;
#2
select product_id,product,location,sum(rqty) as qty 
from ( 
                select  * #rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0 
                        where e.p_invoice_no='114324'
) group by rbs.id  ) as g 
#group by product_id,location

#3
select dl.menuid,mnu.name as menuname,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id
			join king_orders o on o.id = rbs.order_id
			join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu mnu on mnu.id = dl.menuid
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and e.invoice_no = 0 
                        where e.p_invoice_no='114324'

select * from t_reserved_batch_stock
select * from king_orders

desc t_reserved_batch_stock ==> batch_id,p_invoice_no, product_id,stock_info_id,order_id,
 
desc t_stock_info ==> stock_id,product_id,location_id,rack_bin_id

desc m_product_info ==>product_id,pid,tmp_itemid,tmp_dealid

desc king_deals ==> dealid,catid,brandid,vendorid,menuid

desc king_orders ==>id>> orderid,transid,userid,itemid,brandid,

desc king_dealitems => dealid,id>itemid,name,quantity,available,tmp_pnh_itemid,tmp_pnh_dealid,is_pnh

desc shipment_batch_process_invoice_link => batch_id,p_invoice_no,invoice_no,assigned_userid

desc king_transactions => transid,orderid,franchise_id,is_pnh,batch_enabled

desc pnh_menu ==> id>>menuid,name,status
select * from pnh_menu where status=1

# Final
======================================================================================================================================
select menuid,menuname,product_id,product,location,sum(rqty) as qty from ( 
                select dl.menuid,mnu.name as menuname,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id
			
			join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and e.invoice_no = 0 

			join king_orders o on o.id = rbs.order_id
			join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1

                        where e.p_invoice_no='114308'
                group by rbs.id  ) as g 
                group by product_id,location;
======================================================================================================================================

#other as p_inv_nos ,CONCAT_WS(',',p_inv_nos) as p
select * from ( 
                    select transid,group_concat(p_inv_nos) as p_inv_nos,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id  
                    from (
                    select o.transid,group_concat(distinct pi.p_invoice_no) as p_inv_nos,o.status,count(*) as ttl_o,tr.franchise_id,tr.actiontime
                            from king_orders o
                            join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            
                            left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1 
                            left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
                            
                            where o.status in (0,1)  and i.id is null and tr.franchise_id != 0 and pi.p_invoice_no='106198' #$cond
                            group by o.transid,o.status
                    ) as g 
                    group by g.transid )as g1   having g1.trans_status = 'partial';

SELECT TRIM(LEADING 'x' FROM 'xxxbarxxx');

select * from shipment_batch_process_invoice_link where p_invoice_no='106198'

select menuid,menuname,product_id,product,location,sum(rqty) as qty from ( 
                select dl.menuid,mnu.name as menuname,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0
                        
                        join king_orders o on o.id = rbs.order_id
                        join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1
                        
                        where e.p_invoice_no='114308' 
                group by rbs.id  ) as g 
                group by product_id,location

