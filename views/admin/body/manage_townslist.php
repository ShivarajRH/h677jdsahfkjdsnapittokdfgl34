<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.towns.css" type="text/css" />
<script	src="<?php echo site_url(); ?>js/datatable/manage.towns.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">  
$(document).ready(function() {
	js_territory_townlist();
	$('#location_tab').tabs();
});
</script>
<div class="container page_wrap">
<h2 class="page_title">Manage States, Territories, Towns</h2>
	           <a href="javascript:void(0)" id="add_delhub" class="button fl_right button-rounded button-action button-small">Add Hub</a>
		  &nbsp;&nbsp;<a href="javascript:void(0)" id="add_towndetail" class="button fl_right button-rounded button-action button-small">Add Town</a>
		  <a href="javascript:void(0)" id="add_territorydetail" class="button fl_right button-rounded button-action button-small">Add Territory</a>
		  <a href="javascript:void(0)" id="add_statedetail" class="button fl_right button-rounded button-action button-small"> Add State</a>	

</div>
 <div class="clear"></div>
			<div id="location_tab">
				  <ol>
				         <li><a href="#state_type">State</a></li>
				  		<li><a href="#territory_type">Territory</a></li>
				  		<li><a href="#town_type">Town</a></li>
				  		<li><a href="#hublink_type">Hub</a></li>
				  </ol>
				   <div id="state_type">
				   <table id="managestate_table" class="display " cellspacing="0" width="100%">
			      <thead>
				  <tr>					
					    <th >State ID</th>
						<th>State Name</th>									
						<th>Action</th>					
				 </tr>
				</thead>
				</table>
				  </div>
				   <div id="territory_type">
				  <table id="manageterritory_table" class="display " cellspacing="0" width="100%">
			      <thead>
				  <tr>					
					   
						<th class="statesval">State Name</th>
						<th>Territory ID</th>
						<th>Territory Name</th>	
						<th>Action</th>	
				 </tr>
				</thead>
				</table>
			    </div>
				  <div id="town_type">
				  <table id="managetown_table" class="display " cellspacing="0" width="100%">
			      <thead>
				  <tr>					
					    <th class="stateval">State Name</th>
						<th class="territoryval">Territory Name</th>
						<th class="townval">Town Name</th>
						<th>Created On</th>						
						<th>Linket to Hub</th>
						<th>Totel Franchises</th>				
						<th>Employee</th>						
						<th>Action</th>					
				 </tr>
				</thead>
				</table>
			    </div>
			      <div id="hublink_type">
				  <table id="managehublink_table" class="display " cellspacing="0" width="100%">
			      <thead>
				  <tr>					
					    <th>Hub Name</th>
						<th>Total Linked Towns</th>
						<th>Total Linked FCs</th>
						<th>Created By</th>											
						<th>Action</th>					
				 </tr>
				</thead>
				</table>
			    </div>
	</div>
 <div id="stateform" title=" Add New State" style="display:none;">
		<table cellspacing="10">
			<tr>
				<td>State Name<span class="red_star">*</span></td><td>:</td>
				<td>
					<input type="text" name="statename" id="statename" class="sdata" data-required="true">
				</td>
			</tr>
			<tr><td> <span class="serrormsg" style="color: red;"></span></td></tr>
		
		</table>
</div>

 <div id="territoryform" title=" Add New Territory" style="display:none;">
		<table cellspacing="10">
			<tr>
				<td>State Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="trstatename"  id="trstatename" class="trdata" data-required="true">
											<option value="">Select State</option>
												<?php foreach($statelist as $slist){?>
											<option value="<?php echo $slist['state_id']?>"><?php echo $slist['state_name']?></option>
											<?php }?>
													
											</select></td>
			</tr>
			<tr>
				<td>Territory Name<span class="red_star">*</span></td><td>:</td>
				<td>
					<input type="text" name="trterritoryname" id="trterritoryname" class="trdata" data-required="true">
				</td>
			</tr>
			<tr><td> <span class="trerrormsg" style="color: red;"></span></td></tr>
		
		</table>
</div>

 <div id="townform" title=" Add New Town" style="display:none;">
		<table cellspacing="10">
			<tr>
				<td>State Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="tstatename"  id="tstatename" class="tstatename tdata" data-required="true">
											<option value="">Select State</option>
												<?php foreach($statelist as $slist){?>
											<option value="<?php echo $slist['state_id']?>"><?php echo $slist['state_name']?></option>
											<?php }?>
													
											</select></td>
			</tr>
			<tr>
				<td>Territory Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="tterritoryname"  id="tterritoryname" class="dterritoryval tdata" data-required="true">
											<option value="">Select Territory</option>		
											</select></td>
			</tr>
			<tr>
				<td>Town Name<span class="red_star">*</span></td><td>:</td>
				<td>
					<input type="text" name="ttownname" id="ttownname" class="tdata"  data-required="true">
				</td>
			</tr>
			<tr><td> <span class="terrormsg" style="color: red;"></span></td></tr>
		
		</table>
</div>

 <div id="updatestateform" title=" Update State">	
</div>
 <div id="updateterritoryform" title=" Update Territory" style="display:none;">	
 			<table cellspacing="10">
			<tr>
				<td>State Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="ut_statename"  id="ut_statename" class="trdata" data-required="true">
											<option value="">Select State</option>
												<?php foreach($statelist as $slist){?>
											<option value="<?php echo $slist['state_id']?>"><?php echo $slist['state_name']?></option>
											<?php }?>
													
											</select></td>
			</tr>
			<tr>
				<td>Territory Name<span class="red_star">*</span></td><td>:</td>
				<td>
					<input type="text" name="uterritname" id="uterritname" class="trdata" data-required="true">
				</td>
			</tr>
			<tr><td> <span class="utrerrormsg" style="color: red;"></span></td></tr>
		
		</table>
</div>
 <div id="updatetownform" title=" Update Town" style="display:none;">	
 <table cellspacing="10">
			<tr>
				  <td>State Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="utstatename"  id="utstatename" class="tstatename tdata" class="tdata" data-required="true">
											<option value="">Select State</option>
												<?php foreach($statelist as $slist){?>
											<option value="<?php echo $slist['state_id']?>"><?php echo $slist['state_name']?></option>
											<?php }?>										
											</select></td>
			</tr>
			<tr>
				<td>Territory Name<span class="red_star">*</span></td><td>:</td>
											<td><select name="utterritoryname"  id="utterritoryname" class="dterritoryval tdata" data-required="true">
											<option value="">Select Territory</option>		
											</select></td>
			</tr>
			<tr>
				<td>Town Name<span class="red_star">*</span></td><td>:</td>
				<td>
					<input type="text" name="uttownname" id="uttownname" class="tdata"  data-required="true">
				</td>
			</tr>
			<tr><td> <span class="terrormsg" style="color: red;"></span></td></tr>
		
		</table>
</div>

<div id="manage_delivery_hub" style="display:none;">
	<form action="<?php echo site_url('admin/pnh_upd_hubinfo');?>" method="post">
		<input type="hidden" value="0" name="hub_id" >
		<table>
			<tr><td><b>Hub Name</b></td> <td><input type="text" name="hub_name" value=""></td></tr>
			<tr><td><b>Link Towns</b></td> <td><select name="town_id[]"  data-placeholder="Choose"  style="width: 300px" multiple="true"  class="sel_multi_town"></select> </td></tr>
			<tr>
				<td><b>Link Field Coordinators</b></td> 
				<td><select name="fc_id[]" data-placeholder="Choose" style="width: 300px" multiple="true"  class="sel_fc_list"></select> </td>
			</tr>
		</table>
	</form>
</div>
<style>
#managetown_table td.franchise-count  { text-align: center }
</style>