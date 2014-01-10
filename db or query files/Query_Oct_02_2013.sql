#franchise multiple products groupid
alter table `king_transactions` change column `trans_grp_refno` `trans_grp_ref_no` int (50)UNSIGNED  NOT NULL COMMENT 'Group of transactions id' after `trans_created_by`;

#=====================

select count(order_id) as total_orders from products_group_orders  group by transid order by total_orders desc;

select * from cou_coupon;
select * from pnh_member_info;
select * from pnh_member_points_track where user_id=77633;
desc pnh_member_points_track;



select max(trans_grp_refno) as grp_refno from king_transactions order by trans_grp_refno desc limit 1

select trans_grp_refno as grp_refno from king_transactions order by grp_refno desc limit 1

select * from pnh_member_info where mobile=99009999;
select * from king_users where userid=82045;

delete from pnh_member_info where mobile=99009999;
delete from king_users where userid=82045;

select ifnull(max(trans_grp_ref_no),400001) as n from king_transactions where trans_grp_ref_no > 0

select datediff(curdate(),date(from_unixtime(created_on))) as reg_days,current_balance as balance,credit_limit as credit,is_suspended,reason from pnh_m_franchise_info where franchise_id='39996125'

select value from m_config_params where name = 'LAST_MEMBERID_ALLOTED'; //22001693

select count(*) as t from pnh_member_info where pnh_member_id = 22001693 + 1

select m.*,a.name as admin from pnh_m_allotted_mid m 
join king_admin a on a.id=m.created_by
where franchise_id='151' order by mid_start desc;

select * from pnh_member_info where pnh_member_id=22001693;

SELECT i.*,d.publish,c.loyality_pntvalue FROM king_dealitems i 
JOIN king_deals d ON d.dealid=i.dealid 
JOIN pnh_menu c ON c.id = d.menuid 
WHERE i.is_pnh=1 AND  i.pnh_id=1242764 AND i.pnh_id!=0 AND c.id IN(112,118,122)


select if(max(trans_grp_ref_no)=0,400001) from king_transactions where trans_grp_ref_no > 0
select IF(trans_grp_ref_no='0',400001,max(trans_grp_ref_no)+1) AS n from king_transactions
select IF(max(trans_grp_ref_no)=0,4000024,max(trans_grp_ref_no)+1) AS n from king_transactions limit 1

update king_transactions set trans_grp_ref_no=0;
update king_transactions set trans_grp_ref_no=400001 limit 1;

CREATE TABLE `t_imeino_allotment_track` (
`id` bigint(11) NOT NULL AUTO_INCREMENT,
`imeino_id` bigint(11) DEFAULT '0',
`product_id` bigint(11) DEFAULT '0',
`imei_no` varchar(255) DEFAULT NULL,
`order_id` bigint(20) DEFAULT '0',
`invoice_no` bigint(20) DEFAULT '0',
`transid` varchar(255) DEFAULT NULL,
`is_cancelled` int(1) DEFAULT '0',
`alloted_on` datetime DEFAULT NULL,
`cancelled_on` datetime DEFAULT NULL,
`alloted_by` bigint(11) DEFAULT '0',
`cancelled_by` bigint(11) DEFAULT '0',
PRIMARY KEY (`id`)
);

alter table king_invoice add column ref_dispatch_id bigint(11) default 0;

select * from king_transactions order by trans_grp_ref_no desc;
##=============================================================================================================
## Oct_04_2013 streams updates
select distinct user_id from m_stream_users where stream_id=2

select a.id,a.name,a.email,s.title as stream_title from king_admin a
left join m_stream_users su on su.user_id=a.id and 
left join m_streams s on s.id=su.stream_id
where a.id=1

select a.id,a.name,a.email,s.title as stream_title from king_admin a
	join m_stream_users su on su.user_id=a.id
	join m_streams s on s.id=su.stream_id and s.id=3
	where a.id=1

 and su.stream_id=4

select title from m_streams where id = 1

select * from m_stream_users where user_id=1;

#////////////////////////////////////////////
DELIMITER $$
CREATE FUNCTION UC_FIRST (INPUT VARCHAR(255))
 
RETURNS VARCHAR(255)
 
DETERMINISTIC
 
BEGIN
    DECLARE len INT;
    DECLARE i INT;
 
    SET len   = CHAR_LENGTH(INPUT);
    SET INPUT = LOWER(INPUT);
    SET i = 0;
 
    WHILE (i < len) DO
        IF (MID(INPUT,i,1) = ' ' OR i = 0) THEN
            IF (i < len) THEN
                SET INPUT = CONCAT(
                    LEFT(INPUT,i),
                    UPPER(MID(INPUT,i + 1,1)),
                    RIGHT(INPUT,len - i - 1)
                );
            END IF;
        END IF;
        SET i = i + 1;
    END WHILE;
 
    RETURN INPUT;
END$$
DELIMITER ;
##///////////////////////////////////////////////////
##=============================================================================================================
SELECT UC_FIRST('my string of words');

#SHOW ALL USER DEFINED FUNCTIONS OF DATABASE
SHOW PROCEDURE STATUS;
SHOW FUNCTION STATUS;

DROP FUNCTION CAP_FIRST
DROP FUNCTION IF EXISTS UC_FIRST;
DROP FUNCTION IF EXISTS UC_DELIMETER;

help show
#=====================

select * from pnh_m_franchise_info where (login_mobile1=9964205827 and login_mobile1<>0) or (login_mobile2=9964205827 and login_mobile2!=0);

## Oct-05-2013
set @fid=39996125; #@franchise_id
select a.*,town_name,territory_name from pnh_m_franchise_info a 
join pnh_m_territory_info b on a.territory_id = b.id 
join pnh_towns c on a.town_id = c.id where a.pnh_franchise_id=@fid
 
set @mobile=9886747436;
select * from pnh_m_franchise_info where (login_mobile1=@mobile and login_mobile1<>0) or (login_mobile2=@mobile and login_mobile2!=0)

## ============================================================

select * from m_stream_posts where stream_id=4

select a.id,a.name,a.email,s.title as stream_title from king_admin a
                                                join m_stream_users su on su.user_id=a.id
                                                join m_streams s on s.id=su.stream_id and s.id= '2'

                                                where a.id=Array

select distinct user_id from m_stream_users where stream_id=4