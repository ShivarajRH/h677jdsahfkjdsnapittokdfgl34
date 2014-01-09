<script type="text/javascript" src="<?php echo base_url().'js/jquery.autocomplete-min.js'?>"></script>
<div class="container" style="width: 780px;">
	<div style="float: right">
		<form method="post" action="" id="barcode_frm">
			<b>Scan Barcode</b> : <input type="text" id="barcodeinput" style="padding:2px;font-size: 22px;width: 230px;border:1px solid #aaa;">
		</form>
	</div>
<h3>Link Barcodes to Product</h3>
<form id="barcodefrm" method="post">
<table id="barcode_entry" cellspacing="0" cellpadding="0" border="0" class="table_grid_view" style="width: auto;"  >
<thead>
	<th>Item Name</th>
	<th>Item ID</th>
	<th>MRP</th>
	<th>Available</th>
	<th>Barcode</th>
</thead>
<tbody>
	
</tbody>
</table>
<div align="right" style="width: 780px;">
	<input type="submit" value="Save Barcodes">
</div>
</form>

<div>
	<p>
	This works this way... <br>1. load the item using suggestion list by searching <br>2. place the barcode reader in barcode text box. auto "enter" makes the form auto-submit
	</p>
</div>

<div id="search"></div>
<br />
</div>

<style>
.autocomplete-w1 { background:url(img/shadow.png) no-repeat bottom right; position:absolute; top:0px; left:0px; margin:8px 0 0 6px; /* IE6 fix: */ _background:none; _margin:0; }
.autocomplete { border:1px solid #999; background:#FFF; cursor:default; text-align:left; max-height:350px; overflow:auto; margin:-6px 6px 6px -6px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; }
.autocomplete .selected { background:#F0F0F0; }
.autocomplete div { padding:2px 5px; white-space:nowrap; }
.autocomplete strong { font-weight:normal; color:#3399FF; }
.table_grid_view tr td{padding:2px;}
</style>


<script>

function createrow(stat){

	var gridHtml = '<tr>';
		 
		gridHtml += '	<td>'; 
		gridHtml += '	<input type="hidden" class="itemid" name="itemid[]">';
		gridHtml += '	<input type="text" class="search_item" name="itemname[]" style="width:400px;font-size:13px;padding:3px;">';
		gridHtml += '	</td>';
		gridHtml += '	<td>';
		gridHtml += '	<input type="text" size="12" class="itemiddisp" disabled="disabled">';
		gridHtml += '	</td>';
		gridHtml += '	<td>';
		gridHtml += '	<input type="text" size="6" class="itemmrp" disabled="disabled">';
		gridHtml += '	</td>';
		gridHtml += '	<td>';
		gridHtml += '	<input type="text" size="6" class="itemavail" disabled="disabled">';
		gridHtml += '	</td>';
		gridHtml += '	<td>';
		gridHtml += '	<input type="text" style="padding:3px;" class="itembarcode" name="barcode[]">';
		gridHtml += '	</td>';
		gridHtml += '</tr>';

		$('#barcode_entry tbody').append(gridHtml);
		 
		
		if(stat){
			$('.search_item:last').focus();
		} 
	
}

var selected_row = 0;
	function sel_itembyid(itemdet){
		$('.itemid:eq('+selected_row+')').val(itemdet.id);
		$('.itemiddisp:eq('+selected_row+')').val(itemdet.id);
		$('.itemmrp:eq('+selected_row+')').val(itemdet.mrp);
		$('.itemavail:eq('+selected_row+')').val(itemdet.avail_qty);
		get_barcode();
	}


	function get_barcode(){
		$('#barcodeinput').select().focus();
	}

	$('#barcode_frm').submit(function(){

		if($('#barcodeinput').val()){
			$('.itembarcode:last').val($('#barcodeinput').val());
			if((selected_row+1) == $('.search_item').length){
				createrow(1);
			}
		}else{
			$('#barcodeinput').select().focus();
		}
		
		
		return false;
	});


$('.search_item').live('focus',function(){
	selected_row = $('.search_item').index(this);

	if($(this).data('autosuggeststat') == undefined){
		var a = $(this).autocomplete({ 
		    serviceUrl:"<?=site_url("admin/jx_suggest_items")?>",
		    minChars:2, 
		    delimiter: /(,|;)\s*/, // regex or character 
		    maxHeight:400,
		    width:300,
		    zIndex: 9999,
		    deferRequestBy: 0, //miliseconds
		    params: { country:'Yes' }, //aditional parameters
		    noCache: false, //default is false, set to true to disable caching
		    // callback function:
		    onSelect: function(value, data){
		        sel_itembyid(data);
		    },
		  });
		$(this).data('autosuggeststat',1);
	}
	
}); 

 

$('.itemremove').live('click',function(){
	if(confirm('Are you sure you want to remove this entry')){
		$(this).parent().parent().fadeOut().remove();
	}
});

createrow(0);
  
</script>


<?php
