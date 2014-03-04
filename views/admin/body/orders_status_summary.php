<div class="container">
    <div style="clear: both;overflow: hidden">
    	<div class="fl_left">
    		<h2 style="margin:0px;">Order Status Summary</h2>
    	</div>
    	<div class="fl_right" >
	        <form>
	            <select id="sel_territory" name="sel_territory" >
	            	<option value="00">All Territory</option>
	                <?php foreach($pnh_terr as $terr):?>
	                	<option value="<?php echo $terr['id'];?>"><?php echo $terr['territory_name'];?></option>
					<?php endforeach;  ?>
				</select>
	            <select id="sel_town" name="sel_town">
	            	<option value="00">All Towns</option>
	                	<?php foreach($pnh_towns as $town): ?>
	                    	<option value="<?php echo $town['id'];?>"><?php echo $town['town_name'];?></option>
						<?php endforeach; ?>
				</select>
	            <select id="sel_franchise" name="sel_franchise">
	            	<option value="00">All Franchise</option>
				</select>
	        </form>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="sel_status"></div>
    </div>
    
    
    <hr>
    <div id="franchise_order_list_wrapper" style="clear: both; z-index: 9 !important;">
        <div id="franchise_ord_list" style="clear: both;overflow: hidden">
        
        		<div style="clear:both">
	        		<div class="fl_right" style="margin-top: 2px">
	                	<form id="ord_list_frm" method="post">
	                    	<input type="hidden" value="all" name="type" name="type">
	                        <b>Show Orders </b> :
	                        From :<input type="text" style="width: 90px;" id="date_from" name="date_from" value="<?php echo date('Y-m-d',time()-60*60*24)?>" />
	                        To :<input type="text" style="width: 90px;" id="date_to" name="date_to" value="<?php echo date('Y-m-d',time())?>" /> 
	                        <input type="submit" value="Submit">
	                    </form>
					</div>
	                <div class="tab_list fl_left" >
	                	<ol>
	                    	<li><a class="load_type selected" id="all" href="javascript:void(0)">All</a><div class="all_pop"></div></li>
	                        <li><a class="load_type" id="shipped" href="javascript:void(0)">Shipped</a><div class="shipped_pop"></div></li>
	                        <li><a class="load_type" id="unshipped" href="javascript:void(0)">UnShipped</a><div class="unshipped_pop"></div></li>
	                        <li><a class="load_type" id="cancelled" href="javascript:void(0)">Cancelled</a><div class="cancelled_pop"></div></li>
	                        <li><a class="load_type" id="removed" href="javascript:void(0)">Batch Disabled</a><div class="removed_pop"></div></li>
						</ol>
					</div>
				</div>
				<div style="clear:both">
					<div class="" style="float: left">
						<select id="sel_menu" name="sel_menu" >
							<option value="00">Select Menu</option>
	                        	<?php foreach($pnh_menu as $menu): ?>
	                            	<option value="<?php echo $town['id'];?>"><?php echo $menu['name'];?></option>
								<?php endforeach; ?>
						</select>
	                    <select id="sel_brands" name="sel_brands">
	                    	<option value="00">Select Brands</option>
	                        <?php foreach($pnh_brands as $brand): ?>
	                        	<option value="<?php echo $brand['id'];?>"><?php echo $brand['name'];?></option>
							<?php endforeach; ?>
						</select>
					</div>         
					                   
				</div>
				
        </div>
        <div class="orders_status_summary_div">&nbsp;</div>
    </div>
</div>


<div id="inv_transitlogdet_dlg" title="Shipment Transit Log">
	<h3 style="margin:3px 0px;"></h3>
	<div id="inv_transitlogdet_tbl"></div>
</div>

<script>
$(function(){ $("#from,#to").datepicker();});
function date_range()
{
	location="<?=site_url("admin/order_status_summary")?>/"+$("#from").val()+"/"+$("#to").val();
}
</script>


<script>
    //ENTRY 7
    $("#sel_menu").live("change",function() {
            var menuid=$(this).find(":selected").val();//text();
            
            var url="<?php echo site_url("admin/jx_get_brandsbymenuid"); ?>"+"/"+menuid;
            $.post(url,function(resp) {
                    if(resp.status=='success') {
                         var obj = jQuery.parseJSON(resp.brands);
                        $("#sel_brands").html(objToOptions_brands(obj));
                    }
                    else {
                        $("#sel_brands").val($("#sel_brands option:nth-child(0)").val());
                        //$(".sel_status").html(resp.message);
                    }
                },'json').done(done).fail(fail);

        loadTableData(0);
        return false;
    });
    $("#sel_brands").live("change",function() {
        loadTableData(0);
        return false;
    });
    $("#sel_franchise").live("change",function() {
        var franchiseid=($("#sel_franchise").val()=='00')? 00 :$("#sel_franchise").val();
        if(franchiseid!=00) {
            var url="<?php echo site_url("admin/jx_franchise_creditnote"); ?>"+"/"+franchiseid;
            $.post(url,function(resp) {
                    if(resp.status=='success') {
                         $(".sel_status").html(resp);
                    }
                    else {
                        $(".sel_status").html(resp);
                    }
                }).done(done).fail(fail);
        }
        else {
            $(".sel_status").html(" ");
        }
        loadTableData(0);
        return false;
    });
    
    //ENTRY 6
    $("#sel_town").live("change",function() { 
        var townid=$(this).find(":selected").val();//text();
        var terrid=$("#sel_territory").find(":selected").val();//text();
        var url="<?php echo site_url("admin/jx_suggest_fran"); ?>"+"/"+terrid+"/"+townid;
        $.post(url,function(resp) {
                if(resp.status=='success') {
                     var obj = jQuery.parseJSON(resp.franchise);
                    $("#sel_franchise").html(objToOptions_franchise(obj));
                }
                else {
                    $("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
                    //$(".sel_status").html(resp.message);
                }
            },'json').done(done).fail(fail);
        
        loadTableData(0);
        return false;
    });
    
    
    //ENTRY 5
    $("#sel_territory").live("change",function() {
        var terrid=$(this).find(":selected").val();//text();
//        if(terrid=='00') {          $(".sel_status").html("Please select territory."); return false;        }
        
        var url="<?php echo site_url("admin/jx_suggest_townbyterrid"); ?>/"+terrid;//  alert(url);
        $.post(url,function(resp) {
            if(resp.status=='success') {
                 //print(resp.towns);
                 var obj = jQuery.parseJSON(resp.towns);
                $("#sel_town").html(objToOptions_terr(obj));
            }
            else {
                $("#sel_town").val($("#sel_town option:nth-child(0)").val());
                $("#sel_franchise").val($("#sel_franchise option:nth-child(0)").val());
                            //$(".sel_status").html(resp.message);
            }
        },'json').done(done).fail(fail);
        loadTableData(0);
        return false;
    });
     
     $(".tab_list a").bind("click",function(e){
         $(".tab_list a.selected").removeClass('selected');
         $(this).addClass('selected');
         loadTableData(0);
     });
     
    //ENTRY 2
    $("#ord_list_frm").bind("submit",function(e){
        e.preventDefault();
        loadTableData(0);
        return false;
    });
    
    function loadTableData(pg) {
            
        $(".ttl_orders_status_listed,.c2,.all_pop, .shipped_pop, .unshipped_pop, .cancelled_pop, .removed_pop, .show_totalamount").html("").css({"display":"none"});
            
         var type = $('.tab_list .selected').attr('id');
         var date_from=$( "#date_from").val();
         var date_to=$( "#date_to").val();
         var terrid= ($("#sel_territory").val()=='00')?0:$("#sel_territory").val();
         var townid=($("#sel_town").val()=='00')?0:$("#sel_town").val();
         var franchiseid=($("#sel_franchise").val()=='00')?0:$("#sel_franchise").val();
         var menuid=($("#sel_menu").val()=='00')?0:$("#sel_menu").val();
         var brandid=($("#sel_brands").val()=='00')?0:$("#sel_brands").val();
         
         $('.orders_status_summary_div').html("<div class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>");
         $.post(site_url+"/admin/jx_orders_status_summary"+"/"+type+"/"+date_from+"/"+date_to+'/'+terrid+'/'+townid+'/'+franchiseid+'/'+menuid+'/'+brandid+'/'+pg,{},function(resp){
            $('.orders_status_summary_div').html(resp);
         });
    }
    
    //ENTRY 3
    $(".orders_status_pagination a").live("click",function(e){
        e.preventDefault();
        $('.orders_status_summary_div').html("<div class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loading...</div>");
        $.post($(this).attr('href'),{},function(resp){
            $('.orders_status_summary_div').html(resp);
        });
        return false;
    });
 
    function done(data) { }
    function fail(xhr,status) { $('.orders_status_summary_div').print("Error: "+xhr.responseText+" "+xhr+" | "+status);}
    function success(resp) {
            $('.orders_status_summary_div').html(resp);
    }
       
    //ENTRY 4
    $(document).ready(function() {
        //FIRST RUN
        var reg_date = "<?php echo date('m/d/Y',  time()*60*60*24);?>";
        
        $( "#date_from").datepicker({
             changeMonth: true,
             dateFormat:'yy-mm-dd',
             numberOfMonths: 1,
             maxDate:0,
//             minDate: new Date(reg_date),
               onClose: function( selectedDate ) {
                 $( "#date_to" ).datepicker( "option", "minDate", selectedDate ); //selectedDate
             }
           });
        $( "#date_to" ).datepicker({
            changeMonth: true,
             dateFormat:'yy-mm-dd',
//             numberOfMonths: 1,
             maxDate:0,
             onClose: function( selectedDate ) {
               $( "#date_from" ).datepicker( "option", "maxDate", selectedDate );
             }
        });

        prepare_daterange('date_from','date_to');
        loadTableData(0);
        
    });

    function objToOptions_brands(obj) {
        var output='';
            output += "<option value='00' selected>All Brands</option>\n";
        $.each(obj,function(key,elt){
            if(obj.hasOwnProperty(key)) {
                output += "<option value='"+elt.id+"'>"+elt.name+"</option>\n";
            }
        });
        return(output);
    }
    function objToOptions_terr(obj) {
        var output='';
            output += "<option value='00' selected>All Towns</option>\n";
        $.each(obj,function(key,elt){
            if(obj.hasOwnProperty(key)) {
                output += "<option value='"+elt.id+"'>"+elt.town_name+"</option>\n";
            }
        });
        return(output);
    }
    function objToOptions_franchise(obj) {
        var output='';
            output += "<option value='00' selected>All Franchise</option>\n";
        $.each(obj,function(key,elt){
            if(obj.hasOwnProperty(key)) {
                output += "<option value='"+elt.franchise_id+"'>"+elt.franchise_name+"</option>\n";
            }
        });
        return(output);
    }
    
  
</script>
<style type="text/css">

.leftcont {        display: none;    }

.loading {
    text-align: center;
    margin: 5% 0 0 40%;
    visibility: visible; font-size: 16px; 
}
.dash_bar_right {
    background-color: #F1F0FF;
    color: #443266;
    font-size: 11px;
    min-width: 100px !important;
    text-align: center;
    padding: 3px 6px;
    margin:0px 5px 5px 5px; 
}
.dash_bar_right span {
    display: block;
    font-size: 14px;
}
.dash_bar {
    background-color: #F1F0FF;
    color: #443266; font-size: 18px;float: right;
}
        #franchise_order_list_wrapper .tab_list{
                display: block;

        }
        #franchise_order_list_wrapper .tab_list ol{
                padding-left:0px;
        }
        #franchise_order_list_wrapper .tab_list li{
                display: inline-block;
        }
        #franchise_order_list_wrapper .tab_list li a{
                display: block;
                background: #F1F0FF;
                padding:5px 20px;
                font-size: 13px;
                color: #454545;
                cursor:pointer;
                font-weight: bold;
                text-decoration: none;
        }
        #franchise_order_list_wrapper .tab_list li a.selected{
                background: #443266;
                color: #C3C3E5;
        }
.transit_link{
	border-radius:5px;
	background:#96C5E0;
	display:inline-block;
	padding:3px 7px;
	color:#fff;
}
.transit_link:hover{
	border-radius:0px;
	background:#3084C1;
	text-decoration:none;
}

.grid_bg { background:#F1F0FF; }
/*.datagrid td { background: #F1F0FF; }*/
.datagrid th { background: #443266;color: #C3C3E5; }

/*.datagrid th{padding:8px 7px;}*/
/*.datagrid tr { background: #F1F0FF;color: #443266; }
.datagrid1 { border-collapse: collapse;border:none !important;}
.datagrid1 th{padding:8px 7px; border:none !important;font-size: 13px;background: #C3C3E5;color: #443266;}
.datagrid1 td{ border-right:none;border-left:none;border-bottom:none;font-size: 12px;padding:2px;color: #444;background-color:#F1F0FF; text-transform: capitalize;}
.datagrid1 td:hover {background-color:transparent; }
.datagrid1 td a{text-transform: capitalize;}
.datagrid1 td b{font-weight: bold;font-size: 11px;}*/

</style>	

<script type="text/javascript">


function get_invoicetransit_log(ele,invno) {
	$('#inv_transitlogdet_dlg').data({'invno':invno}).dialog('open');
        return false;
}

var refcont = null;
$('#inv_transitlogdet_dlg').dialog({width:'900',height:'auto',autoOpen:false,modal:true,
                open:function(){


                        //,'width':refcont.width()
                        //$('div[aria-describedby="inv_transitlogdet_dlg"]').css({'top':(refcont.offset().top+15+refcont.height())+'px','left':refcont.offset().left});

                        $('#inv_transitlogdet_tbl').html('loading...');
                        $.post(site_url+'/admin/jx_invoicetransit_det','invno='+$(this).data('invno'),function(resp){
                                if(resp.status == 'error')
                                {
                                        alert(resp.error);
                                }else
                                {
                                        var inv_transitlog_html = '<table class="datagrid" width="100%"><thead><th width="30%">Msg</th><th width="10%">Status</th><th width="10%">Handle By</th><th width="10%">Logged On</th><th width="15%">SMS</th></thead><tbody>';
                                        $.each(resp.transit_log,function(i,log){
                                                inv_transitlog_html += '<tr><td>'+log[5]+'</td><td>'+log[1]+'</td><td>'+log[2]+'('+log[4]+')</td><td>'+log[3]+'</td><td>'+log[6]+'</td></tr>';
                                        });
                                        inv_transitlog_html += '</tbody></table>';
                                        $('#inv_transitlogdet_tbl').html(inv_transitlog_html);

                                        $('#inv_transitlogdet_dlg h3').html('Invoice no :<span style="color:blue;font-size:12px">'+resp.invoice_no+'</span>  Franchise name: <span style="color:orange;font-size:12px">'+resp.Franchise_name +'</span> Town : <span style="color:gray;font-size:12px">'+resp.town_name+'</span>'+' ManifestoNo :'+resp.manifesto_id);




                                }
                        },'json');
                }
});
</script>
 <style>
	 	.subdatagrid th{padding:3px !important;text-align: left;}
.span_count_wrap {
background: none repeat scroll 0 0 #87318C;
font-size: 11px;
color: #FFF;
margin: 2px;
padding: 0px;
width: 180px;
text-align: center;
display: block;
}
.show_totalamount{background: #ffffd0;padding:5px;}
	 </style>
    <?php
