<?php
	include "filepath.php";

	$InputIndex = $_SERVER["QUERY_STRING"];
	$parse_temp = explode( "=" , $InputIndex );
	$InputIndex = chop($parse_temp[1]);
	
	$calloutfile = "$BASE_CONF/callout";
	$callouttemp = "$BASE_CONF/callouttemp";
	$CALLOUT_CONF = "$BASE_AST/sip_trunk.conf";
	
	
	if(isset($_POST[Submit]))
	{
		unlink($calloutfile);
		$fp = fopen( $calloutfile , "w+");	
		unlink($CALLOUT_CONF);
		$fpCallOutConf = fopen( $CALLOUT_CONF , "w+");	
		unlink($SIPTRUNKREG_PATH);
		$fpSIPTRUNKREG_PATH = fopen( $SIPTRUNKREG_PATH , "w+");	
		$bReload = 0;
		
		
		for( $i = 0; $i < $MAX_CALLOUT_FIELD; $i++ )
		{	
			// get field value
			$ch_nameT = sprintf("ch_name%d",$i);
			$ch_nameT = $$ch_nameT;
			$ch_numT = sprintf("ch_num%d",$i);
			$ch_numT = $$ch_numT;
			$ch_typeT = sprintf("ch_type%d",$i);
			$ch_typeT = $$ch_typeT;
			$ch_maxretryT = sprintf("ch_maxretry%d",$i);
			$ch_maxretryT = $$ch_maxretryT;
			$ch_retryTT = sprintf("ch_retryT%d",$i);
			$ch_retryTT = $$ch_retryTT;
			$ch_waitTT = sprintf("ch_waitT%d",$i);
			$ch_waitTT = $$ch_waitTT;
			$ch_ProxyT = sprintf("ch_Proxy%d",$i);
			$ch_ProxyT = $$ch_ProxyT;
			$ch_accountT = sprintf("ch_account%d",$i);
			$ch_accountT = $$ch_accountT;
			$ch_pwdT = sprintf("ch_pwd%d",$i);
			$ch_pwdT = $$ch_pwdT;
			
			if( $ch_numT == "" ) // do not save null number
				continue;
			// store call out detail	
			$StrTotal = "[$ch_numT]\n";
			$StrTotal .= "name=$ch_nameT\n";
			$StrTotal .= "num=$ch_numT\n";
			$StrTotal .= "type=$ch_typeT\n";
			$StrTotal .= "maxretry=$ch_maxretryT\n";
			$StrTotal .= "retryt=$ch_retryTT\n";
			$StrTotal .= "waitt=$ch_waitTT\n";
			$StrTotal .= "proxy=$ch_ProxyT\n";
			$StrTotal .= "account=$ch_accountT\n";
			$StrTotal .= "pwd=$ch_pwdT\n";
			fwrite( $fp , $StrTotal );
			
			// set Asterisk outbound call in sip_callout.conf
			if( $ch_ProxyT != "" && $ch_accountT != "" )
			{
				// set account
				$proxytmp = explode( ":" , $ch_ProxyT );
				$proxy = $proxytmp[0];
				
				$StrTotal = "[$ch_accountT]\n";
				$StrTotal .= "username=$ch_accountT\n";
				$StrTotal .= "secret=$ch_pwdT\n";
				$StrTotal .= "host=$proxy\n";
				$StrTotal .= "dtmfmode=rfc2833\n";
				$StrTotal .= "type=friend\n";
				$StrTotal .= "nat=yes\n";
				$StrTotal .= "qualify=yes\n";
				$StrTotal .= "disallow=all\n";
				$StrTotal .= "context=ava_media\n";
				$StrTotal .= sprintf("videosupport=%s\n",($ch_typeT == "video") ? "yes":"no");
				$StrTotal .= "allow=ulaw\n";
				fwrite( $fpCallOutConf , $StrTotal );
				
				// register file
				$StrTotal = sprintf("register => %s:%s@%s\n",$ch_accountT,$ch_pwdT,$ch_ProxyT);
				fwrite( $fpSIPTRUNKREG_PATH , $StrTotal );
				$bReload = 1;
			}
		}
		fclose( $fpCallOutConf );	
		fclose( $fpSIPTRUNKREG_PATH );	
		fclose( $fp );	
		
		if( $bReload )
			shell_exec( "asterisk -rx reload" );
			
			
		echo "<Script language='JavaScript'>";
		echo "opener.window.history.go(0);";
		echo "window.close();";
		echo"</Script>";
		
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
	
	
	for( $j = 0;$j < $MAX_CALLOUT_FIELD; $j++ )
	{
		if( $ch_maxretry[$j] == "" )
			$ch_maxretry[$j] = "1";
		if( $ch_retryT[$j] == "" )
			$ch_retryT[$j] = "6";
		if( $ch_waitT[$j] == "" )
			$ch_waitT[$j] = "30";
	}
	
function GetTypeSelect($Type)
{
	$TypeArray = array("audio");
	
	$i = 0;
	if( $Type != "" )
		echo "<option value=$Type>$Type</option>";
		
	while( $TypeArray[$i] != "" )
	{
		if( $Type != $TypeArray[$i] )
			echo "<option value=$TypeArray[$i]>$TypeArray[$i]</option>";
		$i++;
	}
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference Edition</title>


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

<p align="center" style=FILTER:Shadow(Color=8888ff,direction=150);height=20 color="#0000FF" >&nbsp;Dial Attribute</p>
<table width="100%" border="1">
  <tr>
    <td width="40%"><div align="center">Field</div></td>
    <td width="60%"><div align="center">Value</div></td>
 </tr>
<?php
	for( $i = 0; $i < $MAX_CALLOUT_FIELD; $i++ )
	{			
		if( $i == $InputIndex )
		{
			echo "<tr><th scope='row' >Name</th>";
			echo "<td><input name='ch_name$i' type='text' value='$ch_name[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";
		
			echo "<tr><th scope='row' >Phone Numbers</th>";
			echo "<td><input name='ch_num$i' type='text' value='$ch_num[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";
		
			echo "<tr><th scope='row'>Type</th>";
			echo "<td><select name='ch_type$i' STYLE='background-color:#FF7979;width :100%' >";
			GetTypeSelect($ch_type[$i]);
			echo "</select></td>";
		
			echo "<tr><th scope='row' >Retry Times</th>";
			echo "<td><input name='ch_maxretry$i' type='text' value='$ch_maxretry[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";

			echo "<tr><th scope='row' >Seconds Between Retries</th>";
			echo "<td><input name='ch_retryT$i' type='text' value='$ch_retryT[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";
			
			echo "<tr><th scope='row' >Seconds to wait for an answer</th>";
			echo "<td><input name='ch_waitT$i' type='text' value='$ch_waitT[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";

			echo "</table>";

			echo "<p align='center'>&nbsp;Registration (Outbound call)</p>";
			echo "<table width='100%' border='1'><tr><td width='40%'><div align='center'>Field</div></td>";
			echo "<td width='60%'><div align='center'>Value</div></td> </tr>";

	    
	  		echo "<tr><th scope='row' >Proxy:Port</th>";
			echo "<td><input name='ch_Proxy$i' type='text' value='$ch_Proxy[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";
	  
	  		echo "<tr><th scope='row' >Account</th>";
			echo "<td><input name='ch_account$i' type='text' value='$ch_account[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";

	  		echo "<tr><th scope='row' >Password</th>";
			echo "<td><input name='ch_pwd$i' type='text' value='$ch_pwd[$i]' style='background-color:#FF7979; width: 100%'></td></tr>";
		}
		else
		{
			echo "<input name='ch_name$i' type='hidden' value='$ch_name[$i]' >";
			echo "<input name='ch_num$i' type='hidden' value='$ch_num[$i]' >";
			echo "<input name='ch_type$i' type='hidden' value='$ch_type[$i]' >";
			echo "<input name='ch_maxretry$i' type='hidden' value='$ch_maxretry[$i]' >";
			echo "<input name='ch_retryT$i' type='hidden' value='$ch_retryT[$i]' >";
			echo "<input name='ch_waitT$i' type='hidden' value='$ch_waitT[$i]' >";
			echo "<input name='ch_Proxy$i' type='hidden' value='$ch_Proxy[$i]' >";
			echo "<input name='ch_account$i' type='hidden' value='$ch_account[$i]' >";
			echo "<input name='ch_pwd$i' type='hidden' value='$ch_pwd[$i]' >";
		
		}
	}
 ?>  

</table>


<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
<input type="submit" name="Cancel" onclick="window.close()" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
