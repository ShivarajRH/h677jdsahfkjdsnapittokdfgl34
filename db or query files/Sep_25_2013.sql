select distinct d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,c.transid,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
                    from king_orders o 
                    join king_transactions c on o.transid = c.transid 
                    join pnh_member_info pu on pu.user_id=o.userid 
                    join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
                    join pnh_m_territory_info f on f.id = d.territory_id
                    join pnh_towns e on e.id = d.town_id 
                    join king_dealitems dl on dl.id=o.itemid
                    join king_deals deal on deal.dealid=dl.dealid
                    join king_brands br on br.id = deal.brandid 
                    join pnh_menu m on m.id = deal.menuid 
            where c.init between 1377973800 and 1380133799 
            group by c.transid  
            order by c.init desc   limit 0,25


select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = 'PNHYGW18199'
                                                                     order by c.p_invoice_no desc
/* SHIPPED **/
select distinct d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,b.transid,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
                            from shipment_batch_process_invoice_link sd
                            join proforma_invoices b on sd.p_invoice_no = b.p_invoice_no
                            join king_transactions c on c.transid = b.transid
                            join king_orders o on o.id = b.order_id  
                            join pnh_member_info pu on pu.user_id=o.userid 
                            join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
                            join pnh_m_territory_info f on f.id = d.territory_id
                            join pnh_towns e on e.id = d.town_id 
                            join king_dealitems dl on dl.id=o.itemid
                            join king_deals deal on deal.dealid=dl.dealid
                            join king_brands br on br.id = deal.brandid 
                            join pnh_menu m on m.id = deal.menuid 

                            where o.status = 2 and sd.shipped = 1 and c.is_pnh = 1  and sd.shipped_on between from_unixtime(1377973800) and from_unixtime(1380133799) 
                            group by b.transid 
                            order by sd.shipped_on desc limit 0,25 

select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = 'PNHHDZ48559'
                                                                     and a.status = 2 and sd.shipped = 1 and sd.shipped_on between from_unixtime(1377973800) and from_unixtime(1380133799) 
								order by c.p_invoice_no desc

select round(sum(nlc*quantity)) as amt from king_invoice i join king_orders o on o.order_id = i.id where o.invoice_no = '20141006550';
