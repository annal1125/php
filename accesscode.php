<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}
	include "filepath.php";
	$EX_CODES = "$BASE_AST/ex_codes.conf";
	// $FEATURE_CODES = "$BASE_AST/features.conf";

	
	if(isset($_POST[Submit]))
	{
		$StrTotal = "RINGTIMER = $RingTime\n";
		$StrTotal .= "UFWON = $AllFWOn\n";
		$StrTotal .= "UFWOFF = $AllFWOff\n";
		$StrTotal .= "BFWON = $BusyFWOn\n";
		$StrTotal .= "BFWOFF = $BusyFWOff\n";
		$StrTotal .= "NFWON = $NoAnsFWOn\n";
		$StrTotal .= "NFWOFF = $NoAnsFWOff\n";
		unlink($EX_CODES);
		$fp = fopen( $EX_CODES , "w+");	
		fwrite( $fp , $StrTotal );
		fclose( $fp );
		
		$StrTotal = "[general]\n";
		$StrTotal .= "pickupexten = $Pickup\n";
		
		// $fpFeature = fopen( $FEATURE_CODES , "w+");	
		// fwrite( $fpFeature , $StrTotal );
		// fclose( $fpFeature );
	
		
		
		shell_exec( "asterisk -rx reload" );
	}	

	$content_array = file( $EX_CODES );
	
	$i = 0;
	$RingTime = "";
	$AllFWOn = "";
	$AllFWOff = "";
	$BusyFWOn = "";
	$BusyFWOff = "";
	$NoAnsFWOn = "";
	$NoAnsFWOff = "";
	
	
	
	while( $content_array[$i] != "" )
	{
		$parse_temp = explode( "=" , $content_array[$i] );	
		$Field = chop( ltrim( $parse_temp[0] ) );
		$Value = chop( ltrim( $parse_temp[1] ) );
	
		switch( $Field )
		{
		case "RINGTIMER":	
			$RingTime = $Value; break;
		case "UFWON":	
			$AllFWOn = $Value; break;
		case "UFWOFF":	
			$AllFWOff = $Value; break;
		case "BFWON":	
			$BusyFWOn = $Value; break;
		case "BFWOFF":	
			$BusyFWOff = $Value; break;
		case "NFWON":	
			$NoAnsFWOn = $Value; break;
		case "NFWOFF":	
			$NoAnsFWOff = $Value; break;
		}
		$i++;
	}
	
	// $content_array = file( $FEATURE_CODES );
	// while( list($key , $val) = each($content_array) )
	// {
		// if( strstr( $val , "pickupexten" ) != "" )
		// {
			// $tmp = explode( "=" , $val );
			// $Pickup = trim($tmp[1]);
		// }
	// }


?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>access</title>
<script type="text/JavaScript">
function isNum(N)
{ 
  numtype="*#0123456789"; 
  for(i=0;i<N.length;i++){ //檢討是否有不在 0123456789之內的字 
    if(numtype.indexOf(N.substring(i,i+1))<0) 
      return false;            //是的話....結束迴圈;傳回false 
  } 
  return true; 
} 



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
</style></head>


<body>
<div id='Layer1' >
<?include "languag.php";?>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['accesscode'][$LANG];
	echo "</p>";
?>



<table width="60%" border="2" align="center">

<?
	$Color1 = "#FF7979";
	$Color2 = $MainBackGroundColor;
	// All forward
	echo "<tr>";
	echo "<th width='25%'><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['Un_enable'][$LANG]."</th>";
	echo "<td width='50%'><input name='AllFWOn' type='text' value='$AllFWOn' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th><scope='row' ><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['Un_disable'][$LANG]."</th>";
	echo "<td><input name='AllFWOff' type='text' value='$AllFWOff' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	// busy forward
	echo "<tr>";
	echo "<th width='50%'><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['enable-busy'][$LANG]."</th>";
	echo "<td width='50%'><input name='BusyFWOn' type='text' value='$BusyFWOn' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th><scope='row' ><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['disable-busy'][$LANG]."</th>";
	echo "<td><input name='BusyFWOff' type='text' value='$BusyFWOff' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	// no answer forward
	echo "<tr>";
	echo "<th width='50%'><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['enable-nofwtitle'][$LANG]."</th>";
	echo "<td width='50%'><input name='NoAnsFWOn' type='text' value='$NoAnsFWOn' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th><scope='row' ><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['disable-nofwtitle'][$LANG]."</th>";
	echo "<td><input name='NoAnsFWOff' type='text' value='$NoAnsFWOff' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	// ring time
	echo "<th><scope='row' ><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['ring-timer'][$LANG]."</th>";
	echo "<td><input name='RingTime' type='text' value='$RingTime' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	//pickup
	// echo "<th><scope='row' ><div align='center' style='$MainFieldStyle'; padding:5px>".$WORDLIST['pickup'][$LANG]."</th>";
	// echo "<td><input name='Pickup' type='text' value='$Pickup' STYLE='background-color:$MainEditBackGround; width: 100%'></td>";
	// echo "</tr>";
	
	


?>
</table>
	
<p align="center"> 

<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>

</p>

</form>
</div>
</body>


</html>
