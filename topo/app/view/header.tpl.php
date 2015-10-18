<?php 
	$title = (isset($title) && $title != '') ? $title . ' - ' : '';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0, minimum-scale=1, user-scalable=yes"/>

		<link rel="icon" href="img/favicon.png" type="image/x-icon" />
		<link rel="shortcut icon" href="#" type="image/x-icon" />
		
		<title><?php echo $title."Topo"; ?></title>
	
		<link href="<?php root(); ?>css/reset.css" rel="stylesheet">

		<link href='//fonts.googleapis.com/css?family=Crimson+Text:400,400italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
		<link href='//fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
		<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
		
		<!-- Dynamic Css insert -->
		<?php self::renderCss(); ?>

		<!-- Dynamic Javascript insert -->
		<?php self::renderJs();?>	

		<meta property="og:title" content="<?php echo $title."Topo"; ?>" />
		<meta property="og:url" content="<?php root; ?>" />
		<!--<meta property="og:image" content="<?php root; ?>img/site-logo.png" />-->
		<meta property="og:type" content="website" />

		
		
	</head>
	<body>