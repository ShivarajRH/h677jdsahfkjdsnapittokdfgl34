select * from king_brands order by name;

select * from king_deals;
select * from king_dealitems;

select * from king_orders;

select * from king_transactions;

select * from king_orders o
join king_dealitems dl on dl.id=o.itemid
join king_deals deal on deal.dealid=dl.dealid
where deal.menuid=104 and deal.brandid=62772127;


select mn.id,mn.name from pnh_menu mn
join king_deals deal on deal.menuid=mn.id
join king_orders o on o.id=deal.dealid
where mn.status=1
group by mn.id
order by mn.name;

select br.id,br.name from king_brands br
join king_orders o on o.id=deal.dealid
join king_orders o on o.brandid=br.brandid
join king_deals deal on deal.brandid=br.id
where deal.menuid=104
order by br.name;





select mn.id,mn.name from pnh_menu mn
join king_deals deal on deal.menuid=mn.id
join king_orders o on o.id=deal.dealid
where mn.status=1
group by mn.id
order by mn.name;

select br.id,br.name from king_brands br
join king_orders o on o.brandid=br.id
group by br.id
order by br.name;

select br.id,br.name from king_brands br
join king_orders o on o.brandid=br.id
group by br.id
order by br.name;


select deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,distinct b.transid,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id
                                from king_orders o 
                                join king_transactions b on o.transid = b.transid 
                                left join proforma_invoices c on c.order_id = o.id
                                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no and sd.shipped = 0 
                                join pnh_member_info pu on pu.user_id=o.userid 
                                join pnh_m_franchise_info d on d.franchise_id = b.franchise_id
                                join pnh_m_territory_info f on f.id = d.territory_id
                                join pnh_towns e on e.id = d.town_id 
                                join king_dealitems dl on dl.id=o.itemid
                                join king_deals deal on deal.dealid=dl.dealid
                                join king_brands br on br.id = deal.brandid 
                                join pnh_menu m on m.id = deal.menuid 

                                where o.status != 3 and o.actiontime between 1362853800 and 1378492199 
                                group by b.transid 
                                order by b.init desc

