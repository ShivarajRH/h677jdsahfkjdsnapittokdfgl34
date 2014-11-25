<div class="container">

<h2>Franchises bulk upload</h2>

<form method="post" action="<?php echo site_url('admin/process_update_franchises')?>" target="_blank" enctype="multipart/form-data" id="bk_form">
Upload file : <input type="file" name="franchise"><input type="submit" name="submit" value="Upload">
<div style="color:#888;margin:10px 20px;"><ul><li>Only CSV format supported</li><li>First row as head neglected always</li>

</ul></div>
</form>

<h4 style="margin:0px;">Template</h4>
<div style="max-width:900px;overflow:auto">
<table class="datagrid noprint">
<?php $template=array("Name","Address","Locality","Taluk","Hobli","City","State","Postcode","Login Mobile1","Login Mobile2","Email","Class","Territory","Town","Shop Name","Type of business","No of employees","Area","Latitude","Longtude","Own Property","Website","Internet","Tin No","Pan No","Service Tax No","Registration No","Contact Name","Designation","Mobile 1","Mobile 2","Telephone","Fax","Email1","Email2","Type","Deposit Amount","Instrument Type","Cheque/DD No","Cheque/DD Date","Bank Name","Deposit Remarks");
$values=array("Foot World ","Timmappa Nayak Circle ","Siddapur","Siddapur","Siddapur","Coorg","Karnataka ","586101","9739059008","9739059008","test@storeking.in","A","Davengere","Davengere","PC Provision Stores","Retail","4","12.307278","75.687979","4940492:2,4489354:1","1","www.11feet.com","Airtel","29940663557","DFMTS6701G","AYXPA9688F","AYXPA9688F","Sunil","Store Keeper","9739059008","9739059008","08054612336","08054612336","test@storeking.in","test@storeking.in","RF","18000","DD","56345345345","2014-09-27","HDFC Bank","added today");
?>
<thead>
<tr>
<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php foreach($template as $i=>$t){?><td><?=$values[$i]?></td><?php }?>
</tr>
</tbody>
</table>
</div>

<br><br>

</div>

<script>
$(function(){
	$("#bk_form").submit(function(){
		window.setTimeout(function(){location="<?=site_url("admin/pnh_franchise_bulk_upload")?>";},5000);
		return true;
	});
});
</script>
<?php
