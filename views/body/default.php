<div class="loginbox">

<div style="margin-left:670px;padding-top:180px;margin-right:10px;" align="left">
<form action="<?=site_url("franchisee/login/true")?>" method="post">
<table width="100%" cellpadding=3>
<tr>
<td><nobr>Username</nobr></td><td>:</td>
<td><input type="text" name="fran_username" class="loginp"></td>
</tr>
<tr>
<td>Password</td><td>:</td>
<td><input type="password" name="fran_password" class="loginp"></td>
</tr>
<Tr>
<td></td><td></td>
<td align="right"><input type="submit" value="Login"></td>
</Tr>
</table>
</form>
</div>

<div style="margin-left:670px;padding-top:50px;margin-right:10px;" align="left">
<form action="<?=site_url("procagentlogin")?>" method="post">
<table width="100%" cellpadding=3>
<tr>
<td><nobr>Mobile No.</nobr></td><td>:</td>
<td><input type="text" name="via_username" class="loginp"></td>
</tr>
<tr>
<td>Password</td><td>:</td>
<td><input type="password" name="via_password" class="loginp"></td>
</tr>
<Tr>
<td></td><td></td>
<td align="right"><input type="submit" value="Login"></td>
</Tr>
</table>
</form>
</div>

</div>
<style>
.loginp{
width:100%;
bordeR:1px solid #aaa;
padding:2px;
}
.mainsubcontainer{
margin-top:40px;
}
.loginbox{
margin-left:-20px;
font-size:14px;
height:450px;
width:940px;
background:url(<?=base_url()?>images/login.png) no-repeat;
}
</style>