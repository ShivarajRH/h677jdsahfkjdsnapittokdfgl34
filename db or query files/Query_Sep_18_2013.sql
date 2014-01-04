select * from king_admin order by name desc;

Select s.*,ka.username,ka.email,ka.mobile from m_streams s
join king_admin ka on ka.id=s.created_by
where s.created_by=1;

Select s.*,ka.username,ka.email,ka.mobile from m_streams s
                                    join king_admin ka on ka.id=s.created_by
                                    where s.id=4
                                    order by s.created_time desc

select su.stream_id,su.user_id,su.access,su.created_by from m_stream_users su where su.user_id=3 order by su.user_id

/*************/
alter table `snapitto_live_august`.`m_streams` add column `modified_by` varchar (100)  NULL  after `created_time`;

alter table `snapitto_live_august`.`m_streams` change `created_by` `created_by` varchar (255) DEFAULT '0' NOT NULL  COLLATE utf32_unicode_ci , 
		change `modified_by` `modified_by` varchar (100) DEFAULT '0' NULL  COLLATE utf32_unicode_ci,
		change `modified_time` `modified_time` varchar (90)  NOT NULL  COLLATE utf32_unicode_ci 

/****************/

update m_streams set title='Shipping44444',description='Shipping related queries',modified_by='1',modified_time=1379512114,status='0' where id='3';

select * from m_stream_users su

(select s.*,count(sp.stream_id) as total_posts,su.* from m_streams s
	join  m_stream_posts sp on s.id=sp.stream_id
	join m_stream_users su on su.stream_id = s.id
	where s.status=1 group by s.id order by s.title asc)

select * from m_stream_post_reply spr

select * from m_stream_posts sp
join m_stream_post_reply spr on sp.id != spr.post_id
 where sp.stream_id=2  group by spr.post_id,sp.stream_id

select * from m_stream_posts sp where sp.stream_id=2 and sp.id NOT IN (select post_id from m_stream_post_reply);

/***********************************************************************************/
##### Sep-25-2013
select sp.*,ka.id as userid,ka.username,ka.name,ka.email from m_stream_posts sp
					join king_admin ka on ka.id=sp.posted_by
					left join m_stream_post_reply spr on spr.post_id=sp.id
					where sp.stream_id=3 and sp.status=1  and (sp.description like "%9591133432%" or spr.description like "%'9448870117'%")
					group by sp.id order by sp.posted_on desc



 


