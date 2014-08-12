<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/manage_reservations_style.css" />

<div class="container">
    <div class="container_head">
        <form id="head_filter_form" name="head_filter_form">
            <div>
                <h2>Manage Transaction Reservations</h2>
                <div class="above_header_block_btns">
                    <div class="process_by_fran_link"></div>
                </div>
            </div>
        </form>
    </div>
    <div class="left_block fl_left">
            
            <div class="clear"></div>
            <div id="list_wrapper">
                <table width="100%" >
                        <tr>
                                <td width="60%">
                                        <div class="tab_list" style="clear: both;">
                                                    <ol>
                                                        <?php $selected='';
                                                        if($this->erpm->auth(true,true)) {
                                                        ?>
                                                            <li><a class="load_type selected" id="ready" href="javascript:void(0)" title="Transactions are ready for shipping">READY</a><div class="ready_pop"></div></li>
                                                            <li><a class="load_type" id="partial" href="javascript:void(0)" title="Transactions are partial ready for shipping">PARTIAL</a><div class="partial_pop"></div></li>
                                                            <li><a class="load_type" id="pending" href="javascript:void(0)" title="Transactions are pending for shipping">PENDING</a><div class="pending_pop"></div></li>
                                                            <li><a class="load_type" id="trans_disabled" href="javascript:void(0)" title="Transactions are disabled for shipping">Transaction Disabled</a></li>
                                                        <?php
                                                        }
                                                        else {
                                                            $selected=' selected ';
                                                        }
                                                        ?>
                                                            <li><a class="load_type <?=$selected;?>" id="assigned_batches" href="javascript:void(0)" title="Transactions are batched for shipping">Assigned Batches</a><div class="batch_pop"></div></li>
                                                    </ol>
                                        </div>
                                </td>
                        </tr>
                </table>
            </div>
            <div class="level1_filters">
                <fieldset>
                    <span title="Toggle Filter Block" class="close_filters"><span class="close_btn">Show</span>
                        <h3 class="filter_heading">Filters:</h3>
                    </span>
                        <div class="filters_block">
                                <div class="fl_right"><a class="reset_all button button-rounded button-tiny button-caution" onclick="javascript:btn_fn_reset_filters();">Reset</a></div>
                                <form action="" name="form_filters" id="form_filters">
                                    <div class="group_filter">
                                        <select id="sel_menu" name="sel_menu" colspan="2">
                                            <option value="00">Select Menu</option>
                                        </select> &nbsp;
                                        <select id="sel_brands" name="sel_brands">
                                            <option value="00">Select Brands</option>
                                        </select>


                                        <select id="sel_territory" name="sel_territory" >
                                            <option value="00">All Territory</option>
                                        </select>
                                        <select id="sel_town" name="sel_town">
                                            <option value="00">All Towns</option>
                                        </select>
                                        <select id="sel_franchise" name="sel_franchise" style="width: 204px;">
                                            <option value="00">All Franchise</option>
                                        </select>
                                        <span>Batch Group Type:
                                            <select id="sel_batch_group_type" name="sel_batch_group_type" style="width: 104px;">
                                                <option value="00">Any</option>
                                                <option value="1">Grouped</option>
                                                <option value="2">Un-Grouped</option>
                                            </select>
                                        </span>
                                    </div>

                                    <div class="clear"></div>
                                    <span class="limit_display_block">
                                        Show
                                            <select name="limit_filter" id="limit_filter">
                                                <option value="20" selected>20</option>
                                                <option value="50" >50</option>
                                                <option value="100">100</option>
                                            </select>
                                        items per page.
                                    </span>
                                </form>
                                <div class="date_filter">
                                    <form id="trans_date_form" method="post">
                                            <b>Show transactions : </b>
                                            <label for="date_from">From :</label><input type="text" id="date_from"
                                                    name="date_from" value="<?php //echo date('Y-m-01',time()-60*60*24*7*4*4)?>" />
                                            <label for="date_to">To :</label><input type="text" id="date_to"
                                                    name="date_to" value="<?php //echo date('Y-m-d',time())?>" /> 
                                            <input type="submit" value="Submit" class="button button-tiny button-royal">
                                    </form>
                                </div>
                        </div>
                        <input type="hidden" name="pg_num" class="page_num" value="0" size="3" />
                </fieldset>
                
            </div>
            <div class="clear"></div>
            <form id="form_filters_2" name="form_filters_2">
                <div class="level2_filters">
                        <div class="trans_pagination pagination_top"></div>

                        <div class="chk_latest_batch">
                            <label for="latest_batches">Latest Batches</label>
                            <input type="checkbox" id="latest_batches" name="latest_batches" value="<?=isset($latest_batches)? 1 : 0; ?>" <?=isset( $latest_batches ) ? 'checked' : "" ; ?> /></div>

                        <div class="oldest_newest_sel_block"><select name="sel_old_new" id="sel_old_new"><option value="1" selected>NEWEST</option><option value="0" <?=( isset($oldest_newest) && $oldest_newest=='0') ? "selected":""; ?> >OLDEST</option></select></div>
                        <div class="block_alloted_status">
			    <select id="sel_alloted_status" name="sel_alloted_status">
				<option value="0" <?=( isset($alloted_status) && $alloted_status == 0 ? 'selected' : "" ); ?> >Not Alloted</option>
				<option value="1" <?= ( isset($alloted_status) && $alloted_status===1 ? 'selected':'') ?> >Alloted</option>
			    </select></div>

                        <div class="sel_terr_block"></div>
                        <div class="sel_terr_block"></div>
                        <span class="ttl_trans_listed dash_bar"></span>

                </div>
            </form>
            <div id="trans_list_replace_block"></div>
    </div>
    
    <div class="right_block fl_right">
        
        <div style="position: fixed; display: table;" class="">
            <div class="right_block_head">&nbsp;</div>
            
            <div class="clear">&nbsp;</div>
            <div class="batch_btn_link"></div>
            
            <div class="clear">&nbsp;</div>
            <div class="btn_picklist_block"></div>
            
            <div class="clear">&nbsp;</div> 
            <div class="re_allot_all_block"></div>
        </div>
        
    </div>
    
</div>

<!-- Dialog: Picklist dialog block -->
<div id="show_picklist_block" style="display: none;" ></div>

<!-- Dialog: -->
<div style="display: none;">
    <div id="dlg_create_group_batch_block"  ></div>
    <div class="reservation_action_status" ></div>
</div>

<script type="text/javascript" src="<?=base_url()?>/min/index.php?g=reservations_js&<?php echo strtotime(date('Y-m-d'));?>&1=1"></script>
<script>
// <![CDATA[
   
// ]]>
</script>

<?php
