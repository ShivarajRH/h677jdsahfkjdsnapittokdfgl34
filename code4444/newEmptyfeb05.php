        function load_credit_notes(pg)
        {
                $.post(site_url+'/admin/jx_getfrancreditnotes/'+pg,'fid='+franchise_id,function(resp){
                        if(resp.status == 'error')
                        {
                                alert(resp.error)
                        }else
                        {
                                $('#credit_notes .module_cont_block_grid_total .total b').text(resp.total);
                                if(resp.fran_crnotelist.length == 0)
                                {
                                        $('#credit_notes .module_cont_block_grid .datagrid tbody').html('<tr><td colspan="12"><div align="center">No Data found</div></td></tr>');			
                                }else
                                {
                                        var crnotelist_html = '';
                                                $.each(resp.fran_crnotelist,function(a,b){
                                                        crnotelist_html += '<tr>'
                                                                                                        +'<td>'+(pg+a+1)+'</td>'
                                                                                                        +'<td>'+b.credit_note_id+'</td>'
                                                                                                        +'<td><a target="_blank" href="'+site_url+'/admin/invoice/'+b.invoice_no+'"><b>'+b.invoice_no+'</b></a></td>'
                                                                                                        +'<td>'+b.order_id+'</td>'
                                                                                                        +'<td>'+b.credit_note_amt+'</td>'
                                                                                                        +'<td>'+formatDateTime(new Date(b.createdon*1000))+'</td>'
                                                                                                +'</tr>';
                                                });
                                        $('#credit_notes .module_cont_block_grid .datagrid tbody').html(crnotelist_html);

                                        $('#credit_notes .module_cont_grid_block_pagi').html(resp.fran_crnotelist_pagi);

                                        $('#credit_notes .module_cont_grid_block_pagi a').unbind('click').click(function(e){
                                                        e.preventDefault();

                                                var link_part = $(this).attr('href').split('/');
                                                var link_pg = link_part[link_part.length-1]*1;
                                                        if(isNaN(link_pg))
                                                                link_pg = 0;

                                                        load_credit_notes(link_pg);	
                                        });

                                }
                        }
                },'json');
        }