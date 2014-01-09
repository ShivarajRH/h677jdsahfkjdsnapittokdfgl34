<div class="container">
	<h2>Download PNH Sales by Deal</h2>
	<div class="clearboth">
		<form action="" method="post" target="hndl_deallistdownload">
			<table>
				<tr>
					<td colspan="4">
						<b>Deal</b>:
						<input type="text" size="60" id="suggestdeal" value="">
						<input type="hidden" size="30" name="dealid" value="">
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<b>From</b>:<input type="text" size="10" id="from" name="from" value="">
				 		<b>To</b>:<input type="text" size="10" id="to" name="to" value="">
				 	</td>
				</tr>
				<tr>
					<td colspan="2" align="left"><input type="submit" value="Download"></td>
				</tr>
			</table>
		</form>
		<iframe id="hndl_deallistdownload" name="hndl_deallistdownload" style="width: 1px;height: 1px;border:0px;"></iframe>
	</div>
</div>
<script type="text/javascript">
	prepare_daterange('from','to');
	$( "#suggestdeal" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: site_url+'admin/jx_suggestdealsbykwd',
          dataType: "json",
          type:"POST",
          data: {
            kwd: request.term
          },
          success: function( data ) {
            response( $.map( data, function( item ) {
              return {
                label: item.label,
                value: item.label,
                id: item.id,
              }
            }));
          }
        });
      },
      minLength: 2,
      select: function( event, ui ) {
        $('input[name="dealid"]').val(ui.item.id);
      },
      open: function() {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });
    
    
    
	
</script>