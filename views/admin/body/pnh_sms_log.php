<div class="container">

<div style="float:right;padding-right:120px;">
Date Range : <input type="text" id="f_start" size=10> to <input size=10 type="text" id="f_end"> <input type="button" value="Go" onclick='date_range()'>
</div>

<h2><?=($fid?"":"Recent ")?>SMS Log <?php if($fid){?>for '<?=($fid?$this->db->query("select franchise_name as n from pnh_m_franchise_info where franchise_id=?",$fid)->row()->n:"")?>'<?php }?></h2>
<div class="tabs">
<ul>
<li><a href="#log">In &lt;-&gt; Out Log</a></li>
<li><a href="#erplog">SMS from ERP</a></li>
</ul>

<div id="log">
<table class="datagrid">
<thead><tr><th>From</th><th colspan=2>Msg</th><th width="50">Replied?</th><th>Time</th><?=!$fid?"<th>Franchise</th>":""?></tr></thead>
<tbody>
<?php foreach($log as $l){?>
<tr><td><?=$l['from']?></td><td colspan=2><?=nl2br($l['input'])?></td><td><?=!empty($l['reply'])?"YES":"NO"?></td><td><?=date("g:ia d/m/y",$l['created_on'])?></td>
<?php if(!$fid){?><td><a href="<?=site_url("admin/pnh_franchise/{$l['franchise_id']}")?>"><?=$l['franchise']?></a></td><?php }?></tr>
<?php if(!empty($l['reply'])){?>
<tr style="background:#eee;"><td style="background:#FFFFEF"></td><td>REply:</td><td><?=nl2br($l['reply'])?> 
	 
		
	 
</td><td></td><td><?=date("g:ia d/m/y",$l['reply_on'])?></td>
<?php if(!$fid){?><td>
	<form class="resend_sms" action="<?php echo site_url('admin/jx_pnh_fran_resendreply')?>" method="post">
			<input type="hidden" name="resend_reply_for" value="<?php echo $l['reply_for']?>" >	
			<input type="submit" value="Resend SMS" style="font-size: 10px;border:1px solid #ccc;background: #f7f7f7;color:#000 !important;padding:3px 10px;">
		</form>
</td><?php }?>
</tr>
<?php }?>
<?php }?>
</tbody>
</table>
</div>

<div id="erplog">
<table class="datagrid" width="100%">
<thead><tr><th width="150px">To</th><th>Msg</th><th>Sent on</th><?php if(!$fid){?><th>Franchise</th><?php }?></tr></thead>
<tbody>
<?php foreach($erp as $l){?>
<tr><td><?=$l['to']?></td><td><?=$l['msg']?></td><td><?=date("g:ia d/m/y",$l['sent_on'])?></td>
<?php if(!$fid){?><td><a href="<?=site_url("admin/pnh_franchise/{$l['franchise_id']}")?>"><?=$l['franchise']?></a><?php }?>
</tr>
<?php }?>
</tbody>
</table>
</div>


</div>
</div>

<script>

$('.resend_sms').submit(function(){
	if(!confirm("Are you sure want to resend this reply to franchise ?"))
	{
		return false;
	}

	$.post($(this).attr('action'),$(this).serialize(),function(resp){
		alert(resp.message);
	},'json');
	return false;
});

function date_range()
{
	location='<?=site_url("admin/pnh_sms_log/".$fid*1)?>/'+$("#f_start").val()+"/"+$("#f_end").val();
}
$(function(){
	$("#f_start,#f_end").datepicker();
});

</script>

<?php
