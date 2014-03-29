<div id="container">
<h2 class="page_title">Test SMS </h2>

<form target="hndl_smsinput" action="<?php echo base_url()?>/pnh_sms.php" method="get" >
	<table>
		<tr>
			<td valign="top">
				<table>
					<tr>
						<td><b>From</b></td>
						<td><input type="text" name="From" value="" maxlength="11"></td>
					</tr>
					<tr>
						<td><b>SMS Text</b></td>
						<td><textarea name="Body" style="width: 400px;height: 30px;"></textarea></td>
					</tr>
					<tr>
						<td colspan="2" align="left">
							<input type="submit" value="submit" >
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<iframe id="hndl_smsinput" name="hndl_smsinput" style="width: 500px;height: 300px;"></iframe>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
</div>