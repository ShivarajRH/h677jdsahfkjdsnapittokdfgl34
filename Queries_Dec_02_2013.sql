select * from m_batch_config

###########################################################################################
alter table `m_batch_config` #add column `territory_id` int (11) DEFAULT '0' NULL  after `assigned_uid`, add column `townid` int (11) DEFAULT '0' NULL  after `territory_id`
,change `batch_grp_name` `batch_grp_name` varchar (150)  NULL  COLLATE utf8_unicode_ci 
, change `assigned_menuid` `assigned_menuid` varchar (100)  NULL  COLLATE utf8_unicode_ci 
, change `assigned_uid` `assigned_uid` varchar (100)  NULL  COLLATE utf8_unicode_ci;

alter table `snapittoday_db_nov`.`m_batch_config` drop column `townid`, drop column `territory_id`, drop column `assigned_uid`;

###########################################################################################

select  *#d.menuid,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) 

from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid  #and d.menuid in (?)
                                where sd.batch_id=5000
                                order by tr.init asc;

select * from shipment_batch_process where batch_id='5000'
select * from shipment_batch_process_invoice_link where batch_id='5000';

select o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id,di.name
                                ,o.status,pi.p_invoice_no,o.quantity
                                ,f.franchise_id,pi.p_invoice_no
                                from king_orders o
                                join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1 
                                join king_dealitems di on di.id = o.itemid 
                                where i.id is null # and tr.transid in ('PNHESC16249') # and f.franchise_id = ? $cond 
                                order by tr.init desc; #,di.name

#### Nov_03_2013 ###
select * from shipment_batch_process where batch_id=5000

D:--> added assigned_userid, territory_id,batch_configid fields to shipment_batch_process 

D:--> picklist-invoice id shoulkd carry 

--> generate picklist for un-grouped batches

###########################################################################################
alter table `shipment_batch_process` 
	add column `assigned_userid` int (11) DEFAULT '0' NULL  after `status`, 
	add column `territory_id` int (11) DEFAULT '0' NULL  after `assigned_userid`, 
	add column `batch_configid` int (11) DEFAULT '0' NULL  after `territory_id`;

alter table `snapittoday_db_nov`.`m_batch_config` drop column `assigned_uid`

alter table `snapittoday_db_nov`.`m_batch_config` add column `group_assigned_uid` varchar (120)  NULL  after `batch_size`;

alter table `snapittoday_db_nov`.`shipment_batch_process_invoice_link` drop column `assigned_userid`;

###########################################################################################


select * from m_batch_config;

select o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    from king_orders o
                    join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1
			#left join shipment_batch_process_invoice_link sd on sd.p_invoice_no=pi.p_invoice_no
                    join king_dealitems di on di.id = o.itemid 
                    where i.id is null #and tr.transid = ?
                    order by tr.init,di.name;

select * from proforma_invoices
select * from shipment_batch_process_invoice_link
select * from king_invoice

select distinct o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id,di.name
                                ,o.status,pi.p_invoice_no,o.quantity
                                ,f.franchise_id,pi.p_invoice_no
                                ,sd.batch_id
                                from king_orders o
                                join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1
                                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
                                join king_dealitems di on di.id = o.itemid 
                                where f.franchise_id = '83'  and i.id is null and tr.transid in ('PNHTQE79561') #('PNHEIP95585')
                                order by tr.init desc;


#Dec_04_2013

select * from shipment_batch_process_invoice_link

select * from king_admin

select * from ( 
                    select transid,TRIM(BOTH ',' FROM group_concat(p_inv_nos)) as p_inv_nos,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id  
                    from (
                    select o.transid,ifnull(group_concat(distinct pi.p_invoice_no),'') as p_inv_nos,o.status,count(*) as ttl_o,tr.franchise_id,tr.actiontime
                            from king_orders o
                            join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1 
                            left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
                            left join shipment_batch_process sbp on sbp.batch_id = sd.batch_id
                            where o.status in (0,1)  and i.id is null and tr.franchise_id != 0  and sbp.assigned_userid = 37  and ((sd.packed=0 and sd.p_invoice_no > 0) or (sd.p_invoice_no is null and sd.packed is null ))
                            group by o.transid,o.status
                    ) as g 
                    group by g.transid )as g1 having g1.trans_status = 'ready';

select * from (
            select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
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
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id #and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id 
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null  and tr.transid in ('PNHEQC73122')
            group by o.transid) as g  group by transid order by  g.actiontime DESC
#=================================================

select distinct o.itemid,d.menuid,mn.name as menuname,f.territory_id,
	sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) 
from king_transactions tr
                                    join king_orders as o on o.transid=tr.transid
                                    join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                    join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                    join king_dealitems dl on dl.id = o.itemid
                                    join king_deals d on d.dealid = dl.dealid
				
				join pnh_menu mn on mn.id=d.menuid
				join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                    where sd.batch_id='5000' and f.territory_id='4'
                                    order by tr.init asc

#                                   limit 0,4
=>115rows

select * from pnh_m_territory_info

select * from shipment_batch_process where batch_id='5000';

####################################################################
delete from shipment_batch_process  where batch_id='5040';
####################################################################

select distinct * from shipment_batch_process_invoice_link where batch_id='5042' group by id

UPDATE `shipment_batch_process_invoice_link` SET `batch_id` = 5000 WHERE `batch_id` = '5040';

UPDATE `shipment_batch_process_invoice_link` SET `batch_id` = 5038 WHERE `id` = 372675
#=============================================================
CREATE TABLE `pnh_m_states` (                            
                `state_id` int(11) NOT NULL AUTO_INCREMENT,            
                `state_name` varchar(255) DEFAULT NULL,                
                `created_on` datetime DEFAULT NULL,                    
                `created_by` int(11) DEFAULT '0',                      
                PRIMARY KEY (`state_id`)                               
              );

#Dec_05_2013
select id,username from king_admin where account_blocked=0

select * from shipment_batch_process_invoice_link where p_invoice_no='114299';

select count(*) as ttl from proforma_invoices where p_invoice_no='114299'


select distinct d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no 
									from proforma_invoices i 
									join king_orders o on o.id=i.order_id and i.transid = o.transid 
									join m_product_deal_link pl on pl.itemid=o.itemid 
									join m_product_info p on p.product_id=pl.product_id 
									join king_dealitems d on d.id=o.itemid 
									join king_deals e on e.dealid=d.dealid 
									join shipment_batch_process_invoice_link sb on sb.p_invoice_no = i.p_invoice_no and sb.invoice_no = 0  
									where i.p_invoice_no='114299' and i.invoice_status=1 order by o.sno

select d.pic,d.is_pnh,e.menuid,i.discount,i.discount,p.product_id,p.mrp,i.transid,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no 
from proforma_invoices i 
join king_orders o on o.id=i.order_id and i.transid = o.transid 
join products_group_orders pgo on pgo.order_id=o.id 
join m_product_group_deal_link pl on pl.itemid=o.itemid 
join m_product_info p on p.product_id=pgo.product_id 
join king_dealitems d on d.id=o.itemid join king_deals e on e.dealid=d.dealid 
join shipment_batch_process_invoice_link sb on sb.p_invoice_no = i.p_invoice_no and sb.invoice_no = 0  
where i.p_invoice_no='114299' and i.invoice_status=1 order by o.sno 


### Dec_11_2013
#==============================================================================
update t_imei_no set status=0 and order_id=0 where imei_no = '356631059543977';
#==============================================================================

join king_orders o on o.id = rbs.order_id
                        join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1




set @inv_no='114077';
select distinct o.time,e.menuid,mnu.name as menuname,d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no 
									from proforma_invoices i 
									join king_orders o on o.id=i.order_id and i.transid = o.transid 
									join m_product_deal_link pl on pl.itemid=o.itemid 
									join m_product_info p on p.product_id=pl.product_id 
									join king_dealitems d on d.id=o.itemid 
									join king_deals e on e.dealid=d.dealid
									left join pnh_menu as mnu on mnu.id = e.menuid and mnu.status=1
									join shipment_batch_process_invoice_link sb on sb.p_invoice_no = i.p_invoice_no and sb.invoice_no = 0  
									where i.invoice_status=1 
											and i.p_invoice_no in (@inv_no) 
									order by o.sno;
==> 187 rows
==> 152 rows



set @inv_no='114077';
select d.pic,d.is_pnh,e.menuid,i.discount,i.discount,p.product_id,p.mrp,i.transid,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no 
									from proforma_invoices i 
									join king_orders o on o.id=i.order_id and i.transid = o.transid 
									join products_group_orders pgo on pgo.order_id=o.id 
									join m_product_group_deal_link pl on pl.itemid=o.itemid 
									join m_product_info p on p.product_id=pgo.product_id 
									join king_dealitems d on d.id=o.itemid 
									join king_deals e on e.dealid=d.dealid 
									left join pnh_menu as mnu on mnu.id = e.menuid and mnu.status=1
									join shipment_batch_process_invoice_link sb on sb.p_invoice_no = i.p_invoice_no and sb.invoice_no = 0  
									where i.invoice_status=1 and i.p_invoice_no in (@inv_no) 
									order by o.sno;

select * from king_orders
desc king_orders;

#Dec_12_2013
select consider_mrp_chng from pnh_menu

#Dec_13_2013
select note from king_transaction_notes where transid=? and note_priority=1 order by id asc limit 1;
select transid from proforma_invoices where p_invoice_no in ($p_invno_list);

select note from king_transaction_notes tnote
join proforma_invoices `pi` on pi.transid=tnote.transid
where tnote.note_priority=1 and pi.p_invoice_no in ('10004')
order by tnote.id asc limit 1;

select * from proforma_invoices where invoice_status=1


select c.status,a.product_id,product_barcode,mrp,location_id,rack_bin_id,b.stock_id 
			from t_reserved_batch_stock a 
			join t_stock_info b on a.stock_info_id = b.stock_id 
			join t_imei_no c on c.product_id = b.product_id 
	#where a.p_invoice_no = ? and a.order_id = ? and imei_no = ?
#==> 7880888 rows / 374ms

select a.status,a.product_id,b.product_barcode,b.mrp,b.location_id as location_id,
										b.rack_bin_id as rack_bin_id,
										b.stock_id from (
									select a.status,a.product_id,b.product_barcode,ifnull(b.mrp,c.mrp) as mrp,ifnull(b.location_id,c.location_id) as location_id,
										ifnull(b.rack_bin_id,c.rack_bin_id) as rack_bin_id,
										b.stock_id
										from t_imei_no a 
										left join t_stock_info b on a.stock_id = b.stock_id and a.product_id = b.product_id
										join t_grn_product_link c on c.grn_id = a.grn_id and a.product_id = c.product_id 
										where imei_no = '356631059543977' 
									) as a 
									join t_stock_info b on a.product_id = b.product_id 
									where a.mrp = b.mrp and a.location_id = b.location_id and a.rack_bin_id = b.rack_bin_id

select status,product_id from t_imei_no where imei_no = '358956056763247';


select distinct o.time,e.menuid,mnu.name as menuname,d.pic,d.is_pnh,i.discount,p.product_id,p.mrp,p.barcode,i.transid,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no 
									from proforma_invoices i 
									join king_orders o on o.id=i.order_id and i.transid = o.transid 
									join m_product_deal_link pl on pl.itemid=o.itemid 
									join m_product_info p on p.product_id=pl.product_id 
									join king_dealitems d on d.id=o.itemid 
									join king_deals e on e.dealid=d.dealid
                                                                        left join pnh_menu as mnu on mnu.id = e.menuid and mnu.status=1
                                                                        join shipment_batch_process_invoice_link sb on sb.p_invoice_no = i.p_invoice_no and sb.invoice_no = 0  
									where i.invoice_status=1 order by e.menuid DESC # and i.p_invoice_no in ($inv_no)

# Dec_14_2013

select territory_name from pnh_m_territory_info where id='3';

select distinct o.itemid,d.menuid,mn.name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid # and d.menuid in ('')
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                where sd.batch_id=5000  and f.territory_id = 3 
                                order by tr.init asc
                                limit 0,1;

select dispatch_id,group_concat(distinct a.id) as man_id,group_concat(distinct b.invoice_no) as invs 
                                                                                                    from pnh_m_manifesto_sent_log a
                                                                                                    join shipment_batch_process_invoice_link b on a.manifesto_id = b.inv_manifesto_id and b.invoice_no != 0 
                                                                                                    join proforma_invoices c on c.p_invoice_no = b.p_invoice_no and c.invoice_status = 1  
                                                                                                    join king_transactions d on d.transid = c.transid 
                                                                                                    where date(sent_on) between '2013-11-01' and '2013-11-07' and dispatch_id != 0  
                                                                                            group by franchise_id;

#100rows/93ms

select * from pnh_m_manifesto_sent_log


#Dec_16_2013

select dispatch_id,group_concat(distinct a.id) as man_id,group_concat(distinct b.invoice_no) as invs,f.territory_id
				    from pnh_m_manifesto_sent_log a
				    join shipment_batch_process_invoice_link b on a.manifesto_id = b.inv_manifesto_id and b.invoice_no != 0 
				    join proforma_invoices c on c.p_invoice_no = b.p_invoice_no and c.invoice_status = 1  
				    join king_transactions d on d.transid = c.transid 
				    join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
				    where date(sent_on) between '2013-11-01' and '2013-11-07' and dispatch_id != 0 #and f.territory_id='3'
			    group by d.franchise_id
#100 rows/187ms

select * from shipment_batch_process_invoice_link

desc shipment_batch_process_invoice_link;

select f.territory_id,d.franchise_id,dispatch_id,group_concat(distinct a.id) as man_id,group_concat(distinct b.invoice_no) as invs 
	from pnh_m_manifesto_sent_log a 
	join shipment_batch_process_invoice_link b on a.manifesto_id = b.inv_manifesto_id and b.invoice_no != 0 
	join proforma_invoices c on c.p_invoice_no = b.p_invoice_no and c.invoice_status = 1 join king_transactions d on d.transid = c.transid 
	join pnh_m_franchise_info f on f.franchise_id = d.franchise_id where date(sent_on) between '2013-11-01' and '2013-12-16' and dispatch_id != 0 and f.territory_id='3' group by d.franchise_id;


set @invs='20141014918,20141014287,20141014389';
select a.transid,a.createdon as invoiced_on,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,d.init,b.itemid,c.name,if(c.print_name,c.print_name,c.name) as print_name,c.pnh_id,group_concat(distinct a.invoice_no) as invs,
                                                        sum((i_orgprice-(i_discount+i_coup_discount))*a.invoice_qty) as amt,
                                                        sum(a.invoice_qty) as qty 
                                                from king_invoice a 
                                                join king_orders b on a.order_id = b.id 
                                                join king_dealitems c on c.id = b.itemid
                                                join king_transactions d on d.transid = a.transid
                                                where a.invoice_no in (@invs) 
                                group by itemid



####################################################################
alter table `shipment_batch_process_invoice_link` add column `is_acknowlege_printed` int (11) DEFAULT '0' NULL  after `delivered_by`;
####################################################################

select * from pnh_m_territory_info where id=3

select f.territory_id,dispatch_id,group_concat(distinct a.id) as man_id,group_concat(distinct b.invoice_no) as invs,count(distinct b.invoice_no) as ttl_invs
                                                            from pnh_m_manifesto_sent_log a
                                                            join shipment_batch_process_invoice_link b on a.manifesto_id = b.inv_manifesto_id and b.invoice_no != 0 and b.is_acknowlege_printed = 0
                                                            join proforma_invoices c on c.p_invoice_no = b.p_invoice_no and c.invoice_status = 1  
                                                            join king_transactions d on d.transid = c.transid 
                                                            join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
                                                            where date(sent_on) between '2013-11-01' and '2013-12-16' and dispatch_id != 0  and f.territory_id=16
                                                    group by d.franchise_id order by f.territory_id asc;

### Dec_17_2013 ###
update `shipment_batch_process_invoice_link` set `is_acknowlege_printed`='0' where 

select * from shipment_batch_process_invoice_link where is_acknowlege_printed>0


    Table: "picklist_log_reservation"
id
printcount
p_inv_no
created_by
createdon
####################################################################
create table `picklist_log_reservation` (  `id` bigint NOT NULL AUTO_INCREMENT , `group_no` bigint (20) DEFAULT '0', `p_inv_no` int (100) , `created_by` int (11) DEFAULT '0', `createdon` datetime , `printcount` int (100) , PRIMARY KEY ( `id`));
####################################################################

picklist_log_reservationpicklist_log_reservation

X insert into `picklist_log_reservation`(`id`,`group_no`,`p_inv_no`,`created_by`,`createdon`,`printcount`) values ( NULL,'1','114344','1',NULL,NULL);
X truncate table `snapittoday_db_nov`.`picklist_log_reservation`picklist_log_reservation;


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
update picklist_log_reservation set printcount = `printcount` + 1 where id = 11 limit 1

## Dec_18_2013 ##

select DATE_FORMAT(shipped_on,"%w-%a") as day_of_week,DATE(shipped_on) as normaldate,shipped_on,shipped,invoice_no 
from shipment_batch_process_invoice_link 
where shipped_by=1 and day_of_week is not null .
order by shipped_on ASC

#7627rows

desc shipment_batch_process_invoice_link;

select * from shipment_batch_process_invoice_link

select * from shipment_batch_process

#73124rows

select week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by from (
select DATE_FORMAT(shipped_on,"%w") as week_day,shipped_on,unix_timestamp(shipped_on) as shipped_on_time,shipped,invoice_no,shipped_by
from shipment_batch_process_invoice_link
where shipped=1
order by shipped_on DESC
) as g where g.week_day is not null and shipped_on_time!=0 and shipped_by>0 and shipped_on_time between '1383284282' and '1385619678'

# =>5086rows/62ms

# Dec_19_2013

select week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by from (
select DATE_FORMAT(shipped_on,"%w") as week_day,shipped_on,unix_timestamp(shipped_on) as shipped_on_time,shipped,invoice_no,shipped_by
from shipment_batch_process_invoice_link
where shipped=1
order by shipped_on DESC
) as g where g.week_day is not null and shipped_on_time!=0 and shipped_by>0

# =>65078rows

select * from shipment_batch_process_invoice_link
select * from batch
select * from shipment_batch_process

select week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by from (
select DATE_FORMAT(shipped_on,"%w") as week_day,shipped_on,unix_timestamp(shipped_on) as shipped_on_time,shipped,invoice_no,shipped_by
from shipment_batch_process_invoice_link
where shipped=1
order by shipped_on DESC
) as g where g.week_day is not null and shipped_on_time!=0 and shipped_by>0

select * from pnh_m_territory_info;

select employee_id,name,email,gender,city,contact_no,if(job_title=4,'TM','BE') as job_role 
from m_employee_info 
where job_title in (4,5) and is_suspended=0 order by job_title ASC;
=>28rows

select distinct emp.employee_id,emp.name,emp.email,emp.gender,emp.city,emp.contact_no,if(emp.job_title=4,'TM','BE') as job_role,ttl.is_active
from m_employee_info emp
join m_town_territory_link ttl on ttl.employee_id = emp.employee_id and ttl.is_active=1
join pnh_m_territory_info t on t.id = ttl.territory_id
where job_title in (4) and is_suspended=0 #and t.id='1'
#group by emp.employee_id
order by job_title ASC;

select * from m_town_territory_link

select  * from m_employee_info w where w.name like '%Kantaraj Naik%' and is_suspended=0;

select * from proforma_invoices
select * from shipment_batch_process_invoice_link


select week_day,shipped_on,shipped_on_time,shipped,invoice_no,shipped_by from (
select DATE_FORMAT(shipped_on,"%w") as week_day,shipped_on,unix_timestamp(shipped_on) as shipped_on_time,shipped,invoice_no,shipped_by
from shipment_batch_process_invoice_link
where shipped=1
order by shipped_on DESC
) as g where g.week_day is not null and shipped_on_time!=0 and shipped_by>0 and shipped_on_time between '1383284282' and '1385619678';


select week_day,shipped_on_time,shipped,invoice_no,shipped_by from (
	select DATE_FORMAT(sd.shipped_on,'%w') as week_day,unix_timestamp(sd.shipped_on) as shipped_on_time,sd.shipped,sd.invoice_no,sd.shipped_by
	from shipment_batch_process_invoice_link sd
	join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1  
	join king_transactions tr on tr.transid = pi.transid
	join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
    where shipped=1 and sd.shipped_by>0 and f.territory_id ='4'
    order by shipped_on DESC
) as g where g.week_day is not null and g.shipped_on_time!=0 and g.shipped_on_time between 1383244200 and 1387391400 

#5346 rows => #18008


select distinct week_day,shipped,invoice_no,shipped_by from ( select DATE_FORMAT(sd.shipped_on,'%w') as week_day,sd.shipped,sd.invoice_no,sd.shipped_by from shipment_batch_process_invoice_link sd join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1  join king_transactions tr on tr.transid = pi.transid join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id where shipped=1 and f.territory_id ='3' and unix_timestamp(sd.shipped_on) !=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 order by shipped_on DESC ) as g where g.week_day is not null

# Dec_20_2013

select week_day,shipped_on,shipped,invoice_no,shipped_by from ( select DATE_FORMAT(sd.shipped_on,'%w') as week_day,sd.shipped_on,sd.shipped,sd.invoice_no,sd.shipped_by from shipment_batch_process_invoice_link sd join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1  join king_transactions tr on tr.transid = pi.transid join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and f.territory_id ='3' and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 order by shipped_on DESC ) as g where g.week_day is not null


select distinct week_day,shipped_on,shipped,invoice_no,shipped_by from (
select DATE_FORMAT(sd.shipped_on,'%w') as week_day,sd.shipped_on,sd.shipped,sd.invoice_no,sd.shipped_by
from shipment_batch_process_invoice_link sd
join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1
join king_transactions tr on tr.transid = pi.transid
join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and f.territory_id ='' and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800
order by shipped_on DESC
) as g where g.week_day is not null;

-- select distinct week_day,shipped_on,shipped,invoice_no_str,shipped_by from (
		    select sd.shipped_on,sd.shipped,group_concat(sd.invoice_no) as invoice_no_str,count(distinct sd.invoice_no) as ttl_invs,sd.shipped_by
		    from shipment_batch_process_invoice_link sd
		    join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
		    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 and f.territory_id ='3'
		   
			order by shipped_on DESC
--                                                 ) as g where g.week_day is not null;


select f.territory_id,dispatch_id,group_concat(distinct a.id) as man_id,group_concat(distinct b.invoice_no) as invs,count(distinct b.invoice_no) as ttl_invs
                                                               from pnh_m_manifesto_sent_log a
                                                               join shipment_batch_process_invoice_link b on a.manifesto_id = b.inv_manifesto_id and b.invoice_no != 0 #$cond_join
                                                               join proforma_invoices c on c.p_invoice_no = b.p_invoice_no and c.invoice_status = 1  
                                                               join king_transactions d on d.transid = c.transid 
                                                               join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
                                                               where date(sent_on) between '2013-11-01 17:27:17' and '2013-11-27 18:44:22' and dispatch_id != 0  and f.territory_id='3'
                                                       group by d.franchise_id order by f.territory_id asc


## idea 1
select f.territory_id,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped,group_concat(sd.invoice_no) as invoice_no_str,count(distinct sd.invoice_no) as ttl_invs,sd.shipped_by
		    from pnh_m_manifesto_sent_log man
			join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
		    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
		    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 #and f.territory_id ='3'
			group by f.territory_id
			order by f.territory_id DESC


-- idea 2
select f.territory_id,t.territory_name,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped,group_concat(sd.invoice_no) as invoice_no_str,count(distinct sd.invoice_no) as ttl_invs,emp.employee_id		    
		from pnh_m_manifesto_sent_log man
			join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
		    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
 			join m_town_territory_link ttl on ttl.territory_id = f.territory_id and is_active=1
			join m_employee_info emp on emp.employee_id = ttl.employee_id


			join pnh_m_territory_info t on t.id = f.territory_id
                    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 #and f.territory_id ='3'
			group by f.territory_id
			order by f.territory_id DESC;
-- Outout: 19rows/312ms

select * from m_town_territory_link

# Dec_21_2013

select f.territory_id,t.territory_name,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped,group_concat(distinct sd.invoice_no) as invoice_no_str
			,count(distinct sd.invoice_no) as ttl_invs,count(distinct f.franchise_id) as ttl_franchises
		from pnh_m_manifesto_sent_log man
			join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
		    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
 			join m_town_territory_link ttl on ttl.territory_id = f.territory_id and is_active=1
			join m_employee_info emp on emp.employee_id = ttl.employee_id


			join pnh_m_territory_info t on t.id = f.territory_id
                    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 #and f.territory_id ='3'
			group by f.territory_id
			order by f.territory_id DESC;


#================================================================================================
alter table king_invoice add column `paid_status` tinyint(11) DEFAULT '0' after ref_dispatch_id;
alter table king_dealitems add column `billon_orderprice` tinyint(1) DEFAULT '0' after nyp_price;
alter table king_orders add column `billon_orderprice` tinyint(1) DEFAULT '0' after note;
alter table king_orders add column `is_paid` tinyint(1) DEFAULT '0' after offer_refid;
alter table king_orders add column `partner_order_id` varchar(30) DEFAULT '0' after offer_refid;

CREATE TABLE `king_partner_settelment_filedata` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `payment_uploaded_id` bigint(20) DEFAULT '0',
  `file_name` varchar(255) DEFAULT NULL,
  `orderid` bigint(20) DEFAULT '0',
  `logged_on` bigint(20) DEFAULT '0',
  `processed_by` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
);

alter table king_tmp_orders change brandid `brandid` bigint(20) DEFAULT '0',change vendorid `vendorid` bigint(20) DEFAULT '0';

alter table king_tmp_orders add column `partner_order_id` varchar(30) DEFAULT '0' after user_note;
alter table king_tmp_orders add column `partner_reference_no` varchar(100) DEFAULT '0' after partner_order_id;

alter table king_transactions change partner_reference_no `partner_reference_no` varchar(100) NOT NULL;
alter table king_transactions add column `credit_days` int(11) DEFAULT '0' after trans_grp_ref_no;
alter table king_transactions add column `credit_remarks` varchar(255) DEFAULT NULL after credit_days;

alter table m_courier_info add column `ref_partner_id` int(11) DEFAULT '0' after remarks;


CREATE TABLE `m_partner_settelment_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `order_value` double DEFAULT NULL,
  `shipping_charges` double DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_amount` double DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

alter table m_product_info add column `corr_status` tinyint(1) DEFAULT '0' after modified_by;
alter table m_product_info add column `corr_updated_on` datetime DEFAULT NULL after corr_status;

CREATE TABLE `m_product_update_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `message` text,
  `logged_by` int(11) DEFAULT '0',
  `logged_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

alter table m_vendor_brand_link change applicable_from `applicable_from` bigint(20) DEFAULT NULL;
alter table m_vendor_brand_link change applicable_till `applicable_till` bigint(20) DEFAULT NULL;


CREATE TABLE `pnh_m_creditlimit_onprepaid` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` bigint(12) DEFAULT NULL,
  `book_id` bigint(12) DEFAULT NULL,
  `book_value` bigint(12) DEFAULT NULL,
  `receipt_id` bigint(12) DEFAULT NULL,
  `credit_limit_on_prepaid` double DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `valid_till` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `pnh_m_fran_security_cheques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `franchise_id` int(11) DEFAULT '0',
  `bank_name` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(30) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `collected_on` date DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `returned_on` date DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

alter table pnh_m_franchise_info change store_open_time `store_open_time` time DEFAULT NULL;
alter table pnh_m_franchise_info change store_close_time `store_close_time` time DEFAULT NULL;
# X
alter table pnh_m_franchise_info add column `purchase_limit` double DEFAULT '0' after reason;
alter table pnh_m_franchise_info add column `new_credit_limit` double DEFAULT '0' after purchase_limit;

alter table pnh_m_manifesto_sent_log add column `lrno_update_refid` bigint(11) DEFAULT '0' after lrno;


CREATE TABLE `pnh_m_states` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `state_name` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  PRIMARY KEY (`state_id`)
);

alter table pnh_m_territory_info add column `state_id` bigint(11) DEFAULT '0' after id;

alter table pnh_menu add column `voucher_credit_default_margin` double DEFAULT '0' after default_margin;


CREATE TABLE `pnh_menu_margin_track` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) DEFAULT NULL,
  `default_margin` double DEFAULT NULL,
  `balance_discount` double DEFAULT NULL,
  `balance_amount` bigint(20) DEFAULT NULL,
  `loyality_pntvalue` double DEFAULT NULL,
  `created_by` int(12) DEFAULT NULL,
  `created_on` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

alter table pnh_sms_log_sent add column `no_ofboxes` bigint(20) DEFAULT '0' after ticket_id;

CREATE TABLE `pnh_town_courier_priority_link` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `town_id` int(11) DEFAULT '0',
  `courier_priority_1` int(5) DEFAULT '0',
  `courier_priority_2` int(5) DEFAULT '0',
  `courier_priority_3` int(5) DEFAULT '0',
  `delivery_hours_1` int(3) DEFAULT '0',
  `delivery_hours_2` int(3) DEFAULT '0',
  `delivery_hours_3` int(3) DEFAULT '0',
  `delivery_type_priority1` int(3) DEFAULT '0',
  `delivery_type_priority2` int(3) DEFAULT '0',
  `delivery_type_priority3` int(3) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_billedmrp_change_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` bigint(11) DEFAULT '0',
  `p_invoice_no` int(11) DEFAULT '0',
  `packed_mrp` double DEFAULT NULL,
  `billed_mrp` double DEFAULT NULL,
  `remarks` text,
  `logged_on` datetime DEFAULT NULL,
  `logged_by` int(5) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_imei_update_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `imei_no` varchar(255) DEFAULT NULL,
  `product_id` bigint(11) DEFAULT '0',
  `stock_id` bigint(11) DEFAULT '0',
  `grn_id` bigint(11) DEFAULT '0',
  `alloted_order_id` bigint(11) DEFAULT '0',
  `alloted_on` datetime DEFAULT NULL,
  `invoice_no` bigint(11) DEFAULT '0',
  `return_id` bigint(11) DEFAULT '0',
  `is_cancelled` tinyint(1) DEFAULT '0',
  `cancelled_on` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `logged_on` datetime DEFAULT NULL,
  `logged_by` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_pnh_creditlimit_track` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `franchise_id` bigint(20) DEFAULT NULL,
  `payment_modetype` tinyint(1) DEFAULT NULL COMMENT '1:postpaid,2:prepaid using vouchers,3:prepaid by holding Acounts',
  `prepaid_credit_id` double DEFAULT NULL,
  `reconsolation_rid` bigint(11) DEFAULT NULL,
  `order_id` bigint(12) DEFAULT NULL,
  `transid` varchar(255) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `prepaid_creditlimit` double DEFAULT NULL,
  `purchase_limit` double DEFAULT '0',
  `init` bigint(20) DEFAULT NULL,
  `actiontime` bigint(20) DEFAULT NULL,
  `paid` double DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `t_prepaid_credit_receipt_track` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` bigint(11) DEFAULT NULL,
  `receipt_amount` double DEFAULT '0',
  `prepaid_credit` double DEFAULT '0',
  `franchise_id` bigint(11) DEFAULT NULL,
  `receipt_realizedon` bigint(20) DEFAULT '0',
  PRIMARY KEY (`id`)
);


CREATE TABLE `m_batch_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `batch_grp_name` varchar(150) DEFAULT NULL,
  `assigned_menuid` int(11) DEFAULT '0',
  `batch_size` int(11) DEFAULT '0',
  `group_assigned_uid` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

alter table shipment_batch_process add column `assigned_userid` int(11) DEFAULT '0' after status;
alter table shipment_batch_process add column `territory_id` int(11) DEFAULT '0' after assigned_userid;
alter table shipment_batch_process add column `batch_configid` int(11) DEFAULT '0' after territory_id;

CREATE TABLE `t_exotel_agent_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `callsid` varchar(255) DEFAULT NULL,
  `from` varchar(50) DEFAULT NULL,
  `dialwhomno` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
#================================================================

select f.territory_id,t.territory_name,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped,group_concat(distinct sd.invoice_no) as invoice_no_str
			,count(distinct sd.invoice_no) as ttl_invs,count(distinct f.franchise_id) as ttl_franchises
		from pnh_m_manifesto_sent_log man
			join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
		    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
 			join m_town_territory_link ttl on ttl.territory_id = f.territory_id and is_active=1
			join m_employee_info emp on emp.employee_id = ttl.employee_id


			join pnh_m_territory_info t on t.id = f.territory_id
                    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and unix_timestamp(sd.shipped_on) between 1383244200 and 1387477800 #and f.territory_id ='3'
			group by f.territory_id
			order by f.territory_id DESC;

select f.territory_id,t.territory_name,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped,group_concat(distinct sd.invoice_no) as invoice_no_str,count(tr.franchise_id) as ttl_franchises
			,count(distinct sd.invoice_no) as ttl_invs,count(distinct f.franchise_id) as ttl_franchises
		from pnh_m_manifesto_sent_log man
			join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
		    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
		    join king_transactions tr on tr.transid = pi.transid
		    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
 			join m_town_territory_link ttl on ttl.territory_id = f.territory_id and is_active=1
			join m_employee_info emp on emp.employee_id = ttl.employee_id
			join pnh_m_territory_info t on t.id = f.territory_id
                    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and date(man.sent_on) between from_unixtime('1383244200') and from_unixtime('1387477800') #and f.territory_id ='3'
			group by f.territory_id
			order by f.territory_id DESC;

select group_concat(man.sent_invoices) grp_invs from pnh_m_manifesto_sent_log man where date(man.sent_on) between from_unixtime('1383244200') and from_unixtime('1387477800')

select  from pnh_m_manifesto_sent_log man
join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
where date(man.sent_on) between from_unixtime('1383244200') and from_unixtime('1387477800')


select m_town_territory_link

select f.territory_id,t.territory_name,pi.dispatch_id,group_concat(distinct man.id) as man_id,sd.shipped_on,sd.shipped
                    ,group_concat(distinct sd.invoice_no) as invoice_no_str,count(distinct sd.invoice_no) as ttl_invs,count(distinct f.franchise_id) as ttl_franchises		    
                                                from pnh_m_manifesto_sent_log man
                                                        join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
                                                    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
                                                    join king_transactions tr on tr.transid = pi.transid
                                                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                                        join pnh_m_territory_info t on t.id = f.territory_id
                                                    where shipped=1 and sd.shipped_by>0 and unix_timestamp(sd.shipped_on)!=0 and dispatch_id != 0 and unix_timestamp(sent_on) between 1387564200 and 1387391400 
                                                group by f.territory_id
                                                order by t.territory_name ASC;
select from_unixtime('1383244200')

#=======================================================================================================
select unix_timestamp('2013-10-20') as utime;
select from_unixtime(1382207400) as time;
#=======================================================================================================

select group_concat(man.sent_invoices) grp_invs
from pnh_m_manifesto_sent_log man 
join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
join king_transactions tr on tr.transid = pi.transid
join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
join pnh_m_territory_info t on t.id = f.territory_id
where date(man.sent_on) between from_unixtime('1383244200') and from_unixtime('1387477800') and f.territory_id='3';

select * from pnh_m_manifesto_sent_log

select group_concat(man.sent_invoices) grp_invs
	    from pnh_m_manifesto_sent_log man 
	    join shipment_batch_process_invoice_link sd on sd.inv_manifesto_id = man.manifesto_id
	    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no and pi.invoice_status = 1 
	    join king_transactions tr on tr.transid = pi.transid
	    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
	    join pnh_m_territory_info t on t.id = f.territory_id
	    where date(man.sent_on) between from_unixtime('3') and from_unixtime(1384281000) and f.territory_id=1384453800



set @dd = '2013-12-25';
select weekday(@dd),date_add(@dd,interval weekday(@dd) day );

4,3 
3-4 -1day 

# =========================================================================================================

select * from (
	select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status 
		from king_orders a 
		join king_transactions tr on tr.transid = a.transid and tr.is_pnh=1 
		where a.status in (0,1) and tr.batch_enabled=1 
		and a.transid in ("'PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485','PNHZSN85485'"
	) 
group by a.transid) as ddd 
where ddd.orders_status=0

select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status from king_orders a join king_transactions tr on tr.transid = a.transid and tr.is_pnh=1 where a.status in (0,1) and tr.batch_enabled=1 and a.transid in ('PNHFFB52222','PNHTCI15423') group by a.transid) as ddd where ddd.orders_status=0;

#Dec_27_2013

select * from shipment_batch_process_invoice_link;

#Dec_28_2013


# =========================================================================================================
alter table `shipment_batch_process_invoice_link` drop column `is_acknowlege_printed`;

use snapittoday_db_jan_2014;

create table `pnh_acknowledgement_print_log` (  `sno` bigint NOT NULL AUTO_INCREMENT ,`log_id` varchar (150), `tm_emp_id` varchar (100) NULL, `be_emp_id` varchar (100) , `p_inv_no` varchar (100) NOT NULL 
, `created_on` varchar (50) , `created_by` int (30) , `status` int (10) DEFAULT '0', `count` int (100) DEFAULT '0', PRIMARY KEY ( `sno`));


# =========================================================================================================

select terr.territory_name,a.transid,a.createdon as invoiced_on,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,bill_phone,d.init,b.itemid,c.name,if(c.print_name,c.print_name,c.name) as print_name,c.pnh_id,group_concat(distinct a.invoice_no) as invs,
						((a.mrp-(a.discount))) as amt,
						sum(a.invoice_qty) as qty 
				from king_invoice a 
				join king_orders b on a.order_id = b.id 
				join king_dealitems c on c.id = b.itemid
				join king_transactions d on d.transid = a.transid
				join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
				join pnh_m_territory_info terr on terr.id=f.territory_id
				where a.invoice_no in (20141016108,20141016107,20141015715,20141015838,20141015885,20141015995,20141015996,20141015599,20141015598,20141015721,20141015915,20141015916,20141015917,20141015918,20141015919,20141015886,20141015887,20141015888,20141015889,20141015890,20141015891,20141015892,20141015893,20141015894) 
				group by f.territory_id
				order by c.name;
#=>67rows 94ms

select f.territory_id,terr.territory_name,group_concat(distinct a.invoice_no) as invoice_no_str,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,b.bill_phone
				from king_invoice a 
				join king_orders b on a.order_id = b.id 
				join king_dealitems c on c.id = b.itemid
				join king_transactions d on d.transid = a.transid
				join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
				join pnh_m_territory_info terr on terr.id=f.territory_id
				where a.invoice_no in (20141016108,20141016107,20141015715,20141015838,20141015885,20141015995,20141015996,20141015599,20141015598,20141015721,20141015915,20141015916,20141015917,20141015918,20141015919,20141015886,20141015887,20141015888,20141015889,20141015890,20141015891,20141015892,20141015893,20141015894) 
				group by f.territory_id
				order by c.name;

select * from acknowledgement_print_log;

SELECT *
FROM `acknowledgement_print_log`
WHERE `p_inv_no` = '2147483647' AND `created_by` = 1
LIMIT 1 

# Dec_30_2013
SELECT * FROM (`acknowledgement_print_log`) WHERE `p_inv_no` = '20141016108' AND `created_by` IS NULL LIMIT 1;

select * from acknowledgement_print_log where log_id='23876A5';

SELECT *FROM (`acknowledgement_print_log`) WHERE `p_inv_no`='20141016108' LIMIT 50;

select * from t_imei_no

select * from shipment_batch_process_invoice_link where invoice_no='20141016103'

select * from  m_town_territory_link

select * from king_admin
select * from m_employee_info


select tlink.employee_id,e.name from shipment_batch_process_invoice_link sd
join proforma_invoices pi on pi.p_invoice_no = sd.p_invoice_no
join king_transactions tr on tr.transid = pi.transid
join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
join m_town_territory_link tlink on tlink.territory_id = f.territory_id and tlink.is_active=1
left join m_employee_info e on e.employee_id = tlink.employee_id 
where sd.invoice_no = '20141016107' and e.job_title in (4,5)
group by tlink.employee_id;

#

select * from employee_info

select f.franchise_id,f.franchise_name
																			from t_imei_no i
																			join king_orders b on i.order_id = b.id
																			join king_dealitems c on c.id = b.itemid
																			join m_product_deal_link d on d.itemid = c.id
																			join king_transactions e on e.transid = b.transid
																			join pnh_m_franchise_info f on f.franchise_id = e.franchise_id 
																			where b.status in (1,2) and b.imei_scheme_id > 0  
																			group by f.franchise_id
																			order by franchise_name 


SELECT i.is_imei_activated,count(distinct i.id) as ttl,sum(imei_reimbursement_value_perunit) as amt 
							FROM t_imei_no i 
									Join king_orders o on o.id = i.order_id 
									JOIN king_transactions t ON t.transid=o.transid
									JOIN m_product_deal_link p ON p.itemid=o.itemid
									JOIN m_product_info l ON l.product_id=p.product_id
									JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
									JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
									#left join pnh_member_info b on b.pnh_member_id=i.activated_member_id 
									#left join t_invoice_credit_notes tcr on tcr.invoice_no = inv.invoice_no 
									JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
							WHERE o.status in (1,2) and o.imei_scheme_id > 0 and t.franchise_id= 17  #and (date(imei_activated_on) >= date("2013-11-30") and date(imei_activated_on) <= date("2013-12-07")) 
							group by i.is_imei_activated 
							ORDER BY l.product_name ASC


select * from acknowledgement_print_log where p_inv_no in ('20141016108','20141016107','20141015715','20141015838,20141015885,20141015995,20141015996,20141015599,20141015598')
select * from acknowledgement_print_log where p_inv_no in ('20141015599,20141015598')

select * from acknowledgement_print_log where p_inv_no in ("20141015599","20141015598")

select log_id,tm_emp_id,be_emp_id,group_concat(p_inv_no) as grp_invs,created_on,created_by,status,count from acknowledgement_print_log 
where p_inv_no in ("20141015599","20141015598")
group by log_id;

C:\Users\User\Downloads\StoreKing_Database_2014_01_01_08_00_AM

#Jan_01_2014
create database snapittoday_db_jan_2014;

select md5('17c4520f6cfd1ab53d8745e84681eb49'); =>superadmin

select md5('9027da57d66aa309df4d13q0e6ab0d06')

uuudgs5h1d28-234234arabin445221

select * from ( select transid,TRIM(BOTH ',' FROM group_concat(p_inv_nos)) as p_inv_nos,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id 
	from ( 
		select o.transid,ifnull(group_concat(distinct pi.p_invoice_no),'') as p_inv_nos,o.status,count(*) as ttl_o,tr.franchise_id,tr.actiontime 
			from king_orders o join king_transactions tr on tr.transid=o.transid 
			left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
			left join proforma_invoices pi on pi.order_id = o.id and o.transid = pi.transid and pi.invoice_status = 1 left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
			left join shipment_batch_process sbp on sbp.batch_id = sd.batch_id 
			where o.status in (0,1) and i.id is null and tr.franchise_id != 0 #and sbp.assigned_userid = 37
				and ((sd.packed=0 and sd.p_invoice_no > 0) or (sd.p_invoice_no is null and sd.packed is null )) 
				group by o.transid,o.status ) as g group by g.transid )as g1 having g1.trans_status = 'ready';

sbp.assigned_userid

select * from shipment_batch_process_invoice_link;

#=============================================================================

alter table `shipment_batch_process` 
	add column `assigned_userid` int (11) DEFAULT '0' NULL  after `status`, 
	add column `territory_id` int (11) DEFAULT '0' NULL  after `assigned_userid`, 
	add column `batch_configid` int (11) DEFAULT '0' NULL  after `territory_id`;

#=============================================================================

update t_imei_no set status=0 and order_id=0 where imei_no = '355842059098166';

select * from proforma_invoices where p_invoice_no='115786';

select * from t_imei_no where status=0 and product_id='8702';

select * from shipment_batch_process_invoice_link;

Electronics 112,118 4 1,2,37,36

Beauty      100     2 1,2,37,36

select * from shipment_batch_process;

#======== Jan_09_2014 ======================

set @global_batch_id=5000;
select distinct o.itemid,d.menuid,mn.name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                where sd.batch_id=@global_batch_id 
                                order by tr.init asc;


select * from shipment_batch_process;


CREATE TABLE `pnh_m_fran_security_cheques` (             
                               `id` int(11) NOT NULL AUTO_INCREMENT,                  
                               `franchise_id` int(11) DEFAULT '0',                    
                               `bank_name` varchar(255) DEFAULT NULL,                 
                               `cheque_no` varchar(30) DEFAULT NULL,                  
                               `cheque_date` date DEFAULT NULL,                       
                               `collected_on` date DEFAULT NULL,                      
                               `amount` double DEFAULT NULL,                          
                               `returned_on` date DEFAULT NULL,                       
                               `created_on` datetime DEFAULT NULL,                    
                               `modified_on` datetime DEFAULT NULL,                   
                               `created_by` int(11) DEFAULT NULL,                     
                               `modified_by` int(11) DEFAULT NULL,                    
                               PRIMARY KEY (`id`)                                     
                             );

set @global_batch_id=5000;
set @batch_id=5548; #5544-48
update shipment_batch_process_invoice_link set batch_id=@global_batch_id where batch_id=@batch_id;
delete from shipment_batch_process where batch_id = @batch_id;



set @global_batch_id=5000;
select distinct o.itemid,d.menuid,mn.name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                
                                where sd.batch_id=@global_batch_id 
-- 								group by d.menuid
                                order by d.menuid,tr.init asc;


select distinct 
                                o.itemid
--                                 bc.id as menuid,bc.batch_grp_name as menuname
								,sd.invoice_no
								,d.menuid
								,f.territory_id
                                ,sd.id,sd.batch_id,sd.p_invoice_no
                                ,from_unixtime(tr.init) as init 
                                    from king_transactions tr
                                    join king_orders as o on o.transid=tr.transid
                                    join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                    join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no and sd.invoice_no=0
                                    join king_dealitems dl on dl.id = o.itemid
                                    join king_deals d on d.dealid = dl.dealid 
                                    join pnh_menu mn on mn.id=d.menuid
                                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
--                                     join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                    where sd.batch_id=5000  and f.territory_id = 3 
--                                         group by  bc.id
                                    order by tr.init asc;


select distinct 
                            o.itemid,count(*) as ttl_orders
                            ,bc.id as menuid,bc.batch_grp_name as menuname,f.territory_id
                            ,sd.id,sd.batch_id,sd.p_invoice_no
                            ,from_unixtime(tr.init) as init 
                                from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no and sd.invoice_no=0
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                where sd.batch_id=5000  and f.territory_id = 00 
                                    group by  bc.id
                                order by tr.init;

select distinct o.itemid,d.menuid,mn.name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no and sd.invoice_no=0
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                 join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) and  bc.id = 1 
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                
                                where sd.batch_id=5000 
                                order by d.menuid,tr.init asc


set @global_batch_id=5000,@batch_id=5531; #,5531,5532,5533,5534,5535';
update shipment_batch_process_invoice_link set batch_id=@global_batch_id where batch_id in (@batch_id);
delete from shipment_batch_process where batch_id in (@batch_id);

select * from shipment_batch_process_invoice_link;

desc m_batch_config

select 
sb.batch_id,sum(num_orders) as ttl_trans,assigned_userid,created_on,sd.id,sd.p_invoice_no,sd.invoice_no,group_concat(sd.p_invoice_no) as grp_p_invoice_no,group_concat(sd.p_invoice_no) as grp_invoice_no
,bc.assigned_menuid,bc.batch_grp_name
 from shipment_batch_process sb
join shipment_batch_process_invoice_link sd on sd.batch_id = sb.batch_id and sd.invoice_no=0
#join proforma_invoices `pi` on pi.id=sd.p_invoice_no and pi.invoice_status=1
#join king_orders o on o.id = pi.order_id
join m_batch_config bc on bc.id=sb.batch_configid
where created_on between '2013-12-13 15:20:50' and '2014-01-10 15:20:50'
group by sb.batch_id
order by created_on desc

#=>227rows

select sb.batch_id,terr.territory_name,bc.batch_grp_name,sb.num_orders,sb.status,sb.created_on,a.username as assigned_to
from shipment_batch_process sb
join m_batch_config bc on bc.id=sb.batch_configid
left join pnh_m_territory_info terr on terr.id=sb.territory_id
join king_admin a on a.id=sb.assigned_userid
where batch_id>'5000' and assigned_userid=1
order by batch_id desc;




desc king_transactions
desc king_orders;
desc proforma_invoices



select f.franchise_name,count(sd.p_invoice_no) as num_orders,t.territory_name,tw.town_name
	from shipment_batch_process_invoice_link sd
join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
join king_orders o on o.id=pi.order_id
join king_transactions tr on tr.transid=o.transid
join pnh_m_franchise_info f on f.franchise_id=tr.franchise_id
join pnh_m_territory_info t on t.id=f.territory_id
join pnh_towns tw on tw.id=f.town_id
where batch_id='5577' and sd.invoice_no = 0
group by f.franchise_id;


select f.franchise_id,f.franchise_name,count(sd.p_invoice_no) as num_orders,t.territory_name,tw.town_name,sd.batch_id
                    from shipment_batch_process_invoice_link sd
                    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                    join king_orders o on o.id=pi.order_id
                    join king_transactions tr on tr.transid=o.transid
                    join pnh_m_franchise_info f on f.franchise_id=tr.franchise_id
                    join pnh_m_territory_info t on t.id=f.territory_id
                    join pnh_towns tw on tw.id=f.town_id
                    where batch_id='5577' and sd.invoice_no = 0
                    group by f.franchise_id;

select group_concat(distinct sd.p_invoice_no) as p_invoice_ids
		from shipment_batch_process_invoice_link sd
		join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
		join king_orders o on o.id=pi.order_id
		join king_transactions tr on tr.transid=o.transid
where sd.batch_id='5577' and tr.franchise_id='254';


select group_concat(distinct sd.p_invoice_no) as p_invoice_ids from shipment_batch_process_invoice_link sd 
join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no 
join king_orders o on o.id=pi.order_id join king_transactions tr on tr.transid=o.transid where sd.batch_id='5578'



select menuid,menuname,p_invoice_no,product_id,product,location,sum(rqty) as qty from ( 
                select dl.menuid,mnu.name as menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0
                        
                        join king_orders o on o.id = rbs.order_id
                        join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1
                        
                        where e.p_invoice_no='115640' 
                group by rbs.id  ) as g 
                group by product_id,location;


select menuid,menuname,p_invoice_no,product_id,product,location,sum(rqty) as qty from ( 
                select dl.menuid,mnu.name as menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0
                        
                        join king_orders o on o.id = rbs.order_id
                        join king_dealitems dlt on dlt.id = o.itemid
			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1
                        
                        where  e.batch_id = '5575'
                group by rbs.id  ) as g 
                group by product_id,location;

#### Jan_11_2014 ======================
select * from (
            select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
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
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id  and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id 
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null  and tr.transid in ('PNH11244','PNHAXG89881','PNHBQV35115','PNHFMK39418','PNHGJL64274','PNHGQR82517','PNHJGC19279','PNHJSM35174','PNHSCD99697','PNHWTN26375','PNHWTW35421','PNHWUI91457','PNHWUM95923','PNHXJC33559','PNHZQI66535') 
            group by o.transid) as g  
where  ( g.batch_id is null and g.batch_id >= 5000 ) 
group by transid order by  g.actiontime DESC ;

select * from king_admin
select md5('basava')

select * from t_reserved_batch_stock rbs
join king_orders o on o.id=rbs.order_id
where batch_id='5575'


select franchise_id,franchise_name,batch_id,menuid,menuname,p_invoice_no,product_id,product,location,sum(rqty) as qty from ( 
                select tr.franchise_id,f.franchise_name,e.batch_id,dl.menuid,mnu.name as menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                        from t_reserved_batch_stock rbs 
                        join t_stock_info si on rbs.stock_info_id = si.stock_id 
                        join m_product_info pi on pi.product_id = si.product_id 
                        join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                        join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0
                        
                        join king_orders o on o.id = rbs.order_id
						join king_transactions tr on tr.transid = o.transid
						join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                        join king_dealitems dlt on dlt.id = o.itemid

			join king_deals dl on dl.dealid = dlt.dealid
			join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1
                        
                        where  e.batch_id = '5584'
                group by rbs.id  ) as g 
                group by product_id,location;

#=>32 rows

select * from shipment_batch_process sb
join shipment_batch_process_invoice_link sd on sd.batch_id = sb.batch_id
where sb.batch_id='5584'
group by sd.batch_id;

select f.franchise_id,f.franchise_name,count(sd.p_invoice_no) as num_orders,t.territory_name,tw.town_name,sd.batch_id
                    from shipment_batch_process_invoice_link sd
                    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                    join king_orders o on o.id=pi.order_id
                    join king_transactions tr on tr.transid=o.transid
                    join pnh_m_franchise_info f on f.franchise_id=tr.franchise_id
                    join pnh_m_territory_info t on t.id=f.territory_id
                    join pnh_towns tw on tw.id=f.town_id
                    where batch_id='5584' and sd.invoice_no = 0
                    group by f.franchise_id;

select * from shipment_batch_process_invoice_link

select * from king_transactions
desc king_transactions;



            select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                    ,ter.territory_name,twn.town_name
                    ,dl.menuid,m.name as menu_name,bs.name as brand_name
                    ,sd.batch_id
            from king_transactions tr
                    join king_orders o on o.transid=tr.transid
                    join king_dealitems di on di.id=o.itemid
                    join king_deals dl on dl.dealid=di.dealid
                    join pnh_menu m on m.id = dl.menuid
                    join king_brands bs on bs.id = o.brandid
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id  and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id 
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE tr.status in (0,1) and i.id is null and tr.batch_enabled=0 
            group by tr.transid order by  tr.actiontime DESC;


select o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    from king_orders o
                    join king_transactions tr on tr.transid = o.transid 
                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                    left join king_invoice i on o.id = i.order_id 
                    left join proforma_invoices `pi` on pi.order_id = o.id
                    join king_dealitems di on di.id = o.itemid
                    where i.id is null and tr.transid = 'PNHCPX81979' and tr.status in (1)
                    order by tr.init,di.name;

select * from king_transactions where transid='PNH97446';


# Jan_13_2014
select * from proforma_invoices

select * from shipment_batch_process;

select * 
from king_orders o
join proforma_invoices pi on pi.transid=o.transid
where o.transid='PNHKWQ67556';


select unix_timestamp(now())

select from_unixtime('1389609882');

error: PNH22377

select * from king_invoice;


# Jan_14_2014

-- =============================================================================
update t_imei_no set status=0 and order_id=0 where imei_no = '911208355656643';
-- =============================================================================

select (100*40)/100;

select ceil(50/2);

#============================================================================================
#========== BATCH RESET =====================================================================
set @global_batch_id=5000;
set @batch_id=5714; #5544-48
update shipment_batch_process_invoice_link set batch_id=@global_batch_id where batch_id=@batch_id;
delete from shipment_batch_process where batch_id = @batch_id;
#============================================================================================

select ceil(66/20) =>4
select 66%20 6
select ceil(20/2) 10
6<=10
26-26 = 0
batches 3

select * from king_deals d
join king_dealitems di on di.dealid=d.dealid where id='9953912416';

select * from king_dealitems;

deal edit:dealid=9953912416
<div class="extra_text"> <table>  <tr>      <td>     <ul class="fk-ul-disc">      <li>14.2 Megapixels</li>      <li>Optical Zoom: 5x</li>     </ul>    </td>    <td>     <ul class="fk-ul-disc">            <li>CCD Image Sensor</li>            <li>with 

select * from pnh_m_voucher;


select distinct o.itemid,bc.id as menuid,bc.batch_grp_name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                where sd.batch_id=5000  and f.territory_id = 11 
                                group by o.transid
                                order by menuname,tr.init asc

# Jan_16_2014 ===========

select * from king_admin
m_batch_config

select * from king_menu;
select * from pnh_menu

-- -- -- -- -- -- //===============================================
alter table `king_orders` add index `status` (`status`);
alter table `m_product_info` add index `product_id` (`product_id`);
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

select distinct from_unixtime(tr.init,'%D %M %Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name as menu_name,bs.name as brand_name
                    ,sd.batch_id
                    ,pi.cancelled_on
            from king_transactions tr
                    join king_orders o on o.transid=tr.transid
                    join king_dealitems di on di.id=o.itemid
                    join king_deals dl on dl.dealid=di.dealid
                    join pnh_menu m on m.id = dl.menuid
                    join king_brands bs on bs.id = o.brandid
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id  and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id 
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null  and tr.transid in ('PNH22839','PNH33295','PNH35578','PNH35818','PNH71847','PNH93611','PNHAXG89881','PNHBQV35115','PNHCXE88342'
,'PNHDJX75781','PNHEHA75287','PNHESP26174','PNHFMK39418','PNHGJL64274','PNHGQR82517','PNHHCP63831','PNHIHX16957','PNHIYC47867','PNHJGC19279','PNHJSM35174','PNHNVQ34988','PNHQQK26697','PNHQSG33417','PNHRAL44539'
,'PNHSCD99697','PNHSZZ72836','PNHTMP33937','PNHTRW66732','PNHTWJ53918','PNHUEA37733','PNHURC82659','PNHVSE94433','PNHVVX36284','PNHWTN26375','PNHWTW35421','PNHWUI91457','PNHWUM95923','PNHWZM23398','PNHXJC33559'
,'PNHZEV97667','PNHZQI66535')
            group by o.transid asc) as g  where  and g.batch_id = 5000 and  g.batch_id >= 5000 group by transid order by  g.actiontime DESC;

# Jan_17_2014

-- =============================================================================
update t_imei_no set status=0 and order_id=0 where imei_no = '354619056098758';
-- =============================================================================

select * from t_imei_no
select * from m_batch_config

desc m_batch_config;

select e.*,b.batch_id from proforma_invoices a 
			join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
			join king_transactions c on c.transid = a.transid  
			join pnh_m_franchise_info d on d.franchise_id = c.franchise_id  and d.is_suspended = 0
			join pnh_m_territory_info e on e.id = d.territory_id 
			where  a.invoice_status = 1 and batch_id = '5000'
			group by d.territory_id 
			order by territory_name;

select * from pnh_m_territory_info;

select distinct o.itemid,bc.id as menuid,bc.batch_grp_name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                where sd.batch_id=5000  and f.franchise_id = 31 
                                group by o.transid
                                order by menuname,tr.init asc;


G1 -
112,118,122 - Mobiles & Tablets,Computers & Peripherals,Cameras & Accessories

select * from pnh_member_info where first_name = 'shivraj'

select * from king_admin
select * from m_batch_config

select product_name from m_product_info where product_id='8583';

### Jan_18_2014 ###
select * from shipment_batch_process

select * from king_transactions where transid='PNH43375';

select * from t_stock_info

-- #================================================================

#new fields added
alter table `shipment_batch_process_invoice_link` add column `batched_on` bigint (20)  NULL  after `delivered_by`, add column `batched_by` int (11)  NULL  after `batched_on`;
DROP TABLE IF EXISTS `picklist_log_reservation`;

CREATE TABLE `picklist_log_reservation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT '0',
  `createdon` datetime DEFAULT NULL,
  `printcount` int(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- #================================================================
select * from shipment_batch_process_invoice_link

B5595
PNHWZM23398

select * from proforma_invoices where transid='PNHWZM23398'

select printcount from picklist_log_reservation where p_inv_no='116095';


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



select t.* from proforma_invoices pi 
		join shipment_batch_process_invoice_link sd on pi.p_invoice_no = sd.p_invoice_no 
		join king_transactions tr on tr.transid = pi.transid  
		join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
		join pnh_m_territory_info t on t.id = f.territory_id 
		where pi.invoice_status = 1 and sd.batch_id = '5000'
		group by f.territory_id 
		order by t.territory_name

# Jan_21_2014

-- ALTER TABLE `m_vendor_brand_link` ADD COLUMN `cat_id` bigint(11) DEFAULT 0 NULL AFTER `brand_id`;

-- ALTER TABLE `t_po_info` ADD COLUMN `status_remarks` Varchar(2555) NULL AFTER `modified_on`; 

-- XXXXX alter table `shipment_batch_process_invoice_link` drop column `is_acknowlege_printed`;

-- XXXXX create table `acknowledgement_print_log` (  `sno` bigint NOT NULL AUTO_INCREMENT ,`log_id` varchar (150), `tm_emp_id` varchar (100) NULL, `be_emp_id` varchar (100) , `p_inv_no` varchar (100) NOT NULL 
, `created_on` varchar (50) , `created_by` int (30) , `status` int (10) DEFAULT '0', `count` int (100) DEFAULT '0', PRIMARY KEY ( `sno`));

-- ALTER TABLE `pnh_sch_discount_track` ADD COLUMN `dealid` BIGINT(0) NULL AFTER `catid`; 
-- ALTER TABLE `pnh_sch_discount_brands` ADD COLUMN `dealid` BIGINT(11) DEFAULT 0 NULL AFTER `discount`;  

Jan_21_2014 drop table `acknowledgement_print_log`





select t.* from proforma_invoices pi 
		join shipment_batch_process_invoice_link sd on pi.p_invoice_no = sd.p_invoice_no 
		join king_transactions tr on tr.transid = pi.transid  
		join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
		join pnh_m_territory_info t on t.id = f.territory_id 
		join king_orders o on o.transid=tr.transid
		left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
		where pi.invoice_status = 1 and sd.batch_id = '5000' and tr.batch_enabled=1 and i.id is null
		group by f.territory_id 
		order by t.territory_name


select distinct 
		o.itemid,count(distinct tr.transid) as ttl_trans,group_concat(distinct tr.transid) as grp_trans
		,bc.id as menuid,bc.batch_grp_name as menuname,group_concat(distinct d.menuid) as actualmenus,f.territory_id
		,sd.id,sd.batch_id,sd.p_invoice_no
		,from_unixtime(tr.init) as init,bc.batch_size,bc.group_assigned_uid as bc_group_uids
			from king_transactions tr
			join king_orders as o on o.transid=tr.transid
			join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
			join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no and sd.invoice_no=0 
			join king_dealitems dl on dl.id = o.itemid
			join king_deals d on d.dealid = dl.dealid 
			join pnh_menu mn on mn.id=d.menuid
			join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
			join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
			where   f.territory_id = '2' and sd.batch_id = 5000 and tr.batch_enabled=1
				group by  bc.id
			order by tr.init asc

#PNH55415,PNH24756

select * from (
            select distinct from_unixtime(tr.init,'%d/%m/%Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name as menu_name,bs.name as brand_name
                    ,sd.batch_id,sd.batched_on
                    ,pi.cancelled_on
            from king_transactions tr
                    join king_orders o on o.transid=tr.transid
                    join king_dealitems di on di.id=o.itemid
                    join king_deals dl on dl.dealid=di.dealid
                    join pnh_menu m on m.id = dl.menuid
                    join king_brands bs on bs.id = o.brandid
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id  and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id 
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null  and tr.transid  = 'PNH24756' #tr.transid in ('PNH24756','PNH11768','PNH11769','PNH14153','PNH16531','PNH16758','PNH16829','PNH18499','PNH18613','PNH18694','PNH19988','PNH21379','PNH22377','PNH24756','PNH24973','PNH25135','PNH25654','PNH25842','PNH26544','PNH26765','PNH26782','PNH26895','PNH27179','PNH28758','PNH28935','PNH29347','PNH29776','PNH32855','PNH33145','PNH33191','PNH34632','PNH35578','PNH36978','PNH37124','PNH38468','PNH38666','PNH39728','PNH39841','PNH43375','PNH45122','PNH45598','PNH45771','PNH46113','PNH46474','PNH46595','PNH49892','PNH51259','PNH52451','PNH52959','PNH54347','PNH54546','PNH55285','PNH55415','PNH55963','PNH57714','PNH59216','PNH61185','PNH61811','PNH62431','PNH62792','PNH62894','PNH63543','PNH64597','PNH64835','PNH65361','PNH65422','PNH65711','PNH66479','PNH66571','PNH66924','PNH68492','PNH68863','PNH68996','PNH69272','PNH71162','PNH71171','PNH73246','PNH73648','PNH74896','PNH75579','PNH76542','PNH76553','PNH76695','PNH77194','PNH78763','PNH78771','PNH79767','PNH81262','PNH81424','PNH82245','PNH82796','PNH83136','PNH83532','PNH84494','PNH84838','PNH84978','PNH85514','PNH85534','PNH86684','PNH86879','PNH86943','PNH87251','PNH87352','PNH87866','PNH87895','PNH88261','PNH88784','PNH89294','PNH91142','PNH91414','PNH92727','PNH93167','PNH93343','PNH93391','PNH93714','PNH94121','PNH95855','PNH97158','PNH97261','PNH97446','PNH98427','PNH99546','PNH99823','PNHADS69161','PNHAED74212','PNHAGH88692','PNHAGN36664','PNHAHR92662','PNHANH58567','PNHANP18692','PNHARM33812','PNHAUT82647','PNHAVY15723','PNHAWP81139','PNHAXZ95887','PNHBCQ31955','PNHBDQ46772','PNHBIC14589','PNHBJX88262','PNHBQY95588','PNHBTY26342','PNHBUG92628','PNHBVZ88616','PNHBXL77127','PNHCBX23293','PNHCIK77141','PNHCIQ31331','PNHCMG84238','PNHCNI94123','PNHCUC68876','PNHDAC33555','PNHDAZ29396','PNHDHN67744','PNHDKQ83788','PNHDLH38581','PNHDLU22337','PNHDRB84232','PNHDRN86616','PNHDXQ59712','PNHEBM77371','PNHEDE76659','PNHEEA25773','PNHEKI99239','PNHEKN99876','PNHEKQ79585','PNHEMC36338','PNHEUC28782','PNHEVL56344','PNHEWD63196','PNHEWS63546','PNHEXJ46975','PNHFDQ39861','PNHFEP19358','PNHFGH39552','PNHFJJ89521','PNHFJW85241','PNHFMC31231','PNHFNJ93618','PNHFPD52595','PNHFQW52357','PNHFRK85831','PNHGBA81545','PNHGBW42171','PNHGCN55994','PNHGDB83273','PNHGFX26856','PNHGGV79376','PNHGIA46937','PNHGPD98365','PNHGPK52349','PNHGQW98992','PNHGRK87445','PNHGSP85927','PNHGTD69361','PNHGZG96317','PNHGZK66297','PNHGZX93525','PNHHAM59938','PNHHDV12858','PNHHFN96199','PNHHJT55225','PNHHKV17658','PNHHLJ32837','PNHHLK63918','PNHHPL57445','PNHIBP85449','PNHIEK31187','PNHIKZ84631','PNHINL91791','PNHITM15567','PNHJHY22236','PNHJIG23987','PNHJJW49978','PNHJPK79588','PNHJUB83444','PNHJUR83585','PNHJWH19378','PNHJXM54977','PNHJYI53269','PNHJYR49625','PNHKBX34595','PNHKFE84894','PNHKHA69179','PNHKHF18118','PNHKIU86986','PNHKJX49398','PNHKKW32568','PNHKMH14852','PNHKMP39512','PNHKRI73163','PNHKRJ67714','PNHKSE17358','PNHKSH76263','PNHKVE56634','PNHKVK83266','PNHKWQ67556','PNHKWT97949','PNHKWV36154','PNHKXY29712','PNHKZZ52757','PNHLCH25589','PNHLFZ38473','PNHLGE26468','PNHLGR41262','PNHLHF23981','PNHLJU53973','PNHLPT92586','PNHLUD18722','PNHLVQ28751','PNHLXS47336','PNHMCZ13697','PNHMGI67624','PNHMIQ71617','PNHMIT85823','PNHMQG96137','PNHMTK76282','PNHMUN63624','PNHMXM72843','PNHNBH61653','PNHNEF55642','PNHNES62472','PNHNFQ43361','PNHNHM42857','PNHNJW86618','PNHNLX33758','PNHNMW22688','PNHNPZ32251','PNHNQS25748','PNHNRI89691','PNHNRN27821','PNHNTL29354','PNHPJJ83597','PNHPLJ53135','PNHPLQ52878','PNHPNE57734','PNHPNV48628','PNHPWK16236','PNHPWN91691','PNHQJM28368','PNHQKF47245','PNHQMA88926','PNHQQA12834','PNHQRF86241','PNHQSN62741','PNHQVP81358','PNHQXN83166','PNHRCX12337','PNHRCX79657','PNHRGH56152','PNHRLJ44264','PNHRML93953','PNHRRA11758','PNHRRS58716','PNHRSM64458','PNHRSR33587','PNHRUV64528','PNHRUZ38876','PNHRVS63522','PNHRYS25414','PNHRZV84351','PNHSDG26737','PNHSII24313','PNHSJS19152','PNHSML27435','PNHSNN13966','PNHSQW37875','PNHSSH26593','PNHSYN46875','PNHTID81523','PNHTJC71133','PNHTRE43287','PNHTRL83653','PNHTRN68473','PNHTVC97613','PNHUCU43371','PNHUQC77913','PNHURC82659','PNHVMJ76716','PNHVPZ83614','PNHVQB23439','PNHVRS64166','PNHVXV45964','PNHWAV76894','PNHWJA56139','PNHWJW38128','PNHWWP64836','PNHWZH17882','PNHWZM23398','PNHXEN58219','PNHXFM55127','PNHXGX78951','PNHXMN52299','PNHXMS97273','PNHXQH46883','PNHXQP58382','PNHXUQ31412','PNHYAQ29515','PNHYCC44768','PNHYLV31787','PNHYMX51367','PNHYSG22114','PNHYSG25161','PNHYVR76171','PNHZFL18126','PNHZHW58257','PNHZIP31262','PNHZMD85141','PNHZQE12455','PNHZRT64433','PNHZVC56319')
            group by o.transid) as g  where  g.batch_id >= 5000  group by transid order by  g.actiontime DESC 

select * from shipment_batch_process_invoice_link where batch_id='5714'
5719
#=================================================================================================
#============== BATCH RESET ======================================================================
set @global_batch_id=5000;
set @batch_id=5760; #5544-48
update shipment_batch_process_invoice_link set batch_id=@global_batch_id where batch_id=@batch_id;
delete from shipment_batch_process where batch_id = @batch_id;
#=================================================================================================

Beauty
100,101,102,103,104,105,106,107,108,109,110,111,120

Electronics
112,113,115,116,117,118,122,126

Footwear & Clothing
114,119,121

Company Assets
124,125

select printcount from picklist_log_reservation where batch_id='5730'

select status,product_id from t_imei_no where imei_no = '355681053892715'; #51084_4090_1_1_163256_4271887558_5649623681';

# Jan_22_2014

-- =============================================================================
update t_imei_no set status=0 and order_id=0 where imei_no = '356519052961611';
-- =============================================================================
select * from m_product_info where product_id='132961';
# Stock intake the product with product id
set @product_id = '132961'; # frequent
set @id='24573';
set @imei_no = '364619056098766';
insert into t_imei_no(id,product_id,imei_no,status,grn_id,stock_id,order_id,created_on,modified_on) values(@id,@product_id,@imei_no,0,'2235',0,0,now(),0);

-- =============================================================================
select * from t_stock_info where product_id='8702'

select * from t_reserved_batch_stock where  product_id='132989'

select * from t_imei_no where imei_no='354619056098765' product_id='132989'



-- // Territory 
select t.*,sd.batch_id,i.id from proforma_invoices pi 
                                                        join shipment_batch_process_invoice_link sd on pi.p_invoice_no = sd.p_invoice_no 
                                                        join king_transactions tr on tr.transid = pi.transid  
                                                        join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                                        join pnh_m_territory_info t on t.id = f.territory_id 
                                                        join king_orders o on o.transid=tr.transid
                                                        left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                                        where pi.invoice_status = 1 and sd.batch_id = '5000' and tr.batch_enabled=1 #and i.id is null
                                                        group by f.territory_id 
                                                        order by t.territory_name

select * from shipment_batch_process_invoice_link sd where batch_id='5000';

select distinct 
                            o.itemid,count(distinct tr.transid) as ttl_trans,group_concat(distinct tr.transid) as grp_trans
                            ,bc.id as menuid,bc.batch_grp_name as menuname,group_concat(distinct d.menuid) as actualmenus,f.territory_id
                            ,sd.id,sd.batch_id,sd.p_invoice_no
                            ,from_unixtime(tr.init) as init,bc.batch_size,bc.group_assigned_uid as bc_group_uids
                                from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no and sd.invoice_no=0
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                where sd.batch_id = '5000' and tr.batch_enabled=1
                                    group by  bc.id
                                order by tr.init asc

select * from 

select status,product_id from t_imei_no where imei_no = '1223334566777_7089_1_10_163421_4628351511_7719121613'

select * from t_imei_no  where product_id='8583';

select * from shipment_batch_process_invoice_link

-- =====================================================================
# INSERT NEW IMEI FOR PRODUCT
insert into t_imei_no(id,product_id,imei_no,status,grn_id,stock_id,order_id,created_on,modified_on)
values(24565,29226,'354619056098767',0,'2235',0,0,now(),0);
-- =====================================================================

select * from m_product_info where product_id='29226';

ALTER TABLE `m_vendor_info` ADD COLUMN `payment_type` INT(1) DEFAULT 0 NULL AFTER `require_payment_advance`;

ALTER TABLE `pnh_t_receipt_info` ADD COLUMN `cheq_realized_on` VARCHAR(255) NULL AFTER `activated_on`;

-- 1
select r.*,m.name AS modifiedby,a.name as admin,act.name as act_by,d.remarks AS submittedremarks,sub.name AS submittedby,d.submitted_on,can.cancelled_on,can.cancel_reason 
from pnh_t_receipt_info r 
LEFT OUTER JOIN `pnh_m_deposited_receipts`can ON can.receipt_id=r.receipt_id 
left outer join king_admin a on a.id=r.created_by left outer join king_admin act on act.id=r.activated_by 
LEFT OUTER JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id 
LEFT OUTER JOIN king_admin sub ON sub.id=d.submitted_by 
LEFT OUTER JOIN king_admin m ON m.id=r.modified_by where franchise_id='371' group by r.receipt_id;

-- 2
select dm.created_on,di.id,di.device_sl_no,d.device_name 
from pnh_m_device_info di 
join pnh_m_device_type d on d.id=di.device_type_id 
join pnh_t_device_movement_info dm on dm.device_id=di.id where di.issued_to='371'

-- 3
SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a 
JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid 
JOIN pnh_menu m ON m.id=a.menuid 
WHERE a.status=1 AND b.franchise_id='371';

-- 4
select f.*,f.franchise_id,f.sch_discount,f.sch_discount_start,f.sch_discount_end,f.credit_limit,f.security_deposit,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name as assigned_to,t.territory_name,f.is_prepaid 
from pnh_m_franchise_info f 
left outer join king_admin u on u.id=f.assigned_to 
join pnh_m_territory_info t on t.id=f.territory_id 
join pnh_m_class_info c on c.id=f.class_id 
where f.franchise_id='371' order by f.franchise_name asc;

-- 5 new invalid
select i.invoice_no,i.transid,i.mrp,tr.franchise_id,from_unixtime(i.createdon)
	from king_invoice i
join king_transactions tr on tr.transid=i.transid
where i.invoice_status=1 and tr.is_pnh=1 and tr.franchise_id='224'
 order by i.createdon asc

#invoice_status


#Jan_24_2014

select * from king_invoice
select count(invoice_no),group_concat(mrp) from king_invoice group by invoice_no,transid

-- =============================================================================
update t_imei_no set status=0 and order_id=0 where imei_no = '356519052961611';
-- =============================================================================
select * from m_product_info where product_id='132961';
# Stock intake the product with product id
set @product_id = '132961'; # frequent
set @id='24573';
set @imei_no = '364619056098766';
insert into t_imei_no(id,product_id,imei_no,status,grn_id,stock_id,order_id,created_on,modified_on) values(@id,@product_id,@imei_no,0,'2235',0,0,now(),0);

-- =============================================================================


-- new 1 valid
select i.invoice_no,i.transid,tr.franchise_id,from_unixtime(i.createdon),sum(i.mrp) as inv_total,count(i.invoice_no) as num_invs,group_concat(i.mrp) as grp_mrp,group_concat(distinct ref_dispatch_id) as grp_dispatch_id
	from king_invoice i
join king_transactions tr on tr.transid=i.transid
where i.invoice_status=1 and tr.is_pnh=1 and tr.franchise_id='224'
 group by i.invoice_no,i.transid order by i.createdon asc

#============
select * from pnh_t_receipt_info where franchise_id = '43';


-- ====================================================================================
# Jan_24_2014
create table `pnh_t_receipt_reconcilation` (  `id` bigint (20) NOT NULL AUTO_INCREMENT , `debit_note_id` bigint (20) DEFAULT '0', `invoice_no` bigint (20) , `dispatch_id` int (100) , `inv_amount` float (50) DEFAULT '0', `unreconciled` float (50) DEFAULT '0', `created_on` int (50) , `created_by` int (20) , `modified_on` int (50) , `modified_by` int (20) , PRIMARY KEY ( `id`))  
create table `pnh_t_receipt_reconcilation_log` (  `logid` bigint (20) NOT NULL AUTO_INCREMENT , `credit_note_id` int (50) , `receipt_id` int (50) , `reconcile_id` int (50) , `reconcile_amount` float (50) DEFAULT '0', `is_reversed` int (11) DEFAULT '0', `created_on` int (100) , `created_by` int (20) , PRIMARY KEY ( `logid`))  
alter table `pnh_t_receipt_info` add column `unreconciled_value` double   NULL  after `modified_on`, add column `unreconciled_status` varchar (11) DEFAULT 'pen'  NULL  after `unreconciled_value`;
update pnh_t_receipt_info set unreconciled_value = receipt_amount;

-- ====================================================================================

select * from king_invoice
-- new 
select ref_dispatch_id from king_invoice where invoice_no='20141019614' group by invoice_no

-- new
insert into pnh_t_receipt_reconcilation

# Jan_25_2014

CREATE TABLE `king_admin_activitylog` (                  
                          `id` int(11) NOT NULL AUTO_INCREMENT,                  
                          `user_id` int(11) DEFAULT '0',                         
                          `visited_url` varchar(4000) DEFAULT NULL,              
                          `reference_method` varchar(50) DEFAULT NULL,           
                          `ipaddress` varchar(255) DEFAULT NULL,                 
                          `logged_on` datetime DEFAULT NULL,                     
                          PRIMARY KEY (`id`)                                     
                        );

select * from pnh_t_receipt_info where franchise_id = '43';

update pnh_t_receipt_info set unreconciled_value='' and unreconciled_status='' where 1=1;

truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;

-- new 1
select sum(unreconciled_value),count(receipt_id) as num_receipts from pnh_t_receipt_info where franchise_id = '43' and unreconciled_value != 0 and status in (1);

-- new 2
select receipt_id,receipt_amount,unreconciled_value from pnh_t_receipt_info where franchise_id = '43' and status = 1;

# Jan_29_2014
select * from  pnh_t_receipt_info where franchise_id = '43' and unreconciled_status !='p'

-- // RESET RECONCILE TABLE
truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
update pnh_t_receipt_info set unreconciled_value = receipt_amount;

jx_manage_reservation_create_batch_form.php

select * from pnh_t_receipt_info where franchise_id = '43' and status = 1;

#Jan_30_2014
-- // RESET RECONCILE TABLE
truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation`;
truncate table `snapittoday_db_jan_2014`.`pnh_t_receipt_reconcilation_log`;
update pnh_t_receipt_info set unreconciled_value = receipt_amount;
