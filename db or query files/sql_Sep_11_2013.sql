user_access_roles
select user_id,name,username,email,mobile,corpid,corpemail,address,country from king_admin;
select * from king_admin
select * from m_streams where status=1;
select * from king_users;


select user_id,name,username from king_admin where account_blocked!=1 order by name asc


INSERT into m_stream_users(stream_id,user_id,access,is_active,created_by,created_on,modified_by,modified_on) values(?,?,?,?,?,?,?,?) (`0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`) VALUES (8, '34', 1, 1, '1', '2013-09-11 16:13:36', '1', '2013-09-11 16:13:36')

select su.*,ka.name,ka.username from m_stream_users as su 
join king_admin ka on ka.id=su.user_id
where su.stream_id=9
group by su.user_id
order by su.user_id

select * from m_streams;
select * from m_stream_users;
select * from m_stream_posts;
select * from m_stream_assigned_users;

select sau.*,ka.username,ka.name,ka.email from m_stream_assigned_users sau

join king_admin ka on ka.id=sau.userid group by sau.userid

join m_stream_posts sp on sp.stream_id=

select sau.*,ka.username,ka.name,ka.email from m_stream_assigned_users sau
join king_admin ka on ka.id=sau.assigned_userid where ka.streamid=11

select * from m_stream_posts sp
where sp.stream_id=11 and sp.status=1
order by sp.posted_on;


select sp.*,ka.username,ka.name,ka.email from m_stream_posts sp
join king_admin ka on ka.id=sp.posted_by
where sp.stream_id='12' and sp.status=1


select sp.*,ka.username,ka.name,ka.email from m_stream_posts sp
					join m_stream_post_assigned_users spau on spau.post_id=sp.id
                                            join king_admin ka on ka.id=sp.posted_by
                                            where sp.stream_id=1 and sp.status=1
                                            order by sp.posted_on desc

select sau.*,ka.username,ka.name,ka.email from m_stream_post_assigned_users sau
                                                    join king_admin ka on ka.id=sau.assigned_userid where sau.post_id=1

select su.*,ka.name,ka.username from m_stream_users as su 
                                            join king_admin ka on ka.id=su.user_id 
                                            where stream_id=?
                                            group by su.user_id order by su.user_id

select sau.*,ka.username,ka.name,ka.email from m_stream_post_assigned_users sau
                                                    join king_admin ka on ka.id=sau.assigned_userid where sau.assigned_userid=? group by sau.userid

select su.*,ka.name,ka.username from m_stream_users as su 
                                            join king_admin ka on ka.id=su.user_id
						
                                            group by su.user_id,su.access order by su.user_id

select sp.*,ka.username,ka.name,ka.email from m_stream_posts sp
                                            join king_admin ka on ka.id=sp.posted_by
                                            
                                            order by sp.posted_on desc

select name,email from king_admin where id=12
