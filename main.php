<?php
	include "platform.php";

	$DISPLAY = "AVA Media PBX";
	switch( $PLATFORM )
	{
	case 0:
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "AVA Media PBX"; break;
		case 2: $DISPLAY = "AVA VCS Server"; break;
		case 2: $DISPLAY = "AVA Media Server"; break;
		}
		break;
	case 1:
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "CEI IPX-100"; break;
		case 2: $DISPLAY = "CEI IP-DID"; break;
		}
		break;
	case 2:
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "AVA Media Server"; break;
		case 2: $DISPLAY = "AVA VXML Server"; break;
		}
		break;
	case 3:
		$DISPLAY = "PLANET MCU Management";
		break;
	case 4:	// Hit Point
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "HIT POINT PBX"; break;
		case 2: $DISPLAY = "HIT POINT MCU"; break;
		}
		break;
		
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><? echo $DISPLAY ?></title>
</head>
<?
	if( $PLATFORM == 3 )
	{
		echo "<frameset rows=88,* frameBorder=0 framespacing='0'>";
		echo "<frame name=banner scrolling=no noresize  src=title.php marginwidth=0 marginheight=0 >";
	}

?>
  <frameset rows=* cols=155,* frameBorder=0 framespacing="0">
    <frame name="right" src=menu.php marginwidth=0 marginheight=0 scrolling=auto>
<?
	if(  $PLATFORM == 2 && $SUBFORM == 2 ) // vxml
		echo "<frame name=left src=voicexml.php  scrolling=auto marginwidth=0 marginheight=0 noresize>";
	else
		echo "<frame name=left src=status.php  scrolling=auto marginwidth=0 marginheight=0 noresize>";
?>

  </frameset>
  <noframes>
  <body>
  <p>This Page needs frame support.
Please update your browser version.</p>

  </body>
  </noframes>
</frameset>

</html>
