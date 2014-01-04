select s.*,su.* from m_streams s 
join m_stream_users su on su.stream_id = s.id 
where status=1 group by s.id order by s.title asc

select su.*,ka.name,ka.username from m_stream_users as su 
                                            join king_admin ka on ka.id=su.user_id 
                                            where stream_id=1
                                            group by su.user_id order by su.user_id

select sp.*,ka.id as userid,ka.username,ka.name,ka.email from m_stream_posts sp
                                            join king_admin ka on ka.id=sp.posted_by
                                            where sp.stream_id=2 and sp.status=1
                                            order by sp.posted_on desc

select sau.*,ka.name,ka.username,ka.email,ka.mobile,ka.gender,ka.img_url from m_stream_post_assigned_users sau
         join king_admin ka on ka.id=sau.assigned_userid where sau.post_id=9 and ka.account_blocked!=1

/* 14 sep */

select spr.*,ka.username,ka.email,ka.img_url from m_stream_post_reply spr
join king_admin ka on ka.id=spr.replied_by
where status=1 and post_id = 11 and account_blocked!=1
order by replied_on desc limit 0,10