<style>
	h2 {width:60%;float:left;}
	.inpadding { padding: 5px; width: 40px; }
	.filters_block {
		margin-top: 0px;
		margin-bottom: 8px;
		width: 100%;
		display: block;
	}
	.filters_block .filter {
		float: left;
		background-color: #FCF5F5;
		border: 1px solid #dddddd;
		margin: 5px 5px;
		padding: 3px 5px;
	}
	.pagination_link { margin: 10px 4px 0px 0px; padding: 5px;}
	.pagination_link a {
		background: none repeat scroll 0 0 #dddddd;
		color: rgb(0, 0, 0);
		font-size: 13px;
		padding: 3px 6px;
	}
	select#franchise_id { max-width: 210px; }
	.logblock { cursor: pointer; }
	.datagrid th { text-align: center !important; }
</style>
<?php 
	$prioritys=array("Low","Medium","High","Urgent");
?>
<div class="container">
	<h2>Manage Tickets</h2>
	
	<div class="btn_reset_filters fl_right button button-flat-highlight" style="margin: 0 8% 0 0;" onclick="return reset_filters();">Reset Filters</div>
	
	<div class="notification_blk" style=""></div>

	<div id="manage_tickets_tab" style="width:100%;float:left">
		<ul>
			<li><a href="#11" type="storeking_tickts" onclick="return load_tab_content(1)">StoreKing</a></li>
			<li><a href="#11" type="snapittoday_tickts" onclick="return load_tab_content(2)">Snapittoday</a></li>
		</ul><?php //echo site_url().'/admin/support_tickts_jx/2/0/0/0/0/0/0/0'; ?>
		
		<div id="11">
			<div>
				<div class="filters_block">

					<div class="filter">

						<input type="hidden" name="source" class="source" value="1" size="4" />
						
						Status : <select id="status">
							<option value="0">All</option>
							<option value="1">Unassigned</option>
							<option value="2">Open</option>
							<option value="3">In Progress</option>
							<option value="4">Closed</option>
							<option value="5">Unknown</option>
						</select>
						<br>
						Priority : <select id="priority">
							<option value="0">All</option>
							<option value="1">Low</option>
							<option value="2">Medium</option>
							<option value="3">High</option>
							<option value="4">Urgent</option>
						</select>
					</div>

					<div class="filter" id="franchise_filter_block">
						Franchise : <select id="franchise_id">
							<option value="0">All</option>
							<?php
							$fran_info_res = $this->db->query("SELECT t.franchise_id,f.franchise_name FROM support_tickets t
														LEFT JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
														WHERE t.franchise_id != 0
														GROUP BY f.franchise_id
														ORDER BY f.franchise_name ASC;");
							if( $fran_info_res->num_rows() ) {
								foreach($fran_info_res->result_array() as $fran_info) {?>
									<option value="<?=$fran_info['franchise_id'];?>"><?=$fran_info['franchise_name']; ?></option>

			<?php				}
							}
							?>
						</select>
					</div>


					<div class="filter">
						Date range : <input type="text" size="8" class="inp" id="ds_range" value=""> to <input size="8" type="text" class="inp" id="de_range" value="">
					</div>

					<div class="filter">
						Tickets From : <select id="tickets_from">
								<option value="0">All</option>
								<option value="1">ERP/Web</option>
								<option value="2">Mobile/API</option>
							</select>
					</div>

					<div class="filter">
						Types :<select name="type" class="type">
									<option value="all">All</option>
									<option value="0">Query</option>
									<option value="1">Order Issue</option>
									<option value="2">Bug</option>
									<option value="3">Suggestion</option>
									<option value="4">Common</option>
									<option value="5">PNH Returns</option>
									<option value="6">Courier Followups</option>
									<!--new services-->
									<option value="10">Request</option>
									<option value="11">Complaint</option>
								</select>
					</div>


					<div class="filter">
						Related To : <select class="related_to">
								<option value="0">All</option>
								<?php $req_types = $this->erpm->get_request_types();
								if($req_types) {
									foreach($req_types as $req_type) {

										echo '<option value="'.$req_type['id'].'">'.$req_type['name'].'</option>';
									}
								}
								else {
									echo '<option value="0">No results</option>';
								}
								?>
							</select>
					</div>


					<div class="filter">
						<input type="button" value="Go" onclick='return showtickets()' class="button button-action button-tiny button-rounded">
					</div>



					<!--=======================< END FILTERS CODE >=======================-->


					<!--=======================< Show Counts Log >=======================-->
					<div class="clear">&nbsp;</div>
						<!--ttl_unassinged ttl_open ttl_inprogress ttl_closed-->
						<!--<a href="<?php //echo site_url("admin/support")?>"></a>--><?php //echo $this->db->query("select count(1) as l from support_tickets")->row()->l; ?>
						<!--<a href="<?php //echo site_url("admin/support/unassigned")?>"></a><?php //echo $this->db->query("select count(1) as l from support_tickets where status=0")->row()->l?>-->
						<!--<a href="<?php //echo site_url("admin/support/opened")?>"></a>--><?php //echo $this->db->query("select count(1) as l from support_tickets where status=1")->row()->l?>
						<!--<a href="<?php //echo site_url("admin/support/inprogress")?>"></a>--><?php //echo $this->db->query("select count(1) as l from support_tickets where status=2")->row()->l?>
						<!--<a href="<?php //echo site_url("admin/support/closed")?>"></a>--><?php //echo $this->db->query("select count(1) as l from support_tickets where status=3")->row()->l?>
					<div class="dash_bar logblock" onclick="return show_tkts(0);">
						<span class="ttl_tickets">0</span> Total tickets
					</div>
					<div class="dash_bar logblock" onclick="return show_tkts(1);">
						<span class="ttl_unassinged">0</span> Unassigned tickets
					</div>
					<div class="dash_bar logblock" onclick="return show_tkts(2);">
						<span class="ttl_open">0</span> Opened tickets
					</div>
					<div class="dash_bar logblock" onclick="return show_tkts(3);">
						<span class="ttl_inprogress">0</span> In-progress tickets
					</div>
					<div class="dash_bar logblock" onclick="return show_tkts(4);">
						<span class="ttl_closed">0</span> Closed tickets
					</div>
					<!--<div class="clear"></div>-->
					<div class="dash_bar fl_right">
						Average resolve time : <span class="avg_resolve_time">0</span>
					</div>

					<div class="clear"></div>

				</div>

				<!--<h2 width="60%" class="fl_left"><?=ucfirst($filter)?> support tickets <?=isset($pagetitle)?$pagetitle:"last 30 days"?></h2>-->
				<h2 width="60%" class="fl_left pg_msg"></h2>

				<a href="<?=site_url("admin/addticket")?>" class="button button-tiny button-action button-rounded fl_right">Add new ticket</a>
				<table class="show_tickets_tbl datagrid" width="100%">
					<thead>
					<tr>
					<th width="5" align="center">#</th>
					<th width="15" align="center">Created on</th>
					<th width="15" align="center">Last Activity on</th>
					<th align="center">Ticket No</th>
					<th align="center">User / Franchise</th>
					<th align="center">Status</th>
					<!--<th align="center">Type</th>-->
					<th align="center">Related To</th>
					<th align="center">Priority</th>
					<th align="center">Assigned To</th>
					<!--<th align="center">Linked Depts</th>-->
					<th align="center">From APP/WEB</th>
					<th align="center">Email</th>
					<th align="center">Transaction</th>
					</tr>
					</thead>
					<tbody align="center" width="100%"></tbody>
				</table>
				<div class="pagination_link fl_right"></div>

			</div>
		
		</div>
		
		
		<!--<div id="12">Coming Soon...</div>-->
		
		
	</div>
	
	
	<!--=======================< START FILTERS CODE >=======================-->
	
</div>


<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=tickets_script&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>

<?php //echo $this->uri->segment(4); ?>

<?php
