<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/manage_reservations_style.css" />

<div class="container">
    <div class="container_head">
        <div>
                <h2>Manage Transaction Reservations</h2>
                <div class="above_header_block_btns">
                    <div class="process_by_fran_link"></div>
                </div>
            </div>
    </div>
    <div class="left_block fl_left">
            
            <div class="clear"></div>
            <div id="list_wrapper">
                <table width="100%" >
                        <tr>
                                <td width="60%">
                                        <div class="tab_list" style="clear: both;">
                                                    <ol>
                                                            <li><a class="load_type selected" id="ready" href="javascript:void(0)" title="Transactions are ready for shipping">READY</a><div class="ready_pop"></div></li>
                                                            <li><a class="load_type" id="partial" href="javascript:void(0)" title="Transactions are partial ready for shipping">PARTIAL</a><div class="partial_pop"></div></li>
                                                            <li><a class="load_type" id="pending" href="javascript:void(0)" title="Transactions are pending for shipping">PENDING</a><div class="pending_pop"></div></li>
                                                            <li><a class="load_type" id="assigned_batches" href="javascript:void(0)" title="Transactions are batched for shipping">Assigned Batches</a><div class="batch_pop"></div></li>
                                                    </ol>
                                            </div>
                            </td>
                        </tr>
                </table>
            </div>
        <!--<p class="page_trans_description"></p>-->
            <div class="level1_filters">
                <fieldset>
                    <span title="Toggle Filter Block" class="close_filters"><span class="close_btn">Show</span>
                        <h3 class="filter_heading">Filters:</h3>
                    </span>
                        <div class="filters_block">
                                
                                <div class="group_filter">
                                    <select id="sel_menu" name="sel_menu" colspan="2">
                                        <option value="00">Select Menu</option>
                                         <?php /*foreach($pnh_menu as $menu): ?>
                                                <option value="<?php echo $menu['id'];?>"><?php echo $menu['name'];?></option>
                                        <?php endforeach;*/ ?>
                                    </select> &nbsp;
                                    <select id="sel_brands" name="sel_brands">
                                        <option value="00">Select Brands</option>
                                         <?php /* foreach($pnh_brands as $brand): ?>
                                                <option value="<?php echo $brand['id'];?>"><?php echo $brand['name'];?></option>
                                        <?php endforeach; */?>
                                    </select>


                                    <select id="sel_territory" name="sel_territory" >
                                        <option value="00">All Territory</option>
                                        <?php /* foreach($pnh_terr as $terr):?>
                                                <option value="<?php echo $terr['id'];?>"><?php echo $terr['territory_name'];?></option>
                                        <?php endforeach; */ ?>
                                    </select>
                                    <select id="sel_town" name="sel_town">
                                        <option value="00">All Towns</option>
                                        <?php /*foreach($pnh_towns as $town): ?>
                                                <option value="<?php echo $town['id'];?>"><?php echo $town['town_name'];?></option>
                                        <?php endforeach; */ ?>
                                    </select>
                                    <select id="sel_franchise" name="sel_franchise" style="width: 204px;">
                                        <option value="00">All Franchise</option>
                                    </select>
                                    <span>Batch Group Status:
                                        <select id="sel_batch_group_type" name="sel_batch_group_type" style="width: 204px;">
                                            <option value="00">Any</option>
                                            <option value="1">Grouped</option>
                                            <option value="2">Un-Grouped</option>
                                        </select>
                                    </span>
                                </div>
                                <div class="clear"></div>
                                <div>
                                    <span class="limit_display_block">
                                        Show
                                            <select name="limit_filter" id="limit_filter">
                                                <option value="20" selected>20</option>
                                                <option value="50" >50</option>
                                                <option value="100">100</option>
                                            </select>
                                        items per page.
                                    </span>
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

                        </div>
                        <input type="hidden" name="pg_num" class="page_num" value="0" size="3" />
                </fieldset>
            </div>
            <div class="clear"></div>
            <div class="level2_filters">
                    <div class="trans_pagination pagination_top"></div>

                    
                    <div class="oldest_newest_sel_block"><select name="sel_old_new" id="sel_old_new"><option value="1" selected>NEWEST</option><option value="0" <?=($oldest_newest=='0') ? "selected":""; ?> >OLDEST</option></select></div>
                    <div class="sel_terr_block"></div>

                    <span class="ttl_trans_listed dash_bar"></span>

            </div>
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

<script type="text/javascript" src="<?=base_url()?>js/manage_trans_reservations_script.js"></script>
<script>
// <![CDATA[
   
// ]]>
</script>

<?php
