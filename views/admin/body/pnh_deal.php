<?php

	$item_id = $this->uri->segment(3);
	$deal_pub_det = @$this->db->query("select a.created_on,b.username,is_publish,remarks
			from t_deal_publish_changelog a
			left join king_admin b on a.created_by = b.id
			where item_id = ?
			order by a.id desc limit 1;",$deal['id'])->row_array();
?>

<style>
.jqplot-highlighter-tooltip, .jqplot-canvasOverlay-tooltip
{
	font-size: 13px !important;
    margin-left:15px;
    background:none repeat scroll 0 0 #FFFFFF !important; 
}
.deal_sales_left_wrap b
{
	float: left;
    margin: 0 6px;
    text-align: right;
    width: 224px;
}
.deal_sales_left_wrap span
{
	background: none repeat scroll 0 0 #EEE9D3;
    float: left;
    margin: 1px 0;
    padding: 7px 0;
    width: 100%;
}
#deal_stat
{
	margin-left:7px;clear: both;width:800px;
}
#deal_stat_terr
{
	width:420px;
	float: left;
}
.franch_list
{
    float: right;
    margin: 43px 9px 0 0;
    width: 363px;
}
ul.tabs 
{
	margin: 0;
	padding: 0;
	
	list-style: none;
	width: 100%;
	border:none;
	margin-top:20px;
}

ul.tabs li 
{
	float: left;
	margin: 0;
	cursor: pointer;
	padding: 3px 20px;
	line-height: 25px;
	border: 1px solid #f8f8f8;
	font-weight: bold;
	background: #EEEEEE;
	position: relative;
	border-bottom:0px;
	border-radius:5px 5px 0px 0px;
	margin-right:3px;
}
ul.tabs li:hover 
{
	background: #CCCCCC;
}	
ul.tabs li.active
{
	background: #FAFAF5;
	border: 1px solid #FAFAF5;
}
.tab_container 
{
	
	border-top: none;
	clear: both;
	float: left; 
	width: 100%;
	background: #FAFAF5;
}
.tabcontent 
{
    padding: 10px;
	display: none;
}
.tabs
{
	border-radius : 0px !important;
}
.link
{
	color:blue;	
}
.badge 
{
  display: block;
  position: absolute;
  top: -12px;
  right: 3px;
  line-height: 16px;
  height: 16px;
  padding: 0 5px;
  font-family: Arial, sans-serif;
  color: white;
  text-shadow: 0 1px rgba(0, 0, 0, 0.25);
  border: 1px solid;
  border-radius: 10px;
  -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.3), 0 1px 1px rgba(0, 0, 0, 0.08);
  box-shadow: inset 0 1px rgba(255, 255, 255, 0.3), 0 1px 1px rgba(0, 0, 0, 0.08);
}
.badge 
{
  background: #67c1ef;
  border-color: #30aae9;
  background-image: -webkit-linear-gradient(top, #acddf6, #67c1ef);
  background-image: -moz-linear-gradient(top, #acddf6, #67c1ef);
  background-image: -o-linear-gradient(top, #acddf6, #67c1ef);
  background-image: linear-gradient(to bottom, #acddf6, #67c1ef);
}
.badge.green 
{
  background: #77cc51;
  border-color: #59ad33;
  background-image: -webkit-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: -moz-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: -o-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: linear-gradient(to bottom, #a5dd8c, #77cc51);
}
.sbutton span {
    color: #FFFFFF;

}
.sbutton {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    -moz-user-select: none;
    background: -moz-linear-gradient(center top , rgba(255, 255, 255, 0.1) 0%, rgba(0, 0, 0, 0.1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.4);
    border-image: none;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    line-height: 11px;
    margin-right:7px;
    outline: medium none;
    overflow: visible;
    position: relative;
    white-space: nowrap;
}

.sbutton.green {
    background-color: #91BD09;
}
.sbutton, .sbutton span {
    border-radius: 3px;
    display: inline-block;
}
.state_filter
{
	 margin: 9px 10px 0;
    text-align: right;
}
.alert_wrap
{
	 color: #FF0000;
    font-size: 12px;
    font-weight: bold;
    margin-top: 50px;
    text-align: center;
}
.jqplot-highlighter-tooltip
{
	
}
/* styling for the image wrapper  */
#image_wrap {
    /* dimensions */
     background-color: #EFEFEF;
    border: 2px solid #FFFFFF;
    outline: 1px solid #DDDDDD;
    padding: 15px 0;
    text-align: center;
    margin-bottom:1px;
    height:400px;
}
.scrollable {
	background: none repeat scroll 0 0 #FFFFFF;
    height: 86px;
    overflow: hidden;
    position: relative;
}
.scrollable .items {
  /* this cannot be too large */
  	margin-top: 11px;
	position: absolute;
	width: 20000em;
}
.items div {
  float:left;
}
/*
.badge.yellow {
  background: #faba3e;
  border-color: #f4a306;
  background-image: -webkit-linear-gradient(top, #fcd589, #faba3e);
  background-image: -moz-linear-gradient(top, #fcd589, #faba3e);
  background-image: -o-linear-gradient(top, #fcd589, #faba3e);
  background-image: linear-gradient(to bottom, #fcd589, #faba3e);
}
.badge.red {
  background: #fa623f;
  border-color: #fa5a35;
  background-image: -webkit-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: -moz-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: -o-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: linear-gradient(to bottom, #fc9f8a, #fa623f);
}
*/
.prd_active_wrap
{
	float: right;
    text-align: right;
    width: 50%;
}
.red {
    background: none repeat scroll 0 0 #FF6655;
    color: #FFFFFF;
    font-weight: bold;
    padding: 3px;
}
.green {
    background: none repeat scroll 0 0 #619C5C;
    color: #FFFFFF;
    font-weight: bold;
    padding: 3px;
}
</style>


<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/css/analytic.css" />
<link class="include" rel="stylesheet" type="text/css" href="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.css" />
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.highlighter.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.pointLabels.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>


<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.cursor.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.barRenderer.min.js"></script>
<script class="include"  type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.pieRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jq_plot/plugins/jqplot.categoryAxisRenderer.min.js"></script>


<?php $linked_prod = $this->db->query("select gl.*,g.group_name from m_product_group_deal_link gl join products_group g on g.group_id=gl.group_id where gl.itemid=?",$deal['id']);
	
	  $pack_qty = $this->db->query("select * from m_product_deal_link where qty>1 and itemid = ?",$deal['id']);
?>


<div class="container page_wrap" style="padding:10px">
<div style="width:75%;">	
	<?=$deal['menu_name']?>  &raquo; 
	<a href="<?=site_url("admin/viewcat/{$deal['catid']}")?>"><?=$deal['category']?></a>  &raquo; 
	<a href="<?=site_url("admin/viewbrand/{$deal['brandid']}")?>"><?=$deal['brand']?></a>
</div>

<div class="clear" >
	<h3 class="page_title">
		<br>
		<?=$deal['name']?> <a style="font-size: 9px;" href="<?=site_url("admin/pnh_editdeal/{$deal['id']}")?>">Edit</a> 
	</h3>
	<span class="stock_wrap" style="width:60%;">
		<div>
		<?php $d=$deal;  ?>
		
		<div class="prd_active_wrap">
			<?php if($deal_stk != '0'){ ?>
				<span class="notifications green" style="margin-right: 4px">Stock : <span style="font-size: 14px"><?=$deal_stk ?></span> </span>
			<?php }else{ ?>	
				<span class="notifications red">No Stock</span>
			<?php } ?>
				
			<span style="font-size:19px"> | </span> 
			
			<?php if($deal['live']){ ?>
				<span class="notifications green">Sourceble</span>
			<?php }else{ ?>	
				<span class="notifications red">Not Sourceble</span>
			<?php } ?>
		</div>

		</div>
	</span>
</div>
<div class="body_center_content">
	<div class="">
		<div class="">
			<ul class="tabs" style="clear:both"> 
		        <li rel="deal_det" style="margin-left:10px;" class="active" >Deal Details</li>
		        <li rel="prod_linked">Product Linked</li>
		        <li rel="prc_chanlog">Price Changelog</li>
		        <li rel="pub_chanlog">Publish Changelog</li>
		        <li rel="recent">Recent Orders</li>
		        <li rel="spec_marg_his">Special Margin History</li>
		        <li rel="analytics" class="sales_anl">Sales Analytics</li>
		    </ul>
		    
	     	<div class="tab_container"> 
	       		<!-- #Deal Details Block start -->
	       		<div id="deal_det" class="tabcontent">
	       			<table width="100%">
	       				<tr>
	       					<td width="50%">
				       			<table cellpadding=3 class="datagrid" width="100%">
									<tr>
										<td>PNH PID :</td><Td><?=$deal['pnh_id']?></Td>
									</tr>
									<tr><td>Deal Name :</td><Td style="font-weight:bold;"><?=$deal['name']?></Td></tr>
									<tr><td>Print Name :</td><Td style="font-weight:bold;"><?=isset($deal['print_name']) ? $deal['print_name'] : $deal['name']?></Td></tr>
									<tr>
									<td>Status :</td>
										<td><?=$d['publish']==1?"Published":"Not Published"?>
											<?php 
												if($this->erpm->auth(UPDATE_PRODUCT_DEAL_ROLE,true))
												{
											?> 
													<a class="button button-tiny button-rounded button-caution"  href="<?=site_url("admin/pnh_pub_deal/{$d['id']}/{$d['publish']}")?>">change</a>
											<?php 
												}
												
												echo '<div style="font-size:11px;">Updated by : '.$deal_pub_det['username'].' | On : '.format_datetime($deal_pub_det['created_on']).' </div>';
											?>
										</td>
									</tr>
									
									<tr><td>Tagline :</td><td><?=$deal['tagline']?></td></tr>
									<tr><td>MRP :</td><td>Rs <?=$deal['orgprice']?></td></tr>
									<tr><td><b>Offer price/Dealer Price </b>:</td><td>Rs <?=$deal['price']?></td></tr>
									<tr><td><b>Member Price </b>:</td><td>Rs <?=$deal['member_price']?></td></tr>
									<tr><td><b>Member Price Max order qty </b>:</td><td><?=$deal['mp_mem_max_qty']?></td></tr>
									<tr><td>Store price :</td><td>Rs <?=$deal['store_price']?></td></tr>
									<tr><td>NYP price :</td><td>Rs <?=$deal['nyp_price']?></td></tr>
									
									<tr><td>Bill on Orderprice :</td><td><?=$deal['billon_orderprice']?'Yes':'No'?></td></tr>
									<tr><Td>Gender Attribute :</Td><td><?=$deal['gender_attr']?></td></tr>
									<tr><Td>Max Allowed Qty <br>(for franchise per day):</Td><td><?=$deal['max_allowed_qty']?></td></tr>
									<tr><td><b>Has Insurance</b> :</td><td><?=$deal['has_insurance']==1?'Yes':'No';?></td></tr>
									<tr><td>ShipsIn :</td><Td style="font-weight:bold;"><?=$deal['shipsin']?></Td></tr>
									<tr><td>Brand :</td><Td style="font-weight:bold;"><?=$deal['brand']?></Td></tr>
									<tr><td>Category :</td><Td style="font-weight:bold;"><?=$deal['category']?></Td></tr>
									<tr>
									<td>Description :</td>
									<td style="font-weight:bold;">
										<div id="description" >
											<p class="show"><?=$deal['description']?></p>
										</div>
									</td>
									</tr>
									<tr><td>Created by :</td><Td style="font-weight:bold;"><?=$deal['created_by']?></Td></tr>
									<tr><td>Modified by :</td><Td style="font-weight:bold;"><?=$deal['mod_name']?></Td></tr>
									
									<?php $d=$deal;?>
									
									<tr>
									<?php $sch_status=$this->db->query("select 1 from pnh_superscheme_deals where itemid=? and is_active=0 and ? between valid_from and valid_to order by created_on desc  limit 1 ",array($deal['id'],time()))->row();?>
									
									<td width="30%">Pnh Super Scheme:</td><td><?=$sch_status?"Disabled":"Enabled"?>
									</td>
									</tr>
								</table>
							</td>
							<td width="50%">
								<?php $img_res = $this->db->query("select id from king_resources where dealid=?",$deal['dealid'])->result_array(); 
									if($img_res)
									{?>
										<div id="image_wrap">
											<img src="<?=IMAGES_URL?>items/<?=$deal['pic']?>.jpg" style="margin-left:20px;" >
											<!---->
										</div>
										<div style="margin:0 auto; width: 634px; height:120px;">
										<!-- "previous page" action -->
										<a class="prev browse left"></a>
										 
										<!-- root element for scrollable -->
										<div class="scrollable" id="scrollable">
										 
										  <!-- root element for the items -->
										  <div class="items">
										 
										    
										    <img src="<?=IMAGES_URL?>items/<?=$deal['pic']?>.jpg" style="margin-left:20px;" width="50" height="50">
										    <?php foreach($img_res as $i)
												{ ?>
													<div>
														<img src="<?=IMAGES_URL?>items/<?=$i['id']?>.jpg" style="margin-left:20px;" width="50" height="50">
													</div>
												<?php } ?>
										</div>
										 
										<!-- "next page" action -->
										<a class="next browse right"></a>
										</div>
									<?php }
									else
									{ ?>
										<div>
											<img src="<?=IMAGES_URL?>items/<?=$deal['pic']?>.jpg" style="margin-left:20px;">
											
										</div>
									<?php }	
								?>
								
								<a class="upload_pic_wrapper" href="<?=site_url("admin/pnh_deal_extra_images/{$deal['id']}")?>"><button type="button" class="btn btn-info btn-mini">Click Here</button></a>
							</td>
						</tr>
					</table>
				       			
	       		</div>	
	       		<!-- #Deal Details Block End -->
       		
       		
	       		<!-- #Price Changelog Block start -->
	       		<div id="prc_chanlog" class="tabcontent">
	       			<?php
						$prc_chan = $this->erpm->get_price_change_log($deal['id']); 
						
						if($prc_chan)
						{	
					?>
					<table class="datagrid smallheader noprint" width="100%">
	       				<thead>
							<tr>
								<th>Sno</th><th>MRP <small>(Rs)</small></th><th>Price <small>(Rs)</small></th> <th>Member Price <small>(Rs)</small></th><th>Reference</th><th>Updated By</th><th>Date</th>
							</tr>
						</thead>
						
						<tbody>
							<?php $i=1; foreach($prc_chan as $pc){?>
							<tr>
							<td><?=$i++?></td>
							<td><?=$pc['old_mrp']?>  => <b><?=$pc['new_mrp']?></b></td>
							<td><?=$pc['old_price']?> => <b><?=$pc['new_price']?></b></td>
							<td><?=$pc['old_member_price']?> => Rs <b><?=$pc['new_member_price']?></b></td>
							
							<td>
							<?php if($pc['reference_grn']==0) echo "MANUAL";else{?>
							<a href="<?=site_url("admin/viewgrn/{$pc['reference_grn']}")?>"><?=$pc['reference_grn']?></a>
							<?php }?>
							</td>
							<td><?=  ucfirst($pc['logged_by']);?></td>
							<td><?=format_datetime_ts($pc['created_on'])?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
				<?php } else { ?>
					<div style="margin:10px;font-weight:bold"> No Details Found </div>
				<?php } ?>
	       		</div>	
	       		<!-- #Price Changelog Block End -->
       		
	       		<!-- #Price Changelog Block start -->
	       		<div id="pub_chanlog" class="tabcontent">
	       			<?php
						$pub_chan = $this->db->query("select a.created_on,b.username,is_publish,remarks
															from t_deal_publish_changelog a 
															left join king_admin b on a.created_by = b.id 
															where item_id = ?
															order by a.id desc limit 10;",$deal['id']); 
						
						
					?>
							<table class="datagrid smallheader noprint" >
			       				<thead>
									<tr>
										<th>Slno</th>
										<th>Status</th>
										<th>Updated By</th>
										<th>Updated On</th>
									</tr>
								</thead>
								<tbody>
									
									<?php 
										if($pub_chan->num_rows())
										{	
											foreach($pub_chan->result_array() as $k=>$pub_c){?>
											<tr>
												<td><?=($k+1);?></td>
												<td><?=($pub_c['is_publish']?'Published':'Unpublished');?></td>
												<td><?=ucfirst($pub_c['username']);?></td>
												<td><?=format_datetime($pub_c['created_on'])?></td>
											</tr>
											<?php }
										}else
										{
											echo '<tr><td colspan="3" align="center">No Data Found</td></tr>';
										}	
									?>
											
								</tbody>
							</table>
					
	       		</div>	
	       		<!-- #Price Changelog Block End -->
       		
       		
	       		<!-- #Special Margin History Block start -->
	       		<div id="spec_marg_his" class="tabcontent">
	       			<div id="margin_his" style="clear: both;">
						<ul>
							<li><a href="#spec_margin">Special Margin</a></li>
							<li><a href="#sup_scheme">Super Scheme</a></li>
							<li><a href="#mem_scheme">Member Scheme</a></li>
						</ul>
						
						<div id="spec_margin">
							<table width="100%">
								<tr>
									<td width="45%">
										<div>
								  			<h4>Make it as Special Margin Deal<span style="color:#777;padding:1px 4px;background:#eee;">(Calculate Margin on Offer Price)</span></h4>
											<form id="frm_specialmargin" action="<?=site_url("admin/pnh_special_margin_deal/{$deal['id']}")?>" method="post">
												<table class="datagrid noprint" width="88%">
												<tr><Td>Special Margin : </Td><td><input type="text" i_mrp="<?php echo $deal['orgprice']; ?>" i_price="<?php echo $deal['price']; ?>"  name="special_margin" class="inp" size=6> in <label><input type="radio" name="type" value=0 checked="checked">%</label> <label><input type="radio" name="type" value=1>Rs</label></td></tr>
												<tr><td>From : </td><td><input type="text" class="inp" name="from" id="sm_from"></td></tr>
												<tr><td>To : </td><td><input type="text" class="inp" name="to" id="sm_to"></td></tr>
												
												<tr><td></td><Td><input type="submit" value="Submit" style="float:right"></Td></tr>
												</table>
											</form>
								  		</div>
								  	</td>
								  	<td>
										<div>
											<h4>Special Margin history</h4>
											<table class="datagrid smallheader" width="88%">
												<thead>
													<tr><th style="text-align: center;">MRP</th><th style="text-align: center;">Price</th><th style="text-align: center;">Margin %</th><th style="text-align: center;">Margin Rs</th><th style="text-align: center;">Landing Price</th><th>From</th><th>To</th><th>Assigned on</th><th>Assigned by</th><th></th></tr>
												</thead>
												<?php $spec_margin_his = $this->db->query("select s.*,a.name as admin from pnh_special_margin_deals s join king_admin a on a.id=s.created_by where s.itemid=? order by id desc limit 10",$deal['id']);?>
												<tbody>
													<?php
														if($spec_margin_his->num_rows())
														{
															foreach($spec_margin_his->result_array() as $s)
															{
													?>
															<tr>
																<td><?php echo (!is_null($s['i_mrp'])?$s['i_mrp']:"-na-");?></td>
																<td><?php echo (!is_null($s['i_price'])?$s['i_price']:"-na-");?></td>
																<td style="color: maroon;font-weight: bold"><?=$s['special_margin']?>%</td><td style="color: maroon;font-weight: bold"><?php echo (((!is_null($s['i_price'])?$s['i_price']:$deal["price"])*$s['special_margin']/100))?></td><td style="color: green;font-weight: bold;"><?php echo ($deal['price']-($deal['price']*$s['special_margin']/100))?> </td><td><b><?=format_date_ts($s['from'])?></b></td><td><b><?=format_date_ts($s['to'])?></b></td><td><?=format_date_ts($s['created_on'])?></td><td><?=$s['admin']?></td>
																<td>
													<?php 
																if(format_date_ts($s['to'])>=date("d/m/Y") && $this->erpm->auth(SPECIAL_MARGIN_UPDATE,true))
																{
													?>				<?php if($s['is_active']==1){?><a href="<?=site_url("admin/pnh_disable_special_margin/{$s['id']}")?>" class="danger_link">Disable</a><?php }else{?><?php echo "<b>Disabled</b>"; }?>
													<?php 
																}
													?>
																</td>
													<?php 
															}
													?>
															</tr>
													<?php
														}
														else
														{
													?>
														<tr><td style="margin:10px;font-weight: bold">No Data</td></tr>
													<?php
														} 
													?>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div id="sup_scheme">
							<table width="100%">
								<tr>
									<td width="45%">
										<div>
								  			<h4>Enable/Disable from Super Scheme</h4>
											<form action="<?=site_url("admin/pnh_superscheme_deal/{$deal['id']}")?>" method="post">
												<table class="datagrid noprint" width="88%"> 
													<tr><td>Super Scheme Status :</td>
													<td><select name="super_schstatus">
													<option value="0">Disable</option>
													<option value="1">Enable</option>
													</select></td></tr>
													<tr><td>From : </td><td><input type="text" class="inp" name="from" id="supersch_from"></td></tr>
													<tr><td>To : </td><td><input type="text" class="inp" name="to" id="supersch_to"></td></tr>
													<tr><td>Reason: </td><td><textarea class="inp" name="reason" style="width: 144px; height: 44px;"></textarea></td></tr>
													<tr><td></td><Td><input type="submit" value="Submit" style="float:right"></Td></tr>
												</table>
											</form>
								  		</div>
								  	</td>
								  	<td>
										<div>
											<h4>Super Scheme Log</h4>
											<table class="datagrid smallheader" width="88%">
												<thead>
													<tr>
														<Th style="text-align: center;">Status</Th>
														<Th style="text-align: center;">Valid from</Th>
														<Th style="text-align: center;">Valid from</Th>
														<th>Assigned on</th><th>Assigned by</th>
													</tr>
												</thead>
												<?php $sup_scheme_log = $this->db->query("select s.*,a.name as admin from pnh_superscheme_deals s join king_admin a on a.id=s.created_by where s.itemid=? order by id desc limit 10",$deal['id']);?>
												<tbodY>
													<?php 
													if($sup_scheme_log->num_rows())
													{
														foreach($sup_scheme_log->result_array() as $s){?>
															<tr><td style="color: maroon;font-weight: bold"><?=$s['is_active']==0?'Disabled':'Enabled'?></td><td><b><?=date("d/m/y",$s['valid_from'])?></b></td><td><b><?=date("d/m/y",$s['valid_to'])?></b></td><td><?=date("g:ia d/m/y",$s['created_on'])?></td><td><?=$s['admin']?></td>
														<?php if(format_date_ts($s['to'])>=date("d/m/Y")&& $this->erpm->auth(SPECIAL_MARGIN_UPDATE,true)){?><td><?php if($s['is_active']==1){?><a href="<?=site_url("admin/pnh_disable_special_margin/{$s['id']}")?>" class="danger_link">Disable</a><?php }else{?><?php echo "<b>Disabled</b>"; }?><?php }?></td></tr>
														<?php }}else{ ?>
															<tr><td style="margin:10px;font-weight: bold">No Data</td></tr>
														<?php } ?>
												</tbodY>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</div>
				
						<div id="mem_scheme">
							<table width="100%">
								<tr>
									<?php if($deal['menuid']==112){?>
										<td width="45%">
											<div>
									  			<h4>Enable/Disable from Member Scheme</h4>
												<form action="<?=site_url("admin/pnh_memberscheme_deal/{$deal['id']}")?>" method="post">
													<table class="datagrid noprint" width="88%">
														<tr><td>Member Scheme Status :</td>
														<td><select name="mbr_schstatus">
														<option value="0">Disable</option>
														<option value="1">Enable</option>
														</select></td></tr>
														<tr><td>From : </td><td><input type="text" class="inp" name="from" id="mbrsch_from"></td></tr>
														<tr><td>To : </td><td><input type="text" class="inp" name="to" id="mbrsch_to"></td></tr>
														<!--  <tr><td>Reason: </td><td><textarea class="inp" name="reason" style="width: 144px; height: 44px;"></textarea></td></tr>-->
														<tr><td></td><Td><input type="submit" value="Submit" style="float:right"></Td></tr>
													</table>
												</form>
									  		</div>
									  	</td>
									  	<td>
											<div>
												<h4>Member Scheme Log</h4>
												<table class="datagrid smallheader" width="88%">
													<thead>
														<tr>
															<Th style="text-align: center;">Status</Th>
															<Th style="text-align: center;">Valid from</Th>
															<Th style="text-align: center;">Valid from</Th>
															<th>Assigned on</th><th>Assigned by</th>
														</tr>
													</thead>
													<?php $member_scheme_log = $this->db->query("select s.*,a.name as admin from pnh_membersch_deals s join king_admin a on a.id=s.created_by where s.itemid=? order by id desc limit 10",$deal['id']);?>
													<tbodY>
														<?php 
														if($member_scheme_log->num_rows())
														{
														foreach($member_scheme_log->result_array() as $s){?>
														<tr><td style="color: maroon;font-weight: bold"><?=$s['is_active']==0?'Disabled':'Enabled'?></td><td><b><?=date("d/m/y",$s['valid_from'])?></b></td><td><b><?=date("d/m/y",$s['valid_to'])?></b></td><td><?=date("g:ia d/m/y",$s['created_on'])?></td><td><?=$s['admin']?></td>
														<?php if(format_date_ts($s['to'])>=date("d/m/Y")&& $this->erpm->auth(SPECIAL_MARGIN_UPDATE,true)){?><td><?php if($s['is_active']==1){?><a href="<?=site_url("admin/pnh_disable_special_margin/{$s['id']}")?>" class="danger_link">Disable</a><?php }else{?><?php echo "<b>Disabled</b>"; }?><?php }?></td></tr>
														<?php } }else{ ?>
															<tr><td style="margin:10px;font-weight: bold">No Data</td></tr>
														<?php } ?>
													</tbodY>
												</table>
											</div>
										</td>
									<?php }
									else {?>
										<td style="margin:10px;font-weight: bold">Not Available</td>
									<?php } ?>
								</tr>
							</table>
						</div>
					</div>
	       		</div>	
	       		<!-- #Special Margin History Block End -->
       		
       		
	       		<!-- #Product Linked Block start -->
	       		<div id="prod_linked" class="tabcontent">
	       			
	       			
	       			<table width="100%">
	       				<tr>
	       					<td width="70%">
	       						<?php if($linked_prod->num_rows()){ ?>
		       						<h4>Linked Product Groups</h4>
									<table class="datagrid smallheader noprint" width="88%">
										<thead>
											<tr>
												<th>Sno</th><th>ID</th><th>Group Name</th><th>Qty</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($linked_prod->result_array() as $i=>$p){?>
											<tr><td><?=++$i?></td><td><?=$p['group_id']?></td><td><a href="<?=site_url("admin/product_group/{$p['group_id']}")?>"><?=$p['group_name']?></a></td><td><?=$p['qty']?></td></tr>
											<?php }?>
										</tbody>
									</table>
								<?php }
								if($prods){ ?>
									<h4>Linked Products</h4>
									<table class="datagrid smallheader noprint"  width="98%">
										<thead>
											<tr><th>Sno</th><th>ID</th><th>SKU CODE</th><th>Product Name</th><th>MRP</th><th>Qty</th><th>Sourceable</th><th>Vendor Stock</th></tr>
										</thead>
										
										<tbody>
											<?php foreach($prods as $i=>$p){
												$p_det = $this->db->query("select sku_code,is_sourceable,mrp from m_product_info where product_id = ? ",$p['product_id'])->row_array();
												
												$p['is_sourceable'] = $p_det['is_sourceable'];
												$p['sku_code'] = $p_det['sku_code'];
												$p['mrp'] = $p_det['mrp'];
											?>
											<tr>
												<td><?=++$i?></td>
												<td><?=$p['product_id']?></td>
												<td><?=$p['sku_code']?></td>
												<td><a href="<?=site_url("admin/product/{$p['product_id']}")?>"><?=$p['product_name']?></a></td>
												<td><?=$p['mrp']?></td>
												<td><?=$p['qty']?></td>
												<td><?=($p['is_sourceable']?'Yes':'No')?></td>
												<td>
													<?php
														$ven_stock_qty_det_res = @$this->db->query('select ven_stock_qty from m_vendor_product_link where product_id = ? ',$p['product_id']);
														if(!$ven_stock_qty_det_res->num_rows())
															echo '-na-';
														else
															echo $ven_stock_qty_det_res->row()->ven_stock_qty;
													?>
												</td>
											</tr>
											<?php }?>
										</tbody>
									</table>
								<?php } ?>
	       					</td>
	       					<td width="30%">
	       						<h4>Product Link Update Log</h4>
	       						<?php 
	       						if($pnh_Deal_upd_log)
									{
	       						 ?>
					       		<table class="datagrid smallheader noprint" width="90%">
									<thead>
										<tr>
											<th>#</th>
											<th>Product Name</th>
											<th>Action</th>
											<th>Updated On</th>
											<th>Updated by</th>
										</tr>
									</thead>
									<tbody>
									<?php 
										foreach($pnh_Deal_upd_log as $i=>$log_det)
										{
									?>
										<tr>
											<td><?php echo $i+1;?></td>
											<td><?php echo $log_det['product_name'];?></td>
											<td>
												<?php echo ($log_det['is_updated']==1)?'Removed':'Added';?>
											</td>
											<td><?php echo date('d/m/Y',strtotime($log_det['perform_on']));?></td>
											<td><?php echo $log_det['username'];?></td>
										</tr>
										
									<?php }?>
									</tbody>
								</table>
								<?php } else{?>
									<table class="datagrid smallheader noprint" width="88%">
										<tbody>
											<tr><td width="100%" style="margin:10px;font-weight: bold;">No Data</td></tr>
										</tbody>
									</table><?php } ?>
	       					</td>
	       				</tr>
	       			</table>
	       		</div>	
	       		<!-- #Product Linked Block End -->
       		
       		
	       		<!-- #Recent Orders Block start -->
	       		<div id="recent" class="tabcontent">
					<div id="recent_orders_list"></div>
	       		</div>	
	       		<!-- #Recent Orders Block End -->
       		
       		
	       		<!-- #Product Link Update Log Block start -->
	       		<div id="prod_update_log" class="tabcontent">
	       			
	       		</div>	
	       		<!-- #Product Link Update Log Block End -->
	       		
       		<!-- #Sale anaylics Block start -->
       		<div id="analytics" class="tabcontent">
       			<table width="100%">
					<tr>
						<td width="100%">
							<h4>Sales Analytics</h4>
							<div style="width:100%;float:left;border:1px solid #ccc;">
								<table>
									<tr>
										<td width="37%">
											<div class="deal_sales_left_wrap">
												<span><b>Total Amount of Sales :</b> <?=round($this->db->query("select sum(i_orgprice*quantity) as amt from king_orders  where itemid = '".$item_id."' and status != 3 ")->row()->amt);?></span>
												<span><b>Till Date Quantity Sold :</b> <?=round($this->db->query("select sum(quantity) as qty from king_orders  where itemid = '".$item_id."' and status != 3")->row()->qty);?></span>
												<span><b>Min Selling Price :</b> <?=round($this->db->query("select min(i_orgprice-(i_discount+i_coup_discount)) as min_disc_price 
																					from king_orders where itemid = '".$item_id."' and status != 3 ")->row()->min_disc_price) ?></span>
												<span><b>Max Selling Price :</b> <?=round($this->db->query("select max(i_orgprice-(i_discount+i_coup_discount)) as max_disc_price 
																				from king_orders where itemid = '".$item_id."' and status != 3 ")->row()->max_disc_price) ?></span>
											</div>
									
											<div id="tab_deal_sales" style="clear: both;">
												<ul>
													<li><a href="#most_sold">Most Sold</a></li>
													<li><a href="#last_sold">Latest Sold</a></li>
													<li><a href="#all_sales">All Franchises<span class="badge green"><?=count($ttl_fran)?></span></a></li>
												</ul>
												
												<div id="most_sold">
													<table class="datagrid" width="100%">
														<tbody>
															<tr>
																<th>Name</th><th>Qty</th><th>Transaction Id</th>
															</tr>
															
															<?php
															foreach($most_sold_fran as $most)
															{
															?>
															<tr>
																<td width="250px"><a target="_blank" href="<?=site_url('admin/pnh_franchise/'.$most['franchise_id'])?>"><?=$most['franchise_name']?></a></td>
																<td><?=$most['sold_qty']?></td>
																<td><a target="_blank" style="color:blue" href="<?=site_url("admin/trans/{$most['transid']}")?>" class="link"><?=$most['transid']?></a></td>
															</tr>
															<?php 
															}								
															?>
														</tbody>
													</table>
												</div>
												<div id="last_sold">
													<table class="datagrid" width="100%">
														<tbody>
															<tr>
																<th>Name</th><th>Qty</th><th>Transaction Id</th><th>Date</th>
															</tr>
															<?php
															foreach($latest_fran as $l)
															{
															?>
															<tr>
																<td width="250px"><a target="_blank" href="<?=site_url('admin/pnh_franchise/'.$l['franchise_id'])?>"><?=$l['franchise_name']?></a></td>
																<td><?=$l['sold_qty']?></td>
																<td><a target="_blank" style="color:blue" href="<?=site_url("admin/trans/{$l['transid']}")?>"><?=$l['transid']?></a></td>
																<td><?=date($l['date'])?></td>
															</tr>
															<?php 
															}								
															?>
														</tbody>
													</table>
												</div>
										
												<div id="all_sales">
													<table class="datagrid" width="100%">
														<tbody>
															<tr>
																<th>Name</th><th>Qty</th><th>Value</th>
															</tr>
															
															<?php
															foreach($ttl_fran as $f)
															{
															?>
															<tr>
																<td width="250px"><a target="_blank" href="<?=site_url('admin/pnh_franchise/'.$f['franchise_id'])?>"><?=$f['franchise_name']?></a></td>
																<td><?=$f['sold_qty']?></td>
																<td><?=round($f['ttl_sales_value'])?></td>
															</tr>
															<?php 
															}								
															?>
														</tbody>
													</table>
												</div>
											</div>
										</td>
										
										<td>
											<div>
												<form id="grid_list_frm_to" method="post">
					                                    <div style="margin:2.5px 0px;font-size:12px;text-align: right">
					                                    	<b>From</b> : <input type="text" style="width: 90px;" id="date_from"
					                                                name="date_from" value="<?php echo date('Y-m-d',time()-60*3600*24)?>" />
					                                        <b>To</b> : <input type="text" style="width: 90px;" id="date_to"
					                                                name="date_to" value="<?php echo date('Y-m-d',time())?>" /> 
					                                        <button type="submit" class="sbutton small green"><span>Go</span></button>
					                                    </div>
					                            </form>
													
													<div id="deal_stat">
														<div class="deal_grph_view">
														</div>
													</div>
													
													<div class="franch_list">
														
													</div>
													<?php $state_lst=$this->db->query("select state_id,state_name from pnh_m_states order by state_id asc "); ?>
														
													<div id="deal_stat_terr">
														 
														<div class="state_filter">
															<b>State</b> :
															<select name="state" id="state_filter">
																<?php foreach($state_lst->result_array() as $s) {?>
																	<option value="<?=$s['state_id']?>"><?=$s['state_name']?></option>
																<?php } ?>
															</select>
														</div>
														<div class="deal_piestat_view">
														</div>
													</div>
												</div>
											</td>
										</tr>
									</table>	
								</div>
							</td>	
						</tr>	
					</table>
	       		</div><!-- #Sale anaylics Block End -->	
	       	</div> 		
		 </div>      		
	</div>	       			
</div>	       			
</div>

<style>
	#description {padding:10px;background: #fcfcfc;max-height: 200px;overflow: hidden}
	#description table{background: #FFF;font-size: 11px;width: 100%;}
	#description table th{background: #f8f8f8;color:#555}
	#description table td{font-weight: normal}
	.pagination a{background: #cdcdcd;color:#555;}
	.leftcont{display: none;}
</style>

<script>
$(function() {
  // initialize scrollable
  $(".scrollable").scrollable();
});

$(".items img").click(function() {
	// see if same thumb is being clicked
	if ($(this).hasClass("active")) { return; }
 
	// calclulate large image's URL based on the thumbnail URL (flickr specific)
	var url = $(this).attr("src").replace("_t", "");
 
	// get handle to element that wraps the image and make it semi-transparent
	var wrap = $("#image_wrap").fadeTo("medium", 0.5);
 
	// the large image from www.flickr.com
	var img = new Image();
 

	// call this function after it's loaded
	img.onload = function() {
 
		// make wrapper fully visible
		wrap.fadeTo("fast", 1);
 
		// change the image
		wrap.find("img").attr("src", url);
 
	};
 
	// begin loading the image from www.flickr.com
	img.src = url;
 
	// activate item
	$(".items img").removeClass("active");
	$(this).addClass("active");
 
// when page loads simulate a "click" on the first image
}).filter(":first").click();


$('#frm_specialmargin').submit(function(){
	var spm_val = $('input[name="special_margin"]',this).val();
	var spm_deal_mrp = $('input[name="special_margin"]',this).attr('i_mrp')*1;
	var type = $('input[name="type"]:checked',this).val();
	var spm_from = $('input[name="from"]',this).val();
	var spm_to = $('input[name="to"]',this).val();
	
	var error_string = new Array();	
		if(spm_val == "")
		{
			error_string.push("Invalid Margin Entered");
		}
		if(type == "0" && spm_val > 100)
		{
			error_string.push("Please note margin entered  is more than 100%");
		}else
		{
			if(spm_deal_mrp < spm_val)
			{
				error_string.push("Please note Margin amount is more than MRP");	
			}
		}
		
		if(spm_from == "" || spm_to == "")
		{
			error_string.push("Invalid Date Range Selected");
		}
	if(error_string.length)
	{
		alert(error_string.join("\r\n"));
		return false;
	}				
});

$(document).ready(function() 
{
	$('.franch_list').html("");
	$(".tabcontent").hide();
	$(".tabcontent:first").show(); 
	
	$('.sales_anl').click(function(){
			deal_sale_stat();
			state_id = $('select[name="state"]').val();
			deal_stat_pieview(state_id);
	});

	
	$("ul.tabs li").click(function() 
	{
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tabcontent").hide();
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).fadeIn(); 
	});
});

var itemid = "<?php echo $this->uri->segment(3);?>";

$('select[name="state"]').change(function(){
	state_id = $('select[name="state"]').val();
	deal_stat_pieview(state_id);
});

(function ($) { 

	$('#tab_deal_sales').tabs();
	$('#margin_his').tabs();

	$("#sm_from,#sm_to").datepicker();
	$("#date_from,#date_to").datepicker();
	
	
	$('#description').prepend("<div style='text-align:right'><a stat='1' href='javascript:void(0)' class='tgl_desc'>More</a></div>");

	$('.tgl_desc').click(function(){
		if($(this).attr('stat') == 1)
		{
			$(this).attr('stat',0);
			$('#description').css('max-height','none');
			$(this).text('Less');
		}else
		{
			$(this).attr('stat',1);
			$('#description').css('max-height','200px');
			$(this).text('More');
		}
	});

	$("#supersch_from,#supersch_to").datepicker({changeMonth: false,minDate:0,}).focus(function(){
		  $(".ui-datepicker-prev, .ui-datepicker-next").remove();
			});
	$("#mbrsch_from,#mbrsch_to").datepicker({changeMonth: false,minDate:0,}).focus(function(){
		  $(".ui-datepicker-prev, .ui-datepicker-next").remove();
	});
})(jQuery);

$("#grid_list_frm_to").bind("submit",function(e){
    e.preventDefault();
    deal_sale_stat();
    state_id = $('select[name="state"]').val();
	deal_stat_pieview(state_id);
    return false;
});



function deal_sale_stat()
{
	var start_date = $('#date_from').val();
	var end_date = $('#date_to').val();
	$.getJSON(site_url+'/admin/jx_deal_getsales/'+start_date+'/'+end_date+'/'+itemid,'',function(resp){
		if(resp.summary == 0)
		{
			$('#deal_stat .deal_grph_view').html("<div class='alert_wrap' style='padding:113px 0px'>No Sales statisticks found between "+start_date+" and "+end_date+"</div>" );	
		}
		else
		{
			// reformat data ;
			if(resp.date_diff <= 31)
		  	{
		  		var interval = 5;
		    }
			else
			{
				var interval = 10;
			}
			$('#deal_stat .deal_grph_view').empty();
			plot2 = $.jqplot('deal_stat .deal_grph_view', [resp.summary], {
		       		seriesDefaults: {
			        showMarker:false,
			        pointLabels: { show:true }
			      },
			      axesDefaults: {
			        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
			        tickOptions: {
			          fontFamily: 'tahoma',
			          fontSize: '11px',
			          angle: -30
			        }
			    },
			        axes:{
			        xaxis:{
			          renderer: $.jqplot.CategoryAxisRenderer,
			          	label:'Date',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			        },
			        yaxis:{
				          min : 0,
						  tickInterval : interval,
						  label:'Quantity',
				          labelOptions:{
				            fontFamily:'Arial',
				            fontSize: '14px'
				          },
				          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				        }
			      }
				});
				
				/*
 				$("#deal_stat .deal_grph_view").bind('jqplotMouseMove', function(ev, gridpos, datapos, neighbor, plot) {
 					  if (neighbor) {
 					  	if(resp.date_diff <= 31)
 					  	{
 					  		var tick = 'Date : '+plot.axes.xaxis.ticks[neighbor.pointIndex][0]+plot.axes.xaxis.ticks[neighbor.pointIndex][1]+plot.axes.xaxis.ticks[neighbor.pointIndex][2]+plot.axes.xaxis.ticks[neighbor.pointIndex][3]+plot.axes.xaxis.ticks[neighbor.pointIndex][4]+plot.axes.xaxis.ticks[neighbor.pointIndex][5]+plot.axes.xaxis.ticks[neighbor.pointIndex][6]+plot.axes.xaxis.ticks[neighbor.pointIndex][7]+plot.axes.xaxis.ticks[neighbor.pointIndex][8]+plot.axes.xaxis.ticks[neighbor.pointIndex][9];
 					    }
						else
						{
							var tick = 'Month : '+plot.axes.xaxis.ticks[neighbor.pointIndex][0]+plot.axes.xaxis.ticks[neighbor.pointIndex][1]+plot.axes.xaxis.ticks[neighbor.pointIndex][2]+plot.axes.xaxis.ticks[neighbor.pointIndex][3]+plot.axes.xaxis.ticks[neighbor.pointIndex][4]+plot.axes.xaxis.ticks[neighbor.pointIndex][5]+plot.axes.xaxis.ticks[neighbor.pointIndex][6];
						}			           
			            console.log("plot.axes.xaxis.ticks="+tick);
			            $(".jqplot-highlighter-tooltip").html("<div>" + tick+"<br /> Quantity : " + datapos.yaxis.toFixed(0)+"</div>");
			        }
			    });
			   */ 
			}
		});
}
function deal_stat_pieview(state_id)
{
	var state_id=state_id;
	var start_date = $('#date_from').val();
	var end_date = $('#date_to').val();
	
	var state_name_name = document.getElementById('state_filter').options[document.getElementById('state_filter').selectedIndex].text;
	$.getJSON(site_url+'/admin/jx_deal_getsales_by_territory/'+itemid+'/'+state_id+'/'+start_date+'/'+end_date,'',function(resp){
			
	if(resp.result == 0)
	{
		//var state_name_name = document.getElementById('state_filter').options[document.getElementById('state_filter').selectedIndex].text;
		$('#deal_stat_terr .deal_piestat_view').html("<div class='alert_wrap'>No territories found</div>");
		$('.franch_list').hide();
	}
	else
	{
		// reformat data ;
		$('#deal_stat_terr .deal_piestat_view').empty();
		var resp = resp.result;
		plot3 = jQuery.jqplot('deal_stat_terr .deal_piestat_view', [resp], 
		{
			seriesDefaults:{
	            renderer: jQuery.jqplot.PieRenderer,
	            pointLabels: { show: true },
                rendererOptions: {
                    // Put data labels on the pie slices.
                    // By default, labels show the percentage of the slice.
                    showDataLabels: true,
                  }
	        },
	        highlighter: {
			    show: true,
			    useAxesFormatters: false, // must be false for piechart   
			    tooltipLocation: 's',
			    formatString:'Territory : %s <br />Quantity : %P'
			},
			grid: {borderWidth:0, shadow:false,background:'#ccc'},
	        legend:{show:true,rendererOptions: {
		            numberColumns: 2
		        } }
	   });
	    
	    $(".jqplot-xaxis-label")
	    .css({
	        cursor: "pointer",
	        zIndex: "1"
	    });
	    $('#deal_stat_terr .deal_piestat_view').bind('jqplotDataClick', function(ev,seriesIndex,pointIndex,data) {
	    	var territory_id = resp[pointIndex][2];
	    	var territory_name = resp[pointIndex][0];
	    	$('.franch_list').show();
	    	//alert(territory_id);
	    	$.getJSON(site_url+'/admin/jx_deal_getfranchise_by_territory/'+territory_id+'/'+itemid,'',function(resp)
	    	{
	    		var fran_html='';
	    		if(resp.status == 'error')
				{
					alert(resp.message);	
				}
				else
				{
					fran_html+="<b style='float:left;margin-bottom:10px'>Franchises in "+territory_name+" territory</b>";
					fran_html+='<table class="datagrid" width="100%"><tr><th style="font-size: 10px;line-height: 5px;padding: 7px 8px !important;">Sl.No</th><th style="font-size: 10px;line-height: 5px;padding: 7px 8px !important;">Franchise Name</th></tr>';
					var k=1;
					$.each(resp.fran_list,function(i,f){
						fran_html+='<tr><td style="font-size: 10px;line-height: 5px;padding: 7px 8px !important;">'+(k++)+'</td><td style="font-size: 10px;line-height: 5px;padding: 7px 8px !important;"><a href="<?php echo site_url("admin/pnh_franchise/'+f.franchise_id+'") ?>">'+f.franchise_name+'</td></tr>';
					});
					fran_html+='</table>';
					$('.franch_list').html(fran_html);
						}	
			    	});
				});
 
			}
		});
}




function load_recent_orders(link)
{
	$('#recent_orders_list').html("Loading...");
	$.get(link,function(resp){
		$('#recent_orders_list').html(resp);
	});
}

$('#recent_orders_list .pagination a').live('click',function(e){
	e.preventDefault();
	load_recent_orders($(this).attr('href'));
});

load_recent_orders(site_url+'/admin/jx_getordersbydeal/'+itemid);

$(".dealstock").dealstock({
    popup:false
    ,change:"text" //text,row
});

</script>
<script class="include" type="text/javascript" src="<?php echo base_url();?>/js/jquery.tools.min.js"></script>
<?php
