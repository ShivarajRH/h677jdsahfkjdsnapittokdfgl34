<?php
	$user = $this->erpm->auth();
?>
<!--<title>Product procurement list</title>-->
<style>
	body{font-family:arial;font-size:14px;}
	.onscreen{width:850px;margin:10px auto;}
	@media print
	  {
	  	.onscreen {width:100%}
	  }
          .heading2 { margin-bottom: 5px; float: left;  }
          .heading2 h2 { width: 680px; }
          h3 {
              width: 495px;
                margin: 10px;
          }
          .print_link_block { margin-top: 10px; }
          .print_link_block .print_link {font-weight:bold;}
          .tbl_class {font-family:arial;font-size:13px;width: 100%;}
          /*.clear {}*/
          .head_right_block {float: right;}
          .head_right_block span { padding: 4px 5px; }
</style>

<div class="onscreen">
        <div class="blockonscreen">
		<div class="head_right_block">
                        <div><b>Date : </b><?=Date('d-m-Y'); ?></div>
                        <div><b>By : </b><?php echo $user['username']; ?></div>
                        <div class="print_link_block"><a href="javascript:void(0);" class="print_link" onclick="">Print</a></div>
		</div>
		<div class="heading2">
			<h2 style="">Product-wise Pick slip <?=$this->uri->segment(3)?></h2>	
		</div>
	</div>
        <div class="clear"></div>
        
        
	<?php 
        $pinvno_log_arr=array();
        foreach($prods as $ii=>$product) { ?>
        <!--<div class="print_link_block"><a href="javascript:void(0);" class="print_link" onclick="print_this()">Print</a></div>-->
        
        <style>
            .wrapper {
                    clear: both;
            }
            .signature {
                    display:none;
            }
            @media print{
                .wrapper {
                    clear: both;
                    /*page-break-after:auto;*/
                }
                .signature {
                    display: block;
                }
/*                .signature_default {
                    display: none;
                }*/
                .print_link_block {
                    display: none;
                }
            }
                
        </style>
        
	<div class="wrapper" style="">
                <h3><?=$product['menuname']?></h3>
                
		<table border=1 class="tbl_class"  cellpadding=3>
		<tr style="background:#aaaaaa">
                    <th>#</th><th>Proforma Invoice No</th><th>Product ID</th><th>Product Name</th><th>Qty</th><Th>MRP</Th><th>Location</th>
		</tr>
		<?php $i=1;
                    $tmp_arr=$tmp_arr2=array();
                    foreach($product as $k=>$p){
                        if($k==='menuname')  continue;
                        
                        if(!in_array($p['p_invoice_no'],$tmp_arr)) {
                            $tmp_arr[] = $p['p_invoice_no'];
                            array_push($pinvno_log_arr,$p['p_invoice_no']);
                            $invoice_msg = '<a target="_blank" href="'.site_url('admin/pack_invoice/'.$p['p_invoice_no']).'">'.$p['p_invoice_no'].'</a>';
                        }
                        else {
                            $invoice_msg = '--"--';
                        }
                        
                        if(!in_array($p['product_id'],$tmp_arr2)) {
                            $tmp_arr2[] = $p['product_id'];
                            $product_id_msg = '<a target="_blank" href="'.site_url('admin/product/'.$p['product_id']).'">'.$p['product_id'].'</a>';
                        }
                        else 
                            $product_id_msg = '--"--';
                        
                        if(!in_array($p['product'],$tmp_arr2)) {
                            $tmp_arr2[] = $p['product'];
                            $product_msg = ''.$p['product'].'';
                        }
                        else 
                            $product_msg = '--"--';
                        
                        
                        
                    ?>
			<tr <?php if($i%2==0){?>style="background:#eee;"<?php }?>>
                                <td><?=$i?></td>
				<td  width="80"><?=$invoice_msg;?></td>
				<td  width="80"><?=$product_id_msg;?></td>
				<td><?=$product_msg?></td>
				<td width="20" align="center"><?=$p['qty']?></td>
				<?php list($loc,$mrp) = explode('::',$p['location']);?>
				<td width="30" ><?=$mrp?></td>
				<td width="150"><?=$loc?>&nbsp;</td>
			</tr>
		<?php $i++;
                    }
		?>
		</table>
<!--                <div class="block signature" style="">
                        <br>
                        <span style="margin:22px 0px 0px;float:right;"><b>Validated By</b> : _______________<br /></span><br />
                        <span style="margin:7px 0px;float:left;;"><b>Picked By</b> : _____________________<br /></span>
                </div> -->
	</div>

	<?php }
        //echo count($pinvno_log_arr);
        ?>
        <input type="hidden" name="all_inv_list" id="all_inv_list" value="<?=implode(',',array_unique($pinvno_log_arr));?>"/>
        <div class="block signature_default" style="">
                <br>
                <span style="margin:22px 0px 0px;float:right;"><b>Validated By</b> : _______________<br /></span><br />
                <span style="margin:7px 0px;float:left;;"><b>Picked By</b> : _____________________<br /></span>
        </div>
        <p>&nbsp;</p>

</div>
<script>
//    function print_block();
$(".print_link").click(function() {
    $('#show_picklist_block').printElement({
        printMode:"popup"
        ,pageTitle:"Product Pick List"
        ,leaveOpen:false
        /*,printBodyOptions: {
            styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',
            classNameToAdd : 'wrapper2'}*/
    });
    log_printcount();
});


function log_printcount()
{
    var all_inv_list = $("#all_inv_list").val();
    $.post(site_url+'/admin/jx_update_picklist_print_log','all_inv_list='+all_inv_list,function(resp){ 
        print(resp);
    });
}
</script>