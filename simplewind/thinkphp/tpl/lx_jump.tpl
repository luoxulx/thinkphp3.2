<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>LNMPA跳转提示</title>
		<link rel="stylesheet" href="__STATIC__/js/sweetalert/sweetalert.css"/>
		<script src="//cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
		<script src="__STATIC__/js/sweetalert/sweetalert.min.js"></script>
    </head>
    <body>
		<script type="text/javascript">
			
		    (function(){
		        var wait = "<?php echo($wait);?>";
				var href = "<?php echo($url);?>";
				var text = wait + '秒后自动跳转';
				
				<present name="message">
					swal({
						title: "<?php echo(strip_tags($msg));?>",
						text: text,
						type: 'success',
						timer: <?php echo($wait*1000); ?>,
						confirmButtonText: "跳转", 
					}, function(isConfirm){
						window.parent.location.href = href;
					});
				<else/>
					swal({
						title: "<?php echo(strip_tags($msg));?>",
						text: text,
						type: 'error',
						timer: <?php echo($wait*1000); ?>,
						confirmButtonText: "跳转", 
					}, function(isConfirm){
						window.parent.location.href = href;
					});
				</present>
				
		    })();
		</script>
    </body>
</html>