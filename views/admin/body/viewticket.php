<?php $prioritys=array("Low","Medium","High","Urgent"); ?>

<div class="container">
<h2>View Support Ticket : TK<?=$ticket['ticket_no']?></h2>
<!--=============================< TICKET DETAILS STARTED >===========================--> 
<table class="datagrid" width="100%">
	<thead>
	<tr>
		<th>Priority</th>
		<th>Ticket No</th>
		<th>Franchise</th>
		<th>User</th>
		<th>Email</th>
		<th>Mobile</th>
		<th>Transaction</th>
		<th>Status</th>
		<th>Type</th>
		<th>Notify Depts</th>
		<th>Assigned To</th>
		<th>From APP/Web</th>
		<th>Created by</th>
		<th>Created on</th>
		<th>Last activity on</th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td><?=$prioritys[$ticket['priority']]?>

		 <a href="javascript:void(0)"  onclick='$("#tck_prior_cont").show()' class="button button-primary button-tiny">change</a>
		<div id="tck_prior_cont" style="display:none">
		<form method="post">
		<input type="hidden" name="action" value="3">
		<select name="priority">
		<?php foreach($prioritys as $i=>$p){?>
		<option value="<?=$i?>"><?=$p?></option>
		<?php }?>
		</select><input type="submit" value="Ok" class="button button-action button-tiny">
		</form>
		</div>
		</td>
		<td><a href="<?=site_url("admin/ticket/{$ticket['ticket_id']}")?>">TK<?=$ticket['ticket_no']?></a></td>
		<td align="center">
<?php 
			$franchise_name='--';
			if($ticket['franchise_name']!='' && $ticket['franchise_name']!=null)
				$franchise_name='<a href="'.site_url('/admin/pnh_franchise/'.$ticket['franchise_id']).'" target="_blank">'.$ticket['franchise_name'].'</a>';

			echo $franchise_name;
?>
		</td>
		<td align="center">
<?php 
		
		$user_det='';
		if($ticket['source']==1)
			$user_det=$ticket['req_mem_name'];
		else {
			if($ticket['name']!=null && $ticket['name']!='' )
				$user_det=''.$ticket['name'].'';
		}
		echo $user_det;
		
		/*if($ticket['user_id']){
					$ticket['user'];
					if($ticket['user']!=$ticket['name'] && $ticket['name']!="")
						echo " <br> {$ticket['name']}";
		}
		else echo $ticket['name']; */
?>
		</td>
		<td><?=$ticket['email']?></td>
		<td><?=$ticket['mobile']?>
		<img src="<?=IMAGES_URL?>phone.png" class="phone_small" onclick='makeacall("0<?=$ticket['mobile']?>")'>

		</td>
		<td>
		<?php if(!empty($ticket['transid'])){?>
		<a href="<?=site_url("admin/trans/{$ticket['transid']}")?>">
		<?=$ticket['transid']?></a><br><a style="font-size:95%" href="<?=site_url("admin/callcenter/{$ticket['transid']}")?>">PG details</a>
		<?php }?>
		</td>
		<td><?php switch($ticket['status']){
			case 0:
				echo 'Pending';
				break;
			case 1:
				echo 'Opened';
				break;
			case 2:
				echo 'In Progress';
				break;
			case 3:
				echo 'Closed';
				break;
			default:
				echo 'unknown';
		}?>
		 <a href="javascript:void(0)"  onclick='$("#tck_status_cont").show()' class="button button-primary button-tiny">change</a>
		<div id="tck_status_cont" style="display:none">
			<form id="tck_status" method="post">
			<input type="hidden" name="action" value="1">
			<select name="tck_status">
				<?php 
				$statuss=array("Unassigned","Opened","In Progress","Closed"); 

				if($ticket['type']== '10' || $ticket['type'] == '11')
						$statuss=array("Open","Open","Open","Closed");

				for($i=$ticket['status']+1>3?1:$ticket['status']+1;$i<count($statuss);$i++){?>
					<option value="<?=$i?>"><?=$statuss[$i]?></option>
				<?php }?>
			</select><input type="submit" value="Ok" class="button button-action button-tiny">
			</form>
		</div>
		</td>
		<td><?php 
		if($ticket['type']==0)
			echo 'Query';
		else if($ticket['type']==1)
			echo 'Order Issue';
		else if($ticket['type']==2)
			echo 'Bug';
		else if($ticket['type']==3)
			echo 'Suggestion';
		else if($ticket['type']==4)
			echo 'Common';
		else if($ticket['type']==5)
			echo 'PNH Returns';
		else if($ticket['type']==6)
			echo 'Courier Followups';
		else if($ticket['type']==7)
			echo 'In Transit';
		else if($ticket['type']==8)
			echo 'Received';
		else if($ticket['type']==9)
			echo 'Not Received';
		else if($ticket['type']==10)
			echo 'Request';
		else if($ticket['type']==11)
			echo 'Complaint';

		?>

		 <a href="javascript:void(0)"  onclick='$("#tck_type_cont").show()' class="button button-primary button-tiny">Change</a>
		<div id="tck_type_cont" style="display:none">
			<form method="post">
				<input type="hidden" name="action" value="2">
				<select name="type">

				<?php 
				
					
				if($ticket['type']>=5 || $ticket['type']>6)
				{
					$statuss=array();
					$statuss[7]="In Transit";
					$statuss[8]="Received";
					$statuss[9]="Not Received";
					$statuss[10]="Request";
					$statuss[11]="Complaint";
					
					foreach($statuss as $i=>$s){
						?>
						<option value="<?=$i?>"><?=$s?></option>
				<?php }
					}else{
						$statuss[0]="Query";
						$statuss[1]="Order Issue";
						$statuss[2]="Bug";
						$statuss[3]="Suggestion";
						$statuss[4]="Common";
//						$statuss[5]="PNH Returns";
						$statuss[6]="Courier Followups";
						$statuss[10]="Request";
						$statuss[11]="Complaint";
						
						foreach($statuss as $i=>$s){?>
							<option value="<?=$i?>"><?=$s?></option>
				<?php	}
					}?>

				</select><input type="submit" value="Ok" class="button button-action button-tiny">
			</form>
		</div>

		</td>
		<td><?=$ticket['dept_dets'];?></td>
		<td><?=ucfirst($ticket['assignedto']);?>
		<a href="javascript:void(0)" onclick='$(this).hide();$("#assign").show();' class="button button-primary button-tiny">
			<?php if(!$ticket['assigned_to']){?>
			Assign
			<?php }else{?>Change<?php }?>
		</a>
		<div id="assign" style="display:none">
		<input type="text" class="inp" id="assigntext">
		<div class="srch_result_pop" id="admins_list">
		</div>
		</div>
		</td>
		<td><?php 
		$from_app='ERP / Web';
		if($ticket['from_app'] == 1)
			$from_app='Mobile / API';
		echo $from_app; ?></td>
		<td>
<?php
		$created_by='--';
		if($ticket['created_by']!=null && $ticket['created_by']!='')
			$created_by=$ticket['created_by'];
		
		echo $created_by;
?>
		</td>
		<td><?=$ticket['created_on']?></td>
		<td><?=$ticket['updated_on']?></td>
		</tr>
	</tbody>
</table>
<!--=============================< TICKET DETAILS ENDS >===========================-->

<!--=============================< TICKET MESSAGE AND REPLY DETAILS STARTED >===========================-->
<h2>To-Fro messages & notes</h2>
<input type="button" value="Add notes" id="addnotes_but" class="button button-action button-tiny">
<input type="button" value="Add reply" id="addreply_but" class="button button-action button-tiny">
<div id="addnotes" class="notes_rep_cont" style="display:none;background:#f9f9c3;margin-bottom:30px;padding:5px;">
	<form method="post">
	<input type="hidden" name="type" value="0">
	<input type="hidden" name="medium" value="0">
	<table cellpadding=5>
	<tr><td>Message</td><td><textarea name="msg" style="width:500px;height:170px;"></textarea></td></tr>
	<tr><td></td><td><input type="submit" value="Add notes" class="button button-caution button-tiny"></td></tr>
	</table>
	</form>
</div>
<div id="addreply" class="notes_rep_cont" style="display:none;background:#f9f9c3;margin-bottom:30px;padding:5px;">
	<form method="post">
	<input type="hidden" name="type" value="1">
	<table cellpadding=5>
	<tr><td>Message</td><td><textarea name="msg" style="width:500px;height:170px;"></textarea></td></tr>
	<tr><td>Medium</td><td><select name="medium"><option value="0">Email</option><option value="1">Phone</option><option value="2">Others</option><?php if($ticket['type']>=5 || $ticket['type']!=6 ){?><option value="3">SMS</option><?php }?></select></td></tr>
	<tr><td colspan=2>Note that, if email is selected as medium, the reply will be sent to the customer</td></tr>
	<tr><td></td><td><input type="submit" value="Add Reply" class="button button-caution button-tiny"></td></tr>
	</table>
	</form>
</div>

<?php foreach($msgs as $m){?>
<div style="margin:10px;border:1px solid #aaa;background:<?=$m['msg_type']==1?"#F7EFB9":($m['msg_type']==0?"#ff9":"#eef9f9")?>;">
	<table width="100%" cellpadding=5>
		<tr>
			<td valign="top" width="120">
				<b><?=$m['from_customer']?$ticket['user']:($m['support_user']==0?"Mr.Cron":$m['admin_user'])?></b><br>
				<?php if($m['msg_type']){?>
				<?=$m['from_customer']?"Customer":"Support"?> Message
				<?php }?>
				<div style="font-size:80%;"><?=$m['created_on']?></div>
				<div style="font-size:90%;">
				<br>
				<?php if($m['msg_type']==1){?>
				Medium : <?php if($m['medium']==0) echo 'email'; else if($m['medium']==1) echo 'phone';else if($m['medium']==3) echo 'SMS'; else echo 'other';?>
							<br><b>Message</b>
				<?php }elseif($m['msg_type']==2){?>
							<b>Assignment</b>
				<?php }else{?>
							<b>Notes</b>
				<?php }?>
				</div>
			</td>
			<td>
			<div style="padding:7px;background:#fff;border:1px solid #ccc;">
			<?php if($m['msg_type']!=2){?>
					<iframe style="width:100%;border:0px;" src="<?=site_url("admin/ticket_frag/{$m['id']}")?>"><?=$m['msg']?></iframe>
			<?php }
				else echo $m['msg'];?>
			</div>
			</td>
		</tr>
	</table>
</div>
<?php } ?>
<!--=============================< TICKET MESSAGE AND REPLY DETAILS STARTED >===========================-->
</div>

<script>
function assign_admin(id,name)
{
	$("#admins_list").hide();
	if(!confirm("Are you sure want to assign "+name+" to this ticket?"))
		return;
	location='<?=site_url("admin/assignadmintoticket/{$ticket['ticket_id']}")?>/'+id;
}
$(function(){
	$("#addnotes_but, #addreply_but").click(function(){
		$(".notes_rep_cont").hide();
		if($(this).attr("id")=="addreply_but")
			$("#addreply").show();
		else	
			$("#addnotes").show();
	});
	$("#assigntext").keyup(function(){
		q=$(this).val();
		$.post("<?=site_url("admin/jx_assignticketadmins")?>",{q:q},function(data){
			$("#admins_list").html(data).show();
		});
	}).focus(function(){
		if($("#admins_list a").length==0)
			return;
		$("#admins_list").show();
	});
	
});
</script>
<style>
	.srch_result_pop {
		display: none;
		position: absolute;
		width: 200px !important;
		margin-left:0px !important; 
		overflow: auto;
		background: #EEE;
		border: 1px solid #AAA;
		margin-top: -3px;
	}
</style>
<?php
