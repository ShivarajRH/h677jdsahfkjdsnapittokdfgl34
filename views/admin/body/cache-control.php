<div class="container">
<h3>Cache Control</h3>

<?php if(isset($msg)){?>
<div align="center" style="padding:5px;background:#fff;color:green;margin:10px;"><?=$msg?></div>
<?php }?>

<table width="100%">
<tr>
	<td align="centeR"><form method="post"><input type="hidden" name="msg" value="Menu"><input type="submit" name="menu" value="Clear Menu"></form></td>
	<td align="center"><form method="post"><input type="hidden" name="msg" value="Deals"><input type="submit" name="deals" value="Clear deals"></form></td>
	<td align="center"><form method="post"><input type="hidden" name="msg" value="All"><input type="submit" name="clearall" value="Clear ALL"></form></td>
</tr>
</table>

</div>
