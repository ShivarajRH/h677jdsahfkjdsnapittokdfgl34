<div class="container">

<div style="float:right;padding-right:120px;">
        Date Range : <input type="text" id="date_start" size=10 value="<?=$from;?>"> to <input size=10 type="text" id="date_end" value="<?=$to;?>"> <input type="button" value="Go" onclick='date_range()'>
</div>

<h2>
    <?=($mid?"":"Recent ")?>SMS Log for '<?=$member_name;?>'
</h2>
<div class="tabs">
    <ul>
        <li><a href="#log">In &lt;-&gt; Out Log</a></li>
        <li><a href="#erplog">SMS from ERP</a></li>
    </ul>

<div id="log">
    <table class="datagrid">
        <thead>
            <tr><th>From</th><th colspan=2>Msg</th><th width="50">Replied?</th><th>Time</th><?=!$mid?"<th>Member</th>":""?></tr>
        </thead>
        <tbody>
            <?php foreach($log as $l){?>
                <tr><td><?=$l['from']?></td><td colspan=2><?=nl2br($l['input'])?></td><td><?=!empty($l['reply'])?"YES":"NO"?></td><td><?=date("g:ia d/m/y",$l['created_on'])?></td>
                <?php if(!$mid){ ?><td><a href="<?=site_url("admin/pnh_viewmember/{$l['user_id']}")?>"><?=$l['member']?></a></td><?php }?></tr>
            
                <?php if(!empty($l['reply'])){?>
                    <tr style="background:#eee;"><td style="background:#FFFFEF"></td><td>Reply:</td><td><?=nl2br($l['reply'])?> 

                        </td><td></td><td><?=date("g:ia d/m/y",$l['reply_on'])?></td>
                        <?php if(!$mid){?><td>
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
        <thead><tr><th width="150px">To</th><th>Msg</th><th>Sent on</th><?php if(!$mid){?><th>Franchise</th><?php }?></tr></thead>
        <tbody>
            <?php foreach($erp as $l){?>
            <tr><td><?=$l['to']?></td><td><?=$l['msg']?></td><td><?=date("g:ia d/m/y",$l['sent_on'])?></td>
            <?php if(!$mid){?><td><a href="<?=site_url("admin/pnh_viewmember/{$l['user_id']}")?>"><?=$l['member']?></a><?php }?>
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
	location='<?=site_url("admin/pnh_sms_log_member/".$mid*1)?>/'+$("#date_start").val()+"/"+$("#date_end").val();
}
$(function(){
	$("#date_start,#date_end").datepicker();
});

</script>

<?php
