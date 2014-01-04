<html>
	<head>
		<title><?php echo $print_title;?></title>
		<style>
			body{font-family: arial;font-size: 12px;}
			table{font-family: arial;font-size: 12px;}
			table td{padding:3px;font-size: 11px}
		</style>
	</head>
	<body>
		<?php
			echo $print_data;
		?>
		<script>
			<?php
				if($auto_print)
				{
			?>
					window.print();
			<?php		
				}
			?>
		</script>
	</body>
</html>