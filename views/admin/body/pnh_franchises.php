<div class="container">
	<span style="float: right;margin:10px;">
		<a href="<?=site_url("admin/pnh_addfranchise")?>">Add Franchise</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="<?php echo site_url('admin/pnh_unorderd_log') ?>">Unorderd Franchise Log</a>
	</span>
<h2><?=isset($pagetitle)?$pagetitle:"Franchises"?></h2>
<div>

<div class="dash_bar">
<span><?=count($frans)?></span>
Registered 
</div>
<? /*
<div class="dash_bar">
<span><?=$suspended_frans_ttl?></span>
Suspended
</div> */ ?>
<? /*
<?php if($this->erpm->auth(true,true)){ ?>
<div class="dash_bar">
<span><?=count($frans)-$suspended_frans_ttl?></span>
Active
</div> <?php } ?>
*/ ?>

<div class="dash_bar_right">
Generate Print by Territory : <select id="sel_p_terry">
<option value="0">select</option><?php foreach($this->db->query("select id,territory_name as name from pnh_m_territory_info order by name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['name']?></option>
<?php }?>
</select>
</div> 
<div class="clear"></div>
</div>





<div id="tab_showfranby">
	<ul>
		
		<li><a id="link_franlist_latest20" href="#franlist_latest20" onclick="load_franchisesbysel(1,0,0,0,0,0)">New Franchises</a></li>
		<li><a id="link_franlist_regthismonth" href="#franlist_regthismonth" onclick="load_franchisesbysel(2,0,0,0,0,0)">Registered This Month</a></li>
		<li><a href="#franlist_all" onclick="load_franchisesbysel(5,0,0,0,0,0)">All Franchises</a></li>
		<?php /*?>
		<li><a href="#franlist_suspended" onclick="load_franchisesbysel(4,0,0,0,0,0)">Suspended Franchises</a></li>
		<?php /*/?>
	</ul>
	
	<div id="franlist_latest20">
		<div class="franlist_holder"></div>
	</div>
	<div id="franlist_regthismonth">
		<div class="franlist_holder"></div>
	</div>
	<?php /*?>
	<div id="franlist_suspended">
		<div class="franlist_holder"></div>
	</div>
	<?php /*/?>
	<div id="franlist_all">
		<div class="franlist_holder"></div>
	</div>
</div>

</div>
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

</style>
<script>
$(function(){
	$("#sel_p_terry").change(function(){
		v=$(this).val();
		if(v==0)
			return;
		window.open("<?=site_url("admin/pnh_print_franchisesbyterritory")?>/"+v);
	}).val("0");
	$("#sel_terry").change(function(){
		v=$(this).val();
		if(v==0)
			return;
		location="<?=site_url("admin/pnh_franchisesbyterritory")?>/"+v;
	}).val("0");
	$("#sel_town").change(function(){
		v=$(this).val();
		if(v==0)
			return;
		location="<?=site_url("admin/pnh_franchisesbytown")?>/"+v;
	}).val("0");
});
$('.datagridsort').tablesorter({sortList:[[0,0]]});

var disp_config_params = {};

function load_franchisesbysel(type,alpha,terr_id,town_id,menuid,pg)
{
	var tab_ele = '';
	if(type == 1)
		tab_ele = $('#franlist_latest20 .franlist_holder');
	else if(type == 2)
		tab_ele = $('#franlist_regthismonth .franlist_holder');
	else if(type == 3)	
		tab_ele = $('#franlist_byalpha_'+alpha+' .franlist_holder');
	else if(type == 4)	
		tab_ele = $('#franlist_suspended .franlist_holder');
	else if(type == 5)	
		tab_ele = $('#franlist_all .franlist_holder');		
		
	//tab_ele.html("Loading...");
	
	if($('#jx_frlist',tab_ele).length)
		$('#jx_frlist',tab_ele).html('<div align="center" style="padding:10px;"><img src="'+base_url+'/images/loading.gif'+'"></div>');
	else
		tab_ele.html('<div align="center" style="padding:10px;"><img src="'+base_url+'/images/loading.gif'+'"></div>');
	
	disp_config_params = {'type':type,'alpha':alpha,'terr_id':terr_id,'town_id':town_id,'menuid':menuid,'pg':pg}
	
	$.post(site_url+'/admin/jx_getfranchiseslist',disp_config_params,function(resp){
		tab_ele.html(resp);
	});
}


$('#tab_showfranby').tabs();

function reload_frlist()
{
	load_franchisesbysel(disp_config_params.type,disp_config_params.alpha,disp_config_params.terr_id,disp_config_params.town_id,disp_config_params.menuid,disp_config_params.pg);
}

$(function(){
	$('#link_franlist_latest20').trigger('click');
	
        $('select[name="fil_terr"]').live('change',function(){
		disp_config_params.terr_id = $(this).val();
		disp_config_params.town_id = 0;
		reload_frlist();
	});
	
	$('select[name="fil_town"]').live('change',function(){
		disp_config_params.town_id = $(this).val();
		reload_frlist();
	});
	
	$('select[name="fil_menu"]').live('change',function(){
		disp_config_params.terr_id = 0;
		disp_config_params.town_id = 0;
		disp_config_params.menuid = $(this).val();
		reload_frlist();
	});
	
	$('.pagination a').live('click',function(e){
		e.preventDefault();
		
		$('.franlist_holder:visible').html('loading...');
		
		$.post($(this).attr('href'),{},function(resp){
			$('.franlist_holder:visible').html(resp);
		});
	
	});
	
});

function showby_aplha(alpha)
{
	disp_config_params.alpha = alpha;
	reload_frlist();
}

</script>

<?php
