<style>.leftcont{display:none}</style>

<?php 
$fran_status_arr=array();
$fran_status_arr[0]="Live";
$fran_status_arr[1]="Permanent Suspension";
$fran_status_arr[2]="Payment Suspension";
$fran_status_arr[3]="Temporary Suspension";
?>

<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Franchise Hygenie Analytics.</h2>
		</div>
		<div class="fl_right stats" style="float:right;margin-bottom:6px;">
			Export to csv :<input type="button" class="button button-flat-highlight" value="Download" id="export_btn">
		</div> 

</div>		
<div class="clearboth">
	<div class="dash_bar" style="float:left;">
	Territory :<select	id="disp_terry" name="disp_terry">
							<option value="0">All</option>
							<?php foreach ($this->db->query("SELECT a.franchise_id,b.territory_id,c.territory_name FROM pnh_m_franchise_info a JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id JOIN `pnh_m_territory_info`c ON c.id=b.territory_id  GROUP BY territory_id order by territory_name asc")->result_array() as $terr_det){?>
			            	<option id="fil_opt_terry_<?php echo $terr_det['territory_id']; ?>" value="<?php echo set_value('disp_terry',$terr_det['territory_id']);?>" <?php echo  $terr_det['territory_id']==$trid?"selected":"";?>>
			            	<?php echo $terr_det['territory_name']?>
			            	</option>
							<?php }?>
	       				</select>
	</div>
	
	  <div class="dash_bar" style="float:left;">
	Town :<select	id="disp_twn" name="disp_twn" style="width: 150px;">
							<option value="0">All</option>
							<?php foreach ($this->db->query("SELECT a.franchise_id,b.town_id,c.town_name FROM pnh_m_franchise_info a JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id JOIN `pnh_towns`c ON c.id=b.town_id  GROUP BY town_id order by town_name asc")->result_array() as $twn_det){?>
			            	<option id="fil_opt_twn_<?php echo $twn_det['town_id'];?>" value="<?php echo set_value('disp_twn',$twn_det['town_id']);?>" <?php echo  $twn_det['town_id']==$twnid?"selected":"";?>>
			            	<?php echo $twn_det['town_name']?>
			            	</option>
							<?php }?>
	       				</select>
	</div>
	
	<div class="dash_bar" style="float:left;">
	Franchisee:<select id="disp_fran" style="width: 250px;">
					<option value="0" >All</option>
					<?php foreach ($this->db->query("SELECT a.franchise_id,b.franchise_name FROM pnh_t_receipt_info a JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id GROUP BY franchise_id
													 order by franchise_name asc")->result_array() as $fran_det){?>
	            	<option id="fil_opt_fr_<?php echo $fran_det['franchise_id']; ?>"  value="<?php echo $fran_det['franchise_id'];?>"
	            	<?php echo  $fran_det['franchise_id']==$fid?"selected":"";?>>
	            	
	            	<?php echo $fran_det['franchise_name']?>
	            	</option>
					<?php }?>
	       	</select>
	</div>
	<div class="dash_bar show_menu" style="float:left;display:none;">
	Menu:<select id="disp_menu" style="width:150px">
	</select>
	</div>
</div>



<div id="tbl_cont" style="overflow-x: scroll;scrollbar-base-color:#ffeaff;clear:both" >
 <table class="datagrid" width="100%"  >

<thead>
<th>Sl no</th><th>Territory</th><th>Town</th><th>Franchise</th><th>Created on</th>
<th>Last Orderd On</th><th>Current Week Sales</th><th>Week<br>(<?php echo $fortwkdaterange['startdate'].' - '.$fortwkdaterange['endate'];?>)</th>
<th>Week<br>(<?php echo $thirdwkdaterange['startdate'].'-'.$thirdwkdaterange['endate'];?>)</th><th>Week<br>(<?php echo $secwkdaterange['startdate'].'-'.$secwkdaterange['endate'];?>)</th><th>Week<br>(<?php echo $frstwkdaterange['startdate'].'-'.$frstwkdaterange['endate'];?>)</th><th>Month<br>(<?php echo $last_monthdesc;?>)</th><th>Month<br>(<?php echo $last_secmonth;?>)</th><th>Month<br>(<?php echo $last_thrdcmonth;?>)</th><th>Month<br>(<?php echo $last_forthcmonth;?>)</th><th>Sales Till Date</th><th>Current Month Top Selling Category</th><th>Top most Selling Category last month<br>(<?php echo $last_monthdesc;?>)</th><th>2nd Most Selling Category last month<br>(<?php echo $last_monthdesc;?>)</th><th>Total Members</th><th>Current Pending Amount</th><th>Uncleared Cheque</th><th>Credit Limit</th><th>Last Shipment Value</th>
<th>Last week No of Transactions</th><th>Last week No of Orders<th><th>	Last week Total Order Qty</th>
<th>Last Month No of Transactions</th><th>Last Month No of Orders</th><th>Last Month Total Order Qty</th>
<th>Suspension Status</th>
</thead>
<tbody>
<?php $i=1; foreach($fran_bio_res as $fr_bio_det){?>
<tr class="franchises_det" fr_id="<?php echo $fr_bio_det['franchise_id'] ?>" twn_id="<?php echo $fr_bio_det['town_id'] ?>" terry_id="<?php echo $fr_bio_det['territory_id']?>">

<td><?php echo $i;?></td><td><?php echo $fr_bio_det['territory_name']?></td><td><?php echo $fr_bio_det['town_name']?></td><td><?php echo $fr_bio_det['franchise_name']?></td><td><?php echo format_date_ts($fr_bio_det['created_on'])?></td>
<?php $last_ordate=@$this->db->query("SELECT t.init FROM king_orders o  JOIN king_transactions t ON t.transid=o.transid  where franchise_id=? ORDER BY t.init DESC LIMIT 1",$fr_bio_det['franchise_id'])->row_array() ;?>
<td><?php echo format_datetime_ts($last_ordate['init'])?format_datetime_ts($last_ordate['init']):'--na--';?></td>
<?php $curwk_sales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(CURDATE()) AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $curwk_sales['ttl_sales']?$curwk_sales['ttl_sales']:0 ;?></td>
<?php $forth_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 4 WEEK)) AND a. franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $forth_wksales['ttl_sales']?$forth_wksales['ttl_sales']:0 ;?></td>

<?php $thrd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 3 WEEK)) AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $thrd_wksales['ttl_sales']?$thrd_wksales['ttl_sales']:0 ;?></td>
<?php $secnd_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $secnd_wksales['ttl_sales']?$secnd_wksales['ttl_sales']:0 ;?></td>
<?php $one_wksales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   WEEK(DATE(FROM_UNIXTIME(a.init)))=WEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 1 WEEK))  AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $one_wksales['ttl_sales']?$one_wksales['ttl_sales']:0 ;?></td>
<?php $last_monthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))  AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $last_monthsales['ttl_sales']?$last_monthsales['ttl_sales']:0 ;?></td>
<?php $last_secmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)) AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $last_secmonthsales['ttl_sales']?$last_secmonthsales['ttl_sales']:0 ;?></td>
<?php $last_thrdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)) AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $last_thrdmonthsales['ttl_sales']?$last_thrdmonthsales['ttl_sales']:0 ;?></td>
<?php $last_forthdmonthsales=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE   MONTH(DATE(FROM_UNIXTIME(a.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 4 MONTH)) and year(DATE(FROM_UNIXTIME(a.init))) = year(DATE_SUB(CURDATE(), INTERVAL 4 MONTH))  AND a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo $last_forthdmonthsales['ttl_sales']?$last_forthdmonthsales['ttl_sales']:0 ;?></td>
<?php $ttlsales_tildate=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS ttl_sales  FROM king_transactions a  JOIN king_orders b ON a.transid = b.transid JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id WHERE a.franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td> Rs <?php echo formatInIndianStyle($ttlsales_tildate['ttl_sales']?$ttlsales_tildate['ttl_sales']:0 );?></td>
<?php 
if($menuid) 
		$cond ='AND l.id = '.$menuid;
else 
		$cond='';
	$curmonth_topcat=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*o.quantity),2) AS ttl_sales,d.menuid,m.name AS menu,SUM(o.quantity) AS sold
											FROM king_deals d JOIN king_dealitems i ON i.dealid=d.dealid  
											JOIN king_categories c ON c.id=d.catid  
											JOIN king_orders o ON o.itemid=i.id 
											JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1 
											JOIN pnh_menu m ON m.id=d.menuid 
											WHERE i.is_pnh=1 AND MONTH(DATE(FROM_UNIXTIME(t.init)))=MONTH(CURDATE()) AND t.franchise_id=? 
											ORDER BY sold DESC
											LIMIT 1",$fr_bio_det['franchise_id'])->row_array();
?>									
<td>  <?php echo $curmonth_topcat['menu'].' - Rs'.formatInIndianStyle($curmonth_topcat['ttl_sales'])?$curmonth_topcat['menu'].'-Rs'.formatInIndianStyle($curmonth_topcat['ttl_sales']):'--na--';?></td>
<?php $lastmonth_topcat=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*o.quantity),2) AS ttl_sales,d.menuid,m.name AS menu,SUM(o.quantity) AS sold
											FROM king_deals d JOIN king_dealitems i ON i.dealid=d.dealid  
											JOIN king_categories c ON c.id=d.catid  
											JOIN king_orders o ON o.itemid=i.id 
											JOIN king_transactions t ON t.transid=o.transid AND t.is_pnh=1 
											JOIN pnh_menu m ON m.id=d.menuid 
											WHERE i.is_pnh=1 AND MONTH(DATE(FROM_UNIXTIME(t.init)))=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND t.franchise_id=? 
											ORDER BY sold DESC
											LIMIT 2",$fr_bio_det['franchise_id'])->result_array();


?>
<td>  <?php echo $lastmonth_topcat[0]['menu'].' - Rs'.formatInIndianStyle($lastmonth_topcat[0]['ttl_sales']) ? $lastmonth_topcat[0]['menu'].'-  Rs '.formatInIndianStyle($lastmonth_topcat[0]['ttl_sales']):'--na--';?></td>
<td>  <?php echo $lastmonth_topcat[1]['menu'].' - Rs'.formatInIndianStyle($lastmonth_topcat[1]['ttl_sales']) ? $lastmonth_topcat[1]['menu'].'-  Rs '.formatInIndianStyle($lastmonth_topcat[1]['ttl_sales']):'--na--';?></td>

<?php $ttl_mem_reg=$this->db->query("SELECT count(*) as ttl_regmem FROM pnh_member_info  WHERE franchise_id=? ",$fr_bio_det['franchise_id'])->row_array();?>
<td><?php echo $ttl_mem_reg['ttl_regmem'];?></td>
<?php 
$acc_statement = $this->erpm->get_franchise_account_stat_byid($fr_bio_det['franchise_id']);
$net_payable_amt = $acc_statement['net_payable_amt'];
	$credit_note_amt = $acc_statement['credit_note_amt'];
	$shipped_tilldate = $acc_statement['shipped_tilldate'];
	$paid_tilldate = $acc_statement['paid_tilldate'];
	$uncleared_payment = $acc_statement['uncleared_payment'];		
	$cancelled_tilldate = $acc_statement['cancelled_tilldate'];
	$ordered_tilldate = $acc_statement['ordered_tilldate'];
	$not_shipped_amount = $acc_statement['not_shipped_amount'];
	$acc_adjustments_val = $acc_statement['acc_adjustments_val'];
	$pending_payment = formatInIndianStyle($shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt),2);

 ?>

<td>Rs <?php echo  $pending_payment;?></td>
<td>Rs <?php echo  formatInIndianStyle($uncleared_payment?$uncleared_payment:0);?></td>	
<td>Rs <?php echo formatInIndianStyle($fr_bio_det['credit_limit']?$fr_bio_det['credit_limit']:0)?></td>	
<?php $last_shipped_amt=$this->db->query("SELECT DISTINCT d.franchise_id,c.amount,o.transid
											FROM shipment_batch_process_invoice_link sd
											JOIN proforma_invoices b ON sd.p_invoice_no = b.p_invoice_no
											JOIN king_transactions c ON c.transid = b.transid
											JOIN king_orders o ON o.id = b.order_id  
											JOIN pnh_member_info pu ON pu.user_id=o.userid 
											JOIN pnh_m_franchise_info d ON d.franchise_id = c.franchise_id
											JOIN pnh_m_territory_info f ON f.id = d.territory_id
											JOIN pnh_towns e ON e.id = d.town_id 
											JOIN king_dealitems dl ON dl.id=o.itemid
											JOIN king_deals deal ON deal.dealid=dl.dealid
											JOIN king_brands br ON br.id = deal.brandid 
											JOIN pnh_menu m ON m.id = deal.menuid 
											WHERE o.status = 2 AND sd.shipped = 1 AND c.is_pnh = 1 AND d.franchise_id=? 
											GROUP BY b.transid 
											ORDER BY sd.shipped_on DESC LIMIT 1",$fr_bio_det['franchise_id'])->row_array();?>
<td>Rs <?php echo formatInIndianStyle($last_shipped_amt['amount']?$last_shipped_amt['amount']:0);?></td>		
<?php $lst_wk_no_of_trans=$this->db->query("SELECT COUNT(*) AS ttl,WEEK(DATE(FROM_UNIXTIME(init))) FROM king_transactions WHERE WEEK(DATE(FROM_UNIXTIME(init)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td><?php echo $lst_wk_no_of_trans['ttl']?$lst_wk_no_of_trans['ttl']:0?></td>	
<?php $last_wk_ttl_orders=$this->db->query("SELECT COUNT(*) AS ttl,WEEK(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE WEEK(DATE(FROM_UNIXTIME(time)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>								
<td><?php echo $last_wk_ttl_orders['ttl']?$last_wk_ttl_orders['ttl']:0;?></td>
<td></td>
<?php $lst_wk_ttl_qty=$this->db->query("SELECT SUM(o.quantity) AS ttl,WEEK(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE WEEK(DATE(FROM_UNIXTIME(time)))=WEEK(DATE_SUB(CURDATE(),INTERVAL 1 WEEK)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td><?php echo $lst_wk_ttl_qty['ttl']?$lst_wk_ttl_qty['ttl']:0;?></td>
<?php $lst_mnth_no_of_trans=$this->db->query("SELECT COUNT(*) AS ttl,MONTH(DATE(FROM_UNIXTIME(init))) FROM king_transactions WHERE MONTH(DATE(FROM_UNIXTIME(init)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td><?php echo $lst_mnth_no_of_trans['ttl']?$lst_mnth_no_of_trans['ttl']:0;?></td>

<?php $last_mnth_ttl_orders=$this->db->query("SELECT COUNT(*) AS ttl,MONTH(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE MONTH(DATE(FROM_UNIXTIME(time)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>								
<td><?php echo $last_mnth_ttl_orders['ttl']?$last_mnth_ttl_orders['ttl']:0;?></td>

<?php $lst_month_ttl_qty=$this->db->query("SELECT SUM(o.quantity) AS ttl,MONTH(DATE(FROM_UNIXTIME(time))) FROM king_orders o join king_transactions t on t.transid=o.transid WHERE MONTH(DATE(FROM_UNIXTIME(time)))=MONTH(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) AND franchise_id=?",$fr_bio_det['franchise_id'])->row_array();?>
<td><?php echo $lst_month_ttl_qty['ttl']?$lst_month_ttl_qty['ttl']:0;?></td>

<td><?php echo $fran_status_arr[$fr_bio_det['is_suspended']]?></td>

</tr>
</tbody>
<?php $i++;}?>






</table>


</div>

</div>
<script>
$(".dash_bar show_menu").hide();
//$(".datagrid tbody tr").hide();

$(document).ready(function () {

    function exportTableToCSV($table, filename) {

    	var $rows = $table.find('thead,tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"', 
            
            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('th,td');
                	

                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $.trim($col.text());
                    return text.replace('"', '""'); // escape double quotes

                }).get().join(tmpColDelim);

            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',

            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        $(this)
            .attr({
            'download': filename,
                'href': csvData,
                'target': '_blank'
        });
    }
   
    $("#export_btn").live('click', function (event) {
    	var export_report='1';
    	var print_url = site_url+'/admin/pnh_export_franchise_analytics_report/'+export_report;
    		window.open(print_url);
    });

	$('#tbl_cont').width($(document).width()-30);

});

$('#disp_fran option:gt(0)').addClass('valid_opt').hide();
$('#disp_terry option:gt(0)').addClass('valid_opt').hide();
$('#disp_twn option:gt(0)').addClass('valid_opt').hide();

$(function(){
	$('.franchises_det').each(function(){
		$('#fil_opt_fr_'+$(this).attr('fr_id')).removeClass('valid_opt').show();
		$('#fil_opt_twn_'+$(this).attr('twn_id')).removeClass('valid_opt').show();
		$('#fil_opt_terry_'+$(this).attr('terry_id')).removeClass('valid_opt').show();
	;
	});
	$('#disp_fran option.valid_opt').remove();	
	$('#disp_terry option.valid_opt').remove();
	$('#disp_twn option.valid_opt').remove();
});

$(function(){
	
var fid=$('#disp_fran').val()?$('#disp_fran').val():0;
var trid=$('#disp_terry').val()?$('#disp_terry').val():0;
var twnid=$('#disp_twn').val()?$('#disp_twn').val():0;
var menuid=$('#disp_menu').val()?$('#disp_menu').val():0;

/*if(fid == 0 && trid==0 && twnid==0 && menuid==0 )
	$(".datagrid tbody tr").hide();
else
	$(".datagrid tbody tr").show();*/

$('#disp_fran').change(function(){
	fid=$(this).val();
	location="<?=site_url("admin/fr_hyg_anlytcs_report/")?>/"+trid+'/'+twnid+'/'+fid+'/'+menuid;
	$('.datagrid tbody tr').show();
	
});


$('#disp_terry').change(function(){
	trid=$(this).val();
	location="<?=site_url("admin/fr_hyg_anlytcs_report/")?>/"+trid+'/'+twnid+'/'+fid+'/'+menuid;	
	$('.datagrid tbody tr').show();
});



$('#disp_twn').change(function(){
	twnid=$(this).val();
	location="<?=site_url("admin/fr_hyg_anlytcs_report/")?>/"+trid+'/'+twnid+'/'+fid+'/'+menuid;
	$('.datagrid tbody tr').show();
});



	if(fid!='0')
	{
		$(".show_menu").show();
		$("#disp_menu").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/get_menu_byfranchiseid/'+fid,'',function(resp){
			var menu_html='';
				if(resp.status=='error')
				{
					alert(resp.message);
				}
				else
				{
					
					menu_html+='<option value="0">All</option>';
					$.each(resp.menu_list,function(i,b){
					menu_html+='<option value="'+b.menuid+'">'+b.name+'</option>';
					});
				}
		 	$('#disp_menu').html(menu_html).trigger("liszt:updated");
		 //	$('#disp_menu').trigger('change');
		});

		$('#disp_menu').change(function(){
			menuid=$(this).val();
			location="<?=site_url("admin/fr_hyg_anlytcs_report/")?>/"+trid+'/'+twnid+'/'+fid+'/'+menuid;
		});
	
	
	$('.datagrid tbody tr').show();
	
	}




});
</script>