select sp.*,ka.id as userid,ka.username,ka.name,ka.email from m_stream_posts sp
	                                    join king_admin ka on ka.id=sp.posted_by
					join m_stream_post_assigned_users
	                                    where sp.stream_id=1 and sp.status=1
	                                    order by sp.posted_on desc