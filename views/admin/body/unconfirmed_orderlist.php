<style>
.orders_display_log {
	margin-left: -27%;

}
.span_count_wrap {
background: none repeat scroll 0 0 #87318C;
font-size: 11px;
color: #FFF;
margin: 2px;
padding: 0px;
width: 120px;
text-align: center;
display: block;
}
.level_wrapper
{
	font-size: 9px;
	color:#fff;
	padding:2px 3px;
	border-radius:3px;
	margin-right: 7px;
}

</style>

<?php $transid=$this->uri->segment(3)?$this->uri->segment(3):0;?>

<div id="container">
	<div style="clear: both; overflow: hidden">
		<div class="fl_left">
			<h2 style="margin: 0px;">Unconfirmed Order List</h2>
		</div>

		<form method="post" onsubmit="return filter_form_submit()">

			<input type="hidden" value="<?php echo $transid;?>" name="transid">
			
			<div class="clear"></div>

			<div class="filters_block">

				<div class="filter">
					<span> <b>From :</b><input type="text" name="frm_dt" id="frm_dt"
						style="width: 90px;" value="">
					</span> <span> <b>To :</b> <input type="text" name="to_dt"
						id="to_dt" value="" style="width: 90px;">
					</span>
				</div>
				
				<div class="filter">
					<span> <input type="radio" name="cnfrm_status" id="cnfrm_status"
						checked value="0">Pending <input type="radio" name="cnfrm_status"
						id="cnfrm_status" value="2">Rejected
				</span>
				</div>
				
				<div class="filter">
					<span><b>Order from :</b> </span> <span> <select name="order_frm"
						id="order_frm">
							<option value="0">All</option>
							<option value="1">ERP</option>
							<option value="2">API</option>
							<option value="3">SMS</option>
					</select>
					</span>
				</div>
				</div>
				<br>
				<div class="clear"></div>
				<div class="filters_block">
			
				<div class="filter">
					<span><b>State :</b></span>
					<span><select name="state" id="state">
							<option value="">All States</option>
							<?php $state_list=$this->db->query("SELECT * FROM pnh_m_states order by state_id asc");
								foreach($state_list->result_array() as $s){
							?>
							<option value="<?php echo $s['state_id']?>"><?php echo $s['state_name'] ?></option>
							<?php }?>
						</select></span>
						
				</div>
				<div class="filter">
					<span><b>Territory :</b> </span> 
					<span> <select name="terri" id="terri">
							<option value="">All Territories</option>
							<?php $terri_list=$this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc")->result_array();
							foreach($terri_list as $tr){
					?>
							<option value="<?=$tr['id']?>">
								<?= $tr['territory_name'] ?>
							</option>
							<?php }?>
					</select>
					</span>
				</div>
				<div class="filter">
					<span><b>Town :</b> </span> <span> <select name="town" id="town">
							<option value="0">All Towns</option>

					</select>
					</span>
				</div>

				<div class="filter">
					<span><b>Franchisee :</b> </span> <span> <select name="franchisee"
						id="franchisee">
							<option value="0">All Franchisee</option>
					</select>
					</span>
				</div>
			
				<div  class="fl_left">
					<input type="submit" value="Go"
						class="button button-action button-small"
						style="vertical-align: bottom !important; margin-top: 15px;" />
				</div>
			</div>
		</form>
		<div id="uncrorder_list" class="page_content">
			<div class="pagination fl_right"></div>
		
			<!-- <div class="orders_display_log fl_left"></div> -->

			<div class="clear">&nbsp;</div>
				<div class="total_orders fl_left" style="font-size: 15px;"></div>
			<table class="datagrid" width="100%">
				<thead>
					<th><input type="checkbox" class="chk_all"></th>
					<th>Franchisee</th>
					<th>Time</th>
					<th>Order</th>
					<th>Amount</th>
					<th>Deal/Product Details</th>
					<th>Uncleard Payments</th>
					<th>Current Pending Amt</th>
					<th>Confirmd Open Order Amt</th>
					<th>Remarks</th>
					<th>Action</th>
				</thead>
				<tbody></tbody>
			</table>
			<br>
			<div style="float: right; display: none;" id="confrm_status_bloc">
				<a href="javascript:void(0)" class="button button-small button-rounded button-action " 	onclick="confirm_ordrstatus(1)">Approve</a> 
				<a href="javascript:void(0)" class="button button-small button-rounded button-caution" onclick="confirm_ordrstatus(0)">Reject</a>
			</div>
		</div>
		<div class="pagination" align="right"></div>
	</div>

	<script type="text/javascript"
		src="<?=base_url()?>/min/index.php?g=unconfirmed_orderlist_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>