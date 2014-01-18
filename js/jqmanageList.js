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

				if(obj.data('manageListEnabled') == undefined)
                	obj.data('manageListEnabled',false);
                
                
                populateList(obj);
                
                if(!obj.data('manageListEnabled'))
                {
		            $('.manageListOptionsAdd',obj).live('click',function(){
			            $(obj).append('<li>'+$(this).parent().html()+'</li>');
			            
			            console.log($(this).parent().html());
			            $(this).remove();
			            populateList(obj);
			            $('li:last .clearContent',obj).val('');
			            
			        });
	
	
		            $('.manageListOptionsRemove',obj).live('click',function(){
			            $(this).parent().remove();
			            populateList(obj);
			           
			        });
			        
			        obj.data('manageListEnabled',true);
		        }
		        
		        
            });
            function populateList(obj){

		 	    $('.manageListOptions',obj).remove();   
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