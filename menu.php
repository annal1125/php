<?php
	include "filepath.php";
?>

<html>
<head>
<title></title>

<style type="text/css">
<html>
<head>
<title>AVA</title>


<style type="text/css">
	
<!--
a:hover{position:relative;top:1px;left:1px;}
-->

<!--
div#links {
	position: relative;
	width: 140px;
}

div#links a {
	display: block;
	width: 155px;
	text-align: center;
	font-size: 13pt;
	padding: 5px 5px 5px 5px;
	margin: 1px 0 0 0;
	border-width: 0px;
	text-decoration: none;
	color: <? echo $MenuTextColor ?>;
	
}

div#links a:hover {
<? 
	echo "color:$MenuTextOverColor;";
	echo "background:$MenuItemBackGroundColor;";
?>	
	}

-->
</style> 

</head>


<body  BGCOLOR="<? echo $MenuBackGroundColor1 ?>">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<br><br>


<div id="links">
<?
	echo "<a href='netconfig.php' target='left'><b>";
	echo $WORDLIST['networksetting'][$LANG];
	echo "</b></a>";
	
	
	
	
	
	
	
	switch( $PLATFORM )
	{
	case 0:
	case 3:
		switch( $SUBFORM )
		{
		case 1: 
			echo "<a href='extension2.php' target='left'><b>";
			echo $WORDLIST['accesscode'][$LANG];
			echo "</b></a>";
//			echo "<a href='group.php' target='left'><b>";
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['groupsetting'][$LANG];
			echo "</b></a>";
			echo "<a href='extension2.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='mod.php' target='left'><b>";
			echo $WORDLIST['streaming'][$LANG];
			echo "</b></a>";
			echo "<a href='ipcam.php' target='left'><b>";
			echo $WORDLIST['ipcam'][$LANG];
			echo "</b></a>";
			echo "<a href='mcusetting.php' target='left'><b>";
			echo $WORDLIST['mcuset'][$LANG];
			echo "</b></a>";
			echo "<a href='mcustatus.php' target='left'><b>";
			echo $WORDLIST['mcustatus'][$LANG];
			echo "</b></a>";
			echo "<a href='autodial.php' target='left'><b>";
			echo $WORDLIST['autodial'][$LANG];
			echo "</b></a>";
			echo "<a href='voicexml.php' target='left'><b>";
			echo $WORDLIST['VXMLSet'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		case 2: 
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
                        echo "<a href='accesscode.php' target='left'><b>";
                        echo $WORDLIST['accesscode'][$LANG];
                        echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='ipcam.php' target='left'><b>";
			echo $WORDLIST['ipcam'][$LANG];
			echo "</b></a>";
			echo "<a href='mcusetting.php' target='left'><b>";
			echo $WORDLIST['mcuset'][$LANG];
			echo "</b></a>";
			echo "<a href='mcustatus.php' target='left'><b>";
			echo $WORDLIST['mcustatus'][$LANG];
			echo "</b></a>";
			echo "<a href='autodial.php' target='left'><b>";
			echo $WORDLIST['autodial'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		case 3: 
			echo "<a href='accesscode.php' target='left'><b>";
			echo $WORDLIST['accesscode'][$LANG];
			echo "</b></a>";
			echo "<a href='group.php' target='left'><b>";
			echo $WORDLIST['groupsetting'][$LANG];
			echo "</b></a>";
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			// echo "<a href='mod.php' target='left'><b>";
			// echo $WORDLIST['streaming'][$LANG];
			// echo "</b></a>";
			// echo "<a href='ipcam.php' target='left'><b>";
			// echo $WORDLIST['ipcam'][$LANG];
			// echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		} // end switch( $SUBFORM )
		break;
	case 1:
		switch( $SUBFORM )
		{
		case 1:
			echo "<a href='accesscode.php' target='left'><b>";
			echo $WORDLIST['accesscode'][$LANG];
			echo "</b></a>";
			echo "<a href='group.php' target='left'><b>";
			echo $WORDLIST['groupsetting'][$LANG];
			echo "</b></a>";
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='disa.php' target='left'><b>";
			echo $WORDLIST['disa'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			echo "<a href='time.php' target='left'><b>";
			echo $WORDLIST['time'][$LANG];
			echo "</b></a>";
			break;
		case 2:
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		}// end switch( $SUBFORM )
		break;
	case 2:
		switch( $SUBFORM )
		{
		case 1:
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='mod.php' target='left'><b>";
			echo $WORDLIST['streaming'][$LANG];
			echo "</b></a>";
			echo "<a href='ipcam.php' target='left'><b>";
			echo $WORDLIST['ipcam'][$LANG];
			echo "</b></a>";
			echo "<a href='voicexml.php' target='left'><b>";
			echo $WORDLIST['VXMLSet'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		case 2:
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='voicexml.php' target='left'><b>";
			echo $WORDLIST['VXMLSet'][$LANG];
			echo "</b></a>";
			break;
		} // end switch( $SUBFORM )
		break;
	case 4: // Hit Point
		switch( $SUBFORM )
		{
		case 1: // pbx
			echo "<a href='accesscode.php' target='left'><b>";
			echo $WORDLIST['accesscode'][$LANG];
			echo "</b></a>";
			echo "<a href='group.php' target='left'><b>";
			echo $WORDLIST['groupsetting'][$LANG];
			echo "</b></a>";
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			// echo "<a href='mod.php' target='left'><b>";
			// echo $WORDLIST['streaming'][$LANG];
			// echo "</b></a>";
			// echo "<a href='ipcam.php' target='left'><b>";
			// echo $WORDLIST['ipcam'][$LANG];
			// echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
		break;
		case 2:
			echo "<a href='extension.php' target='left'><b>";
			echo $WORDLIST['extensionset'][$LANG];
			echo "</b></a>";
			echo "<a href='SIPTrunk.php' target='left'><b>";
			echo $WORDLIST['SIPTrunk'][$LANG];
			echo "</b></a>";
			echo "<a href='dialplan.php' target='left'><b>";
			echo $WORDLIST['dialplan'][$LANG];
			echo "</b></a>";
			echo "<a href='meetmeSet.php' target='left'><b>";
			echo $WORDLIST['mcuset'][$LANG];
			echo "</b></a>";
			echo "<a href='mcustatus.php' target='left'><b>";
			echo $WORDLIST['mcustatus'][$LANG];
			echo "</b></a>";
			echo "<a href='autodial.php' target='left'><b>";
			echo $WORDLIST['autodial'][$LANG];
			echo "</b></a>";
			echo "<a href='status.php' target='left'><b>";
			echo $WORDLIST['status'][$LANG];
			echo "</b></a>";
			break;
		}
		break;
	} // end switch( $PLATFORM )
?>
<!--
<a href="time.php" target='left'><b><? echo $WORDLIST['time'][$LANG] ?></b></a>
-->

<a href="upgrade.php" target='left'><b><? echo $WORDLIST['upgrade'][$LANG] ?></b></a>
<a href="password.php" target='left'><b><? echo $WORDLIST['password'][$LANG] ?></b></a>
<a href="reboot.php" target='left'><b><? echo $WORDLIST['reboot'][$LANG] ?></b></a>
</div> 



<p><p><p>
   
</table>        
</body>                                                                                                                        
</html>
