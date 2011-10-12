<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;	
	}
	include "filepath.php";
	$WEBCAM_PATH = "$BASE_AST/ex_webcam.conf";

	$SizeArray = array( "cif" => "CIF" , "qcif" => "QCIF" );
	$BitrateArray = array( "64" , "128" , "256" , "384" ,"512" );

	if(isset($_POST[Submit]))
	{
		unlink($WEBCAM_PATH);
		$fp = fopen( $WEBCAM_PATH , "w+");	
		
		$bTitle = 0;
		$StrTotal = "";
		for( $i = 0; $i < $MAX_WEB_CAM; $i++ )
		{		
			$num = sprintf("num%d",$i);
			$num = $$num;
			$ip = sprintf("ip%d",$i);
			$ip = $$ip;
			$fps = sprintf("fps%d",$i);
			$fps = $$fps;
			$Size = sprintf("Size%d",$i);
			$Size = $$Size;			
			$bitrate = sprintf("bitrate%d",$i);
			$bitrate = $$bitrate;
						
			if( $num == "" || $ip == "" || $fps == "" )
				continue;
			
			if( $bTitle == 0 )
			{
				$StrTotal .= "[ava_media]\n";
				$bTitle = 1;
			}
			$StrTotal .= "exten => $num,1,Answer\n";
			$StrTotal .= "exten => $num,n,transcode(,$num@webcam,h263@$Size/fps=$fps/kb=$bitrate/qmin=8/qmax=20/gs=50)\n";
			$StrTotal .= "exten => $num,n,Hangup\n\n";
		}	
		$bTitle = 0;
		for( $i = 0; $i < $MAX_WEB_CAM; $i++ )
		{		
			$num = sprintf("num%d",$i);
			$num = $$num;
			$ip = sprintf("ip%d",$i);
			$ip = $$ip;
			
			if( $num == "" || $ip == "" )
				continue;
			
			if( $bTitle == 0 )
			{
				$StrTotal .= "[webcam]\n";
				$bTitle = 1;
			}
			$StrTotal .= "exten => $num,1,Answer\n";
			$StrTotal .= "exten => $num,n,rtsp($ip)\n";
			$StrTotal .= "exten => $num,n,Hangup\n\n";
		}
		
		fwrite( $fp , $StrTotal );	
		fclose( $fp );
		shell_exec( "asterisk -rx reload" );
	}	
	// init param
	$num = "";
	$ip = "";
	$Size = "";
	$fps = "";
	$bitrate = "";

	
	$content_array = file( $WEBCAM_PATH );
	$i = 0;
	$j = 0;
	$k = 0;
	while( $content_array[$i] != "" )
	{
		if( strstr($content_array[$i] , "transcode(" ) != "" )
		{
			//exten => 777,n,transcode(,777@webcam,h263@cif/fps=20/kb=384/qmin=8/qmax=20/gs=50)
			$Str1 = explode( "/" , $content_array[$i]);
			$Str2 = explode( "@" , $Str1[0]);
			$Size[$j] = chop($Str2[2]);

			$Str2 = explode( "=" , $Str1[1]);
			$fps[$j] = chop($Str2[1]);
			$Str2 = explode( "=" , $Str1[2]);
			$bitrate[$j] = chop($Str2[1]);
			$j++;
		}
		else if( strstr($content_array[$i] , "rtsp(" ) != "" )
		{
			//exten => 777,n,rtsp(rtsp://192.168.1.99:554/mpeg4)
			$Str1 = explode( " => " , $content_array[$i]);
			$Str2 = explode( "," , $Str1[1]);
			$num[$k] = chop($Str2[0]);
			$Str1 = explode( "(" , $content_array[$i]);
			$Str2 = explode( ")" , $Str1[1]);
			$ip[$k] = chop($Str2[0]);
			$k++;
		}
		$i++;
	}
	

	
function SetComboSize( $Size )
{	
	global $SizeArray;
	reset( $SizeArray ); 
	
	if( $Size != "" )
		echo "<option value=$Size>$SizeArray[$Size]</option>";

	while(list($key , $val) = each($SizeArray) )
	{
		if( $key == $Size )
			continue;

		echo "<option value=$key>$val</option>";
	}

}
function SetComboBitrate( $bitrate )
{
	global $BitrateArray;

	if( $bitrate != "" )
		echo "<option value=$bitrate>$bitrate</option>";
		
	for( $i = 0; $i < sizeof($BitrateArray); $i++ )
	{
		if( $bitrate == $BitrateArray[$i] )
			continue;
		echo "<option value=$BitrateArray[$i]>$BitrateArray[$i]</option>";
	}
}
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Passwords</title>



<style type="text/css">
<!--
body {
        background-color: <? echo $MainBackGroundColor ?>;
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
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['ipcam'][$LANG];
	echo "</p>";

?>
<table width="100%" border="1" align="center">

<tr>
<th scope="row" width="20%" font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['num'][$LANG] ?></font></th>
<th scope="row" width="40%" font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['ipaddress'][$LANG] ?></font></th>
<th scope="row" width="15%" font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['Size'][$LANG] ?></font></th>
<th scope="row" width="10%" font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['FPS'][$LANG] ?></font></th>
<th scope="row" width="15%" font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['Bitrate'][$LANG] ?></font></th>

<?
	$Color1 = $MainEditBackGround;
	$Color2 = $MainEditBackGround1;

	for( $i = 0; $i < $MAX_WEB_CAM; $i++ )
	{
		if( ($i % 2 ) == 0 )
			$FieldColor = $Color1;
		else
			$FieldColor = $Color2;

		echo "<tr>";
 		echo "<td><input name='num$i' type='text' value='$num[$i]' style='background-color:$FieldColor; width: 100%'></td>";
 		echo "<td><input name='ip$i' type='text' value='$ip[$i]' style='background-color:$FieldColor; width: 100%'></td>";
		echo "<td><select name='Size$i' style='background-color:$FieldColor; width :100%'>";
 		SetComboSize( $Size[$i] );
		echo "</select></td>";
		echo "<td><input name='fps$i' type='text' value='$fps[$i]' style='background-color:$FieldColor; width: 100%'></td>";
		echo "<td><select name='bitrate$i' style='background-color:$FieldColor; width :100%'>";
 		SetComboBitrate( $bitrate[$i] );
		echo "</select></td>";
		echo "</tr>";
	}
?>
</table>

<br><br>

<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>
<br><br>
</form>
</body>


</html>
