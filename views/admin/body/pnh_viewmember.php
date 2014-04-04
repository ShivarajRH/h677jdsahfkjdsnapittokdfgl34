

<!-- the jScrollPane script -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.jscrollpane.min.js"></script>

<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/mwheelIntent.js"></script>

<!-- the mousewheel plugin - optional to provide mousewheel support -->
<script type="text/javascript" src="<?=base_url()?>/js/custom_scrollbar/jquery.mousewheel.js"></script>

<!-- styles needed by jScrollPane -->
<link type="text/css" rel="stylesheet" href="<?=base_url()?>/css/custom_scrollbar/jquery.jscrollpane.css">

<style>
/**************************************** Brands CSS ********************************************************/
/******** General CSS start********************/
.mod_widget_small
{
	float:left;
	width:34%;
	min-width:34%;
	margin-right:2%;
}
.mod_widget_large
{
	float:left;
	width:64%;
}
.mod_widget_sub
{
	float:left;
	width:50%;
}
.mod_widget_title
{
	background: none repeat scroll 0 0 #D7E2EE;
    border: 1px solid #C7C3C0;
    width:100%;
}
.mod_widget_content
{
	background: none repeat scroll 0 0 #fcfcfc;
    border-bottom: 1px solid #C7C3C0;
    border-left: 1px solid #C7C3C0;
    border-right: 1px solid #C7C3C0;
    height:200px;
    width:100% !important;
}
.mod_widget_content ul
{
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_content li
{
	border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 18px;
}
.mod_widget_small .mod_widget_content li
{
	margin: 0 2%;
    padding: 2%;
}
.mod_widget_large .mod_widget_content li
{
	margin: 0 1%;
    padding: 1.2%;
}
.mod_widget_title div.heading_wrap
{
	font-size: 13px;
	font-weight: bold;
	color: #606060;
	padding:5px;
}
.mod_widget_title img
{
	display: none;
}
.mod_widget_small .mod_widget_sub .mod_widget_content ul
{
	margin-top: 8px;
	list-style-type:none;
	color:#1D1D1D;
}
.mod_widget_small .mod_widget_sub .mod_widget_content li
{
    border-bottom: 1px solid #D2D1CF;
    font-size: 12px;
    font-weight: bold;
    height: 28px;
    margin: 4% 4% 0;
    padding: 0 7%;
}
.mod_widget_small .mod_widget_sub .mod_widget_content span
{
	margin-left:5px;
	text-transform:capitalize;
}
.mod_widget_content span
{
	margin-left:4px;
	text-transform:capitalize;
}
.mod_widget_row
{
	width:100%;margin-top:2%;
}
/******** General CSS end *********************/
/********** Custom CSS ********************/
.mrp_wrap
{
	color: #FF0000;
    font-size: 11px;
    padding: 0 10px;
    text-align: left;
    width: 9%;
}
.price_wrap
{
	color: #0B0B0E;
    font-size: 11px;
    font-weight: bold;
    text-align: left;
    width: 9%;
}
.barcode_wrap
{
 	color: #0B0B0E;
    font-size: 11px;
    font-weight: bold;
    text-align: left;
    width: 17%;
}
.ttl_count
{
	 color: #000000;
    font-size: 12px;
    padding: 12px 6px 6px 8px;
}
.content_head span.row_label
{
	 float: left;
    font-size: 12px;
    text-align: right;
    width: 26%;
    color: #666666;
}
.content_head span.value
{
	 color: #000000;
    font-size: 13px;
    font-weight: bold;
}
.tab_view_inner li
{
	padding:0px !important;
	margin:0px !important;
	height:auto !important;
}
</style>
<script>
$(function()
{
	//Applied custom scrollbar 
	$('.border_content_right_blk').jScrollPane();
	$('.border_content_left_blk').jScrollPane();
	$('.mod_widget_content').jScrollPane();
});
</script>

<?php $u=$member; 
$gender=array("Male","Female");
$salutation=array("Mr","Mrs","Ms");
$marital=array("Single","Married","Other");
$expenses=array("&lt; Rs. 2000","Rs 2001 - Rs 5000","Rs 5001 - Rs 10000","&gt; Rs. 10000");
?>
<div class="container outer_blk">
	<!----------------------------------Page Title--------------------------->
	<h2> Member - <?=$salutation[$u['salute']]?>. <?="{$u['first_name']} {$u['last_name']}"?></h2>
	
	<!-- Member details -->
	<div class="mod_widget_small">
		<div class="mod_widget_title">
			<div class="heading_wrap"><img src="<?php echo base_url().'images/home2.png'?>">Member Details
			</div>
		</div>
		<div class="mod_widget_content">
			<ul class="content_head">
				<li>
					<span class="row_label">Member ID : </span>
					<span class="value"><?=$u['pnh_member_id']?></span> 
				</li>
				<li>
					<span class="row_label">Franchise : </span>
					<span class="value"><?=$this->db->query("select concat(franchise_name,', ',city) as name from pnh_m_franchise_info where franchise_id=?",$u['franchise_id'])->row()->name?></span> 
				</li>
				<li>
					<span class="row_label">Total Orders : </span>
					 <span class="value"><?=$this->db->query("select count(distinct(transid)) as l from king_orders where userid=?",$u['user_id'])->row()->l?></span>
				</li>
				<li>
					<span class="row_label">Order Value : </span>
					<span class="value">Rs <?=number_format($this->db->query("select sum(t.amount) as l from king_transactions t where t.transid in (select transid from king_orders o where o.userid=?)",$u['user_id'])->row()->l)?></span> 
				</li>
				<li>
					 <span class="row_label">Loyalty Points : </span>
					 <span class="value"><?=$u['points']?></span> 
				</li>
				<li>
					<span class="row_label">Voucher value : </span>
					<span class="value">Rs <?=$this->db->query("SELECT SUM(denomination) AS voucher_value  FROM pnh_t_voucher_details l JOIN `pnh_m_voucher`v ON v.voucher_id=l.voucher_id WHERE STATUS>=3 AND member_id=?",$u['pnh_member_id'])->row()->voucher_value?></span>
				</li>
				<li>
					 <span class="row_label">Voucher Balance : </span>
					 <span class="value">Rs <?=$this->db->query("SELECT SUM(customer_value) AS voucher_bal FROM pnh_t_voucher_details WHERE STATUS in (3,5) AND member_id=?",$u['pnh_member_id'])->row()->voucher_bal?> </span>
				</li>
			</ul>
		</div>
	</div>
	
	<!----------------------------------Orders &amp; Loyalty Points block--------------------------->
	<div class="mod_widget_large">
		<div class="mod_widget_title">
			<div class="heading_wrap">
				<img src="<?php echo base_url().'images/home2.png'?>">Orders &amp; Loyalty Points
 			</div>
		</div>
		<div class="mod_widget_content ">
			<table class="datagrid" width="100%">
				<tbody>
					<tr>
						<td><b>Transid</b></td>
						<td><b>Amount</b></td>
						<td><b>Cancelled/Returned</b></td>
						<td><b>Payable</b></td>
						<td><b>Ordered On</b></td>
						<td><b>Status</b></td>
						<td><b>Loyalty Points[Allotted On]</b></td>
						<?php /*?><td>Payment Type</th>/*/?>
					</tr>
					<?php 
						$status=array("Pending","Invoiced","Shipped","Cancelled"); 
						foreach($this->db->query("select o.*,t.amount,sum((o.i_orgprice-(i_discount+i_coup_discount))*o.quantity) as payable,l.voucher_slno,p.points,p.created_on 
													from king_orders o 
													join king_transactions t on t.transid=o.transid 
													left join pnh_voucher_activity_log l on l.transid=o.transid 
													join pnh_member_points_track p on p.user_id=o.userid
													where o.userid=? and o.status not in (3,4)   
													group by o.transid 
													order by sno desc",$u['user_id'])->result_array() as $o){?>
						 <tr>
							<td>
								<a class="link" href="<?=site_url("admin/trans/{$o['transid']}")?>"><?=$o['transid']?></a>
							</td>
							<td>
								<?=$o['amount']?>
							</td>
							<td>
								<?=$o['amount']-$o['payable']?>
							</td>
							<td>
								<?=$o['payable']?>
							</td>
							<td>
								<?=date("g:ia d/m/y",$o['time'])?>
							</td>
							<td>
								<?=$status[$o['status']]?>
							</td>
							<td>
								<?=$o['points']?> [ <?=date("d/m/y",$o['created_on'])?> ]
								
							</td>
							<?php /*?>
								<td><?php echo  $o['voucher_slno']?'<b>Prepaid</b>':'<b>Postpaid</b>'?></td>
							<?php /*/?>
						 </tr>
					  <?php }?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="fl_left mod_widget_row" style="">
		<!----------------------------------Basic information block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap"><img src="<?php echo base_url().'images/home2.png'?>">Basic Details
				</div>
			</div>
			<div class="mod_widget_content max_height_wrap">
				<ul class="content_head">
					<li>
						<span class="row_label">Name : </span>
						<span class="value"><?=$salutation[$u['salute']]?>. <?="{$u['first_name']} {$u['last_name']}"?></span> 
					</li>
					<li>
						 <span class="row_label">Mobile : </span>
						 <span class="value"><?=$u['mobile']?></span>
					</li>
					<li>
						<span class="row_label">DOB : </span>
						<span class="value"><?=format_date($u['dob']);?></span> 
					</li>
					<li>
						<span class="row_label">Gender : </span>
						<span class="value"><?=$gender[$u['gender']]?></span> 
					</li>
					<li>
						 <span class="row_label">Address : </span>
						 <span class="value"><?=nl2br($u['address'])?></span> 
					</li>
					<li>
						<span class="row_label">City : </span>
						<span class="value"><?=$u['city']?></span>
					</li>
					<li>
						 <span class="row_label">Pincode : </span>
						 <span class="value"><?=$u['pincode']?></span>
					</li>
					<li>
						 <span class="row_label">Email : </span>
						 <span class="value"><?=$u['email']?></span>
					</li>
				</ul>
			</div>
		</div>
		<!----------------------------------Offers Log block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					<img src="<?php echo base_url().'images/home2.png'?>">Offers Log
	 			</div>
			</div>
			<div class="mod_widget_content ">
				<?php 
	                $offers_q = $this->db->query("select * from pnh_member_offers a join pnh_member_info b on b.pnh_member_id=a.member_id where member_id=?  and a.offer_type != 0 ",$u['pnh_member_id']);
	                if($offers_q->num_rows())
               		 {
               	?>
				<table class="datagrid" width="100%">
					<tbody>
						<tr>
							<td><b>#</b></td>
							<td><b>Created on</b></td>
                            <td><b>TransId</b></td>
                            <td><b>Franchise</b></td>
                            <td><b>Type</b></td>
                            <td><b>Value</b></td>
                            <td><b>Status</b></td>
						</tr>
						<?php
	                        
	                        $offers = $offers_q->result_array();
							$arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
	                        $arr_offer_status = array(0=>"Not Processed",1=>"Processed");
	                        
	                        foreach($offers as $i=>$offer) {
	                        		$franch=$this->db->query("select a.franchise_id,b.franchise_name from pnh_member_offers a 
											join pnh_m_franchise_info b on b.franchise_id=a.franchise_id
											where a.franchise_id='".$offer['franchise_id']."'")->row_array(); 
						?>
                        <tr>
                            <td><?=++$i;?></td>
                            <td><?=format_datetime_ts($offer['created_on']);?></td>
                            <td><?=$offer['transid_ref'];?></td>
                            <td><a href="<?=site_url("/admin/pnh_franchise/".$franch['franchise_id']);?>" target="_blank"><?=$franch['franchise_name'];?></a></td>
                            <td><?=$arr_offer_type[$offer['offer_type']];?></td>
                            <td><?=formatInIndianStyle($offer['offer_value']);?></td>
                            <td><?=$arr_offer_status[$offer['process_status']];?></td>
                        </tr>
	                        <!--<div style="padding:4px 5px;border-bottom:1px solid #DDDDDD;">Rs. <?=formatInIndianStyle($offer['offer_value']);?> worth of <?=$arr_offer_type[$offer['offer_type']];?> given</div>-->
	                	<?php   
							}
	                    	} 
	                    ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="fl_left mod_widget_row" style="">
		<!----------------------------------Extra Details block--------------------------->
		<div class="mod_widget_small">
			<div class="mod_widget_title">
				<div class="heading_wrap"><img src="<?php echo base_url().'images/home2.png'?>">Extra Details
				</div>
			</div>
			<div class="mod_widget_content max_height_wrap">
				<ul class="content_head">
					<li>
						<span class="row_label">Marital Status : </span>
						<span class="value"><?=$marital[$u['marital_status']]?></span> 
					</li>
					<li>
						 <span class="row_label">Wed. Anniversary : </span>
						 <span class="value"><?=$u['anniversary']==0?"na":date("d/m/Y",strtotime($u['anniversary']))?></span>
					</li>
					<li>
						<span class="row_label">Spouse Name : </span>
						<span class="value"><?=$u['spouse_name']?></span> 
					</li>
					<li>
						<span class="row_label">Child's Name 1 : </span>
						<span class="value"><?=$u['child1_name']?></span> 
					</li>
					<li>
						<span class="row_label">Child's Name 2 : </span>
						<span class="value"><?=$u['child2_name']?></span> 
					</li>
				</ul>
			</div>
		</div>
		<!----------------------------------Voucher log block--------------------------->
		<div class="mod_widget_large">
			<div class="mod_widget_title">
				<div class="heading_wrap">
					<img src="<?php echo base_url().'images/home2.png'?>">Voucher Log
	 			</div>
			</div>
			<div class="mod_widget_content ">
				<div class="tab_view tab_view_inner">
					<ul>
						<li><a href="#activated_voucher">Activated Voucher</a></li>
						<li><a href="#fully_redeemed">Fully Redeemed</a></li>
						<li><a href="#partially_redeemed">Partially Redeemed</a></li>
						<li><a href="#not_redeemed">Not Redeemed</a></li>
					</ul>
					
					<div id="activated_voucher">
						<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status>=3 AND t.member_id=? AND l.status=1",$u['pnh_member_id']);
							if($ac_v -> num_rows()){?>
								<table class="datagrid">
									<tbody>
										<tr>
											<th><b>Slno</b></th>
											<th><b>Voucher slno</b></th>
											<th><b>Menu</b></th>
											<th><b>Franchise Name</b></th>
											<th><b>Voucher Value</b></th>
											<th><b>Activated On</b></th>
										</tr>
										<?php $i=1;
											foreach($ac_v->result_array() as $ac ){
										?>
										
										<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $ac['voucher_serial_no'];?></td>
											<td><?php echo $ac['menu'];?></td>
											<td><?php echo $ac['franchise_name'];?></td>
											<td><?php echo $ac['denomination'];?></td>
											<td><?php echo format_datetime($ac['activated_on']);?></td>
										</tr>
										</tbody>
										<?php $i++; }?>
								</table>
						<?php } else echo "<b style='text-align:center;font-size:11px;color:#FF0000'>No Data Found</b>";?>
					</div>
					
					<div id="fully_redeemed">
						<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status=4 AND l.status=1 AND t.member_id=?",$u['pnh_member_id']);
							if($ac_v -> num_rows()){?>
								<table class="datagrid">
									<tbody>
										<tr>
											<th><b>Slno</b></td>
											<th><b>Voucher Slno</b></th>
											<th><b>Menu</b></th>
											<th><b>Franchise Name</b></th>
											<th><b>Voucher Value</b></th>
											<th><b>Transaction Id</b></th>
											<th><b>Redeemed Value</b></th>
											<th><b>Voucher Balance</b></th>
											<th><b>Redeemed On</b></th>
										</tr>
										<?php $i=1;
											foreach($ac_v->result_array() as $ac ){
										?>
								
										<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $ac['voucher_serial_no'];?></td>
											<td><?php echo $ac['menu'];?></td>
											<td><?php echo $ac['franchise_name'];?></td>
											<td><?php echo $ac['denomination'];?></td>
											<td><a href="<?=site_url("admin/trans/".$ac['transid'])?>" class="link" target="_blank"><?php echo $ac['transid'];?></a></td>
											<td><?php echo $ac['debit'];?></td>
											<td><?php echo $ac['customer_value'];?></td>
											<td><?php echo format_datetime($ac['redeemed_on']);?></td>
										</tr>
									</tbody>
									<?php $i++;}?>
								</table>
						<?php }else echo "<b style='text-align:center;font-size:11px;color:#FF0000'>No Data Found</b>";?>
					</div>
					
					<div id="partially_redeemed">
						<?php $ac_v=$this->db->query("SELECT t.*,f.franchise_name,v.denomination,l.debit,l.transid,m.name AS menu FROM pnh_t_voucher_details t  JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  JOIN pnh_m_voucher v ON v.voucher_id=t.voucher_id  JOIN pnh_voucher_activity_log l ON l.voucher_slno=t.voucher_serial_no  JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=t.id  JOIN pnh_t_book_details c ON c.book_id=b.book_id  JOIN pnh_t_book_allotment e ON e.book_id=c.book_id  JOIN pnh_m_book_template q ON q.book_template_id=c.book_template_id  JOIN pnh_menu m ON m.id=q.menu_ids WHERE t.status=5 AND l.status=1 AND t.member_id=?",$u['pnh_member_id']);
							if($ac_v -> num_rows()){?>
								<table class="datagrid">
									<tbody>
										<tr>
											<th><b>Slno</b></th>
											<th><b>Voucher Slno</b></th>
											<th><b>Menu</b></th>
											<th><b>Franchise Name</b></th>
											<th><b>Voucher Value</b></th>
											<th><b>Transaction Id</b></th>
											<th><b>Redeemed Value</b></th>
											<th><b>Voucher Balance</b></th>
											<th><b>Redeemed On</b></th>
										</tr>													
										<?php $i=1;
											foreach($ac_v->result_array() as $ac ){
										?>
										<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $ac['voucher_serial_no'];?></td>
											<td><?php echo $ac['menu'];?></td>
											<td><?php echo $ac['franchise_name'];?></td>
											<td><?php echo $ac['denomination'];?></td>
											<td><a href="<?=site_url("admin/trans/".$ac['transid'])?>" class="link" target="_blank"><?php echo $ac['transid'];?></a></td>
											<td><?php echo $ac['debit'];?></td>
											<td><?php echo $ac['customer_value'];?></td>
											<td><?php echo format_datetime($ac['redeemed_on']);?></td>
										</tr>
									</tbody>
									<?php $i++;}?>
								</table>
							<?php }else echo "<b style='text-align:center;font-size:11px;color:#FF0000'>No Data Found</b>";?>
					</div>
					
					<div id="not_redeemed">
						<?php $ac_v=$this->db->query("select t.*,f.franchise_name,v.denomination from pnh_t_voucher_details t join pnh_m_franchise_info f on f.franchise_id=t.franchise_id join pnh_m_voucher v on v.voucher_id=t.voucher_id where status=3 and t.member_id=?",$u['pnh_member_id']);
							if($ac_v -> num_rows()){?>
								<table class="datagrid">
									<tbody>
										<tr>
											<td><b>Slno</b></td>
											<td><b>Voucher Slno</b></td>
											<td><b>Franchise Name</b></td>
											<td><b>Voucher Value</b></td>
											<td><b>Activated On</b></td>
										</tr>
										<?php $i=1;
										foreach($ac_v->result_array() as $ac ){
										?>
										<tr>
											<td><?php echo $i;?></td>
											<td><?php echo $ac['voucher_serial_no'];?></td>
											<td><?php echo $ac['franchise_name'];?></td>
											<td><?php echo $ac['denomination'];?></td>
											<td><?php echo format_datetime($ac['activated_on']);?></td>
										</tr>
									</tbody>
								<?php $i++; }?>
								</table>
						<?php }else echo "<b style='text-align:center;font-size:11px;color:#FF0000'>No Data Found</b>";?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.mod_widget_small .mod_widget_content li {
	margin: 0px;
	padding: 6px;
	clear: both;
	overflow: hidden;
	background: #FFF;
	margin: 2px 0px;
	height: auto;
}
.content_head span.row_label {
float: left;
font-size: 12px;
text-align: right;
width: 26%;
color: #444;
display: block;
text-align: left;
}
.content_head span.value {
color: #444;
font-size: 12px;
font-weight: bold;
display: block;
float: left;
width: 70%;
}
.mod_widget_content,.mod_widget_title{border-color: #f1f1f1;border:none;}
.mod_widget_content{height: 220px;background: #FaFaFa}
.mod_widget_content li{border:none;border-top:1px dotted #f7f7f7;background: #fcfcfc;}
.mod_widget_title{background: #D2B48C;}
.mod_widget_title div.heading_wrap{color: #fff;}
</style>

<script>
$('.tab_view').tabs();
</script>
<?php
