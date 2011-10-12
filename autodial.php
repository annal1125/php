<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;
	
	}
	include "filepath.php";
	include "AstManager.php";
	$CALLOUTDET = "/var/spool/asterisk/outgoing";

	$ch_name = array();
	$ch_num = array();
	$ch_maxretry = array();
	$ch_retryT = array();
	$ch_waitT = array();
	$ch_Trunk = array();
	$ch_IntoExten = array();
	$ch_Trunkacc = array();
	
	$content_array = file( $AUTODIAL_PATH );
		
	while( list($key , $val) = each($content_array) )
	{
		if( strstr( $val , "=" ) == "" ) continue;
		$parse_temp = explode( "=" , $val );
		switch( $parse_temp[0] )
		{
		case "name": // user name
			array_push( $ch_name , chop($parse_temp[1]) );
			break;
		case "num": // phone numbers
			array_push( $ch_num , chop($parse_temp[1]) );
			break;
		case "maxretry": //retry times
			array_push( $ch_maxretry , chop($parse_temp[1]) );
			break;
		case "retryt": // Seconds between retries
			array_push( $ch_retryT , chop($parse_temp[1]) );
			break;
		case "waitt": // Seconds to wait for an answer 
			array_push( $ch_waitT , chop($parse_temp[1]) );
			break;
		case "trunk": // select trunk
			array_push( $ch_Trunk , chop($parse_temp[1]) );
			break;	
		case "trunkaccount": // select trunk account
			array_push( $ch_Trunkacc , chop($parse_temp[1]) );
			break;	
		case "IntoExten": // select trunk
			array_push( $ch_IntoExten , chop($parse_temp[1]) );
			break;	
		} // end switch( $parse_temp[0] )
	}// end while( $content_array[$i] != "" )
		
	if(isset($_POST[Submit]))
	{
//		Net_AsteriskManager( '127.0.0.1' , '5038' );
//		connect();
//		login('admin', 'avadesign22221266');
	
		for( $i = 0; $i < $MAX_AUTO_DIAL_FIELD; $i++ )
		{	
			$CheckSel = sprintf("check%d",$i);
			$CheckSel = $$CheckSel;
			
			if( $CheckSel != "on" || $ch_num[$i] == "" )
				continue;
								
			
			$Callout = "$BASE_CONF/$ch_num[$i].call";
			$fp = fopen( $Callout , "w+");

	
//			$StrTotal = "Action: Originate\r\n";
			$StrTotal = "";
			
			if( $ch_Trunk[$i] != "IP_PHONE" )
			{ // outbound call
//				$StrTotal .= "Channel: SIP/$ch_num[$i]@$ch_Trunk[$i]\r\n";
				$StrTotal .= "Channel: SIP/$ch_num[$i]@$ch_Trunk[$i]\r\n";
				$StrTotal .= "Callerid: <$ch_Trunkacc[$i]>\n";
//				echo $ch_Trunkacc[$i];
			}
			else
			{ // normal call
				$StrTotal .= "Channel: SIP/$ch_num[$i]\n";
				$StrTotal .= "Callerid: <$ch_name[$i]>\n";
			}
			
			$StrTotal .= "MaxRetries: $ch_maxretry[$i]\n";
			$StrTotal .= "RetryTime: $ch_retryT[$i]\n";
			$StrTotal .= "WaitTime: $ch_waitT[$i]\n";
			$StrTotal .= "Context: MCU\n";
			$StrTotal .= "Extension: $ch_IntoExten[$i]\n";
			$StrTotal .= "Priority: 1\n";
			
			
			
			// start make call
//			echo $StrTotal;
//			$res = Dialout( $StrTotal );
			fwrite( $fp , $StrTotal );
			fclose( $fp );	
			
			system("mv -f $Callout $CALLOUTDET");

			//echo "sudo cp -f $Calloutcalloutfile $calloutDst<br>";
		
		}
		
//		logout();	
	}	
	
?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Auto Dial</title>

<script type="text/JavaScript">
<!--
function selectbtn(msg) 
{
	FORM=document.forms[0];
	var page = 'autodialedit.php?v=' + msg;   
//	window.open(page,"","");	
	window.open(page,"","width=600,height=500,scrollbars=yes");	
//	alert(FORM.FieldSel);
}

//-->


</script>

<style type="text/css">
<!--
body {
        background-color: <? echo $MainBackGroundColor ?>;
}
body,td,th {
	color: <? echo $MainTitleTextColor ?>;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->
</style>
</head>

<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">

<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['autodial'][$LANG];
	echo "</p>";
?>
<table width="100%" border="1">
  <tr>
    <th width="5%" scope="row"></th> 
    <td width="25%"><div align="center" style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['ex-name'][$LANG] ?></div></td>
    <td width="30%"><div align="center" style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['num'][$LANG] ?></div></td>
    <td width="20%"><div align="center" style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['mcu-room-num'][$LANG] ?></div></td>
    <td width="20%"><div align="center" style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['modify'][$LANG] ?></div></td>
 
 
 </tr> 
  
 <?php
	$EmptyStr = "&nbsp;";
	for( $i = 0; $i < $MAX_AUTO_DIAL_FIELD; $i++ )
	{
		echo "<tr>";
		echo "<th scope='row'><input name='check$i' type='checkbox'></th>";
		if( $ch_name[$i] == "" )
			echo "<th scope='row'>$EmptyStr</th>";
		else	
			echo "<th scope='row'>$ch_name[$i]</th>";
		
		if( $ch_num[$i] == "" )
			echo "<th scope='row'>$EmptyStr</th>";
		else
			echo "<th scope='row'>$ch_num[$i]</th>";
			
		
		if( $ch_IntoExten[$i] == "" )
			echo "<th scope='row'>$EmptyStr</th>";
		else
			echo "<th scope='row'>$ch_IntoExten[$i]</th>";

		

		echo "<td><div align='center'><input name='edit1' align='center' type='button' onClick='javascript:selectbtn($i)' value='";
		echo $WORDLIST['modify'][$LANG];
	//	echo "' style='border: 3px dotted #C0C0C0; background-color: #FFFF66; width: 50%'></div></td>";
		echo "' style='$MainBtnStyle; width: 50%'></div></td>";
//		echo "<td><div align='center'><input name='edit1' align='center' type='button' onClick='javascript:selectbtn($i)' value='Modify' style='background-color:#FFFF66; width: 50%'></div></td>";
		echo "</tr>";
   } 
  ?>
</table>

<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
