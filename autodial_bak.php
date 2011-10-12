<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
//		echo "<meta http-equiv=refresh content=0;URL='index.html'>";
		exit;
	
	}
	include "filepath.php";
	
	$calloutfile = "$BASE_CONF/callout";
	$callouttemp = "$BASE_CONF/callouttemp";
	$calloutDst = "/var/spool/asterisk/outgoing/";
	$CALLOUT_CONF = "$BASE_AST/sip_trunk.conf";

//	extract($_POST,EXTR_OVERWRITE);


	$content_array = file( $MCUPATH );
	$i = 0;
	while( $content_array[$i] != "" )
	{
		$parse_temp = explode( " => " , $content_array[$i] );

		if( $parse_temp[0] != "exten" )
		{
			$i++;
			continue;
		}
		$parse_temp1 = explode( "," , $parse_temp[1] );
	
		if( strstr( $parse_temp1[2] , "MeetMe" ) != "" )
			$MeetmeNum = chop($parse_temp1[0]);
		$i++;
	}
	
	$content_array = file( $calloutfile );
	$i = 0;
	$j = -1;
		
	while( $content_array[$i] != "" )
	{
//		echo "$content_array[$i]<br>";
		if( strstr($content_array[$i] , "[" ) != "" )
		{
			$j++;
			$i++;
			continue;
		}
		if( $j < 0  ) 
		{
			$i++;
			continue;
		}
		
		$parse_temp = explode( "=" , $content_array[$i] );
		
		switch( $parse_temp[0] )
		{
		case "name": // user name
			$ch_name[$j] = chop($parse_temp[1]);
			break;
		case "num": // phone numbers
			$ch_num[$j] = chop($parse_temp[1]);
			break;
		case "type": // type : video, audio
			$ch_type[$j] = chop($parse_temp[1]);
			break;
		case "maxretry": //retry times
			$ch_maxretry[$j] = chop($parse_temp[1]);
			break;
		case "retryt": // Seconds between retries
			$ch_retryT[$j] = chop($parse_temp[1]);
			break;
		case "waitt": // Seconds to wait for an answer 
			$ch_waitT[$j] = chop($parse_temp[1]);
			break;
		case "proxy": // register proxy 
			$ch_Proxy[$j] = chop($parse_temp[1]);
			break;
		case "account": // account 
			$ch_account[$j] = chop($parse_temp[1]);
			break;
		case "pwd":  // password
			$ch_pwd[$j] = chop($parse_temp[1]);
			break;
		} // end switch( $parse_temp[0] )
		$i++;
	}// end while( $content_array[$i] != "" )
	
	
	if(isset($_POST[Submit]))
	{
		for( $i = 0; $i < $MAX_CALLOUT_FIELD; $i++ )
		{	
			$CheckSel = sprintf("check%d",$i);
			
			if( $$CheckSel != "on" )
				continue;
				
			if( $ch_num[$i] == "" )
				continue;
				
			$Calloutcalloutfile = sprintf("%s/%s.call",$callouttemp,$ch_num[$i] );
			exec("sudo rm -f $Calloutcalloutfile");
				
			$fpCallFile = fopen( $Calloutcalloutfile , "w+");
			
			if( $ch_Proxy[$i] != "" && $ch_account[$i] != "" )
			{ // outbound call
				$Str1 = sprintf( "Channel: SIP/%s@%s\n", $ch_num[$i],$ch_account[$i]);
				$Str2 = sprintf( "CallerID: <%s>\n" , $ch_account[$i]);
			}
			else
			{ // normal call
				$Str1 = sprintf( "Channel: SIP/%s\n", $ch_num[$i]);			
				$Str2 = sprintf( "CallerID: <%s>\n" , $ch_num[$i]);
			}
			$Str3 = sprintf( "MaxRetries: %s\nRetryTime: %s\nWaitTime: %s\n",
				$ch_maxretry[$i],$ch_retryT[$i],$ch_waitT[$i]);		
			
 			if( $ch_type[$i] == "audio" )// make audio call
				$Str4 = sprintf( "Context: ava_media\nExtension: %s\nPriority: 1\n",$MeetmeNum);
			else // make video call
				$Str4 = sprintf( "Context: ava_media\nExtension: %s\nPriority: 1\n",$AppConf);
				
			$StrTotal = sprintf("%s%s%s%s",$Str1,$Str2,$Str3,$Str4);
			
			fwrite( $fpCallFile , $StrTotal );
			fclose( $fpCallFile );	
			// start make call
			//echo "sudo cp -f $Calloutcalloutfile $calloutDst<br>";
			system("mv -f $Calloutcalloutfile $calloutDst");
			
		}
	}	
	
	
?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference</title>

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
	background-color: #990000;
}
body,td,th {
	color: #FFFFFF;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->
</style></head>

<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">

<br>
<p align="left" style=FILTER:Shadow(Color=8888ff,direction=150);height=20 color="#0000FF">&nbsp;&nbsp;Conference Audio Auto Dial System</p>
<table width="100%" border="1">
  <tr>
    <th width="5%" scope="row"></th>
    <td width="35%"><div align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">Name</div></td>
    <td width="40%"><div align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">Numbers</div></td>
    <td width="20%"><div align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">Edit</div></td>
 </tr> 
  
 <?php
	$EmptyStr = "&nbsp;";
	for( $i = 0; $i < $MAX_CALLOUT_FIELD; $i++ )
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

		echo "<td><div align='center'><input name='edit1' align='center' type='button' onClick='javascript:selectbtn($i)' value='Modify' style='border: 3px dotted #C0C0C0; background-color: #FFFF66; width: 50%'></div></td>";
//		echo "<td><div align='center'><input name='edit1' align='center' type='button' onClick='javascript:selectbtn($i)' value='Modify' style='background-color:#FFFF66; width: 50%'></div></td>";
		echo "</tr>";
   } 
  ?>
</table>

<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
