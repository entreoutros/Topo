<?php
	//Declare the needed css to this page
	//$styles[] = root."css/cleaner.css";
	//$styles[] = root."css/estilo_ui.css";

	//Declare the needed js to this page
	//$scripts[] = "//code.jquery.com/jquery-latest.js";
	//$scripts[] = "//code.jquery.com/ui/1.10.4/jquery-ui.js";
	//$scripts[] = root."js/jquery.easing.1.3.js";
	//$scripts[] = "//connect.facebook.net/en_US/all.js";
	//$scripts[] = root."js/libs/underscore.js";

	$scripts[] = root."js/libs/backbone.js";
	$scripts[] = array('src' => root."js/libs/require.js", 'data-main' => 'js/main');

	$title = (isset($view['args'][0]))?$view['args'][0]:'';
?>

<div id="body">
	<h1>This is a Topogiogio API.</h1><br/> Please, come back to site and let's we do this part alone. = )
</div>
<div id="debugg">
</div>