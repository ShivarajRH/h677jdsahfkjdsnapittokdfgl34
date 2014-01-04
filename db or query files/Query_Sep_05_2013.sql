/* 06_sep_2013 */
select distinct b.transid,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
                            from shipment_batch_process_invoice_link sd
                            join proforma_invoices b on sd.p_invoice_no = b.p_invoice_no
                            join king_transactions c on c.transid = b.transid
                            join king_orders o on o.id = b.order_id  
                            join pnh_member_info pu on pu.user_id=o.userid 
                            join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
                            join pnh_m_territory_info f on f.id = d.territory_id
                            join pnh_towns e on e.id = d.town_id 

                            where o.status = 2 and sd.shipped = 1 and is_pnh = 1  and sd.shipped_on between from_unixtime(1364754600) and from_unixtime(1378492199) 
                            group by b.transid 
                            order by sd.shipped_on desc limit 0,25 

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no and sd.packed = 1 and sd.shipped = 1 
                                                                where a.transid = 'PNHZFG17244'
                                                                     and a.status = 2 and sd.shipped = 1 and sd.shipped_on between from_unixtime(1372617000) and from_unixtime(1378492199)  order by c.p_invoice_no desc
/* 05_sep_2013 


select distinct b.transid,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id
		from king_orders o 
			join king_transactions b on o.transid = b.transid 
			left join proforma_invoices c on c.order_id = o.id
			left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no and sd.shipped = 0 
			join pnh_member_info pu on pu.user_id=o.userid 
			join pnh_m_franchise_info d on d.franchise_id = b.franchise_id
			join pnh_m_territory_info f on f.id = d.territory_id
			join pnh_towns e on e.id = d.town_id 

			where o.status != 3 and o.actiontime between 1377973800 and 1378405799  
			group by b.transid 
			order by b.init desc*/

select * from king_orders;

select tw.id,tw.town_name from pnh_towns tw order by tw.town_name;

select id,name from pnh_menu where status=1 order by name;

select id,name from king_brands order by name;

select * from king_orders o
	join king_dealitems dl on dl.id=o.itemid
	join king_deals deal on deal.dealid=dl.dealid
	where deal.menuid=104 and deal.brandid=62772127;


