<?php $c=false;if(isset($cat)) $c=$cat;?>
<div class="container">
<h2><?=$c?"Edit":"Add"?> category</h2>
<form method="post">
Category Name : <input type="text" class="inp" name="cat_name" value="<?=$c?$c['name']:""?>">
<br><br>
Main Category : <select name="main">
<option value="0">no main category</option>
<?php foreach($this->db->query("select id,name from king_categories where 1 order by name asc")->result_array() as $m){?>
<option <?=$c&&$m['id']==$c['type']?"selected":""?> value="<?=$m['id']?>"><?=$m['name']?></option>
<?php }?>
</select><br><br>
Has Attributes? <input type="checkbox" name="has_attributes" id="has_attributes" value="1" <?= ($c['attribute_ids']=='') ? '' : "checked"; ?> >
<br><br>
<div class="attributes_block hide">
    <ul class="manageList">
        <?php
            $arr_attrids = explode(",",$c['attribute_ids']);
        
            foreach($arr_attrids as $ed_attr_id) {
        ?>
        <li>
                    <select name="attributes[]" class="attributes">
                        <option value="00">None</option>
                    <?php
                        foreach($attr_list as $attr)
                        { 
                     ?>
                                <option value="<?=$attr['attr_id'];?>" <?=(($attr['attr_id'] === $ed_attr_id)? "selected":'');?> ><?=$attr['attr_name'];?></option>
                        <?php
                        }
                        ?>
                    </select>
        </li>
        
    <?php 
                                               
            } ?>
    </ul>
   
</div>
<input type="submit" value="<?=$c?"Update":"Add"?>">
</form>
</div>
<style>
    select {
        width: 220px;
        margin-right: 54px;
    }
    .add_attr { margin-left: 220px; }
    .hide { display:none;}
</style>
<script>
    $("#has_attributes").change(function() {
        if($(this).is(":checked")) {
            $(".attributes_block").removeClass("hide");
        }
        else {
            $(".attributes_block").addClass("hide");
        }
    }).trigger("change");
    
</script>
<style type="text/css">
.manageListOptions{
	font-weight:bold;
	font-size:14px;
	cursor:pointer;
	background:#555;
	color:#FFF;
	padding:1px 4px;
	margin:1px;
	border-radius:5px;
}
</style>  

<script type="text/javascript">
    	(function($){
	    $.fn.extend({
	        //plugin name - animatemenu
	    	manageList: function(options) {
	 
	            //Settings list and the default values
	            var defaults = {
	                animatePadding: 60,
	                defaultPadding: 10,
	                evenColor: '#ccc',
	                oddColor: '#eee'
	            };
	             
	            var options = $.extend(defaults, options);
	         
	            return this.each(function() {
	                var o =options;
	                 
	                //Assign current element to variable, in this case is UL element
	                var obj = $(this);             

	                populateList(obj);
	                
		            $('.manageListOptionsAdd').live('click',function(){
			            $(obj).append('<li>'+$(this).parent().html()+'</li>');
			            $(this).remove();
			            populateList(obj);
			            $('li:last .clearContent',obj).val('');
			        });


		            $('.manageListOptionsRemove').live('click',function(){
			            $(this).parent().remove();
			            populateList(obj);
			           
			        });
			        
	            });
	            function populateList(obj){

			 	    $('.manageListOptions').remove();   
		        	//Get all LI in the UL
	                var items = $("li", obj);
	                var ttl_items  = items.length;
	                    items.each(function(i,ele){
		                    if(ttl_items > 1){
		                    	$(this).append('<span class="manageListOptionsRemove manageListOptions">x</span>');
		                    }
		                    if(ttl_items == i+1){
			                    $(this).append('<span class="manageListOptionsAdd manageListOptions">+</span>');
			                } 
		                });    
			   }
	        },
	       
	    });
	})(jQuery);

	$('.manageList').manageList();
</script>

<?php
