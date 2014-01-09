<div class="page_wrap container" style="width: 98%;">
	<h2 class="page_title">Product report</h2>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
		</div>
		
		<div class="page_action_buttonss fl_right" align="right" style="display:none;">
			Snapittoday menus:
			<select name="snapittoday_menu">
				<option value="0">Choose</option>
				<?php if($sit_menus_list)
				{
					foreach($sit_menus_list as $menu)
					{
				?>
				<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>		
				<?php }
					
				}
				?>
			</select>
			PNH menu :
			<select name="pnh_menu">
				<option value="0">Choose</option>
				<?php if($pnh_menus_list)
				{
					foreach($pnh_menus_list as $menu)
					{
						?>
				<option value="<?php echo $menu['id']; ?>"><?php echo $menu['name']; ?></option>		
			<?php   }
				
				}?>
			</select>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div style="width:100% !important" id="report_container" class="page_content"></div>
	
	<div id="deals_report_frag" style="opacity:0.9;background:#fff;padding:20px;position:fixed;top:110px;left:200px;width:900px;border:1px solid #aaa;height:420px;display:none;">
		<div id="deals_report_frag_cont" style="opacity:1;overflow:auto;height:420px;"></div>
		<a href="javascript:void(0)" onclick='$("#deals_report_frag").hide()'>close</a>
	</div>


	<div id="prods_report_frag" style="opacity:0.9;background:#fff;padding:20px;position:fixed;top:110px;left:200px;width:900px;border:1px solid #aaa;height:450px;display:none;">
		<div id="prods_report_frag_cont" style="opacity:1;overflow:auto;height:420px;"></div>
		<a href="javascript:void(0)" onclick='$("#prods_report_frag").hide()'>close</a>
	</div>
	
</div>


<script>
function show_deals(p)
{
	$("#prods_report_frag").hide();
	
	$("#deals_report_frag_cont").html("Loading...");
	$("#deals_report_frag").show();
	
	$.post('<?=site_url("admin/jx_deals_report")?>',{p:p},function(data){
		$("#deals_report_frag_cont").html(data);
		$("#deals_report_frag").show();
	});
}

function show_products(p)
{
	$("#deals_report_frag").hide();
	
	$("#prods_report_frag_cont").html("Loading...");
	$("#prods_report_frag").show();
	
	$.post('<?=site_url("admin/jx_deals_report_prod")?>',{p:p},function(data){
		$("#prods_report_frag_cont").html(data);
		$("#prods_report_frag").show();
	});
}

$(function(){
	$("#dr_loading").hide();
	$("#dr_show_after").show();
	$(document).keyup(function(e){
		if(e.which==27)
			$("#deals_report_frag,#prods_report_frag").hide();
	});
	$("#dr_show_after .fixed").each(function(){
		$("th",$(this)).each(function(i){
			w=$($(".fake th",$(this).parents("table").get(0)).get(i)).width();
			if($(this).next().length==0)
				w=w+15;
			$(this).css("width",w+"px");
		});
		$(this).css("position","absolute");
		$(this).css("top",$(this).parent().parent().position().top+"px");
//		$(this).parent().css("width",$(this).width()+"px");
	});
	
});


function load_report(alpha)
{
	var pnh_menu_id=$("select[name='pnh_menu']").val();
	var sit_menu_id=$("select[name='snapittoday_menu']").val();
	 

	if(!alpha)
		$("#report_container").html("<div align='center'><img src='"+base_url+'/images/loading_bar.gif'+"'></div>");
	else
		$("#report_container .datagrid").html("<div align='center'><img src='"+base_url+'/images/loading_bar.gif'+"'></div>");
		
	$.post(site_url+'/admin/jx_load_product_report/',{pnh_menu_id:pnh_menu_id,sit_menu_id:sit_menu_id,alpha:alpha},function(res){
		$("#report_container").html(res.product_report);
		$(".page_action_buttonss").show();
	},'json');
}

load_report('a');

$("select[name='pnh_menu']").change(function(){
	var menu_id=$(this).val();

	$('select[name="snapittoday_menu"] option:eq(0)').prop('selected', true);
	load_report('a');
	
});

$("select[name='snapittoday_menu']").change(function(){
	var menu_id=$(this).val();
	
	$('select[name="pnh_menu"] option:eq(0)').prop('selected', true);
	load_report('a');
	
});

function showby_aplha(alpha)
{
	load_report(alpha)
}

/*$("#reprot_container").scroll(function () {
	var top=$("#reprot_container").scrollTop();

	if(top > 1)
	{
		$(".stats_bar").css("margin-bottom",top);
		$(".stats_bar").addClass("fix_pos");
	}else{
		$(".stats_bar").removeClass("fix_pos");
		}
});*/

</script>


<style>
.leftcont{display:none;}
table.datagridsort thead tr th, table.datagridsort tfoot tr th {
	border: 1px solid #FFF;
	font-size: 12px; 
}
#franby_aphabets{}
#franby_aphabets a{display: inline-block;padding:6px 7px;font-size: 12px;background: #FFF;margin:1px;text-transform: capitalize;font-weight: bold}
#franby_aphabets a.selected{background: #666;color: #FFF;}
.datagrid {font-size: 12px;}
.datagrid th{font-size: 13px;}
.datagrid td{font-size: 12px;padding:10px 5px;}
.datagrid a{color: #0000FF}
.ui-tabs .ui-tabs-panel{padding:0px;}
.pagination{padding:5px;}
.pagination a{padding:5px 8px;background: #ddd;margin: 3px;display: inline-block;font-size: 12px;}
.ui-widget-header{background: none;border:none;}
.ui-widget{border:none;}
.stats_bar{padding:5px;background: #f2f2f2;}
.fix_pos{position:absolute;width:100%;display:inline-block;overflow:hidden;margin-bottom:30px;}
</style>