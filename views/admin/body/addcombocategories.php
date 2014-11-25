<?php $c=false; if(isset($category_val)) $c=$category_val;?>
<div class="container">
<h2>Edit Combo Category</h2>
<div class="listpage">
	<div >
		<form method="post" id="member_plan_detail"  data-validate="parsley">
			<table  cellpadding="8">		
				<tbody>
					<tr>
						<td>Gender Attribute:</td>
						<td colspan="2">
							<select name="genderval" id="genderval">
							<option value="">Select Gender</option>
							<option value="MEN" <?=$c?($c['gender_attr']=='MEN'?"selected":""):""?>>Men</option>
							<option value="WOMEN" <?=$c?($c['gender_attr']=='WOMEN'?"selected":""):""?>>Women</option>
							<option value="BABY" <?=$c?($c['gender_attr']=='BABY'?"selected":""):""?>>Baby</option>
							</select><span style="color: red;"> <?php if(validation_errors()) { echo form_error('genderval');	}?></span>
						</td>
						
					</tr>
						<tr>
						<td>Category Name:</td>
						<td colspan="2">
							<select name="categoryval" id="categoryval">
							<option value="">Select Category</option>
							<?php foreach($category_list as $list){?>
							<option value="<?php echo $list['id'];?>" <?=$c?($c['catid']==$list['id']?"selected":""):""?>><?php echo $list['name'];?></option>
						<?php } ?>
							</select><span style="color: red;"> <?php if(validation_errors()) { echo form_error('categoryval');	}?></span>
						</td>
						
					</tr>
					
					<tr>
						<td colspan="3">
							<div align="right"><input type="submit" name="submit" value="Submit" class="dark-button"></div>
						</td>
					</tr>
					
				</tbody>
			</table>
		</form>
	</div>
</div>

</div>



<script>




</script>
<style>
/** PAGE CSS**/
.listpage{
	<!--width:1000px;
	margin: 0 auto; -->
	position:relative;
}
.listpage-title{
	font-size: 18px;
	margin-top:10px;
	}
.listpage-totalitems{font-size: 13px;margin-right: 10px;}

/** Button CSS **/
.button{font-size: 11px;font-weight: bold;background: #e3e3e3;color: #676767;padding:4px 10px;text-decoration: none;text-transform: uppercase;}
.dark-button{background: #676767;color: #f3f3f3}
.dark-button:hover{background: #676767;color:#FFF}

.light-button{background: #f3f3f3;color: #676767}
.light-button:hover{background: #f3f3f3;color:#FFF}

input[type="text"],input[type="password"],select,textarea,file{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}

input[type="password"],input[type="file"]{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}
/** block CSS **/
.block_300{width: 300px;}
.block_100{width: 100px;}

.min_block{margin-top:40;width:"500";height:"500";}

.left{
	float:left;
}
.right{
	float:right
}

.error_inp {
	color: #cd0000;
}

.add-button{
	text-align:center;
	width:20px;
	background: #676767;
	color: #f3f3f3;
	cursor: pointer;
	font-weight:bolder;
	font-style:bold;
}

.remove-button{
	text-align:center;
	width:20px;
	background: #676767;
	color: #f3f3f3;
	cursor: pointer;
	font-weight:bolder;
	font-style:bold;
}

.add-button:hover{
	background: #676767;
	color:#FFF;
}
.remove-button:hover{
	background: #676767;
	color:#FFF;
}
/** Button CSS **/
.button{font-size: 11px;font-weight: bold;background: #e3e3e3;color: #676767;padding:4px 10px;text-decoration: none;text-transform: uppercase;}
.dark-button{background: #676767;color: #f3f3f3}
.dark-button:hover{background: #676767;color:#FFF}

.light-button{background: #f3f3f3;color: #676767}
.light-button:hover{background: #f3f3f3;color:#FFF}

input[type="text"],input[type="password"],select,textarea,file{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}

input[type="password"],input[type="file"]{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}
</style>
<?php
