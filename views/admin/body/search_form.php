<div id="container">
	<h2>Search</h2><?php // echo site_url('admin/p_search') action=""?>
	<form name="search_form" method="post" data-validate="parsley" onsubmit="return fn_search(this);">
		<table>
			<tr>
				<td>Search Input : <input type="text" name="search_data" id="search_data" autocomplete="off" data-required="true"></td>
					
				<td><input type="submit" value="search" id="search"></td>
				
			</tr>
			<tr><td id="serch_kwrd_title"></td></tr>
		</table>
	</form>
	
	<div id="search_cont" >
	<table class="datagrid" id="srch_tbl_cont" width="100%">
		<thead>
		<th>Deal id</th>
		<th>Deal name</th>
		<th>Sold qty</th> 
		<th>Brand</th>
		<th>category</th>
		<th>MRP</th>
		<th>Offer Price</th>
		</thead>
		<tbody></tbody>

	</table>
	
	
	</div>
</div>
<script>


function fn_search(elt) {
	//$("#search").click(function(){
	var srch_cont_tbl='';
	$("#srch_tbl_cont tbody").html("");
	if(($("#search_data").val()).length!=0)
	{
		$('#srch_tbl_cont tbody').html('<tr><td colspan="8"><div align="center"><img src="'+base_url+'/images/loading_bar.gif'+'"> </div></td></tr>');
		
		var srch_cont_tbl='';
		$.post(site_url+"admin/p_search",{search_data:$("#search_data").val()},function(resp){
			if(resp.srch_results.length == 0)
			{
				$('#srch_tbl_cont tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
			}else
			{
			
				$.each(resp.srch_results,function(i,s){
					srch_cont_tbl+="<tr>";
					srch_cont_tbl+="<td>"+s.dealid+"</td>";
					srch_cont_tbl+="<td>"+s.dealname+"</td>";
					srch_cont_tbl+="<td>"+s.sold_qty+"</td>";
					srch_cont_tbl+="<td>"+s.brand_name+"</td>";
					srch_cont_tbl+="<td>"+s.cat_name+"</td>";
					srch_cont_tbl+="<td>"+s.mrp+"</td>";
					srch_cont_tbl+="<td>"+s.offerprice+"</td>";
					srch_cont_tbl+="</tr>";
				});
			}
			$("#srch_tbl_cont tbody").html(srch_cont_tbl);
		},'json');
		
	}
	else
	{
		alert('please input search keyword');
		var srch_cont_tbl='';
		return false;
	}
	//});
	return false;
}

</script>