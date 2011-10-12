<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}


include "filepath.php";

	$dISA_PATH = "$BASE_AST/ex_disa_rec.conf";

	
	if(isset($_POST[Submit]))
	{	
		$fp = fopen( $dISA_PATH , "w+");
		
		$StrTotal = "[ava_media]\n";
		$StrTotal  .= "exten => $file_welcome,1,Goto(disa_rec,\${EXTEN},1)\n";
		$StrTotal  .= "exten => $file_noanswer,1,Goto(disa_rec,\${EXTEN},1)\n";
		$StrTotal  .= "exten => $file_busy,1,Goto(disa_rec,\${EXTEN},1)\n";
		$StrTotal  .= "exten => $file_invalid,1,Goto(disa_rec,\${EXTEN},1)\n";
		
		
		$StrTotal .= "[disa_rec]\n";
		if( $file_welcome != "" )
		{
			$StrTotal  .= "exten => $file_welcome,1,Background(disa/welcome)\n";
			$StrTotal  .= "exten => $file_welcome,n,Record(disa_welcome:wav)\n";
			$StrTotal  .= "exten => $file_welcome,n,Set(filetype=welcome)\n";
			$StrTotal  .= "exten => $file_welcome,n,Macro(check_rec)\n";
			$StrTotal  .= "exten => $file_welcome,n,Hangup()\n";
		}
		if( $file_noanswer != "" )
		{
			$StrTotal  .= "exten => $file_noanswer,1,Background(disa/welcome)\n";
			$StrTotal  .= "exten => $file_noanswer,n,Record(disa_noanswer:wav)\n";
			$StrTotal  .= "exten => $file_noanswer,n,Set(filetype=noanswer)\n";
			$StrTotal  .= "exten => $file_noanswer,n,Macro(check_rec)\n";
			$StrTotal  .= "exten => $file_noanswer,n,Hangup()\n";
		}
		if( $file_busy != "" )
		{
			$StrTotal  .= "exten => $file_busy,1,Background(disa/welcome)\n";
			$StrTotal  .= "exten => $file_busy,n,Record(disa_busy:wav)\n";
			$StrTotal  .= "exten => $file_busy,n,Set(filetype=busy)\n";
			$StrTotal  .= "exten => $file_busy,n,Macro(check_rec)\n";
			$StrTotal  .= "exten => $file_busy,n,Hangup()\n";
		}
		
		if( $file_invalid != "" )
		{
			$StrTotal  .= "exten => $file_invalid,1,Background(disa/welcome)\n";
			$StrTotal  .= "exten => $file_invalid,n,Record(disa_invalid:wav)\n";
			$StrTotal  .= "exten => $file_invalid,n,Set(filetype=invalid)\n";
			$StrTotal  .= "exten => $file_invalid,n,Macro(check_rec)\n";
			$StrTotal  .= "exten => $file_invalid,n,Hangup()\n";
		}
		
		$StrTotal  .= "exten => 1,1,System(mv /var/lib/asterisk/sounds/disa_\${filetype}.wav /var/lib/asterisk/sounds/disa/\n";
		$StrTotal  .= "exten => 1,n,Hangup()\n";
		
		
		$StrTotal  .= "exten => 2,1,Background(disa_\${filetype})\n";
		$StrTotal  .= "exten => 2,n,Macro(check_rec)\n";
		
		
		$StrTotal  .= "exten => 3,1,GotoIf($[\"\${filetype}\"=\"welcome\"]?first)\n";
		$StrTotal  .= "exten => 3,n,GotoIf($[\"\${filetype}\"=\"noanswer\"]?sec)\n";
		$StrTotal  .= "exten => 3,n,GotoIf($[\"\${filetype}\"=\"busy\"]?third)\n";
		$StrTotal  .= "exten => 3,n,Goto($invalid,1)\n";
		$StrTotal  .= "exten => 3,n(first),Goto($file_welcome,1)\n";
		$StrTotal  .= "exten => 3,n(sec),Goto($file_noanswer,1)\n";
		$StrTotal  .= "exten => 3,n(third),Goto($file_busy,1)\n";

		
		
		$StrTotal  .= "[macro-check_rec]\n";
		$StrTotal  .= "exten => s,1,Background(disa/rec_check)\n";
		$StrTotal  .= "exten => s,n,WaitExten(6)\n";
		$StrTotal  .= "exten => s,n,Background(disa/goodbye)\n";
		
		fwrite( $fp , $StrTotal );
		fclose( $fp );
		
		shell_exec( "asterisk -rx reload" );

	}	


	$content_array = file( $dISA_PATH );
	while( list($key , $val) = each($content_array) )
	{
		if( strstr( $val , "disa_welcome" ) != "" )
		{
			$parse_temp = explode( "," , $val );	
			$file_welcome = substr($parse_temp[0], 9 );	
		}
		else if( strstr( $val , "disa_noanswer" ) != "" )
		{
			$parse_temp = explode( "," , $val );	
			$file_noanswer = substr($parse_temp[0], 9 );	
		}
		else if( strstr( $val , "disa_busy" ) != "" )
		{
			$parse_temp = explode( "," , $val );	
			$file_busy = substr($parse_temp[0], 9 );	
		}
		else if( strstr( $val , "disa_invalid" ) != "" )
		{
			$parse_temp = explode( "," , $val );	
			$file_invalid = substr($parse_temp[0], 9 );	
		}
	
	
	
	
	}






?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference</title>

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
	echo $WORDLIST['disa'][$LANG];
	echo "</p>";
	
	echo "<table width='50%' border='1' align='center'>";
	
	
	echo "<tr>";
	echo "<th width='50%' scope='row'>";
	echo $WORDLIST['recwelcome'][$LANG];
	echo "</th>";
	
 	echo "<td><input name='file_welcome' type='text' value='$file_welcome' style='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<th width='50%' scope='row'>";
	echo $WORDLIST['recnoanswer'][$LANG];
	echo "</th>";
	
 	echo "<td><input name='file_noanswer' type='text' value='$file_noanswer' style='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th scope='row'>";
	echo $WORDLIST['recbusy'][$LANG];
	echo "</th>";
	
 	echo "<td><input name='file_busy' type='text' value='$file_busy' style='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<th scope='row'>";
	echo $WORDLIST['recinvalid'][$LANG];
	echo "</th>";
	
 	echo "<td><input name='file_invalid' type='text' value='$file_invalid' style='background-color:$MainEditBackGround; width: 100%'></td>";
	echo "</tr>";

	echo "</table>";
	
?>



	
	
<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
