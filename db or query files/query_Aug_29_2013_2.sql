/* Calls MADE
FROM EMPLOYEES */
select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join m_employee_info emp on emp.contact_no = substr(exa.from,2) 

/* Calls MADE 
FROM FRANCHISE */
select frn.franchise_id callerid,frn.franchise_name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2)

/* Calls MADE
FROM UNKNOWN (NOT FROM EMP AND FRANCHISE)*/

select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join m_employee_info emp on emp.contact_no = substr(exa.dialwhomno,2)
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.dialwhomno,2)
WHERE emp.employee_id IS NULL OR emp.name IS NULL 

SELECT * FROM 
(
(
select exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
WHERE substr(exa.dialwhomno,2) NOT IN (SELECT emp.contact_no mobile FROM m_employee_info emp)
)
UNION
(
select exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
WHERE substr(exa.dialwhomno,2) NOT IN (SELECT frn.login_mobile1 mobile FROM pnh_m_franchise_info frn)
)
) as b


select emp.employee_id callerid,emp.name callername,exa.from,frn.login_mobile1,emp.contact_no,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2)
LEFT join m_employee_info emp on emp.contact_no = substr(exa.from,2)

select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join m_employee_info emp on emp.contact_no = substr(exa.from,2)
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.from,2)
WHERE emp.employee_id IS NOT NULL and emp.name IS NOT NULL 

WHERE emp.employee_id IS NULL and emp.name IS NULL 

SELECT * FROM 
(
(
select exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
WHERE substr(exa.from,2) NOT IN (SELECT emp.contact_no mobile FROM m_employee_info emp)
)
UNION
(
select exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
WHERE substr(exa.from,2) NOT IN (SELECT frn.login_mobile1 mobile FROM pnh_m_franchise_info frn)
)
) as b




/*WHERE emp.employee_id IS NULL and emp.name IS NULL order by calledtime,callsid ASC ; */

/* Calls RECEIVED
TO EMPLOYEES */
select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join m_employee_info emp on emp.contact_no = substr(exa.dialwhomno,2) 

/* Calls RECEIVED 
TO FRANCHISE */
select frn.franchise_id callerid,frn.franchise_name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa 
join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.dialwhomno,2) 

/* Calls RECEIVED
FROM UNKNOWN (NOT FROM EMP AND FRANCHISE)*/
select emp.employee_id callerid,emp.name callername,exa.from mobile,exa.callsid,exa.dialwhomno as towhom,exa.status,exa.created_on as calledtime from t_exotel_agent_status exa
LEFT join m_employee_info emp on emp.contact_no = substr(exa.dialwhomno,2)
LEFT join pnh_m_franchise_info frn on frn.login_mobile1 = substr(exa.dialwhomno,2)
WHERE emp.employee_id IS NULL OR emp.name IS NULL;


