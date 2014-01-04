<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Snapittoday.com Callcenter Panel</title>
<script src="<?=base_url()?>js/jquery.js"></script>
<style>
a{color:blue;}
</style>
</head>
<body style="background:#fff;font-size:12px;color:#000;font-family:arial;">
<?php $this->load->view("callcenter/header");?>
<?php $this->load->view("callcenter/body/$page");?>
</body>
</html>