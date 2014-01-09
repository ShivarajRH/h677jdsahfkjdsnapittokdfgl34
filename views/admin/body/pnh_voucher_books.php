<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Voucher Books</h2>
		</div>
		<div class="fl_right" >
			<?php /*<a href="<?php echo site_url('admin/pnh_create_voucher_book') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small">Create voucher book</a>&nbsp;*/ ?>
			<a href="<?php echo site_url('admin/pnh_create_book') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small">Create book</a>&nbsp;
			<a href="<?php echo site_url('admin/pnh_book_stock_in') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small">Book stock in</a>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="fl_left" >
			<b>Total Books :</b> <span><?php echo $total_books; ?></span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<form method="post" id="filter_form">
				Search book slno:<input type="text" name="srch">
				<input type="submit" value="Go">
			</form>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		<?php 
		if($books_list)
		{
		?>
			<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>#</th>
						<th>Book type</th>
						<th>Book serial no</th>
						<th>Book menu</th>
						<th>Value</th>
						<th>Vouchers</th>
						<th>Is alloted</th>
						<th>Created on</th>
						<th>Created by</th>
					</tr>
				</thead>
				<tbody>
					<?php 				
						foreach($books_list as $i=> $book)
						{
					?>	
						<tr>
							<td><?php echo ($i+1);?></td>
							<td><?php echo ucfirst($book['book_type_name']); ?></td>
							<td><?php echo $book['book_slno']; ?></td>
							<td>
								<?php 
									$menus_list=$this->db->query("select name from pnh_menu where id in (select a.menu_ids from pnh_m_book_template a join pnh_t_book_details b on b.book_template_id=a.book_template_id where b.book_id=?)",$book['book_id'])->result_array();
									if($menus_list)
									{
										foreach($menus_list as $m)
										{
											echo $m['name']."<br>";	
										}
									}
								?>
							</td>
							<td><?php echo $book['book_value']; ?></td>
							<?php $voucher_serial_no=$this->db->query("select min(voucher_serial_no) as s,max(voucher_serial_no) as e from pnh_t_book_voucher_link a join pnh_t_voucher_details b on b.id=a.voucher_slno_id where a.book_id=?",$book['book_id'])->row_array()?>
							<td>
								<span style="font-size:9px;"><?php echo '('.$voucher_serial_no['s'].'-'.$voucher_serial_no['e'].')'; ?></span><br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" book_id="<?php echo $book['book_id']?>" class="view_vouchers">view</a>
							</td>
							<td><?php echo $book['franchise_id']?'yes':'No'; ?></td>
							<td><?php echo format_date($book['created_on']);?></td>
							<td><?php echo $book['username'];?></td>
						</tr>
				<?php } ?>
					<tr >
						<td colspan="12" align="right"><?php echo $pagination; ?></td>
					</tr>
				</tbody>
			</table>
	<?php }else{
		echo '<div align="center"><b>No books found</b></div>';
	}?>
	</div>
</div>

<div id="book_vouchers_list" title="vouchers list">
</div>

<script>
	$(".view_vouchers").click(function(){
		var book_id=$(this).attr('book_id');
		$("#book_vouchers_list").data({'book_id':book_id}).dialog('open');	
	});

	$("#book_vouchers_list").dialog({
		modal:true,
		autoOpen:false,
		width:'300',
		height:'300',
		open:function(){
			$("#book_vouchers_list").html('');
			var book_id=$(this).data('book_id');
			var html_cnt='';
			$.post(site_url+'/admin/jx_get_vouchers_list_by_book',{book_id:book_id},function(resp){
				if(resp.status=='error')
				{
					alert(resp.message);
				}else{
					html_cnt+='<table class="datagrid" cellpadding="5" cellspacing="0" width="100%"><thead><tr><th>#</th><th>Voucher</th><th>Voucher slno</th><th>Value</th></tr><tbody>';
					$.each(resp.vouchers_list,function(a,b){
						html_cnt+="<tr>";
							html_cnt+="<td>"+(a+1)+"</td>";
							html_cnt+="<td>"+b.voucher_name+"</td>";
							html_cnt+="<td>"+b.voucher_serial_no+"</td>";
							html_cnt+="<td>"+b.denomination+"</td>";
						html_cnt+="</tr>";	
						});	
					html_cnt+='</tbody></table>';
					$("#book_vouchers_list").html(html_cnt);
				}	
			},'json');
		},
		buttons:{
				'Close':function(){
			$(this).dialog('close');
		}
	}
	});

	//filter
	$("#filter_form").submit(function(){
		var search_query=$("input[name='srch']",this).val();

		if(!search_query)
			search_query=0;
		
		location.href = site_url+'admin/pnh_voucher_book/'+search_query;
		return false;
	});
</script>
