<div class="container">
<h2>Generate Kfile</h2>

<form method="post" action="<?php echo site_url('admin/generate_kfile_byrange');?>"> 
	<table class="datagrid">
		<tr>
			<td>
				<b>Start Date</b> :
				<div><input type="text" id="from_date" name="from" value="<?php echo date('Y-m-d')?>" /></div>
			</td>
			<td>
				<b>End Date</b> :
				<div><input type="text" id="to_date" name="to" value="<?php echo date('Y-m-d')?>" /></div>
			</td>
			<td>
				&nbsp;
				<div><input type="submit" value="Generate" style="float:right" /></div>
			</td>
		</tr>
	</table>
</form>

<div style="margin:5px 30px;margin-left: 165px;">OR</div> 
<h3>Last 5 days of Outscan</h3>
<?php foreach($outscans as $date=>$o){?>
<h4 style="margin:0px;"><?=$date?></h4>
<div style="background:#eee;padding:5px;margin-bottom:20px;">
<form method="post">
<input type="hidden" name="ids" value="<?=implode(",",$o)?>">
<?php foreach($o as $c=>$i){?>
<?php if($c>0){?>, <?php }?><a href="<?=site_url("admin/invoice/$i")?>"><?=$i?></a>
<?php }
if(!empty($o)){?>
<input type="submit" value="Generate">
<?php }else echo "No outscans made";?>
</form>
</div>
<?php }?>

</div>
<script>
prepare_daterange('from_date','to_date');
 
</script>
<?php
