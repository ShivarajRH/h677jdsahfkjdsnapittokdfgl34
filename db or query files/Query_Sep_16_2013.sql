select md5('suresh')
#spau.userid=1 and 

select * from m_stream_post_assigned_users spau
where spau.viewed=0 and spau.assigned_userid=26

update m_stream_post_assigned_users set viewed=1 where assigned_userid=26

select * from king_admin where username='suresh';