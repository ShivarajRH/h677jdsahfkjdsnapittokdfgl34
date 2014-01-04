select frn.franchise_id callerid,frn.franchise_name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2) 

select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join m_employee_info emp on (emp.contact_no = substr(exa.from,2) AND emp.contact_no <> substr(exa.from,2)) 
WHERE (emp.contact_no != substr(exa.from,2)) 

SHOW TABLE STATUS LIKE 'pnh_m_franchise_info';

select ti.territory_name,ti.id as territory_id,m.name as menu_name,d.menuid,f.franchise_id,f.franchise_name,t.amount,p.brand_id,p.product_id,o.time,o.transid,i.name as deal,i.id as itemid,p.product_name,sum(s.available_qty) as stock,i.price from king_orders o 
	join king_dealitems i on i.id=o.itemid join king_deals d on d.dealid = i.dealid 
	join king_transactions t on t.transid=o.transid 
	left outer join m_product_deal_link l on l.itemid=i.id 
	left outer join products_group_orders po on po.order_id=o.id 
	left outer join m_product_info p on p.product_id=ifnull(l.product_id,po.product_id) 
	left outer join t_stock_info s on s.product_id=ifnull(p.product_id,po.product_id) 
	left join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
	left join pnh_menu m on m.id = d.menuid and t.is_pnh = 1 
	left join pnh_m_territory_info ti on ti.id = f.territory_id 
#where o.time between $from and $to 
group by o.transid,p.product_id order by o.time desc