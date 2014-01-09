<div class="container">
<a href="<?php echo site_url("admin/pnh_franchise/$fid")?>"style="font-size: 18px;color: red;"><?=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name?></h2><h1 style="font-size: 11px;color:black;">(back)</h1></a>
<h2>Upload photos of franchise</h2>
<form method="post" enctype="multipart/form-data">
<div id="target">
<div align="right">
<input type="button" value="+" onclick='add_img()'>
</div>
</div>
<div>
<input type="submit" value="Upload">
</div>
</form>
</div>
<style>
#target .img{
padding:5px;
}
#target{
padding:5px;
padding-bottom:15px;
margin-bottom:10px;
border:1px solid #aaa;
background:#fafafa;
display:inline-block;
}
</style>

<script>
var a_i=0;
function add_img()
{
	a_i++;
	$("#target").append('<div class="img">Photo '+a_i+' : <input type="file" name="pic[]"></div>');
}
$(function(){
	add_img();
});
</script>
<?php
