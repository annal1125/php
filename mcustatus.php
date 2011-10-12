<?php
include "platform.php";

if( $PLATFORM == 3 )
	$FlashData = "ava_mcu_monitor_planet.swf";
else
	$FlashData = "ava_mcu_monitor.swf";
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Streaming ServeR</title>

<script type="text/javascript" src="status/swfobject.js"></script>
<style type="text/css">
            /* hide from ie on mac \*/
            html {
				height: 100%;
				width:  100%;
				overflow: auto;
			}
			#flashcontent {
				position: absolute;
				top: 0px;
				left: 0px;
				height: 100%;
				width:  100%;
			}
        </style>
</head>

<body>

<div id="flashcontent">
        <p><strong>Streaming ServeR</strong></p>
</div>
	
<script type="text/javascript">
		// <![CDATA[
		swf_string = "status/<? echo $FlashData ?>?WEBIP=" + location.hostname;
		var so = new SWFObject(swf_string, "ava_mcu_monitor", "100%", "100%", "9", "#000000");
		so.addParam("scale", "noscale");
		so.addParam("allowScriptAccess", "always");
		so.addParam('menu', 'false');
		so.addParam('salign', 'lt');
		so.addParam("quality", "high");
		so.addParam("name", "ava_mcu_monitor");
		so.addParam("id", "ava_mcu_monitor");
		so.addParam("loop", "false");
		so.addParam("menu", "false");
		so.write("flashcontent");
		
		// ]]>
</script>
</body>
</html>
