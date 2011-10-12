<?php
	include "filepath.php";

	$InputIndex = $_SERVER["QUERY_STRING"];
	$parse_temp = explode( "=" , $InputIndex );
	$InputIndex = chop($parse_temp[1]);
	
	$IntoExten = array();
	$content_array = file( $MCU_PATH );
	while( list($key , $val) = each($content_array) )
	{	
		if( strstr( $val , "members=" ) != "" )
		{
			$iPos = strpos( $val , " => " ) + 4;
			$tmp = substr( $val , $iPos , strpos( $val,",")- $iPos  );
			array_push( $IntoExten , $tmp );
		}
	}	
	$TrunkAlias = array("IP_PHONE");
	//	$TrunkAccount = array();
	$content_array = file( $SIPTRUNK_PATH );
	
	while( list($key , $val) = each($content_array) )
	{
		if( strstr($val , "[") != "" )
		{
			$StrTep = chop( substr($val, 1 , -2 ) );
			if( $StrTep != "" )
				array_push( $TrunkAlias , $StrTep );		
		}
	//	if( strstr( $val , "username=" ) != "" )
		if( strstr( $val , "fromuser=" ) != "" )
		{
			$tmp = explode( "=" , $val );
			$TrunkAccount[$StrTep]  = chop(ltrim($tmp[1]));		
		}
	}
	
	if(isset($_POST[Submit]))
	{
		$fp = fopen( $AUTODIAL_PATH , "w+");
		for( $i = 0; $i < $MAX_AUTO_DIAL_FIELD; $i++ )
		{
			$StrTotal = "";
			if( $ch_num[$i] == "" | $ch_IntoExten[$i] == "" ) continue;
			
			$StrTotal .= "[$ch_num[$i]]\n";
			$StrTotal .= "name=$ch_name[$i]\n";
			$StrTotal .= "num=$ch_num[$i]\n";
			$StrTotal .= "maxretry=$ch_maxretry[$i]\n";
			$StrTotal .= "retryt=$ch_retryT[$i]\n";
			$StrTotal .= "waitt=$ch_waitT[$i]\n";
			$StrTotal .= "IntoExten=$ch_IntoExten[$i]\n";
			$StrTotal .= "trunk=$ch_Trunk[$i]\n";
			$StrTotal .= "trunkaccount=".$TrunkAccount[$ch_Trunk[$i]]."\n";
			$StrTotal .= "\n";
			fwrite( $fp , $StrTotal );
			
		}
		
		fclose( $fp );	
		
		echo "<Script language='JavaScript'>";
		echo "opener.window.location.reload();";
//		echo "opener.window.history.go(0);";
		echo "window.close();";
		echo"</Script>";
	}
	
	
	$ch_name = array();
	$ch_num = array();
	$ch_maxretry = array();
	$ch_retryT = array();
	$ch_waitT = array();
	$ch_Trunk = array();
	$ch_IntoExten = array();
	
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
		case "IntoExten": // select trunk
			array_push( $ch_IntoExten , chop($parse_temp[1]) );
			break;	
		} // end switch( $parse_temp[0] )
	}// end while( $content_array[$i] != "" )
	
	for( $j = 0;$j < $MAX_AUTO_DIAL_FIELD; $j++ )
	{
		if( $ch_maxretry[$j] == "" )
			$ch_maxretry[$j] = "1";
		if( $ch_retryT[$j] == "" )
			$ch_retryT[$j] = "6";
		if( $ch_waitT[$j] == "" )
			$ch_waitT[$j] = "30";
			
	}
	
function GetTypeSelect($Trunk)
{
	global $TrunkAlias;
	
	if( $Trunk != "" )
		echo "<option value=$Trunk>$Trunk</option>";
	
	reset( $TrunkAlias );
	while( list($key , $val) = each($TrunkAlias) )
	{
		if( $val == $Trunk )
			continue;
			
		echo "<option value=$val>$val</option>";	
	}
}
function GetMCUExten($Type)
{
	global $IntoExten;
	
	if( $Type != "" )
		echo "<option value=$Type>$Type</option>";
	
	
	for( $j = 0; $j < sizeof($IntoExten); $j++ )
	{
		if( $IntoExten[$j] == $Type )
			continue;
			
		echo "<option value=$IntoExten[$j]>$IntoExten[$j]</option>";	
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
	echo $WORDLIST['Dial-Attribute'][$LANG];
	echo "</p>";
?>

<table width="100%" border="1">
  <tr>
    <td width="40%"><div align="center">Field</div></td>
    <td width="60%"><div align="center">Value</div></td>
 </tr>
<?php
	for( $i = 0; $i < $MAX_AUTO_DIAL_FIELD; $i++ )
	{	
		if( $i == $InputIndex )
		{
			echo "<tr><th scope='row' >";
			echo $WORDLIST['ex-name'][$LANG];
			echo "</th>";
			echo "<td><input name='ch_name[]' type='text' value='$ch_name[$i]' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";
		
			echo "<tr><th scope='row'>";
			echo $WORDLIST['num'][$LANG];
			echo "</th>";
			
			echo "<td><input name='ch_num[]' type='text' value='$ch_num[$i]' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";
		
			echo "<tr><th scope='row'>";
			echo $WORDLIST['Retry-Times'][$LANG];
			echo "</th>";
			
			echo "<td><input name='ch_maxretry[]' type='text' value='$ch_maxretry[$i]' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";

			echo "<tr><th scope='row'>";
			echo $WORDLIST['Seconds-Between-Retries'][$LANG];
			echo "</th>";
			
			echo "<td><input name='ch_retryT[]' type='text' value='$ch_retryT[$i]' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";
			
			echo "<tr><th scope='row'>";
			echo $WORDLIST['wait-ans'][$LANG];
			echo "</th>";
			
			echo "<td><input name='ch_waitT[]' type='text' value='$ch_waitT[$i]' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";
			
			echo "<tr><th scope='row'>";
			echo $WORDLIST['trunk-alias'][$LANG];
			echo "</th>";
					
			echo "<td><select name='ch_Trunk[]' STYLE='background-color:$MainEditBackGround;width :100%' >";
			GetTypeSelect($ch_Trunk[$i]);					
			echo "</select></td>";	
			
			echo "<tr><th scope='row' >";
			echo $WORDLIST['mcu-room-num'][$LANG];
			echo "</th>";
			
			echo "<td><select name='ch_IntoExten[]' STYLE='background-color:$MainEditBackGround;width :100%' >";
			GetMCUExten($ch_IntoExten[$i]);
			echo "</select></td>";
			
			
		}
		else
		{
			echo "<input name='ch_name[]' type='hidden' value='$ch_name[$i]' >";
			echo "<input name='ch_num[]' type='hidden' value='$ch_num[$i]' >";
			echo "<input name='ch_maxretry[]' type='hidden' value='$ch_maxretry[$i]' >";
			echo "<input name='ch_retryT[]' type='hidden' value='$ch_retryT[$i]' >";
			echo "<input name='ch_waitT[]' type='hidden' value='$ch_waitT[$i]' >";
			echo "<input name='ch_IntoExten[]' type='hidden' value='$ch_IntoExten[$i]' >";
			echo "<input name='ch_Trunk[]' type='hidden' value='$ch_Trunk[$i]' >";
		
		}
	}
 ?>  

</table>


<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" onclick="window.close()" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
